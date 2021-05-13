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

class MappingExtension{
	private static $instance = null;
	private static $extension = [];
	private static $validatefile = [];

	private function __construct(){
		add_action('wp_ajax_mappingfields',array($this,'mapping_field_function'));
		add_action('wp_ajax_getfields',array($this,'get_fields'));
		add_action('wp_ajax_get_export_fields',array($this,'get_export_fields'));
		add_action('wp_ajax_templateinfo',array($this,'get_template_info'));
		add_action('wp_ajax_search_template',array($this,'search_template'));
	}

	public static function getInstance() {
		if (MappingExtension::$instance == null) {
			MappingExtension::$instance = new MappingExtension;
			MappingExtension::$validatefile = new ValidateFile;
			foreach(get_declared_classes() as $class){
				if(is_subclass_of($class, 'Smackcoders\WCSV\ExtensionHandler')){ 
					array_push(MappingExtension::$extension ,$class::getInstance() );	
				}
			}
			return MappingExtension::$instance;
		}
		return MappingExtension::$instance;
	}


	/**
	* Ajax Call 
	* Provides all Widget Fields for Mapping Section
	* @return array - mapping fields
	*/
	public function mapping_field_function(){
		$import_type = $_POST['Types'];
		$hash_key = $_POST['HashKey'];
		$mode = $_POST['Mode'];
		global $wpdb;

		$response = [];
		$details = [];
		$info = [];

		$table_name = $wpdb->prefix."smackcsv_file_events";
		$wpdb->get_results("UPDATE $table_name SET mode ='$mode' WHERE hash_key = '$hash_key'");

		$get_result = $wpdb->get_results("SELECT file_name FROM $table_name WHERE hash_key = '$hash_key' ");
		$filename = $get_result[0]->file_name;
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
		$template_table_name = $wpdb->prefix."ultimate_csv_importer_mappingtemplate";
		$get_result = $wpdb->get_results("SELECT distinct(templatename) FROM $template_table_name WHERE csvname = '$filename' and module = '$import_type' and templatename != '' ");
		
		/* Provides Template Details, if Templates are stored*/
		if(!empty($get_result)) {
			foreach($get_result as $value){

				$template_name = $value->templatename;
				$get_temp_result = $wpdb->get_results("SELECT createdtime , module , mapping FROM $template_table_name WHERE templatename = '{$template_name}' ");
				$mapping = $get_temp_result[0]->mapping;
				$mapped_elements = unserialize($mapping);
				$matched_count = $this->get_matched_count($mapped_elements);	
				$created_time = $get_temp_result[0]->createdtime;
				$module = $get_temp_result[0]->module;
				$details['template_name'] = $template_name;
				$details['created_time'] = $created_time;
				$details['module'] = $module;
				$details['count'] = $matched_count;
				array_push($info , $details);

			}
			$response['success'] = true;
			$response['show_template'] = true;
			$response['info'] = $info;
			echo wp_json_encode($response);
			wp_die();
		}

		/* Provides widget fields, if templates are not stored */
		else{
		
			$smackcsv_instance = SmackCSV::getInstance();
			$upload_dir = $smackcsv_instance->create_upload_dir();
			$response = [];

			if($file_extension == 'csv' || $file_extension == 'txt'){
				ini_set("auto_detect_line_endings", true);
				$info = [];
				if (($h = fopen($upload_dir.$hash_key.'/'.$hash_key, "r")) !== FALSE) 
				{
				// Convert each line into the local $data variable
				$delimiters = array( ',','\t',';','|',':','&nbsp');
				$file_path = $upload_dir . $hash_key . '/' . $hash_key;
				$delimiter = MappingExtension::$validatefile->getFileDelimiter($file_path, 5);
				$array_index = array_search($delimiter,$delimiters);
				if($array_index == 5){
					$delimiters[$array_index] = ' ';
				}
				while (($data = fgetcsv($h, 0, $delimiters[$array_index])) !== FALSE) 
				{	
					// Read the data from a single line
					$trimmed_array = array_map('trim', $data);
					array_push($info , $trimmed_array);	
					$exp_line = $info[0];
					$response['success'] = true;
					$response['show_template'] = false;
					$response['csv_fields'] = $exp_line;
					$value = $this->mapping_fields($import_type);
					$response['fields'] = $value;
					echo wp_json_encode($response);
					wp_die();  			
				}	
				// Close the file
				fclose($h);
				}
			}if($file_extension == 'xml'){
				$xml_class = new XmlHandler();
				$upload_dir_path = $upload_dir. $hash_key;
				if (!is_dir($upload_dir_path)) {
					wp_mkdir_p( $upload_dir_path);
				}
				chmod($upload_dir_path, 0777);   
				$path = $upload_dir . $hash_key . '/' . $hash_key;   
				$xml = simplexml_load_file($path);
				$xml_arr = json_decode( json_encode($xml) , 1);
			
				foreach($xml->children() as $child){   
					$child_name = $child->getName();    
				}
				$parse_xml = $xml_class->parse_xmls($hash_key);
				$i = 0;
				foreach($parse_xml as $xml_key => $xml_value){
					if(is_array($xml_value)){
						foreach ($xml_value as $e_key => $e_value){
							$headers[$i] = $e_value['name'];
							$i++;
						}
					}
				}
				$response['success'] = true;
				$response['show_template'] = false;
				$response['csv_fields'] = $headers;
				$value = $this->mapping_fields($import_type);
				$response['fields'] = $value;
				echo wp_json_encode($response);
				wp_die();  			
			}

		}
	}


	public function mapping_fields($import_type){
		$support_instance = [];
		$value = [];
		for($i = 0 ; $i < count(MappingExtension::$extension) ; $i++){
			$extension_instance = MappingExtension::$extension[$i];
			if($extension_instance->extensionSupportedImportType($import_type)){
				array_push($support_instance , $extension_instance);		
			}	
		}		
		for($i = 0 ;$i < count($support_instance) ; $i++){	
			$supporting_instance = $support_instance[$i];
			$fields = $supporting_instance->processExtension($import_type);
			array_push($value , $fields);			
		}
		return $value;
	}

	/**
	* Ajax Call 
	* Provides all Widget Fields for Export Section
	* @return array - mapping fields
	*/
	public function get_export_fields(){
		$import_type = $_POST['Types'];
		$response = [];
		$value = $this->mapping_fields($import_type);
		$response['success'] = true;
		$response['fields'] = $value;
		echo wp_json_encode($response);
		wp_die();  
	}

	/**
	* Provides all Widget Fields for Export Section
	* @return array - mapping fields
	*/
	public function get_fields($module){ 
		$import_type = $module;
		$response = [];
		$value = $this->mapping_fields($import_type);
		$response['fields'] = $value;
		return $response;
	}


	/**
	* Ajax Call 
	* Provides mapped fields from Template
	* @return array - already mapped fields
	*/
	public function get_template_info(){
		global $wpdb;
		$template_name = $_POST['TemplateName'];
		$import_type = $_POST['Types'];
		$hash_key = $_POST['HashKey'];
		$response = [];
		$template_table_name = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";
		$table_name = $wpdb->prefix."smackcsv_file_events";
		$response['success'] = true;

		if(!empty($template_name)){	
			$get_detail   = $wpdb->get_results( "SELECT mapping , csvname , mapping_type FROM $template_table_name WHERE templatename = '$template_name' " );	
			$get_mapping = $get_detail[0]->mapping;
			$mapping_type = $get_detail[0]->mapping_type;
			$result = unserialize($get_mapping);
			$response['already_mapped'] = $result;
			$response['mapping_type'] = $mapping_type;
		}
		if(empty($hash_key)){	
			$get_detail   = $wpdb->get_results( "SELECT eventKey FROM $template_table_name WHERE templatename = '$template_name' " );
			$hash_key = $get_detail[0]->eventKey;
		}
		$get_result = $wpdb->get_results("SELECT file_name FROM $table_name WHERE hash_key = '$hash_key' ");
		$filename = $get_result[0]->file_name;
		
		if(empty($filename)){
			$get_result = $wpdb->get_results("SELECT csvname FROM $template_table_name WHERE eventKey = '$hash_key' ");
			$filename = $get_result[0]->csvname;	
		}
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
		
		$smackcsv_instance = SmackCSV::getInstance();
		$upload_dir = $smackcsv_instance->create_upload_dir();

		if($file_extension == 'csv' || $file_extension == 'txt'){
			ini_set("auto_detect_line_endings", true);
			$info = [];
			if (($h = fopen($upload_dir.$hash_key.'/'.$hash_key, "r")) !== FALSE) 
			{
			// Convert each line into the local $data variable
			$delimiters = array( ',','\t',';','|',':','&nbsp');
			$file_path = $upload_dir . $hash_key . '/' . $hash_key;
			$delimiter = MappingExtension::$validatefile->getFileDelimiter($file_path, 5);
			$array_index = array_search($delimiter,$delimiters);
			if($array_index == 5){
				$delimiters[$array_index] = ' ';
			}
			while (($data = fgetcsv($h, 0, $delimiters[$array_index])) !== FALSE) 
				{		
					// Read the data from a single line
					$trimmed_array = array_map('trim', $data);
					array_push($info , $trimmed_array);
					
					$exp_line = $info[0];
					$response['csv_fields'] = $exp_line;
					$value = $this->mapping_fields($import_type);	
					$response['fields'] = $value;
					echo wp_json_encode($response);
					wp_die();  			
				}	
				// Close the file
				fclose($h);
			}
		}

		if($file_extension == 'xml'){
			$xml_class = new XmlHandler();
			
			$upload_dir_path = $upload_dir. $hash_key;
			if (!is_dir($upload_dir_path)) {
				wp_mkdir_p( $upload_dir_path);
			}
			chmod($upload_dir_path, 0777);   
			$path = $upload_dir . $hash_key . '/' . $hash_key; 
			$xml = simplexml_load_file($path);
			$xml_arr = json_decode( json_encode($xml) , 1);	
			foreach($xml->children() as $child){   
				$child_name = $child->getName();    
			}
			$parse_xml = $xml_class->parse_xmls($hash_key);
			$i = 0;
			foreach($parse_xml as $xml_key => $xml_value){
				if(is_array($xml_value)){
					foreach ($xml_value as $e_key => $e_value){
						$headers[$i] = $e_value['name'];
						$i++;
					}
				}
			}
			$response['show_template'] = false;
			$response['csv_fields'] = $headers;
			$value = $this->mapping_fields($import_type);

			$response['fields'] = $value;
			echo wp_json_encode($response);
			wp_die();  			
		}

	}

	/**
	* Provides mapped fields count from template
	* @param array $mappingList
	* @return int - count
	*/
	public function get_matched_count($mappingList){
		$count = 0;
		foreach ($mappingList as $templatename => $group) {	
			$count += count(array_filter($group));
		}
		return $count;	
	}
	

	/**
	* Ajax Call 
	* Searches Templates based on Template Name and Dates
	* @return array - Template Details
	*/
	public function search_template(){
		global $wpdb;
		$template_name = $_POST['TemplateName'];
		$start_date = $_POST['FromDate'];
		$end_date = $_POST['ToDate'];
		$filename = $_POST['filename'];
		$module = $_POST['module'];
		$info = [];
		$details = [];
		$startDate = $start_date . ' 00:00:00';
		$endDate = $end_date . ' 23:59:59';
		$filterclause = '';
		if ( $start_date != 'Invalid date' && $end_date != 'Invalid date'){
			$filterclause .= "createdtime between '$startDate' and '$endDate' and";
			$filterclause = substr($filterclause, 0, -3);
		} else {
			if ( $start_date != 'Invalid date'){
				$filterclause .= "createdtime >= '$startDate' and";
				$filterclause = substr($filterclause, 0, -3);
			} else {
				if ( $end_date != 'Invalid date'){
					$filterclause .= "createdtime <= '$endDate' and";
					$filterclause = substr($filterclause, 0, -3);
				}
			}
		}
		
		if (!empty($template_name) && $start_date != 'Invalid date' && $end_date != 'Invalid date'){
			$filterclause .= " and templatename = '$template_name'";
		}
		if (!empty($template_name) && $start_date == 'Invalid date' && $end_date == 'Invalid date'){
			$filterclause .= " templatename = '$template_name'";
		}
		if (!empty($filterclause)) {
			$filterclause = "where $filterclause";
		}
		
		$templateList = $wpdb->get_results("select * from {$wpdb->prefix}ultimate_csv_importer_mappingtemplate ".$filterclause." and csvname = '".$filename ."' ");
		
		if(!empty($templateList)){
			foreach($templateList as $value){
				$templateName = $value->templatename;
		
				if(!empty($templateName)){					
					$details['template_name'] = $templateName;
					$details['module'] = $value->module;
					$details['created_time'] = $value->createdtime;
					$mapping = $value->mapping;
					$map = unserialize($mapping);
					$count = $this->get_matched_count($map);
					$details['count'] = $count;	
					array_push($info , $details);
				}	
			}
			$response['success'] = true;
			$response['info'] = $info;
		}else{
			$response['success'] = false;
			$response['message'] = "Templates not found";
		}
		echo wp_json_encode($response);
		wp_die(); 	
	}

}		