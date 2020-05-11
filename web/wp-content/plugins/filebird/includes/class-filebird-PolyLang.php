<?php

class NJT_FB_PolyLang
{
    //Plugin
    private $plugin_name;
    private $version;
    private $active;
    private $pl_term_taxonomy_id;
    private $total;
    private $table_filebird_polylang;
    public $delete_process_id;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->active = false;
        $this->total = 0;
        $this->delete_process_id = null;
    }

    public function init()
    {
        global $wpdb, $polylang;
        $this->active = function_exists("pll_get_post_translations");

        if ($this->active) {
            if (isset($polylang->curlang) && is_object($polylang->curlang) && $polylang->options['media_support'])
            {
                $this->pl_term_taxonomy_id = $polylang->curlang->term_taxonomy_id;
            
                 $this->set_total();
    
                add_filter('njt_filebird_attachment_counter', array($this, 'items_in_folder'), 10, 2);
                add_filter('njt_filebird_uncategorized_counter', array($this, 'uncategorized_counter'), 10);
                add_filter('njt_filebird_all_categorized_counter', array($this, 'all_categorized_counter'), 10);
                add_filter('njt_filebird_postsClauses', array($this, 'postsClauses'), 10, 2);
                add_filter( 'pll_filter_query_excluded_query_vars', array($this, 'excludedQueryVars'), 10, 3);
                
                // add_action('delete_attachment', array($this, 'delete_attachment'));
                // add_action('pll_translate_media', array($this, 'duplicate_attachment_to_folder'), 10, 3);
                // add_action('njt_filebird_save_attachment', array($this, 'save_attachment'), 10, 2);
            }
        }
    }

    public function set_total(){
        global $wpdb;
        // var_dump($this->pl_term_taxonomy_id);
        // exit;
        $this->total = (int) $wpdb->get_var("SELECT COUNT(tmp.ID) FROM
            (   
                SELECT posts.ID
                FROM $wpdb->posts AS posts
                LEFT JOIN $wpdb->term_relationships AS trs 
                ON posts.ID = trs.object_id
                LEFT JOIN $wpdb->postmeta AS postmeta
                ON (posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file')
                WHERE posts.post_type = 'attachment'
                AND trs.term_taxonomy_id IN ($this->pl_term_taxonomy_id)
                AND (posts.post_status = 'inherit' OR posts.post_status = 'private')
                GROUP BY posts.ID
            ) as tmp
        ");
    }

    public function postsClauses($clauses, $term_taxonomy_id){
        global $wpdb;
        $clauses['join'] .= " INNER JOIN $wpdb->term_relationships as tmp ON ($wpdb->posts.ID = tmp.object_id and tmp.term_taxonomy_id = $term_taxonomy_id)";
        return $clauses;
    }

    public function items_in_folder($term_count, $term_id)
    {
        global $wpdb;
        $term_taxonomy_id = get_term_by('id', (int) $term_id, NJT_FILEBIRD_FOLDER, OBJECT)->term_taxonomy_id;
        $counter = (int) $wpdb->get_var("SELECT COUNT(tmp.ID) FROM
        (
            SELECT posts.ID FROM $wpdb->posts AS posts  
            LEFT JOIN $wpdb->term_relationships AS tr1 
            ON (posts.ID = tr1.object_id) 
            INNER JOIN $wpdb->term_relationships AS tr2 
            ON (posts.ID = tr2.object_id and tr2.term_taxonomy_id IN ($term_taxonomy_id)) 
            LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file' ) 
            WHERE (tr1.term_taxonomy_id IN ($this->pl_term_taxonomy_id)) 
            AND posts.post_type = 'attachment' 
            AND ((posts.post_status = 'inherit' OR posts.post_status = 'private')) 
            GROUP BY posts.ID
        ) as tmp
        ");
        return $counter ? "data-number={$counter}" : '';
    }

    public function all_categorized_counter()
    {
        return $this->total ? "data-number={$this->total}" : '';
    }

    public function uncategorized_counter()
    {
        global $wpdb;
        $where = apply_filters("njt_booking_uncategorized_counter_pll_where", "");
        $fileInFolder = (int) $wpdb->get_var("SELECT COUNT(tmp.ID) FROM 
            (
                SELECT posts.ID
                FROM $wpdb->posts AS posts 
                INNER JOIN $wpdb->term_relationships AS tr1 
                ON posts.ID = tr1.object_id AND tr1.term_taxonomy_id IN ($this->pl_term_taxonomy_id)
                INNER JOIN $wpdb->term_relationships AS tr2 
                ON (tr2.object_id = posts.ID)
                JOIN $wpdb->term_taxonomy as tx
                ON tx.term_taxonomy_id = tr2.term_taxonomy_id AND tx.taxonomy = 'nt_wmc_folder' $where 
                LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file' ) 
                WHERE posts.post_type = 'attachment' 
                AND (posts.post_status='inherit' OR posts.post_status = 'private')
                GROUP BY posts.ID
            ) as tmp
        ");

        $uncategory = $this->total - $fileInFolder;

        return $uncategory ? "data-number={$uncategory}" : '';
    }

    
    public function duplicate_attachment_to_folder($post_id, $tr_id, $lang_slug)
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

    public function save_attachment($id, $folder_id){
        $pl_description = wp_get_post_terms($id, 'post_translations');
        if(!empty($pl_description)){
            $items_traslated = maybe_unserialize($pl_description[0]->description);
            foreach ($items_traslated as $k => $v) {
                if($v != $id){
                    wp_set_object_terms(intval($v), intval($_REQUEST['folder_id']), NJT_FILEBIRD_FOLDER, false);
                }
            }
        }
    }

    public function delete_attachment($post_ID)
    {
        if ($post_ID != $this->delete_process_id) {
            $pl_description = wp_get_post_terms($post_ID, 'post_translations');
            if(!empty($pl_description)){
                $items_traslated = maybe_unserialize($pl_description[0]->description);
                foreach ($items_traslated as $k => $v) {
                    $this->delete_process_id = $v;
                    if($v != $post_ID){
                        wp_delete_attachment(intval($v));
                    }
                }
            }
        }
    }
    public function excludedQueryVars($excludes, $query, $lang) {
      if(isset($query->query['fbv_count']) && $query->query_vars['post_type'] == 'attachment' ) {
        $excludes = array_values(array_diff($excludes, array('post__in', 'post__not_in')));
      }
      return $excludes;
    }
}
