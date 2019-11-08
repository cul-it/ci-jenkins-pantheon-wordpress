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

class PodsImport {
    private static $pods_instance = null;

    public static function getInstance() {
		
		if (PodsImport::$pods_instance == null) {
			PodsImport::$pods_instance = new PodsImport;
			return PodsImport::$pods_instance;
		}
		return PodsImport::$pods_instance;
    }
    function set_pods_values($header_array ,$value_array , $map, $post_id , $type){	
			$post_values = [];
			$helpers_instance = ImportHelpers::getInstance();	
			$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
			
			$this->pods_import_function($post_values,$type, $post_id , $header_array , $value_array);
    }

    public function pods_import_function($data_array, $importas, $pID, $header_array , $value_array) {
		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();
		$media_instance = MediaHandling::getInstance();

		$list_taxonomy = get_taxonomies();
		
        $podsFields = array();
        $import_type = $helpers_instance->import_post_types($importas, null);
        $post_id = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_name= %s and post_type = %s", $import_type, '_pods_pod'));
        if(!empty($post_id)) {
            $lastId  = $post_id[0]->ID;
            $get_pods_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name FROM {$wpdb->prefix}posts where post_parent = %d AND post_type = %s", $lastId, '_pods_field' ) );
            if ( ! empty( $get_pods_fields ) ) :
                foreach ( $get_pods_fields as $pods_field ) {
                    $get_pods_types = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta where post_id = %d AND meta_key = %s", $pods_field->ID, 'type' ) );
                        $podsFields["PODS"][ $pods_field->post_name ]['label'] = $pods_field->post_name;
                        $podsFields["PODS"][ $pods_field->post_name ]['type']  = $get_pods_types[0]->meta_value;
                }
                endif;
            }

		$createdFields = array();
		foreach ($data_array as $dkey => $dvalue) {
			$createdFields[] = $dkey;
		}
		#TODO File fields
		foreach ($data_array as $custom_key => $custom_value) {
			if($podsFields["PODS"][$custom_key]['type'] == 'file' || $podsFields["PODS"][$custom_key]['type'] == 'avatar'){
				
				$exploded_file_items = explode('|', $custom_value);
				$gallery_ids = array();
				foreach($exploded_file_items as $file) {	
					if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $file,$matched_gallerylist,PREG_PATTERN_ORDER)){
						
						$get_file_id = $media_instance->media_handling($file, $pID,$data_array,'','','',$header_array,$value_array);
						if($get_file_id != '') {
							$gallery_ids[] = $get_file_id;
						}
					} else {
						$galleryLen = strlen($file);
						$checkgalleryid = intval($file);
						$verifiedGalleryLen = strlen($checkgalleryid);
						if($galleryLen == $verifiedGalleryLen) {
							$gallery_ids[] = $file;
						}
					}
				}

				if(in_array($importas, $list_taxonomy)){
					update_term_meta($pID,$custom_key, $gallery_ids);
				}
				elseif($importas == 'Users'){
					update_user_meta($pID, $custom_key, $gallery_ids);
				}
				else{
					update_post_meta($pID, $custom_key, $gallery_ids);
				}	
			}
			
			elseif($podsFields["PODS"][$custom_key]['type'] == 'pick'){
				$exploded_rel_items = explode(',', $custom_value);

				if(in_array($importas, $list_taxonomy)){
					update_term_meta($pID, $custom_key, $exploded_rel_items);
				}
				elseif($importas == 'Users'){
					update_user_meta($pID, $custom_key, $exploded_rel_items);
				}
				else{
					update_post_meta($pID, $custom_key, $exploded_rel_items);
				}	
			}
			
			else{
				if(in_array($importas, $list_taxonomy)){
					update_term_meta($pID, $custom_key, $custom_value);
				}
				elseif($importas == 'Users'){
					update_user_meta($pID, $custom_key, $custom_value);
				}
				else{
					update_post_meta($pID, $custom_key, $custom_value);
				}	
			}
		}
		return $createdFields;
    }
    
}