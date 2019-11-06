#!/bin/bash
git stash -q --keep-index
./vendor/bin/phpcs --standard=phpcs-ruleset.xml -p -s -v -n src --extensions=php

RESULT=$?
git stash pop -q
[ $RESULT -ne 0 ] && exit 1

find ./src -name "*.php" -print0 | xargs -0 -n1 -P8 php -l
[ $RESULT -ne 0 ] && exit 1

exit 0
