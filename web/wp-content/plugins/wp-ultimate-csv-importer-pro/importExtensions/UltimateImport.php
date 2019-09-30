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

class UltimateImport {
    private static $ultimate_instance = null;

    public static function getInstance() {
		
		if (UltimateImport::$ultimate_instance == null) {
			UltimateImport::$ultimate_instance = new UltimateImport;
			return UltimateImport::$ultimate_instance;
		}
		return UltimateImport::$ultimate_instance;
    }
    function set_ultimate_values($header_array ,$value_array , $map, $post_id , $type){	
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->ultimate_import_function($post_values, $post_id);

    }

    public function ultimate_import_function($data_array, $uID) {
        if(!empty($data_array)) {
            foreach ($data_array as $custom_key => $custom_value) {
                update_user_meta($uID, $custom_key, $custom_value);
            }
        }
	}
}
