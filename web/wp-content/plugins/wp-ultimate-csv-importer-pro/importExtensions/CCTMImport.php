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

class CCTMImport {
    private static $cctm_instance = null;

    public static function getInstance() {
		
				if (CCTMImport::$cctm_instance == null) {
					CCTMImport::$cctm_instance = new CCTMImport;
					return CCTMImport::$cctm_instance;
				}
				return CCTMImport::$cctm_instance;
    }
    function set_cctm_values($header_array ,$value_array , $map, $post_id , $type){	

				$post_values = [];
				$helpers_instance = ImportHelpers::getInstance();	
				$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
				
				$this->cctm_import_function($post_values, $post_id);
        
    }
    function cctm_import_function($data_array,$pID) {
				$createdFields = array();
				foreach ($data_array as $custom_key => $custom_value) {
					$createdFields[] = $custom_key;
					update_post_meta($pID, $custom_key, $custom_value);
				}
				return $createdFields;
    }
}