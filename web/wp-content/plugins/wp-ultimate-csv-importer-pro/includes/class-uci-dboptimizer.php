<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCIDBOptimizer {

	/**
	 * Function for optimizing the database
	 *
	 */
	function Process_dboptimizer($request) {
		global $wpdb;
		$affected_rows = array('orphaned' => 'non_affected', 'unassignedTags' => 'non_affected', 'postpagerevisions' => 'non_affected', 'autodraftedpostpage' => 'non_affected', 'postpagetrash' => 'non_affected', 'spamcomments' => 'non_affected', 'trashedcomments' => 'non_affected', 'unapprovedcomments' => 'non_affected', 'pingbackcomments' => 'non_affected', 'trackbackcomments' => 'non_affected');
		if(sanitize_text_field($_POST['orphaned']) == 1) {
			$array_post_id = '';
			$get_post_id = $wpdb->get_results($wpdb->prepare("select DISTINCT pm.post_id from $wpdb->postmeta pm JOIN $wpdb->posts wp on wp.ID = %d", 'pm.post_id'));
			foreach($get_post_id as $postID) {
				$array_post_id .= $postID->post_id . ',';
			}
			$array_post_id = substr($array_post_id, 0, -1);
			$get_post_meta_id = $wpdb->get_results($wpdb->prepare("DELETE FROM $wpdb->postmeta where post_id not in (%d)",$array_post_id),ARRAY_A);
			$affected_rows['orphaned'] = $wpdb->rows_affected;
		} 
		if(sanitize_text_field($_POST['unassignedTags']) == 1) {
			$wpdb->query("DELETE t,tt FROM  $wpdb->terms t INNER JOIN $wpdb->term_taxonomy tt ON t.term_id=tt.term_id WHERE tt.taxonomy='post_tag' AND tt.count=0");
			$affected_rows['unassignedTags'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['postpagerevisions']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
			$affected_rows['postpagerevisions'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['autodraftedpostpage']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
			$affected_rows['autodraftedpostpage'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['postpagetrash']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'trash'");
			$affected_rows['postpagetrash'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['spamcomments']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
			$affected_rows['spamcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['trashedcomments']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
			$affected_rows['trashedcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['unapprovedcomments']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = '0'");
			$affected_rows['unapprovedcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['pingbackcomments']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_type = 'pingback'");
			$affected_rows['pingbackcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['trackbackcomments']) == 1) {
			$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_type = 'trackback'");
			$affected_rows['trackbackcomments'] = $wpdb->rows_affected;
		}
		print_r(json_encode($affected_rows));
		die();
	}
}
