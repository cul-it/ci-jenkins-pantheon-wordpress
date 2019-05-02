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

class SmackUCIWPCommerceHelper extends SmackUCIHelper {

    public function importWPCommerceProducts($data_array, $importType, $mode, $eventKey, $duplicateHandling) {
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
        $data_array['post_type'] = 'wpsc-product';

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
                $duplicate_check_query = "select DISTINCT p.ID from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = 'wpsc-product' and p.post_status != 'trash' and pm.meta_value = '{$data_array['PRODUCTSKU']}'";
                $update_product_info = true;
            }elseif (in_array('post_name', $conditions)) {
		$whereCondition = " post_name = \"{$data_array['post_name']}\"";
                $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}')  and (post_status != 'trash') order by ID DESC";
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
                $user_records = $this->get_from_user_details( $data_array['post_author'] );
                $data_array['post_author'] = $user_records['user_id'];
                $assigned_author = $user_records['message'];
            }
        }
        /* Post Status Options */
        if ( !empty($data_array['post_date']) ) {
            $data_array = $this->assign_post_status( $data_array );
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
            if(is_wp_error($retID)) {
                $uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
                $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Product. " . $retID->get_error_message();
                return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
                #TODO Exception
            } else {
                //WPML support on post types
                global $sitepress;
                if($sitepress != null) {
                    $this->UCI_WPML_Supported_Posts($data_array, $retID);
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


    public function importWPCommerceCoupons($data_array, $importType, $mode, $eventKey, $duplicateHandling)
    {
        global $wpdb, $uci_admin;
        $returnArr = array();
        $mode_of_affect = 'Inserted';
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
        if($data_array['discount_type'] == "percentage" || $data_array['discount_type'] == "Percentage"){
            $percentage = "1";
        }
        else {
            $percentage = "0";
        }

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
            $wpdb->insert( $wpdb->prefix .'wpsc_coupon_codes' , array('coupon_code' =>$data_array['coupon_code'], 'value' => $data_array['discount'] , 'is-percentage' => $percentage , 'use-once' => $data_array['use_once'], 'is-used' =>'0','active'=>'1','every_product'=>$data_array['apply_on_all_products'],'start'=>$data_array['start'],'expiry'=>$data_array['expiry'],'condition'=>'a:0:{}'),array('%s','%f','%d','%d','%d','%d','%d','%s','%s','%s'));
            $last_id = $wpdb->insert_id;
            if($last_id == "0") {
                $uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
                $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Coupon. ";
                return array('MODE' => $mode, 'ERROR_MSG' => "Can't Insert Record");
                #TODO Exception
            } else {
                $_SESSION[$eventKey]['summary']['inserted'][] = $last_id;
                $uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
                $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Coupon ID: ' . $last_id;
            }
        } else {
            if ( ($mode == 'Update' || $mode == 'Schedule') && $update_coupon_info == true ) {
                if($duplicate_action == 'Skip') {
                    $uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Coupon found!.";
                    $returnArr['MODE'] = $mode;
                    return $returnArr;
                }
                $update_query = $wpdb->prepare("select id from ". $wpdb->prefix ."wpsc_coupon_codes where id = %s", $data_array['COUPONID']);
                
                 $ID_result = $wpdb->get_results($update_query);
                if (is_array($ID_result) && !empty($ID_result)) {
                    $last_id = $ID_result[0]->id;
                    $data_array['ID'] = $last_id;
                    $wpdb->update( $wpdb->prefix."wpsc_coupon_codes" ,  array('coupon_code' =>$data_array['coupon_code'], 'value' => $data_array['discount'] , 'is-percentage' => $percentage , 'use-once' => $data_array['use_once'], 'is-used' =>'0','active'=>'1','every_product'=>$data_array['apply_on_all_products'],'start'=>$data_array['start'],'expiry'=>$data_array['expiry'],'condition'=>'a:0:{}'), array('id' => $data_array['ID']) ,array('%s','%f','%d','%d','%d','%d','%d','%s','%s','%s') );
                   // $last_id = $wpdb->id;
                    $mode_of_affect = 'Updated';
                    $_SESSION[$eventKey]['summary']['updated'][] = $last_id;
                    $uci_admin->setUpdatedRowCount($uci_admin->getUpdatedRowCount() + 1);
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated Coupon ID: ' . $last_id;
                } else {
                    $wpdb->insert( $wpdb->prefix .'wpsc_coupon_codes' , array('coupon_code' =>$data_array['coupon_code'], 'value' => $data_array['discount'] , 'is-percentage' => $percentage , 'use-once' => $data_array['use_once'], 'is-used' =>'0','active'=>'1','every_product'=>$data_array['apply_on_all_products'],'start'=>$data_array['start'],'expiry'=>$data_array['expiry'],'condition'=>'a:0:{}'),array('%s','%f','%d','%d','%d','%d','%d','%s','%s','%s'));
                    $last_id = $wpdb->insert_id;
                    if($last_id == "0") {
                        $uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
                        $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this Coupon. ";
                        return array('MODE' => $mode, 'ERROR_MSG' => "Can't Insert Record");
                        #TODO Exception
                    }
                    $_SESSION[$eventKey]['summary']['inserted'][] = $last_id;
                    $uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Coupon ID: ' . $last_id;
                }
            } else {
                if($duplicate_action == 'Skip') {
                    $uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Coupon found!.";
                    $returnArr['MODE'] = $mode;
                    return $returnArr;
                }
                $wpdb->insert( $wpdb->prefix .'wpsc_coupon_codes' , array('coupon_code' =>$data_array['coupon_code'], 'value' => $data_array['discount'] , 'is-percentage' => $percentage , 'use-once' => $data_array['use_once'], 'is-used' =>'0','active'=>'1','every_product'=>$data_array['apply_on_all_products'],'start'=>$data_array['start'],'expiry'=>$data_array['expiry'],'condition'=>'a:0:{}'),array('%s','%f','%d','%d','%d','%d','%d','%s','%s','%s'));
                $last_id = $wpdb->insert_id;
                $_SESSION[$eventKey]['summary']['inserted'][] = $last_id;
                $uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
                $uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted Coupon ID: ' . $last_id;
            }
        }

        $returnArr['ID'] = $last_id;
        $returnArr['MODE'] = $mode_of_affect;
        if (!empty($data_array['post_author'])) {
            $returnArr['AUTHOR'] = isset($assigned_author) ? $assigned_author : '';
        }
        return $returnArr;
    }


    public function importMetaInformation($wpcommeta, $pID) { 
        global $uci_admin;
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
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['SKU'] = $wpcommeta[$wpkey];
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
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['Stock Qty'] = $wpcommeta[$wpkey];
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
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['Tags'] = $wpcommeta[$wpkey];
                    break;
                case 'product_category' :
                    $categories[$wpkey] = $wpcommeta[$wpkey];
                    $uci_admin->detailed_log[$uci_admin->processing_row_id]['Categories'] = $wpcommeta[$wpkey];
                    break;
                case 'image_gallery' :
                    #TODO: Need to add media handling support.
                    $get_all_gallery_images = explode('|', $wpcommeta[$wpkey]);
                    $gallery_image_ids = array();
                    foreach($get_all_gallery_images as $gallery_image) {
                        if(is_numeric($gallery_image)) {
                            $gallery_image_ids[] = $gallery_image;
                        } else {
                            $attachmentId = $this->set_featureimage($gallery_image, $pID);
                            $gallery_image_ids[] = $attachmentId;
                        }
                    }
                    $metaDatas['_wpsc_product_gallery'] = $gallery_image_ids;
                    break;
            }
            // Code for wp-ecommerce-custom-fields support
            if (!empty($uci_admin->groupMapping['WPECOMMETA'])) {
                $get_wpcf = unserialize(get_option('wpsc_cf_data'));
                if (is_array($get_wpcf)) {
                    foreach ($get_wpcf as $wpcf_key => $wpcf_val) {
                        if ($wpkey == $wpcf_val['slug']) {
                            $name = '_wpsc_' . $wpcf_val['slug'];
                            if ($wpcf_val['type'] == 'radio' || $wpcf_val['type'] == 'checkbox') {
                                $exploded_check_value = explode('|', $wpcommeta[$wpkey]);
                                if (!empty($exploded_check_value)) {
                                    $metaDatas[$name] = $exploded_check_value;
                                } else {
                                    $metaDatas[$name] = array(0 => $wpcommeta[$wpkey]);
                                }
                            } else {
                                $metaDatas[$name] = $wpcommeta[$wpkey];
                            }
                        }
                    }
                }
            }
            // Code ends here
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
