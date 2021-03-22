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

class WPMLExtension extends ExtensionHandler{
	private static $instance = null;

	public static function getInstance() {
		if (WPMLExtension::$instance == null) {
			WPMLExtension::$instance = new WPMLExtension;
		}
		return WPMLExtension::$instance;
	}
	public function processExtension($data) {
	    $response = [];
        $import_type = $this->import_name_as($data);
        if($import_type == 'Taxonomies' || $import_type =='Tags' || $import_type =='Categories'){
        	$wpmlFields = array(
			'LANGUAGE_CODE' => 'language_code',
			'TRANSLATED_TAXONOMY_TITLE' => 'translated_taxonomy_title');
        }
        else{
        	$wpmlFields = array(
			'LANGUAGE_CODE' => 'language_code',
			'TRANSLATED_POST_TITLE' => 'translated_post_title');
        }
		$wpml_value = $this->convert_static_fields_to_array($wpmlFields);
		$response['wpml_fields'] = $wpml_value ;
		return $response;
	}

	/**
	 * Nextgen Gallery extension supported import types
	 * @param string $import_type - selected import type
	 * @return boolean
	 */
	public function extensionSupportedImportType($import_type){
		global $sitepress;
		if($sitepress != null) {
			if($import_type == 'nav_menu_item'){
				return false;
			}
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'WooCommerce' || $import_type =='Taxonomies' || $import_type =='Tags' || $import_type =='Categories' || $import_type =='CustomPosts' ) {
				return true;
			}
			else{
				return false;
			}
		}
	}
}
