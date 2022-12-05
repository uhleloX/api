# uhleloX API

## uhleloX Update API

## URL
https://api.uhlelox.com/updates/

### General
The folder `updates` Structure of folder content has to be preserved.

### Operations

- Generate a keypair with `sh createKeyPair.sh`. Never share private key, put public key into Release root folder (need to be in end users's install root).
- Add a release zip to the `repo` folder. Release zip shall not include anything the end user might have overwritten (`.htaccess`, `config.php`, etc). Must me named `uhleloX-1.2.3.zip` where `1.2.3` is the version. Must be clear of `__MACOS` garbage. 
- Create a signature with `sh createignature.sh` (after each new release added to repo). Command: `sh createSignature.sh {zip name}`
- Update the database JSON with `sh updateDB.sh {buildid} {version} {filename} {releasenotes} y` (omit `y` to only print results)
- Confirm Database is updated. Eventually amend `releasenotes`. Supports HTML markup.
- Currently, you have to manually add in a `roadmap`. Supports HTML markup.

Read more about trigger options on inspiring repo https://github.com/ozzi-/php-app-updater
