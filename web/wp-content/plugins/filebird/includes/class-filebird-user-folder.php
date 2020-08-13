<?php

class FileBird_User_Folder
{
  public function __construct()
  {
    add_action( 'njt_fb_after_inserting_folfer', array($this, 'afterInsertingFolfer'));
    add_filter( 'njt_fb_can_rename_folder', array($this, 'currenFolderBelongsCurrentUser'), 10, 2);
    add_filter( 'njt_fb_can_move_folder', array($this, 'currenFolderBelongsCurrentUser'), 10, 2);
    add_filter( 'njt_fb_can_delete_folder', array($this, 'currenFolderBelongsCurrentUser'), 10, 2);
    add_filter( 'njt_fb_can_new_edit_attachment_folder', array($this, 'currenFolderBelongsCurrentUser'), 10, 2);
    add_filter( 'njt_fb_can_save_attachment', array($this, 'currenFolderBelongsCurrentUser'), 10, 2);
    add_action('deleted_post', array($this, 'deletedPost'));
    add_filter('njt_booking_uncategorized_counter_where', array($this, 'uncategorized_counter_where'));
    add_filter('njt_booking_uncategorized_counter_wpml_query', array($this, 'uncategorized_counter_wpml_query'));
    add_filter('njt_booking_uncategorized_counter_pll_where', array($this, 'uncategorized_counter_pll_where'));
    add_filter('njt_fb_append_when_move', array($this, 'append_when_move'), 10, 3);
    add_action('njt_fb_before_moving', array($this, 'before_moving'), 10, 2);
  }

  public function afterInsertingFolfer($folder_id) {
    if(FileBird_Helpers::foldersForEachUserEnabled() === true) {
      add_term_meta($folder_id, 'fb_created_by', get_current_user_id());
      //update_option(NJT_FILEBIRD_FOLDER . '_children', 0);
    }
  }
  public function currenFolderBelongsCurrentUser($can, $folder_id) {
    if(FileBird_Helpers::foldersForEachUserEnabled() === true && $folder_id > 0) {
      $can = (int)get_term_meta($folder_id, 'fb_created_by', true) == (int)get_current_user_id();
    }
    return $can;
  }

  public function deletedPost($post_id) {
    global $wpdb;
    $post_id = intval($post_id);
    $post = get_post($post_id);

    if(!is_object($post) || $post->post_type != 'attachment') {
      return;
    }
    $current_user_id = get_current_user_id();
    //gets all folders contain this file, but not created by current_user
    $query = "SELECT `term_taxonomy_id` FROM $wpdb->term_relationships 
    WHERE object_id = $post_id 
    AND `term_taxonomy_id` NOT IN (
      SELECT term_id FROM $wpdb->termmeta WHERE `meta_key` = 'fb_created_by' AND `meta_value` = $current_user_id
    )";
    
    $folders = $wpdb->get_col($query);
    //refresh folders
    foreach($folders as $k => $folder) {
      $q = "DELETE FROM $wpdb->term_relationships WHERE 
      object_id = $post_id  
      AND term_taxonomy_id = $folder";
      $wpdb->query($q);
      wp_update_term_count_now([$folder], 'nt_wmc_folder');
    }
  }
  public function uncategorized_counter_wpml_query($query) {
    return $this->appendConditionForTerms($query);
  }
  public function uncategorized_counter_pll_where($where) {
    return $this->appendConditionForTerms($where);
  }
  public function uncategorized_counter_where($where) {
    return $this->appendConditionForTerms($where);
  }
  public function append_when_move($append, $post_id, $folder_id) {
    global $wpdb;
    return FileBird_Helpers::foldersForEachUserEnabled() === true;
  }
  public function before_moving($image_id, $folder_id) {
    global $wpdb;
    if(FileBird_Helpers::foldersForEachUserEnabled() === true) {
      $current_user_id = get_current_user_id();
      $query = "SELECT tt.term_id FROM $wpdb->term_taxonomy as tt INNER JOIN $wpdb->termmeta as meta ON (tt.term_id = meta.term_id) WHERE tt.taxonomy = 'nt_wmc_folder' AND meta.meta_key = 'fb_created_by' AND meta.meta_value = '$current_user_id' AND tt.term_taxonomy_id IN (SELECT ts.term_taxonomy_id FROM $wpdb->term_relationships as ts WHERE ts.object_id = $image_id)";
      $delete_term_ids = $wpdb->get_col($query);
      $delete_term_ids = array_map('intval', $delete_term_ids);
      wp_remove_object_terms( $image_id, $delete_term_ids, NJT_FILEBIRD_FOLDER );
    }
  }
  private function appendConditionForTerms($str) {
    global $wpdb;
    $terms = array();
    if(FileBird_Helpers::foldersForEachUserEnabled() === true) {
      $terms = FileBird_Helpers::termsByUser(get_current_user_id());
    } else {
      $terms = FileBird_Helpers::termsHaveNoAuthor();
    }
    if(count($terms) <= 0) {
      $terms = array(-1);
    }
    $str .= ' and term_id in ('.implode(',', $terms).')';
    return $str;
  }
}
