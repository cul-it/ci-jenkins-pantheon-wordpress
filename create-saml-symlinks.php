<?php
$site_root = __FILE__;
for ($i = 0; $i < 5; $i++) {
    $site_root = dirname($site_root);
}
$saml = "vendor/simplesamlphp/simplesamlphp";
$config = "$site_root/$saml/config";
$config_target = "../../../../private/simplesaml/config";
$metadata = "$site_root/$saml/metadata";
$metadata_target = "../../../../private/simplesaml/metadata";
try {
    if (is_dir($config)) {
        if (is_link("$config/config.php")) {
            unlink("$config/config.php");
        }
        if (is_link("$config/authsources.php")) {
            unlink("$config/authsources.php");
        }
    } else {
        if (!mkdir($config, 0777, true)) {
            throw new Exception("Can not make $config", 1);
        }
    }
    symlink("$config_target/config.php", "$config/config.php");
    symlink("$config_target/authsources.php", "$config/authsources.php");
    if (is_dir($metadata)) {
        if (is_link("$metadata/saml20-idp-remote.php")) {
            unlink("$metadata/saml20-idp-remote.php");
        }
        if (is_link("$metadata/shib13-idp-remote.php")) {
            unlink("$metadata/shib13-idp-remote.php");
        }
    } else {
        if (!mkdir($metadata, 0777, true)) {
            throw new Exception("Can not make $metadata", 1);
        }
    }
    symlink("$metadata_target/saml20-idp-remote.php", "$metadata/saml20-idp-remote.php");
    symlink("$metadata_target/shib13-idp-remote.php", "$metadata/shib13-idp-remote.php");

} catch (\Exception $e) {
    echo($e->getMessage().PHP_EOL);
}
