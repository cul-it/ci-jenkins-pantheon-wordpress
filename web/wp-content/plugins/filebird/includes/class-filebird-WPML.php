<?php

class NJT_FB_WPML
{
    //Plugin
    private $plugin_name;
    private $version;

    //WPML
    private $is_wpml_active;
    private $total;
    private $lang;
    private $table_icl_translations;
    public $delete_process_id;

    protected $post_translations;
    private $sitepress;

    public function __construct($plugin_name, $version)
    {
      $this->plugin_name = $plugin_name;
      $this->version = $version;
      $this->is_wpml_active = false;
      $this->total = 0;
      $this->delete_process_id = null;
    }

    public function init()
    {
        global $sitepress, $wpdb;
        $is_wpml_active = $sitepress !== null && get_class($sitepress) === "SitePress";

        if ($is_wpml_active) {
            $settings = $sitepress->get_setting('custom_posts_sync_option', array());
            if (isset($settings['attachment']) && $settings['attachment'] && $sitepress->get_current_language() !== 'all') {
                $this->is_wpml_active = true;
                $this->lang = $sitepress->get_current_language();
                $this->table_icl_translations = $wpdb->prefix . 'icl_translations';

                $this->set_total();
            }
            $this->sitepress = $sitepress;
            $this->post_translations = $sitepress->post_translations();
        }

        if ($this->is_wpml_active) {
            add_filter('njt_filebird_attachment_counter', array($this, 'items_in_folder'), 10, 2);
            add_filter('njt_filebird_uncategorized_counter', array($this, 'uncategorized_counter'), 10);
            add_filter('njt_filebird_all_categorized_counter', array($this, 'all_categorized_counter'), 10);

            // add_action('delete_attachment', array($this, 'delete_attachment'));
            // add_action('wpml_media_create_duplicate_attachment', array($this, 'duplicate_attachment_to_folder'), 10, 2);
            // add_action('njt_filebird_save_attachment', array($this, 'save_attachment'), 10, 2);
            add_filter('njt_fb_in_not_in', array($this, 'njt_fb_in_not_in'));
        }
    }

    public function set_total(){
        global $wpdb;

        $this->total = (int) $wpdb->get_var("SELECT COUNT(*)
            FROM $this->table_icl_translations AS wpmlt
            INNER JOIN $wpdb->posts AS p ON p.id = wpmlt.element_id
            WHERE wpmlt.element_type =  'post_attachment'
            AND wpmlt.language_code =  '$this->lang'");
}

    public function items_in_folder($term_count, $term_id)
    {
        global $wpdb;
        $term_taxonomy_id = get_term_by('id', (int) $term_id, NJT_FILEBIRD_FOLDER, OBJECT)->term_taxonomy_id;

        $join = "INNER JOIN $wpdb->term_relationships AS term_rela ON term_rela.object_id = wpmlt.element_id";
        $where = "wpmlt.element_type =  'post_attachment' AND term_rela.term_taxonomy_id = $term_taxonomy_id AND wpmlt.language_code =  '$this->lang'";
        $query = apply_filters('fb_wpml_count_items_query', array( 'join' => $join, 'where' => $where));
        $all_ids = $wpdb->get_col("SELECT wpmlt.element_id FROM $this->table_icl_translations AS wpmlt " . $query['join'] . " WHERE " . $query['where']);
        $counter = false;
        if(count($all_ids) > 0) {
            $counter = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE `ID` IN (".implode(',', $all_ids).") AND (post_status = 'inherit' OR post_status = 'private')");
        }
        return $counter ? "data-number={$counter}" : '';
    }

    public function all_categorized_counter()
    {
        return $this->total ? "data-number={$this->total}" : '';
    }

    public function uncategorized_counter()
    {
        global $wpdb;
        $query = apply_filters("njt_booking_uncategorized_counter_wpml_query", "SELECT COUNT(*)
        FROM (SELECT * FROM $this->table_icl_translations as wpmlt
        INNER JOIN $wpdb->posts as p on p.id = wpmlt.element_id
        WHERE wpmlt.element_type = 'post_attachment'
        and wpmlt.language_code = '$this->lang') as tmp_table
        JOIN $wpdb->term_relationships as term_relationships on tmp_table.element_id = term_relationships.object_id
        JOIN $wpdb->term_taxonomy as term_taxonomy on term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id where taxonomy = 'nt_wmc_folder'");
        $fileInFolder = (int) $wpdb->get_var($query);

        $uncategory = $this->total - $fileInFolder;

        return $uncategory ? "data-number={$uncategory}" : '';
    }

    public function save_attachment($id, $folder_id){
        global $wpdb;
        $query = "SELECT element_id from {$wpdb->prefix}icl_translations
        WHERE trid = (SELECT trid from {$wpdb->prefix}icl_translations WHERE element_id = $id)
        AND element_id <> $id";
        $lists = $wpdb->get_results($query);
        foreach ($lists as $list) {
            wp_set_object_terms(intval($list->element_id), intval($_REQUEST['folder_id']), NJT_FILEBIRD_FOLDER, false);
        }
    }

    public function duplicate_attachment_to_folder($post_id, $tr_id)
    {
        $filebird_Folder = isset($_REQUEST["ntWMCFolder"]) ? sanitize_text_field($_REQUEST["ntWMCFolder"]) : null;
        if (is_null($filebird_Folder)) {
            $filebird_Folder = isset($_REQUEST["njt_filebird_folder"]) ? sanitize_text_field($_REQUEST["njt_filebird_folder"]) : null;
        }
        if ($filebird_Folder !== null) {
            $filebird_Folder = (int) $filebird_Folder;
            wp_set_object_terms($tr_id, $filebird_Folder, NJT_FILEBIRD_FOLDER, false);
        }
    }

    public function delete_attachment($post_ID)
    {
        global $wpdb;
        if ($post_ID != $this->delete_process_id) {
            $query = "SELECT element_id from {$wpdb->prefix}icl_translations
            WHERE trid = (SELECT trid from {$wpdb->prefix}icl_translations WHERE element_id = $post_ID)
            AND element_id <> $post_ID";
            $lists = $wpdb->get_results($query);
            foreach ($lists as $list) {
                $this->delete_process_id = $list->element_id;
                wp_delete_attachment(intval($list->element_id));
            }
        }
    }
    public function njt_fb_in_not_in($query) {
      $query = $this->adjust_q_var_pids($query, 'post__not_in');
      $query = $this->adjust_q_var_pids($query, 'post__in');
      return $query;
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
}
