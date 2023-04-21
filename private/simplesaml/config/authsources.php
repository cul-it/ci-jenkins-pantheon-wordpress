<?php

/**
 * select which version to include based on Pantheon environmental variables
 * original contents of this file now in authsourches.php.default.
 */

// find current host url
if (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS']) {
    $url = 'https://';
} else {
    $url = 'http://';
}
// Append the host(domain name, ip) to the URL.
$url .= $_SERVER['HTTP_HOST'];
define(SP_ENTITY_ID, "$url/simplesaml/module.php/saml/sp/metadata/default-sp");

if (defined('PANTHEON_ENVIRONMENT') && !empty($_ENV['PANTHEON_ENVIRONMENT'])) {
    if ('live' == $_ENV['PANTHEON_ENVIRONMENT']) {
        require 'authsources.prod.php';
    } else {
        require 'authsources.test.php';
    }
}
