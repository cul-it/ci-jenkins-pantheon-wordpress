<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackUCIWooCommerceHelper {

	public function importWooCommerceProducts ($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
		$data_array['PRODUCTSKU'] = trim($data_array['PRODUCTSKU']);
		/*if(isset($data_array['PRODUCTSKU']) && $data_array['PRODUCTSKU'] != '' && $data_array['PRODUCTSKU'] != null) {
			$data_array['PARENTSKU'] = $data_array['PRODUCTSKU'];
			$returnArr = $this->importWooCommerceVariations($data_array, 'WooCommerceVariations', $mode, $eventKey, $duplicateHandling);
			return $returnArr;
		}*/
		$returnArr = array();
		$assigned_author = '';
		$mode_of_affect = 'Inserted';
		global $wpdb, $uci_admin;
		$is_handle_duplicate = $duplicateHandling['is_duplicate_handle'];
		$conditions = $duplicateHandling['conditions'];
		$duplicate_action = $duplicateHandling['action'];
		if($is_handle_duplicate != 'off' && ($duplicate_action == 'Update' || $duplicate_action == 'Skip')):
			$mode = 'Update';
		endif;
		$update_product_info = false;

		// Assign post type
		$data_array['post_type'] = 'product';

		if($mode != 'Insert' && !empty($conditions) || $mode == 'Insert' && !empty($conditions)):
			if (in_array('ID', $conditions)) {
				$whereCondition = " ID = '{$data_array['ID']}'";
				$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
				$update_product_info = true;
			} elseif (in_array('post_title', $conditions)) {
				$whereCondition = " post_title = \"{$data_array['post_title']}\"";
				$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
				$update_product_info = true;
			} elseif (in_array('PRODUCTSKU', $conditions)) {
				$duplicate_check_query = "select DISTINCT p.ID from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = 'product' and p.post_status != 'trash' and pm.meta_value = '{$data_array['PRODUCTSKU']}'";
				$update_product_info = true;
			}elseif(in_array('post_name', $conditions)){
                                $whereCondition = " post_name = \"{$data_array['post_name']}\"";
				$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
				$update_product_info = true;				
			}
		endif;
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
		/* foreach (array('transition_post_status', 'save_post', 'pre_post_update', 'add_attachment', 'edit_attachment', 'edit_post', 'post_updated', 'wp_insert_post') as $act) {
			remove_all_actions($act);
		} */
		#print_r($data_array); die;
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
			if(is_wp_error($retID || $retID == '')) {
				$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Product. " . $retID->get_error_message();
				return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
				#TODO Exception
			} else {
				//WPML support on post types
				global $sitepress;
				if($sitepress != null) {
					$uci_admin->UCI_WPML_Supported_Posts($data_array, $retID);
				}
			}
			$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
			$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Product ID: ' . $retID . ', ' . $assigned_author;
		} else {
			if ( ($mode == 'Update' || $mode == 'Schedule') && $update_product_info == true ) {
				if($duplicate_action == 'Skip'){
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
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
					$uci_admin->setUpdatedRowCount($uci_admin->getUpdatedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Product ID: ' . $retID . ', ' . $assigned_author;
				} else {
					$retID = wp_insert_post($data_array);
					if(is_wp_error($retID)) {
						$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Product. " . $retID->get_error_message();
						return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
						#TODO Exception
					}
					$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
					$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Product ID: ' . $retID . ', ' . $assigned_author;
				}
			} else {
				if($duplicate_action == 'Skip'){
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Product found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$retID = wp_insert_post($data_array);
				$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
				$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
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

		#TODO: Need to add the inline image support with scheduling option
		// Media handling on the inline images
		if( !empty($data_array['post_content']) ) {
			$shortcodes = $uci_admin->capture_shortcodes($data_array['post_content'], $retID, 'Inline', $media_handle);
			if(!empty($media_handle['download_img_tag_src']) && $media_handle['download_img_tag_src'] == 'on'){
				$uci_admin->convert_local_image_src($data_array['post_content'], $retID, $media_handle);
			}
			if(!empty($shortcodes)){
				$uci_admin->convert_shortcode_to_image($shortcodes, $retID, 'Inline', $media_handle,$eventKey);
			}
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		if (!empty($data_array['post_author'])) {
			$returnArr['AUTHOR'] = isset($assigned_author) ? $assigned_author : '';
		}
		return $returnArr;
	}

	public function importMetaInformation ($data_array, $pID) {
		global $uci_admin;
		global $wpdb;
		$event_information = $uci_admin->getEventInformation();
		$type = $event_information['import_type'];
		if(isset($event_information['csv_row_mapping']['CORE']['PRODUCTSKU']) && $event_information['csv_row_mapping']['CORE']['PRODUCTSKU'] != '') {
			$type = 'WooCommerceVariations';
		}
		$metaData = array();
		$get_csvpro_settings = get_option('sm_uci_pro_settings');
		$visibility = 2;
		foreach ($data_array as $ekey => $eval) {
			switch ($ekey) {
				case 'stock_qty' :
					$metaData['_stock'] = $data_array[$ekey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Stock Qty'] = $data_array[$ekey];
					break;
				case 'visibility' :
					//Product visibility is taxonomy based instead of meta based in woocommerce 3.0.0 
				$plugininfo = get_plugin_data( WP_PLUGIN_DIR .'/'.'woocommerce/woocommerce.php');
				$versionOfWoocom = $plugininfo['Version'];
					$visibility = '';
					if ($data_array[$ekey] == 1) {
						$visibility = 'visible';
					}
					if ($data_array[$ekey] == 2) {
						$visibility = 'catalog';
					}
					if ($data_array[$ekey] == 3) {
						$visibility = 'search';
					}
					if ($data_array[$ekey] == 4) {
						$visibility = 'hidden';
					}
					if($versionOfWoocom >= 3){
						if ($product = wc_get_product($pID)) {
							$product->set_catalog_visibility($visibility);
							$product->save();
						}
					}
					else
						$metaData['_visibility'] = $visibility;
					break;
				case 'stock_status' :
					$stock_status = '';
					if ($data_array[$ekey] == 1) {
						$stock_status = 'instock';
					}
					if ($data_array[$ekey] == 2) {
						$stock_status = 'outofstock';
					}
					$metaData['_stock_status'] = $stock_status;
					break;
				case 'downloadable' :
					$metaData['_downloadable'] = $data_array[$ekey];
					break;
				case 'virtual' :
					$metaData['_virtual'] = $data_array[$ekey];
					break;
				case 'product_image_gallery' :
					#TODO: Need to add media handling support.
					$get_all_gallery_images = explode('|', $data_array[$ekey]);
					$gallery_image_ids = '';
					foreach($get_all_gallery_images as $gallery_image) {
						if(is_numeric($gallery_image)) {
							$gallery_image_ids .= $gallery_image . ',';
						} else {
							$attachmentId = $uci_admin->set_featureimage($gallery_image, $pID);
							$gallery_image_ids .= $attachmentId . ',';
						}
					}
					$product_image_gallery[$ekey] = $gallery_image_ids;
					break;
				case 'regular_price' :
					$metaData['_regular_price'] = $data_array[$ekey];
					$metaData['_price'] = $data_array[$ekey];
					break;
				case 'sale_price' :
					$metaData['_sale_price'] = $data_array[$ekey];
					break;
				case 'tax_status' :
					$tax_status = '';
					if ($data_array[$ekey] == 1) {
						$tax_status = 'taxable';
					}
					if ($data_array[$ekey] == 2) {
						$tax_status = 'shipping';
					}
					if ($data_array[$ekey] == 3) {
						$tax_status = 'none';
					}
					$metaData['_tax_status'] = $tax_status;
					break;
				case 'tax_class' :
					$tax_class = '';
					if ($data_array[$ekey] == 1) {
						$tax_class = '';
					}
					if ($data_array[$ekey] == 2) {
						$tax_class = 'reduced-rate';
					}
					if ($data_array[$ekey] == 3) {
						$tax_class = 'zero-rate';
					}
					$metaData['_tax_class'] = $tax_class;
					break;
				case 'purchase_note' :
					$metaData['_purchase_note'] = $data_array[$ekey];
					break;
				case 'featured_product' :
					$metaData['_featured'] = $data_array[$ekey];
					break;
				case 'weight' :
					$metaData['_weight'] = $data_array[$ekey];
					break;
				case 'length' :
					$metaData['_length'] = $data_array[$ekey];
					break;
				case 'width' :
					$metaData['_width'] = $data_array[$ekey];
					break;
				case 'height' :
					$metaData['_height'] = $data_array[$ekey];
					break;
				case 'product_attribute_name' :
					$attribute_names[$ekey] = $data_array[$ekey];
					break;
				case 'product_attribute_value' :
					$attribute_values[$ekey] = $data_array[$ekey];
					break;
				case 'product_attribute_visible' :
					$attribute_visible[$ekey] = $data_array[$ekey];
					break;
				case 'product_attribute_variation' :
					$attribute_variation[$ekey] = $data_array[$ekey];
					break;
				case 'product_attribute_position' :
					$attribute_position[$ekey] = $data_array[$ekey];
					break;
				case 'sale_price_dates_from' :
					$metaData['_sale_price_dates_from'] = $data_array[$ekey];
					break;
				case 'sale_price_dates_to' :
					$metaData['_sale_price_dates_to'] = $data_array[$ekey];
					break;
				case 'backorders' :
					$backorders = '';
					if ($data_array[$ekey] == 1) {
						$backorders = 'no';
					}
					if ($data_array[$ekey] == 2) {
						$backorders = 'notify';
					}
					if ($data_array[$ekey] == 3) {
						$backorders = 'yes';
					}
					$metaData['_backorders'] = $backorders;
					break;
				case 'manage_stock' :
					$metaData['_manage_stock'] = $data_array[$ekey];
					break;
				case 'file_paths' :
					$metaData['_file_paths'] = $data_array[$ekey];
					break;
				case 'download_limit' :
					$metaData['_download_limit'] = $data_array[$ekey];
					break;
				case 'download_expiry' :
					$metaData['_download_expiry'] = $data_array[$ekey];
					break;
				case 'download_type' :
					$metaData['_download_type'] = $data_array[$ekey];
					break;
				case 'product_url' :
					$metaData['_product_url'] = $data_array[$ekey];
					break;
				case 'button_text' :
					$metaData['_button_text'] = $data_array[$ekey];
					break;
				case 'product_type' :
					$product_type = 'simple';
					if ($data_array[$ekey] == 1) {
						$product_type = 'simple';
					}
					if ($data_array[$ekey] == 2) {
						$product_type = 'grouped';
					}
					if ($data_array[$ekey] == 3) {
						$product_type = 'external';
					}
					if ($data_array[$ekey] == 4) {
						$product_type = 'variable';
					}
					if ($data_array[$ekey] == 5) {
						$product_type = 'subscription';
					}
					if ($data_array[$ekey] == 6) {
						$product_type = 'variable-subscription';
					}
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Type of Product'] = $product_type;
					wp_set_object_terms($pID, $product_type, 'product_type');
					break;
				case 'product_shipping_class' :
					$metaData['_product_shipping_class'] = $data_array[$ekey];
					break;
				case 'sold_individually' :
					$metaData['_sold_individually'] = $data_array[$ekey];
					break;
				case 'default_attributes' :
					if ($data_array[$ekey]) {
						$dattribute = explode(',',$data_array[$ekey]);
						foreach($dattribute as $dattrkey){
							$def_attribute = explode('|',$dattrkey);
							$def_attribute_lower = wc_sanitize_taxonomy_name($def_attribute[0]);
							$defAttribute[$def_attribute_lower] = $def_attribute[1];
						}
					}
					break;
				case 'custom_attributes' :
					if ($data_array[$ekey]) {
						$cusattribute = explode(',',$data_array[$ekey]);
						foreach($cusattribute as $cusattrkey){
							$cus_attribute = explode('|',$cusattrkey);
							$cus_attribute_lower = wc_sanitize_taxonomy_name($cus_attribute[0]);
							$cusAttribute[$cus_attribute_lower] = $cus_attribute[1];
						}
					}
					break;
				case 'product_tag' :
					$tags[$ekey] = $data_array[$ekey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Tags'] = $data_array[$ekey];
					break;
				case 'product_category' :
					$categories[$ekey] = $data_array[$ekey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Categories'] = $data_array[$ekey];
					break;
				case 'downloadable_files' :
					$downloadable_files = '';
					if ($data_array[$ekey]) {
						$exp_key = array();
						$exploded_file_data = explode('|', $data_array[$ekey]);
						foreach($exploded_file_data as $file_datas){
							$exploded_separate = explode(',', $file_datas);
							$file_name = $uci_admin->convert_string2hash_key($exploded_separate[1]);
							$exp_key[$file_name]['name'] = $exploded_separate[0];
							$exp_key[$file_name]['file'] = $exploded_separate[1];
							$downloadable_files = $exp_key;
						}
					}
					$metaData['_downloadable_files'] = $downloadable_files;
					break;
				case 'crosssell_ids' :
					$crosssellids = '';
					if ($data_array[$ekey]) {
						$exploded_crosssell_ids = explode(',', $data_array[$ekey]);
						$crosssellids = $exploded_crosssell_ids;
					}
					$metaData['_crosssell_ids'] = $crosssellids;
					break;
				case 'upsell_ids' :
					$upcellids = '';
					if ($data_array[$ekey]) {
						$exploded_upsell_ids = explode(',', $data_array[$ekey]);
						$upcellids = $exploded_upsell_ids;
					}
					$metaData['_upsell_ids'] = $upcellids;
					break;
				case 'sku' :
					$metaData['_sku'] = $data_array[$ekey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['SKU'] = $data_array[$ekey];
					break;
				case 'variation_sku' :
					$metaData['_sku'] = $data_array[$ekey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['SKU'] = $data_array[$ekey];
					break;
				case 'thumbnail_id' :
					if (is_numeric($data_array[$ekey])) {
						$metaData['_thumbnail_id'] = $data_array[$ekey];
					}else{
						#TODO thumbnail need to add
					}
					break;
				//WooCommerce Chained Products Fields
				case 'chained_product_detail' :
					$arr = array();
					$cpid_key = '';
					if ($data_array[$ekey]) {
						$chainedid = explode('|', $data_array[$ekey]);
						foreach ($chainedid as $unitid ) {
							$cpid = explode(',', $unitid);
							$id = $cpid[0];
							$query_result = $wpdb->get_results($wpdb->prepare("select post_title from $wpdb->posts where ID = %d",$id));
							$product_name = $query_result[0]->post_title;
							if(isset($product_name) && $product_name != '' ) {
								$cpid_key[$cpid[0]]['unit'] = $cpid[1];
								$cpid_key[$cpid[0]]['product_name'] = $product_name;
							}
							$arr[] = $cpid[0];
						}
						$chained_product_detail = $cpid_key;
					} else {
						$chained_product_detail = '';
					}
					$metaData['_chained_product_detail'] = $chained_product_detail;
					$metaData['_chained_product_ids'] = $arr;
					break;
				case 'chained_product_manage_stock' :
					$metaData['_chained_product_manage_stock'] = $data_array[$ekey];
					break;
				//WooCommerce Product Retailers Fields
				case 'wc_product_retailers_retailer_only_purchase' :
					$metaData['_wc_product_retailers_retailer_only_purchase'] = $data_array[$ekey];
					break;
				case 'wc_product_retailers_use_buttons' :
					$metaData['_wc_product_retailers_use_buttons'] = $data_array[$ekey];
					break;
				case 'wc_product_retailers_product_button_text' :
					$metaData['_wc_product_retailers_product_button_text'] = $data_array[$ekey];
					break;
				case 'wc_product_retailers_catalog_button_text' :
					$metaData['_wc_product_retailers_catalog_button_text'] = $data_array[$ekey];
					break;
				case 'wc_product_retailers_id' :
					$retailer_id[$ekey] = $data_array[$ekey];
					break;
				case 'wc_product_retailers_price' :
					$retailer_price[$ekey] = $data_array[$ekey];
					break;
				case 'wc_product_retailers_url' :
					$retailer_url[$ekey] = $data_array[$ekey];
					break;
				//WooCommerce Product Add-ons Fields
				case 'product_addons_exclude_global' :
					$metaData['_product_addons_exclude_global'] = $data_array[$ekey];
					break;
				case 'product_addons_group_name' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_group_description' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_type' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_position' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_required' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_label_name' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_price' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_minimum' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				case 'product_addons_maximum' :
					$product_addons[$ekey] = $data_array[$ekey];
					break;
				//WooCommerce Warranty Requests Fields
				case 'warranty_label' :
					$metaData['_warranty_label'] = $data_array[$ekey];
					break;
				case 'warranty_type' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				case 'warranty_length' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				case 'warranty_value' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				case 'warranty_duration' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				case 'warranty_addons_amount' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				case 'warranty_addons_value' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				case 'warranty_addons_duration' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				case 'no_warranty_option' :
					$warranty[$ekey] = $data_array[$ekey];
					break;
				//WooCommerce Pre-Orders Fields
				case 'preorders_enabled' :
					$metaData['_wc_pre_orders_enabled'] = $data_array[$ekey];
					break;
				case 'preorders_availability_datetime' :
					if ($data_array[$ekey]) {
						$datetime_value = strtotime($data_array[$ekey]);
					}
					else {
						$datetime_value = '';
					}
					$metaData['_wc_pre_orders_availability_datetime'] = $datetime_value;
					break;
				case 'preorders_fee' :
					$metaData['_wc_pre_orders_fee'] = $data_array[$ekey];
					break;
				case 'preorders_when_to_charge' :
					$metaData['_wc_pre_orders_when_to_charge'] = $data_array[$ekey];
					break;
				//woocommerce_coupons starting
				case 'discount_type' :
					$metaData['discount_type'] = $data_array[$ekey];
					break;
				case 'coupon_amount' :
					$metaData['coupon_amount'] = $data_array[$ekey];
					break;
				case 'individual_use' :
					$metaData['individual_use'] = $data_array[$ekey];
					break;
				case 'exclude_product_ids' :
					$metaData['exclude_product_ids'] = $data_array[$ekey];
					break;
				case 'product_ids' :
					$metaData['product_ids'] = $data_array[$ekey];
					break;
				case 'usage_limit' :
					$metaData['usage_limit'] = $data_array[$ekey];
					break;
				case 'usage_limit_per_user' :
					$metaData['usage_limit_per_user'] = $data_array[$ekey];
					break;
				case 'limit_usage_to_x_items' :
					$metaData['limit_usage_to_x_items'] = $data_array[$ekey];
					break;
				case 'expiry_date' :
					$metaData['expiry_date'] = $data_array[$ekey];
					break;
				case 'free_shipping' :
					$metaData['free_shipping'] = $data_array[$ekey];
					break;
				case 'exclude_sale_items' :
					$metaData['exclude_sale_items'] = $data_array[$ekey];
					break;
				case 'minimum_amount' :
					$metaData['minimum_amount'] = $data_array[$ekey];
					break;
				case 'maximum_amount' :
					$metaData['maximum_amount'] = $data_array[$ekey];
					break;
				case 'customer_email' :
					$customer_email[$ekey] = $data_array[$ekey];
					break;
				case 'exclude_product_categories' :
					$exclude_product[$ekey] = $data_array[$ekey];
					break;
				case 'product_categories' :
					$product_cate[$ekey] = $data_array[$ekey];
					break;
				//woocommerce_orders starting
				case 'payment_method_title' :
					$metaData['_payment_method_title'] = $data_array[$ekey];
					break;
				case 'payment_method' :
					$metaData['_payment_method'] = $data_array[$ekey];
					break;
				case 'transaction_id' :
					$metaData['_transaction_id'] = $data_array[$ekey];
					break;
				case 'billing_first_name' :
					$metaData['_billing_first_name'] = $data_array[$ekey];
					break;
				case 'billing_last_name' :
					$metaData['_billing_last_name'] = $data_array[$ekey];
					break;
				case 'billing_company' :
					$metaData['_billing_company'] = $data_array[$ekey];
					break;
				case 'billing_address_1' :
					$metaData['_billing_address_1'] = $data_array[$ekey];
					break;
				case 'billing_address_2' :
					$metaData['_billing_address_2'] = $data_array[$ekey];
					break;
				case 'billing_city' :
					$metaData['_billing_city'] = $data_array[$ekey];
					break;
				case 'billing_postcode' :
					$metaData['_billing_postcode'] = $data_array[$ekey];
					break;
				case 'billing_state' :
					$metaData['_billing_state'] = $data_array[$ekey];
					break;
				case 'billing_country' :
					$metaData['_billing_country'] = $data_array[$ekey];
					break;
				case 'billing_phone' :
					$metaData['_billing_phone'] = $data_array[$ekey];
					break;
				case 'billing_email' :
					$metaData['_billing_email'] = $data_array[$ekey];
					break;
				case 'shipping_first_name' :
					$metaData['_shipping_first_name'] = $data_array[$ekey];
					break;
				case 'shipping_last_name' :
					$metaData['_shipping_last_name'] = $data_array[$ekey];
					break;
				case 'shipping_company' :
					$metaData['_shipping_company'] = $data_array[$ekey];
					break;
				case 'shipping_address_1' :
					$metaData['_shipping_address_1'] = $data_array[$ekey];
					break;
				case 'shipping_address_2' :
					$metaData['_shipping_address_2'] = $data_array[$ekey];
					break;
				case 'shipping_city' :
					$metaData['_shipping_city'] = $data_array[$ekey];
					break;
				case 'shipping_postcode' :
					$metaData['_shipping_postcode'] = $data_array[$ekey];
					break;
				case 'shipping_state' :
					$metaData['_shipping_state'] = $data_array[$ekey];
					break;
				case 'shipping_country' :
					$metaData['_shipping_country'] = $data_array[$ekey];
					break;
				case 'customer_user' :
					$metaData['_customer_user'] = $data_array[$ekey];
					break;
				case 'order_currency' :
					$metaData['_order_currency'] = $data_array[$ekey];
					break;
				case 'item_name' :
					$orderItem['order_item_name'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_type' :
					$orderItem['order_item_type'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_product_id' :
					$Item_metaDatas['_product_id'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_variation_id' :
					$Item_metaDatas['_variation_id'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_line_subtotal' :
					$Item_metaDatas['_line_subtotal'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_line_subtotal_tax' :
					$Item_metaDatas['_line_subtotal_tax'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_line_total' :
					$Item_metaDatas['_line_total'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_line_tax' :
					$Item_metaDatas['_line_tax'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_line_tax_data' :
					$Item_metaDatas['_line_tax_data'] = explode('|', $data_array[$ekey]);
					break;
				case 'item_tax_class' :
					$Item_metaDatas['_tax_class'] = explode(',', $data_array[$ekey]);
					break;
				case 'item_qty' :
					$Item_metaDatas['_qty'] = explode(',', $data_array[$ekey]);
					break;
				case 'fee_name' :
					$orderFee['order_item_name'] = explode(',', $data_array[$ekey]);
					break;
				case 'fee_type' :
					$orderFee['order_item_type'] = explode(',', $data_array[$ekey]);
					break;
				case 'fee_tax_class' :
					$Fee_metaDatas['_tax_class'] = explode(',', $data_array[$ekey]);
					break;
				case 'fee_line_total' :
					$Fee_metaDatas['_line_total'] = explode(',', $data_array[$ekey]);
					break;
				case 'fee_line_tax' :
					$Fee_metaDatas['_line_tax'] = explode(',', $data_array[$ekey]);
					break;
				case 'fee_line_tax_data' :
					$Fee_metaDatas['_line_tax_data'] = explode('|', $data_array[$ekey]);
					break;
				case 'fee_line_subtotal' :
					$Fee_metaDatas['_line_subtotal'] = explode(',', $data_array[$ekey]);
					break;
				case 'fee_line_subtotal_tax' :
					$Fee_metaDatas['_line_subtotal_tax'] = explode(',', $data_array[$ekey]);
					break;
				case 'shipment_name' :
					$Shipment_name['order_item_name'] = explode(',', $data_array[$ekey]);
					break;
				case 'shipment_method_id' :
					$Shipment_metaDatas['method_id'] = explode(',', $data_array[$ekey]);
					break;
				case 'shipment_cost' :
					$Shipment_metaDatas['cost'] = explode(',', $data_array[$ekey]);
					break;
				case 'shipment_taxes' :
					$Shipment_metaDatas['taxes'] = explode('|', $data_array[$ekey]);
					break;
				//woocommerce_redunds starting
				case 'refund_amount' :
					$metaData['_refund_amount'] = $data_array[$ekey];
					break;
				case 'order_shipping_tax' :
					$metaData['_order_shipping_tax'] = $data_array[$ekey];
					break;
				case 'order_tax' :
					$metaData['_order_tax'] = $data_array[$ekey];
					break;
				case 'order_shipping' :
					$metaData['_order_shipping'] = $data_array[$ekey];
					break;
				case 'cart_discount' :
					$metaData['_cart_discount'] = $data_array[$ekey];
					break;
				case 'cart_discount_tax' :
					$metaData['_cart_discount_tax'] = $data_array[$ekey];
					break;
				case 'order_total' :
					$metaData['_order_total'] = $data_array[$ekey];
					break;
				default:
					$metaData[$ekey] = $data_array[$ekey];
					$metaData['_subscription_payment_sync_date'] = 'a:2:{s:3:"day";i:0;s:5:"month";i:0;}';
					break;
			}
		}

		if(is_array($orderItem)){
			foreach ($orderItem['order_item_name'] as $key => $value) {
				$value_order_item[$key]['order_item_name'] = $orderItem['order_item_name'][$key];
				$value_order_item[$key]['order_item_type'] = $orderItem['order_item_type'][$key];
			}
			foreach ($orderItem['order_item_name'] as $key => $value) {
				foreach ($Item_metaDatas as $key1 => $value1) {
					$value_order_item_meta[$key][$key1] = $Item_metaDatas[$key1][$key];
				}

			}
			foreach ($value_order_item as $key => $value) {
				$oid = wc_add_order_item($pID, $value);
				foreach ($value_order_item_meta[$key] as $itemkey => $itemvalue) {
					wc_add_order_item_meta($oid, $itemkey, $itemvalue);
				}
			}
		}

		if(is_array($orderFee)){
			foreach ($orderFee['order_item_name'] as $key => $value) {
				$value_order_fee[$key]['order_item_name'] = $orderFee['order_item_name'][$key];
				$value_order_fee[$key]['order_item_type'] = $orderFee['order_item_type'][$key];
			}
			foreach ($orderFee['order_item_name'] as $key => $value) {
				foreach ($Fee_metaDatas as $key1 => $value1) {
					$value_order_fee_meta[$key][$key1] = $Fee_metaDatas[$key1][$key];
				}

			}
			foreach ($value_order_fee as $key => $value) {
				$oid = wc_add_order_item($pID, $value);
				foreach ($value_order_fee_meta[$key] as $feekey => $feevalue) {
					wc_add_order_item_meta($oid, $feekey, $feevalue);
				}
			}
		}

		if(is_array($Shipment_name)){
			foreach ($Shipment_name['order_item_name'] as $key => $value) {
				$value_shipment[$key]['order_item_name'] = $Shipment_name['order_item_name'][$key];
				$value_shipment[$key]['order_item_type'] = 'shipping';
			}
			foreach ($Shipment_name['order_item_name'] as $key => $value) {
				foreach ($Shipment_metaDatas as $key1 => $value1) {
					$value_shipment_meta[$key][$key1] = $Shipment_metaDatas[$key1][$key];
				}

			}
			foreach ($value_shipment as $key => $value) {
				$oid = wc_add_order_item($pID, $value);
				foreach ($value_shipment_meta[$key] as $shipkey => $shipvalue) {
					wc_add_order_item_meta($oid, $shipkey, $shipvalue);
				}
			}
		}

		//$metaData['_visibility'] = $visibility;
		if (!empty($customer_email)) {
			$exploded_email = explode(',', $customer_email['customer_email']);
			foreach ($exploded_email as $cus_email) {
				$metaData['customer_email'][] = $cus_email;
			}
		}
		if(!empty($exclude_product)) {
			$exploded_exclude = explode(',', $exclude_product['exclude_product_categories']);
			foreach ($exploded_exclude as $exp_cat) {
				$metaData['exclude_product_categories'][] = $exp_cat;
			}
		}
		if(!empty($product_cate)) {
			$exploded_cate = explode(',', $product_cate['product_categories']);
			foreach ($exploded_cate as $pro_cat) {
				$metaData['product_categories'][] = $pro_cat;
			}
		}
		if (!empty($product_image_gallery)) {
			$exploded_gallery_images = explode('|', $product_image_gallery['product_image_gallery']);
			$image_gallery = '';
			foreach ($exploded_gallery_images as $images) {
				$image_gallery .= $images . ',';
			}
			$Gallery = substr($image_gallery, 0, -1);
			$productImageGallery = $Gallery;
			if ($productImageGallery) {
				$metaData['_product_image_gallery'] = $productImageGallery;
			}
		}
		if (!empty($attribute_names)) {
			$exploded_att_names = explode('|', $attribute_names['product_attribute_name']);
			foreach ($exploded_att_names as $attr_name) {
				$attribute['name'][] = $attr_name;
			}
		}
		if (!empty($attribute_values)) {
			$exploded_att_values = explode(',', $attribute_values['product_attribute_value']);
			foreach ($exploded_att_values as $attr_val) {
				$attribute['value'][] = $attr_val;
			}
		}
		if (!empty($attribute_visible)) {
			$exploded_att_visible = explode('|', $attribute_visible['product_attribute_visible']);
			foreach ($exploded_att_visible as $attr_visible) {
				$attribute['is_visible'][] = $attr_visible;
			}
		}
		if (!empty($attribute_variation)) {
			$exploded_att_variation = explode('|', $attribute_variation['product_attribute_variation']);
			foreach ($exploded_att_variation as $attr_variation) {
				$attribute['is_variation'][] = $attr_variation;
			}
		}
		if (!empty($attribute_position)) {
			$exploded_att_position = explode('|', $attribute_position['product_attribute_position']);
			foreach ($exploded_att_position as $attr_position) {
				$attribute['position'][] = $attr_position;
			}
		}

		//WooCommerce Product Retailers Fields
		if (!empty($retailer_id)) {
			$exploded_ret_id = explode('|', $retailer_id['wc_product_retailers_id']);
			foreach ($exploded_ret_id as $ret_id) {
				$product_retailer['id'][] = $ret_id;
			}
		}
		if (!empty($retailer_price)) {
			$exploded_ret_price = explode('|', $retailer_price['wc_product_retailers_price']);
			foreach ($exploded_ret_price as $ret_price) {
				$product_retailer['product_price'][] = $ret_price;
			}
		}
		if (!empty($retailer_url)) {
			$exploded_ret_url = explode('|', $retailer_url['wc_product_retailers_url']);
			foreach ($exploded_ret_url as $ret_url) {
				$product_retailer['product_url'][] = $ret_url;
			}
		}
		if (!empty($product_retailer)) {
			$retailers_detail = array();
			$count_value = count($product_retailer['id']);
			for ($at = 0; $at < $count_value; $at++) {
				if (isset($product_retailer['id']) && isset($product_retailer['id'][$at])) {
					$retailers_detail[$product_retailer['id'][$at]]['id'] = $product_retailer['id'][$at];
				}
				if (isset($product_retailer['product_price']) && isset($product_retailer['product_price'][$at])) {
					$retailers_detail[$product_retailer['id'][$at]]['product_price'] = $product_retailer['product_price'][$at];
				}
				if (isset($product_retailer['product_url']) && isset($product_retailer['product_url'][$at])) {
					$retailers_detail[$product_retailer['id'][$at]]['product_url'] = $product_retailer['product_url'][$at];
				}
			}
		}
		if (!empty($retailers_detail)) {
			$metaData['_wc_product_retailers'] = $retailers_detail;
		}

		//WooCommerce Product Add-ons
		if (!empty($product_addons)) {
			$exploded_lab_name = explode('|', $product_addons['product_addons_label_name']);
			$count_lab_name = count($exploded_lab_name);
			for ($i = 0; $i < $count_lab_name; $i++) {
				$exploded_label_name = explode(',', $exploded_lab_name[$i]);
				foreach ($exploded_label_name as $lname) {
					$addons_option['label'][$i][] = $lname;
				}
			}
			$explode_lab_price = explode('|', $product_addons['product_addons_price']);
			$count_lab_price = count($explode_lab_price);
			for ($i = 0; $i < $count_lab_price; $i++) {
				$exploded_price = explode(',', $explode_lab_price[$i]);
				foreach ($exploded_price as $lprice) {

					$addons_option['price'][$i][] = $lprice;
				}
			}
			$expl_min = explode('|', $product_addons['product_addons_minimum']);
			$count_min = count($expl_min);
			for ($i = 0; $i < $count_min; $i++) {
				$exploded_min = explode(',', $expl_min[$i]);
				foreach ($exploded_min as $min) {
					$addons_option['min'][$i][] = $min;
				}
			}
			$expl_mac = explode('|',$product_addons['product_addons_maximum']);
			$count_max = count($expl_mac);
			for($i = 0; $i < $count_max; $i++){
				$exploded_max = explode(',', $expl_mac[$i]);
				foreach ($exploded_max as $max) {
					$addons_option['max'][] = $max;
				}
			}
			if(!empty($addons_option)) {
				$options_array = array();
				$cv = count($addons_option['label']);
				for ($a = 0; $a < $cv; $a++) {
					if (isset($addons_option['label']) && isset($addons_option['label'][$a])){
						$options_array[$a]['label'] =$addons_option['label'][$a];
					}
					if (isset($addons_option['price']) && isset($addons_option['price'][$a])){
						$options_array[$a]['price'] =$addons_option['price'][$a];
					}
					if (isset($addons_option['min']) && isset($addons_option['min'][$a])) {
						$options_array[$a]['min'] =$addons_option['min'][$a];
					}
					if (isset($addons_option['max']) && isset($addons_option['max'][$a])) {
						$options_array[$a]['max'] =$addons_option['max'][$a];
					}
				}
			}
			$exploded_group_name = explode('|', $product_addons['product_addons_group_name']);
			foreach ($exploded_group_name as $gname) {
				$addons['name'][] = $gname;
			}
			$exploded_group_description = explode('|', $product_addons['product_addons_group_description']);
			foreach ($exploded_group_description as $gdes) {
				$addons['description'][] = $gdes;
			}
			$exploded_position = explode('|', $product_addons['product_addons_position']);
			foreach ($exploded_position as $pos) {
				$addons['position'][] = $pos;
			}
			$exploded_type = explode('|', $product_addons['product_addons_type']);
			foreach ($exploded_type as $type) {
				$addons['type'][] = $type;
			}
			$exploded_required = explode('|', $product_addons['product_addons_required']);
			foreach ($exploded_required as $req) {
				$addons['required'][] = $req;
			}
			if(!empty($addons)) {
				$addons_array = array();
				$cnt = count($addons['name']);
				for ($b = 0; $b < $cnt; $b++) {
					if (isset($addons['name']) && isset($addons['name'][$b])) {
						$addons_array[$addons['name'][$b]]['name'] = $addons['name'][$b];
					}
					if (isset($addons['description']) && isset($addons['description'][$b])) {
						$addons_array[$addons['name'][$b]]['description'] = $addons['description'][$b];
					}
					if (isset($addons['type']) && isset($addons['type'][$b])) {
						$addons_array[$addons['name'][$b]]['type'] = $addons['type'][$b];
					}
					if (isset($addons['position']) && isset($addons['position'][$b])) {
						$addons_array[$addons['name'][$b]]['position'] = $addons['position'][$b];
					}
					if (isset($addons_option['label']) && isset($addons_option['label'][$b])){
						for ($i = 0; $i < count($addons_option['label'][$b]); $i++) {
							$addons_array[$addons['name'][$b]]['options'][$i]['label'] = $addons_option['label'][$b][$i];
						}
					}
					if (isset($addons_option['price']) && isset($addons_option['price'][$b])){
						for ($i = 0; $i < count($addons_option['price'][$b]); $i++) {
							$addons_array[$addons['name'][$b]]['options'][$i]['price'] = $addons_option['price'][$b][$i];
						}
					}
					if (isset($addons_option['min']) && isset($addons_option['min'][$b])) {
						for ($i = 0; $i < count($addons_option['min'][$b]); $i++) {
							$addons_array[$addons['name'][$b]]['options'][$i]['min'] = $addons_option['min'][$b][$i];
						}
					}
					if (isset($addons_option['max']) && isset($addons_option['max'][$b])) {
						for ($i = 0; $i < count($addons_option['max'][$b]); $i++) {
							$addons_array[$addons['name'][$b]]['options'][$i]['max'] = $addons_option['max'][$b][$i];
						}
					}
					if (isset($addons['required']) && isset($addons['required'][$b])) {
						$addons_array[$addons['name'][$b]]['required'] =$addons['required'][$b];
					}
				}
			}
			if(!empty($addons_array)) {
				$metaData['_product_addons'] = $addons_array;
			}
		}
		//WooCommerce Warranty Requests
		if (!empty($warranty)) {
			if ($warranty['warranty_type'] == 'included_warranty') {
				$warranty_result['type'] = $warranty['warranty_type'];
				$warranty_result['length'] = $warranty['warranty_length'];
				$warranty_result['value'] = $warranty['warranty_value'];
				$warranty_result['duration'] = $warranty['warranty_duration'];
				$metaData['_warranty'] = $warranty_result;
			}else if ( $warranty['warranty_type'] == 'addon_warranty' ) {
				if($warranty['warranty_addons_amount'] != '') {
					$addon_amt = explode('|', $warranty['warranty_addons_amount']);
					foreach ($addon_amt as $amt) {
						$warranty_addons['amount'][] = $amt;
					}
				}
				if($warranty['warranty_addons_value'] != '') {
					$addon_val = explode('|', $warranty['warranty_addons_value']);
					foreach ($addon_val as $val) {
						$warranty_addons['value'][] = $val;
					}
				}
				if($warranty['warranty_addons_duration'] != '') {
					$addon_dur = explode('|', $warranty['warranty_addons_duration']);
					foreach ($addon_dur as $dur) {
						$warranty_addons['duration'][] = $dur;
					}
				}
				if (!empty($warranty_addons)) {
					$warranty_addons_detail = array();
					$addon_count = count($warranty_addons['amount']);
					for ($ad = 0; $ad < $addon_count; $ad++) {
						if (isset($warranty_addons['amount']) && isset($warranty_addons['amount'][$ad])) {
							$warranty_addons_detail[$warranty_addons['amount'][$ad]]['amount'] = $warranty_addons['amount'][$ad];
						}
						if (isset($warranty_addons['value']) && isset($warranty_addons['value'][$ad])) {
							$warranty_addons_detail[$warranty_addons['amount'][$ad]]['value'] = $warranty_addons['value'][$ad];
						}
						if (isset($warranty_addons['duration']) && isset($warranty_addons['duration'][$ad])) {
							$warranty_addons_detail[$warranty_addons['amount'][$ad]]['duration'] = $warranty_addons['duration'][$ad];
						}
					}
				}
				if (!empty($warranty_addons_detail)) {
					$warranty_result['type'] = $warranty['warranty_type'];
					$warranty_result['addons'] = $warranty_addons_detail;
					$warranty_result['no_warranty_option'] = $warranty['no_warranty_option'];
					$metaData['_warranty'] = $warranty_result;
				}
			}else {
				$metaData['_warranty'] = '';
			}
		}
		//attributes
		if (!empty($attribute)) {
			$product_attributes = $exploded_attribute_value = array();
			if(isset($attribute['name'])) {
				$attr_count = count($attribute['name']);}
			if(isset($attr_count)){
				for ($att = 0; $att < $attr_count; $att++) {
					$get_csvpro_settings = get_option('sm_uci_pro_settings');
					$attrlabel = sanitize_title(trim($attribute['name'][$att]));
					$attrlabel = $attribute['name'][$att];
					$attrslug = wc_sanitize_taxonomy_name($attrlabel);
					if (isset($attribute['name']) && isset($attribute['name'][$att])) {
						$product_attributes[$attrlabel]['name'] = $attrlabel;
					}
					if (isset($attribute['value']) && isset($attribute['value'][$att])) {
						$product_attributes[$attrlabel]['value'] = $attribute['value'][$att];
					} else {
						$product_attributes[$attrlabel]['value'] = '';
					}
					if (isset($attribute['position']) && isset($attribute['position'][$att])){
						$product_attributes[$attrlabel]['position'] = $attribute['position'][$att];
					} else {
						$product_attributes[$attrlabel]['position'] = 0;
					}
					if (isset($attribute['is_visible']) && isset($attribute['is_visible'][$att])) {
						$visible=$attribute['is_visible'][$att];
						$product_attributes[$attrlabel]['is_visible'] = intval($visible);
					}
					else
					{
						$product_attributes[$attrlabel]['is_visible'] = '';
					}
				   
					if (isset($attribute['is_variation']) && isset($attribute['is_variation'][$att])) {
					$variation=$attribute['is_variation'][$att];
					
					$product_attributes[$attrlabel]['is_variation'] =intval($variation);
					}
					else
					{
						$product_attributes[$attrlabel]['is_variation'] = '';
					}
					
					$product_attributes[$attrlabel]['is_taxonomy'] = 0;

					// product attributes
					if (!empty($product_attributes)) {
						if($type == 'WooCommerceVariations'){
							$product_detail = $wpdb->get_col($wpdb->prepare("select post_parent from $wpdb->posts where ID = %d", $pID));
							if(!empty($product_detail))
								$productID = $product_detail[0];
							else
								$productID = '';

							if($productID != '') {
								update_post_meta($productID, '_product_attributes', $product_attributes);
							}
						}
						// else{
							$metaData['_product_attributes'] = $product_attributes;
						//}
						//default attribute for variations
						if(!empty($defAttribute)){
							if($type == 'WooCommerceVariations'){
								$product_detail = $wpdb->get_col($wpdb->prepare("select post_parent from $wpdb->posts where ID = %d",$pID));
								if(!empty($product_detail))
									$productID = $product_detail[0];
								else
									$productID = '';
								if($productID != ''){
									update_post_meta($productID,'_default_attributes',$defAttribute);
								}
							}
						}
						//custom attribute for variations
						if(!empty($cusAttribute)){
							foreach($cusAttribute as $cusAttkey => $cusAttval){
								$metaData['attribute_'.$cusAttkey] = $cusAttval;
							}
						}
					}

				}
			} // WooCommerce attribute registration ends here
		}
		//start
					
                                if (isset($metaData['_product_attributes']) && !empty($metaData['_product_attributes'])) {
                                        foreach($metaData['_product_attributes'] as $attrKey => $attrVal) {
                                        	

                                                $get_attributeLabel = $wpdb->get_results( "SELECT attribute_id, attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_label = '{$attrVal['name']}'" );
                                              
                                                
                                                $attrlabel = trim($attrVal['name']);
                                                $attrslug = strtolower($attrlabel);
                                                $attrslug = preg_replace("/[^a-zA-Z0-9._\s]/", "", $attrslug);
                                                $attrslug = preg_replace('/\s/', '-', $attrslug);
                                                $custom_attribute_name = "pa_" . $attrslug;
                                                $attrtype = 'text';
                                                $attrordr = 'menu_order';
                                                $attr_data = $attrVal['value'];
                                                $get_transistent_atrributes = get_option('_transient_wc_attribute_taxonomies');
                                                $get_count_transistent_attr = count($get_transistent_atrributes);
                                                $count_transistent_attr = $get_count_transistent_attr + 1;
                                                if(!empty($get_transistent_atrributes)){
                                                        foreach($get_transistent_atrributes as $tak => $tav) {
                                                        	if (!isset($new_trans_attr_list[$tak])) 
    														$new_trans_attr_list[$tak] = new \stdClass();
                                                                foreach($tav as $attr_reg_key => $attr_reg_val) {
                                                                        $new_trans_attr_list[$tak]->$attr_reg_key = $attr_reg_val;
                                                                }
                                                            }
                                                }
                                            
												if(empty($get_attributeLabel)) {
                                                        $wpdb->insert("{$wpdb->prefix}woocommerce_attribute_taxonomies", array('attribute_name' => $attrslug, 'attribute_label' => $attrlabel, 'attribute_type' => $attrtype, 'attribute_orderby' => $attrordr));
                                                        $attr_taxo_id = $wpdb->insert_id;
                                                        if (!isset($new_trans_attr_list[$count_transistent_attr])) 
    														$new_trans_attr_list[$count_transistent_attr] = new \stdClass();
                                                        $new_trans_attr_list[$count_transistent_attr]->attribute_id = $attr_taxo_id;
                                                        $new_trans_attr_list[$count_transistent_attr]->attribute_name = $attrslug;
                                                        $new_trans_attr_list[$count_transistent_attr]->attribute_label = $attrlabel;
                                                        $new_trans_attr_list[$count_transistent_attr]->attribute_type = $attrtype;
                                                        $new_trans_attr_list[$count_transistent_attr]->attribute_orderby = $attrordr;
                                                        update_option('_transient_wc_attribute_taxonomies', $new_trans_attr_list);
                                                        $split_line = explode('|', $attr_data);
                                                        $reg_attribute_id = wp_set_object_terms($pID, $split_line, $custom_attribute_name);
                                                        $woocomm_term_data = 'order_'.$custom_attribute_name;
                                                   
                                                } else {
                                                        $attrId = $get_attributeLabel[0]->attribute_id;
                                                        $split_line = explode('|', $attr_data);
                                                        $reg_attribute_id = wp_set_object_terms($pID, $split_line, $custom_attribute_name);
                                                }
                                            } //foreach end
                                }
                       
        //end 
		// Insert all meta information
		foreach ($metaData as $meta_key => $meta_value) {
			update_post_meta($pID, $meta_key, $meta_value);
		}
	}

	public function importWooCommerceVariations ($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
		global $wpdb;
		global $uci_admin;
		$productInfo = '';
		$returnArr = array('MODE' => $mode , 'ID' => '');
		$product_id = isset($data_array['PRODUCTID']) ? $data_array['PRODUCTID'] : '';
		$parent_sku = isset($data_array['PARENTSKU']) ? $data_array['PARENTSKU'] : '';
		$variation_id =  isset($data_array['VARIATIONID']) ? $data_array['VARIATIONID'] : '';
		$variation_sku = isset($data_array['VARIATIONSKU']) ? $data_array['VARIATIONSKU'] : '';
		$is_handle_duplicate = $duplicateHandling['is_duplicate_handle'];
		$conditions = $duplicateHandling['conditions'];
		$duplicate_action = $duplicateHandling['action'];
		if($is_handle_duplicate != 'off' && ($duplicate_action == 'Update' || $duplicate_action == 'Skip')):
			$mode = 'Update';
		endif;
		if($product_id != '') {
		//	$variation_condition = 'insert_using_product_id';
			$variation_condition = 'update_using_variation_sku';
			#endif;
		} elseif($parent_sku != '') {
			$get_parent_product_id = $wpdb->get_results( $wpdb->prepare( "select post_id from $wpdb->postmeta where meta_value = %s order by post_id desc", $parent_sku ) );
			$count = count( $get_parent_product_id );
			#$key = $count - 1;
			$key = 0;
			if ( ! empty( $get_parent_product_id ) ) {
				$product_id = $get_parent_product_id[$key]->post_id;
			} else {
				$product_id = '';
			}
			$variation_condition = 'insert_using_product_sku';
		}
		// Get basic information for a product based on product id
		if($product_id != '') {
			$is_exist_product = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where ID = %d", $product_id));
			if(!empty($is_exist_product) && $is_exist_product[0]->ID == $product_id) {
				$productInfo = $is_exist_product[0];
			} else {
				#return $returnArr;
			}
		}

		if($mode != 'Insert' && !empty($conditions)):
			if(in_array('VARIATIONSKU', $conditions) && in_array('VARIATIONID', $conditions)) {
				$variation_condition = 'update_using_variation_id_and_sku';
			} elseif (in_array('VARIATIONID', $conditions)) {
				$variation_condition = 'update_using_variation_id';
			} elseif (in_array('VARIATIONSKU', $conditions)) {
				$variation_condition = 'update_using_variation_sku';
			}
		endif;

		switch ($variation_condition) {
			case 'update_using_variation_id_and_sku':
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Variation found!.";
					return $returnArr;
				}
				$get_variation_data = $wpdb->get_results( $wpdb->prepare( "select DISTINCT pm.post_id from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.ID = %d and p.post_type = %s and pm.meta_value = %s", $variation_id, 'product_variation', $variation_sku ) );

				if ( ! empty( $get_variation_data ) && $get_variation_data[0]->post_id == $variation_id ) {
					$returnArr = $this->importVariationData( $product_id, $variation_id, 'update_using_variation_id_and_sku', $productInfo, $get_variation_data );
				} else {
					$returnArr = $this->importVariationData( $product_id, $variation_id, 'default', $productInfo );
				}
				break;
			case 'update_using_variation_id':
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Variation found!.";
					return $returnArr;
				}
				$get_variation_data = $wpdb->get_results( $wpdb->prepare( "select * from $wpdb->posts where ID = %d and post_type = %s", $variation_id, 'product_variation' ) );
				if ( ! empty( $get_variation_data ) && $get_variation_data[0]->ID == $variation_id ) {
					$returnArr = $this->importVariationData( $product_id, $variation_id, 'update_using_variation_id', $productInfo, $get_variation_data );
				} else {
					$returnArr = $this->importVariationData( $product_id, $variation_id, 'default', $productInfo );
				}
				break;
			case 'update_using_variation_sku':
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Variation found!.";
					return $returnArr;
				}
				$variation_data = $wpdb->get_results($wpdb->prepare("select post_id from $wpdb->postmeta where meta_value = %s order by post_id desc", $variation_sku));
				$variation_id = $variation_data[0]->post_id;
				$get_variation_data = $wpdb->get_results( $wpdb->prepare( "select * from $wpdb->posts where ID = %d and post_type = %s", $variation_id, 'product_variation' ) );
				if ( ! empty( $get_variation_data ) && $get_variation_data[0]->ID == $variation_id) {
					$returnArr = $this->importVariationData( $product_id,$variation_id, 'update_using_variation_sku', $productInfo, $get_variation_data );
				} else {
					$returnArr = $this->importVariationData( $product_id, $variation_id, 'default', $productInfo );
				}
				break;
			case 'insert_using_product_id':
				$returnArr = $this->importVariationData( $product_id, $variation_id, 'insert_using_product_id', $productInfo );
				break;
			case 'insert_using_product_sku':
				$returnArr = $this->importVariationData( $product_id, $variation_id, 'insert_using_product_sku', $productInfo );
				break;
			default:
				$returnArr = $this->importVariationData( $product_id, $variation_id, 'default', $productInfo );
				break;
		}

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
				update_option( 'smack_featured_' . $returnArr['ID'], $featured_image_info );
			}
		}

		return $returnArr;
	}

	public function importVariationData ($product_id, $variation_id, $type, $productInfo, $exist_variation_data = array()) {
		global $wpdb;
		global $uci_admin;
		$event = $uci_admin->getEventInformation();
		$eventKey = $event['event_key'];
		// Create a new variation for the specific product if product exists.
		if($type == 'default' || $type == 'insert_using_product_id' || $type == 'insert_using_product_sku') {
			$get_count_of_variations = $wpdb->get_results( $wpdb->prepare( "select count(*) as variations_count from $wpdb->posts where post_parent = %d and post_type = %s", $product_id, 'product_variation' ) );
			$variations_count = $get_count_of_variations[0]->variations_count;
			$menu_order_count = 0;
			if ($variations_count == 0) {
				$variations_count = '';
				$menu_order= 0 ;
			} else {
				$variations_count = $variations_count + 1;
				$menu_order_count = $variations_count - 1;
				$variations_count = '-' . $variations_count;
			}
			$get_variation_data = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where ID = %d", $product_id));
			foreach($get_variation_data as $key => $val) {
				if($product_id == $val->ID){
					$title = 'Variation #' . $variation_id . ' of ' . $val->post_title;
					$id = $wpdb->insert('wp_posts',
						array('post_title' => $title,
						      'post_type' => 'product_variation',
						),
						array('%s','%s')
					);
					$variation_id = $wpdb->insert_id;
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Variation ID: ' . $variation_id;
					$variation_data['ID'] = $variation_id;
					$variation_data['post_title'] = 'Variation #' . $variation_id . ' of ' . $val->post_title;;
					$variation_data['post_date'] = $val->post_date;
					$variation_data['post_status'] = 'publish';
					$variation_data['comment_status'] = 'closed';
					$variation_data['ping_status'] = 'closed';
					$variation_data['menu_order'] = $menu_order_count;
					$variation_data['post_name'] = 'product-' . $val->ID . '-variation' . $variations_count;
					$variation_data['post_parent'] = $val->ID;
					$variation_data['guid'] =  site_url() . '?product_variation=product-' . $val->ID . '-variation' . $variations_count;
				}
			}
			wp_update_post($variation_data);
			$_SESSION[$eventKey]['summary']['inserted'][] = $variation_id;
			$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
			$returnArr = array( 'ID' => $variation_id, 'MODE' => 'Inserted' );
			return $returnArr;
			//Update the existing variations using variation id and sku
		} elseif ($type == 'update_using_variation_id' || $type == 'update_using_variation_sku' || $type == 'update_using_variation_id_and_sku') {

			foreach($exist_variation_data as $key => $val) {
				if($variation_id == $val->ID){
					$variation_data['ID'] = $val->ID;
					$variation_data['post_title'] = 'Variation #' . $val->ID . ' of ' . $val->post_title;
					$variation_data['post_status'] = 'publish';
					$variation_data['comment_status'] = 'open';
					$variation_data['ping_status'] = 'open';
					$variation_data['post_name'] = $val->post_name;
					$variation_data['post_parent'] = $val->post_parent;
					$variation_data['guid'] = $val->guid;
					$variation_data['post_type'] = 'product_variation';
					$variation_data['menu_order'] = $val->menu_order;
				}}

			wp_update_post($variation_data);
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Variation ID: ' . $variation_id;
			$_SESSION[$eventKey]['summary']['updated'][] = $variation_id;
			$uci_admin->setUpdatedRowCount($uci_admin->getUpdatedRowCount() + 1);
			$returnArr = array( 'ID' => $variation_id, 'MODE' => 'Updated');
			return $returnArr;
		}
	}

	public function importWooCommerceOrders ($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
		$returnArr = array();
		$mode_of_affect = 'Inserted';
		global $wpdb, $uci_admin;
		$is_handle_duplicate = $duplicateHandling['is_duplicate_handle'];
		$conditions = $duplicateHandling['conditions'];
		$duplicate_action = $duplicateHandling['action'];
		if($is_handle_duplicate != 'off' && ($duplicate_action == 'Update' || $duplicate_action == 'Skip')):
			$mode = 'Update';
		endif;
		$update_order_info = false;
		if($mode != 'Insert' && !empty($conditions)):
			if (in_array('ORDERID', $conditions)) {
				$update_order_info = true;
			}
		endif;

		$data_array['post_type'] = 'shop_order';
		$data_array['post_excerpt'] = $data_array['customer_note'];
		if(isset($data_array['order_status'])) {
			$data_array['post_status'] = $data_array['order_status'];}
		/* Assign order date */
		if(!isset( $data_array['order_date'] )) {
			$data_array['post_date'] = current_time('Y-m-d H:i:s');
		} else {
			if(strtotime( $data_array['order_date'] )) {
				$data_array['post_date'] = date( 'Y-m-d H:i:s', strtotime( $data_array['order_date'] ) );
			} else {
				$data_array['post_date'] = current_time('Y-m-d H:i:s');
			}
		}
		if ($mode == 'Insert') {
			$retID = wp_insert_post( $data_array );
			if(is_wp_error($retID)) {
				$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Order. " . $retID->get_error_message();
				return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
				#TODO Exception
			} else {
			}
			$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
			$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Order ID: ' . $retID;
		} else {
			if ( ($mode == 'Update' || $mode == 'Schedule') && $update_order_info == true ) {
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Order found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$update_query = "select ID from $wpdb->posts where (ID = '{$data_array['ORDERID']}') and (post_type = '{$data_array['post_type']}') order by ID DESC";
				$ID_result = $wpdb->get_results($update_query);
				if (is_array($ID_result) && !empty($ID_result)) {
					$retID = $ID_result[0]->ID;
					$data_array['ID'] = $retID;
					wp_update_post($data_array);
					$mode_of_affect = 'Updated';
					$_SESSION[$eventKey]['summary']['updated'][] = $retID;
					$uci_admin->setUpdatedRowCount($uci_admin->getUpdatedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Order ID: ' . $retID;
				} else {
					$retID = wp_insert_post($data_array);
					if(is_wp_error($retID)) {
						$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Order. " . $retID->get_error_message();
						return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
						#TODO Exception
					}
					$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
					$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Order ID: ' . $retID;
				}
			} else {
				if($duplicate_action == 'Skip'){
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Order found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$retID = wp_insert_post($data_array);
				$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
				$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Order ID: ' . $retID;
			}
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		return $returnArr;
	}

	public function importWooCommerceCoupons ($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
		$returnArr = array();
		$mode_of_affect = 'Inserted';
		global $wpdb, $uci_admin;
		$is_handle_duplicate = $duplicateHandling['is_duplicate_handle'];
		$conditions = $duplicateHandling['conditions'];
		$duplicate_action = $duplicateHandling['action'];
		if($is_handle_duplicate != 'off' && ($duplicate_action == 'Update' || $duplicate_action == 'Skip')):
			$mode = 'Update';
		endif;
		$update_coupon_info = false;
		if($mode != 'Insert' && !empty($conditions)):
			if (in_array('COUPONID', $conditions)) {
				$update_coupon_info = true;
			}
		endif;
		$data_array['post_type'] = 'shop_coupon';
		$data_array['post_title'] = $data_array['coupon_code'];
		$data_array['post_name'] = $data_array['coupon_code'];
		if(isset($data_array['description'])) {
			$data_array['post_excerpt'] = $data_array['description'];
		}

		/* Post Status Options */
		if ( !empty($data_array['coupon_status']) ) {
			$data_array = $uci_admin->assign_post_status( $data_array );
		} else {
			$data_array['coupon_status'] = 'publish';
		}

		if ($mode == 'Insert') {
			$retID = wp_insert_post( $data_array );
			if(is_wp_error($retID)) {
				$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Coupon. " . $retID->get_error_message();
				return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
				#TODO Exception
			} else {
			}
			$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
			$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Coupon ID: ' . $retID;
		} else {
			if ( ($mode == 'Update' || $mode == 'Schedule') && $update_coupon_info == true ) {
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Coupon found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$update_query = "select ID from $wpdb->posts where (ID = '{$data_array['COUPONID']}') and (post_type = '{$data_array['post_type']}') order by ID DESC";
				$ID_result = $wpdb->get_results($update_query);
				if (is_array($ID_result) && !empty($ID_result)) {
					$retID = $ID_result[0]->ID;
					$data_array['ID'] = $retID;
					wp_update_post($data_array);
					$mode_of_affect = 'Updated';
					$_SESSION[$eventKey]['summary']['updated'][] = $retID;
					$uci_admin->setUpdatedRowCount($uci_admin->getUpdatedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Coupon ID: ' . $retID;
				} else {
					$retID = wp_insert_post($data_array);
					if(is_wp_error($retID)) {
						$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Coupon. " . $retID->get_error_message();
						return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
						#TODO Exception
					}
					$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
					$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Coupon ID: ' . $retID;
				}
			} else {
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Coupon found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$retID = wp_insert_post($data_array);
				$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
				$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Coupon ID: ' . $retID;
			}
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		return $returnArr;
	}

	public function importWooCommerceRefunds ($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
		$returnArr = array();
		$mode_of_affect = 'Inserted';
		global $wpdb, $uci_admin;
		$is_handle_duplicate = $duplicateHandling['is_duplicate_handle'];
		$conditions = $duplicateHandling['conditions'];
		$duplicate_action = $duplicateHandling['action'];
		if($is_handle_duplicate != 'off' && ($duplicate_action == 'Update' || $duplicate_action == 'Skip')):
			$mode = 'Update';
		endif;
		$update_refund_info = false;
		if($mode != 'Insert' && !empty($conditions)):
			if (in_array('REFUNDID', $conditions)) {
				$update_refund_info = true;
			}
		endif;
		$parent_order_id = 0;
		$post_excerpt = '';
		if(isset($data_array['REFUNDID']))
			$order_id = $data_array['REFUNDID'];
		elseif(isset($data_array['post_parent']))
			$parent_order_id = $data_array['post_parent'];
		if(isset($data_array['post_excerpt']))
			$post_excerpt = $data_array['post_excerpt'];
		$get_order_id = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where ID = %d", $order_id));
		if(!empty($get_order_id)){
			$refund = $get_order_id[0]->ID;
			if(isset($refund)){
				$date_format = date('M-j-Y-Hi-a');
				$data_array['post_type'] = 'shop_order';
				$data_array['post_parent'] = $parent_order_id;
				$data_array['post_status'] = 'wc-refunded';
				$data_array['post_excerpt'] = $post_excerpt;
				$data_array['post_name'] = 'refund-'.$date_format;
				$data_array['guid'] = site_url() . '?shop_order_refund=' . 'refund-'.$date_format;
			}
		}
		if ($mode == 'Insert') {
			$retID = wp_insert_post( $data_array );
			if(is_wp_error($retID)) {
				$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Refund. " . $retID->get_error_message();
				return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
				#TODO Exception
			} else {
			}
			$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
			$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
			$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Refund ID: ' . $retID;
		} else {
			if ( ($mode == 'Update' || $mode == 'Schedule') && $update_refund_info == true ) {
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Refund found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$update_query = "select ID from $wpdb->posts where (ID = '{$data_array['REFUNDID']}') and post_type in ('shop_order', 'shop_order_refund') order by ID DESC";
				$ID_result = $wpdb->get_results($update_query);
				if (is_array($ID_result) && !empty($ID_result)) {
					$retID = $ID_result[0]->ID;
					$data_array['ID'] = $retID;
					wp_update_post($data_array);
					$mode_of_affect = 'Updated';
					$_SESSION[$eventKey]['summary']['updated'][] = $retID;
					$uci_admin->setUpdatedRowCount($uci_admin->getUpdatedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Refund ID: ' . $retID;
				} else {
					$retID = wp_insert_post($data_array);
					if(is_wp_error($retID)) {
						$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Refund found!.";
						return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
						#TODO Exception
					}
					$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
					$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Refund ID: ' . $retID;
				}
			} else {
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Refund found!.";
					$returnArr['MODE'] = $mode;
					return $returnArr;
				}
				$retID = wp_insert_post($data_array);
				$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
				$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Refund ID: ' . $retID;
			}
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		return $returnArr;
	}
}