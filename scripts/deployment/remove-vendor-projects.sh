#!/bin/bash

# remove all the projects that need to be re-installed by the upstream.
# this is for the cases where someone intalled them via WordPress SFTP
# that kind of install totally fouls up the composer autoload

set -e

BASEDIR=$(dirname "$0")
cd "$BASEDIR/../../"
if [ -d "./vendor" ] && [ -d "./web/wp-content/plugins" ]
then
    cd "./vendor"
else
    echo "Please run $0 from the site root"
    exit 1
fi

PLUGINS=(
        "gettext"
        "simplesamlphp"
        "symfony"
)

for PLUG in "${PLUGINS[@]}"
do
    echo "Removing $PLUG"
    rm -rf "$PLUG"
done