<?php

namespace Smackcoders\WCSV;
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * 
 */

require_once "ToolsetImporter.php";

class WPToolsetImporter extends ToolsetImporter
{
	private static $wpToolsetImporter = NULL,$mediaInstance;
	private $dataArray;
	private $metaData;
	private $fieldType;

	private $postType;
	private $wpTypesFields;


	static function getInstance() {
		if (self::$wpToolsetImporter == NULL) {
			self::$wpToolsetImporter = new WPToolsetImporter();
			self::$mediaInstance = new MediaHandling();
		}
		return self::$wpToolsetImporter;
	}

	function set($dataArray, $metaData, $field_Type,$wpTypesFields,$postType) {

		$this->dataArray 		= $dataArray;
		$this->metaData 		= $metaData;
		$this->fieldType 		= $field_Type;
		$this->wpTypesFields 	= $wpTypesFields;
		$this->postType 		= $postType;

	}

	function import($postId) {
		//get fields and groups of homegroup post   
		if ($this->postType == 'Users') {
			$postTypeValue = 'wp-types-user-group';
		}else{            	
			$postTypeValue = 'wp-types-group';
		}

		$fieldsAndGroups = $this->format($this->dataArray['Parent_Group'],$postTypeValue);

		if ($fieldsAndGroups == 0) {
			$this->insertCustomFields($postId,$this->postType);

		}else{
			$this->loopAndInsertField($postId,$fieldsAndGroups,0,true);
		}
		return;                                

	}

	function loopAndInsertField($postId,$fieldsAndGroups,$index,$isParent) {
		
		foreach ($fieldsAndGroups as $fieldkey => $fieldvalue) {
			foreach ($fieldvalue as $key => $value) {
				$data=$this->explodeFunction('_',$value);
				if ($data[1]=='repeatable' && $data[2]=='group') {
					//send repeatable group id.
					$groupName=$this->getRepeatableName($data[3]);
					$groupRelationId=$this->getRelationshipId($groupName);
					$groups=$this->dataArray[$groupName];               
					// csv directs to create 2 repetable group
					$csvGroupsArray =$this->explodeFunction('|',$groups);
					$elementString=$this->getRepeatableMetaValue($data[3]);
					//from user created toolset group , don't change the symbol
					$elementArray =$this->explodeFunction(',',$elementString);
					if ($index < count($csvGroupsArray)) {
						for ($i=0; $i < count($csvGroupsArray) ; $i++) { 
							//create post , post_type =  $groupName->ram-house $csvGroupsArray[i]
							$childPostId=$this->insertPost($groupName,$csvGroupsArray[$i]);
							$this->insertRelationship($groupRelationId,$postId,$childPostId);
							$this->loopAndInsertField($childPostId, array($elementArray), $i,false);

						}
					}
				} else {  // simply make entry for homefroup fields. 


					$fieldValues=$this->dataArray[$value];              
					$fieldsArray =explode('|',$fieldValues);
					$metaKey=$this->metaData[$value];
					$fieldType=$this->fieldType[$value]; 

					if (!$isParent) {
						if ($index < count($fieldsArray)) {

							$fieldValue=$fieldsArray[$index];
							$this->checkFieldType($postId,$fieldValue,$fieldType,$metaKey);
						}
					} else {
						$this->checkFieldType($postId,$fieldValues,$fieldType,$metaKey);
					}
				}
			}
		}

	}

	function insertCustomFields($postId,$postType){
		
		foreach ($this->dataArray as $key => $value) {
			if(isset($this->fieldType[$key]) && isset($this->metaData[$key])){
				$fieldType=$this->fieldType[$key];
				$metaKey=$this->metaData[$key];
			}

			$listTaxonomy = get_taxonomies();
			if(!empty($fieldType) && !empty($metaKey)) {
				if ( in_array( $postType, $listTaxonomy ) ) {
					$values = explode( '|', $value );
					foreach ( $values as $keys => $value1 ) {
						$this->insertTermFields( $postId, $value1, $fieldType, $metaKey );
					}
				} else {
					$this->checkFieldType( $postId, $value, $fieldType, $metaKey );
				}
			}
		} 
	}
	function insertTermFields($postId,$fieldValue,$field_Types,$metaKey)
	{
		
		if($field_Types=='checkboxes'){
			
			$fieldTypeArray=array_flip($this->metaData);
			$fieldTypeValue=$fieldTypeArray[$metaKey];

			$term_fields = get_option('wpcf-termmeta');
			$wpTypes = $term_fields[$fieldTypeValue]['data']['options'];
			//$wpTypes=$this->wpTypesFields[$fieldTypeValue]['data']['options'];
			
			$checkbox_array = array();
			$fieldValueArrays =$this->explodeFunction(',',$fieldValue);
			
			foreach ($fieldValueArrays as $keys => $values) {
				$values = trim($values);
				foreach ($wpTypes as $key1 => $value1) {
					if ($values == $value1['title']) {
						$term_value = $value1['set_value'];
						$checkbox_array[$key1] = array($term_value);	
					}
				}
			}	
			add_term_meta($postId,$metaKey,$checkbox_array);	
		}
		else
		{
			$this->InsertUpdateTerm($postId,$metaKey,$field_Types,$fieldValue);
		}
	}


	function checkFieldType($postId,$fieldValue,$fields_Type,$metaKey){
		global $wpdb;
		
		if ($fields_Type == 'checkboxes') {
			$fieldValueArray =$this->explodeFunction(',',$fieldValue); 
			$fieldValueArray=array_flip($fieldValueArray);

			$fieldTypeArray=array_flip($this->metaData);
			$fieldTypeValue=$fieldTypeArray[$metaKey];

			$wpTypes=$this->wpTypesFields[$fieldTypeValue]['data']['options'];
			$checkbox_array = array();
			
			foreach ($fieldValueArray as $key => $value) {
				$key = trim($key);
				foreach ($wpTypes as $key1 => $value1) {
					if ($key == $value1['title']) {
						$checkbox_array[$key1] = array(1);
					}
				}
			}
			
			//update_post_meta($postId, $metaKey, $checkbox_array);
			if ($this->postType == 'Users') {
				update_user_meta($postId,$metaKey,$checkbox_array);
			}else{
				update_post_meta($postId,$metaKey,$checkbox_array);
			}
		}elseif ($fields_Type == 'post') {
			
			$fieldValueArray=array_flip($this->metaData);
			$relationshipSlug=$fieldValueArray[$metaKey];
			$groupRelationId=$this->getRelationshipId($relationshipSlug);
			if (is_numeric($fieldValue)) {
				$field_Value=$fieldValue;
			}elseif(is_string($fieldValue)){
				$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$fieldValue}' AND post_status='publish'";
				$name = $wpdb->get_results($query);
				if (!empty($name)) {
					$field_Value=$name[0]->id;
				}else{
					//print_r("Post or Page Name not found");
					return;

				}
			}
			if(!empty($field_Value) && !empty($metaKey)) {
				$this->insertRelationship( $groupRelationId, $field_Value, $postId );
				update_post_meta( $postId, $metaKey, $field_Value );
			}

		}else{
			//add_post_meta($postId,$metaKey,$fieldValue);add_user_meta
			$this->InsertUpdateData($postId,$metaKey,$fields_Type,$fieldValue);
		}

	}
	function InsertUpdateTerm($postId,$metaKey,$fields_Types,$fieldValue)
	{

		$fieldTypeArray=array_flip($this->metaData);
		$fieldTypeValue=$fieldTypeArray[$metaKey];
		settype($isRepetitive, "integer");
		$isRepetitive=$this->wpTypesFields[$fieldTypeValue]['data']['repetitive'];

		if (!empty($isRepetitive) && $isRepetitive == 1) {
			$valuesArray =$this->explodeFunction('|',$fieldValue);
			foreach ($valuesArray as $values) {
				$values=trim($values);

				if ($fields_Types == 'date') {
					$values = strtotime($values);
				}elseif ($fields_Types == 'skype') {
					$values = array(
							'skypename' => $values,
							'action' => 'chat',
							'color' => 'blue',
							'size' => '32'
						       );
				}
				add_term_meta($postId,$metaKey,$values);
			}
		}else{
			if($isRepetitive == 0){
				if ($fields_Types == 'date') {
					$fieldValue = strtotime($fieldValue);
				}elseif ($fields_Types == 'skype') {

					$fieldValue = array(
							'skypename' => $fieldValue,
							'action' => 'chat',
							'color' => 'blue',
							'size' => '32'
							);
				}
				add_term_meta($postId,$metaKey,$fieldValue);
			}
		}
	}

	function InsertUpdateData($postId,$metaKey,$fields_Types,$fieldValue)
	{
		global $wpdb;
		settype($isRepetitive, "integer");
		$fieldTypeArray=array_flip($this->metaData);
		$fieldTypeValue=$fieldTypeArray[$metaKey];
		if(isset($this->wpTypesFields[$fieldTypeValue]['data']['repetitive'])){
			$isRepetitive=$this->wpTypesFields[$fieldTypeValue]['data']['repetitive'];
		}
		if (!empty($isRepetitive) && $isRepetitive == 1 && $fields_Types!='image' && $fields_Types != 'file') {

			$valuesArray =$this->explodeFunction('|',$fieldValue);
			foreach ($valuesArray as $values) {
				$values=trim($values);
				if ($fields_Types == 'date') {
					$values = strtotime($values);
				}elseif ($fields_Types == 'skype') {
					$values = array(
							'skypename' => $values,
							'action' => 'chat',
							'color' => 'blue',
							'size' => '32'
						       );
				}
				if ($this->postType == 'Users') {
					add_user_meta($postId,$metaKey,$values);
				}else{
					add_post_meta($postId,$metaKey,$values);
				}
			}
		}
		if($fields_Types == 'image' || $fields_Types == 'file') {
			$valuesArray =$this->explodeFunction('|',$fieldValue);
			
			foreach($valuesArray as $value) {
				if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i',$value, $matchedlist, PREG_PATTERN_ORDER ) ) {
					$attachid = self::$mediaInstance->media_handling($value, $postId);	
					if(empty($attachid)){
						if ($this->postType == 'Users') {
							add_user_meta($postId,$metaKey,$value);
						}else{
							add_post_meta($postId,$metaKey,$value);
						}	
					}
					else{
						$guid=$wpdb->get_row("select guid from ".$wpdb->prefix."posts where ID='$attachid'");
					$values=$guid->guid;
					if ($this->postType == 'Users') {
						add_user_meta($postId,$metaKey,$value);
					}else{
						add_post_meta($postId,$metaKey,$values);
					}
					}
					$guid=$wpdb->get_row("select guid from ".$wpdb->prefix."posts where ID='$attachid'");
					$values=$guid->guid;
					if ($this->postType == 'Users') {
						add_user_meta($postId,$metaKey,$value);
					}else{
						add_post_meta($postId,$metaKey,$values);
					}
				}
			} 
		}
		else{
			if($isRepetitive == 0){
				if ($fields_Types == 'date') {
					$fieldValue = strtotime($fieldValue);
				}elseif ($fields_Types == 'skype') {

					$fieldValue = array(
							'skypename' => $fieldValue,
							'action' => 'chat',
							'color' => 'blue',
							'size' => '32'
							);
				}

				if ($this->postType == 'Users') {
					add_user_meta($postId,$metaKey,$fieldValue);
				}else{
					add_post_meta($postId,$metaKey,$fieldValue);
				}
			}
		}
	}
}
