#!/bin/bash
echo 'try #11'
pwd
ls -l
php --version
echo "composer install does not work with version 5.5.38 of php (ocramius)"
composer --version
composer install
