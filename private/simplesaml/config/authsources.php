<?php

/**
 * select which version to include based on Pantheon environmental variables
 * original contents of this file now in authsourches.php.default
 */

if (defined('PANTHEON_ENVIRONMENT') && !empty($_ENV['PANTHEON_ENVIRONMENT'])) {
    define(SP_ENTITY_ID, $_ENV['HOME']."/simplesaml/module.php/saml/sp/metadata/default-sp");
    if ($_ENV['PANTHEON_ENVIRONMENT'] == 'live') {
        require 'authsources.prod.php';
    }
    else {
        require 'authsources.test.php';
    }
}
else {
    require 'authsources.default.php';
}
