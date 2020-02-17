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

class WPeComCustomImport {
    private static $wpecom_custom_instance = null;

    public static function getInstance() {
		
		if (WPeComCustomImport::$wpecom_custom_instance == null) {
			WPeComCustomImport::$wpecom_custom_instance = new WPeComCustomImport;
			return WPeComCustomImport::$wpecom_custom_instance;
		}
		return WPeComCustomImport::$wpecom_custom_instance;
    }
    function set_wpecom_custom_values($header_array ,$value_array , $map, $post_id , $type){
        $post_values = [];
        $helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->wpecom_custom_import($post_values, $post_id);	

    }

    function wpecom_custom_import($wpcommeta, $pID){
        foreach ($wpcommeta as $wpkey => $wpval) {
            $get_wpcf = unserialize(get_option('wpsc_cf_data'));
                if (is_array($get_wpcf)) {
                    foreach ($get_wpcf as $wpcf_key => $wpcf_val) {
                        if ($wpkey == $wpcf_val['slug']) {
                            $name = '_wpsc_' . $wpcf_val['slug'];
                            if ($wpcf_val['type'] == 'radio' || $wpcf_val['type'] == 'checkbox') {
                                $exploded_check_value = explode('|', $wpcommeta[$wpkey]);
                                if (!empty($exploded_check_value)) {
                                    $metaDatas[$name] = $exploded_check_value;
                                } else {
                                    $metaDatas[$name] = array(0 => $wpcommeta[$wpkey]);
                                }
                            } else {
                                $metaDatas[$name] = $wpcommeta[$wpkey];
                            }
                        }
                    }
                }
        }
        if (!empty ($metaDatas)) {
            foreach ($metaDatas as $custom_key => $custom_value) {
                update_post_meta($pID, $custom_key, $custom_value);
            }
        }
    }
}