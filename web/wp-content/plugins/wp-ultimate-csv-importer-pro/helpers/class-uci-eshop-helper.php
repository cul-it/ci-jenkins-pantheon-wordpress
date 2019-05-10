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

class SmackUCIeShopHelper extends SmackUCIHelper {

    public function importeShopProducts($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
        $returnArr = array();
        $mode_of_affect = 'Inserted';
        global $wpdb;
        $is_handle_duplicate = $duplicateHandling['is_duplicate_handle'];
        $conditions = $duplicateHandling['conditions'];
        $duplicate_action = $duplicateHandling['action'];
        if($is_handle_duplicate != 'off' && ($duplicate_action == 'Update' || $duplicate_action == 'Skip')):
            $mode = 'Update';
        endif;

        // Assign post type
        $data_array['post_type'] = 'post';

        $update_product_info = false;
        if($mode != 'Insert' && !empty($conditions) || $mode == 'Insert' && !empty($conditions)):
            if (in_array('ID', $conditions)) {
                $whereCondition = " ID = '{$data_array['ID']}'";
                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') order by ID DESC";
                $update_product_info = true;
            } elseif (in_array('post_title', $conditions)) {
                $whereCondition = " post_title = \"{$data_array['post_title']}\"";
                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') order by ID DESC";
                $update_product_info = true;
            } elseif (in_array('PRODUCTSKU', $conditions)) {
                $duplicate_check_query = "select DISTINCT p.ID from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = 'post' and p.post_status != 'trash' and pm.meta_value = '{$data_array['PRODUCTSKU']}'";
                $update_product_info = true;
            }elseif(in_array('post_name', $conditions)){
                 $whereCondition = " post_name = \"{$data_array['post_name']}\"";
		$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') order by ID DESC";
		$update_product_info = true;
	    }
        endif;
	/*if($mode == 'Schedule'){
	    if ($data_array['ID']) {
                $whereCondition = " ID = '{$data_array['ID']}'";
                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') order by ID DESC";
                $update_product_info = true;
            } elseif ($data_array['post_title']) {
                $whereCondition = " post_title = \"{$data_array['post_title']}\"";
                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') order by ID DESC";
                $update_product_info = true;
            } elseif ($data_array['PRODUCTSKU']) {
                $duplicate_check_query = "select DISTINCT p.ID from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = 'post' and p.post_status != 'trash' and pm.meta_value = '{$data_array['PRODUCTSKU']}'";
                $update_product_info = true;
            }elseif($data_array['post_name']){
                 $whereCondition = " post_name = \"{$data_array['post_name']}\"";
                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') order by ID DESC";
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
                $user_records = $this->get_from_user_details( $data_array['post_author'] );
                $data_array['post_author'] = $user_records['user_id'];
                $assigned_author = $user_records['message'];
            }
        }
        /* Post Status Options */
        if ( !empty($data_array['post_date']) ) {
            $data_array = $this->assign_post_status( $data_array );
        }

        // Initiate the action to insert / update the record
        if ($mode == 'Insert') {
            unset($data_array['ID']);
	    $ID_result = $wpdb->get_results($duplicate_check_query);
		if (is_array($ID_result) && !empty($ID_result)) {
                    	$this->skipped = $this->skipped + 1;
			return array('MODE' => $mode, 'ERROR_MSG' => 'Duplicate found can not insert this');
		}else{

            		$retID = wp_insert_post($data_array); // Insert the core fields for the specific post type.
		}
            global $sitepress;
            if(empty($data_array['translated_post_title']) && !empty($data_array['language_code'])){
                $wpdb->update( $wpdb->prefix.'icl_translations', array('language_code' => $data_array['language_code'],'element_id' => $retID),array( 'element_id' => $retID ));
            }else if(!empty($data_array['language_code']) && !empty($data_array['translated_post_title'])){
                $update_query = $wpdb->prepare("select ID,post_type from $wpdb->posts where post_title = %s order by ID DESC",$data_array['translated_post_title']);
                $ID_result = $wpdb->get_results($update_query);
                if(is_array($ID_result) && !empty($ID_result)) {
                    $element_id = $ID_result[0]->ID;
                    $post_type = $ID_result[0]->post_type;
                }else{
                    return false;
                }
                $trid_id = $sitepress->get_element_trid($element_id,'post_'.$post_type);
                $translate_lcode = $sitepress->get_language_for_element($element_id,'post_'.$post_type);
                $wpdb->update( $wpdb->prefix.'icl_translations', array( 'trid' => $trid_id, 'language_code' => $data_array['language_code'], 'source_language_code' => $translate_lcode), array( 'element_id' => $retID));
            }
            if(is_wp_error($retID)) {
                $this->skipped = $this->skipped + 1;
                return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
                #TODO Exception
            } else {
            }
            $_SESSION[$eventKey]['summary']['inserted'][] = $retID;
            $this->inserted = $this->inserted + 1;
        } else {
            if ( ($mode == 'Update' || $mode == 'Schedule') && $update_product_info == true ) {
                if($duplicate_action == 'Skip'){
                    $this->skipped = $this->skipped + 1;
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
                    $this->updated = $this->updated + 1;
                } else {
                    $retID = wp_insert_post($data_array);
                    if(is_wp_error($retID)) {
                        $this->skipped = $this->skipped + 1;
                        return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
                        #TODO Exception
                    }
                    $_SESSION[$eventKey]['summary']['inserted'][] = $retID;
                    $this->inserted = $this->inserted + 1;
                }
            } else {
                if($duplicate_action == 'Skip'){
                    $this->skipped = $this->skipped + 1;
                    $returnArr['MODE'] = $mode;
                    return $returnArr;
                }
                $retID = wp_insert_post($data_array);
                $_SESSION[$eventKey]['summary']['inserted'][] = $retID;
                $this->inserted = $this->inserted + 1;
            }
        }
        $media_handle = array();
        $shortcodes = '';
        $media_handle = isset($duplicateHandling['media_handling']) ? $duplicateHandling['media_handling'] : '';
        /* Set Featured Image */
        if ( !empty($data_array['featured_image']) ) {
            if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $data_array['featured_image'],$matchedlist,PREG_PATTERN_ORDER)){
                $attachid = $this->set_featureimage($data_array['featured_image'],$retID,$media_handle);
                set_post_thumbnail($retID, $attachid);
            }else{
                $shortcodes = $this->capture_shortcodes($data_array['featured_image'],$retID,'Featured',$media_handle);
                if(!empty($shortcodes)){
                    $this->convert_shortcode_to_image($shortcodes,$retID,'Featured',$media_handle,$eventKey);
                }
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

    public function importMetaInformation($eshopmeta, $pID) {
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
