<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackUCITaxonomyHelper {

	/**
	 * Import bulk terms & taxonomies
	 *
	 * @param $data_array   - Data Array
	 * @param $mode         - Mode
	 * @param $importType   - Import Type
	 * @param $importAs     - Import As
	 * @param $eventKey     - Event Key
	 * @param $duplicateHandling    - Duplicate handling configuration
	 *
	 * @return array
	 */
	public function importBulkTermsAndTaxonomies ($data_array, $mode, $importType, $importAs, $eventKey, $duplicateHandling) {
		$returnArr = array();
		$mode_of_affect = 'Inserted';
		global $wpdb, $uci_admin;
		$conditions = $duplicateHandling['conditions'];
		$duplicate_action = $duplicateHandling['action'];
		if($duplicate_action == 'Update' || $duplicate_action == 'Skip'):
			$mode = 'Update';
		endif;
		$update_category_info = false;
		if($mode != 'Insert' && !empty($conditions)):
			if (in_array('name', $conditions)) {
				$update_category_info = true;
			}
		endif;
		$terms_table = $wpdb->term_taxonomy;
		$taxonomy = $importAs;
		$term_children_options = get_option("$taxonomy" . "_children");
		$_name = isset($data_array['name']) ? $data_array['name'] : '';
		$_slug = isset($data_array['slug']) ? $data_array['slug'] : '';
		$_desc = isset($data_array['description']) ? $data_array['description'] : '';
		$_image = isset($data_array['image']) ? $data_array['image'] : '';
		$_display_type = isset($data_array['display_type']) ? $data_array['display_type'] : '';
		$_wpseo_title = ''; $_wpseo_desc = ''; $_wpseo_canonical = ''; $_wpseo_noindex = ''; $_wpseo_sitemap_include = '';
		if(isset($data_array['wpseo_title']))
		{
			$_wpseo_title= $data_array['wpseo_title'];
		}
		if(isset($data_array['wpseo_desc']))
		{
			$_wpseo_desc = $data_array['wpseo_desc'];
		}
		if(isset($data_array['wpseo_canonical']))
		{
			$_wpseo_canonical = $data_array['wpseo_canonical'];
		}
		if(isset($data_array['wpseo_noindex']))
		{
			$_wpseo_noindex = $data_array['wpseo_noindex'];
		}
		if(isset($data_array['wpseo_sitemap_include']))
		{
			$_wpseo_sitemap_include = $data_array['wpseo_sitemap_include'];
		}
		$wpcsvsettings = get_option('wpcsvprosettings');
		$getTerms = $wpdb->get_results($wpdb->prepare("select count(taxonomy) as count from $terms_table where taxonomy = '%s'", $taxonomy));
		$get_category_list = array();
		#$get_category_list = explode('|', $_name);
		if (strpos($_name, '|') !== false) {
			$get_category_list = explode('|', $_name);
		} elseif (strpos($_name, ',') !== false) {
			$get_category_list = explode(',', $_name);
		} elseif (strpos($_name, '->') !== false) {
			$get_category_list = explode('->', $_name);
		} else {
			$get_category_list[] = trim($_name);
		}
		$parent_term_id = 0;
		if (count($get_category_list) == 1) {
			$_name = trim($get_category_list[0]);
		} else {
			$count = count($get_category_list);
			$_name = trim($get_category_list[$count - 1]);
			$checkParent = trim($get_category_list[$count - 2]);
			$parent_term = term_exists("$checkParent", "$taxonomy");
			$parent_term_id = $parent_term['term_id'];
		}
		// Check term whether its already exist or not
		if($conditions){
			if($conditions[0] == 'slug'){
				$duplicate = $_slug;
			}elseif($conditions[0] == 'termid' && !empty($data_array['TERMID'])){
				 $getterms = get_term_by('term_taxonomy_id',$data_array['TERMID']);
				 $duplicate = $getterms->slug;
			}
			if($duplicate)
			$checkAvailable = term_exists(htmlspecialchars($duplicate), "$taxonomy", $parent_term_id);
		}else{
			$checkAvailable = term_exists(htmlspecialchars($_name), "$taxonomy", $parent_term_id);
		}
		$termID = '';
		if($mode == 'Insert'){
			if (!is_array($checkAvailable)) {
				$taxoID = wp_insert_term("$_name", "$taxonomy", array('description' => $_desc, 'slug' => $_slug, 'parent' => $parent_term_id));
				$termID = $taxoID['term_id'];
				//start of added for adding thumbnail
				if(isset($_image)) {
				    $_image = trim($_image);
				    $img = $uci_admin->set_featureimage($_image, $termID, $media_handle);
				    update_term_meta($termID , 'thumbnail_id' , $img);
				    }
				if($_display_type){
					update_term_meta($termID , 'display_type' , $_display_type);
				} 
				//end of added for adding thumbnail
				//start wpsc_product_category meta fields
				if($importType = 'wpsc_product_category'){
					  if($data_array['category_image']){
						$udir = wp_upload_dir();
                        			$imobj = new SmackUCIMediaScheduler();
						$imgurl = $data_array['category_image'];
						$img_name = basename($imgurl);
						$uploadpath = $udir['basedir'] . "/wpsc/category_images/";
                                		$imobj->get_img_from_URL($imgurl,$uploadpath,$img_name);
					  }
					  if($data_array['target_market']){
						$custom_market = explode(',', $data_array['target_market']);
						foreach ($custom_market as $key =>$value) {
							$market[$value - 1] = $value;
						}					
					  }
					  $meta_data = array('uses_billing_address' => $data_array['address_calculate'],'image' => $img_name,'image_width' => $data_array['category_image_width'],'image_height' => $data_array['category_image_height'],'display_type'=>$data_array['catelog_view'],'target_market'=>serialize($market));
					  foreach($meta_data as $mk => $mv){
//					  $wpdb->insert( $wpdb->prefix.'wpsc_meta', array('object_type' => 'wpsc_category','object_id' => $termID,'meta_key' => $mk,'meta_value' => $mv),array('%s','%d','%s','%s')); 
					  }

				}
				//end wpsc_product_category meta fields
				global $sitepress;
				if(empty($data_array['translated_post_title']) && !empty($data_array['language_code'])) {
					$wpdb->update( $wpdb->prefix.'icl_translations', array('language_code' => $data_array['language_code'],'element_id' => $termID),array( 'element_id' => $termID ));
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

				if(isset($wpcsvsettings['yoastseooption']) && ($wpcsvsettings['yoastseooption'] == 'enable')){
					$seo_yoast_cat_array = array('wpseo_title'=>$_wpseo_title, 'wpseo_desc'=>$_wpseo_desc, 'wpseo_canonical'=>$_wpseo_canonical, 'wpseo_noindex'=>$_wpseo_noindex, 'wpseo_sitemap_include'=>$_wpseo_sitemap_include);
					$seo_yoast_cat = get_option('wpseo_taxonomy_meta');
					$seo_yoast_cat[$taxonomy][$termID] = $seo_yoast_cat_array;
					update_option('wpseo_taxonomy_meta',$seo_yoast_cat);
				}
				$_SESSION[$eventKey]['summary']['inserted'][] = $termID;
				$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted ' . $taxonomy . ' ID: ' . $termID;
				$returnArr = array('ID' => $termID, 'MODE' => $mode_of_affect);
			} else {
				$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
				$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Term found!.";
				return array('MODE' => $mode, 'ERROR_MSG' => 'The term already exists!');
			}
		} else {
			if(($mode == 'Update' || $mode == 'Schedule')) {
				if($duplicate_action == 'Skip') {
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Term found!.";
					return array('MODE' => $mode, 'ERROR_MSG' => 'The term already exists!');
				}
				if (is_array($checkAvailable)){
					wp_update_term($checkAvailable['term_id'], "$taxonomy", array('name' => $_name, 'slug' => $_slug, 'description' => $_desc,));
					$termID = $checkAvailable['term_id'];
					//start of added for adding thumbnail
					if(isset($_image)){
					    $_image = trim($_image);
					    $img = $uci_admin->set_featureimage($_image, $termID, $media_handle);
					    update_term_meta($termID , 'thumbnail_id' , $img); 
					}
					if($_display_type){
                                        	update_term_meta($termID , 'display_type' , $_display_type);
                                	}
					//end of added for adding thumbnail
					//start wpsc_product_category meta fields
					if($importType = 'wpsc_product_category'){
						  if($data_array['category_image']){
							$udir = wp_upload_dir();
							$imobj = new SmackUCIMediaScheduler();
							$imgurl = $data_array['category_image'];
							$img_name = basename($imgurl);
							$uploadpath = $udir['basedir'] . "/wpsc/category_images/";
							$imobj->get_img_from_URL($imgurl,$uploadpath,$img_name);
						  }
						  if($data_array['target_market']){
							$custom_market = explode(',', $data_array['target_market']);
							foreach ($custom_market as $key =>$value) {
								$market[$value - 1] = $value;
							}
						  }
						  $meta_data = array('uses_billing_address' => $data_array['address_calculate'],'image' => $img_name,'image_width' => $data_array['category_image_width'],'image_height' => $data_array['category_image_height'],'display_type'=>$data_array['catelog_view'],'target_market'=>serialize($market));
						  foreach($meta_data as $mk => $mv){
//						  $wpdb->insert( $wpdb->prefix.'wpsc_meta', array('object_type' => 'wpsc_category','object_id' => $termID,'meta_key' => $mk,'meta_value' => $mv),array('%s','%d','%s','%s'));  
						  }

					}
					//end wpsc_product_category meta fields
					$mode_of_affect = 'Updated';
					if(isset($wpcsvsettings['yoastseooption']) && ($wpcsvsettings['yoastseooption'] == 'enable')){
						$seo_yoast_cat_array = array('wpseo_title'=>$_wpseo_title, 'wpseo_desc'=>$_wpseo_desc, 'wpseo_canonical'=>$_wpseo_canonical, 'wpseo_noindex'=>$_wpseo_noindex, 'wpseo_sitemap_include'=>$_wpseo_sitemap_include);
						$seo_yoast_cat = get_option('wpseo_taxonomy_meta');
						$seo_yoast_cat[$taxonomy][$termID] = $seo_yoast_cat_array;
						update_option('wpseo_taxonomy_meta',$seo_yoast_cat);
					}
					$_SESSION[$eventKey]['summary']['updated'][] = $termID;
					$uci_admin->setUpdatedRowCount($uci_admin->getUpdatedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated ' . $taxonomy . ' ID: ' . $termID;
					$returnArr = array('ID' => $termID, 'MODE' => $mode_of_affect);
				} else {
					$taxoID = wp_insert_term("$_name", "$taxonomy", array('description' => $_desc, 'slug' => $_slug, 'parent' => $parent_term_id));
					$termID = $taxoID['term_id'];
					if(isset($wpcsvsettings['yoastseooption']) && ($wpcsvsettings['yoastseooption'] == 'enable')){
						$seo_yoast_cat_array = array('wpseo_title'=>$_wpseo_title, 'wpseo_desc'=>$_wpseo_desc, 'wpseo_canonical'=>$_wpseo_canonical, 'wpseo_noindex'=>$_wpseo_noindex, 'wpseo_sitemap_include'=>$_wpseo_sitemap_include);
						$seo_yoast_cat = get_option('wpseo_taxonomy_meta');
						$seo_yoast_cat[$taxonomy][$termID] = $seo_yoast_cat_array;
						update_option('wpseo_taxonomy_meta',$seo_yoast_cat);
					}
					$_SESSION[$eventKey]['summary']['inserted'][] = $termID;
					$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted ' . $taxonomy . ' ID: ' . $termID;
					$returnArr = array('ID' => $termID, 'MODE' => $mode_of_affect);
				}
			} else {
				if($duplicate_action == 'Skip'){
					$uci_admin->setSkippedRowCount($uci_admin->getSkippedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Skipped, Due to duplicate Term found!.";
					return array('MODE' => $mode, 'ERROR_MSG' => 'The term already exists!');
				}
				if (!is_array($checkAvailable)) {
					$taxoID = wp_insert_term("$_name", "$taxonomy", array('description' => $_desc, 'slug' => $_slug, 'parent' => $parent_term_id));
					$termID = $taxoID['term_id'];
					if(isset($wpcsvsettings['yoastseooption']) && ($wpcsvsettings['yoastseooption'] == 'enable')){
						$seo_yoast_cat_array = array('wpseo_title'=>$_wpseo_title, 'wpseo_desc'=>$_wpseo_desc, 'wpseo_canonical'=>$_wpseo_canonical, 'wpseo_noindex'=>$_wpseo_noindex, 'wpseo_sitemap_include'=>$_wpseo_sitemap_include);
						$seo_yoast_cat = get_option('wpseo_taxonomy_meta');
						$seo_yoast_cat[$taxonomy][$termID] = $seo_yoast_cat_array;
						update_option('wpseo_taxonomy_meta',$seo_yoast_cat);
					}
					$_SESSION[$eventKey]['summary']['inserted'][] = $termID;
					$uci_admin->setInsertedRowCount($uci_admin->getInsertedRowCount() + 1);
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted ' . $taxonomy . ' ID: ' . $termID;
					$returnArr = array('ID' => $termID, 'MODE' => $mode_of_affect);
				}else{
					return array('MODE' => $mode, 'ERROR_MSG' => 'The update column name doesnot correct');
				}
			}
		}

		if (isset($taxoID->errors)) {
		}

		if(!is_wp_error($termID)) {
			update_option("$taxonomy" . "_children", $term_children_options);
			delete_option($taxonomy . "_children");
		}
		return $returnArr;
	}
}
