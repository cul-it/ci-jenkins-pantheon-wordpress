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

	public function set_acf_pro_values($header_array ,$value_array , $map, $maps, $post_id , $type,$mode){	
		$helpers_instance = ImportHelpers::getInstance();
		$acf_instance = ACFImport::getInstance();

		$helpers_instance = ImportHelpers::getInstance();
        $post_values =$helpers_instance->get_meta_values($maps , $header_array , $value_array);
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
							$value = '{'.$value.'}';
							$csv_element = str_replace($value, $csv_value_element, $csv_element);
						}
					}

					$math = 'MATH';
						if (strpos($csv_element, $math) !== false) {
									
							$equation = str_replace('MATH', '', $csv_element);
							$csv_element = $helpers_instance->evalmath($equation);
						}
					$wp_element= trim($key);

					if((!empty($csv_element) || $csv_element == 0) && !empty($wp_element)){
						if(is_plugin_active('advanced-custom-fields-pro/acf.php')){
							$this->acfpro_import_function($wp_element , $post_values,$csv_element ,$type, $post_id,$mode);
						} else {
							if(is_plugin_active('advanced-custom-fields/acf.php')){
								$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
								if(is_dir($acf_pluginPath)) {
									$this->acfpro_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
								}
								else{
									$acf_instance->acf_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
								}
							}
						}		
					}
				}

				elseif(!in_array($csv_value , $header_array)){
					$wp_element= trim($key);
					if(is_plugin_active('advanced-custom-fields-pro/acf.php')){
						$this->acfpro_import_function($wp_element ,$post_values, $csv_value ,$type, $post_id,$mode);

					} else {
						if(is_plugin_active('advanced-custom-fields/acf.php')){
							$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
							if(is_dir($acf_pluginPath)) {
								$this->acfpro_import_function($wp_element ,$post_values, $csv_value ,$type, $post_id,$mode);
							}
							else
								$acf_instance->acf_import_function($wp_element ,$post_values, $csv_value ,$type, $post_id,$mode);
						}
					}
				}

				else{
					$get_key= array_search($csv_value , $header_array);
					if(isset($value_array[$get_key])){
						$csv_element = $value_array[$get_key];	

						$wp_element= trim($key);
						if($mode == 'Insert'){
							if((!empty($csv_element) || $csv_element == 0) && !empty($wp_element)){
								if(is_plugin_active('advanced-custom-fields-pro/acf.php')){
									$this->acfpro_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
	
								} else {
									if(is_plugin_active('advanced-custom-fields/acf.php')){
										$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
										if(is_dir($acf_pluginPath)) {
											$this->acfpro_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
										}
										else
											$acf_instance->acf_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
									}
								}
							}	
						}
						else{
							if(!empty($csv_element) || !empty($wp_element)){
								if(is_plugin_active('advanced-custom-fields-pro/acf.php')){
									$this->acfpro_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
	
								} else {
									if(is_plugin_active('advanced-custom-fields/acf.php')){
										$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
										if(is_dir($acf_pluginPath)) {
											$this->acfpro_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
										}
										else
											$acf_instance->acf_import_function($wp_element ,$post_values, $csv_element ,$type, $post_id,$mode);
									}
								}
							}	

						}
						
					}
				}
			}
		} 
	}

	public function set_acf_rf_values($header_array ,$value_array , $map, $maps, $post_id , $type,$mode){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();

		//$post_values = $helpers_instance->get_meta_values($map , $header_array , $value_array);		
        $post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);		
		$img_meta = $helpers_instance->get_meta_values($maps , $header_array , $value_array);
		if(is_plugin_active('advanced-custom-fields/acf.php')){
			$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
			if(is_dir($acf_pluginPath)) {
				$this->acfpro_repeater_import_fuction($post_values,$type, $post_id,$img_meta,$mode);
			}
		}
		if(is_plugin_active('advanced-custom-fields-pro/acf.php')){
			$this->acfpro_repeater_import_fuction($post_values,$type, $post_id,$maps,$mode);
		} else if(is_plugin_active('acf-repeater/acf-repeater.php')){

		}
	}
	
	public function set_acf_fc_values($header_array ,$value_array , $map, $maps, $post_id , $type,$mode){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();

		//$post_values = $helpers_instance->get_meta_values($map , $header_array , $value_array);	
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);		
		$img_meta = $helpers_instance->get_meta_values($maps , $header_array , $value_array);	
		if(is_plugin_active('advanced-custom-fields/acf.php')){
			$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
			if(is_dir($acf_pluginPath)) {
				$this->acfpro_flexible_import_fuction($post_values,$type, $post_id,$img_meta,$mode);
			}
		}
		if(is_plugin_active('advanced-custom-fields-pro/acf.php')){
			$this->acfpro_flexible_import_fuction($post_values,$type, $post_id,$maps,$mode);
		} else if(is_plugin_active('acf-repeater/acf-repeater.php')){

		}
	}

	public function set_acf_gf_values($header_array ,$value_array , $map, $maps, $post_id , $type,$mode){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();

		//$post_values = $helpers_instance->get_meta_values($map , $header_array , $value_array);
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);		
		$img_meta = $helpers_instance->get_meta_values($maps , $header_array , $value_array);
		if((is_plugin_active('advanced-custom-fields/acf.php')) || (is_plugin_active('advanced-custom-fields-pro/acf.php'))){
			$this->acfpro_group_import_fuction($post_values,$type, $post_id, $img_meta,$mode);
		} 
	}

	function acfpro_import_function($acf_wpname_element ,$imgmeta, $acf_csv_element , $importAs , $post_id,$mode){
		$plugin = 'acf';
		$acf_wp_name = $acf_wpname_element;
		$acf_csv_name = $acf_csv_element;
		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();

		$get_acf_fields = $wpdb->get_results($wpdb->prepare("select post_content, post_name from {$wpdb->prefix}posts where post_type = %s and post_excerpt = %s", 'acf-field', $acf_wp_name ), ARRAY_A);
		foreach($get_acf_fields as $keys => $value_type){
			$get_type_field = unserialize($value_type['post_content']);	
			$field_type = $get_type_field['type'];
			$key = $get_acf_fields[0]['post_name'];
			if(isset($get_type_field['return_format'])){
				$return_format = $get_type_field['return_format'];
			}else{
				$return_format = '';
			}
			if($field_type == 'text' || $field_type == 'textarea' || $field_type == 'number' || $field_type == 'email' || $field_type == 'url' || $field_type == 'password' || $field_type == 'range' || $field_type == 'radio' || $field_type == 'true_false' || $field_type == 'time_picker' || $field_type == 'color_picker' || $field_type == 'button_group' || $field_type == 'oembed' || $field_type == 'wysiwyg'){
				$map_acf_wp_element = $acf_wp_name;
				$map_acf_csv_element = $acf_csv_name;
			}
			if($field_type == 'date_time_picker'){

				$dt_var = trim($acf_csv_name);
				$date_time_of = date("Y-m-d H:i:s", strtotime($dt_var) );
				if($mode == 'Insert'){
					if($dt_var == 0 || $dt_var == '')
					$map_acf_csv_element = $dt_var;	
					else{
					
							$map_acf_csv_element = $date_time_of;
					}
				}
				else{
					if($dt_var == 0 || $dt_var == '')
					$map_acf_csv_element = $dt_var;	
					else{
						$map_acf_csv_element = $date_time_of;
					}
				}
				$map_acf_wp_element = $acf_wp_name;
			}
			if($field_type == 'user'){	
				$maps_acf_csv_name = $acf_csv_name;	
				$map_acf_wp_element = $acf_wp_name;
				$explo_acf_csv_name = explode(',',trim($acf_csv_name));		
				foreach($explo_acf_csv_name as $user){
					if(!is_numeric($explo_acf_csv_name)){
						$userid = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}users where user_login = %s",$user));			
						foreach($userid as $users){
							$map_acf_csv_element[] = $users;		
						}
					}
				}
				if(is_numeric($user)){
					$map_acf_csv_element = $user;
				}
			}
			if ($field_type == 'google_map') {

				$location = trim($acf_csv_name);
				list($add, $lat,$lng) = explode('|', $location);
				$area = rtrim($add, ",");
				$map = array(
					'address' => $area,
					'lat'     =>  $lat,
					'lng'     => $lng
				);
				$map_acf_csv_element = $map;
				$map_acf_wp_element = $acf_wp_name;
			}
			if($field_type == 'date_picker'){

				$var = trim($acf_csv_name);
				$date = str_replace('/', '-', "$var");
				$date_of = date('Ymd', strtotime($date));
				if($mode == 'Insert'){
					if($var == 0 || $var == '')
						$map_acf_csv_element = $var;	
					else{
						$map_acf_csv_element = $date_of;
					}
				}
				else{
					if($var == 0 || $var == '')
					$map_acf_csv_element = $var;	
					else{
						$map_acf_csv_element = $date_of;
					}
				}
				$map_acf_wp_element = $acf_wp_name;

			}
			if($field_type == 'select'){
				if($get_type_field['multiple'] == 0){
					$map_acf_csv_element = $acf_csv_name;
				}else{
					$explo_acf_csv_name = explode(',',trim($acf_csv_name));
					$maps_acf_csv_name = array();
					foreach($explo_acf_csv_name as $explo_csv_value){
						$map_acf_csv_element[] = trim($explo_csv_value);
					}	
				}
				$map_acf_wp_element = $acf_wp_name;
			}

			if($field_type == 'post_object' || $field_type == 'page_link'){
				if($get_type_field['multiple'] == 0){
					$maps_acf_csv_name = $acf_csv_name;
				}else{
					$explo_acf_csv_name = explode(',',trim($acf_csv_name));
					$maps_acf_csv_name = array();
					foreach($explo_acf_csv_name as $explo_csv_value){
						$maps_acf_csv_name[] = trim($explo_csv_value);
					}	
				}
				$map_acf_csv_elements = $maps_acf_csv_name;
				if($get_type_field['multiple'] == 0){
					if (!is_numeric($map_acf_csv_elements ) ){
						$id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_title = %s",$map_acf_csv_elements));
						$map_acf_csv_element[]=$id[0];
					}
					else{
						$map_acf_csv_element = $maps_acf_csv_name;
					}
				}
				else{
					foreach($map_acf_csv_elements as $csv_element){
						if (!is_numeric($csv_element ) ){
						$id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_title = %s",$csv_element));
						$map_acf_csv_element[]=$id[0];
					}
					else{
						$map_acf_csv_element = $maps_acf_csv_name;
					}
				}
				}	
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
						
		                    ACFProImport::$media_instance->acfimageMetaImports($map_acf_csv_element,$imgmeta,$plugin);
						}
						else {
						
							$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
							ACFProImport::$media_instance->acfimageMetaImports($map_acf_csv_element,$imgmeta,$plugin);			
						}
					}
					else {
						$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
						ACFProImport::$media_instance->acfimageMetaImports($map_acf_csv_element,$imgmeta,$plugin);						
					}
				}
				else {
					$map_acf_csv_element = ACFProImport::$media_instance->media_handling($acf_csv_name, $post_id, $acf_wpname_element);
				
					ACFProImport::$media_instance->acfimageMetaImports($map_acf_csv_element,$imgmeta,$plugin);
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
			elseif ($field_type == 'gallery') {

				$gallery_ids =array();
				$exploded_gallery_items = explode(',', $acf_csv_name);

				foreach($exploded_gallery_items as $gallery) {
					$gallery = trim($gallery);
					$ext = pathinfo($gallery, PATHINFO_EXTENSION);
					if($ext){
						if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
							$img_id = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where guid = %s AND post_type='attachment'",$gallery));

							if(!empty($img_id)) {
								$gallery_ids[] = $img_id[0];
								ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$imgmeta,$plugin);
							}else{
								$get_gallery_id = ACFProImport::$media_instance->media_handling( $gallery, $post_id );
							
								if($get_gallery_id != '') {
									$gallery_ids[] = $get_gallery_id;
									ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$imgmeta,$plugin);
		
									
								}
							}
						}else {
							$get_gallery_id = ACFProImport::$media_instance->media_handling($gallery, $post_id);
							
							if($get_gallery_id != '') {
								$gallery_ids[] = $get_gallery_id;
								ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$imgmeta,$plugin);
								
							}
						}
					} else {
						$galleryLen = strlen($gallery);
						$checkgalleryid = intval($gallery);
						$verifiedGalleryLen = strlen($checkgalleryid);
						
						if($galleryLen == $verifiedGalleryLen) {
							$gallery_ids[] = $gallery;
							ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$imgmeta,$plugin);
							
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
		}
		elseif($importAs == 'Comments'){
			update_comment_meta($post_id, $map_acf_wp_element, $map_acf_csv_element);
			update_comment_meta($post_id, '_' . $map_acf_wp_element, $key);	
		}
		else {
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

	function acfpro_group_import_fuction($data_array, $importAs, $pID,$maps,$mode) { 

		global $wpdb;
		$plugin = 'acf';
		$helpers_instance = ImportHelpers::getInstance();

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
						if($dt_group_var== 0 || $dt_group_var== ''){
							$acf_grp_field_info[$grp_field_meta_key] = $dt_group_var;
							$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
						}	
						else{
							$acf_grp_field_info[$grp_field_meta_key] = $date_time_group_of;
							$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
						}
					}
					if ($group_type == 'google_map') {
						$location[] = trim($val);
						foreach($location as $loc){
							$locc=implode('|', $location);
						}

						list($add, $lat,$lng) = explode('|', $locc);
						$area = rtrim($add, ",");
						$map = array(
							'address' => $area,
							'lat'     =>  $lat,
							'lng'     => $lng
						);
						$acf_grp_field_info[$grp_field_meta_key] = $map;
						$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
					}

					if($group_type == 'date_picker'){

						$var_group = trim($val);
						$date_group = str_replace('/', '-', "$var_group");
						$date_group_of = date('Ymd', strtotime($date_group));
						if($mode == 'Insert'){
							if($var_group == 0 || $var_group == ''){
								$acf_grp_field_info[$grp_field_meta_key]  = $var_group;
								$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
							}
							else{
								$acf_grp_field_info[$grp_field_meta_key]  = $date_group_of;
								$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];

							}	
						}
						else{
							if($var_group == 0 || $var_group == ''){
								$acf_grp_field_info[$grp_field_meta_key]  = $var_group;
							$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
							}	
							else{
								$acf_grp_field_info[$grp_field_meta_key]  = $date_group_of;
								$acf_grp_field_info['_'.$grp_field_meta_key]=$group_fields[$grpKey];
							}
						}
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
						$exploded_gallery_items = explode( ',', $val );
						foreach ( $exploded_gallery_items as $gallery ) {
							$gallery = trim( $gallery );
							if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
								$get_gallery_id = ACFProImport::$media_instance->media_handling( $gallery, $pID);	
								if ( $get_gallery_id != '' ) {
									$gallery_ids[] = $get_gallery_id;
									ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$maps,$plugin);
								}
							} else {
								$galleryLen         = strlen( $gallery );
								$checkgalleryid     = intval( $gallery );
								$verifiedGalleryLen = strlen( $checkgalleryid );
								if ( $galleryLen == $verifiedGalleryLen ) {
									$gallery_ids[] = $gallery;
									ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$maps,$plugin);
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
				}
				elseif($importAs == 'Comments'){
					update_comment_meta($pID, $pKey, $pVal);
				}
				else{
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
			update_post_meta($pID, $get_group_parent_field[0]->post_excerpt , 1);
			update_post_meta($pID, '_'.$get_group_parent_field[0]->post_excerpt , $get_group_parent_field[0]->post_name );
		} 

		if($get_group_super_parent_field[0]->post_parent != 0 && isset($parent_field_info['type']) && $parent_field_info['type'] == 'group'){
			$meta_key =  $get_group_super_parent_field[0]->post_excerpt . '_' . $meta_key;
			update_post_meta($pID, $get_group_super_parent_field[0]->post_excerpt , 1);
			update_post_meta($pID, '_'.$get_group_super_parent_field[0]->post_excerpt , $get_group_super_parent_field[0]->post_name );
		}
		return $meta_key;
	}
	
	function acfpro_repeater_import_fuction($data_array, $importAs, $pID,$maps,$mode) {
		global $wpdb;
	
		$helpers_instance = ImportHelpers::getInstance();
        $plugin = 'acf';
		$createdFields = $rep_parent_fields = $repeater_fields = $repeater_flexible_content_import_method = array();
		$flexible_array = [];
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
				$flexible_array[$repKey] = $get_field_info[0]->ID;
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
							if(is_string($acf_rep_field_value)){
								$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$acf_rep_field_value}' AND post_status='publish'";
								$name = $wpdb->get_results($query);
								if (!empty($name)) {
									$acf_rep_field_value=$name[0]->id;
								}
							}
							elseif (is_numeric($acf_rep_field_value)) {
								$acf_rep_field_value=$acf_rep_field_value;
							}
						}elseif(!$field_info['multiple'] == 0){
							$acf_rep_value_exp = explode(',',trim($val));
							$acf_rep_field_value = array();
							foreach($acf_rep_value_exp as $acf_reps_value){
								$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$acf_reps_value}' AND post_status!='trash'";
								$multiple_id = $wpdb->get_results($query);
								foreach($multiple_id as $mul_id){
									$acf_rep_field_value[]=trim($mul_id->id);
								}
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
						if($dt_rep_var== 0 || $dt_rep_var== ''){
							$acf_rep_field_info[$rep_field_meta_key] =$dt_rep_var ;
							$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
						}	
						else{
							$acf_rep_field_info[$rep_field_meta_key] = $date_time_rep_of;
							$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
						}
					}
					if ($rep_type == 'google_map') {

						$location[] = trim($val);
						foreach($location as $loc){
							$locc=implode('|', $location);
						}
						list($add, $lat,$lng) = explode('|', $locc);
						$area = rtrim($add, ",");
						$map = array(
							'address' => $area,
							'lat'     =>  $lat,
							'lng'     => $lng
						);
						$acf_rep_field_info[$rep_field_meta_key] = $map;
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					}
					if($rep_type == 'date_picker'){

						$var_rep = trim($val);
						$date_rep = str_replace('/', '-', "$var_rep");
						$date_rep_of = date('Ymd', strtotime($date_rep));
						if($mode == 'Insert'){
							if($var_rep == 0 || $var_rep == ''){
								$acf_rep_field_info[$rep_field_meta_key] = $var_rep;
								$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
							}
							else{
								$acf_rep_field_info[$rep_field_meta_key] = $date_rep_of;
							  	$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];

							}	
						}
						else{
							if($var_rep == 0 || $var_rep == ''){
							$acf_rep_field_info[$rep_field_meta_key] =$var_rep ;
							$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
							}	
							else{
								$acf_rep_field_info[$rep_field_meta_key] = $date_rep_of;
								$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
							}
						}
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
					if($rep_type == 'gallery'){
						$gallery_ids = array();
						if ( is_array( $gallery_ids ) ) {
							unset( $gallery_ids );
							$gallery_ids = array();
						}
						$exploded_gallery_items = explode( ',', $val );
						foreach ( $exploded_gallery_items as $gallery ) {
							$gallery = trim( $gallery );
							if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
								$get_gallery_id = ACFProImport::$media_instance->media_handling( $gallery, $pID);	
								if ( $get_gallery_id != '' ) {
									$gallery_ids[] = $get_gallery_id;
									ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$maps,$plugin);

								}
							} else {
								$galleryLen         = strlen( $gallery );
								$checkgalleryid     = intval( $gallery );
								$verifiedGalleryLen = strlen( $checkgalleryid );
								if ( $galleryLen == $verifiedGalleryLen ) {
									$gallery_ids[] = $gallery;
									ACFProImport::$media_instance->acfgalleryMetaImports($gallery_ids,$maps,$plugin);
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
					if ($rep_type == 'message') {
						$field_info['message'] = $val;
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

		$countof_flexi_child_names = array_count_values($acf_rep_field_info);
		$flexi_inner_parent_child_names = [];

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

				$flexible_parent_id = $flexible_array[$pKey];
				if(!empty($flexible_parent_id)){
					$get_flexi_child_name = $wpdb->get_results("SELECT ID, post_name, post_content, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = $flexible_parent_id", ARRAY_A);
					$flexi_child_array = [];

					$temp = 0;
					foreach($get_flexi_child_name as $flexi_values){	

						if(array_key_exists($flexi_values['post_name'] , $countof_flexi_child_names)){
							array_push($flexi_child_array, $countof_flexi_child_names[$flexi_values['post_name']]);
						}

						$flexi_post_content = unserialize($flexi_values['post_content']);
						if($flexi_post_content['type'] == 'flexible_content'){

							$flexible_parent_name = $wpdb->get_var("SELECT post_excerpt FROM {$wpdb->prefix}posts WHERE ID = $flexible_parent_id ");
							$flexible_layout_names = explode('->' , $data_array[$flexible_parent_name]);
							$flexible_parent_layout_name = $flexible_layout_names[0];

							$flexi_post_id = $flexi_values['ID'];

							$get_inner_flexi_child_name = $wpdb->get_results("SELECT post_name, post_excerpt, post_content FROM {$wpdb->prefix}posts WHERE post_parent = $flexi_post_id", ARRAY_A);
							foreach($get_inner_flexi_child_name as $inner_flexi_values){
								if(array_key_exists($inner_flexi_values['post_name'] , $countof_flexi_child_names)){
									array_push($flexi_child_array, $countof_flexi_child_names[$inner_flexi_values['post_name']]);
								}
							}

							$flexible_child_name = $wpdb->get_var("SELECT post_excerpt FROM {$wpdb->prefix}posts WHERE ID = $flexi_post_id ");	
							if(strpos($flexible_layout_names[1], '|') !== false){
								$flexible_inner_layout_names = explode('|', $flexible_layout_names[1]);
								$flexi_inner_parent_child_names[$flexible_parent_name .'->'. $flexible_child_name] = $flexible_parent_layout_name .'->'.$flexible_inner_layout_names[$temp];	
								$temp++;
							}
							else{
								$flexible_child_layout_name = $flexible_layout_names[1];
								$flexi_inner_parent_child_names[$flexible_parent_name .'->'. $flexible_child_name] = $flexible_parent_layout_name .'->'.$flexible_child_layout_name;	
							}
						}
					}
				}	
				$final_flexi_count = max($flexi_child_array);

				//$flexible_group = explode('|',$data_array[$pKey]);
				$flexi_group_value = $data_array[$pKey];
				foreach ($repeater_field_rows as $repKey => $repVal) {
					//foreach($flexible_group as $flexi_group_value){	
					if(strpos($flexi_group_value, '->') !== false){
						$flexible_inner_group = explode('->', $flexi_group_value);

						$flexible_inner_group_values = $flexible_inner_group[0];
						if($final_flexi_count > 1){
							$flexible_inner_group_values = array_fill(0, $final_flexi_count, $flexible_inner_group_values);
						}
						$flex_value[$repKey] = $flexible_inner_group_values;

						$is_inner_flexible = true;
					}
					else{
						if($final_flexi_count > 1){
							$flexi_group_value = array_fill(0, $final_flexi_count, $flexi_group_value);
						}
						$flex_value[$repKey] = $flexi_group_value;	
					}

					if($is_inner_flexible){	
						foreach($flexi_inner_parent_child_names as $flexi_inner_names_keys => $flexi_inner_names_values){
							$flexi_inner_names_key = explode('->', $flexi_inner_names_keys);
							$flexi_inner_names_value = explode('->', $flexi_inner_names_values);

							if((strpos($pKey, $flexi_inner_names_key[0]) !== false) && (strpos($pKey, $flexi_inner_names_key[1]) !== false)){
								$flexible_inner_groups_values = $flexi_inner_names_value[1];
								if($final_flexi_count > 1){
									$flexible_inner_groups_values = array_fill(0, $final_flexi_count, $flexible_inner_groups_values);
								}
								$flex_value[$repKey] = $flexible_inner_groups_values;
							}
						}
					}		
					//}		
				}		
				if($importAs == 'Users'){
					update_user_meta($pID, $pKey, $flex_value);
				}elseif(in_array($importAs, $listTaxonomy)){
					if($term_meta = 'yes'){
						update_term_meta($pID, $pKey, $flex_value);
					}
				}else{
					if(is_array($flex_value[0])){
						$flex_values = $flex_value[0];
						update_post_meta($pID, $pKey, $flex_values);
					}
					else{
						update_post_meta($pID, $pKey, $flex_value);
					}
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
		//}
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
		if((isset($field_info['type']) && $get_repeater_parent_field[0]->post_parent != 0) && ($field_info['type'] == 'repeater' || $field_info['type'] == 'group' || $field_info['type'] == 'flexible_content' )) {
			$parents[] = $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_';	
			$meta_key .= $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_' . $meta_key;
			$meta_key = $this->getMetaKeyOfRepeaterField($pID, $get_repeater_parent_field[0]->post_excerpt, 0, 0, $parents, $meta_key);
			update_post_meta($pID, $get_repeater_parent_field[0]->post_excerpt , 1);
			update_post_meta($pID, '_'.$get_repeater_parent_field[0]->post_excerpt , $get_repeater_parent_field[0]->post_name );
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

	function acfpro_flexible_import_fuction($data_array, $importAs, $pID,$maps,$mode) {
		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();
		$createdFields = $rep_parent_fields = $repeater_fields = $repeater_flexible_content_import_method = array();
		$flexible_array = [];
		$parent_key_values = [];
		$child_key_values = [];
		$plugin = 'acf';
		foreach($data_array as $repKey => $repVal) {
			$i = 0;
			// Prepare the meta array by field type
			$get_field_info  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from {$wpdb->prefix}posts where post_excerpt = %s", $repKey ) );
			$parentid =  $get_field_info[0]->post_parent ;
			$field_info = unserialize( $get_field_info[0]->post_content );
			$fieldtype =  $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from {$wpdb->prefix}posts where ID = $parentid"  ) );
			$fieldtype_info =unserialize( $fieldtype[0]->post_content );
			$repeater_fields[$repKey] = $get_field_info[0]->post_name;
			if(isset($field_info['type']) && $field_info['type'] == 'flexible_content') {
				$repeater_flexible_content_import_method[ $get_field_info[0]->post_name ] = $field_info['layouts'][0]['name'];
				$flexible_array[$repKey] = $get_field_info[0]->ID;
			} elseif(isset($field_info['type']) && ($field_info['type'] == 'image' || $field_info['type'] == 'file')) {
				if($field_info['type'] == 'image') {
					$repeater_image_import_method[ $get_field_info[0]->post_name ] = $field_info['return_format'];
				} else {
					$repeater_file_import_method[ $get_field_info[0]->post_name ] = $field_info['return_format'];
				}
			} else {
				$repeater_sub_field_type[ $get_field_info[0]->post_name ] = $field_info['type'];
			}
	
				$repeater_field_rows = explode(',', $repVal);
			
			$j = 0;
			foreach($repeater_field_rows as $index => $value) {
				$repeater_field_values = explode('->', $value);
				$checkCount = count($repeater_field_values);
				foreach($repeater_field_values as $key => $val) {
					if($checkCount > 1){
						
							$rep_field_meta_key = $this->getMetaKeyOfFlexibleField( $pID, $repKey, $index, $key );
					}else{
						
							$rep_field_meta_key = $this->getMetaKeyOfFlexibleField( $pID, $repKey, $i, $j );						
					}
					if($rep_field_meta_key[0] == '_')
						$rep_field_meta_key = substr($rep_field_meta_key, 1);
					$rep_field_parent_key = explode( '_' . $repKey, $rep_field_meta_key );
					$rep_field_parent_key = substr( $rep_field_parent_key[0], 0, - 2 );
					if (substr($rep_field_parent_key, -1) == "_") {
						$rep_field_parent_key = substr($rep_field_parent_key, 0, -1);
					}
					$super_parent = explode('_'.$index.'_',$rep_field_parent_key);
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
							if(is_string($acf_rep_field_value)){
								$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$acf_rep_field_value}' AND post_status='publish'";
								$name = $wpdb->get_results($query);
								if (!empty($name)) {
									$acf_rep_field_value=$name[0]->id;
								}
							}
							elseif (is_numeric($acf_rep_field_value)) {
								$acf_rep_field_value=$acf_rep_field_value;
							}
						}elseif(!$field_info['multiple'] == 0){
							$acf_rep_value_exp = explode(',',trim($val));
							$acf_rep_field_value = array();
							foreach($acf_rep_value_exp as $acf_reps_value){
								$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$acf_reps_value}' AND post_status!='trash'";
								$multiple_id = $wpdb->get_results($query);
								foreach($multiple_id as $mul_id){
									$acf_rep_field_value[]=trim($mul_id->id);
								}
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
						if($dt_rep_var== 0 || $dt_rep_var== ''){
							$acf_rep_field_info[$rep_field_meta_key] =$dt_rep_var ;
							$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
						}	
						else{
							$acf_rep_field_info[$rep_field_meta_key] = $date_time_rep_of;
							$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
						}
					}
					if ($rep_type == 'google_map') {
	
						$location[] = trim($val);
						foreach($location as $loc){
							$locc=implode('|', $location);
						}
						list($add, $lat,$lng) = explode('|', $locc);
						$area = rtrim($add, ",");
						$map = array(
							'address' => $area,
							'lat'     =>  $lat,
							'lng'     => $lng
						);
						$acf_rep_field_info[$rep_field_meta_key] = $map;
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					}
					if($rep_type == 'date_picker'){
	
						$var_rep = trim($val);
						$date_rep = str_replace('/', '-', "$var_rep");
						$date_rep_of = date('Ymd', strtotime($date_rep));
							if($var_rep == 0 || $var_rep == ''){
								$acf_rep_field_info[$rep_field_meta_key] = $var_rep;
								$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
							}
							else{
								$acf_rep_field_info[$rep_field_meta_key] = $date_rep_of;
							  	$acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];

							}	
					
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
					if($rep_type == 'gallery'){
						$gallery_ids = array();
						if ( is_array( $gallery_ids ) ) {
							unset( $gallery_ids );
							$gallery_ids = array();
						}
						$exploded_gallery_items = explode( ',', $val );
						foreach ( $exploded_gallery_items as $gallery ) {
							$gallery = trim( $gallery );
							if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
								$get_gallery_id = ACFProImport::$media_instance->media_handling( $gallery, $pID);	
								if ( $get_gallery_id != '' ) {
									$gallery_ids[] = $get_gallery_id;
									ACFProImport::$media_instance->acfimageMetaImports($gallery_ids,$maps,$plugin);
								}
							} else {
								$galleryLen         = strlen( $gallery );
								$checkgalleryid     = intval( $gallery );
								$verifiedGalleryLen = strlen( $checkgalleryid );
								if ( $galleryLen == $verifiedGalleryLen ) {
									$gallery_ids[] = $gallery;
									ACFProImport::$media_instance->acfimageMetaImports($gallery_ids,$maps,$plugin);
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
					if ($rep_type == 'message') {
						$field_info['message'] = $val;
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
	
		$countof_flexi_child_names = array_count_values($acf_rep_field_info);
		$flexi_inner_parent_child_names = [];
	
		foreach($rep_parent_fields as $pKey => $pVal) {
	
			$get_cust  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from {$wpdb->prefix}posts where post_excerpt = %s", $pKey),ARRAY_A);
			foreach ($get_cust as $get_val ) {
				$custvalue = $get_val['post_content'];
				$post_content = unserialize($custvalue);
				$field_layout =$post_content['layouts'];
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
	
				$flexible_parent_id = $flexible_array[$pKey];
				if(!empty($flexible_parent_id)){
					$get_flexi_child_name = $wpdb->get_results("SELECT ID, post_name, post_content, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = $flexible_parent_id", ARRAY_A);
					$flexi_child_array = [];
	
					$temp = 0;
					foreach($get_flexi_child_name as $flexi_values){	
	
						if(array_key_exists($flexi_values['post_name'] , $countof_flexi_child_names)){
							array_push($flexi_child_array, $countof_flexi_child_names[$flexi_values['post_name']]);
						}
	
						$flexi_post_content = unserialize($flexi_values['post_content']);
						if($flexi_post_content['type'] == 'flexible_content'){
	
							$flexible_parent_name = $wpdb->get_var("SELECT post_excerpt FROM {$wpdb->prefix}posts WHERE ID = $flexible_parent_id ");
							$flexible_layout_names = explode('->' , $data_array[$flexible_parent_name]);
							$flexible_parent_layout_name = $flexible_layout_names[0];
	
							$flexi_post_id = $flexi_values['ID'];
	
							$get_inner_flexi_child_name = $wpdb->get_results("SELECT post_name, post_excerpt, post_content FROM {$wpdb->prefix}posts WHERE post_parent = $flexi_post_id", ARRAY_A);	
							foreach($get_inner_flexi_child_name as $inner_flexi_values){
								if(array_key_exists($inner_flexi_values['post_name'] , $countof_flexi_child_names)){
									array_push($flexi_child_array, $countof_flexi_child_names[$inner_flexi_values['post_name']]);
								}
							}
	
							$flexible_child_name = $wpdb->get_var("SELECT post_excerpt FROM {$wpdb->prefix}posts WHERE ID = $flexi_post_id ");	
							if(strpos($flexible_layout_names[1], '|') !== false){
								$flexible_inner_layout_names = explode('|', $flexible_layout_names[1]);
								$flexi_inner_parent_child_names[$flexible_parent_name .'->'. $flexible_child_name] = $flexible_parent_layout_name .'->'.$flexible_inner_layout_names[$temp];	
								$temp++;
							}
							else{
								$flexible_child_layout_name = $flexible_layout_names[1];
								$flexi_inner_parent_child_names[$flexible_parent_name .'->'. $flexible_child_name] = $flexible_parent_layout_name .'->'.$flexible_child_layout_name;	
							}
						}
					}
				}	
			if($field_layout){
				$final_flexi_count = max($flexi_child_array);
				$flexible_group = explode(',',$data_array[$pKey]);
				
				$flexi_group_value=explode(',',$data_array[$pKey]);
				foreach ($repeater_field_rows as $repKey => $repVal) {
					foreach($flexible_group as $flexi_group_key => $flexi_group_value){	
					if(strpos($flexi_group_value, '->') !== false){
						$flexible_inner_group = explode('->', $flexi_group_value);
	
						$flexible_inner_group_values = $flexible_inner_group[0];
						if($final_flexi_count > 1){
							$flexible_inner_group_values = array_fill(0, $final_flexi_count, $flexible_inner_group_values);
						}
						$flex_value[$repKey] = $flexible_inner_group_values;
	
						$is_inner_flexible = true;
					}
					else{
						if($final_flexi_count > 1){
							$flexi_group_value = array_fill(0, $final_flexi_count, $flexi_group_value);
						}
						$flex_value[ $flexi_group_key] = $flexi_group_value;
					}
					if($is_inner_flexible){	
						foreach($flexi_inner_parent_child_names as $flexi_inner_names_keys => $flexi_inner_names_values){
							$flexi_inner_names_key = explode('->', $flexi_inner_names_keys);
							$flexi_inner_names_value = explode('->', $flexi_inner_names_values);
	
							if((strpos($pKey, $flexi_inner_names_key[0]) !== false) && (strpos($pKey, $flexi_inner_names_key[1]) !== false)){
								$flexible_inner_groups_values = $flexi_inner_names_value[1];
								if($final_flexi_count > 1){
									$flexible_inner_groups_values = array_fill(0, $final_flexi_count, $flexible_inner_groups_values);
								}
								$flex_value[$repKey] = $flexible_inner_groups_values;
							}
						}
					}		
					}		
				}
			}	
			else{
				$final_flexi_count = max($flexi_child_array);

				//$flexible_group = explode('|',$data_array[$pKey]);
				$flexi_group_value = $data_array[$pKey];
				foreach ($repeater_field_rows as $repKey => $repVal) {
					//foreach($flexible_group as $flexi_group_value){	
					if(strpos($flexi_group_value, '->') !== false){
						$flexible_inner_group = explode('->', $flexi_group_value);

						$flexible_inner_group_values = $flexible_inner_group[0];
						if($final_flexi_count > 1){
							$flexible_inner_group_values = array_fill(0, $final_flexi_count, $flexible_inner_group_values);
						}
						$flex_value[$repKey] = $flexible_inner_group_values;

						$is_inner_flexible = true;
					}
					else{
						if($final_flexi_count > 1){
							$flexi_group_value = array_fill(0, $final_flexi_count, $flexi_group_value);
						}
						$flex_value[$repKey] = $flexi_group_value;	
					}

					if($is_inner_flexible){	
						foreach($flexi_inner_parent_child_names as $flexi_inner_names_keys => $flexi_inner_names_values){
							$flexi_inner_names_key = explode('->', $flexi_inner_names_keys);
							$flexi_inner_names_value = explode('->', $flexi_inner_names_values);

							if((strpos($pKey, $flexi_inner_names_key[0]) !== false) && (strpos($pKey, $flexi_inner_names_key[1]) !== false)){
								$flexible_inner_groups_values = $flexi_inner_names_value[1];
								if($final_flexi_count > 1){
									$flexible_inner_groups_values = array_fill(0, $final_flexi_count, $flexible_inner_groups_values);
								}
								$flex_value[$repKey] = $flexible_inner_groups_values;
							}
						}
					}		
					//}		
				}
			}	
				if($importAs == 'Users'){
					update_user_meta($pID, $pKey, $flex_value);
				}elseif(in_array($importAs, $listTaxonomy)){
					if($term_meta = 'yes'){
						update_term_meta($pID, $pKey, $flex_value);
					}
				}else{
					if(is_array($flex_value[0])){
						$flex_values = $flex_value[0];
						update_post_meta($pID, $pKey, $flex_values);
					}
					else{
						update_post_meta($pID, $pKey, $flex_value);
					}
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
		//}
	}
	
	function getMetaKeyOfFlexibleField($pID, $field_name, $key , $fKey , $parents = array(), $meta_key = '') {
		global $wpdb;
		$get_field_details  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent, post_excerpt from {$wpdb->prefix}posts where post_excerpt = %s", $field_name ) );
		
		$field_info1           = unserialize( $get_field_details[0]->post_content );
		$i =0;
		$layout = $field_info1['parent_layout'];
		
		$get_repeater_parent_field = $wpdb->get_results( $wpdb->prepare( "select post_content, post_name, post_excerpt, post_parent from {$wpdb->prefix}posts where ID = %d", $get_field_details[0]->post_parent ) );
		$field_info           = unserialize( $get_repeater_parent_field[0]->post_content );
		$layouts=$field_info['layouts'];
		$keys = array_keys($layouts);//get the main keys
		foreach($keys as $layout_key =>$val){
			if($layout == $val){
				$fKey =$layout_key;
				$key= $layout_key;
				if(empty($parents) && $field_name == $get_field_details[0]->post_excerpt) {
					$parents[] = $fKey . '_' . $field_name . '_';
					$meta_key .= $fKey . '_' . $field_name . '_';
				}
				
			}
		}
		if((isset($field_info['type']) && $get_repeater_parent_field[0]->post_parent != 0) && ($field_info['type'] == 'repeater' || $field_info['type'] == 'group' || $field_info['type'] == 'flexible_content' )) {
			$parents[] = $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_';	
			$meta_key .= $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_' . $meta_key;
			$meta_key = $this->getMetaKeyOfRepeaterField($pID, $get_repeater_parent_field[0]->post_excerpt, $key, $fKey, $parents, $meta_key);
			update_post_meta($pID, $get_repeater_parent_field[0]->post_excerpt , 1);
			update_post_meta($pID, '_'.$get_repeater_parent_field[0]->post_excerpt , $get_repeater_parent_field[0]->post_name );
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
	
	
}
