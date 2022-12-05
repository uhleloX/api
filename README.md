# uhleloX API

## uhleloX Update API
The folder `updates` Structure of folder content has to be preserved.
Generate a keypair with `sh createKeyPair.sh`. Never share private key, put public key into Release root folder (or install root).
Add a release zip to the `repo` folder.
Create a signature with `sh createignature.sh` (after each new release added to repo). Command: `sh createSignature {zip name}`
Update the database JSON with `sh updateDB.sh {buildid} {version} {filename} {releasenotes} y` (omit `y` to only print results)
Confirm Database is updated. Eventually amend `releasenotes`. Currently, you have to manually add in a `roadmap`. Both support HTML syntax.

Read more about trigger options on inspiring repo https://github.com/ozzi-/php-app-updater
