<?php
$rootPath = realpath( __DIR__ . '/..' );
require_once( $rootPath . '/vendor/autoload.php' );

// require_once __DIR__ . '/../vendor/autoload.php';
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();
echo "PANTHEON_ENVIRONMENT: " . getenv( 'PANTHEON_ENVIRONMENT' ) . "\n";
echo "Host: " . getenv( 'DB_HOST' ) . "\n";
echo "User: " . getenv( 'DB_USER' ) . "\n";
$env_vars = getenv();
print_r($env_vars);
?>