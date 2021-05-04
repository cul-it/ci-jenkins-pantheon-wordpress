<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

namespace Smackcoders\WCSV;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class MarketPressImport {
    private static $marketpress_instance = null;

    public static function getInstance() {
		
		if (MarketPressImport::$marketpress_instance == null) {
			MarketPressImport::$marketpress_instance = new MarketPressImport;
			return MarketPressImport::$marketpress_instance;
		}
		return MarketPressImport::$marketpress_instance;
    }

    public function marketpress_product_import($data_array, $mode , $check , $hash_key , $line_number) {
		global $wpdb; 
		$core_instance = CoreFieldsImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		global $core_instance;

		$log_table_name = $wpdb->prefix ."import_detail_log";

		$data_array['PRODUCTSKU'] = trim($data_array['PRODUCTSKU']);
		
		$returnArr = array();
		$assigned_author = '';
		$mode_of_affect = 'Inserted';
			
		// Assign post type
		$data_array['post_type'] = 'product';
		$data_array = $core_instance->import_core_fields($data_array);

		$post_type = $data_array['post_type'];

		if($check == 'ID'){
			$ID = $data_array['ID'];	
			$get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE ID = '$ID' AND post_type = '$post_type' AND post_status != 'trash' order by ID DESC ");		
		}
		if($check == 'post_title'){
			$title = $data_array['post_title'];
			$get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND post_type = '$post_type' AND post_status != 'trash' order by ID DESC ");		
		}
		if($check == 'post_name'){
			$name = $data_array['post_name'];
			$get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_name = '$name' AND post_type = '$post_type' AND post_status != 'trash' order by ID DESC ");	
		}
		if($check == 'PRODUCTSKU'){
			$sku = $data_array['PRODUCTSKU'];
			$get_result =  $wpdb->get_results("SELECT DISTINCT p.ID FROM {$wpdb->prefix}posts p join {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id WHERE p.post_type = 'product' AND p.post_status != 'trash' and pm.meta_value = '$sku' ");	
		}

		$updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
		$skipped_count = $updated_row_counts['skipped'];

		if ($mode == 'Insert') {
			if (is_array($get_result) && !empty($get_result)) {
				#skipped
				$core_instance->detailed_log[$line_number]['Message'] = "Duplicate found can not insert this";
				$wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
				$returnArr['MODE'] = $mode_of_affect;
				return $returnArr;
			}else{

				$post_id = wp_insert_post($data_array); 
				set_post_format($post_id , $data_array['post_format']);
				
				if(is_wp_error($post_id) || $post_id == '') {
					# skipped
					$core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Product. " . $post_id->get_error_message();
					$wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
					$returnArr['MODE'] = $mode_of_affect;
					return $returnArr;
				}
				$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Product ID: ' . $post_id . ', ' . $assigned_author;
				$wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");	
			}
		}
		
		if($mode == 'Update'){
			
			if (is_array($get_result) && !empty($get_result)) {
				$post_id = $get_result[0]->ID;
				$data_array['ID'] = $post_id;
				wp_update_post($data_array);
				set_post_format($post_id , $data_array['post_format']);		
			
				$core_instance->detailed_log[$line_number]['Message'] = 'Updated Product ID: ' . $post_id . ', ' . $assigned_author;
				$wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
		
			}else{
				$post_id = wp_insert_post($data_array); 
				set_post_format($post_id , $data_array['post_format']);

				if(is_wp_error($post_id) || $post_id == '') {
					# skipped
					$core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Product. " . $post_id->get_error_message();
					$wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
					$returnArr['MODE'] = $mode_of_affect;
					return $returnArr;
				}
				$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Product ID: ' . $post_id . ', ' . $assigned_author;
				$wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
			}
		}

		$returnArr['ID'] = $post_id;
		$returnArr['MODE'] = $mode_of_affect;
		if (!empty($data_array['post_author'])) {
			$returnArr['AUTHOR'] = isset($assigned_author) ? $assigned_author : '';
		}
		return $returnArr;
    }

    public function marketpress_variation_import($data_array, $mode , $hash_key , $line_number) {
        
		global $wpdb;
		$core_instance = CoreFieldsImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		global $core_instance;

		$log_table_name = $wpdb->prefix ."import_detail_log";
		$mode_of_affect = 'Inserted';
		$variation_data = $update_data = array();
		$product_id = isset($data_array['PRODUCTID']) ? $data_array['PRODUCTID'] : '';
		$variation_id = isset($data_array['VARIATIONID']) ? $data_array['VARIATIONID'] : '';

		$updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
		$skipped_count = $updated_row_counts['skipped'];

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
				$get_count_of_variations = $wpdb->get_results($wpdb->prepare("select count(*) as variations_count from {$wpdb->prefix}posts where post_parent = %d and post_type = %s",$product_id,'mp_product_variation'));
				$variations_count = $get_count_of_variations[0]->variations_count;
				$menuorder_count = 0;
				if ($variations_count == 0) {
					$variations_count = '';
				} else {
					$variations_count = $variations_count + 1;
					$menuorder_count = $variations_count - 1;
					$variations_count = '-' . $variations_count;
				}
				$get_variation_data = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}posts where ID = %d",$product_id));
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
							
						}
					}
				}
			}
			// Initiate the action to insert / update the record
			$retID = wp_insert_post($variation_data); // Insert the core fields for the specific post type.
			
			$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Variation ID: ' . $retID;
			$wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");

			if(is_wp_error($retID)) {

				$core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Variation. " . $retID->get_error_message();
				$wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
				return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
				
			} else {
			}
			
		}
		if($mode == 'Update'){
			$variation_data = get_post($variation_id);
			if(empty($variation_data)){
				return false;
			}
			if($variation_id){
				$get_update_data = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}posts where ID = %s and post_type = %s",$variation_id,'mp_product_variation'),ARRAY_A);
				if($get_update_data) {
					$existing_variation_id = $get_update_data[0]['ID'];
					if ($existing_variation_id == $variation_id) {
						$variation_data = $get_update_data[0];
					} else {
		
					}
				}
			}
			wp_update_post($variation_data);
			$mode_of_affect = 'Updated';

			$core_instance->detailed_log[$line_number]['Message'] = 'Updated Variation ID: ' . $retID;
			$wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
			$retID = $variation_id;
		}
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		
		return $returnArr;
    }
    
    public function marketpress_meta_import_function($data_array, $pID , $type , $line_number) {
		global $wpdb,$core_instance;
		$core_instance = CoreFieldsImport::getInstance();
		
		$metaData = $variation_names = $variation_values = array();
		if(is_plugin_active('wordpress-ecommerce/marketpress.php') && $type == 'MarketPress Product'){
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
						$core_instance->detailed_log[$line_number]['SKU'] = $data_array[$mKey];
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
		} 
	elseif(is_plugin_active('wordpress-ecommerce/marketpress.php') && $type == 'MarketPress Product Variations'){
			foreach ($data_array as $cKey => $cVal) {
				switch ($cKey) {
					case 'product_type':
						$metaData['product_type'] = $data_array[$cKey];
						$metaData['_product_type'] = 'WPMUDEV_Field_Select';
						$core_instance->detailed_log[$line_number]['Type of Product'] = $data_array[$cKey];
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
						$metaData['weight'] = serialize(array('weight_pounds', 'weight_extra_shipping_cost')); 
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
						$metaData['inv'] = serialize(array('inv_inventory', 'inv_out_of_stock_purchase'));
						$metaData['_inv'] = 'WPMUDEV_Field_Complex';
						$metaData['inventory_tracking'] = $data_array[$cKey];
						$metaData['_inventory_tracking'] = 'WPMUDEV_Field_Checkbox';
						break;
					case 'quantity':
						$metaData['inv_inventory'] = $data_array[$cKey];
						$metaData['_inv_inventory'] = 'WPMUDEV_Field_Text';
						$core_instance->detailed_log[$line_number]['Quantity'] = $data_array[$cKey];
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
							
						}
						break;

                    case 'mp_variation_name':   
                        $mp_variation_name = explode('|', $data_array[$cKey]);      
                            foreach($mp_variation_name as $item => $value) {    
                                $variation_names[$item] = trim($value);
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
                    
					$get_attributeLabel = $wpdb->get_results( $wpdb->prepare("SELECT attribute_id, attribute_name FROM {$wpdb->prefix}mp_product_attributes WHERE attribute_name = %s ", $value), ARRAY_A);
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
						wp_set_object_terms( $pID, $mp_variation_value[$key], $attribute_id );
					} else {
						$existings_attid = $wpdb->get_results($wpdb->prepare( "SELECT attribute_id FROM {$wpdb->prefix}mp_product_attributes WHERE attribute_name = %s ",$value),ARRAY_A);
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
			if(isset($metaData['has_variation_content']) && isset($metaData['variation_content_type']) && isset($variation_content_desc)){
				$variation_content = array('post_content' => $variation_content_desc,'ID' => $pID);
				wp_update_post($variation_content);
			}
			foreach ($metaData as $custom_key => $custom_value) {
				update_post_meta($pID, $custom_key, $custom_value);
			}
		}
	}
}