#!/bin/bash
# former part of composer.json post-install-cmd
# run this after composer install
mkdir -p ./vendor/simplesamlphp/simplesamlphp/cert
mkdir -p ./vendor/simplesamlphp/simplesamlphp/config
mkdir -p ./vendor/simplesamlphp/simplesamlphp/metadata
ln -s -f ../../../../private/simplesaml/cert/saml.crt ./vendor/simplesamlphp/simplesamlphp/cert/saml.crt
ln -s -f ../../../../private/simplesaml/cert/saml.pem ./vendor/simplesamlphp/simplesamlphp/cert/saml.pem
ln -s -f ../../../../private/simplesaml/config/config.php ./vendor/simplesamlphp/simplesamlphp/config/config.php
ln -s -f ../../../../private/simplesaml/config/authsources.php ./vendor/simplesamlphp/simplesamlphp/config/authsources.php
ln -s -f ../../../../private/simplesaml/metadata/saml20-idp-remote.php ./vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php
ls -l ./vendor/simplesamlphp/simplesamlphp/cert
ls -l ./vendor/simplesamlphp/simplesamlphp/config
ls -l ./vendor/simplesamlphp/simplesamlphp/metadata