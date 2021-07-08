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

class ToolsetExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {		
		if (ToolsetExtension::$instance == null) {
			ToolsetExtension::$instance = new ToolsetExtension;
		}
		return ToolsetExtension::$instance;
    }

	/**
	* Provides Toolset fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
		global $wpdb;
		$import_types = $data;	
		$import_name_type = $this->import_name_as($import_types);	
		$response = [];
		$typesFields = array();
		if($import_types == 'Users') {
			$getUserMetaFields = get_option('wpcf-usermeta');
			if(is_array($getUserMetaFields)) {
				foreach ($getUserMetaFields as $optKey => $optVal) {
					$typesFields["TYPES"][$optVal['slug']]['label'] = $optVal['name'];
					$typesFields["TYPES"][$optVal['slug']]['name'] = $optVal['slug'];
				}
			}
		} else {
			$import_type = $this->import_post_types($import_name_type);	
			$get_groups = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_type = %s", 'wp-types-group'));	
            $get_groupsc = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_type = %s", 'wp-types-term-group'));
			if(!empty($get_groupsc && ($import_name_type == 'Categories' || $import_name_type == 'Tags' || $import_name_type == 'Taxonomies'))) {	
				$wptermsfields = array();
				$wptermsfields = get_option('wpcf-termmeta');
				foreach($get_groupsc as $item => $group) {
					$lastId       = $group->ID;
					$rule_groups  = $import_type;	
					$rule_groups = trim($rule_groups,',');	
					$rules = explode(',', $rule_groups);
					
					if(in_array($import_type, $rules)) {
						$fields       = get_post_meta( $lastId, '_wp_types_group_fields', true );	
						$group_names  = get_post_meta( $lastId, '_wp_types_associated_taxonomy', false );
						if(in_array($import_types , $group_names)){
							$fields       = trim($fields, ',');
							$types_fields = explode( ',', $fields );
							
							$count = count( $types_fields );
							if ( is_array( $types_fields ) ) {
								for ( $i = 0; $i < $count; $i ++ ) {
									foreach($wptermsfields as $term_field_value){
										$search_value = $term_field_value['slug'] ;
										if(in_array($search_value , $types_fields)){
											$typesFields['TYPES'][ $search_value ]['name']  = $term_field_value['slug'];
											$typesFields['TYPES'][ $search_value ]['slug']  = $term_field_value['slug'];
											$typesFields['TYPES'][ $search_value ]['label'] = $term_field_value['name'];
										}
									}	
								}
							}
						}
					}
				}
				
			}
			
			if(!empty($get_groups && ($import_name_type !== 'Categories' && $import_name_type !== 'Tags' && $import_name_type !== 'Taxonomies'))) {
				$import_type = $this->import_post_types($import_name_type);					
				$relation_group_name = false;	
				foreach($get_groups as $item => $group) {
					$lastId       = $group->ID;	
					$rule_groups  = get_post_meta( $lastId, '_wp_types_group_post_types', true );	
					$rule_group = trim($rule_groups,',');
					$rules = explode(',', $rule_group);					
					if($import_type == 'CustomPosts'){
						$import_type = $import_types;
					}
					
					if(in_array($import_type , $rules)||in_array('all',$rules)){
						
						$get_fields = $wpdb->get_results("SELECT post_id from {$wpdb->prefix}postmeta where meta_value = '$rule_groups' ");	
						foreach($get_fields as $get_id){
							$ID = $get_id->post_id;
							$get_status = $wpdb->get_var("SELECT post_status FROM {$wpdb->prefix}posts WHERE id = $ID");
							if($get_status == 'publish'){
								$fields       = get_post_meta( $ID, '_wp_types_group_fields', true );	
								$fields       = trim($fields, ',');
								$types_fields = explode( ',', $fields );
								$count        = count( $types_fields );
								if ( is_array( $types_fields ) ) {
									for ( $i = 0; $i < $count; $i ++ ) {		
										foreach ( $types_fields as $key => $value ) {	
											if(!empty($value)){
												//change repeatable_group to user readable format	
												$value = $this->changeRepeatableGroupName($value);
												
												if(is_array($value)){	
													foreach($value as $repeat_value){
														$typesFields['TYPES'][ $repeat_value ]['name']  = $repeat_value;
														$typesFields['TYPES'][ $repeat_value ]['slug']  = $repeat_value;
														$typesFields['TYPES'][ $repeat_value ]['label'] = $repeat_value;
													}
													$relation_group_name = true;
												}
												else{
													$typesFields['TYPES'][ $value ]['name']  = $value;
													$typesFields['TYPES'][ $value ]['slug']  = $value;
													$typesFields['TYPES'][ $value ]['label'] = $value;
												}	
											}
										}
									}
								}	
							}
						}
					}
				}
			} 
			if(is_plugin_active('types/wpcf.php')){
			$relationship_table_name = $wpdb->prefix . "toolset_relationships";
			$get_relationship = $wpdb->get_results( "SELECT id FROM $relationship_table_name" );
			}
			$import_type = $this->import_post_types($import_name_type);
			
			if($import_type == 'CustomPosts'){
				$import_type = $import_types;
			}

			if(!empty($get_relationship)){
				if($import_name_type !== 'Categories' && $import_name_type !== 'Tags' && $import_name_type !== 'Taxonomies'){
					$check_relation_id = array();
					$check_relationship = $wpdb->get_results("SELECT parent_types, child_types FROM $relationship_table_name WHERE origin = 'wizard' ");
					foreach($check_relationship as $check_relationship_values){	
						$check_relation_id[] = $check_relationship_values->parent_types;
						$check_relation_id[] = $check_relationship_values->child_types;
					}
					$get_relation_types = array();
					foreach($check_relation_id as $get_relation_id){
						$get_relation_types[] = $wpdb->get_var("SELECT type FROM {$wpdb->prefix}toolset_type_sets WHERE set_id = $get_relation_id ");
					}

					$check_intermediate = $wpdb->get_results("SELECT slug FROM $relationship_table_name where intermediary_type != '' ");
					$is_intermediate = false;
					if(!empty($check_intermediate)){
						$intermediate_rel = array();	
						foreach($check_intermediate as $check_value){
							$intermediate_rel[] = $check_value->slug;
						}
						if(in_array($import_types , $intermediate_rel)){
							$typesFields['TYPES']['intermediate']['label'] = 'Intermediate';
							$typesFields['TYPES']['intermediate']['name'] = 'intermediate';
							$typesFields['TYPES']['intermediate']['slug'] = 'intermediate';

							$is_intermediate = true;
						}
					}	
					
					$is_relation = false;
					if(!$is_intermediate && in_array($import_type,$get_relation_types)){
						$typesFields['TYPES']['types_relationship']['label'] = 'Types Relationship';
						$typesFields['TYPES']['types_relationship']['name'] = 'types_relationship';
						$typesFields['TYPES']['types_relationship']['slug'] = 'types_relationship';

						$is_relation = true;
					}	

					if($is_intermediate || $is_relation){
						$typesFields['TYPES']['relationship_slug']['label'] = 'Relationship Slug';
						$typesFields['TYPES']['relationship_slug']['name'] = 'relationship_slug';
						$typesFields['TYPES']['relationship_slug']['slug'] = 'relationship_slug';
					}

					if($relation_group_name){
						$typesFields['TYPES']['Parent_group']['label'] = 'Parent Group';
						$typesFields['TYPES']['Parent_group']['name'] = 'Parent_Group';
						$typesFields['TYPES']['Parent_group']['slug'] = 'Parent_Group';
					}
				}
			}
		}
		$tool_value = $this->convert_fields_to_array($typesFields);
		$response['types_fields'] = $tool_value;
		return $response;		
	}

	public function changeRepeatableGroupName($value) {
        global $wpdb;
        $explode = explode('_',$value);
        if (count($explode)>1) {
            if (in_array('repeatable',$explode)) {
				$merge = [];
				$name = $wpdb->get_results("SELECT post_name FROM ".$wpdb->prefix."posts WHERE id ='{$explode[3]}'");	
				$types_fields = array();
				$repeat_id = $explode[3];
				$repeat_fields = get_post_meta( $repeat_id, '_wp_types_group_fields', true );	
				$repeat_field  = trim($repeat_fields, ',');
				$types_fields = explode( ',', $repeat_field );
				array_push($types_fields ,  $name[0]->post_name);

				foreach($types_fields as $keys => $type_field_value){
					if (strpos($type_field_value, '_repeatable_group') !== false) {	
						$type_fields = $this->changeRepeatableGroupName($type_field_value);
						unset($types_fields[$keys]);
					}
				}
	
				if(!empty($type_fields)){
					$merge = array_merge($types_fields ,$type_fields);
				}else{
					$merge = $types_fields;
				}
				return $merge;
            }else{
				return $value;
			}
        }else{
            return $value;
		}
	}

	/**
	* Toolset extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
	public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('types/wpcf.php')){
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
			else{
				return false;
			}
		}
	}
}
