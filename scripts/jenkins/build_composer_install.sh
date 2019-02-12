#!/bin/bash
echo 'try #2'
ls -l
php --version
echo "composer install does not work with version 5.5.38 of php (ocramius)"
cd ../
pwd
composer install