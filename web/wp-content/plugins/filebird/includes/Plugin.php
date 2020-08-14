<?php
namespace FileBird;

defined('ABSPATH') || exit;
/**
 * Plugin activate/deactivate logic
 */
class Plugin {
  protected static $instance = null;

  public static function getInstance() {
    if (null == self::$instance) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  private function __construct() {
  }

  /** Plugin activated hook */
  public static function activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    $table_fbv = $wpdb->prefix.'fbv';
    //type == 0: folder
    //type == 1: collection
    if ($wpdb->get_var("show tables like '$table_fbv'") != $table_fbv) {
        $sql = 'CREATE TABLE '.$table_fbv.' (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(250) NOT NULL,
        `parent` int(11) NOT NULL DEFAULT 0,
        `type` int(2) NOT NULL DEFAULT 0,
        `ord` int(11) NULL DEFAULT 0,
        `created_by` int(11) NULL DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY `id` (id)) ' . 'ENGINE = InnoDB '.$charset_collate.';';
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    $table = $wpdb->prefix.'fbv_attachment_folder';
    //type == 0: folder
    //type == 1: collection
    if ($wpdb->get_var("show tables like '$table'") != $table) {
        $sql = 'CREATE TABLE '.$table.' (
        `folder_id` int(11) NOT NULL,
        `attachment_id` int(11) NOT NULL,
        UNIQUE( `folder_id`, `attachment_id`),
        FOREIGN KEY (folder_id) REFERENCES '.$table_fbv.'(id) ON DELETE CASCADE
        ) ' . 'ENGINE = InnoDB '.$charset_collate.';';
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
  }

  /** Plugin deactivate hook */
  public static function deactivate() {
  }
}
