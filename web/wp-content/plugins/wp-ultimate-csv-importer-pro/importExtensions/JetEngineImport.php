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

class JetEngineImport {

	private static $instance = null;
	
    public static function getInstance() {		
		if (JetEngineImport::$instance == null) {
			JetEngineImport::$instance = new JetEngineImport;
		}
		return JetEngineImport::$instance;
	}
	function set_jet_engine_values($header_array ,$value_array , $map, $post_id , $type , $mode){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		$this->jet_engine_import_function($post_values,$type, $post_id, $mode);
	}
	function set_jet_engine_rf_values($header_array ,$value_array , $map, $post_id , $type , $mode){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		$this->jet_engine_rf_import_function($post_values,$type, $post_id, $mode);
	}
	public function jet_engine_import_function($data_array, $type, $pID ,$mode) 
	{
		$media_instance = MediaHandling::getInstance();
		$jet_data = $this->JetEngineFields($type);
	
		foreach ($data_array as $dkey => $dvalue) {
			if(array_key_exists($dkey,$jet_data['JE'])){
				if($jet_data['JE'][$dkey]['type'] == 'gallery' || $jet_data['JE'][$dkey]['type'] == 'media'){
					$exploded_gallery_items = explode( ',', $dvalue );
					foreach ( $exploded_gallery_items as $gallery ) {
						$gallery = trim( $gallery );
						if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
							$get_gallery_id = $media_instance->media_handling( $gallery, $pID);	
							$media_id = $media_instance->media_handling( $gallery, $pID);
								if ( $get_gallery_id != '' ) {
								if($jet_data['JE'][$dkey]['type'] == 'media'){
									$media_ids .= $media_id. ',';
								}
								else{
									$gallery_ids .= $get_gallery_id.',';
								}
							}
						} else {
							$galleryLen         = strlen( $gallery );
							$checkgalleryid     = intval( $gallery );
							$verifiedGalleryLen = strlen( $checkgalleryid );
							if ( $galleryLen == $verifiedGalleryLen ) {
								if($jet_data['JE'][$dkey]['type'] == 'media'){
									$media_ids .= $gallery. ',';
								}
								else{
									$gallery_ids .= $gallery. ',';
								}
								
							}
						}
					}
					$gallery_id = rtrim($gallery_ids,',');
					$media_id = rtrim($media_ids,',');
					if($jet_data['JE'][$dkey]['type'] == 'media'){		
						$darray[$jet_data['JE'][$dkey]['name']] = $media_id;
					}
					else{
						$darray[$jet_data['JE'][$dkey]['name']] = $gallery_id;
					}
				}
                elseif($jet_data['JE'][$dkey]['type'] == 'datetime-local'){
				
					$dt_var = trim($dvalue);
					$date_time_of = date("Y-m-d\TH:i", strtotime($dt_var) );
					$date = date_format($date_time_of,"Y-m-d");
					$darray[$jet_data['JE'][$dkey]['name']] = $date_time_of;
					
				}
				elseif($jet_data['JE'][$dkey]['type'] == 'date'){
					$var = trim($dvalue);
				    $date = str_replace('/', '-', "$var");
					$date_of = date('Y-m-d', strtotime($date));
					$darray[$jet_data['JE'][$dkey]['name']] = $date_of;
				}
				elseif($jet_data['JE'][$dkey]['type'] == 'time'){
					$var = trim($dvalue);
					$time = date('H:i', strtotime($var));
					$darray[$jet_data['JE'][$dkey]['name']] = $time;
				}
				elseif($jet_data['JE'][$dkey]['type'] == 'checkbox'){
					$options = $jet_data['JE'][$dkey]['options'];
					$arr = [];
					$opt = [];
					$dvalexp = explode(',' , $dvalue);
					foreach($options as $option_key => $option_val){
						$arr[$option_val['key']] = 'false';
					}
					foreach($dvalexp as $dvalkey => $dvalueval){
						$keys = array_keys($arr);
						foreach($keys as $keys1){
							if($dvalueval == $keys1){
								$arr[$keys1] = 'true';
							}
						}
					}
					$darray[$jet_data['JE'][$dkey]['name']] = $arr;
				}
				elseif($jet_data['JE'][$dkey]['type'] == 'select'){
					$dselect = [];
					if($jet_data['JE'][$dkey]['is_multiple'] == 0){
						$darray[$jet_data['JE'][$dkey]['name']] = $dvalue;	
					}
					else{
						$exp =explode(',',$dvalue);
						$dselect = $exp;
						$darray[$jet_data['JE'][$dkey]['name']] = $dselect;
					}
				}
				elseif($jet_data['JE'][$dkey]['type'] == 'posts'){
					global $wpdb;
				    if($jet_data['JE'][$dkey]['is_multiple'] == 0){
						$jet_posts = trim($dvalue);
						//$jet_posts = $wpdb->_real_escape($jet_posts);
						if(is_string($jet_posts)){
							$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$jet_posts}' AND post_status='publish'";
							$name = $wpdb->get_results($query);
							if (!empty($name)) {
								$jet_posts_value=$name[0]->id;
							}
						}
						elseif (is_numeric($jet_posts)) {
							$jet_posts_value=$jet_posts;
						}

					}
					else{
						$jet_posts_exp = explode(',',trim($dvalue));
						$jet_posts_value = array();
						foreach($jet_posts_exp as $jet_posts_value){
							$jet_posts_value = trim($jet_posts_value);
							$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$jet_posts_value}' AND post_status!='trash' ORDER BY ID DESC";
							$multiple_id = $wpdb->get_results($query);
							$multiple_ids =$multiple_id[0];
								if(!$multiple_id){
								$jet_posts_field_value[]=$jet_posts_value;
							}
							else{
								$jet_posts_field_value[]=trim($multiple_ids->id);
							}
					
						}
	
					}
					$darray[$jet_data['JE'][$dkey]['name']] = $jet_posts_field_value;
				}
				else{
					if($jet_data['JE'][$dkey]['type'] != 'repeater'){
						$darray[$jet_data['JE'][$dkey]['name']] = $dvalue;
					}
				}
				$listTaxonomy = get_taxonomies();
				if($darray){
					if($type == 'Users'){
						foreach($darray as $mkey => $mval){
							update_user_meta($pID, $mkey, $mval);
						}
					}
					elseif(in_array($type, $listTaxonomy)){
						foreach($darray as $mkey => $mval){
							update_term_meta($pID, $mkey, $mval);
						}
					}
					else{
						foreach($darray as $mkey => $mval){
							update_post_meta($pID, $mkey, $mval);
						}
					}
					
				}
			}
		}
	}


	public function jet_engine_rf_import_function($data_array, $type, $pID ,$mode) 
	{
		$media_instance = MediaHandling::getInstance();
		$jet_rf_data = $this->JetEngineRFFields($type);
		foreach ($data_array as $dkey => $dvalue) {
			$dvalue =trim($dvalue);
			$dvaluexp = explode( '|', $dvalue);
            foreach($dvaluexp  as $dvalueexpkey => $dvalues){
				$array = [];
				$item = 'item-'.$dvalueexpkey;
				$gallery_ids = '';
				$media_ids = '';
				if(array_key_exists($dkey,$jet_rf_data['JERF'])){
					if($jet_rf_data['JERF'][$dkey]['type'] == 'gallery' || $jet_rf_data['JERF'][$dkey]['type'] == 'media'){
						$exploded_gallery_items = explode( ',', $dvalues );
						foreach ( $exploded_gallery_items as $gallery ) {
							$gallery = trim( $gallery );
							if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
								$get_gallery_id = $media_instance->media_handling( $gallery, $pID);	
								$media_id = $media_instance->media_handling( $gallery, $pID);
								if ( $get_gallery_id != '' ) {
									 if($jet_rf_data['JERF'][$dkey]['type'] == 'media'){
										$media_ids .= $media_id. ',';
									 }
									 else{
										$gallery_ids .= $get_gallery_id.',';
									 }
								}
							} else {
								$galleryLen         = strlen( $gallery );
								$checkgalleryid     = intval( $gallery );
								$verifiedGalleryLen = strlen( $checkgalleryid );
								if ( $galleryLen == $verifiedGalleryLen ) {
									if($jet_rf_data['JERF'][$dkey]['type'] == 'media'){
										$media_ids .= $gallery. ',';
									}
									else{
										$gallery_ids .= $gallery. ',';
									}
									
								}
							}
						}
						$gallery_id = rtrim($gallery_ids,',');
						$media_id = rtrim($media_ids,',');
						if($jet_rf_data['JERF'][$dkey]['type'] == 'media'){
							$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $media_id;
						}
						else{
							$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $gallery_id;
						}
					}
					elseif($jet_rf_data['JERF'][$dkey]['type'] == 'datetime-local'){
						$dt_var = trim($dvalues);
						$date_time_of = date("Y-m-d\TH:i", strtotime($dt_var) );
						$date = date_format($date_time_of,"Y-m-d");
						$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $date_time_of;
						
					}
					elseif($jet_rf_data['JERF'][$dkey]['type'] == 'date'){
						$var = trim($dvalues);
						$date = str_replace('/', '-', "$var");
						$date_of = date('Y-m-d', strtotime($date));
						$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $date_of;
					}
					elseif($jet_rf_data['JERF'][$dkey]['type'] == 'time'){
						$var = trim($dvalues);
						$time = date('H:i', strtotime($var));
						$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $time;
					}
					elseif($jet_rf_data['JERF'][$dkey]['type'] == 'checkbox'){
						$options = $jet_rf_data['JERF'][$dkey]['options'];
						$arr = [];
						$opt = [];
						$dvalexp = explode(',' , $dvalues);
						foreach($options as $option_key => $option_val){
							//$opt[$option_key]	= $option_val['key'];
							$arr[$option_val['key']] = 'false';
						}
						foreach($dvalexp as $dvalkey => $dvalueval){
							$keys = array_keys($arr);
							foreach($keys as $keys1){
								if($dvalueval == $keys1){
									$arr[$keys1] = 'true';
								}
							}
						}
						$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $arr;
					}
					elseif($jet_rf_data['JERF'][$dkey]['type'] == 'select'){
						$dselect = [];
						if($jet_rf_data['JERF'][$dkey]['is_multiple'] == 0){
							$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $dvalues;	
						}
						else{
							$exp =explode(',',$dvalues);
							$dselect = $exp;
							$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $dselect;
						}
					}
					elseif($jet_rf_data['JERF'][$dkey]['type'] == 'posts'){
						global $wpdb;
						if($jet_rf_data['JERF'][$dkey]['is_multiple'] == 0){
							$jet_posts = trim($dvalues);
							//$jet_posts = $wpdb->_real_escape($jet_posts);
							if(is_string($jet_posts)){
								$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$jet_posts}' AND post_status='publish'";
								$name = $wpdb->get_results($query);
								if (!empty($name)) {
									$jet_posts_field_value=$name[0]->id;
								}
							}
							elseif (is_numeric($jet_posts)) {
								$jet_posts_field_value=$jet_posts;
							}
	
						}
						else{
							$jet_posts_field_value = [];
							$jet_posts_exp = explode(',',trim($dvalues));
							$jet_posts_value = array();
							foreach($jet_posts_exp as $jet_posts_value){
								$jet_posts_value = trim($jet_posts_value);
								$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$jet_posts_value}' AND post_status!='trash' ORDER BY ID DESC";
								$multiple_id = $wpdb->get_results($query);
								$multiple_ids =$multiple_id[0];
								if(!$multiple_id){
									$jet_posts_field_value[]=$jet_posts_value;
								}
								else{
									$jet_posts_field_value[]=trim($multiple_ids->id);
								}
						
							}
		
						}
						$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $jet_posts_field_value;
					}
					else{
						$dvalues = trim($dvalues);
						$darray[$item][$jet_rf_data['JERF'][$dkey]['name']] = $dvalues;
					}
					$repfield =$jet_rf_data['JE'];
					$listTaxonomy = get_taxonomies();
					if($type == 'Users'){
						foreach($repfield as $rep_fkey => $rep_fvalue){
							update_user_meta($pID, $rep_fvalue['name'], $darray);
						}
					}
					elseif(in_array($type, $listTaxonomy)){	
						foreach($repfield as $rep_fkey => $rep_fvalue){
							update_term_meta($pID, $rep_fvalue['name'], $darray);
						}
					}
					else{
						foreach($repfield as $rep_fkey => $rep_fvalue){
							update_post_meta($pID, $rep_fvalue['name'], $darray);
						}
					}
				}

			}

		
	    }
	}


	public function JetEngineFields($type){
		global $wpdb;	
		$jet_field = array();


		$get_meta_box_fields = $wpdb->get_results( $wpdb->prepare("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name='jet_engine_meta_boxes'"));
		$unserialized_meta = maybe_unserialize($get_meta_box_fields[0]->option_value);
		$count =count($unserialized_meta);
		for($i=1 ; $i<=$count ; $i++){
			$fields = $unserialized_meta['meta-'.$count]['meta_fields'];
			foreach($fields as $jet_key => $jet_value){
				$customFields["JE"][ $jet_value['name']]['label'] = $jet_value['title'];
				$customFields["JE"][ $jet_value['name']]['name']  = $jet_value['name'];
				$customFields["JE"][ $jet_value['name']]['type']  = $jet_value['type'];
				$customFields["JE"][ $jet_value['name']]['options'] = $jet_value['options'];
				$customFields["JE"][ $jet_value['name']]['is_multiple'] = $jet_value['is_multiple'];
				//$customFields["JECPT"][ $jet_value['name']]['repeater-fields'] = $jet_value['repeater-fields'];
			}
		}	
		return $customFields;
	}

	public function JetEngineRFFields($type){
		global $wpdb;	
		$jet_field = array();
		$get_meta_box_fields = $wpdb->get_results( $wpdb->prepare("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name='jet_engine_meta_boxes'"));
		$unserialized_meta = maybe_unserialize($get_meta_box_fields[0]->option_value);
		$count =count($unserialized_meta);
		for($i=1 ; $i<=$count ; $i++){
			$fields = $unserialized_meta['meta-'.$count]['meta_fields'];
			foreach($fields as $jet_key => $jet_value){
				$types  = $jet_value['type'];
				if($types == 'repeater'){
					$customFields["JE"][ $jet_value['name']]['name']  = $jet_value['name'];
					$fields=$jet_value['repeater-fields'];
					foreach($fields as $rep_fieldkey => $rep_fieldvalue){
						$customFields["JERF"][ $rep_fieldvalue['name']]['label'] = $rep_fieldvalue['title'];
						$customFields["JERF"][ $rep_fieldvalue['name']]['name']  = $rep_fieldvalue['name'];
						$customFields["JERF"][ $rep_fieldvalue['name']]['type']  = $rep_fieldvalue['type'];
						$customFields["JERF"][ $rep_fieldvalue['name']]['options']  = $rep_fieldvalue['options'];
						$customFields["JERF"][ $rep_fieldvalue['name']]['is_multiple']  = $rep_fieldvalue['is_multiple'];
						$jet_rf_field[] = $rep_fieldvalue['name'];

					}

				}
			}
		}	
		return $customFields;

	}
}