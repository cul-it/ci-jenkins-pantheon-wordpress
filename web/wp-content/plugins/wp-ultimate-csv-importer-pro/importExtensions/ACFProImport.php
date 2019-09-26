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

class ACFProImport {
	protected static $acf_pro_instance = null , $media_instance;

	public static function getInstance() {
		if (ACFProImport::$acf_pro_instance == null) {
			ACFProImport::$acf_pro_instance = new ACFProImport;
			ACFProImport::$media_instance = new MediaHandling;
			return ACFProImport::$acf_pro_instance;
		}
		return ACFProImport::$acf_pro_instance;
	}

	public function set_acf_pro_values($header_array ,$value_array , $map, $post_id , $type){	

		$mapping_instance = MappingExtension::getInstance();
		$acf_instance = ACFImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();

		foreach($map as $key => $value){
			$csv_value= trim($map[$key]);
			if(!empty($csv_value)){
				$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
				if(preg_match_all($pattern, $csv_value, $matches, PREG_PATTERN_ORDER)){	
					$csv_element = $csv_value;
					foreach($matches[2] as $value){
						$get_key = array_search($value , $header_array);
						if(isset($value_array[$get_key])){
							$csv_value_element = $value_array[$get_key];	
							//}
						$value = '{'.$value.'}';
						$csv_element = str_replace($value, $csv_value_element, $csv_element);
					}
				}

				$math = 'MATH(';
						if (strpos($csv_element, $math) !== false) {		
						$equation = str_replace('MATH(', '', $csv_element);
								$equation = str_replace(')', '', $equation);
								$csv_element = $helpers_instance->evalmath($equation);
								}
								$wp_element= trim($key);
								if(!empty($csv_element) && !empty($wp_element)){
								//$post_values[$wp_element] = $csv_element;
								if (in_array('advanced-custom-fields-pro/acf.php', $mapping_instance->get_active_plugins())) {
								$this->acfpro_import_function($wp_element , $csv_element ,$type, $post_id);
								} else {
								if (in_array('advanced-custom-fields/acf.php', $mapping_instance->get_active_plugins())) {
								$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
								if(is_dir($acf_pluginPath)) {
								$this->acfpro_import_function($wp_element , $csv_element ,$type, $post_id);
								}
								else
								$acf_instance->acf_import_function($wp_element , $csv_element ,$type, $post_id);
								}
								}
								}		
								//}
								///}
	}

	elseif(!in_array($csv_value , $header_array)){
		$wp_element= trim($key);
		if (in_array('advanced-custom-fields-pro/acf.php', $mapping_instance->get_active_plugins())) {
			$this->acfpro_import_function($wp_element , $csv_value ,$type, $post_id);

		} else {
			if (in_array('advanced-custom-fields/acf.php', $mapping_instance->get_active_plugins())) {
				$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
				if(is_dir($acf_pluginPath)) {
					$this->acfpro_import_function($wp_element , $csv_value ,$type, $post_id);
				}
				else
					$acf_instance->acf_import_function($wp_element , $csv_value ,$type, $post_id);
			}
		}
	}


	else{

		$get_key= array_search($csv_value , $header_array);
		if(isset($value_array[$get_key])){
			$csv_element = $value_array[$get_key];	
			//}
		$wp_element= trim($key);

		if(!empty($csv_element) && !empty($wp_element)){
			//$post_values[$wp_element] = $csv_element;
			if (in_array('advanced-custom-fields-pro/acf.php', $mapping_instance->get_active_plugins())) {
				$this->acfpro_import_function($wp_element , $csv_element ,$type, $post_id);

			} else {
				if (in_array('advanced-custom-fields/acf.php', $mapping_instance->get_active_plugins())) {
					$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
					if(is_dir($acf_pluginPath)) {
						$this->acfpro_import_function($wp_element , $csv_element ,$type, $post_id);
					}
					else
						$acf_instance->acf_import_function($wp_element , $csv_element ,$type, $post_id);
				}
			}
		}
	}
}
}
}

} 

public function set_acf_rf_values($header_array ,$value_array , $map, $post_id , $type){
	$post_values = [];
	$mapping_instance = MappingExtension::getInstance();
	$helpers_instance = ImportHelpers::getInstance();

	$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);		

	if(in_array('advanced-custom-fields/acf.php',$mapping_instance->get_active_plugins())) {
		$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
		if(is_dir($acf_pluginPath)) {
			$this->acfpro_repeater_import_fuction($post_values,$type, $post_id);
		}
	}
	if (in_array('advanced-custom-fields-pro/acf.php', $mapping_instance->get_active_plugins())) {
		$this->acfpro_repeater_import_fuction($post_values,$type, $post_id);
	} else if (in_array('acf-repeater/acf-repeater.php', $mapping_instance->get_active_plugins())) {
		//$this->acfpro_repeater_import_fuction($post_values,$type, $post_id);
	}
}

public function set_acf_gf_values($header_array ,$value_array , $map, $post_id , $type){
	$post_values = [];
	$mapping_instance = MappingExtension::getInstance();
	$helpers_instance = ImportHelpers::getInstance();

	$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);

	if((in_array('advanced-custom-fields/acf.php',$mapping_instance->get_active_plugins())) || (in_array('advanced-custom-fields-pro/acf.php', $mapping_instance->get_active_plugins()))) { 
		$this->acfpro_group_import_fuction($post_values,$type, $post_id);
	} 
}

function acfpro_import_function($acf_wpname_element ,$acf_csv_element , $importAs , $post_id){
	$acf_wp_name = $acf_wpname_element;
	$acf_csv_name = $acf_csv_element;
	global $wpdb;
	$helpers_instance = ImportHelpers::getInstance();

	$get_acf_fields = $wpdb->get_results($wpdb->prepare("select post_content, post_name from {$wpdb->prefix}posts where post_type = %s and post_excerpt = %s", 'acf-field', $acf_wp_name ), ARRAY_A);
	foreach($get_acf_fields as $keys => $value_type){
		$get_type_field = unserialize($value_type['post_content']);	
		$field_type = $get_type_field['type'];
		$key = $get_acf_fields[0]['post_name'];
		$return_format = $get_type_field['return_format'];
		if($field_type == 'text' || $field_type == 'textarea' || $field_type == 'number' || $field_type == 'email' || $field_type == 'url' || $field_type == 'password' || $field_type == 'range' || $field_type == 'radio' || $field_type == 'true_false' || $field_type == 'time_picker' || $field_type == 'color_picker' || $field_type == 'button_group' || $field_type == 'oembed' || $field_type == 'wysiwyg'){
			$map_acf_wp_element = $acf_wp_name;
			$map_acf_csv_element = $acf_csv_name;	
		}
		if($field_type == 'date_time_picker'){
			
			$dt_var = trim($acf_csv_name);

			$date_time_of = date("Y-m-d H:i:s", strtotime($dt_var) );
			// $date_obj = new \DateTime();	
			// $date_time_of = $date_obj->createFromFormat('d-m-Y H:i', "$dt_var");	
			// if(!$date_time_of){
			// 	return;
			// }
			// else{	
			// 	$date_time_of = $date_time_of->format('Y-M-D h:i:s'); 
			// }
			
			$map_acf_csv_element = $date_time_of;
			$map_acf_wp_element = $acf_wp_name;
		}
		if ($field_type == 'google_map') {
			$location = trim($acf_csv_name);
			$map = array(
					'address' => $location,
					'lat'     => '-37.8021917',
					'lng'     => '144.96398'
				    );
			$map_acf_csv_element = $map;
			$map_acf_wp_element = $acf_wp_name;
		}
		if($field_type == 'date_picker'){

			$var = trim($acf_csv_name);
			$date = str_replace('/', '-', "$var");
			$date_of = date('Ymd', strtotime($date));

			$map_acf_csv_element = $date_of;
			$map_acf_wp_element = $acf_wp_name;

		}
		if($field_type == 'post_object' || $field_type == 'page_link' || $field_type == 'user' || $field_type == 'select'){
			
			if($get_type_field['multiple'] == 0){	
				$maps_acf_csv_name = $acf_csv_name;	
			}else{	
				$explo_acf_csv_name = explode(',',trim($acf_csv_name));	
				$maps_acf_csv_name = array();
				foreach($explo_acf_csv_name as $explo_csv_value){
					$maps_acf_csv_name[] = trim($explo_csv_value);
				}
			}
			$map_acf_csv_element = $maps_acf_csv_name;
			$map_acf_wp_element = $acf_wp_name;
		}
		if($field_type == 'relationship' || $field_type == 'taxonomy'){
			$relations = array();
			$check_is_valid_term = null;
			$get_relations = $acf_csv_name;
			if(!empty($get_relations)){
				$exploded_relations = explode(',', $get_relations);
				foreach ($exploded_relations as $relVal) {
					$relationTerm = trim($relVal);
					if ($field_type == 'taxonomy') {
						$check_is_valid_term = $helpers_instance->get_requested_term_details($post_id, $relationTerm);
						$relations[] = $check_is_valid_term;
					} else {
						$reldata = strlen($relationTerm);
						$checkrelid = intval($relationTerm);
						$verifiedRelLen = strlen($checkrelid);
						if ($reldata == $verifiedRelLen) {
							$relations[] = $relationTerm;
						} else {
							$relation_id = $wpdb->get_col($wpdb->prepare("select id from {$wpdb->prefix}posts where post_title = %s",$relVal));
							if (!empty($relation_id)) {
								$relations[] = $relation_id[0];
							}
						}
					}
				}
			}

			$map_acf_csv_element = $relations;
			$map_acf_wp_element = $acf_wp_name;
		}		

		if($field_type == 'checkbox'){

			$explode_acf_csv = explode(',',trim($acf_csv_name));	
			$explode_acf_csv_name = [];
			foreach($explode_acf_csv as $explode_acf_csv_value){
				$explode_acf_csv_name[] = trim($explode_acf_csv_value);
			}	

			$map_acf_csv_element = $explode_acf_csv_name;
			$map_acf_wp_element = $acf_wp_name;
		}

		if($field_type == 'link'){

			$serial_acf_csv = explode(',', $acf_csv_name);
			$serial_acf_csv_name = [];
			foreach($serial_acf_csv as $serial_acf_csv_value){
				$serial_acf_csv_name[] = trim($serial_acf_csv_value);
			}	
			$serial_acf_csv_names['url'] = $serial_acf_csv_name[0];
			$serial_acf_csv_names['title'] = $serial_acf_csv_name[1];
			if($serial_acf_csv_name[2] == 1){
				$serial_acf_csv_names['target'] = '_blank';
			}else{
				$serial_acf_csv_names['target'] = '';
			}
			$map_acf_csv_element = $serial_acf_csv_names;
			$map_acf_wp_element = $acf_wp_name;
		}
		if ($field_type == 'message') {
			$get_type_field['message'] = $acf_csv_name;
		}
		elseif ($field_type == 'image') {
			if ($return_format == 'url' || $return_format == 'array') {
				$ext = pathinfo($acf_csv_name, PATHINFO_EXTENSION);
				if($ext== 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext = 'gif') {
					$img_id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where guid = %s AND post_type='attachment'",$acf_csv_name));
					if(!empty($img_id)) {
						$map_acf_csv_element=$img_id[0];
					}
					else {
						$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
					}
				}
				else {
					$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
				}
			}
			else {
				$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
			}
			$map_acf_wp_element = $acf_wp_name;
		}
		elseif ($field_type == 'file') {
			if ($return_format == 'url' || $return_format == 'array') {
				$ext = pathinfo($acf_csv_name, PATHINFO_EXTENSION);
				if($ext=='pdf' || $ext=='mp3' || $ext == $ext ){
					$pdf_id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where guid = %s AND post_type='attachment'",$acf_csv_name));
					if(!empty($pdf_id)) {
						$map_acf_csv_element=$pdf_id[0];
					}
					else {
						$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
					}
				}
				else {
					$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
				}
			}
			else {
				$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
			}
			$map_acf_wp_element = $acf_wp_name;
		}
		elseif ($field_type == 'gallery') {#TODO gallery fields
			$gallery_ids =array();
			//$exploded_gallery_items = explode(',', $acf_csv_name);
			$exploded_gallery_items = explode(',', $acf_csv_name);
			
			foreach($exploded_gallery_items as $gallery) {
				$gallery = trim($gallery);
				if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery,$matched_gallerylist,PREG_PATTERN_ORDER)){
					$ext = pathinfo($gallery, PATHINFO_EXTENSION);
					if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
						$img_id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where guid = %s AND post_type='attachment'",$gallery));
						if(!empty($img_id)) {
							$gallery_ids[] = $img_id[0];
						}else{
							$get_gallery_id = ACFProImport::$media_instance->media_handling( $gallery, $post_id );
							if($get_gallery_id != '') {
								$gallery_ids[] = $get_gallery_id;
							}
						}
					}else {
						$get_gallery_id = ACFProImport::$media_instance->media_handling($gallery, $post_id);
						if($get_gallery_id != '') {
							$gallery_ids[] = $get_gallery_id;
						}
					}
				} else {
					$galleryLen = strlen($gallery);
					$checkgalleryid = intval($gallery);
					$verifiedGalleryLen = strlen($checkgalleryid);
					if($galleryLen == $verifiedGalleryLen) {
						$gallery_ids[] = $gallery;
					}
				}
			}
			$map_acf_csv_element = $gallery_ids;
		}
		$map_acf_wp_element = $acf_wp_name;
	}

	if ($importAs == 'Users') {
		update_user_meta($post_id, $map_acf_wp_element, $map_acf_csv_element);
		update_user_meta($post_id, '_' . $map_acf_wp_element, $key);
	} else {
		//update_post_meta($pID, $data_array['groupfield_slug'].'_'.$value, $data_array[$value]);
		update_post_meta($post_id, $map_acf_wp_element, $map_acf_csv_element);
		update_post_meta($post_id, '_' . $map_acf_wp_element, $key);
	}

	$listTaxonomy = get_taxonomies();

	if (in_array($importAs, $listTaxonomy)) {
		if($term_meta = 'yes'){
			update_term_meta($post_id, $map_acf_wp_element, $map_acf_csv_element);
			update_term_meta($post_id, '_' . $map_acf_wp_element, $key);
		}else{
			$option_name = $importAs . "_" . $post_id . "_" . $map_acf_wp_element;
			$option_value = $map_acf_csv_element;
			if (is_array($option_value)) {
				$option_value = serialize($option_value);
			}

			update_option("$option_name", "$option_value");
		}
	}
}

function acfpro_group_import_fuction($data_array, $importAs, $pID) { 

	global $wpdb;
	$helpers_instance = ImportHelpers::getInstance();
	$grpid = '';
	$createdFields = $grp_parent_fields = $group_fields = $group_flexible_content_import_method = array();
	foreach($data_array as $grpKey => $grpVal) {
		$i = 0;

		// Prepare the meta array by field type
		$get_field_info  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from {$wpdb->prefix}posts where post_excerpt = %s", $grpKey ) );

		$field_info = unserialize( $get_field_info[0]->post_content );

		$group_fields[$grpKey] = $get_field_info[0]->post_name;

		if(isset($field_info['type']) && $field_info['type'] == 'flexible_content') {
			$group_flexible_content_import_method[ $get_field_info[0]->post_name ] = $field_info['layouts'][0]['name'];
		} elseif(isset($field_info['type']) && ($field_info['type'] == 'image' || $field_info['type'] == 'file')) {
			if($field_info['type'] == 'image') {
				$group_image_import_method[ $get_field_info[0]->post_name ] = $field_info['return_format'];
			} else {
				$group_file_import_method[ $get_field_info[0]->post_name ] = $field_info['return_format'];
			}
		} else {
			$group_sub_field_type[ $get_field_info[0]->post_name ] = $field_info['type'];
		}

		$group_field_rows = explode('|', $grpVal);

		$j = 0;
		foreach($group_field_rows as $index => $value) {
			$group_field_values = explode('->', $value);

			$checkCount = count($group_field_values);

			foreach($group_field_values as $key => $val) {
				if($checkCount > 1){
					$grp_field_meta_key = $this->getMetaKeyOfGroupField( $pID, $grpKey, $index, $key );
				}
				else{
					$grp_field_meta_key = $this->getMetaKeyOfGroupField( $pID, $grpKey, $i, $j );
				}

				if($grp_field_meta_key[0] == '_')
					$grp_field_meta_key = substr($grp_field_meta_key, 1);
				$grp_field_parent_key = explode( '_' . $grpKey, $grp_field_meta_key );
				$grp_field_parent_key = substr( $grp_field_parent_key[0], 0, - 2 );
				if (substr($grp_field_parent_key, -1) == "_") {
					$grp_field_parent_key = substr($grp_field_parent_key, 0, -1);
				}
				$super_parent = explode('_'.$index.'_',$grp_field_parent_key);
				$grp_parent_fields[$super_parent[0]] = count($group_field_rows);
				if($checkCount > 1)
					$grp_parent_fields[$grp_field_parent_key] = $key + 1;
				else
					$grp_parent_fields[$grp_field_parent_key] = $i + 1;
				$j++;


				$group_type = $group_sub_field_type[$group_fields[$grpKey]];

				if($group_type == 'user' || $group_type == 'page_link' || $group_type == 'post_object' || $group_type == 'select') {

					if($field_info['multiple'] == 0){
						$acf_group_field_value = trim($val);
					}else{

						$acf_group_value_exp = explode(',',trim($val));
						$acf_group_field_value = array();
						foreach($acf_group_value_exp as $acf_grp_value){
							$acf_group_field_value[] = trim($acf_grp_value);
						}
						//$acf_group_field_value = $acf_group_value_exp;
					}
					$acf_grp_field_info[$grp_field_meta_key] = $acf_group_field_value;	
					$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
				}

				if($group_type == 'text' || $group_type == 'textarea' || $group_type == 'email' || $group_type == 'number' || $group_type == 'url' || $group_type == 'password' || $group_type == 'range' || $group_type == 'radio' || $group_type == 'true_false' || $group_type == 'time_picker' || $group_type == 'color_picker' || $group_type == 'button_group' || $group_type == 'oembed' || $group_type == 'wysiwyg'){

					$acf_grp_field_info[$grp_field_meta_key] = trim($val);
					$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];	
				}
				if($group_type == 'date_time_picker'){

					$dt_group_var = trim($val);

					$date_time_group_of = date("Y-m-d H:i:s", strtotime($dt_group_var) );
					// $date_time_group_of = \DateTime::createFromFormat('d/m/Y H:i', "$dt_group_var");
					// if(!$date_time_group_of){
					// 	return;
					// }
					// else{	
					// 	$date_time_group_of = $date_time_group_of->format('Y-m-d h:i:s'); 
					// }

					$acf_grp_field_info[$grp_field_meta_key] = $date_time_group_of;	
					$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
				}
				if ($group_type == 'google_map') {
					$location = trim($val);
					$map = array(
							'address' => $location,
							'lat'     => '-37.8021917',
							'lng'     => '144.96398'
						    );
					$acf_grp_field_info[$grp_field_meta_key] = $map;
					$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
				}
				if($group_type == 'date_picker'){

					$var_group = trim($val);
					$date_group = str_replace('/', '-', "$var_group");
					$date_group_of = date('Ymd', strtotime($date_group));

					$acf_grp_field_info[$grp_field_meta_key]  = $date_group_of;
					$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
				}
				if($group_type == 'link'){
					$serial_acf_csv_group = explode(',',$val);
					$serial_acf_csv_group_name = [];
					foreach($serial_acf_csv_group as $serial_acf_csv_group_value){
						$serial_acf_csv_group_name[] = trim($serial_acf_csv_group_value);
					}	
					$serial_acf_csv_group_names['url'] = $serial_acf_csv_group_name[0];
					$serial_acf_csv_group_names['title'] = $serial_acf_csv_group_name[1];
					if($serial_acf_csv_group_name[2] == 1){
						$serial_acf_csv_group_names['target'] = '_blank';
					}else{
						$serial_acf_csv_group_names['target'] = '';
					}
					$acf_grp_field_info[$grp_field_meta_key] = $serial_acf_csv_group_names;
					$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
				}

				if($group_type == 'checkbox'){
					$explo_acf_val = explode(',',trim($val));	
					$explo_acf_val_name = [];
					foreach($explo_acf_val as $explode_acf_csv_value){
						$explo_acf_val_name[] = trim($explode_acf_csv_value);
					}

					$acf_grp_field_info[$grp_field_meta_key] = $explo_acf_val_name;
					$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];

				}
				if($group_type == 'gallery'){
					$gallery_ids = array();
					if ( is_array( $gallery_ids ) ) {
						unset( $gallery_ids );
						$gallery_ids = array();
					}
					// $exploded_gallery_items = explode( ',', $val );
					$exploded_gallery_items = explode( ',', $val );
					foreach ( $exploded_gallery_items as $gallery ) {
						$gallery = trim( $gallery );
						if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
							$get_gallery_id = ACFProImport::$media_instance->media_handling( $gallery, $pID);	
							if ( $get_gallery_id != '' ) {
								$gallery_ids[] = $get_gallery_id;
							}
						} else {
							$galleryLen         = strlen( $gallery );
							$checkgalleryid     = intval( $gallery );
							$verifiedGalleryLen = strlen( $checkgalleryid );
							if ( $galleryLen == $verifiedGalleryLen ) {
								$gallery_ids[] = $gallery;
							}
						}
					}
					$acf_grp_field_info[$grp_field_meta_key] = $gallery_ids;
					$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
				}
				elseif($group_type == 'relationship' || $group_type == 'taxonomy') {
					$exploded_relations = $relations = array();
					$exploded_relations = explode(',', $val);
					foreach($exploded_relations as $relVal) {
						$relationTerm = trim( $relVal );
						if ( $group_type == 'taxonomy' ) {
							$taxonomy_name       = substr( $grpKey, 4 );
							$check_is_valid_term = $helpers_instance->get_requested_term_details( $pID, $relationTerm, $taxonomy_name );
							$relations[]         = $check_is_valid_term;
						} else {
							$relations[] = $relationTerm;
						}
					}
					$acf_grp_field_info[$grp_field_meta_key] = $relations;
					$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
				} 
				if($group_image_import_method[$group_fields[$grpKey]] == 'url' || $group_image_import_method[$group_fields[$grpKey]] == 'array' ) {
					$image_link = trim($val);
					if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link)){
						$acf_grp_field_info[$grp_field_meta_key] = ACFProImport::$media_instance->media_handling($image_link, $pID);
						$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
					} else {
						$acf_grp_field_info[$grp_field_meta_key] = $image_link;
						$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
					}
				}
				if($group_file_import_method[$group_fields[$grpKey]] == 'url' || $group_file_import_method[$group_fields[$grpKey]] == 'array' ) {
					$image_link = trim($val);
					if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link)){
						$ext = pathinfo($image_link, PATHINFO_EXTENSION);
						if($ext== 'pdf' || $ext == 'mp3' || $ext == $ext) {
							$fil_id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where guid = %s AND post_type='attachment'",$image_link));
							if(!empty($fil_id)) {
								$acf_grp_field_info[$grp_field_meta_key]=$fil_id[0];
							}else {
								$acf_grp_field_info[$grp_field_meta_key] = ACFProImport::$media_instance->media_handling( $image_link, $pID );
							}
							$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
						} else {
							$acf_grp_field_info[$grp_field_meta_key] = ACFProImport::$media_instance->media_handling( $image_link, $pID );
							$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
						}
					} else {
						$acf_grp_field_info[$grp_field_meta_key] = $image_link;
						$acf_grp_field_info['_'.$grp_field_meta_key] = $group_fields[$grpKey];
					}
				}  
				if ($group_type == 'message') {
					$field_info['message'] = $val;
					// $get_acf_field = serialize($get_type_field);
					// $updt_query = "update {$wpdb->prefix}posts set post_content ='$get_acf_field' where post_name = '$value'";
					// $wpdb->query($updt_query);
				}

			}
			$i++;
		}

		if(!empty($acf_grp_field_info)) {

			foreach($acf_grp_field_info as $fName => $fVal) {
				
				$listTaxonomy = get_taxonomies();
				if (in_array($importAs, $listTaxonomy)) {
					if($term_meta = 'yes'){
						update_term_meta($pID, $fName, $fVal);
					}else{
						$option_name = $importAs . "_" . $pID . "_" . $fName;
						$option_value = $fVal;
						if (is_array($option_value)) {
							$option_value = serialize($option_value);
						}
						update_option("$option_name", "$option_value");
					}
				}
				else{
					if($importAs == 'Users'){
						update_user_meta($pID, $fName, $fVal);
					}else{
						update_post_meta($pID, $fName, $fVal);
					}
				}		
			}
		}

		$createdFields[] = $grpKey;
		$grp_fname = $grpKey;
		$grp_fID   = $group_fields[$grpKey];
		// Flexible Content
		$flexible_content = array();
		$listTaxonomy = get_taxonomies();
		if ( array_key_exists( $grp_fID, $group_flexible_content_import_method ) && $group_flexible_content_import_method[ $grp_fID ] != null ) {
			$flexible_content[] = $group_flexible_content_import_method[ $grp_fID ];
			if($importAs == 'Users'){
				update_user_meta($pID, $grp_fname, $flexible_content);
			}
			elseif(in_array($importAs , $listTaxonomy)){
				update_term_meta($pID, $grp_fname, $flexible_content);
			}else{
				update_post_meta($pID, $grp_fname, $flexible_content);
			}	
		}
	}
	foreach($grp_parent_fields as $pKey => $pVal) {
		$listTaxonomy = get_taxonomies();
		if (in_array($importAs, $listTaxonomy)) {
			if($term_meta = 'yes'){
				update_term_meta($pID, $pKey, $pVal);
			}else{
				$option_name = $importAs . "_" . $pID . "_" . $pKey;
				$option_value = $pVal;
				if (is_array($option_value)) {
					$option_value = serialize($option_value);
				}
				update_option("$option_name", "$option_value");
			}
		}
		else{
			if($importAs == 'Users'){
				update_user_meta($pID, $pKey, $pVal);
			}else{
				update_post_meta($pID, $pKey, $pVal);
			}
		}		
	}
}

function getMetaKeyOfGroupField($pID, $field_name, $meta_key = '') {
	global $wpdb;
	$get_field_details  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent, post_excerpt from {$wpdb->prefix}posts where post_excerpt = %s", $field_name ) );

	$get_group_parent_field = $wpdb->get_results( $wpdb->prepare( "select post_content, post_name, post_excerpt, post_parent from {$wpdb->prefix}posts where ID = %d", $get_field_details[0]->post_parent ));
	$field_info = unserialize( $get_group_parent_field[0]->post_content );

	$get_group_super_parent_field = $wpdb->get_results( $wpdb->prepare( "select post_content, post_name, post_excerpt, post_parent from {$wpdb->prefix}posts where ID = %d", $get_group_parent_field[0]->post_parent ));
	$parent_field_info = unserialize( $get_group_super_parent_field[0]->post_content );

	if(empty($parents) && $field_name == $get_field_details[0]->post_excerpt) {	
		$meta_key =  $field_name ;	
	}

	if($get_group_parent_field[0]->post_parent != 0 && isset($field_info['type']) && $field_info['type'] == 'group'  ) {  			
		$meta_key =  $get_group_parent_field[0]->post_excerpt . '_' . $meta_key;	
	} 

	if($get_group_super_parent_field[0]->post_parent != 0 && isset($parent_field_info['type']) && $parent_field_info['type'] == 'group'){
		$meta_key =  $get_group_super_parent_field[0]->post_excerpt . '_' . $meta_key;
	}
	return $meta_key;
}

function acfpro_repeater_import_fuction($data_array, $importAs, $pID) {
	global $wpdb;
	$helpers_instance = ImportHelpers::getInstance();
	$grpid = '';
	$createdFields = $rep_parent_fields = $repeater_fields = $repeater_flexible_content_import_method = array();
	$parent_key_values = [];
	$child_key_values = [];
	foreach($data_array as $repKey => $repVal) {
		$i = 0;

		// Prepare the meta array by field type
		$get_field_info  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from {$wpdb->prefix}posts where post_excerpt = %s", $repKey ) );
		$field_info = unserialize( $get_field_info[0]->post_content );
		$repeater_fields[$repKey] = $get_field_info[0]->post_name;
		if(isset($field_info['type']) && $field_info['type'] == 'flexible_content') {
			$repeater_flexible_content_import_method[ $get_field_info[0]->post_name ] = $field_info['layouts'][0]['name'];
		} elseif(isset($field_info['type']) && ($field_info['type'] == 'image' || $field_info['type'] == 'file')) {
			if($field_info['type'] == 'image') {
				$repeater_image_import_method[ $get_field_info[0]->post_name ] = $field_info['return_format'];
			} else {
				$repeater_file_import_method[ $get_field_info[0]->post_name ] = $field_info['return_format'];
			}
		} else {
			$repeater_sub_field_type[ $get_field_info[0]->post_name ] = $field_info['type'];
		}

		// Parse values if have any multiple values
		$repeater_field_rows = explode('|', $repVal);
		$j = 0;
		foreach($repeater_field_rows as $index => $value) {
			$repeater_field_values = explode('->', $value);
			$checkCount = count($repeater_field_values);
			foreach($repeater_field_values as $key => $val) {
				if($checkCount > 1){
					$rep_field_meta_key = $this->getMetaKeyOfRepeaterField( $pID, $repKey, $index, $key );
				}else{
					$rep_field_meta_key = $this->getMetaKeyOfRepeaterField( $pID, $repKey, $i, $j );
				}
				if($rep_field_meta_key[0] == '_')
					$rep_field_meta_key = substr($rep_field_meta_key, 1);
				$rep_field_parent_key = explode( '_' . $repKey, $rep_field_meta_key );
				$rep_field_parent_key = substr( $rep_field_parent_key[0], 0, - 2 );
				if (substr($rep_field_parent_key, -1) == "_") {
					$rep_field_parent_key = substr($rep_field_parent_key, 0, -1);
				}
				$super_parent = explode('_'.$index.'_',$rep_field_parent_key);
				// $rep_parent_fields[$super_parent[0]] = count($repeater_field_rows);
			
				// if($checkCount > 1)
				// 	$rep_parent_fields[$rep_field_parent_key] = $key + 1;
				// else
				// 	$rep_parent_fields[$rep_field_parent_key] = $i + 1;
				// $j++;

				$parent_key_values[] = count($repeater_field_rows);
				$rep_parent_fields[$super_parent[0]] = max($parent_key_values);
			
				if($checkCount > 1){
					$child_key_values[] = $key + 1;
					$rep_parent_fields[$rep_field_parent_key] = max($child_key_values); 
				}else{
					$child_key_values[] = $i + 1;
					$rep_parent_fields[$rep_field_parent_key] = max($child_key_values);
				}
				$j++;


				$rep_type = $repeater_sub_field_type[$repeater_fields[$repKey]];

				if($rep_type == 'user' || $rep_type == 'page_link' || $rep_type == 'post_object' || $rep_type == 'select') {

					if($field_info['multiple'] == 0){	
						$acf_rep_field_value = trim($val);
					}else{

						$acf_rep_value_exp = explode(',',trim($val));
						$acf_rep_field_value = array();
						foreach($acf_rep_value_exp as $acf_reps_value){
							$acf_rep_field_value[] = trim($acf_reps_value);
						}
					}
					$acf_rep_field_info[$rep_field_meta_key] = $acf_rep_field_value;	
					$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
				}

				if($rep_type == 'text' || $rep_type == 'textarea' || $rep_type == 'email' || $rep_type == 'number' || $rep_type == 'url' || $rep_type == 'password' || $rep_type == 'range' || $rep_type == 'radio' || $rep_type == 'true_false' || $rep_type == 'time_picker' || $rep_type == 'color_picker' || $rep_type == 'button_group' || $rep_type == 'oembed' || $rep_type == 'wysiwyg'){

					$acf_rep_field_info[$rep_field_meta_key] = trim($val);
					$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];	
				}
				if($rep_type == 'date_time_picker'){

					$dt_rep_var = trim($val);

					$date_time_rep_of = date("Y-m-d H:i:s", strtotime($dt_rep_var) );
					// $date_time_rep_of = \DateTime::createFromFormat('d/m/Y H:i', "$dt_rep_var");
					// if(!$date_time_rep_of){
					// 	return;
					// }
					// else{	
					// 	$date_time_rep_of = $date_time_rep_of->format('Y-m-d h:i:s'); 	
					// }

					$acf_rep_field_info[$rep_field_meta_key] = $date_time_rep_of;	
					$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
				}
				if ($rep_type == 'google_map') {
					$location = trim($val);
					$map = array(
							'address' => $location,
							'lat'     => '-37.8021917',
							'lng'     => '144.96398'
						    );
					$acf_rep_field_info[$rep_field_meta_key] = $map;
					$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
				}
				if($rep_type == 'date_picker'){

					$var_rep = trim($val);
					$date_rep = str_replace('/', '-', "$var_rep");
					$date_rep_of = date('Ymd', strtotime($date_rep));

					$acf_rep_field_info[$rep_field_meta_key] = $date_rep_of;
					$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
				}
				if($rep_type == 'checkbox'){

					$explode_val = explode(',',trim($val));	
					$explode_val_name = [];
					foreach($explode_val as $explode_acf_csv_value){
						$explode_val_name[] = trim($explode_acf_csv_value);
					}

					$acf_rep_field_info[$rep_field_meta_key] = $explode_val_name;
					$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
				}
				// if($repeater_sub_field_type[$repeater_fields[$repKey]] == 'checkbox') {
				// 	$acf_rep_field_info[$rep_field_meta_key] = explode(',', trim($val));
				// 	$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
				// } 
				if($rep_type == 'gallery'){
					$gallery_ids = array();
					if ( is_array( $gallery_ids ) ) {
						unset( $gallery_ids );
						$gallery_ids = array();
					}
					// $exploded_gallery_items = explode( ',', $val );
					$exploded_gallery_items = explode( ',', $val );
					foreach ( $exploded_gallery_items as $gallery ) {
						$gallery = trim( $gallery );
						if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
							$get_gallery_id = ACFProImport::$media_instance->media_handling( $gallery, $pID);	
							if ( $get_gallery_id != '' ) {
								$gallery_ids[] = $get_gallery_id;
							}
						} else {
							$galleryLen         = strlen( $gallery );
							$checkgalleryid     = intval( $gallery );
							$verifiedGalleryLen = strlen( $checkgalleryid );
							if ( $galleryLen == $verifiedGalleryLen ) {
								$gallery_ids[] = $gallery;
							}
						}
					}
					$acf_rep_field_info[$rep_field_meta_key] = $gallery_ids;
					$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
				}
				if($rep_type == 'link'){
					$explode_acf_val = explode(',',$val);
					$serial_acf_val = [];
					foreach($explode_acf_val as $explode_acf_value){
						$serial_acf_val[] = trim($explode_acf_value);
					}	
					
					$serial_acf_value['url'] = $serial_acf_val[0];
					$serial_acf_value['title'] = $serial_acf_val[1];
					if($serial_acf_val[2] == 1){
						$serial_acf_value['target'] = '_blank';
					}else{
						$serial_acf_value['target'] = '';
					}
				
					$acf_rep_field_info[$rep_field_meta_key] = $serial_acf_value;
					$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
				}

				//Push meta information into WordPress

				elseif($rep_type == 'relationship' || $rep_type == 'taxonomy') {
					$exploded_relations = $relations = array();
					$exploded_relations = explode(',', $val);
					foreach($exploded_relations as $relVal) {
						$relationTerm = trim( $relVal );
						if ( $rep_type == 'taxonomy' ) {
							$taxonomy_name       = substr( $repKey, 4 );
							$check_is_valid_term = $helpers_instance->get_requested_term_details( $pID, $relationTerm, $taxonomy_name );
							$relations[]         = $check_is_valid_term;
						} else {
							$relations[] = $relationTerm;
						}
					}
					$acf_rep_field_info[$rep_field_meta_key] = $relations;
					$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
				} 


				if($repeater_image_import_method[$repeater_fields[$repKey]] == 'url' || $repeater_image_import_method[$repeater_fields[$repKey]] == 'array' ) {
					$image_link = trim($val);
					if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link)){
						$acf_rep_field_info[$rep_field_meta_key] = ACFProImport::$media_instance->media_handling($image_link, $pID);
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					} else {
						$acf_rep_field_info[$rep_field_meta_key] = $image_link;
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					}
				}
				if($repeater_file_import_method[$repeater_fields[$repKey]] == 'url' || $repeater_file_import_method[$repeater_fields[$repKey]] == 'array' ) {
					$image_link = trim($val);
					if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link)){
						$ext = pathinfo($image_link, PATHINFO_EXTENSION);
						if($ext== 'pdf' || $ext == 'mp3' || $ext == $ext) {
							$fil_id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where guid = %s AND post_type='attachment'",$image_link));
							if(!empty($fil_id)) {
								$acf_rep_field_info[$rep_field_meta_key]=$fil_id[0];
							}else {
								$acf_rep_field_info[$rep_field_meta_key] = ACFProImport::$media_instance->media_handling( $image_link, $pID );
							}
							$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
						} else {
							$acf_rep_field_info[$rep_field_meta_key] = ACFProImport::$media_instance->media_handling( $image_link, $pID );
							$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
						}
					} else {
						$acf_rep_field_info[$rep_field_meta_key] = $image_link;
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					}
				}  
				/*else {
				  $acf_rep_field_info[$rep_field_meta_key] = trim($val);
				  $acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
				  }*/

				if ($rep_type == 'message') {
					$field_info['message'] = $val;
					// $get_acf_field = serialize($get_type_field);
					// $updt_query = "update $wpdb->posts set post_content ='$get_acf_field' where post_name = '$value'";
					// $wpdb->query($updt_query);
				}
			}
			$i++;
		}
		if(!empty($acf_rep_field_info)) {
			foreach($acf_rep_field_info as $fName => $fVal) {

				$listTaxonomy = get_taxonomies();
				if (in_array($importAs, $listTaxonomy)) {
					if($term_meta = 'yes'){
						update_term_meta($pID, $fName, $fVal);
					}else{
						$option_name = $importAs . "_" . $pID . "_" . $fName;
						$option_value = $fVal;
						if (is_array($option_value)) {
							$option_value = serialize($option_value);
						}
						update_option("$option_name", "$option_value");
					}
				}
				else{
					if($importAs == 'Users'){
						update_user_meta($pID, $fName, $fVal);
					}else{
						update_post_meta($pID, $fName, $fVal);
					}
				}

			}
		}

		$createdFields[] = $repKey;
		$rep_fname = $repKey;
		$rep_fID   = $repeater_fields[$repKey];
		// Flexible Content
		$flexible_content = array();
		if ( array_key_exists( $rep_fID, $repeater_flexible_content_import_method ) && $repeater_flexible_content_import_method[ $rep_fID ] != null ) {
			$flexible_content[] = $repeater_flexible_content_import_method[ $rep_fID ];
			$listTaxonomy = get_taxonomies();

			if($importAs == 'Users'){
				update_user_meta($pID, $rep_fname, $flexible_content);
			}
			elseif(in_array($importAs, $listTaxonomy)){
				if($term_meta = 'yes'){	
					update_term_meta($pID, $rep_fname, $flexible_content);
				}
			}
			else{
				update_post_meta($pID, $rep_fname, $flexible_content);
			}	
		}
	}

	foreach($rep_parent_fields as $pKey => $pVal) {
		
		$get_cust  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from {$wpdb->prefix}posts where post_excerpt = %s", $pKey),ARRAY_A);
		foreach ($get_cust as $get_val ) {
			$custvalue = $get_val['post_content'];
			$post_content = unserialize($custvalue);
			$field_type = $post_content['type'];
			$custkey='_'.$pKey; 
		}
		$listTaxonomy = get_taxonomies();
		if (in_array($importAs, $listTaxonomy)) {
			if($term_meta = 'yes'){
				update_term_meta($pID, $pKey, $pVal);
			}else{
				$option_name = $importAs . "_" . $pID . "_" . $pKey;
				$option_value = $pVal;
				if (is_array($option_value)) {
					$option_value = serialize($option_value);
				}
				update_option("$option_name", "$option_value");
			}
		}
		elseif($field_type == 'flexible_content'){
			$flexible_group = explode('|',$data_array[$pKey]);
			foreach ($repeater_field_rows as $repKey => $repVal) {
				foreach($flexible_group as $flexi_group_value){
					$flex_value[$repKey] = $flexi_group_value;		
				}		
			}		
			if($importAs == 'Users'){
				update_user_meta($pID, $pKey, $flex_value);
			}elseif(in_array($importAs, $listTaxonomy)){
				if($term_meta = 'yes'){
					update_term_meta($pID, $pKey, $flex_value);
				}
			}else{
				update_post_meta($pID, $pKey, $flex_value);
			}	
		}
		else{
			if($importAs == 'Users'){
				update_user_meta($pID, $pKey, $pVal);
			}else{
				update_post_meta($pID, $pKey, $pVal);
			}
		}		
	}
}

function getMetaKeyOfRepeaterField($pID, $field_name, $key = 0, $fKey = 0, $parents = array(), $meta_key = '') {
	global $wpdb;
	$get_field_details  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent, post_excerpt from {$wpdb->prefix}posts where post_excerpt = %s", $field_name ) );
	if(empty($parents) && $field_name == $get_field_details[0]->post_excerpt) {
		$parents[] = $fKey . '_' . $field_name . '_';
		$meta_key .= $fKey . '_' . $field_name . '_';
	}
	$get_repeater_parent_field = $wpdb->get_results( $wpdb->prepare( "select post_content, post_name, post_excerpt, post_parent from {$wpdb->prefix}posts where ID = %d", $get_field_details[0]->post_parent ) );
	$field_info           = unserialize( $get_repeater_parent_field[0]->post_content );
	//if(isset($field_info['type']) && $field_info['type'] == 'repeater' && $get_repeater_parent_field[0]->post_parent != 0) {
	if((isset($field_info['type']) && $get_repeater_parent_field[0]->post_parent != 0) && ($field_info['type'] == 'repeater' || $field_info['type'] == 'group' || $field_info['type'] == 'flexible_content' )) {
		$parents[] = $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_';	
		$meta_key .= $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_' . $meta_key;
		$meta_key = $this->getMetaKeyOfRepeaterField($pID, $get_repeater_parent_field[0]->post_excerpt, 0, 0, $parents, $meta_key);

	} else {
		if(!empty($parents)) {
			$meta_key = '';
			for($i = count($parents); $i >= 0 ; $i--) {
				if(isset($parents[$i])){
					$meta_key .= $parents[$i];
				}	
			}
		}
		$meta_key = substr($meta_key, 2);
		$meta_key = substr($meta_key, 0, -1);
		return $meta_key;
	}
	return $meta_key;
}


// function getMetaKeyOfRepeaterField($pID, $field_name, $key = 0, $fKey = 0, $parents = array(), $meta_key = '' , $type = '') {
// 	global $wpdb;
// 	$get_field_details  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent, post_excerpt from {$wpdb->prefix}posts where post_excerpt = %s", $field_name ) );

// 		if(empty($parents) && $field_name == $get_field_details[0]->post_excerpt && $type !== 'repeater') {
// 				$parents[] =  $field_name . '_';
// 				$meta_key .= $fKey . '_' . $field_name . '_';
// 		}

// 	// elseif(empty($parents) && $field_name == $get_field_details[0]->post_excerpt && $type == 'repeater'){
// 	// 	$parents[] = $fKey . '_' . $field_name . '_';
// 	// 	$meta_key .= $fKey . '_' . $field_name . '_';
// 	// }

// 	$get_repeater_parent_field = $wpdb->get_results( $wpdb->prepare( "select post_content, post_name, post_excerpt, post_parent from {$wpdb->prefix}posts where ID = %d", $get_field_details[0]->post_parent ) );
// 	$field_info = unserialize( $get_repeater_parent_field[0]->post_content );

// 	if((isset($field_info['type']) && $get_repeater_parent_field[0]->post_parent != 0) && ($field_info['type'] == 'repeater' || $field_info['type'] == 'group')) {
// 	//if(isset($field_info['type']) && $field_info['type'] == 'repeater' && $get_repeater_parent_field[0]->post_parent != 0) {
// 		$parents[] = $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_';
// 		$meta_key .= $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_' . $meta_key;	
// 		$meta_key = $this->getMetaKeyOfRepeaterField($pID, $get_repeater_parent_field[0]->post_excerpt,0,0, $parents, $meta_key , $field_info['type']);	
// 	}

// 	else {
// 		if(!empty($parents)) {
// 			$meta_key = '';
// 			for($i = count($parents); $i >= 0 ; $i--) {
// 				if(isset($parents[$i])){
// 					$meta_key .= $parents[$i];
// 				}	
// 			}
// 		}
// 		$meta_key = substr($meta_key, 2);
// 		$meta_key = substr($meta_key, 0, -1);
// 		return $meta_key;
// 	}
// 	return $meta_key;
// }

}
