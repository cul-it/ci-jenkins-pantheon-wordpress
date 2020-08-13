<?php
if (!defined('ABSPATH')) {
  exit;
}
class FileBird_Helpers {
  public static function getUncategorizedAttachmentCount() {
    global $wpdb;
    $wp_posts = $wpdb->prefix . "posts";
    $term_relationships = $wpdb->prefix . 'term_relationships';
    $term_taxonomy = $wpdb->prefix . 'term_taxonomy';
    $where = apply_filters('njt_booking_uncategorized_counter_where', '');

    $result = $wpdb->get_var("SELECT COUNT(*)
      FROM $wp_posts AS posts
      WHERE 1=1 AND (posts.ID NOT IN
      (SELECT object_id FROM $term_relationships WHERE term_taxonomy_id IN(
        SELECT term_taxonomy_id from $term_taxonomy where taxonomy = 'nt_wmc_folder' $where))
      ) AND posts.post_type = 'attachment' AND ((posts.post_status = 'inherit' OR posts.post_status = 'private'))");
    return $result ? $result : 0;
  }
  public static function foldersForEachUserEnabled() {
    $filebird_option = get_option('filebird_setting');
    return is_array($filebird_option) && isset($filebird_option['foldersForEachUser']) && ($filebird_option['foldersForEachUser'] === true);
  }
  public static function njtGetTerms($taxonomy, $args) {
    global $wpdb;
    if(self::foldersForEachUserEnabled() === true) {
      $args['meta_query'] = array(
        array(
          'key'     => 'fb_created_by',
          'value'   => get_current_user_id(),
          'compare' => '=',
        ),
      );
    } else {
      $terms_have_no_author = self::termsHaveNoAuthor('term_taxonomy_id');
      if(count($terms_have_no_author) == 0) $terms_have_no_author = array(-1);
      $args['term_taxonomy_id'] = $terms_have_no_author;
    }
    $args['njt-fb'] = true;
    return get_terms($taxonomy, $args);

  }
  public static function njtMoveImage($image_id, $folder_id) {
    do_action('njt_fb_before_moving', $image_id, $folder_id);
    wp_set_object_terms(
      (int)$image_id,
      (int)$folder_id,
      NJT_FILEBIRD_FOLDER,
      apply_filters('njt_fb_append_when_move', false, $image_id, $folder_id)
    );
  }
  public static function termsByUser($user_id) {
    global $wpdb;
    return $wpdb->get_col($wpdb->prepare('SELECT term_id FROM %1$s WHERE meta_key = \'fb_created_by\' and meta_value = %2$d', $wpdb->termmeta, $user_id));
  }

  /**
   * @var String $select. term_id or term_taxonomy_id
   */
  public static function termsHaveNoAuthor($select = 'term_id') {
    global $wpdb;
    return $wpdb->get_col("SELECT $select FROM $wpdb->term_taxonomy WHERE term_id NOT IN (SELECT term_id FROM $wpdb->termmeta WHERE meta_key = 'fb_created_by') AND taxonomy = '".NJT_FILEBIRD_FOLDER."' GROUP BY term_id");
  }

  public static function foldersFromEnhanced($parent = 0, $flat = false) {
    global $wpdb;
    $folders = $wpdb->get_results($wpdb->prepare('SELECT t.term_id as id, t.name as title, tt.term_taxonomy_id FROM %1$s as t  INNER JOIN %2$s as tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy = \'media_category\' AND tt.parent = %3$d', $wpdb->terms, $wpdb->term_taxonomy, $parent));
    foreach ($folders as $k => $folder) {
      $folders[$k]->parent = $parent;
    }
    if($flat) {
      foreach ($folders as $k => $folder) {
        $children = self::foldersFromEnhanced($folder->id, $flat);
        foreach($children as $k2 => $v2) {
          $folders[] = $v2;
        }
      }
    } else {
      foreach ($folders as $k => $folder) {
        $folders[$k]->children = self::foldersFromEnhanced($folder->id, $flat);
      }
    }
    return $folders;
  }
  public static function foldersFromWpmlf($parent = 0, $flat = false) {
    global $wpdb;

    $table_name = $wpdb->base_prefix.'mgmlp_folders';
   
    $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
    if ( ! $wpdb->get_var( $query ) == $table_name ) {
        return array();
    }

    $folders = $wpdb->get_results($wpdb->prepare('select p.ID as id, p.post_title as title, mlf.folder_id as parent from %1$s as p LEFT JOIN %2$s as mlf ON(p.ID = mlf.post_id) where p.post_type = \'mgmlp_media_folder\' and mlf.folder_id = \'%3$s\' order by mlf.folder_id', $wpdb->posts, $wpdb->prefix . 'mgmlp_folders', $parent));

    if($flat) {
      foreach ($folders as $k => $folder) {
        $children = self::foldersFromWpmlf($folder->id, $flat);
        foreach($children as $k2 => $v2) {
          $folders[] = $v2;
        }
      }
    } else {
      foreach ($folders as $k => $folder) {
        $folders[$k]->children = self::foldersFromWpmlf($folder->id, $flat);
      }
    }
    return $folders;
  }
}
