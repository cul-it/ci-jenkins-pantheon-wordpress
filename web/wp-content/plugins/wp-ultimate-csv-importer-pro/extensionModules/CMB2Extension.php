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

class CMB2Extension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {		
		if (CMB2Extension::$instance == null) {
			CMB2Extension::$instance = new CMB2Extension;
		}
		return CMB2Extension::$instance;
	}
	
	/**
	* Provides CMB2 mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
		global $wpdb;
        $response = [];	
		$get_csvpro_settings = get_option('sm_uci_pro_settings');
		$prefix = $get_csvpro_settings['cmb2'];
		$get_meta_info = $wpdb->get_results(("select distinct(meta_key) from {$wpdb->prefix}postmeta where meta_key REGEXP '^{$prefix}'"), ARRAY_A);
		foreach($get_meta_info as $key => $val){
			$meta_key = str_replace($prefix," ",$get_meta_info[$key]['meta_key']);
			$cmb2Fields[$meta_key] = $get_meta_info[$key]['meta_key'];
		}
		$cmbFields = array();
		foreach ($cmb2Fields as $key => $val){
			$cmbFields['CMB2'][$val]['label'] = $key;
			$cmbFields['CMB2'][$val]['name'] = $val;
        }
		$cmb2_value = $this->convert_fields_to_array($cmbFields);
		$response['cmb2_fields'] =  $cmb2_value ;
    	return $response;	

    }

	/**
	* CMB2 extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('cmb2/init.php')){
			if($import_type == 'nav_menu_item'){
				return false;
			}
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'event' || $import_type == 'event-recurring' ) {
				return true;
			}
			else{
				return false;
			}
		}
	}
}