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

class TypesImageMetaExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (TypesImageMetaExtension::$instance == null) {
			TypesImageMetaExtension::$instance = new TypesImageMetaExtension;
		}
		return TypesImageMetaExtension::$instance;
	}
	
	/**
	* Provides Types Image Meta mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = [];
        $types_image_meta_Fields = array(
			        'Caption' => 'types_caption',
					'Alt text' => 'types_alt_text',
					'Description' => 'types_description',
					'File Name' => 'types_file_name',
					'Title' => 'types_title',
        );
		$types_image_meta_value = $this->convert_static_fields_to_array($types_image_meta_Fields);
		$response['types_image_meta_fields'] = $types_image_meta_value ;
		return $response;
		
    }

	/**
	* Types Image Meta Fields extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type){
        if(is_plugin_active('types/wpcf.php')){
            $wptypesfields = get_option('wpcf-fields');
            foreach($wptypesfields as $types_field)
            {
                if( $types_field['type'] == 'image'){
                    if($import_type == 'nav_menu_item'){
                      return false;
                    }
                    $import_type = $this->import_name_as($import_type);
                    if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'event' || $import_type == 'event-recurring' || $import_type == 'location' || $import_type == 'Users' || $import_type == 'WooCommerce' || $import_type == 'MarketPress' || $import_type == 'WPeCommerce' || $import_type == 'eShop' || $import_type == 'Taxonomies' || $import_type == 'Categories' || $import_type == 'Tags' ) {
                      return true;
                    }
                    if($import_type == 'ticket'){
                       if(is_plugin_active('events-manager/events-manager.php')){
                           return false;
                        }else{
                          return true;
                        }
                    }
                    
                }
                
            }
        }

    }
}