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

class ElementorExtension extends ExtensionHandler{
	private static $instance = null;

	public static function getInstance() {	
		if (ElementorExtension::$instance == null) {
			ElementorExtension::$instance = new ElementorExtension;
		}
		return ElementorExtension::$instance;
	}

	/**
	 * Provides Product Meta fields for specific post type
	 * @param string $data - selected import type
	 * @return array - mapping fields
	 */
	public function processExtension($data){  

		$import_type = $data;
		$response = [];
		$import_type = $this->import_type_as($import_type);
		if(is_plugin_active('elementor-pro/elementor-pro.php')){   
			if($import_type == 'elementor_library' || $import_type == 'Pages'){
				$pro_meta_fields = array(
					'Elementor Template Type' => '_elementor_template_type',
					'Elementor Data' => '_elementor_data',
					'Elementor Version' => '_elementor_version',
					'Elementor Pro Version' => '_elementor_pro_version',
					'Page Template' => '_wp_page_template',
					'Elementor Edit Mode' => '_elementor_edit_mode',
					'Elementor Library Type' => 'elementor_library_type',
					'Elementor Controls Usage' => '_elementor_controls_usage',
					//'Elementor Library Category' => 'elementor_library_category',
					'Elementor CSS' => '_elementor_css',
					'Elementor Conditions' => '_elementor_conditions'

				);

			}

		}

		$pro_meta_fields_line = $this->convert_static_fields_to_array($pro_meta_fields);
		$response['elementor_meta_fields'] = $pro_meta_fields_line; 

		return $response;

	}

	/**
	 * Product Meta extension supported import types
	 * @param string $import_type - selected import type
	 * @return boolean
	 */
	public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('elementor-pro/elementor-pro.php')){
			if($import_type == 'elementor_library' || $import_type == 'Pages') { 
				return true;
			}else{
				return false;
			}
		}
	}

}
