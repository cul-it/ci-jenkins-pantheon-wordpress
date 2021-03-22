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

class WPeCommerceImport {
    private static $wpecommerce_instance = null;

    public static function getInstance() {		
		if (WPeCommerceImport::$wpecommerce_instance == null) {
			WPeCommerceImport::$wpecommerce_instance = new WPeCommerceImport;
			return WPeCommerceImport::$wpecommerce_instance;
		}
		return WPeCommerceImport::$wpecommerce_instance;
    }

    public function wpecommerce_product_import($data_array , $mode , $check , $hash_key , $line_number){
        global $wpdb;
        $core_instance = CoreFieldsImport::getInstance();
        $helpers_instance = ImportHelpers::getInstance();
        global $core_instance;
        $log_table_name = $wpdb->prefix ."import_detail_log";
		$data_array['PRODUCTSKU'] = trim($data_array['PRODUCTSKU']);	
		$returnArr = array();
		$assigned_author = '';
		$mode_of_affect = 'Inserted'; 
		$data_array['post_type'] = 'wpsc-product';
		$data_array = $core_instance->import_core_fields($data_array);
        $post_type = $data_array['post_type'];
        $updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
        $skipped_count = $updated_row_counts['skipped'];   
        if($check == 'ID'){
            $ID = $data_array['ID'];	
            $get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE ID = $ID AND post_type = '$post_type' AND post_status != 'trash' order by ID DESC ");	
        
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
            $get_result =  $wpdb->get_results("SELECT DISTINCT p.ID FROM {$wpdb->prefix}posts p join {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id WHERE p.post_type = 'wpsc-product' AND p.post_status != 'trash' and pm.meta_value = '$sku' ");
           
        }

		if ($mode == 'Insert') {
            if (is_array($get_result) && !empty($get_result)) {
                #skipped
                $core_instance->detailed_log[$line_number]['Message'] = "Skipped, Due to duplicate Product found!.";
                $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
            
                $returnArr['MODE'] = $mode;
                return $returnArr;
            
            }else{
                unset($data_array['ID']);
                $post_id = wp_insert_post($data_array); 	
                set_post_format($post_id , $data_array['post_format']);

				if(is_wp_error($post_id) || $post_id == '') {
                    # skipped    
                    $core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Product. " . $post_id->get_error_message();
					$wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
                
                    $returnArr['MODE'] = $mode;
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
                
                    $returnArr['MODE'] = $mode;
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
    
    public function wpecommerce_coupons_import($data_array, $mode , $hash_key , $line_number){

        global $wpdb;
        $returnArr = array();
        $core_instance = CoreFieldsImport::getInstance();
        $helpers_instance = ImportHelpers::getInstance();
        global $core_instance;

        $log_table_name = $wpdb->prefix ."import_detail_log";

        $updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
        $skipped_count = $updated_row_counts['skipped'];
        
        if($data_array['discount_type'] == "percentage" || $data_array['discount_type'] == "Percentage"){
            $percentage = "1";
        }
        if($data_array['discount_type'] == "Free shipping" || $data_array['discount_type'] == "Free Shipping"){
            $percentage = "2";
        }
        else {
            $percentage = "0";
        }

        if(isset($data_array['conditions'])){
			$explode_conditions = explode(',' , $data_array['conditions'] ); 
			
			$condition_arr = array();
        	
            foreach($explode_conditions as $explode_value){	
				$condition = array();	
				$exp_value = explode('|', $explode_value);

                    if(isset($exp_value[0])){
                        switch($exp_value[0]){
                            case 'Item name':
								$condition['property'] = 'item_name';
                                break;
                            case 'Item quantity':
								$condition['property'] = 'item_quantity';
                                break;
                            case 'Total quantity':
								$condition['property'] = 'total_quantity';
                                break;
                            case 'Subtotal amount':
								$condition['property'] = 'subtotal_amount';
								break;
							default:
								break;
                        }
                    }
                    
                    if(isset($exp_value[1])){
                        switch($exp_value[1]){
                            case 'Is equal to':
								$condition['logic'] = 'equal';
                                break;
                            case 'Is greater than':
								$condition['logic'] = 'greater';
                                break;
                            case 'Is less than':
								$condition['logic'] = 'less';
                                break;
                            case 'Contains':
								$condition['logic'] = 'contains';
                                break;
                            case 'Does not contain':
								$condition['logic'] = 'not_contain';
                                break;
                            case 'Begins with':
								$condition['logic'] = 'begins';
                                break;
                            case 'Ends with':
								$condition['logic'] = 'ends';
                                break;
                            case 'In Category':
								$condition['logic'] = 'category';
								break;
							default:
								break;
                        }
                    }
                    
                    if(isset($exp_value[2])){	
						$condition['value'] = $exp_value[2];
					}
        
                    if(isset($exp_value[3])){	
                        switch($exp_value[3]){		
                            case 'AND':
								$condition['operator'] = 'and';
                                break;
                            case 'OR':
								$condition['operator'] = 'or';
								break;
							default:
								break;
                        }
					}
					array_push($condition_arr , $condition);
			}	  
        }
		$condition_value = serialize($condition_arr);
	
        if(!isset( $data_array['start'] )) {
            $data_array['start'] = current_time('Y-m-d H:i:s');
        } else {
            if(strtotime( $data_array['start'] )) {
                $data_array['start'] = date( 'Y-m-d H:i:s', strtotime( $data_array['start'] ) );
            } else {
                $data_array['start'] = current_time('Y-m-d H:i:s');
            }
        }

        if(!isset( $data_array['expiry'] )) {
            $data_array['expiry'] = current_time('Y-m-d H:i:s');
        } else {
            if(strtotime( $data_array['expiry'] )) {
                $data_array['expiry'] = date( 'Y-m-d H:i:s', strtotime( $data_array['expiry'] ) );
            } else {
                $data_array['expiry'] = current_time('Y-m-d H:i:s');
            }
        }

        if ($mode == 'Insert') {
            $currentId = $wpdb->get_var($wpdb->prepare("select id from ". $wpdb->prefix ."wpsc_coupon_codes order by id desc limit %d", 1));
            $id = $currentId + 1;
            $wpdb->insert( $wpdb->prefix .'wpsc_coupon_codes' , array('coupon_code' =>$data_array['coupon_code'], 'value' => $data_array['discount'] , 'is-percentage' => $percentage , 'use-once' => $data_array['use_once'], 'is-used' =>'0','active'=>'1','every_product'=>$data_array['apply_on_all_products'],'start'=>$data_array['start'],'expiry'=>$data_array['expiry'],'condition'=>$condition_value),array('%s','%f','%d','%d','%d','%d','%d','%s','%s','%s')); 
            $last_id = $wpdb->insert_id;
            $mode_of_affect = 'Inserted';
            if($last_id == "0") {
                $core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Coupon.";
                $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
                return array('MODE' => $mode, 'ERROR_MSG' => "Can't Insert Record");
            } else {
                $core_instance->detailed_log[$line_number]['Message'] = 'Inserted Coupon ID: ' . $last_id;
                $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'"); 
            }
        } else {
            if ($mode == 'Update') {
                
                $update_query = $wpdb->prepare("select id from ". $wpdb->prefix ."wpsc_coupon_codes where id = %s", $data_array['COUPONID']);    
                $ID_result = $wpdb->get_results($update_query);
                if (is_array($ID_result) && !empty($ID_result)) {
                    $last_id = $ID_result[0]->id;
                    $data_array['ID'] = $last_id;

                    $wpdb->update( $wpdb->prefix."wpsc_coupon_codes" ,  array('coupon_code' =>$data_array['coupon_code'], 'value' => $data_array['discount'] , 'is-percentage' => $percentage , 'use-once' => $data_array['use_once'], 'is-used' =>'0','active'=>'1','every_product'=>$data_array['apply_on_all_products'],'start'=>$data_array['start'],'expiry'=>$data_array['expiry'],'condition'=>$condition_value), array('id' => $data_array['ID']) ,array('%s','%f','%d','%d','%d','%d','%d','%s','%s','%s') );
                    
                    $mode_of_affect = 'Updated';
                    $core_instance->detailed_log[$line_number]['Message'] = 'Updated Coupon ID: ' . $last_id;
                    $wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
                
                }else{
                    $currentId = $wpdb->get_var($wpdb->prepare("select id from ". $wpdb->prefix ."wpsc_coupon_codes order by id desc limit %d", 1));
                    $id = $currentId + 1;

                    $wpdb->insert( $wpdb->prefix .'wpsc_coupon_codes' , array('coupon_code' =>$data_array['coupon_code'], 'value' => $data_array['discount'] , 'is-percentage' => $percentage , 'use-once' => $data_array['use_once'], 'is-used' =>'0','active'=>'1','every_product'=>$data_array['apply_on_all_products'],'start'=>$data_array['start'],'expiry'=>$data_array['expiry'],'condition'=>$condition_value),array('%s','%f','%d','%d','%d','%d','%d','%s','%s','%s'));

                    $last_id = $wpdb->insert_id;
                    $mode_of_affect = 'Inserted';
                    if($last_id == "0") {
                        $core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Coupon.";
                        $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
                        return array('MODE' => $mode, 'ERROR_MSG' => "Can't Insert Record");
                    } else {
                        $core_instance->detailed_log[$line_number]['Message'] = 'Inserted Coupon ID: ' . $last_id;
                        $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'"); 
                    }
                }
            } 
        }

        $returnArr['ID'] = $last_id;
        $returnArr['MODE'] = $mode_of_affect;
        
        return $returnArr;
    }

    public function wpecommerce_meta_import_function($wpcommeta, $pID , $line_number, $header_array ,$value_array) {
        
        global $wpdb,$core_instance;
		$core_instance = CoreFieldsImport::getInstance();
		
        foreach ($wpcommeta as $wpkey => $wpval) {
            switch ($wpkey) {
                case 'stock' :
                    $metaDatas['_wpsc_stock'] = $wpcommeta[$wpkey];
                    break;
                case 'price' :
                    $metaDatas['_wpsc_price'] = $wpcommeta[$wpkey];
                    break;
                case 'sale_price' :
                    $metaDatas['_wpsc_special_price'] = $wpcommeta[$wpkey];
                    break;
                case 'sku' :
                    $metaDatas['_wpsc_sku'] = $wpcommeta[$wpkey];
                    $core_instance->detailed_log[$line_number][' SKU'] = $wpcommeta[$wpkey];
                    break;
                case 'notify_when_none_left':
                    $wpsc_product_metadata['notify_when_none_left'] = $wpcommeta[$wpkey];
                    break;
                case 'unpublish_when_none_left':
                    $wpsc_product_metadata['unpublish_when_none_left'] = $wpcommeta[$wpkey];
                    break;
                case 'taxable_amount':
                    $wpsc_product_metadata['wpec_taxes_taxable_amount'] = $wpcommeta[$wpkey];
                    break;
                case 'is_taxable':
                    $wpsc_product_metadata['wpec_taxes_taxable'] = $wpcommeta[$wpkey];
                    break;
                case 'external_link':
                    $wpsc_product_metadata['external_link'] = $wpcommeta[$wpkey];
                    break;
                case 'external_link_text':
                    $wpsc_product_metadata['external_link_text'] = $wpcommeta[$wpkey];
                    break;
                case 'external_link_target':
                    $wpsc_product_metadata['external_link_target'] = $wpcommeta[$wpkey];
                    break;
                case 'no_shipping':
                    $wpsc_product_metadata['no_shipping'] = $wpcommeta[$wpkey];
                    break;
                case 'weight':
                    $wpsc_product_metadata['weight'] = $wpcommeta[$wpkey];
                    break;
                case 'weight_unit':
                    $wpsc_product_metadata['weight_unit'] = $wpcommeta[$wpkey];
                    break;
                case 'shipping':
                    $explodedvalue = explode('|', $wpcommeta[$wpkey]);
                    $wpsc_product_metadata['shipping']['local'] = $explodedvalue[0];
                    $wpsc_product_metadata['shipping']['international'] = $explodedvalue[1];
                    break;
                case 'local_shipping':
                    $wpsc_product_metadata['shipping']['local'] = $wpcommeta[$wpkey];
                    break;
                case 'international_shipping':
                    $wpsc_product_metadata['shipping']['international'] = $wpcommeta[$wpkey];
                    break;
                case 'merchant_notes':
                    $wpsc_product_metadata['merchant_notes'] = $wpcommeta[$wpkey];
                    break;
                case 'engraved':
                    $wpsc_product_metadata['engraved'] = $wpcommeta[$wpkey];
                    break;
                case 'can_have_uploaded_image':
                    $wpsc_product_metadata['can_have_uploaded_image'] = $wpcommeta[$wpkey];
                    break;
                case 'enable_comments':
                    $wpsc_product_metadata['enable_comments'] = $wpcommeta[$wpkey];
                    break;
                case 'quantity_limited':
                    $wpsc_product_metadata['quantity_limited'] = $wpcommeta[$wpkey];
                    break;
                case 'special':
                    $wpsc_product_metadata['special'] = $wpcommeta[$wpkey];
                    break;
                case 'display_weight_as':
                    $wpsc_product_metadata['display_weight_as'] = $wpcommeta[$wpkey];
                    break;
                case 'google_prohibited':
                    $wpsc_product_metadata['google_prohibited'] = $wpcommeta[$wpkey];
                    break;
                case 'state':
                    $wpsc_product_metadata['table_rate_price']['state'] = $wpcommeta[$wpkey];
                    break;
                case 'quantity':
                    $wpsc_product_metadata['table_rate_price']['quantity'] = explode(',', $wpcommeta[$wpkey]);
                    $core_instance->detailed_log[$line_number][' Stock Qty'] = $wpcommeta[$wpkey];
                    break;
                case 'table_price':
                    $wpsc_product_metadata['table_rate_price']['table_price'] = explode(',',$wpcommeta[$wpkey]);
                    break;
                case 'height':
                    $wpsc_product_metadata['dimensions']['height'] = $wpcommeta[$wpkey];
                    break;
                case 'height_unit':
                    $wpsc_product_metadata['dimensions']['height_unit'] = $wpcommeta[$wpkey];
                    break;
                case 'width':
                    $wpsc_product_metadata['dimensions']['width'] = $wpcommeta[$wpkey];
                    break;
                case 'width_unit':
                    $wpsc_product_metadata['dimensions']['width_unit'] = $wpcommeta[$wpkey];
                    break;
                case 'length':
                    $wpsc_product_metadata['dimensions']['length'] = $wpcommeta[$wpkey];
                    break;
                case 'length_unit':
                    $wpsc_product_metadata['dimensions']['length_unit'] = $wpcommeta[$wpkey];
                    break;
                case 'dimension_unit':
                    $wpsc_product_metadata['dimension_unit'] = $wpcommeta[$wpkey];
                    break;
                case 'alternative_currencies_and_price':
                    if(!empty($wpcommeta[$wpkey])) {
                        $currency_and_price = explode(',', $wpcommeta[$wpkey]);
                        foreach ($currency_and_price as $value) {
                            $wpsccurrency = explode('|', $value);
                            $wpsc_currency[$wpsccurrency[0]] = $wpsccurrency[1];
                        }
                    }
                    break;
                case 'custom_meta':
                    if(!empty($wpcommeta[$wpkey])) {
                        $custom_meta = explode(',', $wpcommeta[$wpkey]);
                        foreach ($custom_meta as $value) {
                            $custom_value = explode('|', $value);
                            if(!empty($custom_value[0]) && !empty($custom_value[1])){
                                $metaDatas[$custom_value[0]] = $custom_value[1];
                            }
                        }
                    }
                    break;
                case 'meta_data':
                    if(!empty($wpcommeta[$wpkey])) {
                        $custom_meta = explode(',', $wpcommeta[$wpkey]);
                        foreach ($custom_meta as $value) {
                            $custom_value = explode('|', $value);
                            if(!empty($custom_value[0]) && !empty($custom_value[1])){
                                $metaDatas[$custom_value[0]] = $custom_value[1];
                            }
                        }
                    }
                    break;
                case 'product_tags' :
                    $tags[$wpkey] = $wpcommeta[$wpkey];
                    $core_instance->detailed_log[$line_number][' Tags'] = $wpcommeta[$wpkey];
                    break;
                case 'product_category' :
                    $categories[$wpkey] = $wpcommeta[$wpkey];
                    $core_instance->detailed_log[$line_number][' Categories'] = $wpcommeta[$wpkey];
                    break;
                case 'image_gallery' :
                    $media_instance = MediaHandling::getInstance();
                    $get_all_gallery_images = explode('|', $wpcommeta[$wpkey]);
                    $gallery_image_ids = array();
                    foreach($get_all_gallery_images as $gallery_image) {
                        if(is_numeric($gallery_image)) {
                            $gallery_image_ids[] = $gallery_image;
                        } else {
                            $attachmentId = $media_instance->media_handling($gallery_image , $pID ,'','','','',$header_array ,$value_array);
                            $gallery_image_ids[] = $attachmentId;
                        }
                    }
                    $metaDatas['_wpsc_product_gallery'] = $gallery_image_ids;
                    break;
            }
            
        }
        if(!empty($wpsc_currency)){
            $metaDatas['_wpsc_currency'] = $wpsc_currency;
        }
        if (!empty($wpsc_product_metadata)) {
            $metaDatas['_wpsc_product_metadata'] = $wpsc_product_metadata;
        }
        if (!empty ($metaDatas)) {
            foreach ($metaDatas as $custom_key => $custom_value) {
                update_post_meta($pID, $custom_key, $custom_value);
            }
        }
    }

}