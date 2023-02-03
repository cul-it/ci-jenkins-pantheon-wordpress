#!/bin/bash
# former composer.json pre-install-cmd
# run this before composer install
rm -f ./vendor/simplesamlphp/simplesamlphp/config/config.php
rm -f ./vendor/simplesamlphp/simplesamlphp/config/authsources.php
rm -f ./vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php
rm -f ./vendor/simplesamlphp/simplesamlphp/metadata/shib13-idp-remote.php
