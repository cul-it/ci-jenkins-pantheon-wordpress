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

class MultiroleImport {
    private static $multirole_instance = null;

    public static function getInstance() {
		
				if (MultiroleImport::$multirole_instance == null) {
					MultiroleImport::$multirole_instance = new MultiroleImport;
					return MultiroleImport::$multirole_instance;
				}
				return MultiroleImport::$multirole_instance;
    }
    function set_multirole_values($header_array ,$value_array , $map, $post_id , $type){
				$post_values = [];
				$helpers_instance = ImportHelpers::getInstance();	
				$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
				
				$this->multirole_import_function($post_values, $post_id);	
        
    }

    public function multirole_import_function($data_array, $uID){
				if(isset($data_array['multi_user_role'])){
						$roles = explode('|', $data_array['multi_user_role']);
						foreach ($roles as $key => $value) {
								$members_role[$value] = 1;
						}
						update_user_meta($uID, 'wp_capabilities', $members_role);
				}
    }
}