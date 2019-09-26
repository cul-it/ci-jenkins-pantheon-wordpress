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

$import_extensions = glob( __DIR__ . '/importExtensions/*.php');

	foreach ($import_extensions as $import_extension_value) {
		require_once($import_extension_value);
	}

class SaveMapping{
	private static $instance=null;
	private static $smackcsv_instance = null;
	private static $core = null,$nextgen_instance;
	
	private function __construct(){
		add_action('wp_ajax_saveMappedFields',array($this,'check_templatename_exists'));
		add_action('wp_ajax_StartImport' , array($this,'background_starts_function'));
		add_action('wp_ajax_GetProgress',array($this,'import_detail_function'));
		add_action('wp_ajax_ImportState',array($this,'import_state_function'));
		add_action('wp_ajax_ImportStop',array($this,'import_stop_function'));
		add_action('wp_ajax_checkmain_mode',array($this,'checkmain_mode'));
		add_action('wp_ajax_disable_main_mode',array($this,'disable_main_mode'));
	}

	public static function getInstance() {
		
		if (SaveMapping::$instance == null) {
			SaveMapping::$instance = new SaveMapping;
			SaveMapping::$smackcsv_instance = SmackCSV::getInstance();
			SaveMapping::$nextgen_instance = new NextGenGalleryImport;
			return SaveMapping::$instance;
		}
		return SaveMapping::$instance;
	}


	public static function disable_main_mode(){
			$disable_option = $_POST['option'];
			$disable_value = $_POST['value'];
			delete_option($disable_option);
			$result['success'] = true;
			echo wp_json_encode($result);
			wp_die();
	}

	public static function checkmain_mode(){
		$ucisettings = get_option('sm_uci_pro_settings');
		if(isset($ucisettings['enable_main_mode']) && $ucisettings['enable_main_mode'] == 'true') {
			$result['success'] = true;
		}
		else{
			$result['success'] = false;
		}
		echo wp_json_encode($result);
		wp_die();
	}
	/**
	* Checks whether Template name already exists.
	*/
	public function check_templatename_exists(){
		$use_template = $_POST['UseTemplateState'];
		$template_name = $_POST['TemplateName'];	
		$response = [];

		if($use_template === 'true'){	
			$response['success'] = $this->save_temp_fields();

		}else{
			global $wpdb;
			$template_table_name = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";
			$get_template_names = $wpdb->get_results( "SELECT templatename FROM $template_table_name" );	
			if(!empty($get_template_names)){

				foreach($get_template_names as $temp_names){
					$inserted_temp_names[] = $temp_names->templatename;
				}
				if(in_array($template_name , $inserted_temp_names) && $template_name != ''){
					$response['success'] = false;
					$response['message'] = 'Template Name Already Exists';
				}else{
					$response['success'] = $this->save_fields_function();
				}
			}else{	
				$response['success'] = $this->save_fields_function();
			}
		}
		echo wp_json_encode($response); 	
		wp_die();
	}


	/**
	* Save the mapped fields on using template
	* @return boolean
	*/
	public function save_temp_fields(){

		$type          = $_POST['Types'];
		$map_fields    = $_POST['MappedFields'];	
		$template_name = $_POST['TemplateName'];
		$new_template_name = $_POST['NewTemplate'];
		$mapping_type = $_POST['MappingType'];
		$hash_key = $_POST['HashKey'];

		global $wpdb;
		$template_table_name = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";

		$get_detail   = $wpdb->get_results( "SELECT id FROM $template_table_name WHERE templatename = '$template_name' " );
		$get_id = $get_detail[0]->id;

		$mapped_fields = str_replace( "\\", "", $map_fields );
		$mapped_fields = json_decode( $mapped_fields, true );

		$mapping_fields = serialize( $mapped_fields );
		$time = date('Y-m-d h:i:s');

		if(!empty($new_template_name)){
			$fields = $wpdb->get_results("UPDATE $template_table_name SET templatename = '$new_template_name' , mapping ='$mapping_fields' , createdtime = '$time' , module = '$type' , eventKey = '$hash_key' , mapping_type = '$mapping_type' WHERE id = $get_id ");	
		}else{	
			$fields = $wpdb->get_results("UPDATE $template_table_name SET eventKey = '$hash_key' , mapping_type = '$mapping_type' WHERE id = $get_id ");	
		}
		return true;

	}

	/**
	* Save the mapped fields on using new mapping
	* @return boolean
	*/
	public function save_fields_function() {

		$hash_key      = $_POST['HashKey'];
		$type          = $_POST['Types'];
		$map_fields    = $_POST['MappedFields'];	
		$template_name = $_POST['TemplateName'];
		$mapping_type = $_POST['MappingType'];
		global $wpdb;

		$template_table_name = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";
		$file_table_name = $wpdb->prefix . "smackcsv_file_events";

		$mapped_fields = str_replace( "\\", "", $map_fields );
		$mapped_fields = json_decode( $mapped_fields, true );
	
		$mapping_fields = serialize( $mapped_fields );
		$time = date('Y-m-d H:i:s');

		$get_detail   = $wpdb->get_results( "SELECT file_name FROM $file_table_name WHERE `hash_key` = '$hash_key'" );
		$get_file_name = $get_detail[0]->file_name;

		//$fields = $wpdb->get_results(" SET FOREIGN_KEY_CHECKS=0 ");  
		//$fields = $wpdb->get_results( "INSERT INTO $template_table_name(templatename ,mapping ,createdtime ,module,csvname ,eventKey)values('$template_name','$mapping_fields' , '$time' , '$type' , '$get_file_name', '$hash_key' )" );
		//$fields = $wpdb->get_results(" SET FOREIGN_KEY_CHECKS=1 ");

		$get_hash = $wpdb->get_results( "SELECT eventKey FROM $template_table_name" );

		if(!empty($get_hash)){
			foreach($get_hash as $hash_values){
				$inserted_hash_values[] = $hash_values->eventKey;
			}
			if(in_array($hash_key , $inserted_hash_values)){
				$fields = $wpdb->get_results("UPDATE $template_table_name SET templatename = '$template_name' , mapping ='$mapping_fields' , createdtime = '$time' , module = '$type' , mapping_type = '$mapping_type' WHERE eventKey = '$hash_key'");	
			}
			else{
				$fields = $wpdb->get_results( "INSERT INTO $template_table_name(templatename ,mapping ,createdtime ,module,csvname ,eventKey , mapping_type)values('$template_name','$mapping_fields' , '$time' , '$type' , '$get_file_name', '$hash_key', '$mapping_type')" );	
			}
		}else{
			$fields = $wpdb->get_results( "INSERT INTO $template_table_name(templatename ,mapping ,createdtime ,module,csvname ,eventKey , mapping_type)values('$template_name','$mapping_fields' , '$time' , '$type' , '$get_file_name', '$hash_key' , '$mapping_type' )" );
		}
		return true;
	}


	/**
	* Provides import record details
	*/
	public function import_detail_function(){
		
		$hash_key = $_POST['HashKey'];
		$response = [];	
		global $wpdb;

		$log_table_name = $wpdb->prefix . "import_detail_log";
		$file_table_name = $wpdb->prefix ."smackcsv_file_events";

		$file_records = $wpdb->get_results("SELECT mode FROM $file_table_name WHERE hash_key = '$hash_key' ",ARRAY_A);
		$mode = $file_records[0]['mode'];

		if($mode == 'Insert'){
			$method = 'Import';
		}
		if($mode == 'Update'){
			$method = 'Update';
		}

		$total_records = $wpdb->get_results("SELECT file_name , total_records , processing_records ,status ,remaining_records , filesize FROM $log_table_name WHERE hash_key = '$hash_key' ",ARRAY_A);
		
		$response['success'] = true;
		$response['file_name']= $total_records[0]['file_name'];
		$response['total_records']= $total_records[0]['total_records'];
		$response['processing_records']= $total_records[0]['processing_records'];
		$response['remaining_records']= $total_records[0]['remaining_records'];
		$response['status']= $total_records[0]['status'];
		$response['filesize'] = $total_records[0]['filesize'];
		$response['method'] = $method;

		if($total_records[0]['status'] == 'Completed'){
			$response['progress'] = false;
		}else{
			$response['progress'] = true;
		}
		$response['Info'] = [];
		
		echo wp_json_encode($response); 
		wp_die();
	}	

	/**
	* Checks whether the import function is paused or resumed
	*/
	public function import_state_function(){
		$response = [];
		$hash_key = $_POST['HashKey'];
		
		$upload = wp_upload_dir();
        $upload_base_url = $upload['baseurl'];
		$upload_url = $upload_base_url . '/smack_uci_uploads/imports/';
		$upload_dir = SaveMapping::$smackcsv_instance->create_upload_dir();
		
		$log_path = $upload_dir.$hash_key.'/'.$hash_key.'.html';
		if(file_exists($log_path)){
			$log_link_path = $upload_url. $hash_key .'/'.$hash_key.'.html';
		}

		$import_txt_path = $upload_dir.'import_state.txt';
		chmod($import_txt_path , 0777);
		$import_state_arr = array();

		/* Gets string 'true' when Resume button is clicked  */
		if($_POST['State'] == 'true'){

			//first check then set on
			$open_file = fopen( $import_txt_path , "w");
            $import_state_arr = array('import_state' => 'on','import_stop' => 'on');
        	$state_arr = serialize($import_state_arr);
            fwrite($open_file , $state_arr);
            fclose($open_file);

			$response['import_state'] = false;		
		}
		/* Gets string 'false' when Pause button is clicked  */
		if($_POST['State'] == 'false'){
			//first check then set off	
			$open_file = fopen( $import_txt_path , "w");
            $import_state_arr = array('import_state' => 'off','import_stop' => 'on');
        	$state_arr = serialize($import_state_arr);
            fwrite($open_file , $state_arr);
			fclose($open_file);
			if ($log_link_path != null){
				$response['show_log'] = true;	
			}
			else{
				$response['show_log'] = false;
			}
			$response['import_state'] = true;
			$response['log_link'] = $log_link_path;	
		}	
		echo wp_json_encode($response);
		wp_die();
	}


	/**
	* Checks whether the import function is stopped or the page is refreshed
	*/
	public function import_stop_function(){
		
		$upload_dir = SaveMapping::$smackcsv_instance->create_upload_dir();
		/* Gets string 'false' when page is refreshed */
		
		if($_POST['Stop'] == 'false'){
			$import_txt_path = $upload_dir.'import_state.txt';
			chmod($import_txt_path , 0777);
			$import_state_arr = array();

			$open_file = fopen( $import_txt_path , "w");
			$import_state_arr = array('import_state' => 'on','import_stop' => 'off');
			$state_arr = serialize($import_state_arr);
			fwrite($open_file , $state_arr);
			fclose($open_file);
		}
		wp_die();
	}


	/**
	* Starts the import process
	*/
	public function background_starts_function(){
		$hash_key  = $_POST['HashKey'];
		$check = $_POST['Check'];
		$rollback_option = $_POST['RollBack'];
		global $wpdb;

		//first check then set on	
		$upload_dir = SaveMapping::$smackcsv_instance->create_upload_dir();
		$import_txt_path = $upload_dir.'import_state.txt';
		chmod($import_txt_path , 0777);
		$import_state_arr = array();

		$open_file = fopen( $import_txt_path , "w");
        $import_state_arr = array('import_state' => 'on','import_stop' => 'on');
        $state_arr = serialize($import_state_arr);
        fwrite($open_file , $state_arr);
		fclose($open_file);

		$helpers_instance = ImportHelpers::getInstance();
		$core_instance = CoreFieldsImport::getInstance();
		$import_config_instance = ImportConfiguration::getInstance();
		$file_manager_instance = FileManager::getInstance();
		$log_manager_instance = LogManager::getInstance();
		global $core_instance;

		$file_table_name = $wpdb->prefix ."smackcsv_file_events";
		$template_table_name = $wpdb->prefix ."ultimate_csv_importer_mappingtemplate";
		$log_table_name = $wpdb->prefix ."import_detail_log";

		$response = [];	
		$background_values = $wpdb->get_results("SELECT mapping , module  FROM $template_table_name WHERE `eventKey` = '$hash_key' ");	
		foreach($background_values as $values){
			$mapped_fields_values = $values->mapping;	
			$selected_type = $values->module;
		}

		if($rollback_option == 'true'){
			$tables = $import_config_instance->get_rollback_tables($selected_type);
			$import_config_instance->set_backup_restore($tables,$hash_key,'backup');	
		}

		$get_id = $wpdb->get_results( "SELECT id , mode ,file_name , total_rows FROM $file_table_name WHERE `hash_key` = '$hash_key'");

		$get_mode = $get_id[0]->mode;
		$total_rows = $get_id[0]->total_rows;
		$file_name = $get_id[0]->file_name;
		$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$file_size = filesize($upload_dir.$hash_key.'/'.$hash_key);
		$filesize = $helpers_instance->formatSizeUnits($file_size);

		$remain_records = $total_rows - 1;
		//$fields = $wpdb->insert( $log_table_name , array('file_name' => $file_name , 'hash_key' => $hash_key , 'total_records' => $total_rows , 'filesize' => $filesize ) );
		$fields = $wpdb->insert( $log_table_name , array('file_name' => $file_name , 'hash_key' => $hash_key , 'total_records' => $total_rows , 'filesize' => $filesize , 'processing_records' => 1 , 'remaining_records' => $remain_records , 'status' => 'Processing' ) );

		$map = unserialize($mapped_fields_values);

		if($file_extension == 'csv' || $file_extension == 'txt'){
		
			ini_set("auto_detect_line_endings", true);
			$info = [];
			if (($h = fopen($upload_dir.$hash_key.'/'.$hash_key, "r")) !== FALSE) 
			{
			// Convert each line into the local $data variable	
			$line_number = 0;
			$header_array = [];
			$value_array = [];
			$addHeader = true;

			while (($data = fgetcsv($h, 0, ",")) !== FALSE) 
			{			
				// Read the data from a single line
				array_push($info , $data);
				
				if($line_number == 0){
					$header_array = $info[$line_number];
					
				}else{
					$value_array = $info[$line_number];
					
					$get_arr = $this->main_import_process($map , $header_array ,$value_array , $selected_type , $get_mode, $line_number , $check , $hash_key);
					$post_id = $get_arr['id'];	
					$core_instance->detailed_log = $get_arr['detail_log'];
					
					$helpers_instance->get_post_ids($post_id ,$hash_key);

					$remaining_records = $total_rows - $line_number;
					$fields = $wpdb->get_results("UPDATE $log_table_name SET processing_records = $line_number , remaining_records = $remaining_records , status = 'Processing' WHERE hash_key = '$hash_key'");

					if($line_number == $total_rows){
						$fields = $wpdb->get_results("UPDATE $log_table_name SET status = 'Completed' WHERE hash_key = '$hash_key'");
					}
					
					if (count($core_instance->detailed_log) > 5) {
						$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
						$addHeader = false;
						$core_instance->detailed_log = [];
					}		
				}

				// get the pause or resume state

				$open_txt = fopen($import_txt_path , "r");
				$read_text_ser = fread($open_txt , filesize($import_txt_path));  
				$read_state = unserialize($read_text_ser);    
				fclose($open_txt);

				if($read_state['import_stop'] == 'off'){
					return;
				}
				
				while($read_state['import_state'] == 'off'){	
					$open_txts = fopen($import_txt_path , "r");
					$read_text_sers = fread($open_txts , filesize($import_txt_path));  
					$read_states = unserialize($read_text_sers);    
					fclose($open_txts);
					
					if($read_states['import_state'] == 'on'){
						break;
					}
	
					if($read_states['import_stop'] == 'off'){
						return;
					}
				}
				
				$line_number++;			
			}	
			//fclose($file_handle);
			fclose($h);
			}
		}

		if($file_extension == 'xml'){
			$path = $upload_dir . $hash_key . '/' . $hash_key;
			$xml_instance = XmlHandler::getInstance();
			
			$line_number = 0;
			$header_array = [];
			$value_array = [];
			$addHeader = true;

			$xml = simplexml_load_file($path);
			$xml_arr = json_decode( json_encode($xml) , 1);
	
			foreach($xml->children() as $child){   
				$child_name = $child->getName();    
			}
		
			//$total_rows = $xml_instance->get_xml_count($path , $child_name);
	
			for($line_number = 0; $line_number < $total_rows ; $line_number++){
					$header_array = array_keys($xml_arr[$child_name][0]);
					
					$value_array = array_values($xml_arr[$child_name][$line_number]);
				
					foreach($value_array as $key => $value){  
						if(empty($value)){
							$value_array[$key] = '';
						}
					}
					$get_arr = $this->main_import_process($map , $header_array ,$value_array , $selected_type , $get_mode, $line_number , $check , $hash_key);
					$post_id = $get_arr['id'];	
					$core_instance->detailed_log = $get_arr['detail_log'];

					$helpers_instance->get_post_ids($post_id ,$hash_key);

					$remaining_records = $total_rows - $line_number;
					$fields = $wpdb->get_results("UPDATE $log_table_name SET processing_records = $line_number + 1 , remaining_records = $remaining_records + 1, status = 'Processing' WHERE hash_key = '$hash_key'");

					if($line_number == $total_rows - 1){
						$fields = $wpdb->get_results("UPDATE $log_table_name SET status = 'Completed' WHERE hash_key = '$hash_key'");
					}

					if (count($core_instance->detailed_log) > 5) {
						$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
						$addHeader = false;
						$core_instance->detailed_log = [];
					}		
				
					$open_txt = fopen($import_txt_path , "r");
					$read_text_ser = fread($open_txt , filesize($import_txt_path));  
					$read_state = unserialize($read_text_ser);    
					fclose($open_txt);

					if($read_state['import_stop'] == 'off'){
						return;
					}
					
					while($read_state['import_state'] == 'off'){	
						$open_txts = fopen($import_txt_path , "r");
						$read_text_sers = fread($open_txts , filesize($import_txt_path));  
						$read_states = unserialize($read_text_sers);    
						fclose($open_txts);
						
						if($read_states['import_state'] == 'on'){
							break;
						}
		
						if($read_states['import_stop'] == 'off'){
							return;
						}
					}
			}
		}

		if (count($core_instance->detailed_log) > 0) {
			$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
		}

		//$response['message'] = $core_instance->detailed_log;

		$file_manager_instance->manage_records($hash_key ,$selected_type , $file_name , $total_rows);
	       
		foreach ($value_array as $key => $value) {
			if(preg_match("/<img/", $value)){
				SaveMapping::$smackcsv_instance->image_schedule();
				$image = $wpdb->get_results("select * from {$wpdb->prefix}ultimate_csv_importer_shortcode_manager where hash_key = '{$hash_key}'");
				if(!empty($image)){
					SaveMapping::$smackcsv_instance->delete_image_schedule();
				}
			}
		}
			
		$upload = wp_upload_dir();
        $upload_base_url = $upload['baseurl'];
		$upload_url = $upload_base_url . '/smack_uci_uploads/imports/';
		$log_path = $upload_dir.$hash_key.'/'.$hash_key.'.html';
		//if(file_exists($log_path)){
			$log_link_path = $upload_url. $hash_key .'/'.$hash_key.'.html';
			$response['success'] = true;
			$response['log_link'] = $log_link_path;
			if($rollback_option == 'true'){
				$response['rollback'] = true;
			}	
		 // }
			// else{
			// $response['success'] = false;
		 // }
		unlink($import_txt_path);
		echo wp_json_encode($response);
		wp_die();
	}

	public function main_import_process($map , $header_array ,$value_array , $selected_type , $get_mode, $line_number , $check , $hash_key){
		$return_arr = [];
		$core_instance = CoreFieldsImport::getInstance();
		global $core_instance;

		foreach($map as $group_name => $group_value){
			if($group_name == 'CORE'){
				$core_instance = CoreFieldsImport::getInstance();
				$post_id = $core_instance->set_core_values($header_array ,$value_array , $map['CORE'] , $selected_type , $get_mode, $line_number , $check , $hash_key);		
			}
		}

		foreach($map as $group_name => $group_value){
			switch($group_name){

				case 'ACF':	
					$acf_pro_instance = ACFProImport::getInstance();
					$acf_pro_instance->set_acf_pro_values( $header_array, $value_array, $map['ACF'], $post_id, $selected_type );
					break;

				case 'RF':
					$acf_pro_instance = ACFProImport::getInstance();
					$acf_pro_instance->set_acf_rf_values( $header_array, $value_array, $map['RF'], $post_id, $selected_type );
					break;

				case 'GF':
					$acf_pro_instance = ACFProImport::getInstance();
					$acf_pro_instance->set_acf_gf_values( $header_array, $value_array, $map['GF'], $post_id, $selected_type );
					break;

				case 'TYPES':
					$toolset_instance = ToolsetImport::getInstance();
					$toolset_instance->set_toolset_values( $header_array, $value_array, $map['TYPES'], $post_id, $selected_type , $get_mode );
					break;

				case 'PODS':
					$pods_instance = PodsImport::getInstance();
					$pods_instance->set_pods_values($header_array ,$value_array , $map['PODS'], $post_id , $selected_type);
					break;

				case 'AIOSEO':
					$all_seo_instance = AllInOneSeoImport::getInstance();
					$all_seo_instance->set_all_seo_values($header_array ,$value_array , $map['AIOSEO'], $post_id , $selected_type);
					break;

				case 'YOASTSEO':
					$yoast_instance = YoastSeoImport::getInstance();
					$yoast_instance->set_yoast_values($header_array ,$value_array , $map['YOASTSEO'], $post_id , $selected_type);
					break;

				case 'ECOMMETA':
					$product_meta_instance = ProductMetaImport::getInstance();
					$product_meta_instance->set_product_meta_values($header_array ,$value_array , $map['ECOMMETA'], $post_id , $selected_type , $line_number , $get_mode , $map['CORE']);
					break;

				case 'REFUNDMETA':
					$product_meta_instance = ProductMetaImport::getInstance();
					$product_meta_instance->set_product_meta_values($header_array ,$value_array , $map['REFUNDMETA'], $post_id , $selected_type , $line_number , $get_mode , $map['CORE']);
					break;

				case 'ORDERMETA':
					$product_meta_instance = ProductMetaImport::getInstance();
					$product_meta_instance->set_product_meta_values($header_array ,$value_array , $map['ORDERMETA'], $post_id , $selected_type , $line_number , $get_mode , $map['CORE']);
					break;

				case 'COUPONMETA':
					$product_meta_instance = ProductMetaImport::getInstance();
					$product_meta_instance->set_product_meta_values($header_array ,$value_array , $map['COUPONMETA'], $post_id , $selected_type , $line_number , $get_mode , $map['CORE']);
					break;

				case 'CCTM':
					$cctm_instance = CCTMImport::getInstance();
					$cctm_instance->set_cctm_values($header_array ,$value_array , $map['CCTM'], $post_id , $selected_type);
					break;

				case 'CFS':
					$cfs_instance = CFSImport::getInstance();
					$cfs_instance->set_cfs_values($header_array ,$value_array , $map['CFS'], $post_id , $selected_type);
					break;

				case 'CMB2':
					$cmb2_instance = CMB2Import::getInstance();
					$cmb2_instance->set_cmb2_values($header_array ,$value_array , $map['CMB2'], $post_id , $selected_type);
					break;

				case 'BSI':
					$bsi_instance = BSIImport::getInstance();
					$bsi_instance->set_bsi_values($header_array ,$value_array , $map['BSI'], $post_id , $selected_type);
					break;

				case 'WPMEMBERS':
					$wpmembers_instance = WPMembersImport::getInstance();
					$wpmembers_instance->set_wpmembers_values($header_array ,$value_array , $map['WPMEMBERS'], $post_id , $selected_type);
					break;

				case 'MULTIROLE':
					$multirole_instance = MultiroleImport::getInstance();
					$multirole_instance->set_multirole_values($header_array ,$value_array , $map['MULTIROLE'], $post_id , $selected_type);
					break;

				case 'ULTIMATEMEMBER':
					$ultimate_instance = UltimateImport::getInstance();
					$ultimate_instance->set_ultimate_values($header_array ,$value_array , $map['ULTIMATEMEMBER'], $post_id , $selected_type);
					break;

				case 'WPECOMMETA':
					$wpecom_custom_instance = WPeComCustomImport::getInstance();
					$wpecom_custom_instance->set_wpecom_custom_values($header_array ,$value_array , $map['WPECOMMETA'], $post_id , $selected_type);
					break;

				case 'TERMS':
					$terms_taxo_instance = TermsandTaxonomiesImport::getInstance();
					$terms_taxo_instance->set_terms_taxo_values($header_array ,$value_array , $map['TERMS'], $post_id , $selected_type , $get_mode , $line_number , $map['WPML']);
					break;

				case 'WPML':	
					$wpml_instance = WPMLImport::getInstance();
					$wpml_instance->set_wpml_values( $header_array, $value_array, $map['WPML'], $post_id, $selected_type );
					break;

				case 'CORECUSTFIELDS':
					$wordpress_custom_instance = WordpressCustomImport::getInstance();
					$wordpress_custom_instance->set_wordpress_custom_values($header_array ,$value_array , $map['CORECUSTFIELDS'], $post_id , $selected_type);
					break;

				case 'EVENTS':
					$merge = [];
					$merge = array_merge($map['CORE'] , $map['EVENTS']);

					$events_instance = EventsManagerImport::getInstance();
					$events_instance->set_events_values($header_array ,$value_array , $merge, $post_id , $selected_type , $get_mode);
					break;

				case 'NEXTGEN':
					$nextgen_import = SaveMapping::$nextgen_instance->nextgenImport($header_array ,$value_array , $map['NEXTGEN'], $post_id , $selected_type);
					break;

				case 'COREUSERCUSTFIELDS':
					$wordpress_custom_instance = WordpressCustomImport::getInstance();
					$wordpress_custom_instance->set_wordpress_custom_values($header_array ,$value_array , $map['COREUSERCUSTFIELDS'], $post_id , $selected_type);
					break;

			}	
		}
		
		$return_arr['id'] = $post_id;
		$return_arr['detail_log'] = $core_instance->detailed_log;		
		return $return_arr;	
	}

}
