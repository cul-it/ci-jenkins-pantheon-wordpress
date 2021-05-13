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

class ToolsetNewImport {
    private static $toolset_new_instance = null;

    public static function getInstance() {
		if (ToolsetNewImport::$toolset_new_instance == null) {
			ToolsetNewImport::$toolset_new_instance = new ToolsetNewImport;
			return ToolsetNewImport::$toolset_new_instance;
		}
		return ToolsetNewImport::$toolset_new_instance;
    }
    
    public function normalRelationshipnew($data_array,$pID,$mode,$type)
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
                          
                            $checkinter = "select intermediary_type,parent_types,child_types from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
                            $is_inter = $wpdb->get_results($checkinter);  
                            $is_intermediate = $is_inter[0]->intermediary_type;

                            $is_parenttype = $is_inter[0]->parent_types;
                            $is_childtype = $is_inter[0]->child_types;
                           

                            $parent_typequery = "select type from {$wpdb->prefix}toolset_type_sets where set_id ='{$is_parenttype}'";
                            $partyperes = $wpdb->get_results($parent_typequery);  
                            $parent_type =  $partyperes[0]->type;
                         

                            $child_typequery = "select type from {$wpdb->prefix}toolset_type_sets where set_id ='{$is_childtype}'";
                            $childtyperes = $wpdb->get_results($child_typequery);  
                            $child_type =  $childtyperes[0]->type;

                            
                            
                            $pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
                            $post = $wpdb->get_results($pquery);
                            
                            
                            //for version 3.4.1 and above
                            $parrelid = $get_rel_vals[$i];
                            $parcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$parrelid}'";
                            $parconid = $wpdb->get_results($parcon, ARRAY_A);
                            $childcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
                            $childconid = $wpdb->get_results($childcon, ARRAY_A);
                            if($parconid[0]['group_id']){
                                $parent_id =$parconid[0]['group_id'];
                            }
                            else{
                                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                    'element_id' => $parrelid,
                                    'domain' => 'posts',
                                    'wpml_trid' => '0'
                                ));
                                $pq = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$parrelid}'";
                                $pqres = $wpdb->get_results($pq);
                                $parent_id = $pqres[0]->id;
                                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$parent_id' where id = $parent_id");
                                //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $parent_id], ['id' => $parent_id]);
                            }
                            if($childconid[0]['group_id']){
                                $child_id =$childconid[0]['group_id'];
                            }
                            else{
                                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                    'element_id' => $pID,
                                    'domain' => 'posts',
                                    'wpml_trid' => '0'
                                ));
                                $pq1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
                                $pqres1 = $wpdb->get_results($pq1);
                                $child_id = $pqres1[0]->id;
                                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$child_id' where id = $child_id");
                                //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $child_id], ['id' => $child_id]);
                            }
                            if(!empty($is_intermediate)){

                                $interpostquery ="select display_name_plural,intermediary_type from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
                                $interpost = $wpdb->get_results($interpostquery);
                                $array=array('post_title' => $interpost[0]->display_name_plural.': '.$pID.' - '.$get_rel_vals[$i],
                                'post_type' => $interpost[0]->intermediary_type,'post_status'=> 'publish');
                                $interid=wp_insert_post($array);
                                                
                                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                    'element_id' => $interid,
                                    'domain' => 'posts',
                                    'wpml_trid' => '0'
                                ));
                                $pinter = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$interid}'";
                                $pinterres = $wpdb->get_results($pinter);
                                $inter_id = $pinterres[0]->id;
                                
        
                                //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $inter_id], ['id' => $inter_id]);
                                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$inter_id' where id = $inter_id");
                            }
                            else{
                                    $inter_id = 0;
                            }

                            if($type == $parent_type){
                                $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                    'relationship_id' => $post[0]->id,
                                    'parent_id' => $child_id,
                                    'child_id' => $parent_id,
                                    'intermediary_id' => '0'
                                ));

                            }
                            else{

                                $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                    'relationship_id' => $post[0]->id,
                                    'parent_id' => $parent_id,
                                    'child_id' => $child_id,
                                    'intermediary_id' => $inter_id
                                ));
                            }
                              
                        
                        }
                        else{
                           $get_rel_value = $wpdb->_real_escape($get_rel_vals[$i]);
                        
                            $pquery = "select * from {$wpdb->prefix}posts where post_title = '{$get_rel_value}' and post_status != 'trash'";
                        
                            $post = $wpdb->get_results($pquery, ARRAY_A);								
                            $pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
                            $post1 = $wpdb->get_results($pquery);  
                            
                          
                            $checkinter = "select intermediary_type,parent_types,child_types from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
                            $is_inter = $wpdb->get_results($checkinter);  
                            $is_intermediate = $is_inter[0]->intermediary_type;

                            $is_parenttype = $is_inter[0]->parent_types;
                            $is_childtype = $is_inter[0]->child_types;
                           

                            $parent_typequery = "select type from {$wpdb->prefix}toolset_type_sets where set_id ='{$is_parenttype}'";
                            $partyperes = $wpdb->get_results($parent_typequery);  
                            $parent_type =  $partyperes[0]->type;
                         

                            $child_typequery = "select type from {$wpdb->prefix}toolset_type_sets where set_id ='{$is_childtype}'";
                            $childtyperes = $wpdb->get_results($child_typequery);  
                            $child_type =  $childtyperes[0]->type;
                           
                           // die('ihn');

                            if($mode=='Update'){
                             
                                if(!empty($get_rel_value)) {
                                    global $wpdb;
                                    //new
                                    //parentid
                                    $parcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
                                    $parconid = $wpdb->get_results($parcon, ARRAY_A);
                                  
                                    if($parconid[0]['group_id']){
                                        $parentid =$parconid[0]['group_id'];
                                       
                                    }
                                    else{
                                        $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                            'element_id' => $pID,
                                            'domain' => 'posts',
                                            'wpml_trid' => '0'
                                        ));
                                        $pq = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
                                        $pqres = $wpdb->get_results($pq);
                                        $parentid = $pqres[0]->id;
                                        $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$parentid' where id = $parentid");
                                        
                                    }
                                   
                                    ////childid
                                    $childcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$post[0]['ID']}'";
                                    $childconid = $wpdb->get_results($childcon, ARRAY_A);
                                    if($childconid[0]['group_id']){
                                        $child_id =$childconid[0]['group_id'];
                                    }
                                    else{
                                        $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                            'element_id' => $post[0]['ID'],
                                            'domain' => 'posts',
                                            'wpml_trid' => '0'
                                        ));
                                        $pq1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$post[0]['ID']}'";
                                        $pqres1 = $wpdb->get_results($pq1);
                                        $child_id = $pqres1[0]->id;
                                        $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$child_id' where id = $child_id");
                                        //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $child_id], ['id' => $child_id]);
                                    }

                                    if(!empty($is_intermediate)){

                                        $interpostquery ="select display_name_plural,intermediary_type from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
                                        $interpost = $wpdb->get_results($interpostquery);
                                        $array=array('post_title' => $interpost[0]->display_name_plural.': '.$post[0]['ID'].' - '.$pID,
                                        'post_type' => $interpost[0]->intermediary_type,'post_status'=> 'publish');
                                        $interid=wp_insert_post($array);
                                                        
                                        $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                            'element_id' => $interid,
                                            'domain' => 'posts',
                                            'wpml_trid' => '0'
                                        ));
                                        $pinter = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$interid}'";
                                        $pinterres = $wpdb->get_results($pinter);
                                        $inter_id = $pinterres[0]->id;
                                        
                
                                        //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $inter_id], ['id' => $inter_id]);
                                        $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$inter_id' where id = $inter_id");
                                    }
                                    else{
                                         $inter_id = 0;
                                    }
                                   
                                     

                                    ///
                                    if($type == $parent_type){
                                        $parentquery = "select parent_id,child_id from {$wpdb->prefix}toolset_associations where relationship_id ='{$post1[0]->id}' AND parent_id='{$parentid}'";
                                	    $parent_id=$wpdb->get_results($parentquery, ARRAY_A);
                                        $array=json_decode(json_encode($parent_id),true);

                                       
                                        $childidarray .= $child_id.',';
                                       
                                        foreach($array as $parkey => $parval){
                                            //if($parval['parent_id']==$parentid && $parval['child_id']==$child_id){
                                            if($parval['parent_id']==$parentid ){
                                                $child =explode(',',$childidarray);
                                                $count = count($child);    
                                                   
                                                if($count <= 2){
                                                         
                                                    //$query1 = "select id,intermediary_id from {$wpdb->prefix}toolset_associations where relationship_id ='{$post1[0]->id}' AND parent_id='{$parentid}'";
                                                    $query1 = "select id,intermediary_id from {$wpdb->prefix}toolset_associations where relationship_id ='{$post1[0]->id}' AND parent_id='{$parentid}'";
                                                   
                                                    $id1 =$wpdb->get_results($query1, ARRAY_A);
                                                  
                                                    $array1=json_decode(json_encode($id1),true);
                                                    
                                                    foreach($array1 as $arraykey1 => $arrval1){
                                                        $delconinter =$arrval1['intermediary_id'];
                                                        $wpdb->delete($wpdb->prefix . 'toolset_associations',array('id'=>$arrval1['id']));
                                                        $q = "select element_id from {$wpdb->prefix}toolset_connected_elements where id ='{$delconinter}' ";
                                                   
                                                        $delinter =$wpdb->get_results($q, ARRAY_A); 
                                                        $delinterid = $delinter[0]['element_id'];
                                                        $wpdb->get_results("DELETE FROM {$wpdb->prefix}posts WHERE `ID` = $delinterid");
                                                      //  $wpdb->delete($wpdb->prefix . 'posts',array('ID'=>$arrval1['intermediary_id']));
                                                        
                                                    }

                                                }  
                                                
                                                
                                                
                                            }
                                            
                                          
                                        }
                                        $childarray .= $child_id.',';
                                        $childcount = count($childarray);
                                        $checkchildmax = "select cardinality_child_max from {$wpdb->prefix}toolset_relationships where id ='{$post1[0]->id}'";	
                                        $childmax = $wpdb->get_results($checkchildmax, ARRAY_A);
                                      
                                        if($childmax[0]['cardinality_child_max'] == 1){
                                            $childexp = explode(',',$childarray);
                                            if($child_id == $childexp[0]){
                                                $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                                    'relationship_id' => $post1[0]->id,
                                                    'parent_id' => $parentid,
                                                    'child_id' => $child_id,
                                                    'intermediary_id' => $inter_id
                                                ));
                                            }
                                        }
                                        else{
                                            $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                                'relationship_id' => $post1[0]->id,
                                                'parent_id' => $parentid,
                                                'child_id' => $child_id,
                                                'intermediary_id' => $inter_id
                                            ));
                                        }


                                    }
                                	
                                    
                                	else{
                                        $childquery = "select parent_id,child_id from {$wpdb->prefix}toolset_associations where relationship_id ='{$post1[0]->id}' AND  child_id ='{$parentid}'";
                                        $childquery1=$wpdb->get_results($childquery, ARRAY_A);
                                        $array1=json_decode(json_encode($childquery1),true);
                                        $childidarray .= $child_id.',';
                                        foreach($array1 as $parkey => $parval){
                                            if($parval['parent_id']==$child_id ){
                                                $child =explode(',',$childidarray);
                                                $count = count($child);           
                                                if($count <= 2){
                                                    $query1 = "select id,intermediary_id from {$wpdb->prefix}toolset_associations where relationship_id ='{$post1[0]->id}' AND child_id='{$parentid}'";
                                                    $id1 =$wpdb->get_results($query1, ARRAY_A);
                                                    $array2=json_decode(json_encode($id1),true);
                                                  
                                                    foreach($array2 as $arraykey2 => $arrval2){
                                                        $delconinter =$arrval2['intermediary_id'];
                                                        $wpdb->delete($wpdb->prefix . 'toolset_associations',array('id'=>$arrval2['id']));
                                                        $q = "select element_id from {$wpdb->prefix}toolset_connected_elements where id ='{$delconinter}' ";
                                                   
                                                        $delinter =$wpdb->get_results($q, ARRAY_A);
                                                       
                                                        $delinterid = $delinter[0]['element_id'];
                                                        $wpdb->get_results("DELETE FROM {$wpdb->prefix}posts WHERE `ID` = $delinterid");
                                                    
                                                    }
                                                }   
                                            
                                            }
                                            
                                        }
                                    
                                        $childarray .= $child_id.',';
                                        $childcount = count($childarray);
                                        $checkchildmax = "select cardinality_parent_max from {$wpdb->prefix}toolset_relationships where id ='{$post1[0]->id}'";	
                                        $childmax = $wpdb->get_results($checkchildmax, ARRAY_A);
                                       
                                        if($childmax[0]['cardinality_parent_max'] == 1){
                                            $childexp = explode(',',$childarray);
                                            if($child_id == $childexp[0]){
                                                $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                                    'relationship_id' => $post1[0]->id,
                                                    'parent_id' => $child_id,
                                                    'child_id' =>$parentid,
                                                    'intermediary_id' => $inter_id
                                                ));
                                            }
                                        }
                                        else{
                                           
                                            $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                                'relationship_id' => $post1[0]->id,
                                                'parent_id' => $child_id,
                                                'child_id' =>$parentid,
                                                'intermediary_id' => $inter_id
                                            ));
                                        }

                                    }
                                    

                                }
                                else{
                                  $wpdb->delete($wpdb->prefix . 'toolset_associations',array('parent_id'=>$pID));
                                }
                            }
                            
                            else{
                                //insert into association table for version >= 3.4.1   
           
                                $po =$post[0]['ID'];
                               
                               
                                global $wpdb;
                                //$pquery1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$po}'";	
                                $pquery1 = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";	
                                $pmquery1 = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$po}'";
                                
                                $pqueryres1 = $wpdb->get_results($pquery1);
                              
                                if($pqueryres1[0]->group_id){
                                    $parent_id = $pqueryres1[0]->group_id;
                                }
                                else{
                                    $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                        'element_id' => $pID,
                                        'domain' => 'posts',
                                        'wpml_trid' => '0'
                                    ));
                                    $pq1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
                                    $pqres1 = $wpdb->get_results($pq1);
                                    $parent_id = $pqres1[0]->id;
                                    //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $parent_id], ['id' => $parent_id]);
                                    $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$parent_id' where id = $parent_id");
                                }
                              
                                $pmqueryres1 = $wpdb->get_results($pmquery1);
                               
                                if($pmqueryres1[0]->group_id){
                                    $child_id = $pmqueryres1[0]->group_id;
                                }
                                else{
                                    $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                        'element_id' => $po,
                                        'domain' => 'posts',
                                        'wpml_trid' => '0'
                                    ));
                                    $pq2 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$po}'";
                                    $pqres2 = $wpdb->get_results($pq2);
                                    $child_id = $pqres2[0]->id;
                                    $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$child_id' where id = $child_id");
                                  
                                }
                                $inter_id = 0;
                              
                                if(!empty($is_intermediate)){

                                    $interpostquery ="select display_name_plural,intermediary_type from {$wpdb->prefix}toolset_relationships where slug ='{$value}'";
                                    $interpost = $wpdb->get_results($interpostquery);
                                    $array=array('post_title' => $interpost[0]->display_name_plural.': '.$post[0]['ID'].' - '.$pID,
                                    'post_type' => $interpost[0]->intermediary_type,'post_status'=> 'publish');
                                    $interid=wp_insert_post($array);
                                                    
                                    $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                                        'element_id' => $interid,
                                        'domain' => 'posts',
                                        'wpml_trid' => '0'
                                    ));
                                    $pinter = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$interid}'";
                                    $pinterres = $wpdb->get_results($pinter);
                                    $inter_id = $pinterres[0]->id;
                                    
            
                                    //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $inter_id], ['id' => $inter_id]);
                                    $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$inter_id' where id = $inter_id");
                                }
                                else{
                                     $inter_id = 0;
                                }
                               
                             
                                if($type == $parent_type){
                                    $childarray .= $child_id.',';
                                    $childcount = count($childarray);
                                    $checkchildmax = "select cardinality_child_max from {$wpdb->prefix}toolset_relationships where id ='{$post1[0]->id}'";	
                                    $childmax = $wpdb->get_results($checkchildmax, ARRAY_A);
                                 
                                    if($childmax[0]['cardinality_child_max'] == 1){

                                        $childexp = explode(',',$childarray);
                                        if($child_id == $childexp[0]){
                                            $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                                'relationship_id' => $post1[0]->id,
                                                'parent_id' => $parent_id,
                                                'child_id' => $child_id,
                                                'intermediary_id' => $inter_id
                                            ));

                                        }
                                        

                                    }
                                    else{
                                        $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                            'relationship_id' => $post1[0]->id,
                                            'parent_id' => $parent_id,
                                            'child_id' => $child_id,
                                            'intermediary_id' => $inter_id
                                        ));

                                    }
                                    
                                  
                                }
                                else{
                                    
                                    $childarray .= $child_id.',';
                                    $childcount = count($childarray);
                                    $checkchildmax = "select cardinality_parent_max from {$wpdb->prefix}toolset_relationships where id ='{$post1[0]->id}'";	
                                    $childmax = $wpdb->get_results($checkchildmax, ARRAY_A);
                                   
                                    if($childmax[0]['cardinality_parent_max'] == 1){
                                        $childexp = explode(',',$childarray);
                                        if($child_id == $childexp[0]){
                                            $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                                'relationship_id' => $post1[0]->id,
                                                'parent_id' => $child_id,
                                                'child_id' => $parent_id,
                                                'intermediary_id' => $inter_id
                                            ));
                                        }
                                        
                                    }
                                    else{
                                        $wpdb->insert($wpdb->prefix . 'toolset_associations', array(
                                            'relationship_id' => $post1[0]->id,
                                            'parent_id' => $child_id,
                                            'child_id' => $parent_id,
                                            'intermediary_id' => $inter_id
                                        ));
                                    }
                                 
                                }
          
                                
                            }
                            
            
                        }
                    }
                }
            }
		}

    }

    public function intermediateRelationshipnew($data_array,$pID,$type)
	{ 

        $value=$data_array['intermediate'];
		$get_rel_vals = explode(',', $value);
		global $wpdb;
			
		$row = $wpdb->delete($wpdb->prefix.'toolset_associations',array('intermediary_id' => $pID));
        if(intval($get_rel_vals[0])){
            $parrelid = $get_rel_vals[0];
            $parcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$parrelid}'";
            $parconid = $wpdb->get_results($parcon, ARRAY_A);
          
            $childrelid =$get_rel_vals[1];
            $childcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$childrelid}'";
            $childconid = $wpdb->get_results($childcon, ARRAY_A);
         
            if($parconid[0]['group_id']){
                $parent_id =$parconid[0]['group_id'];
            }
            else{
                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                    'element_id' => $parrelid,
                    'domain' => 'posts',
                    'wpml_trid' => '0'
                ));
                $pq = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$parrelid}'";
                $pqres = $wpdb->get_results($pq);
                $parent_id = $pqres[0]->id;
                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$parent_id' where id = $parent_id");
                //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $parent_id], ['id' => $parent_id]);
            }
          
            if($childconid[0]['group_id']){
                $child_id =$childconid[0]['group_id'];
            }
            else{
                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                    'element_id' => $childrelid,
                    'domain' => 'posts',
                    'wpml_trid' => '0'
                ));
                $pq1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$childrelid}'";
                $pqres1 = $wpdb->get_results($pq1);
                $child_id = $pqres1[0]->id;
                //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $child_id], ['id' => $child_id]);
                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$child_id' where id = $child_id");
            }
         
            $intercon = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
            $interconid = $wpdb->get_results($intercon, ARRAY_A);
         
            if($interconid[0]['id']){
                $inter_id =$interconid[0]['id'];
            }
            else{
                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                    'element_id' => $pID,
                    'domain' => 'posts',
                    'wpml_trid' => '0'
                ));
                $pqc1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
                $pqresc1 = $wpdb->get_results($pqc1);
                $inter_id = $pqresc1[0]->id;
                //$wpdb->update($wpdb->toolset_connected_elements, ['group_id' => $inter_id], ['id' => $inter_id]);
                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$inter_id' where id = $inter_id");
            }
            $post = get_post($get_rel_vals[0]);

            $pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$data_array['relationship_slug']}'";	
            $post = $wpdb->get_results($pquery);
            $wpdb->insert($wpdb->prefix.'toolset_associations',array('relationship_id' =>$post[0]->id ,'parent_id' => $parent_id ,'child_id' => $child_id
                        ,'intermediary_id' => $inter_id ));

        }else{
            $pquery = "select * from {$wpdb->prefix}posts where post_title = '{$get_rel_vals[0]}' and post_status != 'trash'";	
            $post = $wpdb->get_results($pquery,ARRAY_A);
         
            $pqueryc = "select * from {$wpdb->prefix}posts where post_title = '{$get_rel_vals[1]}' and post_status != 'trash'";	
            $postc = $wpdb->get_results($pqueryc,ARRAY_A);
          
            $parrelid = $post[0]['ID'];
            $childrelid =$postc[0]['ID'];
            $parcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$parrelid}'";
            $parconid = $wpdb->get_results($parcon, ARRAY_A);
            
            $childcon = "select group_id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$childrelid }'";
            $childconid = $wpdb->get_results($childcon, ARRAY_A);
          
            if($parconid[0]['group_id']){
                $parent_id =$parconid[0]['group_id'];
            }
            else{
                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                    'element_id' => $parrelid,
                    'domain' => 'posts',
                    'wpml_trid' => '0'
                ));
                $pq = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$parrelid}'";
                $pqres = $wpdb->get_results($pq);
                $parent_id = $pqres[0]->id;
                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$parent_id' where id = $parent_id");
              
            }
          
            if($childconid[0]['group_id']){
                $child_id =$childconid[0]['group_id'];
            }
            else{
                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                    'element_id' => $childrelid,
                    'domain' => 'posts',
                    'wpml_trid' => '0'
                ));
                $pq1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$childrelid}'";
                $pqres1 = $wpdb->get_results($pq1);
                $child_id = $pqres1[0]->id;
                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$child_id' where id = $child_id");
               
            }
        
            $intercon = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
            $interconid = $wpdb->get_results($intercon, ARRAY_A);
       
            if($interconid[0]['id']){
                $inter_id =$interconid[0]['id'];
            }
            else{
                $wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
                    'element_id' => $pID,
                    'domain' => 'posts',
                    'wpml_trid' => '0'
                ));
                $pqc1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$pID}'";
                $pqresc1 = $wpdb->get_results($pqc1);
                $inter_id = $pqresc1[0]->id;
                $wpdb->get_results("UPDATE {$wpdb->prefix}toolset_connected_elements set group_id = '$inter_id' where id = $inter_id");
               
            }
            $pquery = "select id from {$wpdb->prefix}toolset_relationships where slug ='{$data_array['relationship_slug']}'";	
            $post1 = $wpdb->get_results($pquery);
                $wpdb->insert($wpdb->prefix.'toolset_associations',array('relationship_id' => $post1[0]->id,'parent_id' => $parent_id ,'child_id' => $child_id
                        ,'intermediary_id' => $inter_id));
        }

		
    
    }



}
