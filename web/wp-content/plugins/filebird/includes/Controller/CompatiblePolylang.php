<?php
namespace FileBird\Controller;

use FileBird\Model\Folder as FolderModel;

defined('ABSPATH') || exit;

class CompatiblePolylang extends Controller {
  protected static $instance = null;

  private $active;
  private $total;
  private $table_filebird_polylang;
  public $delete_process_id;

  public function __construct() {
    global $wpdb, $polylang;

    $this->total = 0;
    $this->delete_process_id = null;

    $this->active = function_exists("pll_get_post_translations");
    if ($this->active) {
      if ($polylang->options['media_support'] == 1)
      {
        add_action('pll_translate_media', array($this, 'duplicateAttachmentToFolder'), 10, 3);
        add_filter( 'pll_filter_query_excluded_query_vars', array($this, 'excludedQueryVars'), 10, 3);
      }
    }
  }

  public function duplicateAttachmentToFolder($post_id, $tr_id, $lang_slug) {
    $folders_of_source = FolderModel::getFoldersOfPost($post_id);
    FolderModel::setFoldersForPosts($tr_id, $folders_of_source);
  }
  public function excludedQueryVars($excludes, $query, $lang) {
    if(isset($query->query['fbv_count']) && $query->query_vars['post_type'] == 'attachment' ) {
      $excludes = array_values(array_diff($excludes, array('post__in', 'post__not_in')));
    }
    return $excludes;
  }
  public static function getInstance() {
    if (null == self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }
}
