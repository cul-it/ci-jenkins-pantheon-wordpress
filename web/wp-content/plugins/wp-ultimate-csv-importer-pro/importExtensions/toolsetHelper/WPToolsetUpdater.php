<?php

namespace Smackcoders\WCSV;
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * 
 */
require_once "ToolsetImporter.php";

/**
 * 
 */
class WPToolsetUpdater extends ToolsetImporter
{

	private static $WPToolsetUpdater = NULL;
	private $dataArray;
	private $metadata;
	private $fieldType;
	private $meta_id;
	private $updatedFieldinfo = array();


	static function getInstance() {
		if (self::$WPToolsetUpdater == NULL) {
			self::$WPToolsetUpdater = new WPToolsetUpdater();
		}
		return self::$WPToolsetUpdater;
	}

	function set($dataArray, $metaData, $fieldType,$wpTypesFields,$postType) {

		$this->dataArray 		= $dataArray;
		$this->metaData 		= $metaData;
		$this->fieldType 		= $fieldType;
		$this->wpTypesFields 	= $wpTypesFields;
		$this->postType 		= $postType;

		if ($this->postType =='Users') {
			$this->meta_id ='umeta_id';
		}else{
			$this->meta_id ='meta_id';
		}
	}

	function update($postId,$post_val) {
		$this->updatedFieldinfo=array();

		if (isset($this->dataArray['Parent_Group'])) {

			$this->loopAndUpdate($postId);

			require_once "WPToolsetImporter.php";
			$wpToolsetImporter = WPToolsetImporter::getInstance();
			$wpToolsetImporter->set($this->dataArray, $this->metaData, $this->fieldType,$this->wpTypesFields,$this->postType);
			$wpToolsetImporter->import($postId,$post_val);

		}else{
			$checkTermKeys = $this->checkTermKeys($postId,$this->postType);
			if($checkTermKeys==1){
				$getTermData = $this->getTermKeys($postId,$this->postType);
				$this->deleteTermMetaKeys($postId,$this->postType);
			}
			else{
				$getMetaData = $this->getMetaKeys($postId,$this->postType);
			}
			$this->deleteCustomFields($getMetaData);			
			require_once "WPToolsetImporter.php";
			$wpToolsetImporter = WPToolsetImporter::getInstance();
			$wpToolsetImporter->set($this->dataArray, $this->metaData, $this->fieldType,$this->wpTypesFields,$this->postType);
			$wpToolsetImporter->import($postId,$post_val);	

		}
	}

	function loopAndUpdate($postId){
		global $wpdb;

		$getMetaData = $this->getMetaKeys($postId,$this->postType);

		$this->deleteCustomFields($getMetaData);

		$allRelationship=$this->findRelationship($postId);

		include_once( 'wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		$plugin_version = $plugins['types/wpcf.php']['Version'];
		

		if (!empty($allRelationship)) {

			foreach ($allRelationship as $key => $value) {

				$this->loopAndUpdate($value['child_id']);
				if($plugin_version < '3.4.1'){
					$wpdb->delete( $wpdb->prefix.'toolset_associations', array( 'id' => $value['id']));
					$wpdb->delete( $wpdb->prefix.'posts', array( 'id' => $value['child_id']));
				}
				else{
				
					$pquery = "select element_id from {$wpdb->prefix}toolset_connected_elements where id ='{$value['child_id']}'";	
					$pqueryres1 = $wpdb->get_results($pquery);
					$p_id = $pqueryres1[0]->element_id;
					$wpdb->delete( $wpdb->prefix.'toolset_associations', array( 'id' => $value['id']));
					//$wpdb->delete( $wpdb->prefix.'toolset_associations', array( 'id' => $p_id));
					$wpdb->delete( $wpdb->prefix.'posts', array( 'id' => $p_id));

				}
				
			}
		}

	}

	function deleteCustomFields($getMetaData){
		if (!empty($getMetaData)) {

			foreach ($getMetaData as $key => $value) {					

				$metaKey=$value['meta_key'];

				$fieldTypeArray=array_flip($this->metaData);

				if (array_key_exists($metaKey, $fieldTypeArray)) {

					$getFieldType=$fieldTypeArray[$metaKey];
					$fieldType=$this->fieldType[$getFieldType];

					$metaId=$value[$this->meta_id];

					if ($fieldType == 'post') {
						$this->checkFieldType($metaId,$fieldType,$metaKey);
					}else{
						$this->deleteMetaKeys($metaId,$this->postType);
					}
				}	
			}
		}
	}

	function checkFieldType($postId,$fieldType,$metaKey){

		global $wpdb;

		if ($fieldType == 'post') {
			$metaKeyIds=$this->getMetaKeyId($postId,$metaKey,$this->postType);
			$metaId=$metaKeyIds[0][$this->meta_id];
			$parent_id=$metaKeyIds[0]['meta_value'];
			include_once( 'wp-admin/includes/plugin.php' );
		    $plugins = get_plugins();
			$plugin_version = $plugins['types/wpcf.php']['Version'];
			if($plugin_version < '3.4.1'){
				$par_con = $wpdb->get_results("SELECT group_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE element_id = {$parent_id} ",ARRAY_A);
				$parconid=$par_con[0]['group_id'];
				$child_con = $wpdb->get_results("SELECT group_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE element_id = {$postId} ",ARRAY_A);
				$childconid = $child_con[0]['group_id'];
				$result=$wpdb->get_results("SELECT id,relationship_id FROM ".$wpdb->prefix."toolset_associations WHERE child_id = {$childconid} and parent_id = {$parconid} ",ARRAY_A);
				$id=$result[0]['id'];
				$relationship_id=$result[0]['relationship_id'];
			}
			else{

				$result=$wpdb->get_results("SELECT id,relationship_id FROM ".$wpdb->prefix."toolset_associations WHERE child_id = {$postId} and parent_id = {$parent_id} ",ARRAY_A);
				$id=$result[0]['id'];
				$relationship_id=$result[0]['relationship_id'];
			}
          

			$wpdb->delete( $wpdb->prefix.'toolset_associations', array( 'id' => $id));
			$this->deleteMetaKeys($metaId,$this->postType);

		}
	}

}