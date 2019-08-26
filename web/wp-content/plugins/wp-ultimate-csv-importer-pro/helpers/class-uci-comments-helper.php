<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackUCICommentsHelper {

	public function importComments($array) {
		global $wpdb, $uci_admin;
		$data_array = $array;
		$commentid = '';
		$post_id = $data_array['comment_post_ID'];
		$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "' and post_status in ('publish','draft','future','private','pending')", 'ARRAY_A');
		$valid_status = array('1', '0', 'spam');
		if(empty($data_array['comment_approved'])) {
			$data_array['comment_approved'] = 0;
		}
		if(!in_array($data_array['comment_approved'], $valid_status)) {
			$data_array['comment_approved'] = 0;
		}
		$data_array['comment_approved'] = trim($data_array['comment_approved']);
		if ($post_exists) {
			$retID = wp_insert_comment($data_array);
			$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Comment ID: ' . $retID;
			$mode_of_affect = 'Inserted';
		} else {
			$retID = $commentid;
			$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to unknown post ID.";
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		return $returnArr;
	}
}