#!/bin/bash
# this is ignored in the Jenkins script
# but can't delete that section without damaging other code for some reason
echo 'try #18'
pwd
ls -l
php --version
echo "composer install does not work with version 5.5.38 of php (ocramius)"
composer --version
composer install
