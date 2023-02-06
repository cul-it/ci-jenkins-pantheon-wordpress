#!/bin/bash
# former part of composer.json post-install-cmd
# run this after composer install
mkdir -p ./vendor/simplesamlphp/simplesamlphp/config
mkdir -p ./vendor/simplesamlphp/simplesamlphp/metadata
ln -s -f ../../../../private/simplesaml/config/config.php ./vendor/simplesamlphp/simplesamlphp/config/config.php
ln -s -f ../../../../private/simplesaml/config/authsources.php ./vendor/simplesamlphp/simplesamlphp/config/authsources.php
ln -s -f ../../../../private/simplesaml/metadata/saml20-idp-remote.php ./vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php
ln -s -f ../../../../private/simplesaml/metadata/shib13-idp-remote.php ./vendor/simplesamlphp/simplesamlphp/metadata/shib13-idp-remote.php