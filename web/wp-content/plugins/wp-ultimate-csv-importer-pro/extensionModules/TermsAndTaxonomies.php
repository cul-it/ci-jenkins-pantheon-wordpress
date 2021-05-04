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

class TermsAndTaxonomies extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		if (TermsAndTaxonomies::$instance == null) {
			TermsAndTaxonomies::$instance = new TermsAndTaxonomies;
		}
		return TermsAndTaxonomies::$instance;
    }

	/**
	* Provides Terms and Taxonomies fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = [];
		$import_type = $data;
		$import_type = $this->import_type_as($import_type);
        $term_taxonomies = array();
		$importas = $this->import_post_types($import_type);	
		$taxonomies = get_object_taxonomies( $importas, 'names' );
		$search_array = array('post_format','product_type','product_visibility','product_shipping_class');
		foreach($search_array as $search_values){
			if(in_array($search_values , $taxonomies)){
				$search_format = array_search($search_values , $taxonomies);
				unset($taxonomies[$search_format]);
			}
		}
			
		if(!empty($taxonomies)) {
			foreach ($taxonomies as $key => $value) {
				$get_taxonomy_label = get_taxonomy($value);
				$taxonomy_label = $get_taxonomy_label->name;
				if($value == 'wpsc_product_category' || $value == 'product_cat'){
					$value = 'product_category';
				}elseif($value == 'category'){
					$value = 'post_category';
				}
				$term_taxonomies['TERMS'][$key]['label'] = $taxonomy_label;
				$term_taxonomies['TERMS'][$key]['name'] = $value;
			}
		}
		
		$terms_value = $this->convert_fields_to_array($term_taxonomies);
		$response['terms_and_taxonomies'] =  $terms_value ;
		return $response;		
    }

	/**
	* Terms and Taxonomies extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		if($import_type == 'nav_menu_item'){
			return false;
		}	
		$import_type = $this->import_name_as($import_type);
			if($import_type =='Posts' || $import_type =='Pages' || $import_type =='CustomPosts' || $import_type =='event' || $import_type == 'event-recurring' || $import_type =='location' || $import_type =='WooCommerce' || $import_type =='MarketPress' || $import_type =='WPeCommerce' || $import_type =='eShop') {	
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