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

class WPMLImport {
	private static $wpml_instance = null;

	public static function getInstance() {

		if (WPMLImport::$wpml_instance == null) {
			WPMLImport::$wpml_instance = new WPMLImport;
			return WPMLImport::$wpml_instance;
		}
		return WPMLImport::$wpml_instance;
	}
	function set_wpml_values($header_array ,$value_array , $map, $post_id , $type){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();	
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);

		$this->wpml_import_function($post_values,$type, $post_id);

	}

	function wpml_import_function($data_array, $importas,$pId) {
		global $sitepress, $wpdb;
		$extension_object = new ExtensionHandler;
		$taxonomies = get_taxonomies();
		if (in_array($importas, $taxonomies)) {
			$import_type = $type;
			if($import_type == 'category' || $import_type == 'product_category' || $import_type == 'product_cat' || $import_type == 'wpsc_product_category' || $import_type == 'event-categories'):
				$import_as = 'Categories';
			elseif($import_type == 'product_tag' || $import_type == 'event-tags' || $import_type == 'post_tag'):
				$import_as = 'Tags';
		else:
			$import_as = 'Taxonomies';
			endif;
		}
		$import_type = $extension_object->import_type_as($importas);
		$importAs = $extension_object->import_post_types($import_type );
		$get_trid = $wpdb->get_results("select trid from {$wpdb->prefix}icl_translations ORDER BY translation_id DESC limit 1");
		$trid = $get_trid[0]->trid;
		if(empty($data_array['translated_taxonomy_title']) && empty($data_array['translated_post_title'])){
			$icl_translations = $wpdb->get_results("UPDATE {$wpdb->prefix}icl_translations SET trid = $trid + 1,  language_code = '{$data_array['language_code']}' WHERE  element_id = $pId");
		}
		elseif(!empty($data_array['language_code']) && !empty($data_array['translated_post_title'] || $data_array['translated_taxonomy_title'])){
			if($import_as == 'Taxonomies' || $import_as == 'Categories' || $import_as == 'Tags'){
				$termdata = get_term_by('name', $data_array['translated_taxonomy_title'],$importas,'ARRAY_A');
				if(is_array($termdata) && !empty($termdata)) {
					$element_id = $termdata['term_id'];
					$taxo_type = $termdata['taxonomy'];
				}
				else{
					return false;
				}
				$trid_id = $sitepress->get_element_trid($element_id,'tax_'.$taxo_type);
				$translate_lcode = $sitepress->get_language_for_element($element_id,'tax_'.$taxo_type);
				$element_type = 'tax_'.$taxo_type;
				$icl_translations = $wpdb->get_results("UPDATE {$wpdb->prefix}icl_translations SET trid = $trid_id,  language_code = '{$data_array['language_code']}',source_language_code = '$translate_lcode' WHERE  element_id = $pId and element_type='$element_type'");
			}
			else{
				$update_query = $wpdb->prepare("select ID,post_type from $wpdb->posts where post_title = %s and post_type=%s order by ID DESC",$data_array['translated_post_title'] , $importAs);
				$ID_result = $wpdb->get_results($update_query);
				if(is_array($ID_result) && !empty($ID_result)) {
					$element_id = $ID_result[0]->ID;
					$post_type = $ID_result[0]->post_type;
				}else{
					return false;
				}
				$trid_id = $sitepress->get_element_trid($element_id,'post_'.$post_type);
				$translate_lcode = $sitepress->get_language_for_element($element_id,'post_'.$post_type);
				$element_type = 'post_'.$post_type;
				$icl_translations = $wpdb->get_results("UPDATE {$wpdb->prefix}icl_translations SET trid = $trid_id,  language_code = '{$data_array['language_code']}',source_language_code = '$translate_lcode' WHERE  element_id = $pId and element_type='$element_type'");			
			}
		}
	}
}
