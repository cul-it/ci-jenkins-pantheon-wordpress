<?php

namespace Smackcoders\WCSV;
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ToolsetImporter {
	public function format($Parent_Group,$postTypeValue) {

		if(!empty($Parent_Group)) {

			return $this->separateElements($Parent_Group,$postTypeValue);

		}else {
			return 0;
		}
	}

	public function separateElements($parentGroupName,$postTypeValue) {
		$explodedParent=$this->explodeFunction('|',$parentGroupName);
		global $wpdb;
		for ($i = 0; $i < count($explodedParent); $i++)
		{
			$post_title = $wpdb->_real_escape($explodedParent[$i]);
			$result = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."posts WHERE post_title='{$post_title}' and post_type='{$postTypeValue}'");
			if (!empty($result[0]->id)) {
				$post_id = $result[0]->id;
				$result = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id ='{$post_id}' and meta_key ='_wp_types_group_fields'");
				$elementString=$result[0]->meta_value;
				$elementArray[]=$this->explodeFunction(',',$elementString);                           
			} 
			else {
				return 0;
			}
		}
		return $elementArray;
	}

	

	public function getRelationshipId($groupName)
	{
		global $wpdb;
		$relation_id = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."toolset_relationships WHERE slug = '{$groupName}'");
		$relation_id=$relation_id[0]->id;
		return $relation_id;
	}

	public function insertPost($groupName,$title)
	{
		global $wpdb;
		$post_id = wp_insert_post( array(
					'post_status' => 'publish',
					'post_title' => $title,
					'post_type' => $groupName
					));
		return $post_id;
	}

	public function insertRelationship($groupRelationId,$postId,$childPostId)
	{
		
		global $wpdb;
		include_once( 'wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		$plugin_version = $plugins['types/wpcf.php']['Version'];
		if($plugin_version < '3.4.1'){
			$wpdb->insert($wpdb->prefix.'toolset_associations', array(
						'relationship_id'   => $groupRelationId,
						'parent_id'         => $postId,
						'child_id'          => $childPostId,
						'intermediary_id'   => '0'
						));
		}
		else{
			$pq = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$postId}'";
			$pqres = $wpdb->get_results($pq);
			$parent_id = $pqres[0]->id;
			if($parent_id){
				$parent_id =$parent_id;
			}
			else{
				$wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
					'element_id' => $postId,
					'domain' => 'posts',
					'wpml_trid' => '0'
					
				));

			}
			
			$pq1 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$postId}'";
			$pqres1 = $wpdb->get_results($pq1);
			$parent_id = $pqres1[0]->id;
			$wpdb->update( $wpdb->prefix . 'toolset_connected_elements' , array( 'group_id' => $parent_id) , array( 'id' => $parent_id ));
			$wpdb->insert($wpdb->prefix . 'toolset_connected_elements', array(
				'element_id' => $childPostId,
				'domain' => 'posts',
				'wpml_trid' => '0'
				
			));
			$pq2 = "select id from {$wpdb->prefix}toolset_connected_elements where element_id ='{$childPostId}'";
			$pqres2 = $wpdb->get_results($pq2);
			$child_id = $pqres2[0]->id;
		
			$wpdb->update( $wpdb->prefix . 'toolset_connected_elements' , array( 'group_id' => $child_id) , array( 'id' => $child_id ));
			$wpdb->insert($wpdb->prefix.'toolset_associations', array(
				'relationship_id'   => $groupRelationId,
				'parent_id'         => $parent_id,
				'child_id'          => $child_id,
				'intermediary_id'   => '0'
				));

		}
	}

	public function getRepeatableMetaValue($value)
	{
		global $wpdb;
		$meta = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id = {$value} and meta_key = '_wp_types_group_fields'");
		return $meta[0]->meta_value;
	}

	public function getRepeatableName($value)
	{
		global $wpdb;
		$meta = $wpdb->get_results("SELECT post_name FROM ".$wpdb->prefix."posts WHERE id = {$value}");

		return $meta[0]->post_name;
	}

	public function explodeFunction($symbol,$value)
	{
		$explode = explode($symbol,$value);

		foreach($explode as $key => $value)
		{
			if(is_null($value) || $value == '')
				unset($explode[$key]);
		}
		return $explode;
	}

	public function getMetaKeys($postId,$postType)
	{
		global $wpdb;
		if ($postType == 'Users') {
			return $wpdb->get_results("SELECT umeta_id,meta_key FROM ".$wpdb->prefix."usermeta WHERE user_id = {$postId}",ARRAY_A);
		}else{
			return $wpdb->get_results("SELECT meta_id,meta_key FROM ".$wpdb->prefix."postmeta WHERE post_id = {$postId}",ARRAY_A);
		}

	}
	public function checkTermKeys($postId,$postType)
	{
		global $wpdb;
		$result=$wpdb->get_results("SELECT term_id FROM ".$wpdb->prefix."term_taxonomy WHERE taxonomy = '{$postType}' ",ARRAY_A);
		if (!empty($result)){
			return 1;
		}
	}
	public function getTermKeys($postId,$postType)
	{
		global $wpdb;
		return $wpdb->get_results("SELECT meta_id,meta_key FROM ".$wpdb->prefix."termmeta WHERE term_id = {$postId}",ARRAY_A);
	}

	public function deleteMetaKeys($metaId,$postType)
	{
		global $wpdb;
		if ($postType == 'Users') {
			$wpdb->delete( $wpdb->prefix.'usermeta', array( 'umeta_id' => $metaId));
		}else{
			$wpdb->delete( $wpdb->prefix.'postmeta', array( 'meta_id' => $metaId));
		}

	}
	public function deleteTermMetaKeys($postId,$postType)
	{

		global $wpdb;       
		$wpdb->delete( $wpdb->prefix.'termmeta', array( 'term_id' => $postId));

	}

	public function getMetaKeyId($postId,$metaKeyname,$postType)
	{
		global $wpdb;
		if ($postType == 'Users') {
			return $wpdb->get_results("SELECT umeta_id,meta_value FROM ".$wpdb->prefix."usermeta WHERE user_id = {$postId} and meta_key = '{$metaKeyname}'",ARRAY_A);
		}else{
			return $wpdb->get_results("SELECT meta_id,meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id = {$postId} and meta_key = '{$metaKeyname}'",ARRAY_A);
		}
	}
	public function findRelationship($postId)
	{
		global $wpdb;
		include_once( 'wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		$plugin_version = $plugins['types/wpcf.php']['Version'];
		if($plugin_version < '3.4.1'){
			return $wpdb->get_results("SELECT id,child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = {$postId}",ARRAY_A);
		}
		else{
		
			$n=$wpdb->get_results("SELECT group_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE element_id = {$postId}",ARRAY_A);
			$pg = $n[0]['group_id'];
			return $wpdb->get_results("SELECT id,child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = {$pg}",ARRAY_A);
		}
	}
}
