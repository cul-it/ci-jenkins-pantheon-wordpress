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

/**
 * Class ScheduleImport
 * @package Smackcoders\WCSV
 */

class ScheduleImport {

	protected static $instance=null;
	protected static $smackcsv_instance = null;
	protected static $save_mapping_instance = null;
	protected static $core = null;
	private static $validatefile = [];


	public static  function getInstance() {
		if (ScheduleImport::$instance == null) {
			ScheduleImport::$instance = new ScheduleImport;
			ScheduleImport::$validatefile = new ValidateFile;
			ScheduleImport::$smackcsv_instance = SmackCSV::getInstance();
			ScheduleImport::$save_mapping_instance = SaveMapping::getInstance();
			//ScheduleImport::$core = CoreFieldsImport::getInstance();
			return self::$instance;
		}
		return self::$instance;
	}

	public function schedule_import($data){
		$hash_key  = $data['eventkey'];
		$check = $data['duplicate_headers'];

		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();
		$core_instance = CoreFieldsImport::getInstance();
		$import_config_instance = ImportConfiguration::getInstance();
		$file_manager_instance = FileManager::getInstance();
		$log_manager_instance = LogManager::getInstance();
		global $core_instance;

		$response = [];

		$file_table_name = $wpdb->prefix ."smackcsv_file_events";
		$template_table_name = $wpdb->prefix ."ultimate_csv_importer_mappingtemplate";
		$log_table_name = $wpdb->prefix ."import_detail_log";
		ScheduleImport::$smackcsv_instance = SmackCSV::getInstance();
		$upload_dir = ScheduleImport::$smackcsv_instance->create_upload_dir();
		$background_values = $wpdb->get_results("SELECT mapping , module  FROM $template_table_name WHERE `eventKey` = '$hash_key' ");

		foreach($background_values as $values){
			$mapped_fields_values = $values->mapping;	
			$selected_type = $values->module;
		}

		$get_id  = $wpdb->get_results( "SELECT id , mode ,file_name , total_rows FROM $file_table_name WHERE `hash_key` = '$hash_key'");

		$get_mode = $get_id[0]->mode;
		$get_hash_id = $get_id[0]->id;
		$total_rows = $get_id[0]->total_rows;
		$file_name = $get_id[0]->file_name;
		$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$file_size = filesize($upload_dir.$hash_key.'/'.$hash_key);
		$filesize = $helpers_instance->formatSizeUnits($file_size);

		$fields = $wpdb->insert( $log_table_name , array('file_name' => $file_name , 'hash_key' => $hash_key , 'total_records' => $total_rows, 'filesize' => $filesize  ) );
		$map = unserialize($mapped_fields_values);

		if($file_extension == 'csv' || $file_extension == 'txt'){
			ini_set("auto_detect_line_endings", true	);
			$info = [];
			if (($h = fopen($upload_dir.$hash_key.'/'.$hash_key, "r")) !== FALSE) 
			{
				$inputFile = $upload_dir.$hash_key.'/'.$hash_key;
				$lines = file($inputFile); 
				$totRecords = count($lines);
				$line_number = 0;
				$header_array = [];
				$value_array = [];
				$addHeader = true;
                $delimiters = array( ',','\t',';','|',':','&nbsp');
				$file_path = $upload_dir . $hash_key . '/' . $hash_key;
				$delimiter = ScheduleImport::$validatefile->getFileDelimiter($file_path, 5);
				$array_index = array_search($delimiter,$delimiters);
				if($array_index == 5){
					$delimiters[$array_index] = ' ';
				}
				while (($data = fgetcsv($h, 0, $delimiters[$array_index])) !== FALSE) 
				{	
					global $wpdb;
					$schedule_tableName = $wpdb->prefix.'ultimate_csv_importer_scheduled_import';
					$getScheduling_data = $wpdb->get_results("select * from $schedule_tableName");
					if(empty($getScheduling_data))
					{
						return true;
					}	
					// Read the data from a single line
					array_push($info , $data);

					if($line_number == 0){
						$header_array = $info[$line_number];

					}else{
						$value_array = $info[$line_number];

						$get_arr = ScheduleImport::$save_mapping_instance->main_import_process($map , $header_array ,$value_array , $selected_type , $get_mode, $line_number , $check , $hash_key);
						$post_id = $get_arr['id'];	
						$core_instance->detailed_log = $get_arr['detail_log'];

						$helpers_instance->get_post_ids($post_id ,$hash_key);

						global $wpdb;
						
						$log_table_name = $wpdb->prefix ."import_detail_log";
						$remaining_records = $total_rows - $line_number;
						$fields = $wpdb->get_results("UPDATE $log_table_name SET processing_records = $line_number , remaining_records = $remaining_records , status = 'Processing' WHERE hash_key = '$hash_key'");

						if($line_number == $total_rows){
							$fields = $wpdb->get_results("UPDATE $log_table_name SET  status = 'Completed' WHERE hash_key = '$hash_key'");
				     	}

						if (count($core_instance->detailed_log) > 5) {
							$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
							$addHeader = false;
							$core_instance->detailed_log = [];
						}
					}
					$line_number++;	
					//$response = SaveMapping::$core->detailed_log;		
				}
				if($line_number >= $totRecords) {	
					$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_import set start_limit = 0, lastrun = '{$data['lastrun']}',nexrun = '{$data['nexrun']}', cron_status = 'completed' where id = '{$data['id']}'" );
				}
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

			for($line_number = 0; $line_number < $total_rows ; $line_number++){
				$header_array = array_keys($xml_arr[$child_name][0]);

				$value_array = array_values($xml_arr[$child_name][$line_number]);

				foreach($value_array as $key => $value){  
					if(empty($value)){
						$value_array[$key] = '';
					}
				}
				$get_arr = ScheduleImport::$save_mapping_instance->main_import_process($map , $header_array ,$value_array , $selected_type , $get_mode, $line_number , $check , $hash_key);
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

				if($line_number == $total_rows - 1) {	
					$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_import set start_limit = 0, lastrun = '{$data['lastrun']}',nexrun = '{$data['nexrun']}', cron_status = 'completed' where id = '{$data['id']}'" );
				}
			}
		}

		if (count($core_instance->detailed_log) > 0) {
			$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
		}

		//$result = self::$core->detailed_log;
		//$response['message'] = $result;

		$file_manager_instance->manage_records($hash_key ,$selected_type , $file_name , $total_rows);

		$upload = wp_upload_dir();
		$upload_base_url = $upload['baseurl'];
		$upload_url = $upload_base_url . '/smack_uci_uploads/imports/';

		$log_path = $upload_dir.$hash_key.'/'.$hash_key.'.html';

		if(file_exists($log_path)){
			$log_link_path = $upload_url. $hash_key .'/'.$hash_key.'.html';
			$response['success'] = true;
			$response['log_link'] = $log_link_path;
		}else{
			$response['success'] = false;
		}
	}
}