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
			return self::$instance;
		}
		return self::$instance;
	}

	public function schedule_import($data_array){
		global $wpdb,$core_instance;
		$hash_key  = $data_array['eventkey'];
		$check = $data_array['duplicate_headers'];
		$last_run = $data_array['lastrun'];
		$next_run = $data_array['nexrun'];
		$data_id = $data_array['id'];
		$frequency = $data_array['frequency'];
		$helpers_instance = ImportHelpers::getInstance();
		$core_instance = CoreFieldsImport::getInstance();
		$file_manager_instance = FileManager::getInstance();
		$log_manager_instance = LogManager::getInstance();
		$response = [];
		$file_table_name = $wpdb->prefix ."smackcsv_file_events";
		$template_table_name = $wpdb->prefix ."ultimate_csv_importer_mappingtemplate";
		$log_table_name = $wpdb->prefix ."import_detail_log";
		ScheduleImport::$smackcsv_instance = SmackCSV::getInstance();
		$upload_dir = ScheduleImport::$smackcsv_instance->create_upload_dir();
		$background_values = $wpdb->get_results("SELECT mapping , module  FROM $template_table_name WHERE `eventKey` = '$hash_key' ");
        $gmode = 'Schedule';
		foreach($background_values as $values){
			$mapped_fields_values = $values->mapping;	
			$selected_type = $values->module;
		}

		$get_id  = $wpdb->get_results( "SELECT id , mode ,file_name , total_rows FROM $file_table_name WHERE `hash_key` = '$hash_key'");
		$get_mode = $get_id[0]->mode;
		$total_rows = $get_id[0]->total_rows;
		$file_name = $get_id[0]->file_name;
		$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
		$file_size = filesize($upload_dir.$hash_key.'/'.$hash_key);
		$filesize = $helpers_instance->formatSizeUnits($file_size);

		$update_based_on = get_option('csv_importer_update_using');
        if(empty($update_based_on)){
            $update_based_on = 'normal';
        }

		$wpdb->insert( $log_table_name , array('file_name' => $file_name , 'hash_key' => $hash_key , 'total_records' => $total_rows, 'filesize' => $filesize  ) );
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
					ignore_user_abort(1);
					set_time_limit(0);
					global $wpdb;
					$schedule_tableName = $wpdb->prefix.'ultimate_csv_importer_scheduled_import';
					$getScheduling_data = $wpdb->get_results("select * from $schedule_tableName");
					if(empty($getScheduling_data))
					{
						return true;
					}	
					$trimmed_array = array_map('trim', $data);
					array_push($info , $trimmed_array);

					if($line_number == 0){
						$header_array = $info[$line_number];

					}else{
						$value_array = $info[$line_number];

						$get_arr = ScheduleImport::$save_mapping_instance->main_import_process($map , $header_array ,$value_array , $selected_type , $get_mode, $line_number , $check , $hash_key, $update_based_on, $gmode);
						$post_id = $get_arr['id'];	
						$core_instance->detailed_log = $get_arr['detail_log'];
						$helpers_instance->get_post_ids($post_id ,$hash_key);
						global $wpdb;						
						$log_table_name = $wpdb->prefix ."import_detail_log";
						$remaining_records = $total_rows - $line_number;
						$wpdb->get_results("UPDATE $log_table_name SET processing_records = $line_number , remaining_records = $remaining_records , status = 'Processing' WHERE hash_key = '$hash_key'");

						if($line_number == $total_rows){
							$wpdb->get_results("UPDATE $log_table_name SET  status = 'Completed' WHERE hash_key = '$hash_key'");
				     	}

						if (count($core_instance->detailed_log) > 5) {
							$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
							$addHeader = false;
							$core_instance->detailed_log = [];
						}
					}
					$line_number++;	
					if($line_number >= $totRecords) {	
						// $wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_import set start_limit = 0, lastrun = '{$data_array['lastrun']}',nexrun = '{$data_array['nexrun']}', cron_status = 'completed' where id = '{$data_array['id']}'" );
						if($frequency != 0){
						$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_import set start_limit = 0, lastrun = '$last_run',nexrun = '$next_run', cron_status = 'waiting for next schedule' where id = $data_id" );
						}
						else{
						$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_import set start_limit = 0, lastrun = '$last_run',nexrun = '$next_run', cron_status = 'completed' where id = $data_id" );
						}
					}	
				}
				fclose($h);
			}
		}
		if($file_extension == 'xml'){
			$path = $upload_dir . $hash_key . '/' . $hash_key;
			$line_number = 0;
			$header_array = [];
			$value_array = [];
			$addHeader = true;
			
			for($line_number = 0; $line_number < $total_rows ; $line_number++){
				$xml_class = new XmlHandler();
				$parse_xml = $xml_class->parse_xmls($hash_key,$line_number);
				$i = 0;
				foreach($parse_xml as $xml_key => $xml_value){
					if(is_array($xml_value)){
						foreach ($xml_value as $e_key => $e_value){
							$header_array['header'][$i] = $e_value['name'];
							$value_array['value'][$i] = $e_value['value'];
							$i++;
						}
					}
				}
				$xml = simplexml_load_file($path);
				foreach($xml->children() as $child){   
					$tag = $child->getName();     
				}
				$total_xml_count = $xml_class->get_xml_count($path , $tag);
				if($total_xml_count == 0 ){
					$sub_child = $xml_class->get_child($child,$path);
					$tag = $sub_child['child_name'];
					$total_xml_count = $sub_child['total_count'];
				}
				$doc = new \DOMDocument();
				$doc->load($path);
				foreach ($map as $field => $value) {
					foreach ($value as $head => $val) {
						if (preg_match('/{/',$val) && preg_match('/}/',$val)){
							preg_match_all('/{(.*?)}/', $val, $matches);
							$line_numbers = $line_number+1;	
							$val = preg_replace("{"."(".$tag."[+[0-9]+])"."}", $tag."[".$line_numbers."]", $val);
							for($i = 0 ; $i < count($matches[1]) ; $i++){		
								$matches[1][$i] = preg_replace("(".$tag."[+[0-9]+])", $tag."[".$line_numbers."]", $matches[1][$i]);
								$value = $this->parse_element($doc, $matches[1][$i], $line_number);	
								$search = '{'.$matches[1][$i].'}';
								$val = str_replace($search, $value, $val);
							}
							$mapping[$field][$head] = $val;	
						} 
						else{
							$mapping[$field][$head] = $val;
						}

					}
				}
				$get_arr = ScheduleImport::$save_mapping_instance->main_import_process($mapping , $header_array['header'] ,$value_array['value'] , $selected_type , $get_mode, $line_number , $check , $hash_key, $update_based_on, $gmode);
				$post_id = $get_arr['id'];	
				$core_instance->detailed_log = $get_arr['detail_log'];

				$helpers_instance->get_post_ids($post_id ,$hash_key);
				$line_numbers = $line_number + 1;
				$remaining_records = $total_rows - $line_numbers;
				$wpdb->get_results("UPDATE $log_table_name SET processing_records = $line_number + 1 , remaining_records = $remaining_records , status = 'Processing' WHERE hash_key = '$hash_key'");

				if($line_number == $total_rows - 1){
					$wpdb->get_results("UPDATE $log_table_name SET status = 'Completed' WHERE hash_key = '$hash_key'");
				}

				if (count($core_instance->detailed_log) > 5) {
					$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
					$addHeader = false;
					$core_instance->detailed_log = [];
				}

				if($line_number == $total_rows - 1) {	
					$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_import set start_limit = 0, lastrun = '$last_run',nexrun = '$next_run', cron_status = 'completed' where id = $data_id" );
				}
			}
		}

		if (count($core_instance->detailed_log) > 0) {
			$log_manager_instance->get_event_log($hash_key , $file_name , $file_extension, $get_mode , $total_rows , $selected_type , $core_instance->detailed_log, $addHeader);
		}
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
	
	public function parse_element($xml,$query){
		$query = strip_tags($query);
		$xpath = new \DOMXPath($xml);
		$entries = $xpath->query($query);
		$content = $entries->item(0)->textContent;
		return $content;
	}
}