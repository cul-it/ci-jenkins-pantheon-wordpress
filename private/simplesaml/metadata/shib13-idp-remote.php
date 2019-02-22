<?php
/**
 * SAML 1.1 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */

/*
$metadata['theproviderid-of-the-idp'] = array(
	'SingleSignOnService'  => 'https://idp.example.org/shibboleth-idp/SSO',
	'certificate'          => 'example.pem',
);
*/
/**
 * select which version to include based on Pantheon environmental variables
 * original contents of this file now in shib13-idp-remote.php.default
 */
if (defined('PANTHEON_ENVIRONMENT') && !empty($_ENV['PANTHEON_ENVIRONMENT'])) {
    if ($_ENV['PANTHEON_ENVIRONMENT'] == 'live') {
        require 'shib13-idp-remote.php.prod';
    }
    else {
        require 'shib13-idp-remote.php.test';
    }
}
else {
    require 'shib13-idp-remote.php.default';
}
