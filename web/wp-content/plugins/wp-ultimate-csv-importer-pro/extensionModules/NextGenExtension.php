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

class NextGenExtension extends ExtensionHandler{
	private static $instance = null;
	
    public static function getInstance() {		
		if (NextGenExtension::$instance == null) {
			NextGenExtension::$instance = new NextGenExtension;
		}
		return NextGenExtension::$instance;
    }

	/**
	* Provides Nextgen Gallery mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = [];
        $nextgenFields = array(
			'FILENAME' => 'filename',
			'ALT & TITLE TEXT' => 'alttext',
			'DESCRIPTION' => 'description',
			'GALLERY NAME' => 'nextgen_gallery',
			'IMAGE' => 'image_url',
			//'TAGS' => 'manage_tags',

        );
		$next_gen_value = $this->convert_static_fields_to_array($nextgenFields);
		$response['nextgen_gallery_fields'] = $next_gen_value ;
		return $response;
    }

	/**
	* Nextgen Gallery extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_types){
		if(is_plugin_active('nextgen-gallery/nggallery.php')){
			if($import_types == 'nav_menu_item'){
				return false;
			}
			$import_type = $this->import_name_as($import_types);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'WooCommerce' || $import_type == 'MarketPress' || $import_type == 'WPeCommerce' || $import_type == 'eShop' || $import_type =='CustomPosts' || $import_type == 'WooCommerceCategories' || $import_types == 'product_cat' || $import_types == 'wpsc_product_category' ) {
				return true;
			}
			else{
				return false;
			}
		}
	}
}