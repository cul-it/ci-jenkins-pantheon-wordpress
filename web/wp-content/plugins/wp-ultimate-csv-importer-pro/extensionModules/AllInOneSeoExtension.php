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

class AllInOneSeoExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (AllInOneSeoExtension::$instance == null) {
			AllInOneSeoExtension::$instance = new AllInOneSeoExtension;
		}
		return AllInOneSeoExtension::$instance;
	}
	
	/**
	* Provides All In One Seo mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = [];
        $all_in_one_seo_Fields = array(
			'Keywords' => 'keywords',
			'Description' => 'description',
			'Title' => 'title',
			'NOINDEX' => 'noindex',
			'NOFOLLOW' => 'nofollow',
			'Canonical URL' => 'custom_link',
			'Title Atr' => 'titleatr',
			'Menu Label' => 'menulabel',
			'Disable' => 'disable',
			'Disable Analytics' => 'disable_analytics',
			'NOODP' => 'noodp',
			'NOYDIR' => 'noydir'
        );
		$all_in_one_seo_value = $this->convert_static_fields_to_array($all_in_one_seo_Fields);
		$response['all_in_one_seo_fields'] = $all_in_one_seo_value ;
		return $response;
		
    }

	/**
	* All In One Seo extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type){
		if(is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')|| is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php')){
			if($import_type == 'nav_menu_item'){
				return false;
			}
			
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'event' || $import_type == 'event-recurring' || $import_type == 'location' || $import_type == 'WooCommerce' || $import_type == 'MarketPress' || $import_type == 'WPeCommerce' || $import_type == 'eShop' ) {	
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