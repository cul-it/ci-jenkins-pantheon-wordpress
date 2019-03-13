<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
class SmackUCIACFDataImport {

	/**
	 * Function to push the acf field information
	 *
	 */
	public function push_acf_data($data_to_import, $duplicateHandling, $mediaConfig) {
		global $uci_admin;
		$data_array = $data_to_import;
		if (!empty($data_array['ACF'])) {
			if (in_array('advanced-custom-fields-pro/acf.php', $uci_admin->get_active_plugins())) {
				$this->importDataForACFProFields($data_array['ACF'], $uci_admin->getImportAs(), $uci_admin->getLastImportId(),$duplicateHandling, $mediaConfig);
			} else {
				if (in_array('advanced-custom-fields/acf.php', $uci_admin->get_active_plugins())) {
					$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
					if(is_dir($acf_pluginPath)) {
						$this->importDataForACFProFields($data_array['ACF'], $uci_admin->getImportAs(), $uci_admin->getLastImportId(),$duplicateHandling, $mediaConfig);
					}
					else
						$this->importDataForACFFields($data_array['ACF'], $uci_admin->getImportAs(), $uci_admin->getLastImportId(),$duplicateHandling, $mediaConfig);
				}
			}
		} if (!empty($data_array['RF'])) {
			if(in_array('advanced-custom-fields/acf.php',$uci_admin->get_active_plugins())) {
				$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
				if(is_dir($acf_pluginPath)) {
					$this->importDataForACFPRORepeaterFields($data_array['RF'], $uci_admin->getImportAs(), $uci_admin->getLastImportId());
				}
			}
			if (in_array('advanced-custom-fields-pro/acf.php', $uci_admin->get_active_plugins())) {
				$this->importDataForACFPRORepeaterFields($data_array['RF'], $uci_admin->getImportAs(), $uci_admin->getLastImportId());
			} else if (in_array('acf-repeater/acf-repeater.php', $uci_admin->get_active_plugins())) {
				$this->importDataForACFRepeaterFields($data_array['RF'], $uci_admin->getImportAs(), $uci_admin->getLastImportId());
			}
		}
	}

	public function importDataForACFProFields($data_array, $importAs, $pID,$duplicateHandling, $mediaConfig) {
		global $uci_admin;
		$plugininfo = get_plugin_data( WP_PLUGIN_DIR .'/'.'advanced-custom-fields-pro/acf.php');
		$versionOfAcf = $plugininfo['Version'];
		$term_meta = 'no';
		if($versionOfAcf >= 5.6)
			$term_meta = 'yes';
		$acf_field = $acf_type = $createdFields = $acf_image_import_method = array();
		global $wpdb;
		$get_acf_fields = $wpdb->get_results($wpdb->prepare("SELECT post_content, post_excerpt, post_name FROM $wpdb->posts where post_type = %s", 'acf-field'), ARRAY_A);
		$acf_value = $get_acf_fields;
		if (is_array($acf_value) && !empty($acf_value)) {
			foreach ($acf_value as $value) {
				$get_acf_field = unserialize($value['post_content']);
				$acf_field[$value['post_name']] = $value['post_excerpt'];
				$acf_type[$value['post_excerpt']] = $get_acf_field['type'];
				if ($get_acf_field['type'] == 'image') {
					$acf_image_import_method[$value['post_name']] = $get_acf_field['return_format'];
				}
				if ($get_acf_field['type'] == 'file') {
					$acf_file_import_method[$value['post_name']] = $get_acf_field['return_format'];
				}
				if ($get_acf_field['type'] == 'message') {
					$acf_field[$value->post_name] = $value->post_name;
					$acf_type[$value->post_name] = $value->post_name;
				}
			}
			$media_handle = array();
	                $shortcodes = '';
			$media_handle = isset($duplicateHandling['media_handling']) ? $duplicateHandling['media_handling'] : '';
			foreach ($acf_field as $key => $value) {
				if (array_key_exists($value, $data_array)) {
					if ($acf_type[$value] == $value) {
						foreach ($acf_value as $val) {
							$get_acf_field = unserialize($val->post_content);
							if ($get_acf_field['type'] == 'message') {
								$get_acf_field['message'] = $data_array[$value];
								$get_acf_field = serialize($get_acf_field);
								$updt_query = "update $wpdb->posts set post_content ='$get_acf_field' where post_name = '$value'";
								$wpdb->query($updt_query);
							}
						}
					}
					// Start of post object 
					if ($acf_type[$value] == 'post_object') {
					
					$data_value = explode(',', $data_array[$value]);
					$data_array[$value] = serialize($data_value);

					}
if ($acf_type[$value] == 'select') {

						$data_array[$value] = explode(',', $data_array[$value]);
					}
					if ($acf_type[$value] == 'page_link') {

						$data_array[$value] = explode(',', $data_array[$value]);
					}
					// End of post object
					if ($acf_type[$value] == 'checkbox') {
						$data_array[$value] = explode(',', $data_array[$value]);
					}
					if ($acf_type[$value] == 'image') {
						if ($acf_image_import_method[$key] == 'url') {
							#TODO image, file fields
							$data_array[$value] = trim($data_array[$value]);
							$data_array[$value] = $uci_admin->set_featureimage($data_array[$value], $pID, $media_handle);
						}
					}
					if($acf_type[$value] == 'file') {
						if ($acf_file_import_method[$key] == 'url') {
							#TODO image, file fields
							$data_array[$value] = trim($data_array[$value]);
							$data_array[$value] = $uci_admin->set_featureimage($data_array[$value], $pID);
						}
					}
					if ($acf_type[$value] == 'gallery') {
						#TODO gallery fields
						$gallery_ids = '';
						$exploded_gallery_items = explode(',', $data_array[$value]);
						foreach($exploded_gallery_items as $gallery) {
							$gallery = trim($gallery);
							if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery,$matched_gallerylist,PREG_PATTERN_ORDER)){
								$get_gallery_id = $uci_admin->set_featureimage($gallery, $pID);
								if($get_gallery_id != '') {
									$gallery_ids[] = $get_gallery_id;
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
						$data_array[$value] = $gallery_ids;
					}
					if ($acf_type[$value] == 'google_map') {
						$location = $data_array[$value];
						$lat_long = $uci_admin->get_latitude_longitude($location);
						$lat_long1 = explode(',', $lat_long);
						//if(!empty($lat_long1[0]) && !empty($lat_long1[1])){
							$map = array('address' => $location, 'lat' => $lat_long1[0], 'lng' => $lat_long1[1]);
							$data_array[$value] = $map;
						//}
					}
					if ($acf_type[$value] == 'wysiwyg') {
						$fieldname = $data_array[$value];
						$data_array[$value] = $fieldname;
					}
					if ($acf_type[$value] == 'relationship' || $acf_type[$value] == 'taxonomy') {
						$relations = array();
						$check_is_valid_term = null;
						$get_relations = $data_array[$value];
						if(!empty($get_relations)){
							$exploded_relations = explode(',', $get_relations);
							foreach ($exploded_relations as $relVal) {
								$relationTerm = trim($relVal);
								if ($acf_type[$value] == 'taxonomy') {
									$check_is_valid_term = $uci_admin->get_requested_term_details($pID, $relationTerm);
									$relations[] = $check_is_valid_term;
								} else {
									$reldata = strlen($relationTerm);
									$checkrelid = intval($relationTerm);
									$verifiedRelLen = strlen($checkrelid);
									if ($reldata == $verifiedRelLen) {
										$relations[] = $relationTerm;
									} else {
										$relation_id = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_title = %s",$relVal));
										if (!empty($relation_id)) {
											$relations[] = $relation_id[0];
										}
									}
								}
							}
						}
						$data_array[$value] = $relations;
					}
					if ($acf_type[$value] == 'user'){
						$user_data = explode(",", $data_array[$value]);
			                        foreach ($user_data as $userKey => $uservalue) {
							$userDet = $uci_admin->get_from_user_details($uservalue);
							$data_array1[$userKey] = $userDet['user_id'];
						}
						$data_array[$value] = $data_array1;
					}
					$createdFields[] = $value;
					if ($importAs == 'users') {
						update_user_meta($pID, $value, $data_array[$value]);
						update_user_meta($pID, '_' . $value, $key);
					} else {
						//update_post_meta($pID, $data_array['groupfield_slug'].'_'.$value, $data_array[$value]);
                                                  update_post_meta($pID, $value, $data_array[$value]);
						update_post_meta($pID, '_' . $value, $key);
					}
					$listTaxonomy = get_taxonomies();
					if (in_array($importAs, $listTaxonomy)) {
						if($term_meta = 'yes'){
							add_term_meta($pID, $value, $data_array[$value]);
						}else{
							$option_name = $importAs . "_" . $pID . "_" . $value;
							$option_value = $data_array[$value];
							if (is_array($option_value)) {
								$option_value = serialize($option_value);
							}
							update_option("$option_name", "$option_value");
						}
					}
				}
			}
		}
		return $createdFields;
	}

	public function importDataForACFFields($data_array, $importAs, $pID,$duplicateHandling, $mediaConfig) {


 global $uci_admin;
                $plugininfo = get_plugin_data( WP_PLUGIN_DIR .'/'.'advanced-custom-fields-pro/acf.php');
                $versionOfAcf = $plugininfo['Version'];
                $term_meta = 'no';
                if($versionOfAcf >= 5.6)
                        $term_meta = 'yes';
                $acf_field = $acf_type = $createdFields = $acf_image_import_method = array();
                global $wpdb;
                $get_acf_fields = $wpdb->get_results($wpdb->prepare("SELECT post_content, post_excerpt, post_name FROM $wpdb->posts where post_type = %s", 'acf-field'), ARRAY_A);
                $acf_value = $get_acf_fields;
                if (is_array($acf_value) && !empty($acf_value)) {
                        foreach ($acf_value as $value) {
                                $get_acf_field = unserialize($value['post_content']);
                                $acf_field[$value['post_name']] = $value['post_excerpt'];
                                $acf_type[$value['post_excerpt']] = $get_acf_field['type'];
                                if ($get_acf_field['type'] == 'image') {
                                        $acf_image_import_method[$value['post_name']] = $get_acf_field['return_format'];
                                }
                                if ($get_acf_field['type'] == 'file') {
                                        $acf_file_import_method[$value['post_name']] = $get_acf_field['return_format'];
                                }
                                if ($get_acf_field['type'] == 'message') {
                                        $acf_field[$value->post_name] = $value->post_name;
                                        $acf_type[$value->post_name] = $value->post_name;
                                }
                        }




$media_handle = array();
                        $shortcodes = '';
                        $media_handle = isset($duplicateHandling['media_handling']) ? $duplicateHandling['media_handling'] : '';
                        foreach ($acf_field as $key => $value) {
                                if (array_key_exists($value, $data_array)) {
                                        if ($acf_type[$value] == $value) {
                                                foreach ($acf_value as $val) {
                                                        $get_acf_field = unserialize($val->post_content);
                                                        if ($get_acf_field['type'] == 'message') {
                                                                $get_acf_field['message'] = $data_array[$value];
                                                                $get_acf_field = serialize($get_acf_field);
                                                                $updt_query = "update $wpdb->posts set post_content ='$get_acf_field' where post_name = '$value'";
                                                                $wpdb->query($updt_query);
                                                        }
                                                }
                                        }




// Start of post object 
                                        if ($acf_type[$value] == 'post_object') {

                                        $data_value = explode(',', $data_array[$value]);
                                        $data_array[$value] = serialize($data_value);

                                        }
if ($acf_type[$value] == 'select') {

                                                $data_array[$value] = explode(',', $data_array[$value]);
                                        }
                                        if ($acf_type[$value] == 'page_link') {

                                                $data_array[$value] = explode(',', $data_array[$value]);
                                        }
                                        // End of post object
                                        if ($acf_type[$value] == 'checkbox') {
                                                $data_array[$value] = explode(',', $data_array[$value]);
                                        }
                                        if ($acf_type[$value] == 'image') {
                                                if ($acf_image_import_method[$key] == 'url') {
                                                        #TODO image, file fields
                                                        $data_array[$value] = trim($data_array[$value]);
                                                        $data_array[$value] = $uci_admin->set_featureimage($data_array[$value], $pID, $media_handle);
                                                }
                                        }
                                        if($acf_type[$value] == 'file') {
                                                if ($acf_file_import_method[$key] == 'url') {
                                                        #TODO image, file fields
                                                        $data_array[$value] = trim($data_array[$value]);
                                                        $data_array[$value] = $uci_admin->set_featureimage($data_array[$value], $pID);
                                                }
                                        }





if ($acf_type[$value] == 'gallery') {
                                                #TODO gallery fields
                                                $gallery_ids = '';
                                                $exploded_gallery_items = explode(',', $data_array[$value]);
                                                foreach($exploded_gallery_items as $gallery) {
                                                        $gallery = trim($gallery);
                                                        if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery,$matched_gallerylist,PREG_PATTERN_ORDER)){
                                                                $get_gallery_id = $uci_admin->set_featureimage($gallery, $pID);
                                                                if($get_gallery_id != '') {
                                                                        $gallery_ids[] = $get_gallery_id;
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
                                                $data_array[$value] = $gallery_ids;
                                        }
                                        if ($acf_type[$value] == 'google_map') {
                                                $location = $data_array[$value];
                                                $lat_long = $uci_admin->get_latitude_longitude($location);
                                                $lat_long1 = explode(',', $lat_long);
                                                //if(!empty($lat_long1[0]) && !empty($lat_long1[1])){
                                                        $map = array('address' => $location, 'lat' => $lat_long1[0], 'lng' => $lat_long1[1]);
                                                        $data_array[$value] = $map;
                                                //}
                                        }
                                        if ($acf_type[$value] == 'wysiwyg') {
                                                $fieldname = $data_array[$value];
                                                $data_array[$value] = $fieldname;
                                        }

if ($acf_type[$value] == 'relationship' || $acf_type[$value] == 'taxonomy') {
                                                $relations = array();
                                                $check_is_valid_term = null;
                                                $get_relations = $data_array[$value];
                                                if(!empty($get_relations)){
                                                        $exploded_relations = explode(',', $get_relations);
                                                        foreach ($exploded_relations as $relVal) {
                                                                $relationTerm = trim($relVal);
                                                                if ($acf_type[$value] == 'taxonomy') {
                                                                        $check_is_valid_term = $uci_admin->get_requested_term_details($pID, $relationTerm);
                                                                        $relations[] = $check_is_valid_term;
                                                                } else {
                                                                        $reldata = strlen($relationTerm);
                                                                        $checkrelid = intval($relationTerm);
                                                                        $verifiedRelLen = strlen($checkrelid);
                                                                        if ($reldata == $verifiedRelLen) {
                                                                                $relations[] = $relationTerm;
                                                                        } else {
                                                                                $relation_id = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_title = %s",$relVal));
                                                                                if (!empty($relation_id)) {
                                                                                        $relations[] = $relation_id[0];
                                                                                }
                                                                        }
                                                                }
                                                        }
                                                }
                                                $data_array[$value] = $relations;
                                        }
                                        if ($acf_type[$value] == 'user'){
                                                $user_data = explode(",", $data_array[$value]);
                                                foreach ($user_data as $userKey => $uservalue) {
                                                        $userDet = $uci_admin->get_from_user_details($uservalue);
                                                        $data_array1[$userKey] = $userDet['user_id'];
                                                }
                                                $data_array[$value] = $data_array1;
                                        }
                                        $createdFields[] = $value;


if ($importAs == 'users') {
                                                update_user_meta($pID, $value, $data_array[$value]);
                                                update_user_meta($pID, '_' . $value, $key);
                                        } else {
                                                //update_post_meta($pID, $data_array['groupfield_slug'].'_'.$value, $data_array[$value]);
                                                  update_post_meta($pID, $value, $data_array[$value]);
                                                update_post_meta($pID, '_' . $value, $key);
                                        }
                                        $listTaxonomy = get_taxonomies();
                                        if (in_array($importAs, $listTaxonomy)) {
                                                if($term_meta = 'yes'){
                                                        add_term_meta($pID, $value, $data_array[$value]);
                                                }else{
                                                        $option_name = $importAs . "_" . $pID . "_" . $value;
                                                        $option_value = $data_array[$value];
                                                        if (is_array($option_value)) {
                                                                $option_value = serialize($option_value);
                                                        }
                                                        update_option("$option_name", "$option_value");
                                                }
                                        }
                                }
                        }
                }
                return $createdFields;




}
	public function getMetaKeyOfRepeaterField($pID, $field_name, $key = 0, $fKey = 0, $parents = array(), $meta_key = '') {
		global $wpdb;
		$get_field_details  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent, post_excerpt from $wpdb->posts where post_excerpt = %s", $field_name ) );
		if(empty($parents) && $field_name == $get_field_details[0]->post_excerpt) {
			$parents[] = $fKey . '_' . $field_name . '_';
			$meta_key .= $fKey . '_' . $field_name . '_';
		}
		$get_repeater_parent_field = $wpdb->get_results( $wpdb->prepare( "select post_content, post_name, post_excerpt, post_parent from $wpdb->posts where ID = %d", $get_field_details[0]->post_parent ) );
		$field_info           = unserialize( $get_repeater_parent_field[0]->post_content );
		if(isset($field_info['type']) && $field_info['type'] == 'repeater' && $get_repeater_parent_field[0]->post_parent != 0) {
			$parents[] = $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_';
			$meta_key .= $key . '_' . $get_repeater_parent_field[0]->post_excerpt . '_' . $meta_key;
			$meta_key = $this->getMetaKeyOfRepeaterField($pID, $get_repeater_parent_field[0]->post_excerpt, 0, 0, $parents, $meta_key);
		} else {
			if(!empty($parents)) {
				$meta_key = '';
				for($i = count($parents); $i >= 0 ; $i--) {
					$meta_key .= $parents[$i];
				}
			}
			$meta_key = substr($meta_key, 2);
			$meta_key = substr($meta_key, 0, -1);
			return $meta_key;
		}
		return $meta_key;
	}

	public function importDataForACFPRORepeaterFields($data_array, $importAs, $pID) {
		global $wpdb, $uci_admin;
		$plugininfo = get_plugin_data( WP_PLUGIN_DIR .'/'.'advanced-custom-fields-pro/acf.php');
		$versionOfAcf = $plugininfo['Version'];
		$term_meta = 'no';
		if($versionOfAcf >= 5.6)
			$term_meta = 'yes';
		$grpid = '';
		$createdFields = $rep_parent_fields = $repeater_fields = $repeater_flexible_content_import_method = array();
		foreach($data_array as $repKey => $repVal) {
			$i = 0;

			// Prepare the meta array by field type
			$get_field_info  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from $wpdb->posts where post_excerpt = %s", $repKey ) );
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
//			$repVal = "1->2|3->4|5->6->7";//FOR mulitple repeater ( nested repeater )	
			// Parse values if have any multiple values
			$repeater_field_rows = explode('|', $repVal);
			$j = 0;
			foreach($repeater_field_rows as $index => $value) {
				$repeater_field_values = explode('->', $value);
				$checkCount = count($repeater_field_values);
				foreach($repeater_field_values as $key => $val) {
					if($checkCount > 1)
					$rep_field_meta_key = $this->getMetaKeyOfRepeaterField( $pID, $repKey, $index, $key );
					else
					$rep_field_meta_key = $this->getMetaKeyOfRepeaterField( $pID, $repKey, $i, $j );
	
					if($rep_field_meta_key[0] == '_')
						$rep_field_meta_key = substr($rep_field_meta_key, 1);
					$rep_field_parent_key = explode( '_' . $repKey, $rep_field_meta_key );
					$rep_field_parent_key = substr( $rep_field_parent_key[0], 0, - 2 );
					if (substr($rep_field_parent_key, -1) == "_") {
						$rep_field_parent_key = substr($rep_field_parent_key, 0, -1);
					}
					$super_parent = explode('_'.$index.'_',$rep_field_parent_key);
					$rep_parent_fields[$super_parent[0]] = count($repeater_field_rows);
					if($checkCount > 1)
					$rep_parent_fields[$rep_field_parent_key] = $key + 1;
					else
					$rep_parent_fields[$rep_field_parent_key] = $i + 1;
					$j++;

					// Push meta information into WordPress
                                            if($repeater_sub_field_type[$repeater_fields[$repKey]] == 'post_object') {
                       $acf_rep_field_info[$rep_field_meta_key]=explode(',',trim($val));
                   $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}
  elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'select') {
  $acf_rep_field_info[$rep_field_meta_key]=explode(',',trim($val));
  $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}
elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'page_link') {

                          $acf_rep_field_info[$rep_field_meta_key]=explode(',',trim($val));
                       $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}
                elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'user') {
                        $acf_rep_field_info[$rep_field_meta_key]=explode(',',trim($val));
                       $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
                 }

elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'true_false') {
                          $acf_rep_field_info[$rep_field_meta_key]=trim($val);
                       $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
                     }
 elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'button_group') {
   $acf_rep_field_info[$rep_field_meta_key]=trim($val);
   $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];

 }
elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'radio') {
   $acf_rep_field_info[$rep_field_meta_key]=trim($val);
  $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];

  }
elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'link') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
  }

elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'date_picker') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

  elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'date_time_picker') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

  elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'time_picker') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

 elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'color_picker') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}  

elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'text') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

  elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'textarea') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'number') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'range') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}


elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'email') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'url') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}

elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'password') {

             $acf_rep_field_info[$rep_field_meta_key] =trim($val);
              $acf_rep_field_info['_'.$rep_field_meta_key]=$repeater_fields[$repKey];
}




					if($repeater_sub_field_type[$repeater_fields[$repKey]] == 'checkbox') {
						$acf_rep_field_info[$rep_field_meta_key] = explode(',', trim($val));
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					} elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'gallery') {
						$gallery_ids = '';
						if ( is_array( $gallery_ids ) ) {
							unset( $gallery_ids );
							$gallery_ids = array();
						}
						$exploded_gallery_items = explode( ',', $val );
						foreach ( $exploded_gallery_items as $gallery ) {
							$gallery = trim( $gallery );
							if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
								$get_gallery_id = $uci_admin->set_featureimage( $gallery, $pID,'');
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
					} elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'google_map') {
						$location = trim($val);
						$lat_long = $uci_admin->get_latitude_longitude($location);
						$lat_long1 = explode(',', $lat_long);
						$map = array(
							'address' => $location,
							'lat'     => $lat_long1[0],
							'lng'     => $lat_long1[1]
						);
						$acf_rep_field_info[$rep_field_meta_key] = $map;
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					} elseif($repeater_sub_field_type[$repeater_fields[$repKey]] == 'relationship' || $repeater_sub_field_type[$repeater_fields[$repKey]] == 'taxonomy') {
						$exploded_relations = $relations = array();
						$exploded_relations = explode(',', $val);
						foreach($exploded_relations as $relVal) {
							$relationTerm = trim( $relVal );
							if ( $repeater_sub_field_type[$repeater_fields[$repKey]] == 'taxonomy' ) {
								$taxonomy_name       = substr( $repKey, 4 );
								$check_is_valid_term = $uci_admin->get_requested_term_details( $pID, $relationTerm, $taxonomy_name );
								$relations[]         = $check_is_valid_term;
							} else {
								$relations[] = $relationTerm;
							}
						}
						$acf_rep_field_info[$rep_field_meta_key] = $relations;
						$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					} else { // Other type of fields
						//$acf_rep_field_info[$rep_field_meta_key] = trim($val);
						//$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
					}
					if($repeater_image_import_method[$repeater_fields[$repKey]] == 'url') {
						$image_link = trim($val);
						if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link)){
							$acf_rep_field_info[$rep_field_meta_key] = $uci_admin->set_featureimage( $image_link, $pID ,'');
							$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
						} else {
							$acf_rep_field_info[$rep_field_meta_key] = $image_link;
							$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
						}
					} 
					if($repeater_file_import_method[$repeater_fields[$repKey]] == 'url') {
						$image_link = trim($val);
						if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link)){
							$acf_rep_field_info[$rep_field_meta_key] = $uci_admin->set_featureimage( $image_link, $pID );
							$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
						} else {
							$acf_rep_field_info[$rep_field_meta_key] = $image_link;
							$acf_rep_field_info['_'.$rep_field_meta_key] = $repeater_fields[$repKey];
						}
					} 

				}
				$i++;
			}
			if(!empty($acf_rep_field_info)) {
				foreach($acf_rep_field_info as $fName => $fVal) {

					$listTaxonomy = get_taxonomies();
				    if (in_array($importAs, $listTaxonomy)) {
				    	if($term_meta = 'yes'){
							add_term_meta($pID, $fName, $fVal);
						}else{
						$option_name = $importAs . "_" . $pID . "_" . $fName;
						$option_value = $fVal;
						if (is_array($option_value)) {
							$option_value = serialize($option_value);
						}
						update_option("$option_name", "$option_value");
					    }
					}
					else
					update_post_meta($pID, $fName, $fVal);
				}
			}

			$createdFields[] = $repKey;
			$rep_fname = $repKey;
			$rep_fID   = $repeater_fields[$repKey];
			// Flexible Content
			$flexible_content = array();
			if ( array_key_exists( $rep_fID, $repeater_flexible_content_import_method ) && $repeater_flexible_content_import_method[ $rep_fID ] != null ) {
				$flexible_content[] = $repeater_flexible_content_import_method[ $rep_fID ];
				update_post_meta($pID, $rep_fname, $flexible_content);
			}
		}
		foreach($rep_parent_fields as $pKey => $pVal) {
			$listTaxonomy = get_taxonomies();
				    if (in_array($importAs, $listTaxonomy)) {
				    	if($term_meta = 'yes'){
							add_term_meta($pID, $pKey, $pVal);
						}else{
						$option_name = $importAs . "_" . $pID . "_" . $pKey;
						$option_value = $pVal;
						if (is_array($option_value)) {
							$option_value = serialize($option_value);
						}
						update_option("$option_name", "$option_value");
						}
					}
					else
					update_post_meta($pID, $pKey, $pVal);
		}
	}

	public function importDataForACFPRORepeaterFields_version51 ($data_array, $importAs, $pID) {
		global $wpdb, $uci_admin;
		$grpid = '';
		$createdFields = array();

		foreach($data_array as $repKey => $repVal) {
			$repeater_fieldname = $repeater_sub_fieldtype = $repeater_sub_fieldname = $get_repeater_field_name = $repeater_sub_fieldlabel = $fieldkey = array();
			$get_field_details  = $wpdb->get_results( $wpdb->prepare( "select ID, post_content, post_name, post_parent from $wpdb->posts where post_excerpt = %s", $repKey ) );
			foreach ( $get_field_details as $repFieldDet ) {
				$get_repeater_field_name = $wpdb->get_results( $wpdb->prepare( "select post_content, post_name, post_excerpt from $wpdb->posts where ID = %d", $repFieldDet->post_parent ) );
				foreach ( $get_repeater_field_name as $rep_field_det ) {
					$repeater_fieldname[ $repFieldDet->post_name ] = $rep_field_det->post_excerpt;
					$repeater_fieldID[ $repFieldDet->post_name ]   = $rep_field_det->post_name;
					$unserialized_flexiblecontent_fields           = unserialize( $rep_field_det->post_content );
					foreach ( $unserialized_flexiblecontent_fields as $contentkey => $contentval ) {
						if ( $contentval == 'flexible_content' ) {
							$repeater_flexible_content_import_method[ $rep_field_det->post_name ] = $unserialized_flexiblecontent_fields['layouts'][0]['name'];
						}
					}
				}
				$repeater_sub_fieldname[ $repFieldDet->post_name ] = $repFieldDet->post_name;
				$unserialized_repfields_content                    = unserialize( $repFieldDet->post_content );
				foreach ( $unserialized_repfields_content as $contentkey => $contentval ) {
					if ( $contentkey == 'type' ) {
						if ( $contentval == 'image' ) {
							$repeater_image_import_method[ $repFieldDet->post_name ] = $unserialized_repfields_content['return_format'];
						}
						if ( $contentval == 'file' ) {
							$repeater_file_import_method[ $repFieldDet->post_name ] = $unserialized_repfields_content['return_format'];
						}
						$repeater_sub_fieldtype[ $repFieldDet->post_name ] = $contentval;
					}
				}
				$repeater_sub_fieldlabel[ $repFieldDet->post_name ] = $repKey;
				$grpname = $repeater_fieldname[ $repFieldDet->post_name ];
				$grpid   = $repeater_fieldID[ $repFieldDet->post_name ];
				foreach ( $repeater_sub_fieldlabel as $fieldkey => $fieldname ) {
					if ( array_key_exists( $fieldname, $data_array ) ) {
						$multi_subfieldvalue      = explode( '|', $data_array[ $fieldname ] );
						$count_multisubfieldvalue = count( $multi_subfieldvalue );
						$get_postmeta             = get_post_meta( $pID, $grpname, false );
						update_post_meta( $pID, $grpname, $count_multisubfieldvalue );
						update_post_meta( $pID, '_' . $grpname, $grpid );
						if ( ! empty( $get_postmeta ) ) {
							if ( intval( $get_postmeta[0] ) > intval( $count_multisubfieldvalue ) ) {
								update_post_meta( $pID, $grpname, $get_postmeta[0] );
							}
						}
						foreach ( $multi_subfieldvalue as $repfieldkey => $repfieldval ) {
							if ( $repeater_sub_fieldtype[ $fieldkey ] == 'checkbox' ) {
								$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = explode( ',', $multi_subfieldvalue[ $repfieldkey ] );
								$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
							} else if ( $repeater_sub_fieldtype[ $fieldkey ] == 'image' ) {
								if ( $repeater_image_import_method[ $fieldkey ] == 'url' ) {
									$exploded_image_items = explode( ',', $multi_subfieldvalue[ $repfieldkey ] );
									foreach($exploded_image_items as $image_link) {
										$image_link = trim( $image_link );
										if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link ) ) {
											$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ]       = $uci_admin->set_featureimage( $image_link, $pID );
											$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
										} else {
											$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $image_link;
											$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
										}
									}
								}
							} else if ( $repeater_sub_fieldtype[ $fieldkey ] == 'file' ) {
								if ( $repeater_file_import_method[ $fieldkey ] == 'url' ) {
									$exploded_image_items = explode( ',', $multi_subfieldvalue[ $repfieldkey ] );
									foreach($exploded_image_items as $image_link) {
										$image_link = trim( $image_link );
										if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $image_link ) ) {
											$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ]       = $uci_admin->set_featureimage( $multi_subfieldvalue[ $repfieldkey ], $pID );
											$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
										} else {
											$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $image_link;
											$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
										}
									}
								}
							} else if ( $repeater_sub_fieldtype[ $fieldkey ] == 'gallery' ) {
								$gallery_ids = '';
								if ( is_array( $gallery_ids ) ) {
									unset( $gallery_ids );
									$gallery_ids = array();
								}
								$exploded_gallery_items = explode( ',', $multi_subfieldvalue[ $repfieldkey ] );
								foreach ( $exploded_gallery_items as $gallery ) {
									$gallery = trim( $gallery );
									if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $gallery ) ) {
										$get_gallery_id = $uci_admin->set_featureimage( $gallery, $pID );
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
								$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $gallery_ids;
								$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
							} else if ( $repeater_sub_fieldtype[ $fieldkey ] == 'google_map' ) {
								$location = $multi_subfieldvalue[ $repfieldkey ];
								$lat_long = $this->acf_get_lat_long( $location );
								$lat_long1 = explode( ',', $lat_long );
								$map = array(
									'address' => $location,
									'lat'     => $lat_long1[0],
									'lng'     => $lat_long1[1]
								);
								$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ]       = $map;
								$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
							} else if ( $repeater_sub_fieldtype[ $fieldkey ] == 'relationship' || $repeater_sub_fieldtype[ $fieldkey ] == 'taxonomy' ) {
								$exploded_relations = $relations = array();
								$get_relations      = null;
								$get_relations      = $multi_subfieldvalue[ $repfieldkey ];
								$exploded_relations = explode( ',', $get_relations );
								foreach ( $exploded_relations as $relVal ) {
									$relationTerm = trim( $relVal );
									if ( $repeater_sub_fieldtype[ $fieldkey ] == 'taxonomy' ) {
										$taxonomy_name       = substr( $fieldname, 4 );
										$check_is_valid_term = $this->get_requested_term_details( $pID, $relationTerm, $taxonomy_name );
										$relations[]         = $check_is_valid_term;
									} else {
										$relations[] = $relationTerm;
									}
								}
								$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ]       = $relations;
								$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
							} else {
								$repdata[ $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ]       = $multi_subfieldvalue[ $repfieldkey ];
								$repdata[ '_' . $grpname . '_' . $repfieldkey . '_' . $repeater_sub_fieldlabel[ $fieldkey ] ] = $fieldkey;
							}
							if ( ! empty( $repdata ) ) {
								foreach ( $repdata as $repmetakey => $repmetaval ) {
									update_post_meta($pID,$repmetakey,$repmetaval);
								}
							}
							$createdFields[] = $fieldname;
							$rep_fname = $grpname;
							$rep_fID   = $grpid;
							// Flexible Content
							$flexible_content = array();
							if ( array_key_exists( $rep_fID, $repeater_flexible_content_import_method ) && $repeater_flexible_content_import_method[ $rep_fID ] != null ) {
								$flexible_content[] = $repeater_flexible_content_import_method[ $rep_fID ];
								update_post_meta($pID, $rep_fname, $flexible_content);
							}
						}
					}
				}
			}
		}
	}

	public function importDataForACFRepeaterFields($data_array, $importAs, $pID) {
		global $wpdb, $uci_admin;
		$grpid = '';
		$repeater_fieldname = $repeater_sub_fieldtype = $repeater_sub_fieldname = $get_repeater_field_name = $repeater_sub_fieldlabel = $fieldkey = $createdFields = array();

		$get_repeater_fields = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts where post_type = %s",'acf'));

		foreach( $get_repeater_fields as $fieldGroupId ) {
			$get_field_details = $wpdb->get_col("select meta_value from $wpdb->postmeta where post_id = $fieldGroupId and meta_key like 'field_%'");
			foreach( $get_field_details as $repFieldDet ) {
				$repeaterFields = unserialize($repFieldDet);
				foreach( $repeaterFields as $fieldKey => $fieldVal ) {
					$repeater_fieldname[$repeaterFields['key']] = $repeaterFields['name'];
					if($fieldKey == 'sub_fields') {
						for($a=0; $a<count($fieldVal); $a++) {
							$repeater_sub_fieldname[$repeaterFields['key']][$fieldVal[$a]['key']] = $fieldVal[$a]['name'];
							$repeater_sub_fieldtype[$repeaterFields['key']][$fieldVal[$a]['key']] = $fieldVal[$a]['type'];
							$repeater_sub_fieldlabel[$repeaterFields['key']][$fieldVal[$a]['key']] = $fieldVal[$a]['label'];
							if($fieldVal[$a]['type'] == 'image' && $fieldVal[$a]['type'] == 'file') {
								$repeater_image_import_method[$repeaterFields['key']][$fieldVal[$a]['key']] = $fieldVal[$a]['save_format'];
							}
						}
					}
				}
			}
		}
		foreach ($repeater_fieldname as $fieldkey => $fieldname) {
			if(!empty($repeater_sub_fieldname[$fieldkey])) {

				foreach ($repeater_sub_fieldname[$fieldkey] as $sub_fieldkey => $sub_fieldname) {
					if(isset($data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]])) {
						$multi_subfieldvalue = explode('|', $data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]]);
						$count_multisubfieldvalue =  count($multi_subfieldvalue);
						update_post_meta($pID, $fieldname, $count_multisubfieldvalue);
						update_post_meta($pID, '_'.$fieldname, $fieldkey);
						foreach($multi_subfieldvalue as $multi_key => $multi_value){
							if ($repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'checkbox') {
								$data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]] = explode(',', $data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]]);
							}
							if ($repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'image' || $repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'file') {
								if($repeater_image_import_method[$fieldkey][$sub_fieldkey] == 'url') {
									$data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]] = $uci_admin->set_featureimage($data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]], $pID);
								}
							}
							if ($repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'google_map') {
								$location = $data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]];
								$lat_long = $this->acf_get_lat_long($location);
								$lat_long1 = explode(',', $lat_long);
								$map= array('address' => $location,'lat' => $lat_long1[0],'lng' => $lat_long1[1]);
								$data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]] = $map;
							}
							if ($repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'wysiwyg') {
								$fieldname = $data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]];
								$data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]] = $fieldname;
							}
							if ($repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'relationship' || $repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'taxonomy') {
								$exploded_relations = $relations = array();
								$get_relations = null;
								//$get_relations = $data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]];
								$get_relations = $multi_value;
								$exploded_relations = explode(',', $get_relations);
								foreach($exploded_relations as $relVal) {
									$relationTerm = trim($relVal);
									if($repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] == 'taxonomy') {
										$taxonomy_name = substr($sub_fieldname, 4);
										$check_is_valid_term = $this->get_requested_term_details($pID, $relationTerm, $taxonomy_name);
										$relations[] = $check_is_valid_term;
									} else {
										$relations[] = $relationTerm;
									}
								}
								$data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]] = $relations;
							}
							//$createdFields[] = $sub_fieldname;
							$createdFields[$sub_fieldname] = $sub_fieldname;
							if($repeater_sub_fieldtype[$fieldkey][$sub_fieldkey] != 'checkbox'){
								$data_array[$repeater_sub_fieldname[$fieldkey][$sub_fieldkey]] = $multi_value;
							}

							if(!empty($data_array[$sub_fieldname])){
								update_post_meta($pID, $fieldname.'_'.$multi_key.'_'.$sub_fieldname, $data_array[$sub_fieldname]);
								update_post_meta($pID, '_'.$fieldname.'_'.$multi_key.'_'.$sub_fieldname, $sub_fieldkey);
							}else{
								update_post_meta($pID, $fieldname.'_'.$multi_key.'_'.$sub_fieldname, '');
								update_post_meta($pID, '_'.$fieldname.'_'.$multi_key.'_'.$sub_fieldname, $sub_fieldkey);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Main Function for ACF Field Registration
	 * Register the ACF Pro Fields
	 * @param $field_info
	 * @param $type
	 *
	 * @return string
	 */
	public function Register_ProFields($field_info, $type = null) {
		global $uci_admin;
		$import_type = $uci_admin->import_post_types($field_info['import_type'], '');
		$id = $this->is_group_exist($import_type);
		if($id) {
			$duplicate_field = $this->is_duplicate($id, $field_info['field_info']['name']);
			if($duplicate_field) {
				if($type != null)
					return "ACF Fields are already registered.";
				print_r("ACF Fields are already registered.");
				die;
			} else {
				$fieldId = $this->append_ACF_field($field_info, $id);
				if($fieldId){
					if($type != null)
						return "ACF Field are added.";
					print_r("ACF Field are added.");
					die;
				} else {
					if($type != null)
						return "Error was occurred while adding the ACF Field.";
					print_r("Error was occurred while adding the ACF Field.");
					die;
				}
			}
		} else {
			if($type != null)
				return "ACF Groups are not exist.";
			print_r("ACF Groups are not exist.");
			die;
		}
	}

	/**
	 * @param $import_type
	 * @return int|string|WP_Error
	 * Check the ACF Feild Group is already exists
	 */
	public function is_group_exist($import_type) {
		global $wpdb;
		$groupKey_info = $wpdb->get_col($wpdb->prepare("select post_name from $wpdb->posts where post_type = %s",'acf-field-group'));
		$import_type_length = strlen($import_type);
		$groupKey_length = $import_type_length + 12;
		$group_data = '';
		foreach($groupKey_info as $groupKey) {
			if(substr($groupKey, 0, $groupKey_length) === 'group_smack_'.$import_type) {
				$group_data = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts where post_type = %s and post_name = %s",'acf-field-group',$groupKey));
				break;
			}
		}
		if(!empty($group_data)) {
			$id = implode("", $group_data);
			return $id;
		}
		else {
			$id = $this->createGroup($import_type);
			return $id;
		}
	}

	/**
	 * Create the ACF Group and return the ACF Group Id
	 * @param $import_type
	 * @return int|WP_Error
	 */
	public function createGroup($import_type) {
		$groupKey = uniqid('group_smack_'.$import_type);
		switch($import_type) {
			case 'users' : {
				$param = 'user_role';
				$loc = 'all';
				break;
			}
			case 'customtaxonomy' : {
				$param = 'taxonomy';
				$loc = 'all';
				break;
			}
			default : {
				$loc = $import_type;
				$param = 'post_type';
				break;
			}
		}
		$post_content = serialize(array('location' => array(array(array('param' => $param, 'operator' => '==', 'value' => $loc))),'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'instruction_placement' => 'label', 'hide_on_screen' =>  ''));
		$post_groupData = array('post_status' => 'publish', 'post_content' => $post_content, 'post_title' => 'ACF Pro: Custom Group for '.$import_type, 'post_excerpt' => 'group_smack_'.$import_type, 'comment_status' => 'closed', 'post_name' => $groupKey, 'post_type' => 'acf-field-group');
		$groupId = wp_insert_post($post_groupData);
		return $groupId;
	}

	/**
	 * Check whether the ACF Fields are already exists
	 * @param $groupId
	 * @param $field_name
	 * @return bool
	 */
	public function is_duplicate($groupId,$field_name) {
		global $wpdb;
		$field_id = $wpdb->get_col($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_parent = %d",$field_name,$groupId));
		if(!empty($field_id))
			return true;
		else
			return false;
	}

	public function append_ACF_field($fieldinfo,$groupId) {
		$fieldinfo = $fieldinfo['field_info'];
		$fieldKey = uniqid('field_');
		$field_choices = '';
		if(isset($fieldinfo['choice']) && $fieldinfo['choice'] != '') {
			$field_choices = explode(',',$fieldinfo['choice']);
			$field_choices = array_combine($field_choices,$field_choices);
		}
		$user_role = isset($fieldinfo['role']) ? $fieldinfo['role'] : '';
		if($fieldinfo['required'] == 'true')
			$required = 1;
		else
			$required = 0;
		if($fieldinfo['field_type'] != '--select--') {
			switch($fieldinfo['field_type']) {
				case 'radio button' : {
					$post_content = array('type' => 'radio', 'instructions' => $fieldinfo['desc'], 'required' => $required, 'choices' => $field_choices, 'layout' => 'vertical');
					break;
				}
				case 'text area' : {
					$post_content = array('type' => 'textarea', 'instructions' => $fieldinfo['desc'], 'required' => $required);
					break;
				}
				case 'file' :
				case 'image' : {
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required,'return_format' => 'url','preview_size' => 'thumbnail','library' => 'all');
					break;
				}
				case 'wysiwyg editor' : {
					$post_content = array('type' => 'wysiwyg','instructions' => $fieldinfo['desc'],'required' => $required,'tabs' => 'all','toolbar' => 'basic','media_upload' => 1);
					break;
				}
				case 'oembed' : {
					$this->check_acfversion();
					$post_content = array('type' => 'oembed','instructions' => $fieldinfo['desc'],'required' => $required,'width' => 100,'height' => 100 );
					break;
				}
				case 'gallery' : {
					$this->check_acfversion();
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required,'min' => 10,'max' => 20,'preview_size' => 'thumbnail','library' => 'uploadedTo');
					break;
				}
				case 'true/false' : {
					$post_content = array('type' => 'true_false','instructions' => $fieldinfo['desc'],'required' => $required,'default_value' => 1);
					break;
				}
				case 'post object' : {
					$post_content = array('type' => 'post_object','instructions' => $fieldinfo['desc'],'required' => $required,'post_type' => array('post'),'return_format' => 'object','ui' => 1);
					break;
				}
				case 'page link' : {
					$post_content = array('type' => 'page_link','instructions' => $fieldinfo['desc'],'required' => $required,'post_type' => array('post'));
					break;
				}
				case 'relationship' : {
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required,'return_format' => 'object');
					break;
				}
				case 'taxonomy' : {
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required,'taxonomy' => 'category','return_format' => 'id');
					break;
				}
				case 'user' : {
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required,'role' => array($user_role));
					break;
				}
				case 'google map' : {
					$post_content = array('type' => 'google_map','instructions' => $fieldinfo['desc'],'required' => $required,'center_lat' => '','center_lng' => '','zoom' => '','height' => 100);
					break;
				}
				case 'date picker' : {
					$post_content = array('type' => 'date_picker','instructions' => $fieldinfo['desc'],'required' => $required,'display_format' => 'd/m/y','return_format' => 'd/m/y','first_day' => 1);
					break;
				}
				case 'color picker' : {
					$post_content = array('type' => 'color_picker','instructions' => $fieldinfo['desc'],'required' => $required);
					break;
				}
				case 'message' : {
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required);
					break;
				}
				case 'url' : {
					$this->check_acfversion();
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required);
					break;
				}
				case 'text' :
				case 'number' :
				case 'password' :
				case 'email' :
				case 'tab' : {
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required);
					break;
				}
				case 'repeater' : {
					$this->check_acfversion();
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required,'min' => 10,'max' => 20,'layout' => 'table','button_label' => 'Add Row');
					break;
				}
				case 'flexible content' : {
					$this->check_acfversion();
					$post_content = array('type' => 'flexible_content','instructions' => $fieldinfo['desc'],'required' => $required,'button_label' => 'Add Row','min' => 10,'max' => 20);
					break;
				}
				default : {
					$post_content = array('type' => $fieldinfo['field_type'],'instructions' => $fieldinfo['desc'],'required' => $required,'choices' => $field_choices,'layout' => 'vertical');
					break;
				}
			}
			/** Insert the ACF Field based on Group Id */
			$postInfo = array('post_content' => serialize($post_content),'post_title' => $fieldinfo['label'], 'post_excerpt' => $fieldinfo['name'], 'post_status' => 'publish', 'post_name' => $fieldKey, 'post_parent' => $groupId, 'post_type' => 'acf-field');
			$fieldId = wp_insert_post($postInfo);
			return $fieldId;
		}
		return false;
	}

	public function Delete_ProFields($field_info) {
		global $wpdb,$uci_admin;
		$import_type = $uci_admin->import_post_types($field_info['import_type'],'');
		$groupId = $this->is_group_exist($import_type);
		$row = $wpdb->delete($wpdb->posts,array('post_title' => $field_info['field_name'],'post_parent' => $groupId));
		if($row) {
			print_r('Field Deleted');
			die;
		}
		else {
			print_r('Field cannot be deleted');
			die;
		}
	}

	/**
	 * Check ACF Fields are supported in required version (Repeater,Flexible Content)
	 */
	public function check_acfversion() {
		$version = '';
		$activeplugins = get_option('active_plugins');
		$plugins = get_plugins();
		$acf_path = 'advanced-custom-fields/acf.php';
		foreach ($plugins as $plugin_info => $plugin_key) {
			if(($plugin_info == $acf_path) && in_array($acf_path,$activeplugins))
				$version = $plugin_key['Version'];
		}
		if($version == '4.3.8' || $version == '4.3.9' || $version == '4.4.0') {
			print_r("This feature is available only in ACF Pro. Please update the ACF Version .");
			die();
		}
	}

	/**
	 * ACF Free Field Registration
	 *
	 * @param $field_info
	 * @param $type
	 *
	 * @return string
	 */
	public function Register_FreeFields($field_info, $type = null) {
		global $uci_admin;
		$import_type = $uci_admin->import_post_types($field_info['import_type'],'');
		$id = $this->acf_free_is_group_exist($import_type);
		if($id) {
			$duplicate_field = $this->acf_free_is_duplicate($id,$field_info['field_info']['name']);
			if($duplicate_field) {
				if($type != null)
					return "ACF Fields are already registered.";
				print_r("ACF Fields are already registered.");
				die;
			}
			else {
				$fieldId = $this->append_ACF_free_field($field_info['field_info'],$id);
				if($fieldId){
					if($type != null)
						return "ACF Field are added.";
					print_r("ACF Field are added.");
					die;
				}
				else {
					if($type != null)
						return "Error was occurred while adding the ACF Field.";
					print_r("Error was occurred while adding the ACF Field.");
					die;
				}
			}
		}
		else {
			if($type != null)
				return "ACF Groups are not exist.";
			print_r("ACF Groups are not exist.");
			die;
		}
	}

	public function acf_free_is_group_exist($import_type) {
		global $wpdb;
		$group_info = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_type = %s and post_name = %s",'acf','acf_smack_'.$import_type));
		if(!empty($group_info))
			$id = implode("",$group_info);
		else
			$id = $this->acf_free_createGroup($import_type);
		return $id;
	}

	public function acf_free_createGroup($import_type) {
		global $wpdb;
		switch($import_type) {
			case 'users' : {
				$param = 'ef_user';
				$location = 'all';
				break;
			}
			case 'customtaxonomy' : {
				$param = 'ef_taxonomy';
				$location = 'all';
				break;
			}
			default : {
				$param = 'post_type';
				$location = $import_type;
				break;
			}
		}
		$rule =serialize(array('param' => $param, 'operator' => '==', 'value' => $location,'order_no' => 0,'group_no' => 0));
		$meta_data = array('position' => 'normal', 'layout' => 'no_box', 'hide_on_screen' => '','rule' => $rule);
		$post_data = array('post_title' => 'ACF Free: Custom Group for '.$import_type, 'post_status' => 'publish', 'comment_status' => 'closed', 'ping_status' => 'closed', 'post_name' => 'acf_smack_'.$import_type, 'post_type' => 'acf');
		$groupId = wp_insert_post($post_data);
		foreach($meta_data as $meta_key => $meta_value) {
			$wpdb->insert($wpdb->postmeta,array('post_id' => $groupId,'meta_key' => $meta_key,'meta_value' => $meta_value));
		}
		return $groupId;
	}

	public function acf_free_is_duplicate($groupId,$field_name) {
		global $wpdb;
		$count = 0;
		$field_info = array();
		$meta_data = $wpdb->get_col($wpdb->prepare("select meta_value from $wpdb->postmeta where post_id = %d and meta_key like 'field_%'",$groupId));
		if(!empty($meta_data)) {
			foreach($meta_data as $meta_value) {
				$field_info[$count] = maybe_unserialize($meta_value);
				$count++;
			}
			foreach($field_info as $name) {
				if(isset($name['label']) && $name['label'] == $field_name)
					return true;
			}
		}
		return false;
	}

	public function append_ACF_free_field($field_info, $groupId) {
		global $wpdb;
		$choice = array();
		if(isset($field_info['choice'])) {
			$choice = explode( ',', $field_info['choice'] );
			$choice = array_combine( $choice, $choice );
		}
		$user_role = isset($field_info['role']) ? $field_info['role'] : '';
		$user_role = explode(" ",$user_role);
		$meta_data = $wpdb->get_col($wpdb->prepare("select meta_value from $wpdb->postmeta where post_id = %d and meta_key like %s", $groupId,'field_%'));
		$fields = count($meta_data);
		$field_order = $fields;
		$fieldKey = uniqid('field_');
		if($field_info['required'] == 'true')
			$required = 1;
		else
			$required = 0;
		if($field_info['field_type'] != '--select--') {
			switch($field_info['field_type']) {
				case 'text' :
				case 'number' :
				case 'password' :
				case 'email' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'order_no' => $field_order));
					break;
				}
				case 'text area' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'textarea','instructions' => $field_info['desc'],'required' => $required,'order_no' => $field_order));
					break;
				}
				case 'wysiwyg editor' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'wysiwyg','instructions' => $field_info['desc'],'required' => $required,'media_upload' => 'yes','toolbar' => 'full','order_no' => $field_order));
					break;
				}
				case 'image' :
				case 'file' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'save_format' => 'object','library' => 'all','order_no' => $field_order));
					break;
				}
				case 'select' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'choices' => $choice,'order_no' => $field_order));
					break;
				}
				case 'checkbox' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'choices' => $choice,'layout' => 'vertical','order_no' => $field_order));
					break;
				}
				case 'radio button' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'radio','instructions' => $field_info['desc'],'required' => $required,'choices' => $choice,'layout' => 'vertical','order_no' => $field_order));
					break;
				}
				case 'true/false' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'true_false','instructions' => $field_info['desc'],'required' => $required,'order_no' => $field_order));
					break;
				}
				case 'page link' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'page_link','instructions' => $field_info['desc'],'required' => $required,'post_type' => array('all'),'order_no' => $field_order));
					break;
				}
				case 'post object' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'post_object','post_type' => array('all'),'instructions' => $field_info['desc'],'required' => $required,'taxonomy' => array('all'),'order_no' => $field_order));
					break;
				}
				case 'relationship' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'return_format' => 'object','post_type' => array('all'),'taxonomy' => array('all'),'filters' => array('search'),'result_element' => array('post_type'),'order_no' => $field_order));
					break;
				}
				case 'taxonomy' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'taxonomy' => 'category','field_type' => 'checkbox','return_format' => 'id','order_no' => $field_order));
					break;
				}
				case 'user' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'role' => $user_role,'order_no' => $field_order));
					break;
				}
				case 'google map' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'google_map' ,'instructions' => $field_info['desc'],'required' => $required,'center_lat' => '','center_lng' => '','height' => 100,'order_no' => $field_order));
					break;
				}
				case 'date picker' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'date_picker','instructions' => $field_info['desc'],'required' => $required,'date_format' => 'yymmdd','display_format' => 'dd/mm/yy','first_day' => 1,'order_no' => $field_order));
					break;
				}
				case 'color picker' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => 'color_picker','instructions' => $field_info['desc'],'required' => $required,'order_no' => $field_order));
					break;
				}
				case 'message' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'message' => '','order_no' => $field_order));
					break;
				}
				case 'tab' : {
					$meta_value = serialize(array('key' => $fieldKey,'label' => $field_info['label'],'name' => $field_info['name'],'type' => $field_info['field_type'],'instructions' => $field_info['desc'],'required' => $required,'order_no' => $field_order));
					break;
				}
				default : {
					print_r('This feature is available only in ACF Pro. Please update the ACF Version .');die();
				}
			}
		}
		$id = $wpdb->insert($wpdb->postmeta,array('post_id' => $groupId,'meta_key' => $fieldKey,'meta_value' => $meta_value));
		return $id;
	}

	public function Delete_FreeFields($field_info) {
		global $uci_admin,$wpdb;
		$field_data = array();
		$row = 0;
		$count = 0;
		$import_type = $uci_admin->import_post_types($field_info['import_type'],'');
		$groupId = $this->acf_free_is_group_exist($import_type);
		$meta_info = $wpdb->get_col($wpdb->prepare("select meta_value from $wpdb->postmeta where post_id = %d and meta_key like %s",$groupId , 'field_%'));
		foreach($meta_info as $meta_value) {
			$field_data[$count] = unserialize($meta_value);
			$count++;
		}
		foreach($field_data as $field_value) {
			if ($field_value['name'] == $field_info['field_name']) {
				$row = $wpdb->delete($wpdb->postmeta, array('post_id' => $groupId, 'meta_key' => $field_value['key']));
				break;
			}
		}
		if($row) {
			print_r('Field Deleted');
			die;
		}
		else {
			print_r('Field cannot be deleted');
			die;
		}
	}

	public function ACF_RequiredFields($import_type) {
		/** ACF Pro Mandatory Fields */
		global $uci_admin,$wpdb;
		$i = 0;
		$acf_required_fields = array();
		$acf_fieldInfo = $uci_admin->ACFProCustomFields();
		if(is_array($acf_fieldInfo) && !empty($acf_fieldInfo) && array_key_exists('ACF',$acf_fieldInfo)) {
			foreach($acf_fieldInfo['ACF'] as $acf_key => $acf_field_data) {
				$post_content = $wpdb->get_col($wpdb->prepare("select post_content from $wpdb->posts where post_title = %s",$acf_field_data['label']));
				if(!empty($post_content)) {
					$post_content = unserialize($post_content[0]);
					if($post_content['required'] == 1) {
						$acf_required_fields[$i] = $acf_fieldInfo['name'];
						$i++;
					}
				}
			}

		}

		/** ACF Free Mandatory Fields */
		$field_data = $wpdb->get_col("select meta_value from $wpdb->postmeta GROUP BY meta_key HAVING meta_key LIKE 'field_%' ORDER BY meta_key");
		if(!empty($field_data) && is_array($field_data)) {
			foreach($field_data as $acf_key => $acf_val) {
				$acf_data = maybe_unserialize($acf_val);
				if($acf_data !== false) {
					if(array_key_exists('required',$acf_data) && $acf_data['required'] == 1) {
						$acf_required_fields[$i] = $acf_data['name'];
						$i++;
					}
				}
			}
		}
		return $acf_required_fields;
	}
}

global $acfHelper;
$acfHelper = new SmackUCIACFDataImport();
#return new SmackUCIACFDataImport();
