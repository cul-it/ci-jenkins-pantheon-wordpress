<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

class SmackUCITypesDataImport {



	public function push_types_data($data_to_import,$mode) {
		
		global $uci_admin;
		$array = $data_to_import;
		if(!empty($array['TYPES'])) {
			$this->importDataForTypesFields($array['TYPES'], $uci_admin->getImportAs(),$uci_admin->getLastImportId(),$mode);

		}
	}
	public function importDataForTypesFields ($data_array, $type, $postId ,$mode) 
	{
		$wptypesfields=array();
     
		$result=array_key_exists('types_relationship',$data_array);
		$intermediateResult=array_key_exists('intermediate',$data_array);
		if($result==1)
		{
			$this->normalRelationship($data_array,$postId);
		}
		elseif($intermediateResult==1)
		{
			$this->intermediateRelationship($data_array,$postId);	
		}
		else
		{
			$createdFields = $types_fieldtype = array();
			foreach($data_array as $dkey => $dvalue)
			{
				$createdFields[] = $dkey;
			}

			$types_fieldname = array();
			$field_type= array();
			$wptypesfields = get_option('wpcf-fields');



			foreach($wptypesfields as $types_field)
			{
				$types_fieldname[$types_field['slug']] = $types_field['meta_key'];
				$types_fieldtype[$types_field['slug']] = $types_field['type'];
			}

			$getUserMetas = get_option('wpcf-usermeta');
			if (is_array($getUserMetas))
			{
				foreach($getUserMetas as $types_field)
				{
					$types_fieldname[$types_field['slug']] = $types_field['meta_key'];
					$types_fieldtype[$types_field['slug']] = $types_field['type'];
				}
			}
			$getTermMetas = get_option('wpcf-termmeta');
			if (is_array($getTermMetas))
			{
				foreach($getTermMetas as $types_field)
				{
					$types_fieldname[$types_field['slug']] = $types_field['meta_key'];
					$types_fieldtype[$types_field['slug']] = $types_field['type'];
				}
			}

			$result=array_intersect_key($types_fieldname,$data_array); //meta table entry.		
			foreach ($result as $key => $value) {
				if (array_key_exists($key, $types_fieldtype)) {
					$field_type[$key] = $types_fieldtype[$key];
				}
			}
			//Theo toolsetImporter code starts here
 
			if ($type =='users') {
				$wptypesfields=$getUserMetas;
			}
			else{
				if(empty($wptypesfields)){
                $wptypesfields=$getTermMetas;
				}
				
			}
			if ($mode=='Update') {
				require_once "toolsetHelper/WPToolsetUpdater.php";
				$WPToolsetUpdater = WPToolsetUpdater::getInstance();
				$WPToolsetUpdater->set($data_array, $result, $field_type,$wptypesfields,$type);
				$WPToolsetUpdater->update($postId);
			}elseif ($mode=='Insert') {

				require_once "toolsetHelper/WPToolsetImporter.php";
				$wpToolsetImporter = WPToolsetImporter::getInstance();
				$wpToolsetImporter->set($data_array, $result, $field_type,$wptypesfields,$type);
				$wpToolsetImporter->import($postId);
			}
			//---------------------------------------------------------
		}
		return $createdFields;

	}

	public function normalRelationship($data_array,$pID)
	{  
		$value=$data_array['types_relationship'];

		global $wpdb;
		$row = $wpdb->delete($wpdb->prefix.'toolset_associations',array('child_id' => $pID));
		$row1 = $wpdb->delete($wpdb->prefix.'toolset_associations',array('parent_id' => $pID));
			$x = preg_replace('/\s*,\s*/', ',', $value);
					$split=explode('|', $x);
                   $get_rel_vals = array();
                   foreach($split as $key=>$item){
                       foreach(explode(',',$item) as $value){
                            $get_rel_vals[$key][] = $value;
                         }
                      }

		//$value=$data_array['types_relationship'];
		//$get_rel_vals = explode(',', $value);
		for ($j = 0; $j < count($get_rel_vals); $j++)
                         { 
                    		$slug=explode('|', $data_array['relationship_slug']);
                    		foreach ($get_rel_vals as $key1 => $get_rel_vals) {
						    foreach ($slug as $key2 => $value) {
							if ($key1 == $key2){


				              for ($i = 0; $i < count($get_rel_vals); $i++)
					           {
					    if (ctype_digit($get_rel_vals[$i]))
						{
						$post = get_post($get_rel_vals[$i]);
						global $wpdb;
						$pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
						$post = $wpdb->get_results($pquery);
						$wpdb->insert($wpdb->prefix . 'toolset_associations', array(
							'relationship_id' => $post[0]->id,
							'parent_id' => $get_rel_vals[$i],
							'child_id' => $pID,
							'intermediary_id' => '0'
						));
						$wpdb->insert($wpdb->prefix . 'toolset_associations', array(
							'relationship_id' => $post[0]->id,
							'parent_id' => $pID,
							'child_id' => $get_rel_vals[$i],
							'intermediary_id' => '0'
						));
						}
					  else
						{ 
						$pquery = "select * from $wpdb->posts where post_title = '{$get_rel_vals[$i]}' and post_status != 'trash'";
						$post = $wpdb->get_results($pquery, ARRAY_A);
						/*if($get_rel_vals[$i]!=$post[0]['post_title']){

						$wpdb->insert($wpdb->prefix . 'posts', array(
							'post_title' => $get_rel_vals[$i],
							'post_type'  => 'services',
							'post_name'  =>  $get_rel_vals[$i]  // for specific slug with relationship import 
						));
						}*/
					
						$pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
						$post1 = $wpdb->get_results($pquery);  

						$wpdb->insert($wpdb->prefix . 'toolset_associations', array(
							'relationship_id' => $post1[0]->id,
							'parent_id' => $pID,
							'child_id' => $post[0]['ID'],
							'intermediary_id' => '0'
						));
						$wpdb->insert($wpdb->prefix . 'toolset_associations', array(
							'relationship_id' => $post1[0]->id,
							'parent_id' => $post[0]['ID'],
							'child_id' => $pID,
							'intermediary_id' => '0'
						));
					      
						}
					}
				    }
		            }
		        }

	        }


	}
	public function intermediateRelationship($data_array,$pID)
	{
		$value=$data_array['intermediate'];
		$get_rel_vals = explode(',', $value);			
		if(intval($get_rel_vals[0])){
			$post = get_post($get_rel_vals[0]);
			global $wpdb;

			$pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$data_array['relationship_slug']}'";	
			$post = $wpdb->get_results($pquery);
			$wpdb->insert($wpdb->prefix.'toolset_associations',array('relationship_id' =>$post[0]->id ,'parent_id' => $get_rel_vals[0] ,'child_id' => $get_rel_vals[1]
						,'intermediary_id' => $pID ));


		}else{
			$pquery = "select * from $wpdb->posts where post_title = '{$get_rel_vals[0]}' and post_status != 'trash'";	
			$post = $wpdb->get_results($pquery,ARRAY_A);
			$pqueryc = "select * from $wpdb->posts where post_title = '{$get_rel_vals[1]}' and post_status != 'trash'";	
			$postc = $wpdb->get_results($pqueryc,ARRAY_A);
			$pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$data_array['relationship_slug']}'";	
			$post1 = $wpdb->get_results($pquery);
			$wpdb->insert($wpdb->prefix.'toolset_associations',array('relationship_id' => $post1[0]->id,'parent_id' => $post[0]['ID'] ,'child_id' => $postc[0]['ID']
						,'intermediary_id' => $pID));

		}
	}
	public function postReferenceType($post_id, $types_fieldnamekey, $value, $type, $data_array)
	{

		global $wpdb;
		foreach ($data_array as $key => $value) {

			$query = "SELECT id FROM {$wpdb->prefix}toolset_relationships WHERE slug ='{$key}'";
			$id = $wpdb->get_results($query);						
		}
		//print_r($id[0]->id);  //  $id[0]->id relation ship id.

		if (is_numeric($value)) {
			$value=$value;
		}elseif(is_string($value)){
			$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$value}' AND post_status='publish'";
			$name = $wpdb->get_results($query);
			if (!empty($name)) {
				$value=$name[0]->id;
			}else{
				//given post title is not found.
				die();
			}

		}
		$toolset_associations = $wpdb->prefix."toolset_associations";

		$query1 = "SELECT id FROM {$wpdb->prefix}toolset_associations WHERE relationship_id ={$id[0]->id} AND child_id={$post_id}";
		$name = $wpdb->get_results($query1);
		if (!empty($name)) {
			// relatioship found, delete and insert relationship
			$wpdb->delete( $toolset_associations, ['id' => $name[0]->id ]);
			$wpdb->insert( $toolset_associations, array(
						'relationship_id' 		=> $id[0]->id,   
						'parent_id' 			=> $value,
						'child_id' 				=> $post_id,
						'intermediary_id' 		=> 0
						));
		}else{
			// create new relatioship
			$wpdb->insert( $toolset_associations, array(
						'relationship_id' 		=> $id[0]->id,   
						'parent_id' 			=> $value,
						'child_id' 				=> $post_id,
						'intermediary_id' 		=> 0
						));
		}
	}

	public function updatePostMetac($id, $field, $array, $type = "", $mode = "",$meta_info)
	{

		if(strpos($meta_info, '|')){
			$meta_info = explode('|', $meta_info);
		}

		if(is_array($meta_info)){

			delete_post_meta($id, $field); 

			foreach($meta_info as $_fieldvalue){					

				if($mode == 'users'){
					add_user_meta($id, $field, $_fieldvalue);
				}else{
					add_post_meta($id, $field, $_fieldvalue);
				}

			}

		}else{
			if($mode == 'users'){
				update_user_meta($id, $field, $_fieldvalue);
			}else{
				update_post_meta($id, $field, $meta_info);
			}
		}
	}

	public function Register_Fields($field_info, $type = null) {
		global $uci_admin;
		$import_type = $uci_admin->import_post_types($field_info['import_type'], '');
		$field_info = $field_info['field_info'];
		$field_info['import_type'] = $import_type;
		$id = $this->is_group_exist($import_type);
		if($id) {
			$duplicate_field = $this->is_duplicate($id, $field_info['name']);
			if($duplicate_field) {
				if($type != null)
					return "TYPES Fields are already registered.";
				print_r("TYPES Fields are already registered.");
				die;
			}
			else {
				$fieldId = $this->append_TYPES_Field($field_info, $id);
				if($fieldId){
					if($type != null)
						return "TYPES Field are added.";
					print_r("TYPES Field are added.");
					die;
				}
				else {
					if($type != null)
						return "Error was occurred while adding the TYPES Field.";
					print_r("Error was occurred while adding the TYPES Field.");
					die;
				}
			}
		}
		else {
			if($type != null)
				return "TYPES Groups are not exist.";
			print_r("TYPES Groups are not exist.");
			die;
		}
	}

	public function is_group_exist($import_type) {
		global $wpdb;
		if($import_type != 'users') {
			$group_info = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_type = %s and post_name = %s",'wp-types-group','types-group-for-'.$import_type));
		}
		else {
			$group_info = $wpdb->get_col($wpdb->prepare("select id from $wpdb->posts where post_type = %s and post_name= %s",'wp-types-user-group','types-group-for-'.$import_type));
		}
		if(!empty($group_info)) {
			$id = implode("",$group_info);
			return $id;
		}
		else {
			$id = $this->createGroup($import_type);
			return $id;
		}
	}

	public function createGroup($import_type) {
		if($import_type === 'users') {
			$post_info = array('post_title' => 'Types Group for '.$import_type,'post_name' => 'Types Group for '.$import_type,'post_content' => 'Groups Description','post_status' => 'publish','post_type' => 'wp-types-user-group');
			$groupId = wp_insert_post($post_info);
			update_post_meta($groupId,'_wp_types_group_showfor',',administrator,');
			return $groupId;
		}
		else {
			$post_info = array('post_title' => 'Types Group for '.$import_type,'post_name' => 'Types Group for '.$import_type,'post_content' => 'Groups Description','post_status' => 'publish','post_type' => 'wp-types-group');
			$groupId = wp_insert_post($post_info);
			update_post_meta($groupId,'_wp_types_group_post_types',','.$import_type.',');
			return $groupId;
		}
	}

	public function is_duplicate($groupId,$field_name) {
		$field_info = get_post_meta($groupId,'_wp_types_group_fields',true);
		if($field_info != '') {
			$field_info = explode(",",$field_info);
			if(!empty($field_info) && !in_array($field_name,$field_info))
				return false;
			else
				return true;
		}
		else
			return false;
	}

	public function append_TYPES_Field($field_info, $groupId) {
		$required = array('required' => array('active' => '1','value' => 'true','message' => 'This Field is required'));
		switch($field_info['field_type']) {
			case 'checkbox' : {
						  if($field_info['required'] == 'false')
							  $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('set_value' => $field_info['choice'],'save_empty' => 'no','display' => 'db','display_value_not_selected' => '','display_value_selected' => '','disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						  else
							  $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('set_value' => $field_info['choice'],'save_empty' => 'no','display' => 'db','display_value_not_selected' => '','display_value_selected' => '','validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						  break;
					  }
			case 'checkboxes' : {
						    $choices = $this->get_fieldChoice($field_info['field_type'],$field_info['choice']);
						    if($field_info['required'] == 'false')
							    $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('options' => $choices,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						    else
							    $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('options' => $choices,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						    break;
					    }
			case 'colorpicker' : {
						     if($field_info['required'] == 'false')
							     $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						     else
							     $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						     break;
					     }
			case 'date' : {
					      if($field_info['required'] == 'false')
						      $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','date_and_time' => 'and_time','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					      else
						      $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','date_and_time' => 'and_time','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					      break;
				      }
			case 'email' : {
					       if($field_info['required'] == 'false')
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       else
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       break;
				       }
			case 'embed' : {
					       if($field_info['required'] == 'false')
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => array('url' => array('active' => '1','message' => 'Please enter a valid URL address')),'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       else
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => array('url' => array('active' => '1','message' => 'Please enter a valid URL address'),$required),'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       break;
				       }
			case 'file' : {
					      if($field_info['required'] == 'false')
						      $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					      else
						      $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					      break;
				      }
			case 'image' : {
					       if($field_info['required'] == 'false')
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['labele'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       else
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       break;
				       }
			case 'numeric' : {
						 if($field_info['required'] == 'false')
							 $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => array('number' => array('active' => '1','message' => 'Please enter numeric data')),'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						 else
							 $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => array('number' => array('active' => '1','message' => 'Please enter numeric data')),$required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						 break;
					 }
			case 'phone' : {
					       if($field_info['required'] == 'false')
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       else
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       break;
				       }
			case 'radio' : {
					       $choices = $this->get_fieldChoice($field_info['field_type'],$field_info['choice']);
					       if($field_info['required'] == 'false')
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('options' => $choices,'display' => 'db','disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       else
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('options' => $choices,'display' => 'db','validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       break;
				       }
			case 'select' : {
						$choices = $this->get_fieldChoice($field_info['field_type'],$field_info['choice']);
						if($field_info['required'] == 'false')
							$field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('options' => $choices,'display' => 'db','disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						else
							$field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('options' => $choices,'display' => 'db','validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						break;
					}
			case 'skype' : {
					       if($field_info['required'] == 'false')
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       else
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       break;
				       }
			case 'textarea' : {
						  if($field_info['required'] == 'false')
							  $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						  else
							  $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'],'','meta_value' => 'postmeta'));
						  break;
					  }
			case 'textfield' : {
						   if($field_info['required'] == 'false')
							   $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'conditional_display' => array('relation' => 'AND','custom' => ''),'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						   else
							   $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						   break;
					   }
			case 'url' : {
					     if($field_info['required'] == 'false')
						     $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-',$field_info['name'].'','meta_value' => 'postmeta'));
					     else
						     $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					     break;
				     }
			case 'video' : {
					       if($field_info['required'] == 'false')
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
					       else
						       $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-',$field_info['name'].'','meta_value' => 'postmeta'));
					       break;
				       }
			case 'wysiwyg' : {
						 if($field_info['required'] == 'false')
							 $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						 else
							 $field_meta = array($field_info['name'] => array('id' => $field_info['name'],'slug' => $field_info['name'],'type' => $field_info['field_type'],'name' => $field_info['label'],'description' => $field_info['desc'],'data' => array('placeholder' => '','repetitive' => 0,'validate' => $required,'disabled_by_type' => 0),'meta_key' => 'wpcf-'.$field_info['name'].'','meta_value' => 'postmeta'));
						 break;
					 }
			default : {
					  echo 'no matches';
				  }
		}
		if($field_info['import_type'] !== 'users') {
			$existing_fieldInfo = get_option('wpcf-fields');
			if($existing_fieldInfo != '')
				$existing_fieldInfo = array_merge($existing_fieldInfo,$field_meta);
			else
				$existing_fieldInfo = $field_meta;
			update_option('wpcf-fields',$existing_fieldInfo);
			$existing_postmeta = get_post_meta($groupId,'_wp_types_group_fields',true);
			$field_name = $existing_postmeta . $field_info['name'];
			update_post_meta($groupId,'_wp_types_group_fields',','.$field_name.',');
			return true;
		}
		else {
			$existing_fieldInfo = get_option('wpcf-usermeta');
			if($existing_fieldInfo != '')
				$existing_fieldInfo = array_merge($existing_fieldInfo,$field_meta);
			else
				$existing_fieldInfo = $field_meta;
			update_option('wpcf-usermeta',$existing_fieldInfo);
			$existing_postmeta = get_post_meta($groupId,'_wp_types_group_fields',true);
			$field_name = $existing_postmeta . $field_info['name'];
			update_post_meta($groupId,'_wp_types_group_fields',','.$field_name.',');
			return true;
		}
		return false;
	}

	public function get_fieldChoice($field_type,$choice) {
		$i = 0;
		$choice = explode(',',$choice);
		$choice = array_combine($choice,$choice);
		if($field_type == 'select') {
			foreach($choice as $choice_key => $choice_value) {
				$uniqueKey = md5(strval(time()).$i);
				$field_optionIndex = 'wpcf-fields-select-option-'.$uniqueKey;
				$field_option[$field_optionIndex] = array('title' => $choice_key,'value' => $choice_value);
				$i++;
			}
			return $field_option;
		}
		if($field_type == 'checkboxes') {
			foreach($choice as $choice_key => $choice_value) {
				$uniqueKey = md5(strval(time()).$i);
				$field_optionIndex = 'wpcf-fields-checkboxes-option-'.$uniqueKey;
				$field_option[$field_optionIndex] = array('title' => $choice_key,'set_value' => $choice_value,'save_empty' => 'no','display' => 'db','display_value_not_selected' => '','display_value_selected' => '');
				$i++;
			}
			return $field_option;
		}
		if($field_type == 'radio') {
			foreach($choice as $choice_key => $choice_value) {
				$uniqueKey = md5(strval(time()).$i);
				$field_optionIndex = 'wpcf-fields-radio-option-'.$uniqueKey;
				$field_option[$field_optionIndex] = array('title' => $choice_key,'value' => $choice_value,'display_value' => $choice_value);
				$i++;
			}
			return $field_option;
		}
	}

	public function Delete_Fields($field_info) {
		global $uci_admin;
		$row = 0;
		$import_type = $uci_admin->import_post_types($field_info['import_type'],'');
		$groupId = $this->is_group_exist($import_type);
		$field_data = get_post_meta($groupId,'_wp_types_group_fields',true);
		if($field_data != '') {
			$field_data = explode(",",$field_data);
			if(!empty($field_data) && in_array($field_info['field_name'],$field_data)) {
				if(($key = array_search($field_info['field_name'],$field_data)) !== false) {
					unset($field_data[$key]);
				}
				$field_data = implode(",",$field_data);
				$row = update_post_meta($groupId,'_wp_types_group_fields',$field_data);
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

	public function TYPES_RequiredFields($import_type) {
		global $uci_admin;
		$i = 0;
		$types_required_field = array();
		$field_info = $uci_admin->TypesCustomFields($import_type);
		if($import_type == 'users')
			$optionData = get_option('wpcf-usermeta');
		else
			$optionData = get_option('wpcf-fields');
		if(is_array($field_info) && !empty($field_info) && array_key_exists('TYPES',$field_info)) {
			foreach($field_info['TYPES'] as $types_key => $types_data) {
				if(is_array($optionData)) {
					if(array_key_exists($types_data['label'],$optionData)) {
						foreach($optionData[$types_data['label']] as $option_key => $option_value) {
							if(is_array($option_value)) {
								if(array_key_exists('validate',$option_value)) {
									foreach($option_value['validate'] as $key => $value) {
										if($key == 'required' && is_array($value) && array_key_exists('active',$value) && $value['active'] == 1) {
											$types_required_field[$i] = $types_data['name'];
											$i++;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $types_required_field;
	}
}

global $typesHelper;
$typesHelper = new SmackUCITypesDataImport();
#return new SmackUCITypesDataImport();
