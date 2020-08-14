<?php
namespace FileBird\Classes;
defined('ABSPATH') || exit;

class Upgrade {
    protected static $instance = null;

    public function __construct() {
        require NJFB_PLUGIN_PATH . '/includes/Lib/plugin-update-checker/plugin-update-checker.php';
        $MyUpdateChecker = \Puc_v4_Factory::buildUpdateChecker(
            'filebird-jstree/includes/Lib/plugin-update-checker/examples/plugin.json', //Metadata URL.
            NJFB_PLUGIN_FILE, //Full path to the main plugin file.
            'filebird' //Plugin slug. Usually it's the same as the name of the directory.
        );
    }
    public static function getInstance() {
        if (null == self::$instance) {
          self::$instance = new self;
        }
        return self::$instance;
    }
}
