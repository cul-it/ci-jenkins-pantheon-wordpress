<?php
namespace FileBird\Controller;

use FileBird\Model\Folder as FolderModel;

defined('ABSPATH') || exit;

class CompatibleWpml extends Controller {
  protected static $instance = null;

  protected $post_translations;
  private $sitepress;
  private $lang;
  private $table_icl_translations;
  
  public static function getInstance() {
    if (null == self::$instance) {
      self::$instance = new self;
      self::$instance->doHooks();
    }
    return self::$instance;
  }
  public function __construct() {
  }

  private function doHooks(){
    global $sitepress, $wpdb;
    if ( $sitepress === null || get_class($sitepress) !== "SitePress" ) {
      return;
    }
    $this->sitepress = $sitepress;
    $this->lang = $sitepress->get_current_language();
    $this->table_icl_translations = $wpdb->prefix . 'icl_translations';
    $this->post_translations = $sitepress->post_translations();

    add_action('fbv_after_set_folder', array($this, 'fbvAfterSetFolder'), 10, 2);
    // add_filter('fbv_in_not_in', array($this, 'filterInNotIn'));
    add_filter('wpml_pre_parse_query', array($this, 'preParseQuery'));
    add_filter('wpml_post_parse_query', array($this, 'postParseQuery'));
    add_filter('fbv_get_count_query', array($this, 'fbv_get_count_query'), 10, 2);
    add_filter('fbv_in_not_in_query', array($this, 'fbv_in_not_in_query'), 10, 2);
    add_filter('fbv_speedup_get_count_query', '__return_true');
    add_filter('fbv_all_folders_and_count', array($this, 'all_folders_and_count_query'));
  }
  public function all_folders_and_count_query($query) {
    global $wpdb;
    $query = "SELECT fbva.folder_id as id, count(fbva.attachment_id) as count FROM {$wpdb->prefix}fbv_attachment_folder AS fbva 
    INNER JOIN {$wpdb->prefix}fbv as fbv ON fbv.id = fbva.folder_id 
    INNER JOIN {$this->table_icl_translations} AS wpmlt ON fbva.attachment_id = wpmlt.element_id 
    INNER JOIN {$wpdb->posts} as posts ON posts.ID = fbva.attachment_id 
    WHERE (posts.post_status = 'inherit' OR posts.post_status = 'private') AND wpmlt.element_type = 'post_attachment' AND wpmlt.language_code = '{$this->lang}' AND 
    fbv.created_by = ".apply_filters('fbv_in_not_in_created_by', '0')." GROUP BY fbva.folder_id";
    return $query;
  }
  public function fbv_get_count_query($q, $folder_id) {
    global $wpdb;
    if($folder_id == -1) {
      $q = "SELECT COUNT(*)
      FROM {$this->table_icl_translations} AS wpmlt
      INNER JOIN {$wpdb->posts} AS p ON p.id = wpmlt.element_id
      WHERE wpmlt.element_type =  'post_attachment'
      AND wpmlt.language_code =  '$this->lang'";
    } else {
      $q = "SELECT count(wpmlt.element_id) FROM {$this->table_icl_translations} AS wpmlt 
      INNER JOIN {$wpdb->posts} as posts ON posts.ID = wpmlt.element_id 
      INNER join {$wpdb->prefix}fbv_attachment_folder as fbvaf on wpmlt.element_id = fbvaf.attachment_id 
      WHERE (post_status = 'inherit' OR post_status = 'private') AND wpmlt.element_type = 'post_attachment' AND wpmlt.language_code = '{$this->lang}' AND fbvaf.folder_id = " . (int)$folder_id;
    }
    return $q;
  }
  public function fbv_in_not_in_query($q, $fbv) {
    global $wpdb;
    if($fbv == 0) {
      // query for uncategorized folder (post__not_in)
      $q = "SELECT wpmlt.element_id FROM {$this->table_icl_translations} AS wpmlt 
      INNER JOIN {$wpdb->posts} as posts ON posts.ID = wpmlt.element_id 
      INNER join {$wpdb->prefix}fbv_attachment_folder as fbvaf on wpmlt.element_id = fbvaf.attachment_id 
      WHERE (post_status = 'inherit' OR post_status = 'private') AND wpmlt.element_type = 'post_attachment' AND wpmlt.language_code = '{$this->lang}'";
    } elseif(is_array($fbv)) {
      // query for specific folders
      $q = "SELECT wpmlt.element_id FROM {$this->table_icl_translations} AS wpmlt 
      INNER JOIN {$wpdb->posts} as posts ON posts.ID = wpmlt.element_id 
      INNER join {$wpdb->prefix}fbv_attachment_folder as fbvaf on wpmlt.element_id = fbvaf.attachment_id 
      WHERE (post_status = 'inherit' OR post_status = 'private') AND wpmlt.element_type = 'post_attachment' AND wpmlt.language_code = '{$this->lang}' AND fbvaf.folder_id IN (".implode(', ', $fbv).")";
    }
    return $q;
  }
  public function fbvAfterSetFolder($id, $folder) {
    global $wpdb;
    $cpt_sync_options = $this->sitepress->get_setting( 'custom_posts_sync_option', array() );
    if(isset($cpt_sync_options['attachment']) && $cpt_sync_options['attachment'] == '0') {
      return;
    }
    $post                     = get_post( $id );
		$post_type                = $post->post_type;
		$post_trid                = $this->sitepress->get_element_trid( $id, 'post_' . $post_type );
    $post_translations        = $this->sitepress->get_element_translations( $post_trid, 'post_' . $post_type );

    foreach ( $post_translations as $post_language => $translated_post ) {
      $translated_post_id         = $translated_post->element_id;
			if ( ! $translated_post_id ) {
				continue;
      }
      FolderModel::setFoldersForPosts($translated_post_id, (int)$folder, false);
    }
  }
  public function filterInNotIn($query) {
    $query = $this->adjust_q_var_pids($query, 'post__not_in');
    $query = $this->adjust_q_var_pids($query, 'post__in');
    return $query;
  }
  public function preParseQuery($q) {
    if ( ! empty( $q->query_vars['post_type'] ) && $q->query_vars['post_type'] == 'attachment' ) {
      $cpt_sync_options = $this->sitepress->get_setting( 'custom_posts_sync_option', array() );
      if(isset($cpt_sync_options['attachment']) && $cpt_sync_options['attachment'] == '0') {
        $q->query_vars['fbv_backup_post__in'] = $q->query_vars['post__in'];
        $q->query_vars['fbv_backup_post__not_in'] = $q->query_vars['post__not_in'];
        $q->query_vars['post__in'] = array();
        $q->query_vars['post__not_in'] = array();
      }
    }
    return $q;
  }
  public function postParseQuery($q) {
    if ( ! empty( $q->query_vars['post_type'] ) && $q->query_vars['post_type'] == 'attachment' ) {
      $cpt_sync_options = $this->sitepress->get_setting( 'custom_posts_sync_option', array() );
      if(isset($cpt_sync_options['attachment']) && $cpt_sync_options['attachment'] == '0') {
        $q->query_vars['post__in'] = $q->query_vars['fbv_backup_post__in'];
        $q->query_vars['post__not_in'] = $q->query_vars['fbv_backup_post__not_in'];
        unset($q->query_vars['fbv_backup_post__in']);
        unset($q->query_vars['fbv_backup_post__not_in']);
      }
		}
    return $q;
  }
  private function adjust_q_var_pids( $q, $index ) {
		if ( ! empty( $q[ $index ] ) ) {

			$untranslated = $q[ $index ];
			$this->post_translations->prefetch_ids( $untranslated );
			$current_lang = $this->sitepress->get_current_language();
			$pid          = array();
			foreach ( $q[ $index ] as $p ) {
				$pid[] = $this->post_translations->element_id_in( $p, $current_lang, true );
			}
			$q[ $index ] = $pid;
		}

		return $q;
	}

  public function countArgs($args) {
    $args['suppress_filters'] = false;
    return $args;
  }
}