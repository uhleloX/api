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
 * The JSON DB File contents.
 *
 * @var int $dbjson JSON Data from JSON DB file.
 */
$dbjson = readDB();

/**
 * The JSON Decoded data from JSON DB File.
 *
 * @var string $db The JSON data.
 */
$db = parseDB( $dbjson );

/**
 * Route GET requests.
 */
if ( isset( $_GET['operation'] ) ) {

	$op = htmlspecialchars( $_GET['operation'] );

	/**
	 * Return status Code 202 on test operation.
	 */
	if ( 'test' === $op ) {
		http_response_code( 202 );
		die();
	}

	/**
	 * Return only newest release JSON when operation requests newest
	 */
	if ( 'newest' === $op ) {
		$newest_release = get_newest( $db );
		echo( json_encode( $newest_release ) );
	}

	/**
	 * Return latest build if request is update, by build id.
	 */
	if ( 'update' === $op ) {
		if ( isset( $_GET['buildid'] ) ) {
			$release        = get_next( $db, htmlspecialchars( $_GET['buildid'] ) );
			$newer_available = intval( $release['buildid'] ) > intval( htmlspecialchars( $_GET['buildid'] ) );
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

	/**
	 * Return latest download if request is download. Signature if request is signature.
	 */
	if ( 'download' === $op ) {
		if ( isset( $_GET['buildid'] ) ) {
			$release = get_by_bid( $db, intval( htmlspecialchars( $_GET['buildid'] ) ) );
			if ( $release == null ) {
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
	// Return full JSON if no request specified.
	echo( $dbjson );
}

function get_by_bid( $db, $bid ) {
	foreach ( $db as $release ) {
		if ( $release['buildid'] == $bid ) {
			return $release;
		}
	}
	return null;
}

function get_next( $db, $buildID ) {
	foreach ( $db as $release ) {
		if ( $release['buildid'] > $buildID ) {
			return $release;
		}
	}
	return false;
}

function get_newest( $db ) {
	$highestRelease = null;
	foreach ( $db as $release ) {
		if ( $highestRelease == null ) {
			$highestRelease = $release;
		} else {
			$highestRelease = $release['buildid'] > $highestRelease['buildid'] ? $release : $highestRelease;
		}
	}
	return $highestRelease;
}

/**
 * JSON Decoded contentes fo Database JSON File.
 *
 * @return string | int The JSON Decoded Database File contents or HTTP error 500 code.
 */
function parseDB( $dbjson ) {
	$db = json_decode( $dbjson, true );
	if ( $db === null ) {
		http_response_code( 500 );
	}
	return $db;
}

/**
 * Get filecontentes of Database JSON File.
 *
 * @return string | int The Database File contents or HTTP error 500 code.
 */
function readDB() {
	$dbfile = 'db.json';
	if ( ! file_exists( $dbfile ) ) {
		http_response_code( 500 );
	}
	$dbjson = file_get_contents( 'db.json' );
	return $dbjson;
}
