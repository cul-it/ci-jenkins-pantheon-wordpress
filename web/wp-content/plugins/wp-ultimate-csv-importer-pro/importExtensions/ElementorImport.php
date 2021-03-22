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

class ElementorImport {
	private static $elementor_instance = null,$media_instance;

	public static function getInstance() {
		if (ElementorImport::$elementor_instance == null) {
			ElementorImport::$elementor_instance = new ElementorImport;
			return ElementorImport::$elementor_instance;
		}
		return ElementorImport::$elementor_instance;
	}

	function set_elementor_values($header_array ,$value_array , $map, $post_id , $type, $group_name){	
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		foreach ($post_values as $custom_key => $custom_value) {
			if(is_serialized($custom_value) && $custom_key != '_elementor_data'){
				$custom_value = unserialize($custom_value);		
			}
			elseif($custom_key =='_elementor_data'){
				$custom_value = wp_slash($custom_value);
			}
			update_post_meta($post_id, $custom_key, $custom_value);
		}

	}
}
