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

class ExportHandler {
	protected static $instance = null,$export_extension;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->doHooks();
			ExportHandler::$export_extension = ExportExtension::getInstance();
		}
		return self::$instance;
	}

	public  function doHooks(){
		add_action('wp_ajax_get_post_types',array($this,'getPostTypes'));
        add_action('wp_ajax_get_taxonomies',array($this,'getTaxonomies'));
		add_action('wp_ajax_get_authors',array($this,'getAuthors'));
		
		add_action('wp_ajax_export_template', array($this, 'exportTemplate'));
		add_action('wp_ajax_export_already_mapped', array($this, 'exportAlreadyMapped'));
	}

	/**
	 * SmackUCIExporter constructor.
	 *
	 * Set values into global variables based on post value
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}


	public  function getPostTypes(){
		$i = 0;
		$get_post_types = get_post_types();
		array_push($get_post_types, 'widgets');
		foreach ($get_post_types as $key => $value) {
			if (($value !== 'featured_image') && ($value !== 'attachment') && ($value !== 'wpsc-product') && ($value !== 'wpsc-product-file') && ($value !== 'revision') && ($value !== 'post') && ($value !== 'page') && ($value !== 'wp-types-group') && ($value !== 'wp-types-user-group')  && ($value !== 'product_variation') && ($value !== 'shop_order') && ($value !== 'shop_coupon') && ($value !== 'acf') && ($value !== 'acf-field') && ($value !== 'acf-field-group') && ($value !== '_pods_pod') && ($value !== '_pods_field') && ($value !== 'shop_order_refund') && ($value !== 'shop_webhook')) {
				$response['custom_post_type'][$i] = $value;
				$i++;
			}
		}						
		echo wp_json_encode($response);
		wp_die();
	}

	public function getAuthors(){
		$i = 0;
		$blogusers = get_users( [ 'role__in' => [ 'administrator', 'author' ] ]);
		foreach( $blogusers as $user ) { 
			$response['user_name'][$i] = $user->display_name;
			$response['user_id'][$i] = $user->ID;
			$i++;
		}
		echo wp_json_encode($response);
		wp_die();
	}

	public function getTaxonomies(){
		$i = 0;
		foreach (get_taxonomies() as $key => $value) {
				$response['taxonomies'][$i] = $value;
				$i++;
		}
		echo wp_json_encode($response);
		wp_die();
	}

	public function exportTemplate(){
		global $wpdb;
		$module = $_POST['module'];
		$optionalType = $_POST['optionalType'];

		$export_template_table_name = $wpdb->prefix ."ultimate_csv_importer_export_template";

		if(empty($optionalType)){
			$get_result = $wpdb->get_results("SELECT filename, createdtime, export_type FROM $export_template_table_name WHERE module = '$module' ", ARRAY_A);
		}else{
			$get_result = $wpdb->get_results("SELECT filename, createdtime, export_type FROM $export_template_table_name WHERE module = '$module' AND optional_type = '$optionalType' ", ARRAY_A);
		}

		$details = [];
		$info = [];
		if(!empty($get_result)) {
			foreach($get_result as $value){				
				
				$details['filename'] = $value['filename']. '.' . $value['export_type'];
				$details['module'] = $module;
				$details['optionalType'] = $optionalType;
				$details['createdtime'] = $value['createdtime'];
				
				array_push($info , $details);
			}
			$response['success'] = true;
			$response['info'] = $info;
			echo wp_json_encode($response);
			wp_die();
		}
	}

	public function exportAlreadyMapped(){
		global $wpdb;
		$module = $_POST['module'];
		$optionalType = $_POST['optionalType'];

		$full_filename = explode(".", $_POST['filename']);
		$filename = $full_filename[0];
		$filetype = $full_filename[1];
		$already_mapped = [];

		if(empty($optionalType)){
			$get_template_details = $wpdb->get_results("SELECT conditions, event_exclusions, split, split_limit, export_mode, actual_start_date, actual_end_date, actual_schedule_date FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE filename = '$filename' AND module = '$module' ");
		}
		else{
			$get_template_details = $wpdb->get_results("SELECT conditions, event_exclusions, split, split_limit, export_mode, actual_start_date, actual_end_date, actual_schedule_date FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE filename = '$filename' AND module = '$module' AND optional_type = '$optionalType' ");
		}

		$get_template_conditions = $get_template_details[0]->conditions;
		$get_template_eventExclusions = $get_template_details[0]->event_exclusions;

		$template_conditions = unserialize($get_template_conditions);
		$template_exclusions = unserialize($get_template_eventExclusions);

		$already_mapped['filename'] = $filename;
		$already_mapped['export_type'] = $filetype;
	
		if($get_template_details[0]->split == 'true'){
			$already_mapped['split_record'] = true;
			$already_mapped['split_limit'] = $get_template_details[0]->split_limit;
		}
		else{
			$already_mapped['split_record'] = false;
		}

		foreach($template_conditions as $condition_key => $condition_value){
			if($condition_key == 'delimiter'){
				if($condition_value['is_check'] == true){
					$already_mapped['is_delimiter'] = true;
					$already_mapped['delimiter'] = $condition_value['delimiter'];
					$already_mapped['optional_delimiter'] = $condition_value['optional_delimiter'];
				}
				else{
					$already_mapped['is_delimiter'] = false;
				}
			}
			elseif($condition_key == 'specific_period'){
				if($condition_value['is_check'] == true){
					$already_mapped['specific_period'] = true;

					$already_mapped['from_date'] = $get_template_details[0]->actual_start_date; 
					$already_mapped['to_date'] = $get_template_details[0]->actual_end_date;
				}
				else{
					$already_mapped['specific_period'] = false;
				}
			}
			elseif($condition_key == 'specific_status'){
				if($condition_value['is_check'] == true){
					$already_mapped['specific_status'] = true;
					$already_mapped['status'] = $condition_value['status'];
				}
				else{
					$already_mapped['specific_status'] = false;
				}
			}
			elseif($condition_key == 'specific_authors'){
				if($condition_value['is_check'] == true){
					$already_mapped['specific_authors'] = true;
					$already_mapped['author'] = $condition_value['author'];
				}
				else{
					$already_mapped['specific_authors'] = false;
				}
			}
			elseif($condition_key == 'specific_category'){
				if($condition_value['is_check'] == true){
					$already_mapped['specific_category'] = true;
					$already_mapped['category'] = $condition_value['category'];
				}
				else{
					$already_mapped['specific_category'] = false;
				}
			}
		}

		$is_exclusion = false;
		foreach($template_exclusions as $exclusion_key => $exclusion_value){
			if($exclusion_key == 'is_check'){
				if($exclusion_value == true){
					$already_mapped['exclusion_headers'] = true;
					$is_exclusion = true;
				}
				else{
					$already_mapped['exclusion_headers'] = false;
				}
			}
			elseif(($exclusion_key == 'exclusion_headers') && ($is_exclusion)){
				$already_mapped['headers'] = $exclusion_value['header'];
			}
		}

		if($get_template_details[0]->export_mode == 'schedule'){

			if(empty($optionalType)){
				$get_schedule_details = $wpdb->get_results("SELECT host_name, host_port, host_username, host_password, host_path, scheduleddate, frequency, scheduledtimetorun, time_zone, exportbymethod FROM {$wpdb->prefix}ultimate_csv_importer_scheduled_export WHERE file_name = '$filename' AND file_type = '$filetype' AND module = '$module' ");
			}
			else{
				$get_schedule_details = $wpdb->get_results("SELECT host_name, host_port, host_username, host_password, host_path, scheduleddate, frequency, scheduledtimetorun, time_zone, exportbymethod FROM {$wpdb->prefix}ultimate_csv_importer_scheduled_export WHERE file_name = '$filename' AND file_type = '$filetype' AND module = '$module' AND optional_type = '$optionalType' ");
			}

			$already_mapped['is_schedule'] = true;

			$schedule_frequency_array = array('OneTime', 'Daily', 'Weekly', 'Monthly', 'Hourly', 'Every 30 mins', 'Every 15 mins', 'Every 10 mins', 'Every 5 mins');
			
			$scheduled_details = [];

	
			$scheduled_details['schedule_date'] = $get_template_details[0]->actual_schedule_date;

			$get_frequency = $get_schedule_details[0]->frequency;
			$scheduled_details['schedule_frequency'] = $schedule_frequency_array[$get_frequency];
			$scheduled_details['time_zone'] = $get_schedule_details[0]->time_zone;
			$scheduled_details['schedule_time'] = $get_schedule_details[0]->scheduledtimetorun;

			$schedule_hosts = [];
			$schedule_hosts['host_name'] = $get_schedule_details[0]->host_name;
			$schedule_hosts['host_port'] = $get_schedule_details[0]->host_port;
			$schedule_hosts['host_username'] = $get_schedule_details[0]->host_username;
			$schedule_hosts['host_password'] = $get_schedule_details[0]->host_password;
			$schedule_hosts['host_path'] = $get_schedule_details[0]->host_path;
			$schedule_hosts['connection_type'] = $get_schedule_details[0]->exportbymethod;

			$already_mapped['schedule_details'] = $scheduled_details;
			$already_mapped['schedule_hosts'] = $schedule_hosts;
		}
		else{
			$already_mapped['is_schedule'] = false;
		}

		$response['success'] = true;
		$response['already_mapped'] = $already_mapped;
		echo wp_json_encode($response);
		wp_die();
	}

}