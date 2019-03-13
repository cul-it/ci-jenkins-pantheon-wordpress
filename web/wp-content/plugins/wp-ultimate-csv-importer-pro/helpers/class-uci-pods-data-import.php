<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCIPODSDataImport {

	public function push_pods_data($data_to_import) {
		global $uci_admin;
		$array = $data_to_import;
		if(isset($array['PODS'])) {
			$data_array = $array['PODS'];}
		if(!empty($data_array)) {
			if ( in_array( 'pods/init.php', $uci_admin->get_active_plugins() ) ) {
				$this->importDataForPODSFields( $data_array, $uci_admin->getImportAs(), $uci_admin->getLastImportId() );
			}
		}
	}

	public function importDataForPODSFields ($data_array, $importas,$pID) {
		global $wpdb;
		global $uci_admin;
                $podsFields = array();
                $import_type = $uci_admin->import_post_types($importas, null);
                $post_id = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_name= %s and post_type = %s", $import_type, '_pods_pod'));
                if(!empty($post_id)) {
                        $lastId          = $post_id[0]->ID;
                        $get_pods_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name FROM $wpdb->posts where post_parent = %d AND post_type = %s", $lastId, '_pods_field' ) );
                        if ( ! empty( $get_pods_fields ) ) :
                                foreach ( $get_pods_fields as $pods_field ) {
                        		$get_pods_types = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta where post_id = %d AND meta_key = %s", $pods_field->ID, 'type' ) );
                                        $podsFields["PODS"][ $pods_field->post_name ]['label'] = $pods_field->post_name;
                                        $podsFields["PODS"][ $pods_field->post_name ]['type']  = $get_pods_types[0]->meta_value;
                                }
                        endif;
                }

		$createdFields = array();
		foreach ($data_array as $dkey => $dvalue) {
			$createdFields[] = $dkey;
		}
		#TODO File fields
		foreach ($data_array as $custom_key => $custom_value) {
			if($podsFields["PODS"][$custom_key]['type'] == 'file'){
				$exploded_file_items = explode('|', $custom_value);
				 foreach($exploded_file_items as $file) {
					if(preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $file,$matched_gallerylist,PREG_PATTERN_ORDER)){
						$get_file_id = $uci_admin->set_featureimage($file, $pID);
						if($get_file_id != '') {
							$gallery_ids[] = $get_file_id;
						}
					} else {
						$galleryLen = strlen($file);
						$checkgalleryid = intval($file);
						$verifiedGalleryLen = strlen($checkgalleryid);
						if($galleryLen == $verifiedGalleryLen) {
							$gallery_ids[] = $file;
						}
					}
				 }
				update_post_meta($pID, $custom_key, $gallery_ids);
			}else{
				update_post_meta($pID, $custom_key, $custom_value);
			}
		}
		return $createdFields;
	}

	/**
	 * Main Function for Register the PODS Fields
	 * @param $field_info
	 * @param $type
	 *
	 * @return string
	 */
	public function Register_Fields($field_info, $type = null) {
		global $uci_admin;
		$import_type = $uci_admin->import_post_types($field_info['import_type'],'');
		$field_info = $field_info['field_info'];
		$id = $this->is_group_exist($import_type);
		if($id) {
			$duplicate_field = $this->is_duplicate($id,$field_info['name']);
			if($duplicate_field) {
				if($type != null)
					return "PODS Fields are already registered.";
				print_r("PODS Fields are already registered.");
				die;
			} else {
				$fieldId = $this->append_PODS_Field($field_info,$id,$import_type);
				if($fieldId){
					if($type != null)
						return "PODS Field are added.";
					print_r("PODS Field are added.");
					die;
				} else {
					if($type != null)
						return "Error was occurred while adding the PODS Field.";
					print_r("Error was occurred while adding the PODS Field.");
					die;
				}
			}
		} else {
			if($type != null)
				return "PODS Groups are not exist.";
			print_r("PODS Groups are not exist.");
			die;
		}
	}

	/**
	 * Check PODS Groups are already exists and return the Group ID.
	 * @param $import_type
	 * @return int|string|WP_Error
	 */
	public function is_group_exist($import_type) {
		global $wpdb;
		$import_type = $this->convert_importType($import_type);
		$post_name = str_replace(' ','-',$import_type);
		$group_info = $wpdb->get_col($wpdb->prepare("select ID from $wpdb->posts where post_type = %s and post_name = %s",'_pods_pod',$post_name));
		if(!empty($group_info)) {
			$id = implode("",$group_info);
			return $id;
		}
		else {
			$id = $this->createGroup($import_type);
			return $id;
		}
	}

	/**
	 * Create the PODS Group and return its ID
	 * @param $import_type
	 * @return int|WP_Error
	 */
	public function createGroup($import_type) {
		global $wpdb;
		$type = 'post_type';
		$import_type = $this->convert_importType($import_type);
		if($import_type == 'user' || $import_type == 'comment')
			$type = $import_type;
		$post_name = str_replace(' ','-',$import_type);
		$storage_value = 'meta';
		$post_info = array('post_title' => $import_type,'post_name' => $post_name,'post_status' => 'publish','post_type' => '_pods_pod');
		$meta_data = array('storage' => $storage_value,'type' => $type,'object' => $post_name,'old_name' => $post_name);
		if($import_type === 'customtaxonomy')
			$meta_data = array('storage' => $storage_value,'type' => $type,'old_name' => $post_name);
		$id = wp_insert_post($post_info);
		foreach($meta_data as $meta_key => $meta_value) {
			$wpdb->insert($wpdb->postmeta, array('post_id' => $id, 'meta_key' => $meta_key, 'meta_value' => $meta_value));
		}
		return $id;
	}

	/**
	 * Converts the module to required post_type
	 * @param $import_type
	 * @return string
	 */
	public function convert_importType($import_type) {
		switch($import_type) {
			case 'users' : {
				return 'user';
				break;
			}
			case 'comments' : {
				return 'comment';
				break;
			}
			default : {
				return $import_type;
				break;
			}
		}
	}

	/**
	 * Check whether the PODS Fields are already exists in required group
	 * @param $groupId
	 * @param $field_name
	 * @return bool
	 */
	public function is_duplicate($groupId,$field_name) {
		global $wpdb;
		$field_id = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_type = %s and post_title = %s and post_parent = %d",'_pods_field',$field_name,$groupId));
		if(!empty($field_id))
			return true;
		else
			return false;
	}

	/**
	 * Insert the PODS Fields under the required group and return the Field ID
	 * @param $field_info
	 * @param $groupId
	 * @param $import_type
	 * @return int|WP_Error
	 */
	public function append_PODS_Field($field_info, $groupId, $import_type) {
		global $wpdb,$uci_admin;
		$get_id = $wpdb->get_results( $wpdb->prepare( "select id from $wpdb->posts where post_type=%s  and post_name= %s", '_pods_pod',$import_type ) );
		if($field_info['field_type'] == 'plain text') {
			$field_info['field_type'] = 'text';
		} elseif ($field_info['field_type'] == 'plain number') {
			$field_info['field_type'] = 'number';
		} elseif ($field_info['field_type'] == 'file/image/video') {
			$field_info['field_type'] = 'file';
		} elseif ($field_info['field_type'] == 'code (syntax highlighting)') {
			$field_info['field_type'] = 'code';
		} elseif ($field_info['field_type'] == 'wysiwyg (visual editor)') {
			$field_info['field_type'] = 'wysiwyg';
		} elseif ($field_info['field_type'] == 'plain paragraph text') {
			$field_info['field_type'] = 'paragraph';
		} elseif ($field_info['field_type'] == 'relationship') {
			$field_info['field_type'] = 'pick';
		} elseif ($field_info['field_type'] == 'color picker') {
			$field_info['field_type'] = 'color';
		} elseif ($field_info['field_type'] == 'yes/no') {
			$field_info['field_type'] = 'boolean';
		}
		$params = array(
			'pod' => $import_type,
			'pod_id' => $get_id[0]->id,
			'label' => $field_info['label'],
			'name' => $field_info['label'],
			'type' => $field_info['field_type'],
		);
		$field_id = pods_api()->save_field($params );
		return $field_id;
	}

	public function Relational_Fields($field_info) {
		global $uci_admin,$wpdb;
		$rel_titleList = '';
		$import_type = $uci_admin->import_post_types($field_info['import_type'],'');
		if($field_info['related_to'] != '') {
			$postId = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta where meta_key = %s and meta_value = %s",'object',$field_info['related_to']));
			if(!empty($postId)) {
				$postId = implode("",$postId);
				$relational_fieldInfo = $wpdb->get_col($wpdb->prepare("SELECT id FROM $wpdb->posts where post_parent = %d",$postId));
				if(!empty($relational_fieldInfo)) {
					$count = 0;
					foreach ($relational_fieldInfo as $relational_id) {
						/** @var  $type
						 *  Check Bi-directional fields are exists
						It was checked based on meta_key
						meta_key have the value as pick_val then it have bi-directional field
						(i.e) It have related fields */
						$is_related = $wpdb->get_col($wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta where post_id = %d and meta_key = %s and meta_value = %s",$relational_id,'pick_val',$import_type));
						if(!empty($is_related)) {
							$rel_titleList[$count] = $wpdb->get_col($wpdb->prepare("SELECT post_title FROM $wpdb->posts where id = %d",$relational_id));
							$count++;
						}
					}
				}
			}
		}
		$rel_titleList = json_encode($rel_titleList);
		print_r($rel_titleList);
		die;
	}

	public function Delete_Fields($field_info) {
		global $wpdb,$uci_admin;
		$import_type = $uci_admin->import_post_types($field_info['import_type'],'');
		$groupId = $this->is_group_exist($import_type);
		$field_id = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_type = %s and post_title = %s and post_parent = %d",'_pods_field',$field_info['field_name'],$groupId));
		$wpdb->delete($wpdb->postmeta,array('post_id' => $field_id[0]));
		$row = $wpdb->delete($wpdb->posts,array('post_title' => $field_info['field_name'],'post_parent' => $groupId));
		if($row) {
			print_r('Field Deleted');
			die;
		}
		else {
			print_r('Field cannot be created');
			die;
		}
	}

	public function PODS_RequiredFields($import_type) {
		global $uci_admin,$wpdb;
		$i = 0;
		$pods_required_fields = array();
		$field_info = $uci_admin->PODSCustomFields($import_type);
		if(is_array($field_info) && !empty($field_info) && array_key_exists('PODS',$field_info)) {
			foreach ($field_info['PODS'] as $pods_key => $pods_field_data) {
				$post_id = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_title = %s", $pods_field_data['label']));
				if (!empty($post_id)) {
					$pods_data = $wpdb->get_col($wpdb->prepare("select meta_value from $wpdb->postmeta where meta_key = %s and post_id = %d", 'required', $post_id[0]));
					if (!empty($pods_data) && $pods_data[0] == 1) {
						$pods_required_fields[$i] = $pods_field_data['name'];
						$i++;
					}
				}
			}
		}
		return $pods_required_fields;
	}
}

global $podsHelper;
$podsHelper = new SmackUCIPODSDataImport();
#return new SmackUCIPODSDataImport();
