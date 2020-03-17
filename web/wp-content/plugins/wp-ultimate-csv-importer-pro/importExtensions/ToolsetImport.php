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

class ToolsetImport {
    private static $toolset_instance = null;

    public static function getInstance() {
		
		if (ToolsetImport::$toolset_instance == null) {
			ToolsetImport::$toolset_instance = new ToolsetImport;
			return ToolsetImport::$toolset_instance;
		}
		return ToolsetImport::$toolset_instance;
    }

    function set_toolset_values($header_array ,$value_array , $map, $post_id , $type , $mode){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->types_import_function($post_values,$type, $post_id, $mode);

	}

	public function types_import_function($data_array, $type, $postId ,$mode) 
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
	//	else
	//	{
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
 
			if ($type =='Users') {
				$wptypesfields=$getUserMetas;
			}
			else{
				if(empty($wptypesfields)){
                	$wptypesfields=$getTermMetas;
				}
				
			}
			if ($mode=='Update') {
				
                if(!empty($data_array)) {
					require_once "toolsetHelper/WPToolsetUpdater.php";
					$WPToolsetUpdater = WPToolsetUpdater::getInstance();
					$WPToolsetUpdater->set($data_array, $result, $field_type,$wptypesfields,$type);
					$WPToolsetUpdater->update($postId);
                }
			}elseif ($mode =='Insert') {
				
                if(!empty($data_array)) { 
					require_once "toolsetHelper/WPToolsetImporter.php";
					$wpToolsetImporter = WPToolsetImporter::getInstance();
					$wpToolsetImporter->set($data_array, $result, $field_type,$wptypesfields,$type);
					$wpToolsetImporter->import($postId);
                } 
			}
		
	//	}
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
					
    $get_rel_val = array();
    foreach($split as $key=>$item){
					
        foreach(explode(',',$item) as $value){
						$get_rel_val[$key][] = $value;
        }
    }
					  
		//$value=$data_array['types_relationship'];
		//$get_rel_vals = explode(',', $value);
	//	for ($j = 0; $j < count($get_rel_val); $j++){
				$slug=explode('|', $data_array['relationship_slug']);
							
        foreach ($get_rel_val as $key1 => $get_rel_vals) {

						foreach ($slug as $key2 => $value) {

							if ($key1 == $key2){
								
				          for ($i = 0; $i < count($get_rel_vals); $i++){
									 
										if (ctype_digit($get_rel_vals[$i])){
										 
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
										else{
										
												$pquery = "select * from {$wpdb->prefix}posts where post_title = '{$get_rel_vals[$i]}' and post_status != 'trash'";
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
		   // }

		}
		
	}

	public function intermediateRelationship($data_array,$pID)
	{
		
		$value=$data_array['intermediate'];
		$get_rel_vals = explode(',', $value);
		global $wpdb;

		$row = $wpdb->delete($wpdb->prefix.'toolset_associations',array('intermediary_id' => $pID));

		if(intval($get_rel_vals[0])){
			$post = get_post($get_rel_vals[0]);

			$pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$data_array['relationship_slug']}'";	
			$post = $wpdb->get_results($pquery);
			$wpdb->insert($wpdb->prefix.'toolset_associations',array('relationship_id' =>$post[0]->id ,'parent_id' => $get_rel_vals[0] ,'child_id' => $get_rel_vals[1]
						,'intermediary_id' => $pID ));

		}else{
			$pquery = "select * from {$wpdb->prefix}posts where post_title = '{$get_rel_vals[0]}' and post_status != 'trash'";	
			$post = $wpdb->get_results($pquery,ARRAY_A);
			$pqueryc = "select * from {$wpdb->prefix}posts where post_title = '{$get_rel_vals[1]}' and post_status != 'trash'";	
			$postc = $wpdb->get_results($pqueryc,ARRAY_A);
			$pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$data_array['relationship_slug']}'";	
			$post1 = $wpdb->get_results($pquery);
			$wpdb->insert($wpdb->prefix.'toolset_associations',array('relationship_id' => $post1[0]->id,'parent_id' => $post[0]['ID'] ,'child_id' => $postc[0]['ID']
						,'intermediary_id' => $pID));

		}
    }
}