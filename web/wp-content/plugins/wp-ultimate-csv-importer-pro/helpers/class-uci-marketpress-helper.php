<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

include_once ( plugin_dir_path(__FILE__) . '../includes/class-uci-helper.php' );

class SmackUCIMarketPressHelper extends SmackUCIHelper {

	public function importMarketPressProducts($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
		$returnArr = array();
		$mode_of_affect = 'Inserted';
		$assigned_author = '';
		global $wpdb, $uci_admin;
		$is_handle_duplicate = $duplicateHandling['is_duplicate_handle'];
		$conditions = $duplicateHandling['conditions'];
		$duplicate_action = $duplicateHandling['action'];
		if($is_handle_duplicate != 'off' && ($duplicate_action == 'Update' || $duplicate_action == 'Skip')):
			$mode = 'Update';
		endif;

		// Assign post type
		$data_array['post_type'] = 'product';

		$update_product_info = false;
		if($mode != 'Insert' && !empty($conditions) || $mode == 'Insert' && !empty($conditions)):
			if (in_array('ID', $conditions)) {
				$whereCondition = " ID = '{$data_array['ID']}'";
				$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}')  and (post_status != 'trash') order by ID DESC";
				$update_product_info = true;
			} elseif (in_array('post_title', $conditions)) {
				$whereCondition = " post_title = \"{$data_array['post_title']}\"";
				$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}')  and (post_status != 'trash') order by ID DESC";
				$update_product_info = true;
			} elseif (in_array('PRODUCTSKU', $conditions)) {
				$duplicate_check_query = "select DISTINCT p.ID from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = 'product' and p.post_status != 'trash' and pm.meta_value = '{$data_array['PRODUCTSKU']}'";
				$update_product_info = true;
			}elseif (in_array('post_name', $conditions)) {
				$whereCondition = " post_name = \"{$data_array['post_name']}\"";
                                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}')  and (post_status != 'trash') order by ID DESC";
                                $update_product_info = true;
			}
		endif;
		/*if($mode == 'Schedule'){
			if ($data_array['ID']) {
                                $whereCondition = " ID = '{$data_array['ID']}'";
                                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}')  and (post_status != 'trash') order by ID DESC";
                                $update_product_info = true;
                        } elseif ($data_array['post_title']) {
                                $whereCondition = " post_title = \"{$data_array['post_title']}\"";
                                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}')  and (post_status != 'trash') order by ID DESC";
                                $update_product_info = true;
                        } elseif ($data_array['PRODUCTSKU']) {
                                $duplicate_check_query = "select DISTINCT p.ID from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = 'product' and p.post_status != 'trash' and pm.meta_value = '{$data_array['PRODUCTSKU']}'";
                                $update_product_info = true;
                        }elseif ($data_array['post_name']) {
                                $whereCondition = " post_name = \"{$data_array['post_name']}\"";
                                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}')  and (post_status != 'trash') order by ID DESC";
                                $update_product_info = true;
                        }
		}*/
		/* Post date options */
		if(!isset( $data_array['post_date'] )) {
			$data_array['post_date'] = current_time('Y-m-d H:i:s');
		} else {
			if(strtotime( $data_array['post_date'] )) {
				$data_array['post_date'] = date( 'Y-m-d H:i:s', strtotime( $data_array['post_date'] ) );
			} else {
				$data_array['post_date'] = current_time('Y-m-d H:i:s');
			}
		}
		/* Post author options */
		if(!isset($data_array['post_author'])) {
			$data_array['post_author'] = 1;
		} else {
			if(isset( $data_array['post_author'] )) {
				$user_records = $uci_admin->get_from_user_details( $data_array['post_author'] );
				$data_array['post_author'] = $user_records['user_id'];
				$assigned_author = $user_records['message'];
			}
		}
		/* Post Status Options */
		if ( !empty($data_array['post_date']) ) {
			$data_array = $uci_admin->assign_post_status( $data_array );
		}

		// Import data with fast mode added by Fredrick Marks
		foreach (array('transition_post_status', 'save_post', 'pre_post_update', 'add_attachment', 'edit_attachment', 'edit_post', 'post_updated', 'wp_insert_post') as $act) {
			remove_all_actions($act);
		}

		// Initiate the action to insert / update the record
		if ($mode == 'Insert') {
			unset($data_array['ID']);
			$ID_result = $wpdb->get_results($duplicate_check_query);
			    if (is_array($ID_result) && !empty($ID_result)) {
				$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Duplicate found can not insert this";
				return array('MODE' => $mode, 'ERROR_MSG' => 'Duplicate found can not insert this');
			    }else{

				$retID = wp_insert_post($data_array); // Insert the core fields for the specific post type.
			    }

			if(is_wp_error($retID) || $retID == '') {
				$uci_admin->setSkippedRowCount( $uci_admin->getSkippedRowCount() + 1 );
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Product. " . $retID->get_error_message();
				return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
				#TODO Exception
			} else {
				// WPML support on post types
				global $sitepress, $uci_admin;
				if($sitepress != null) {
					$uci_admin->UCI_WPML_Supported_Posts($data_array, $retID);
				}
			}
			$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
			$uci_admin->setInsertedRowCount( $uci_admin->getInsertedRowCount() + 1 );
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Product ID: ' . $retID . ', ' . $assigned_author;
		} else {
			if ( ($mode == 'Update' || $mode == 'Schedule') && $update_product_info == true ) {
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount( $uci_admin->getSkippedRowCount() + 1 );
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Product found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$ID_result = $wpdb->get_results($duplicate_check_query);
				if (is_array($ID_result) && !empty($ID_result)) {
					$retID = $ID_result[0]->ID;
					$data_array['ID'] = $retID;
					wp_update_post($data_array);
					$mode_of_affect = 'Updated';
					$_SESSION[$eventKey]['summary']['updated'][] = $retID;
					$uci_admin->setUpdatedRowCount( $uci_admin->getUpdatedRowCount() + 1 );
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Product ID: ' . $retID . ', ' . $assigned_author;
				} else {
					$retID = wp_insert_post($data_array);
					if(is_wp_error($retID)) {
						$uci_admin->setSkippedRowCount( $uci_admin->getSkippedRowCount() + 1 );
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Product. " . $retID->get_error_message();
						return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
						#TODO Exception
					}
					$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
					$uci_admin->setInsertedRowCount( $uci_admin->getInsertedRowCount() + 1 );
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Product ID: ' . $retID . ', ' . $assigned_author;
				}
			} else {
				if($duplicate_action == 'Skip'){
					$uci_admin->setSkippedRowCount( $uci_admin->getSkippedRowCount() + 1 );
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Product found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$retID = wp_insert_post($data_array);
				$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
				$uci_admin->setInsertedRowCount( $uci_admin->getInsertedRowCount() + 1 );
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Product ID: ' . $retID . ', ' . $assigned_author;
			}
		}
		$media_handle = array();
		$shortcodes = '';
		$media_handle = isset($duplicateHandling['media_handling']) ? $duplicateHandling['media_handling'] : '';

		#TODO: Need to import the media for scheduler
		/* Set Featured Image */
		if(isset($data_array['featured_image'])) {
			if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $data_array['featured_image'], $matchedlist, PREG_PATTERN_ORDER)) {
				$nextGenInfo = array();
				$get_event_information = $uci_admin->getEventInformation();
				$media_settings = $uci_admin->parse_media_settings($get_event_information['media_handling'], $data_array);
				if(isset($get_event_information['media_handling']['nextgen_featured_image']) && isset($data_array['nextgen-gallery'])) {
					$nextGenInfo = array(
						'status'          => 'enabled',
						'directory'       => $data_array['nextgen-gallery'],
					);
				}
				$featured_image_info = array(
					'value'           => $data_array['featured_image'],
					'nextgen_gallery' => $nextGenInfo,
					'media_settings'  => $media_settings
				);
				update_option( 'smack_featured_' . $retID, $featured_image_info );
			}
		}
		// Media handling on the inline images
		if( !empty($data_array['post_content']) ) {
			$shortcodes = $this->capture_shortcodes($data_array['post_content'], $retID, 'Inline', $media_handle);
			if(!empty($media_handle['download_img_tag_src']) && $media_handle['download_img_tag_src'] == 'on'){
				$this->convert_local_image_src($data_array['post_content'], $retID, $media_handle);
			}
			if(!empty($shortcodes)){
				$this->convert_shortcode_to_image($shortcodes, $retID, 'Inline', $media_handle, $eventKey);
			}
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		if (!empty($data_array['post_author'])) {
			$returnArr['AUTHOR'] = isset($assigned_author) ? $assigned_author : '';
		}
		return $returnArr;
	}

	public function importDataForMarketPress_Variation($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
		global $wpdb, $uci_admin;
		$mode_of_affect = 'Inserted';
		$variation_data = $update_data = array();
		$product_id = isset($data_array['PRODUCTID']) ? $data_array['PRODUCTID'] : '';
		$variation_id = isset($data_array['VARIATIONID']) ? $data_array['VARIATIONID'] : '';
		if($mode == 'Insert') {
			$product_data = get_post($product_id);
			if(empty($product_data)){
				return false;
			}
			$meta_data = array();
			$meta_data['has_variation'] = 'yes';
			$meta_data['has_variations'] = 1;
			$meta_data['_has_variation'] = 'WPMUDEV_Field_Radio_Group';
			foreach ($meta_data as $custom_key => $custom_value) {
				update_post_meta($product_id, $custom_key, $custom_value);
			}
			if ($product_id) {
				$get_count_of_variations = $wpdb->get_results($wpdb->prepare("select count(*) as variations_count from $wpdb->posts where post_parent = %d and post_type = %s",$product_id,'mp_product_variation'));
				$variations_count = $get_count_of_variations[0]->variations_count;
				$menuorder_count = 0;
				if ($variations_count == 0) {
					$variations_count = '';
				} else {
					$variations_count = $variations_count + 1;
					$menuorder_count = $variations_count - 1;
					$variations_count = '-' . $variations_count;
				}
				$get_variation_data = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where ID = %d",$product_id));
				if($get_variation_data) {
					foreach ($get_variation_data as $key => $val) {
						if ($product_id == $val->ID) {
							$post_name = strtolower($val->post_title);
							$post_name = preg_replace('/[^a-zA-Z0-9._\-\s]/', "", $post_name);
							$post_name = preg_replace('/\s/', '-', $post_name);
							$variation_data['post_title'] = $val->post_title;
							$variation_data['post_date'] = $val->post_date;
							$variation_data['post_status'] = 'publish';
							$variation_data['comment_status'] = 'open';
							$variation_data['ping_status'] = 'open';
							$variation_data['menu_order'] = $menuorder_count;
							$variation_data['post_name'] = $post_name . $variations_count;
							$variation_data['post_parent'] = $val->ID;
							$variation_data['guid'] = site_url() . '?post_type=mp_product_variation&p=' . $val->ID;
							$variation_data['post_type'] = 'mp_product_variation';
						}else{
							#TODO Exception POST ID Does not exists
						}
					}
				}
			}
			// Initiate the action to insert / update the record
			$retID = wp_insert_post($variation_data); // Insert the core fields for the specific post type.
			if(is_wp_error($retID)) {
				$uci_admin->setSkippedRowCount( $uci_admin->getSkippedRowCount() + 1 );
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Variation. " . $retID->get_error_message();
				return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
				#TODO Exception
			} else {
			}
			$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
			$uci_admin->setInsertedRowCount( $uci_admin->getInsertedRowCount() + 1 );
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Variation ID: ' . $retID;
		}
		if($mode == 'Update' || $mode == 'Schedule'){
			$variation_data = get_post($variation_id);
			if(empty($variation_data)){
				return false;
			}
			if($variation_id){
				$get_update_data = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where ID = %s and post_type = %s",$variation_id,'mp_product_variation'),ARRAY_A);
				if($get_update_data) {
					$existing_variation_id = $get_update_data[0]['ID'];
					if ($existing_variation_id == $variation_id) {
						$variation_data = $get_update_data[0];
					} else {
						#TODO Exception POST ID Does not exists
					}
				}
			}
			wp_update_post($variation_data);
			$mode_of_affect = 'Updated';
			$_SESSION[$eventKey]['summary']['updated'][] = $variation_id;
			$retID = $variation_id;
			$uci_admin->setUpdatedRowCount( $uci_admin->getUpdatedRowCount() + 1 );
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Variation ID: ' . $retID;
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;

		/* Set Featured Image */
		if(isset($data_array['featured_image'])) {
			if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $data_array['featured_image'], $matchedlist, PREG_PATTERN_ORDER)) {
				$nextGenInfo = array();
				$get_event_information = $uci_admin->getEventInformation();
				$media_settings = $uci_admin->parse_media_settings($get_event_information['media_handling'], $data_array);
				if(isset($get_event_information['media_handling']['nextgen_featured_image']) && isset($data_array['nextgen-gallery'])) {
					$nextGenInfo = array(
						'status'          => 'enabled',
						'directory'       => $data_array['nextgen-gallery'],
					);
				}
				$featured_image_info = array(
					'value'           => $data_array['featured_image'],
					'nextgen_gallery' => $nextGenInfo,
					'media_settings'  => $media_settings
				);
				update_option( 'smack_featured_' . $retID, $featured_image_info );
			}
		}
		return $returnArr;
	}

	public function importMetaInformation($data_array, $pID) {
		global $uci_admin;
		global $wpdb;
		$metaData = $variation_names = $variation_values = array();
		if ( in_array( 'wordpress-ecommerce/marketpress.php', $uci_admin->get_active_plugins() ) ) {
			foreach ($data_array as $mKey => $mVal) {
				switch ($mKey) {
					case 'variation' :
						$exploded_variations = '';
						if ($data_array[$mKey]) {
							$exploded_variations = explode(',', $data_array[$mKey]);
						}
						$metaData['mp_var_name'] = $exploded_variations;
						break;
					case 'sku' :
						$exploded_product_sku = '';
						if ($data_array[$mKey]) {
							if (strpos($data_array[$mKey], ',') !== false) {
								$exploded_product_sku = explode(',', $data_array[$mKey]);
							}else{
								$exploded_product_sku = $data_array[$mKey];	
							}
						}
						$metaData['sku'] = $exploded_product_sku;
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['SKU'] = $data_array[$mKey];
						break;
					case 'regular_price' :
						$exploded_regular_prices = '';
						if ($data_array[$mKey]) {
							if (strpos($data_array[$mKey], ',') !== false) {
								$exploded_regular_prices = explode(',', $data_array[$mKey]);
							}else{
								$exploded_regular_prices = $data_array[$mKey];
							}
						}
						$metaData['regular_price'] = $exploded_regular_prices;
						if(isset($exploded_regular_prices[0]))
							$metaData['mp_price_sort'] = $exploded_regular_prices[0];

						break;
					case 'is_sale' :
						if($data_array[$mKey] == 'on') {
							$data_array[$mKey] = 1;
						}
						$metaData['mp_is_sale'] = $data_array[$mKey];
						break;
					case 'sale_price' :
						$exploded_sale_prices = '';
						if ($data_array[$mKey]) {
							if (strpos($data_array[$mKey], ',') !== false) {
								$exploded_sale_prices = explode(',', $data_array[$mKey]);
							}else{
								$exploded_sale_prices =  $data_array[$mKey];
							}
						}
						$metaData['sale_price'] = $exploded_sale_prices;
						break;
					case 'track_inventory' :
						$track_inventory = 0;
						if($data_array[$mKey] == 'on') {
							$track_inventory = $data_array[$mKey] = 1;
						}
						if (!is_numeric($data_array[$mKey])) {
							$data_array[$mKey] = strtolower($data_array[$mKey]);
						}
						if ($data_array[$mKey] == 1 || $data_array[$mKey] == 'yes') {
							$track_inventory = 1;
						}
						if ($data_array[$mKey] == 0 || $data_array[$mKey] == 'no') {
							$track_inventory = 0;
						}
						$metaData['mp_track_inventory'] = $track_inventory;
						break;
					case 'inventory' :
						$exploded_inventories = array();
						if ($data_array[$mKey]) {
							$exploded_inventories = explode(',', $data_array[$mKey]);
						}
						$metaData['mp_inventory'] = $exploded_inventories;
						break;
					case 'track_limit' :
						$track_limit = '';
						if($data_array[$mKey] == 'on') {
							$track_limit = $data_array[$mKey] = 1;
						}
						if (!is_numeric($data_array[$mKey])) {
							$data_array[$mKey] = strtolower($data_array[$mKey]);
						}
						if ($data_array[$mKey] == 1 || $data_array[$mKey] == 'yes') {
							$track_limit = 1;
						}
						if ($data_array[$mKey] == 0 || $data_array[$mKey] == 'no') {
							$track_limit = 0;
						}
						$metaData['mp_track_limit'] = $track_limit;
						break;
					case 'limit_per_order' :
						$exploded_mplimit = '';
						if ($data_array[$mKey]) {
							$exploded_mplimit = explode(',', $data_array[$mKey]);
						}
						$metaData['mp_limit'] = $exploded_mplimit;
						break;
					case 'product_link' :
						$metaData['mp_product_link'] = $data_array[$mKey];
						break;
					case 'is_special_tax' :
						$is_special_tax = 0;
						if($data_array[$mKey] == 'on') {
							$is_special_tax = $data_array[$mKey] = 1;
						}
						if(!is_numeric($data_array[$mKey])) {
							$data_array[$mKey] = strtolower($data_array[$mKey]);
						}
						if($data_array[$mKey] == 1 || $data_array[$mKey] == 'yes') {
							$is_special_tax = 1;
						}
						if($data_array[$mKey] == 0 || $data_array[$mKey] == 'no') {
							$is_special_tax = 0;
						}
						$metaData['mp_is_special_tax'] = $is_special_tax;
						break;
					case 'special_tax' :
						$metaData['mp_special_tax'] = $data_array[$mKey];
						break;
					case 'sales_count' :
						$metaData['mp_sales_count'] = $data_array[$mKey];
						break;
					case 'extra_shipping_cost' :
						$extra_cost['extra_cost'] = $data_array[$mKey];
						$metaData['mp_shipping'] = $extra_cost;
						break;
					case 'file_url' :
						$metaData['mp_file'] = $data_array[$mKey];
						break;
				}
			}
		} elseif (in_array('marketpress/marketpress.php', $uci_admin->get_active_plugins())) {
			foreach ($data_array as $cKey => $cVal) {
				switch ($cKey) {
					case 'product_type':
						$metaData['product_type'] = $data_array[$cKey];
						$metaData['_product_type'] = 'WPMUDEV_Field_Select';
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Type of Product'] = $data_array[$cKey];
						break;
					case 'sku':
						$metaData['sku'] = $data_array[$cKey];
						$metaData['_sku'] = 'WPMUDEV_Field_Text';
						break;
					case 'per_order_limit':
						$metaData['per_order_limit'] = $data_array[$cKey];
						$metaData['_per_order_limit'] = 'WPMUDEV_Field_Text';
						break;
					case 'has_sale':
						$metaData['has_sale'] = $data_array[$cKey];
						$metaData['_has_sale'] = 'WPMUDEV_Field_Checkbox';
						break;
					case 'sale_price':
						$metaData['sort_price'] = $data_array[$cKey];
						$metaData['sale_price_amount'] = $data_array[$cKey];
						break;
					case 'regular_price':
						$metaData['regular_price'] = $data_array[$cKey];
						$metaData['_regular_price'] = 'WPMUDEV_Field_Text';
						break;
					case 'sale_price_start_date':
						if(strtotime($data_array[$cKey])){
							$sale_price_start_date = date('Y-m-d', strtotime($data_array[$cKey]));
						}else{
							$sale_price_start_date = current_time('Y-m-d');
						}
						$metaData['sale_price_start_date'] = $sale_price_start_date;
						$metaData['_sale_price_start_date'] = 'WPMUDEV_Field_Datepicker';
						break;
					case 'sale_price_end_date':
						if(strtotime($data_array[$cKey])){
							$sale_price_end_date = date('Y-m-d', strtotime($data_array[$cKey]));
						}else{
							$sale_price_end_date = current_time('Y-m-d');
						}
						$metaData['sale_price_end_date'] = $sale_price_end_date;
						$metaData['_sale_price_end_date'] = 'WPMUDEV_Field_Datepicker';
						break;
					case 'charge_tax':
						$metaData['charge_tax'] = $data_array[$cKey];
						$metaData['_charge_tax']  = 'WPMUDEV_Field_Checkbox';
						break;
					case 'special_tax_rate':
						$metaData['special_tax_rate'] = $data_array[$cKey];
						$metaData['_special_tax_rate'] = 'WPMUDEV_Field_Text';
						break;
					case 'charge_shipping':
						$metaData['charge_shipping'] = $data_array[$cKey];
						$metaData['_charge_shipping'] = 'WPMUDEV_Field_Checkbox';
						break;
					case 'weight_pounds':
						$metaData['weight'] = serialize(array('weight_pounds', 'weight_extra_shipping_cost')); #TODO: serialized value
						$metaData['_weight'] = 'WPMUDEV_Field_Complex';
						$metaData['weight_pounds'] = $data_array[$cKey];
						$metaData['_weight_pounds'] = 'WPMUDEV_Field_Text';
						break;
					case 'weight_ounces':
						$metaData['weight_ounces'] = $data_array[$cKey];
						$metaData['_weight_ounces'] = 'WPMUDEV_Field_Text';
						break;
					case 'weight_extra_shipping_cost':
						$metaData['weight_extra_shipping_cost'] = $data_array[$cKey];
						$metaData['_weight_extra_shipping_cost'] = 'WPMUDEV_Field_Text';
						break;
					case 'inventory_tracking':
						$metaData['inv'] = serialize(array('inv_inventory', 'inv_out_of_stock_purchase')); #TODO: serialized value
						$metaData['_inv'] = 'WPMUDEV_Field_Complex';
						$metaData['inventory_tracking'] = $data_array[$cKey];
						$metaData['_inventory_tracking'] = 'WPMUDEV_Field_Checkbox';
						break;
					case 'quantity':
						$metaData['inv_inventory'] = $data_array[$cKey];
						$metaData['_inv_inventory'] = 'WPMUDEV_Field_Text';
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Quantity'] = $data_array[$cKey];
						break;
					case 'inv_out_of_stock_purchase':
						$metaData['inv_out_of_stock_purchase'] = $data_array[$cKey];
						$metaData['_inv_out_of_stock_purchase'] = 'WPMUDEV_Field_Checkbox';
						break;
					case 'related_products':
						$metaData['related_products'] = $data_array[$cKey];
						$metaData['_related_products'] = 'WPMUDEV_Field_Post_Select';
						break;
					case 'product_images':
						if (is_numeric($data_array[$cKey])) {
							$metaData['mp_product_images'] = $data_array[$cKey];
						} else {
							#TODO mp_product_images
						}
						break;
					case 'file_url':
						$metaData['file_url'] = $data_array[$cKey];
						$metaData['_file_url'] = 'WPMUDEV_Field_File';
						break;
					case 'external_url':
						$metaData['external_url'] = $data_array[$cKey];
						$metaData['_external_url'] = 'WPMUDEV_Field_Text';
						break;
					//variation import
					case 'mp_variation_image' :
						if (is_numeric($data_array[$cKey])) {
							$metaData['_thumbnail_id'] = $data_array[$cKey];
						}else{
							#TODO thumbnail_id
						}
						break;

					case 'mp_variation_name':
						$mp_variation_name = explode('|', $data_array[$cKey]);
						foreach($mp_variation_name as $item => $value) {
							$variation_names[$item] = $value;
						}
						break;
					case 'mp_variation_value':
						$mp_variation_value = explode('|', $data_array[$cKey]);
						foreach($mp_variation_value as $item => $value) {
							$variation_values[$item] = $value;
						}
						break;
					case 'has_variation_content':
						$metaData['has_variation_content'] = $data_array[$cKey];
						break;
					case 'variation_content_type':
						$metaData['variation_content_type'] = $data_array[$cKey];
						break;
					case 'variation_content_desc':
						$variation_content_desc = $data_array[$cKey];
						break;
				}
			}
		}
		if(!empty($metaData)) {
			if(isset($mp_variation_name) && isset($mp_variation_value)){
				foreach($variation_names as $key => $value) {
					$get_attributeLabel             = $wpdb->get_results( $wpdb->prepare("SELECT attribute_id, attribute_name FROM {$wpdb->prefix}mp_product_attributes WHERE attribute_name = '{%s}'",$value),ARRAY_A);
					if ( empty( $get_attributeLabel ) ) {
						$wpdb->insert( "{$wpdb->prefix}mp_product_attributes", array(
							'attribute_name'             => $value,
							'attribute_terms_sort_by'    => 'ID',
							'attribute_terms_sort_order' => 'ASC'
						) );
						$get_attribute_id = $wpdb->insert_id;
						$attribute_id     = 'product_attr_' . $get_attribute_id;
						$termarray        = array(
							'term_id' => $pID,
							'name'    => $value,
							'slug'    => $mp_variation_value[$key]
						);
						register_taxonomy( $attribute_id, 'product', array(
							'show_ui'           => false,
							'show_in_nav_menus' => false,
							'hierarchical'      => true,
						) );
						$reg_attribute_id = wp_set_object_terms( $pID, $mp_variation_value[$key], $attribute_id );
					} else {
						$existings_attid = $wpdb->get_results($wpdb->prepare( "SELECT attribute_id FROM {$wpdb->prefix}mp_product_attributes WHERE attribute_name = '{%s}'",$value),ARRAY_A );
						$attribute_id    = 'product_attr_' . $existings_attid[0]->attribute_id;
						register_taxonomy( $attribute_id, 'product', array(
							'show_ui'           => false,
							'show_in_nav_menus' => false,
							'hierarchical'      => true,
						) );
						wp_set_object_terms( $pID, $mp_variation_value[$key], $attribute_id );
					}
				}
			}
			if(isset($metaDatas['has_variation_content']) && isset($metaDatas['variation_content_type']) && isset($variation_content_desc)){
				$variation_content = array('post_content' => $variation_content_desc,'ID' => $pID);
				wp_update_post($variation_content);
			}
			foreach ($metaData as $custom_key => $custom_value) {
				update_post_meta($pID, $custom_key, $custom_value);
			}
		}
	}
}
