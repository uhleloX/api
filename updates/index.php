<?php
/**
 * The uhleloX Update API.
 * Props to https://github.com/ozzi-/php-app-updater for original work.
 *
 * @since 1.0.0
 * @package uhleloX\api\updates
 */

/**
 * Set headers to return JSON.
 */
header( 'Content-Type: application/json' );

/**
 * The JSON data file.
 *
 * @var int $dbjson Response code if file does not exist.
 */
$dbjson = read_db();

/**
 * The JSON data of releases.
 *
 * @var string $db The JSON data.
 */
$db = parse_db( $dbjson );

/**
 * Route GET requests.
 */
if ( isset( $_GET['operation'] ) ) {

	$op = htmlspecialchars( stripslashes( $_GET['operation'] ) );

	if ( 'test' === $op ) {
		http_response_code( 202 );
		die();
	}
	if ( 'newest' === $op ) {
		$newest_release = getNewest( $db );
		echo( json_encode( $newest_release ) );
	}
	if ( 'update' === $op ) {
		if ( isset( $_GET['buildid'] ) ) {
			$release         = get_next( $db, htmlspecialchars( stripslashes( $_GET['buildid'] ) ) );
			$newer_available = intval( $release['buildid'] ) > htmlspecialchars( stripslashes( intval( $_GET['buildid'] ) ) );
			http_response_code( $newer_available ? 200 : 404 );
			if ( $newer_available ) {
				echo( json_encode( $release ) );
			}
			die();
		} else {
			http_response_code( 400 );
			die();
		}
	}
	if ( 'download' === $op ) {
		if ( isset( $_GET['buildid'] ) ) {
			$release = get_by_ID( $db, htmlspecialchars( stripslashes( $_GET['buildid'] ) ) );
			if ( null === $release ) {
				http_response_code( 404 );
				die();
			}
		} else {
			$release = get_newest( $db );
		}
		if ( isset( $_GET['signature'] ) ) {
			header( 'Content-Type: text/plain' );
			echo( $release['signature'] );
			die();
		} else {
			header( 'Location: repo/' . $release['filename'] );
			die();
		}
	}
} else {
	echo( $dbjson );
}

/**
 * Return the corresponding Build Release data.
 *
 * @param string $db JSON string of release data.
 * @param int    $bid Build ID.
 */
function get_by_ID( $db, $bid ) {
	foreach ( $db as $release ) {
		if ( $bid === $release['buildid'] ) {
			return $release;
		}
	}
	return null;
}

/**
 * Get the next release.
 *
 * @param string $db JSON string of release data.
 * @param int    $bid Build ID.
 */
function get_next( $db, $bid ) {
	foreach ( $db as $release ) {
		if ( $release['buildid'] > $bid ) {
			return $release;
		}
	}
	return false;
}

/**
 * Get the newest release.
 *
 * @param string $db JSON string of release data.
 */
function get_newest( $db ) {
	$highest_release = null;
	foreach ( $db as $release ) {
		if ( null === $highest_release ) {
			$highest_release = $release;
		} else {
			$highest_release = $release['buildid'] > $highest_release['buildid'] ? $release : $highest_release;
		}
	}
	return $highest_release;
}

/**
 * Decode Database File contents.
 *
 * @param string $dbjson The file contents of the Database .
 * @return string | int The decoded JSON or error 500 status.
 */
function parse_db( $dbjson ) {
	$db = json_decode( $dbjson, true );
	if ( null === $db ) {
		http_response_code( 500 );
	}
	return $db;
}

/**
 * Get filecontentes of Database.
 *
 * @return string | int The Database File contents or error 500 code.
 */
function read_db() {
	$dbfile = 'db.json';
	if ( ! file_exists( $dbfile ) ) {
		http_response_code( 500 );
	}
	$dbjson = file_get_contents( 'db.json' );
	return $dbjson;
}

