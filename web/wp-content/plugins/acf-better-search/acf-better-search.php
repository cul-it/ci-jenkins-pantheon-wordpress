<?php 

  /*
    Plugin Name: ACF: Better Search
    Description: Adds to default WordPress search engine the ability to search by content from selected fields of Advanced Custom Fields plugin.
    Version: 3.5.3
    Author: Mateusz Gbiorczyk
    Author URI: https://gbiorczyk.pl/
    Text Domain: acf-better-search
  */

  define('ACFBS_VERSION', '3.5.3');
  define('ACFBS_FILE',    __FILE__);
  define('ACFBS_NAME',    plugin_basename(__FILE__));
  define('ACFBS_PATH',    plugin_dir_path(__FILE__));
  define('ACFBS_URL',     plugin_dir_url(__FILE__));

  require_once __DIR__ . '/vendor/autoload.php';
  new AcfBetterSearch\AcfBetterSearch();