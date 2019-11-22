<?php
namespace Smackcoders\WCSV;

if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

class ErrorHandling{
    private static $instance = null;

    public static function getInstance() {
        if (ErrorHandling::$instance == null) {
            ErrorHandling::$instance = new Plugin;
           
            return ErrorHandling::$instance;
        }
        return ErrorHandling::$instance;
    }
}
	