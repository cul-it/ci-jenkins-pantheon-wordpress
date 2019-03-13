<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * 
 */
require_once "ToolsetImporter.php";

class WPToolsetImporter extends ToolsetImporter
{
	private static $wpToolsetImporter = NULL;
	private $dataArray;
	private $metadata;
	private $field_type;

	static function getInstance() {
		if (self::$wpToolsetImporter == NULL) {
			self::$wpToolsetImporter = new WPToolsetImporter();
		}
		return self::$wpToolsetImporter;
	}

	function set($dataArray, $metaData, $fieldType,$wpTypesFields,$postType) {

		$this->dataArray 		= $dataArray;
		$this->metaData 		= $metaData;
		$this->fieldType 		= $fieldType;
		$this->wpTypesFields 	= $wpTypesFields;
		$this->postType 		= $postType;
	}

	function import($postId) {
		//get fields and groups of homegroup post   
		if ($this->postType == 'users') {
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

		foreach ($fieldsAndGroups as $key => $value) {
			$data=$this->explodeFunction('_',$value);
			if ($data[1]=='repeatable' && $data[2]=='group') {
				//send repeatable group id.
				$groupName=$this->getRepeatableName($data[3]);
				$groupRelationId=$this->getRelationshipId($groupName);
				//print_r($groupRelationId); die('groupRelationId');
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
						$this->loopAndInsertField($childPostId, $elementArray, $i,false);

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

	function insertCustomFields($postId,$postType){
		foreach ($this->dataArray as $key => $value) {
			$fieldType=$this->fieldType[$key];
			$metaKey=$this->metaData[$key];
			$listTaxonomy = get_taxonomies();
			if (in_array($postType, $listTaxonomy)) {
				$values=explode('|', $value);
				foreach ($values as $key => $value1) {
					$this->insertTermFields($postId,$value1,$fieldType,$metaKey);	
				}					
			}
			else{
				$this->checkFieldType($postId,$value,$fieldType,$metaKey);
			}
		} 
	}
	function insertTermFields($postId,$fieldValue,$fieldType,$metaKey)
	{
		if($fieldType=='checkboxes'){
			$fieldTypeArray=array_flip($this->metaData);
			$fieldTypeValue=$fieldTypeArray[$metaKey];
			$wpTypes=$this->wpTypesFields[$fieldTypeValue]['data']['options'];
			$checkbox_array = array();
			$fieldValueArrays =$this->explodeFunction(',',$fieldValue);

			foreach ($fieldValueArrays as $keys => $values) {
				foreach ($wpTypes as $key1 => $value1) {
					if ($values == $value1['title']) {
						$checkbox_array[$key1] = array(1);
					}
				}
			}
			add_term_meta($postId,$metaKey,$checkbox_array);
		}
		else
		{
			$this->InsertUpdateTerm($postId,$metaKey,$fieldType,$fieldValue);
		}
	}


	function checkFieldType($postId,$fieldValue,$fieldType,$metaKey){
		global $wpdb;

		if ($fieldType == 'checkboxes') {			

			$fieldValueArray =$this->explodeFunction(',',$fieldValue); 
			$fieldValueArray=array_flip($fieldValueArray);

			$fieldTypeArray=array_flip($this->metaData);
			$fieldTypeValue=$fieldTypeArray[$metaKey];

			$wpTypes=$this->wpTypesFields[$fieldTypeValue]['data']['options'];
			$checkbox_array = array();

			foreach ($fieldValueArray as $key => $value) {
				foreach ($wpTypes as $key1 => $value1) {
					if ($key == $value1['title']) {
						$checkbox_array[$key1] = array(1);
					}
				}
			}
			//update_post_meta($postId, $metaKey, $checkbox_array);
			if ($this->postType == 'users') {
				update_user_meta($postId,$metaKey,$checkbox_array);
			}else{
				update_post_meta($postId,$metaKey,$checkbox_array);
			}
		}elseif ($fieldType == 'post') {

			$fieldValueArray=array_flip($this->metaData);
			$relationshipSlug=$fieldValueArray[$metaKey];
			$groupRelationId=$this->getRelationshipId($relationshipSlug);
			if (is_numeric($fieldValue)) {
				$fieldValue=$fieldValue;
			}elseif(is_string($fieldValue)){
				$query = "SELECT id FROM {$wpdb->prefix}posts WHERE post_title ='{$fieldValue}' AND post_status='publish'";
				$name = $wpdb->get_results($query);
				if (!empty($name)) {
					$fieldValue=$name[0]->id;
				}else{
					print_r("Post or Page Name not found");
					return;

				}
			}
			$this->insertRelationship($groupRelationId,$fieldValue,$postId);
			update_post_meta($postId,$metaKey,$fieldValue);

		}else{
			//add_post_meta($postId,$metaKey,$fieldValue);add_user_meta
			$this->InsertUpdateData($postId,$metaKey,$fieldType,$fieldValue);
		}

	}
	function InsertUpdateTerm($postId,$metaKey,$fieldType,$fieldValue)
	{

		$fieldTypeArray=array_flip($this->metaData);
		$fieldTypeValue=$fieldTypeArray[$metaKey];
		$isRepetitive=$this->wpTypesFields[$fieldTypeValue]['data']['repetitive'];

		if (!empty($isRepetitive) && $isRepetitive == 1) {
			$valuesArray =$this->explodeFunction('|',$fieldValue);
			foreach ($valuesArray as $values) {
				$values=trim($values);

				if ($fieldType == 'date') {
					$values = strtotime($values);
				}elseif ($fieldType == 'skype') {
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

			if ($fieldType == 'date') {
				$fieldValue = strtotime($fieldValue);
			}elseif ($fieldType == 'skype') {

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

	function InsertUpdateData($postId,$metaKey,$fieldType,$fieldValue)
	{

		$fieldTypeArray=array_flip($this->metaData);
		$fieldTypeValue=$fieldTypeArray[$metaKey];
		$isRepetitive=$this->wpTypesFields[$fieldTypeValue]['data']['repetitive'];

		if (!empty($isRepetitive) && $isRepetitive == 1) {
			$valuesArray =$this->explodeFunction('|',$fieldValue);
			foreach ($valuesArray as $values) {
				$values=trim($values);

				if ($fieldType == 'date') {
					$values = strtotime($values);
				}elseif ($fieldType == 'skype') {
					$values = array(
							'skypename' => $values,
							'action' => 'chat',
							'color' => 'blue',
							'size' => '32'
						       );
				}

				if ($this->postType == 'users') {
					add_user_meta($postId,$metaKey,$values);
				}else{
					add_post_meta($postId,$metaKey,$values);
				}
			}
		}else{

			if ($fieldType == 'date') {
				$fieldValue = strtotime($fieldValue);
			}elseif ($fieldType == 'skype') {

				$fieldValue = array(
						'skypename' => $fieldValue,
						'action' => 'chat',
						'color' => 'blue',
						'size' => '32'
						);
			}

			if ($this->postType == 'users') {
				add_user_meta($postId,$metaKey,$fieldValue);
			}else{
				add_post_meta($postId,$metaKey,$fieldValue);
			}
		}
	}
}

