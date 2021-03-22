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

class PodsExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {	
		if (PodsExtension::$instance == null) {
			PodsExtension::$instance = new PodsExtension;
		}
		return PodsExtension::$instance;
    }

	/**
	* Provides Pods mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
		global $wpdb;
		$import_type = $data;
		$import_type = $this->import_type_as($import_type);
		$response = [];
		$podsFields = array();
		$import_type = $this->import_post_types($import_type);
		$post_id = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_name= %s and post_type = %s", $import_type, '_pods_pod'));
		if(empty($post_id) && $import_type == 'comments'){
            $post_id = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_name= %s and post_type = %s", 'comment', '_pods_pod'));
		}

		if(!empty($post_id)) {
			$lastId = $post_id[0]->ID;
			$get_pods_fields = $wpdb->get_results( $wpdb->prepare( "SELECT post_title, post_name FROM {$wpdb->prefix}posts where post_parent = %d AND post_type = %s", $lastId, '_pods_field' ) );
			if ( ! empty( $get_pods_fields ) ) :
				foreach ( $get_pods_fields as $pods_field ) {
					$podsFields["PODS"][ $pods_field->post_name ]['label'] = $pods_field->post_title;
					$podsFields["PODS"][ $pods_field->post_name ]['name']  = $pods_field->post_name;
				}
			endif;
		}
		$pods_value = $this->convert_fields_to_array($podsFields);
		$response['pods_fields'] = $pods_value;
		return $response;
			
	}

	/**
	* Pods extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
	public function extensionSupportedImportType($import_type){
		if(is_plugin_active('pods/init.php')){
			if($import_type == 'nav_menu_item'){
				return false;
			}
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'Taxonomies' || $import_type == 'Categories' || $import_type == 'Tags' || $import_type == 'event' || $import_type == 'event-recurring' || $import_type == 'location' || $import_type == 'Users' || $import_type == 'WooCommerce' || $import_type == 'MarketPress' || $import_type == 'WPeCommerce' || $import_type == 'eShop' || $import_type == 'Comments') {	
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