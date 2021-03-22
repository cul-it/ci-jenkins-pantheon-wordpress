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

class ACFImageMetaExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (ACFImageMetaExtension::$instance == null) {
			ACFImageMetaExtension::$instance = new ACFImageMetaExtension;
		}
		return ACFImageMetaExtension::$instance;
	}
	
	/**
	* Provides ACF Image Meta mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = [];
        $acf_image_meta_Fields = array(
			        'Caption' => 'acf_caption',
					'Alt text' => 'acf_alt_text',
					'Description' => 'acf_description',
					'File Name' => 'acf_file_name',
					'Title' => 'acf_title',
        );
		$acf_image_meta_value = $this->convert_static_fields_to_array($acf_image_meta_Fields);
		$response['acf_image_meta_fields'] = $acf_image_meta_value ;
		return $response;
		
    }

	/**
	* ACF Image Meta mapping fields extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type){
     
        if(is_plugin_active('advanced-custom-fields/acf.php') || is_plugin_active('advanced-custom-fields-pro/acf.php')){
            global $wpdb;
            $get_acf_fields=$wpdb->get_results("SELECT post_content FROM wp_posts WHERE post_type='acf-field' AND post_status='publish'");
           
            $array=json_decode(json_encode($get_acf_fields),true);
      
            foreach($array as $acf_fields=>$acf_field_values){
                $get_acf_fields=unserialize($acf_field_values['post_content']);
                $field_type =$get_acf_fields['type'];
                if($field_type == 'image' ){
                  //
                     if($import_type == 'nav_menu_item'){
                    return false;
                    }
                
                    $import_type = $this->import_name_as($import_type);
                    if($import_type =='Posts' || $import_type =='Pages' || $import_type =='CustomPosts' || $import_type =='event' || $import_type =='location' || $import_type == 'event-recurring' || $import_type =='Users' || $import_type =='WooCommerce' || $import_type =='WooCommerceCategories' || $import_type =='WooCommerceattribute' || $import_type =='WooCommercetags' || $import_type =='MarketPress' || $import_type =='WPeCommerce' || $import_type =='eShop' || $import_type =='Taxonomies' || $import_type =='Tags' || $import_type =='Categories' || $import_type == 'CustomerReviews') {	
                    return true;
                    }
                    if($import_type == 'ticket'){
                        if(is_plugin_active('events-manager/events-manager.php')){
                           return false;
                        }else{
                           return true;
                        }
                    }
                    else{
                        return false;
                    }
            
                }
                else if($field_type == 'group' || $field_type == 'flexible_content' || $field_type == 'repeater'){
                   
                    global $wpdb;
                    $get_acf_pro_fields=$wpdb->get_results("SELECT post_content FROM wp_posts WHERE post_type='acf-field' AND post_status='publish'");
                   
                    $arrays=json_decode(json_encode($get_acf_pro_fields),true);
                    foreach($arrays as $acf_field=>$acf_field_value){
                        $get_acf_pro_fields=unserialize($acf_field_value['post_content']);
                        $field_types =$get_acf_pro_fields['type'];
                        if($field_types == 'image' ){
                            if($import_type == 'nav_menu_item'){
                           return false;
                           }
                       
                           $import_type = $this->import_name_as($import_type);
                           if($import_type =='Posts' || $import_type =='Pages' || $import_type =='CustomPosts' || $import_type =='event' || $import_type =='location' || $import_type == 'event-recurring' || $import_type =='Users' || $import_type =='WooCommerce' || $import_type =='WooCommerceCategories' || $import_type =='WooCommerceattribute' || $import_type =='WooCommercetags' || $import_type =='MarketPress' || $import_type =='WPeCommerce' || $import_type =='eShop' || $import_type =='Taxonomies' || $import_type =='Tags' || $import_type =='Categories' || $import_type == 'CustomerReviews') {	
                           return true;
                           }
                           if($import_type == 'ticket'){
                               if(is_plugin_active('events-manager/events-manager.php')){
                               return false;
                               }else{
                               return true;
                               }
                           }
                           else{
                             return false;
                           }
                   
                        }
                      
                    }
                }

            }
               
        }
   
    }
    

}