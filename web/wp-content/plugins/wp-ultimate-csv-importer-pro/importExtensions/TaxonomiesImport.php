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

class TaxonomiesImport {
    private static $taxonomies_instance = null;

    public static function getInstance() {
		
		if (TaxonomiesImport::$taxonomies_instance == null) {
			TaxonomiesImport::$taxonomies_instance = new TaxonomiesImport;
			return TaxonomiesImport::$taxonomies_instance;
		}
		return TaxonomiesImport::$taxonomies_instance;
    }

    public function taxonomies_import_function ($data_array, $mode, $importType , $check , $hash_key , $line_number ,$header_array ,$value_array) {

		$returnArr = array();
		$mode_of_affect = 'Inserted';
		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();
		$core_instance = CoreFieldsImport::getInstance();
		$media_instance = MediaHandling::getInstance();
		global $core_instance;

		$log_table_name = $wpdb->prefix ."import_detail_log";
		$events_table = $wpdb->prefix."em_meta" ;

		$updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
		$skipped_count = $updated_row_counts['skipped'];
		
		$terms_table = $wpdb->term_taxonomy;
        //$taxonomy = $importAs;
        $taxonomy = $importType;
		
		$term_children_options = get_option("$taxonomy" . "_children");
		$_name = isset($data_array['name']) ? $data_array['name'] : '';
		$_slug = isset($data_array['slug']) ? $data_array['slug'] : '';
		$_desc = isset($data_array['description']) ? $data_array['description'] : '';
		$_image = isset($data_array['image']) ? $data_array['image'] : '';
		$_parent = isset($data_array['parent']) ? $data_array['parent'] : '';
		$_display_type = isset($data_array['display_type']) ? $data_array['display_type'] : '';
		$_color = isset($data_array['color']) ? $data_array['color'] : '';
		$_top_content = isset($data_array['top_content']) ? $data_array['top_content'] : '';
		$_bottom_content = isset($data_array['bottom_content']) ? $data_array['bottom_content'] : '';

		$get_category_list = array();
		if (strpos($_name, ',') !== false) {
			$get_category_list = explode(',', $_name);
		} elseif (strpos($_name, '>') !== false) {
			$get_category_list = explode('>', $_name);
		} else {
			$get_category_list[] = trim($_name);
		}

		$parent_term_id = 0;
	
		if (count($get_category_list) == 1) {
			$_name = trim($get_category_list[0]);
			if($_parent){
				$get_parent = term_exists("$_parent", "$taxonomy");
				$parent_term_id = $get_parent['term_id'];
			}
		} else {
			$count = count($get_category_list);
			$_name = trim($get_category_list[$count - 1]);
			$checkParent = trim($get_category_list[$count - 2]);
			$parent_term = term_exists("$checkParent", "$taxonomy");
			$parent_term_id = $parent_term['term_id'];
		}
		if($check == 'termid'){
			$termID = $data_array['TERMID'];
		}
		if($check == 'slug'){
			$get_termid = get_term_by( "slug" ,"$_slug" , "$taxonomy");
			$termID = $get_termid->term_id;
		}	
		
		if($mode == 'Insert'){
			if(!empty($termID)){

				$core_instance->detailed_log[$line_number]['Message'] = "Skipped, Due to duplicate Term found!.";
				$wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key  = '$hash_key'");
				return array('MODE' => $mode, 'ERROR_MSG' => 'The term already exists!');

			}else{
				
					$taxoID = wp_insert_term("$_name", "$taxonomy", array('description' => $_desc, 'slug' => $_slug));
					if(is_wp_error($taxoID)){
						$core_instance->detailed_log[$line_number]['Message'] = "Can't insert this " . $taxonomy . ". " . $taxoID->get_error_message();
						$wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key  = '$hash_key'");
					}else{

						$termID= $taxoID['term_id'];
			        	$date = date("Y-m-d H:i:s");

						if(isset($_image)){
							$imageid = $media_instance->media_handling($_image , $termID ,$data_array ,'','','',$header_array ,$value_array);

							if($importType == 'product_cat'){
								add_term_meta($termID , 'thumbnail_id' , $imageid); 
							}
							elseif($importType == 'event-categories' || $importType == 'event-tags'){
								$img_guid = $wpdb->get_results("select guid from {$wpdb->prefix}posts where id = $imageid ");
								foreach($img_guid as $img_value){
									$guid =  $img_value->guid;
								}

								if($importType == 'event-categories'){
									$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'category-image', 'meta_value' => $guid, 'meta_date' => $date) );
									$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'category-image-id', 'meta_value' => $imageid, 'meta_date' => $date) );
								}elseif($importType == 'event-tags'){
									$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'tag-image', 'meta_value' => $guid, 'meta_date' => $date) );
									$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'tag-image-id', 'meta_value' => $imageid, 'meta_date' => $date) );
								}
							}
						}

						if(isset($_display_type)){
							add_term_meta($termID , 'display_type' , $_display_type);
						}

						if(isset($_color)){
							if($importType == 'event-categories'){
								$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'category-bgcolor', 'meta_value' => $_color, 'meta_date' => $date) );
							}elseif($importType == 'event-tags'){
								$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'tag-bgcolor', 'meta_value' => $_color, 'meta_date' => $date) );
							}
						}
						
						if($importType == 'product_cat'){
							if(isset($_top_content) || isset($_bottom_content)){
								$cat_meta = array();
								$cat_meta['cat_header'] = $_top_content;
								$cat_meta['cat_footer'] = $_bottom_content;
								add_term_meta($termID , 'cat_meta' , $cat_meta);
							}
						}

						if(isset($parent_term_id)){
							$update = $wpdb->get_results("UPDATE $terms_table SET `parent` = $parent_term_id WHERE `term_id` = $termID ");
						}	
						$returnArr = array('ID' => $termID, 'MODE' => $mode_of_affect);
						
					//}
						if($importType = 'wpsc_product_category'){
							if($data_array['category_image']){
							$udir = wp_upload_dir();
							$imgurl = $data_array['category_image'];
							$img_name = basename($imgurl);
							$uploadpath = $udir['basedir'] . "/wpsc/category_images/";
							}
							if($data_array['target_market']){
								$custom_market = explode(',', $data_array['target_market']);
									foreach ($custom_market as $key =>$value) {
										$market[$value - 1] = $value;
									}					
							}
							$meta_data = array('uses_billing_address' => $data_array['address_calculate'],'image' => $img_name,'image_width' => $data_array['category_image_width'],'image_height' => $data_array['category_image_height'],'display_type'=>$data_array['catelog_view'],'target_market'=>serialize($market));
								foreach($meta_data as $mk => $mv){
								// $wpdb->insert( $wpdb->prefix.'wpsc_meta', array('object_type' => 'wpsc_category','object_id' => $termID,'meta_key' => $mk,'meta_value' => $mv),array('%s','%d','%s','%s')); 
								}
						}

						global $sitepress;
						if(empty($data_array['translated_post_title']) && !empty($data_array['language_code'])) {
							$wpdb->update( $wpdb->prefix.'icl_#800000translations', array('language_code' => $data_array['language_code'],'element_id' => $termID),array( 'element_id' => $termID ));
						} elseif(!empty($data_array['language_code']) && !empty($data_array['translated_post_title'])) {
							$termdata = get_term_by('name', $data_array['translated_post_title'],$taxonomy,'ARRAY_A');
							if(is_array($termdata) && !empty($termdata)) {
								$element_id = $termdata['term_id'];
								$taxo_type = $termdata['taxonomy'];
							}else{
								return false;
							}
							$trid_id = $sitepress->get_element_trid($element_id,'tax_'.$taxo_type);
							$translate_lcode = $sitepress->get_language_for_element($element_id,'tax_'.$taxo_type);
							$wpdb->update( $wpdb->prefix.'icl_translations', array( 'trid' => $trid_id, 'language_code' => $data_array['language_code'], 'source_language_code' => $translate_lcode), array( 'element_id' => $termID));
						}
						$core_instance->detailed_log[$line_number]['Message'] = 'Inserted ' . $taxonomy . ' ID: ' . $termID;
						$wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
					}
			}
		} else {
			if($mode == 'Update') {
				if(!empty($termID)){
			
					if($importType == 'event-categories' || $importType == 'event-tags'){
						 $wpdb->get_results("UPDATE {$wpdb->prefix}terms SET `name` = '$_name' , `slug` = '$_slug' WHERE `term_id` = '$termID'");
						 $wpdb->get_results("UPDATE $terms_table SET `description` = '$_desc' WHERE `term_id` = '$termID'");
					}
					else{
						wp_update_term($termID, "$taxonomy", array('name' => $_name, 'slug' => $_slug , 'description' => $_desc));
					}
					
					$date = date("Y-m-d H:i:s"); 
				
					//start of added for adding thumbnail
					if(isset($_image)){
							$_img = $media_instance->media_handling($_image , $termID ,$data_array , '','','',$header_array ,$value_array);
						
							if($importType == 'product_cat'){
								update_term_meta($termID , 'thumbnail_id' , $_img); 
							}elseif($importType == 'event-categories' || $importType == 'event-tags'){
							
								$img_guid = $wpdb->get_results("select guid from {$wpdb->prefix}posts where id = $_img ");
								foreach($img_guid as $img_value){
										$guid =  $img_value->guid;
								}
								
								if($importType == 'event-categories'){
									 $wpdb->get_results("UPDATE $events_table SET `meta_value` = '$guid' , `meta_date` = '$date' WHERE `object_id` = '$termID' and `meta_key` = 'category-image'");
									 $wpdb->get_results("UPDATE $events_table SET `meta_value` = $_img  , `meta_date` = '$date' WHERE `object_id` = '$termID' and `meta_key` = 'category-image-id'");
									
								}elseif($importType == 'event-tags'){
									 $wpdb->get_results("UPDATE $events_table SET `meta_value` = '$guid' , `meta_date` = '$date' WHERE `object_id` = '$termID' and `meta_key` = 'tag-image' ");
									 $wpdb->get_results("UPDATE $events_table SET `meta_value` = $_img  , `meta_date` = '$date' WHERE `object_id` = '$termID' and `meta_key` = 'tag-image-id' ");
								}
							}				
					}

					if(isset($_display_type)){
            update_term_meta($termID , 'display_type' , $_display_type);
          }	
					//end of added for adding thumbnail

					if(isset($_color)){
						if($importType == 'event-categories'){
							 $wpdb->get_results("UPDATE $events_table SET `meta_value` = '$_color' , `meta_date` = '$date' WHERE `object_id` = '$termID' and `meta_key` = 'category-bgcolor' ");
						}elseif($importType == 'event-tags'){
							 $wpdb->get_results("UPDATE $events_table SET `meta_value` = '$_color' , `meta_date` = '$date' WHERE `object_id` = '$termID' and `meta_key` = 'tag-bgcolor' ");
						}
					}

					if($importType == 'product_cat'){
						if(isset($_top_content) || isset($_bottom_content)){
							$cat_meta = array();
							$cat_meta['cat_header'] = $_top_content;
							$cat_meta['cat_footer'] = $_bottom_content;
							update_term_meta($termID , 'cat_meta' , $cat_meta);
						}
					}

					if(isset($parent_term_id)){
						$update = $wpdb->get_results("UPDATE $terms_table SET `parent` = $parent_term_id WHERE `term_id` = $termID ");
					}
					
					//start wpsc_product_category meta fields
					if($importType = 'wpsc_product_category'){
						  if($data_array['category_image']){
							$udir = wp_upload_dir();
							$imgurl = $data_array['category_image'];
							$img_name = basename($imgurl);
							$uploadpath = $udir['basedir'] . "/wpsc/category_images/";
						  }
						  if($data_array['target_market']){
							$custom_market = explode(',', $data_array['target_market']);
							foreach ($custom_market as $key =>$value) {
								$market[$value - 1] = $value;
							}
						  }
						  $meta_data = array('uses_billing_address' => $data_array['address_calculate'],'image' => $img_name,'image_width' => $data_array['category_image_width'],'image_height' => $data_array['category_image_height'],'display_type'=>$data_array['catelog_view'],'target_market'=>serialize($market));
						  foreach($meta_data as $mk => $mv){
						// $wpdb->insert( $wpdb->prefix.'wpsc_meta', array('object_type' => 'wpsc_category','object_id' => $termID,'meta_key' => $mk,'meta_value' => $mv),array('%s','%d','%s','%s'));  
						  }
					}
					//end wpsc_product_category meta fields
					$mode_of_affect = 'Updated';		
					$returnArr = array('ID' => $termID, 'MODE' => $mode_of_affect);
					
					$core_instance->detailed_log[$line_number]['Message'] = 'Updated ' . $taxonomy . ' ID: ' . $termID;
					$wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
				
				}else{
					$taxoID = wp_insert_term("$_name", "$taxonomy", array('description' => $_desc, 'slug' => $_slug));
					$termID = $taxoID['term_id'];
					$date = date("Y-m-d H:i:s"); 

					if(isset($_image)){
						$imageid = $media_instance->media_handling($_image , $termID ,$data_array ,'','','',$header_array ,$value_array);
						if($importType == 'product_cat'){
							add_term_meta($termID , 'thumbnail_id' , $imageid); 
						}elseif($importType == 'event-categories' || $importType == 'event-tags'){
							
							$img_guid = $wpdb->get_results("select guid from {$wpdb->prefix}posts where id = $imageid ");
							foreach($img_guid as $img_value){
								$guid =  $img_value->guid;
							}

							if($importType == 'event-categories'){
								$wpdb->insert( $events_table  , array('object_id' =>  $termID, 'meta_key' => 'category-image', 'meta_value' => $guid, 'meta_date' => $date) );
								$wpdb->insert( $events_table  , array('object_id' =>  $termID, 'meta_key' => 'category-image-id', 'meta_value' => $imageid, 'meta_date' => $date) );
							}elseif($importType == 'event-tags'){
								$wpdb->insert( $events_table  , array('object_id' =>  $termID, 'meta_key' => 'tag-image', 'meta_value' => $guid, 'meta_date' => $date) );
								$wpdb->insert( $events_table  , array('object_id' =>  $termID, 'meta_key' => 'tag-image-id', 'meta_value' => $imageid, 'meta_date' => $date) );
							}
						}
						
					}
					if(isset($_display_type)){
						add_term_meta($termID , 'display_type' , $_display_type);
					}

					if(isset($_color)){
						if($importType == 'event-categories'){
							$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'category-bgcolor', 'meta_value' => $_color, 'meta_date' => $date) );
						}elseif($importType == 'event-tags'){
							$wpdb->insert( $events_table , array('object_id' =>  $termID, 'meta_key' => 'tag-bgcolor', 'meta_value' => $_color, 'meta_date' => $date) );
						}
					}

					if($importType == 'product_cat'){
						if(isset($_top_content) || isset($_bottom_content)){
							$cat_meta = array();
							$cat_meta['cat_header'] = $_top_content;
							$cat_meta['cat_footer'] = $_bottom_content;
							add_term_meta($termID , 'cat_meta' , $cat_meta);
						}
					}

					if(isset($parent_term_id)){
						$update = $wpdb->get_results("UPDATE $terms_table SET `parent` = $parent_term_id WHERE `term_id` =$termID ");
					}

					$returnArr = array('ID' => $termID, 'MODE' => $mode_of_affect);

					$core_instance->detailed_log[$line_number]['Message'] = 'Inserted ' . $taxonomy . ' ID: ' . $termID;
					$wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
				}
			} 
		}

		if(!is_wp_error($termID)) {
			update_option("$taxonomy" . "_children", $term_children_options);
			delete_option($taxonomy . "_children");
		}
		return $returnArr;
    }
}
