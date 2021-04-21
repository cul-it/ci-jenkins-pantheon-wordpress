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

	public function set_pods_values($header_array ,$value_array , $map, $maps, $post_id , $type, $lang_map = null){	
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();	
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		$postimg = $helpers_instance->get_meta_values($maps , $header_array , $value_array);
		$lang_values = $helpers_instance->get_header_values($lang_map , $header_array , $value_array);
		$this->pods_import_function($post_values, $postimg, $type, $post_id , $header_array , $value_array, $lang_values);
	}

	public function pods_import_function($data_array, $postimg, $importas, $pID, $header_array , $value_array, $wpml_array) {
		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();
		$media_instance = MediaHandling::getInstance();
		$list_taxonomy = get_taxonomies();
		
		$podsFields = array();
		$import_type = $helpers_instance->import_post_types($importas, null);
		if($import_type == 'WooCommerce Product'){
			$import_type = 'product';

		}

		$post_id = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_name= %s and post_type = %s", $import_type, '_pods_pod'));
		

		if(!empty($post_id)) {
			$lastId  = $post_id[0]->ID;
			$get_pods_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name FROM {$wpdb->prefix}posts where post_parent = %d AND post_type = %s", $lastId, '_pods_field' ) );
			if ( ! empty( $get_pods_fields ) ) :
				foreach ( $get_pods_fields as $pods_field ) {
					$get_pods_types = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta where post_id = %d AND meta_key = %s", $pods_field->ID, 'type' ) );
					$get_pods_object = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta where post_id = %d AND meta_key = %s", $pods_field->ID, 'pick_object' ) );
					$podsFields["PODS"][ $pods_field->post_name ]['label'] = $pods_field->post_name;
					$podsFields["PODS"][ $pods_field->post_name ]['type']  = $get_pods_types[0]->meta_value;
					$podsFields["PODS"][ $pods_field->post_name ]['pick_object']=$get_pods_object[0]->meta_value;

				}
endif;
		}

		$createdFields = array();
		foreach ($data_array as $dkey => $dvalue) {
			$createdFields[] = $dkey;
		}

		foreach ($data_array as $custom_key => $custom_value) {
			$plugin = 'pods';
			if($podsFields["PODS"][$custom_key]['type'] == 'file' || $podsFields["PODS"][$custom_key]['type'] == 'avatar'){
				$exploded_file_items = explode('|', $custom_value);
				$gallery_ids = array();
				foreach($exploded_file_items as $file) {	
					$file = trim($file);
				$ext = pathinfo($file, PATHINFO_EXTENSION);
					if($ext){
						$get_file_id = $media_instance->media_handling($file, $pID,$data_array,'','','',$header_array,$value_array,$wpml_array);
						if($get_file_id != '') {
							$gallery_ids[] = $get_file_id;
						    $media_instance->acfimageMetaImports($gallery_ids,$postimg,$plugin);
							
						}
					} else {
						$galleryLen = strlen($file);
						$checkgalleryid = intval($file);
						$verifiedGalleryLen = strlen($checkgalleryid);
						if($galleryLen == $verifiedGalleryLen) {
							$gallery_ids[] = $file;
							$media_instance->acfimageMetaImports($gallery_ids,$postimg,$plugin);
						}
					}
				}
				if(in_array($importas, $list_taxonomy)){
				
					update_term_meta($pID,$custom_key, $gallery_ids);
				}
				elseif($importas == 'Users'){
					update_user_meta($pID, $custom_key, $gallery_ids);
				}
				elseif($importas == 'Comments'){
                    update_comment_meta($pID, $custom_key, $gallery_ids);
                }
				else{
					update_post_meta($pID, $custom_key, $gallery_ids);
				}	

				global $sitepress;
				if($sitepress != null) {
					$wpdb->prepare("UPDATE {$wpdb->prefix}posts SET post_parent = $pID WHERE ID = $gallery_ids[0]");
					$image_id = $gallery_ids[0];
					$meta_value = array('posts'=> array($pID));
					update_post_meta( $image_id, '_wpml_media_usage', $meta_value);               
				}	
				
			}
			elseif($podsFields["PODS"][$custom_key]['type'] == 'pick'){
				$pick_obj=$podsFields["PODS"][$custom_key]['pick_object'];
				$exploded_rel_items = explode(',', $custom_value);
				foreach($exploded_rel_items as $items){
					$items = trim($items);
					$ids = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts where post_title = %s and post_status=%s",$items,'publish') );
					foreach($ids as $id){
						$item[]=$id->ID;
					}
				}
				
				if(in_array($importas, $list_taxonomy)){
					
					update_term_meta($pID, $custom_key, $exploded_rel_items);
				}
				elseif($importas == 'Users'){
					update_user_meta($pID, $custom_key, $exploded_rel_items);
				}
				elseif($importas == 'Comments'){
                    update_comment_meta($pID, $custom_key, $exploded_rel_items);
                }
				else{
					   if($pick_obj=='custom-simple'){
						update_post_meta($pID, $custom_key,$exploded_rel_items);
					   }
					   elseif(!empty($item)){
						foreach($item as $key =>$value){
							update_post_meta($pID, $custom_key,$value);
						}
						
					   }	
				}	
			}

			else{
			
				if(in_array($importas, $list_taxonomy)){
				
					update_term_meta($pID, $custom_key, $custom_value);
				}
				/*elseif(!in_array($importas, $list_taxonomy)){
                    $wpdb->update($wpdb->prefix.'termmeta' , array('meta_value' => $custom_value ) , array('meta_key' => $custom_key , 'term_id' => $pID ));
				}*/
				elseif($importas == 'Users'){
					update_user_meta($pID, $custom_key, $custom_value);
				}
				elseif($importas == 'Comments'){
                    update_comment_meta($pID, $custom_key, $custom_value);
                }
				else{
				
					//$wpdb->insert($wpdb->prefix.'termmeta' , array('meta_value' => $custom_value ,'meta_key' => $custom_key , 'term_id' => $pID ));
					update_post_meta($pID, $custom_key, $custom_value);
				}	
			}
		}
	
		return $createdFields;
	}

}
