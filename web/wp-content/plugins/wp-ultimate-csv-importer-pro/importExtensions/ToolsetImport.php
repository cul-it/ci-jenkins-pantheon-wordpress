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

    function set_toolset_values($header_array ,$value_array , $map, $maps, $post_id , $type , $mode){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		$post_val = $helpers_instance->get_meta_values($maps , $header_array , $value_array);
		$this->types_import_function($post_values,$post_val,$type, $post_id, $mode);

	}

	public function types_import_function($data_array,$post_val, $type, $postId ,$mode) 
	{
		//$wptypesfields=array();
		$newtoolset_instance = new ToolsetNewImport();
		$wptypesfields=array();
		include_once( 'wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		$plugin_version = $plugins['types/wpcf.php']['Version'];
        
		$result=array_key_exists('types_relationship',$data_array);
		$intermediateResult=array_key_exists('intermediate',$data_array);
		if($result==1)
		{   if($plugin_version < '3.4.1'){
			
			   $this->normalRelationship($data_array,$postId,$mode);
		    }
			else{
				
				$newtoolset_instance->normalRelationshipnew($data_array,$postId,$mode,$type);
			}
			
		}
		elseif($intermediateResult==1)
		{  
			if($plugin_version < '3.4.1'){
			 
			  $this->intermediateRelationship($data_array,$postId);	
			}
			else{
				$newtoolset_instance->intermediateRelationshipnew($data_array,$postId,$type);
			}
		}
		
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
					$WPToolsetUpdater->update($postId,$post_val);
                }
			}elseif ($mode =='Insert') {
				
                if(!empty($data_array)) { 
					require_once "toolsetHelper/WPToolsetImporter.php";
					$wpToolsetImporter = WPToolsetImporter::getInstance();
					$wpToolsetImporter->set($data_array, $result, $field_type,$wptypesfields,$type);
					$wpToolsetImporter->import($postId,$post_val);
                } 
			}
		return $createdFields;

	}

	public function normalRelationship($data_array,$pID,$mode)
	{  
		$value=$data_array['types_relationship'];
		
		global $wpdb;		
		$x = preg_replace('/\s*,\s*/', ',', $value);
		$split=explode('|', $x);
					
    $get_rel_val = array();
    foreach($split as $key=>$item){
					
        foreach(explode(',',$item) as $value){
						$get_rel_val[$key][] = $value;
        }
    }					  
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
										
												$get_rel_value = $wpdb->_real_escape($get_rel_vals[$i]);
											
												$pquery = "select * from {$wpdb->prefix}posts where post_title = '{$get_rel_value}' and post_status != 'trash'";
											
												$post = $wpdb->get_results($pquery, ARRAY_A);								
												$pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
												$post1 = $wpdb->get_results($pquery);  
												if($mode == 'Update'){
													if(!empty($get_rel_value)) {
														global $wpdb;
														$parentquery = "select parent_id,child_id from {$wpdb->prefix}toolset_associations where relationship_id ='{$post1[0]->id}' AND parent_id='{$pID}'";
														$parent_id=$wpdb->get_results($parentquery, ARRAY_A);
														$array=json_decode(json_encode($parent_id),true);
														foreach($array as $parkey => $parval){
															if($parval['parent_id']==$pID && $parval['child_id']==$post[0]['ID']){
																$query1 = "select id,intermediary_id from {$wpdb->prefix}toolset_associations where relationship_id ='{$post1[0]->id}' AND parent_id='{$pID}' AND child_id='{$post[0]['ID']}'";
														        $id1 =$wpdb->get_results($query1, ARRAY_A);
																$array1=json_decode(json_encode($id1),true);
																$wpdb->delete($wpdb->prefix . 'toolset_associations',array('id'=>$array1[0]['id']));
															   $wpdb->delete($wpdb->prefix . 'posts',array('ID'=>$array1[0]['intermediary_id']));
															}
															elseif($parval['parent_id']==$pID){
																$wpdb->delete($wpdb->prefix . 'toolset_associations',array('parent_id'=>$pID));
															}
															
														}
														$interpostquery ="select display_name_plural,intermediary_type from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
							                            $interpost = $wpdb->get_results($interpostquery);
							                            $array=array('post_title' => $interpost[0]->display_name_plural.': '.$post[0]['ID'].'-'.$pID,
							                            'post_type' => $interpost[0]->intermediary_type,'post_status'=> 'publish');
													    $interid=wp_insert_post($array);
														$wpdb->insert($wpdb->prefix . 'toolset_associations', array(
															'relationship_id' => $post1[0]->id,
															'parent_id' => $pID,
															'child_id' => $post[0]['ID'],
															'intermediary_id' => $interid
														));
														$wpdb->insert($wpdb->prefix . 'toolset_associations', array(
															'relationship_id' => $post1[0]->id,
															'parent_id' => $post[0]['ID'],
															'child_id' => $pID,
															'intermediary_id' => '0'
														));

													}
													else{
												     $wpdb->delete($wpdb->prefix . 'toolset_associations',array('parent_id'=>$pID));
													}
												}
												
												else{
													$interpostquery ="select display_name_plural,intermediary_type from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
							                        $interpost = $wpdb->get_results($interpostquery);
							                        $array=array('post_title' => $interpost[0]->display_name_plural.': '.$post[0]['ID'].'-'.$pID,
							                            'post_type' => $interpost[0]->intermediary_type,'post_status'=> 'publish');
												    $interid=wp_insert_post($array);
													$wpdb->insert($wpdb->prefix . 'toolset_associations', array(
														'relationship_id' => $post1[0]->id,
														'parent_id' => $pID,
														'child_id' => $post[0]['ID'],
														'intermediary_id' => $interid
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
