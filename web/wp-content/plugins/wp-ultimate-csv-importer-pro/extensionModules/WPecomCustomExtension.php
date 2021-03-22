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

class WPecomCustomExtension extends ExtensionHandler{
	private static $instance = null;
	
    public static function getInstance() {		
		if (WPecomCustomExtension::$instance == null) {
			WPecomCustomExtension::$instance = new WPecomCustomExtension;
		}
		return WPecomCustomExtension::$instance;
    }

	/**
	* Provides WPecom Custom fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = []; 
        $WPeComCustomFields = array();
		$get_wpecom_custom_fields = get_option('wpsc_cf_data');
		$wpecom_custom_fields = maybe_unserialize($get_wpecom_custom_fields);
		if(!empty($wpecom_custom_fields)) {
			foreach($wpecom_custom_fields as $key => $val) {
				$WPeComCustomFields['WPECOMMETA'][$val['slug']]['label'] = $val['name'];
				$WPeComCustomFields['WPECOMMETA'][$val['slug']]['name'] = $val['slug'];
			}
		}
		$wp_ecom_custom_value = $this->convert_fields_to_array($WPeComCustomFields);
		$response['wp_ecom_custom_fields'] =  $wp_ecom_custom_value ;
		return $response;		
	}
	
	/**
	* WPecom Custom extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		$import_type = $this->import_name_as($import_type);
		if($import_type == 'WPeCommerce' && is_plugin_active('wp-e-commerce-custom-fields/custom-fields.php')){
					return true;
		}else{
            return false;
        }
	}

}