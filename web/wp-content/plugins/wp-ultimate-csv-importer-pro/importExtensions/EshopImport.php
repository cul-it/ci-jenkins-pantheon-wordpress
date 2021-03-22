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

class EshopImport {
    private static $eshop_instance = null;

    public static function getInstance() {
		
		if (EshopImport::$eshop_instance == null) {
			EshopImport::$eshop_instance = new EshopImport;
			return EshopImport::$eshop_instance;
		}
		return EshopImport::$eshop_instance;
    }

    public function eshop_product_import($data_array, $mode , $check , $hash_key , $line_number) {
        global $wpdb,$core_instance;
        $core_instance = CoreFieldsImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
        $log_table_name = $wpdb->prefix ."import_detail_log";
		$data_array['PRODUCTSKU'] = trim($data_array['PRODUCTSKU']);
		$returnArr = array();
		$assigned_author = '';
		$mode_of_affect = 'Inserted';
		$data_array['post_type'] = 'post';
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
            $get_result =  $wpdb->get_results("SELECT DISTINCT p.ID FROM {$wpdb->prefix}posts p join {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id WHERE p.post_type = 'post' AND p.post_status != 'trash' and pm.meta_value = '$sku' ");
            
        }
        
        $updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
		$skipped_count = $updated_row_counts['skipped'];
        
		if ($mode == 'Insert') {
            if (is_array($get_result) && !empty($get_result)) {
                #skipped
                $core_instance->detailed_log[$line_number]['Message'] = 'Duplicate found can not insert this';
                $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
                return array('MODE' => $mode, 'ERROR_MSG' => 'Duplicate found can not insert this');
            }else{

                $post_id = wp_insert_post($data_array); 
                set_post_format($post_id , $data_array['post_format']);
                
                global $sitepress;
                if(empty($data_array['translated_post_title']) && !empty($data_array['language_code'])){
                    $wpdb->update( $wpdb->prefix.'icl_translations', array('language_code' => $data_array['language_code'],'element_id' => $post_id),array( 'element_id' => $post_id ));
                }else if(!empty($data_array['language_code']) && !empty($data_array['translated_post_title'])){
                    $update_query = $wpdb->prepare("select ID,post_type from {$wpdb->prefix}posts where post_title = %s order by ID DESC",$data_array['translated_post_title']);
                    $ID_result = $wpdb->get_results($update_query);
                    if(is_array($ID_result) && !empty($ID_result)) {
                        $element_id = $ID_result[0]->ID;
                        $post_type = $ID_result[0]->post_type;
                    }else{
                        return false;
                    }
                    $trid_id = $sitepress->get_element_trid($element_id,'post_'.$post_type);
                    $translate_lcode = $sitepress->get_language_for_element($element_id,'post_'.$post_type);
                    $wpdb->update( $wpdb->prefix.'icl_translations', array( 'trid' => $trid_id, 'language_code' => $data_array['language_code'], 'source_language_code' => $translate_lcode), array( 'element_id' => $post_id));
                }
                if(is_wp_error($post_id)) {
                    $core_instance->detailed_log[$line_number]['Message'] =  "Can't insert this Product. " . $post_id->get_error_message();
                    $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
                    return array('MODE' => $mode, 'ERROR_MSG' => 'Duplicate found can not insert this');
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
                if(is_wp_error($post_id)) {

                    $core_instance->detailed_log[$line_number]['Message'] =  "Can't insert this Product. " . $post_id->get_error_message();
                    $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
                    return array('MODE' => $mode, 'ERROR_MSG' => 'Duplicate found can not insert this');
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

    public function eshop_meta_import_function($eshopmeta, $pID) {
        foreach ($eshopmeta as $ekey => $eval) {
            switch ($ekey) {
                case 'featured_product' :
                    $isFeatured = strtolower($eshopmeta[$ekey]);
                    $metaDatas['featured'] = $isFeatured;
                    if ($isFeatured == 'yes') {
                        update_post_meta($pID, '_eshop_featured', 'Yes');
                        $metaDatas['featured'] = 'Yes';
                    }
                    break;
                case 'product_in_sale' :
                    $inSale = strtolower($eshopmeta[$ekey]);
                    $metaDatas['sale'] = $inSale;
                    if ($inSale == 'yes') {
                        update_post_meta($pID, '_eshop_sale', 'yes');
                    }
                    break;
                case 'stock_available' :
                    $eval = strtolower($eval);
                    if ($eval == 'yes' || $eval == 1) {
                        update_post_meta($pID, '_eshop_stock', 1);
                    }
                    break;
                case 'cart_option' :
                    $cartOption = strtolower($eshopmeta[$ekey]);
                    if ($cartOption == 'yes' || $cartOption == 'no') {
                        $cartOption = 0;
                    } else {
                        $cartOption = $cartOption;
                    }
                    $metaDatas['cart_radio'] = $cartOption;
                    break;
                case 'description' :
                    $metaDatas['description'] = $eshopmeta[$ekey];
                    break;
                case 'shiprate' :
                    $shipRate = strtoupper($eshopmeta[$ekey]);
                    $metaDatas['shiprate'] = $shipRate;
                    break;
                case 'sku' :
                    $metaDatas['sku'] = $eshopmeta[$ekey];
                    break;
                case 'products_option':
                    $productOptions = $eshopmeta[$ekey];
                    break;
                case 'regular_price':
                    $regularPrice = $eshopmeta[$ekey];
                    break;
                case 'sale_price':
                    $salePrice = $eshopmeta[$ekey];
                    break;

            }
        }
        $get_product_option = '';
        $get_regular_price  = '';
        $get_sale_price = '';
        if (!empty($productOptions)) {
            $get_product_option = explode(',', $productOptions);
        }
        if (!empty($regularPrice)) {
            $get_regular_price = explode(',', $regularPrice);
        }
        if (!empty($salePrice)) {
            $get_sale_price = explode(',', $salePrice);
        }
        $Products[1]['option'] = $get_product_option[0];
        $Products[2]['option'] = $get_product_option[1];
        $Products[3]['option'] = $get_product_option[2];
        $Products[1]['price'] = $get_regular_price[0];
        $Products[2]['price'] = $get_regular_price[1];
        $Products[3]['price'] = $get_regular_price[2];
        $Products[1]['saleprice'] = $get_sale_price[0];
        $Products[2]['saleprice'] = $get_sale_price[1];
        $Products[3]['saleprice'] = $get_sale_price[2];
        $metaDatas['products'] = $Products;
        if (!empty($metaDatas)) {
            update_post_meta($pID, '_eshop_product', $metaDatas);
            foreach ($metaDatas as $custom_key => $custom_value) {
                update_post_meta($pID, $custom_key, $custom_value);
            }
        }

    }
}