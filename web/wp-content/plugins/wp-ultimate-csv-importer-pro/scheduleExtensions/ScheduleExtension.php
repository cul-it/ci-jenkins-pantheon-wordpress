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
 * Class ScheduleExtension
 * @package Smackcoders\WCSV
 */

class ScheduleExtension {

	private static $instance=null;
	private static $smackcsv_instance = null,$save_mapping,$ftp_upload,$schedule_import = null,$smack_instance, $url_upload , $sftp_upload = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$save_mapping = SaveMapping::getInstance();
			self::$ftp_upload = FtpUpload::getInstance();
			self::$schedule_import = ScheduleImport::getInstance();
			self::$url_upload = UrlUpload::getInstance();
			self::$sftp_upload = SftpUpload::getInstance();
			self::$instance->doHooks();
			//self::$instance->smack_uci_cron_scheduler();
		}
		return self::$instance;
	}

	public  function doHooks(){
		add_action('wp_ajax_save_schedule_info',array($this,'saveEventInformationToSchedule'));
		add_action('wp_ajax_timezone' ,array($this,'timezone'));
	}	

	/**
	 * ScheduleExtension constructor.
	 * Set values into global variables based on post value
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();	
	}

	public static function parseDataToExport() {
		global $wpdb;
		require_once ('class-uci-exporter.php');
		die();
	}

	public function saveEventInformationToSchedule() {
		global $wpdb;
		$schedule_table = $wpdb->prefix.'ultimate_csv_importer_scheduled_import';
		$eventKey = sanitize_key($_POST['eventkey']);
		$import_mode = 'Schedule';
		$import_module = $_POST['module'];
		$file_type = $_POST['FileType'];
		$import_method = $_POST['method'];
		$time_zone = $_POST['UTC'];
		if(empty($time_zone)){
			$time_zone = 'Asia/Kolkata';
		}
		$templateId = $this->getTemplateInformation($eventKey);
		if(isset($_POST['configData']['limit'])){
			$import_limit = sanitize_text_field($_POST['configData']['limit']);
		}else{
			$import_limit = 1;
		}

		if(isset($_POST['configData']['offset'])){
			$scheduleRows = sanitize_text_field($_POST['configData']['offset']);
		}else{
			$scheduleRows = '';
		}

		if(isset($_POST['UpdateUsing'])){
        	update_option('csv_importer_update_using', $_POST['UpdateUsing']);
		}
			
		$duplicateHeaders = $_POST['duplicate_header'];
		switch (sanitize_text_field(($_POST['frequency']))) {
			case 'OneTime':
				$frequency = 0;
				break;
			case 'Daily':
				$frequency = 1;
				break;
			case 'Weekly':
				$frequency = 2;
				break;
			case 'Monthly':
				$frequency = 3;
				break;
			case 'Hourly':
				$frequency = 4;
				break;
			case 'Every 30 mins':
				$frequency = 5;
				break;
			case 'Every 15 mins':
				$frequency = 6;
				break;
			case 'Every 10 mins':
				$frequency = 7;
				break;
			case 'Every 5 mins':
				$frequency = 8;
				break;
			case 'Every 2 hours':
				$frequency = 9;
			    break;
			case 'Every 4 hours':
				$frequency = 10;
			    break;
		}
		$nextRun = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['date']) . ' ' . (sanitize_text_field($_POST['Time']))));
		$currentDate = current_time('mysql', 0);
		$currentUser = wp_get_current_user();
		$eventSchedulerId = $currentUser->ID; // Get current user id
		$timestamp = strtotime($_POST['date']);
		$dbdate = date("Y-m-d", $timestamp);
		$wpdb->insert($schedule_table,
				array('templateid' => $templateId,
					'createdtime' => $currentDate,
					'scheduledtimetorun' => sanitize_text_field($_POST['Time']),
					'scheduleddate' => $dbdate,
					'module'	=> $import_module,
					'file_type' => $file_type,
					'event_key'	=> $eventKey,
					'importbymethod' => $import_method,
					'import_limit'	=> $import_limit,
					'import_row_ids' => $scheduleRows,
					'frequency' => $frequency,
					'nexrun' => $nextRun,
					'scheduled_by_user' => $eventSchedulerId,
					'import_mode' => $import_mode,
					'duplicate_headers' => $duplicateHeaders,
					'time_zone' => $time_zone
				     ),
				array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%d', '%s','%d', '%s', '%s', '%s', '%s','%s')
				);
		if(!is_wp_error($wpdb->insert_id)) {
			if ($import_method == 'url') {
				$save_externalfile = "insert into {$wpdb->prefix}ultimate_csv_importer_external_file_schedules (schedule_id, file_url, filename) values ('{$wpdb->insert_id}', '{$_POST['fileurl']}', '{$eventKey}')";
				$wpdb->query($save_externalfile);

			} else {
				if ($import_method == 'ftp' || $import_method == 'ftps' || $import_method == 'sftp') {
					# Save ftp file details
					$save_ftpfile = "insert into {$wpdb->prefix}ultimate_csv_importer_ftp_schedules (schedule_id, hostname, username, password, initial_path, filename, port_no) values ('{$wpdb->insert_id}', '{$_POST['hostname']}', '{$_POST['username']}', '{$_POST['password']}', '{$_POST['initialpath']}', '{$eventKey}' , '{$_POST['portnumber']}')";
					$wpdb->query($save_ftpfile);
				}
			}
			/**************** Store Schedule Order ***************/
			$newSchedule_data = array();
			$getSchedule_data = array();
			$getSchedule_data = get_option('WP_CSV_IMPORT_SCHEDULE_ORDER');
			$newSchedule_data[$wpdb->insert_id]['scheduled_order']['scheduled_id'] = $wpdb->insert_id;
			$newSchedule_data[$wpdb->insert_id]['scheduled_order']['module'] = $import_module;
			if (!is_array($getSchedule_data)) {
				$getSchedule_data = $newSchedule_data;
			} else {
				$scheduleList = array();
				foreach ($getSchedule_data as $schedule_key => $schedule_val) {
					$scheduleList[$schedule_key] = $schedule_val;
					foreach ($newSchedule_data as $new_schedule_key => $new_schedule_val) {
						$scheduleList[$new_schedule_key] = $new_schedule_val;
					}
				}
				$getSchedule_data = $scheduleList;
			}

			update_option('WP_CSV_IMPORT_SCHEDULE_ORDER', $getSchedule_data);
			/*************** End Schedule Order *************/
			$data['notification'] = 'Scheduled CSV successfully'
				;
		} else {
			$data['notification'] = 'Error while inserting into table';
		}
		$data = str_replace('\\', '',$data);
		echo wp_json_encode($data);
		wp_die();
	}

	public  function timezone(){
		static $regions = array(
				\DateTimeZone::AFRICA,
				\DateTimeZone::AMERICA,
				\DateTimeZone::ANTARCTICA,
				\DateTimeZone::ASIA,
				\DateTimeZone::ATLANTIC,
				\DateTimeZone::AUSTRALIA,
				\DateTimeZone::EUROPE,
				\DateTimeZone::INDIAN,
				\DateTimeZone::PACIFIC,
				);
		$timezones = array();
		foreach( $regions as $region )
		{
			$timezones = array_merge( $timezones, \DateTimeZone::listIdentifiers( $region ) );
		}

		$timezone_offsets = array();
		foreach( $timezones as $timezone )
		{
			$tz = new \DateTimeZone($timezone);
			$timezone_offsets[$timezone] = $tz->getOffset(new \DateTime);
		}
		asort($timezone_offsets);
		$timezone_list = array();
		$i = 0;
		foreach( $timezone_offsets as $timezone => $offset )
		{
			$offset_prefix = $offset < 0 ? '-' : '+';
			$offset_formatted = gmdate( 'H:i', abs($offset) );
			$pretty_offset = "UTC${offset_prefix}${offset_formatted}";
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
			$result[$i]['timezone'] = $timezone ;
			$result[$i]['offset']= $timezone_list[$timezone];
			$i++;
		}
		echo  wp_json_encode($result);
		wp_die();
	}

	public  function smack_uci_cron_scheduler() {

		if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true ) {
			return false;
		}

		global $wpdb;
		$endDate = '';
		$schedule_table = $wpdb->prefix.'ultimate_csv_importer_scheduled_import';
		$exturl_schedule_table = $wpdb->prefix.'ultimate_csv_importer_external_file_schedules';
		$schedule_tableName = $wpdb->prefix.'ultimate_csv_importer_scheduled_import';
		$ftp_schedule_table = $wpdb->prefix.'ultimate_csv_importer_ftp_schedules';
		$file_table_name = $wpdb->prefix.'smackcsv_file_events';
		$proceed_scheduling = 1;
		$nextDate = null;
		$timeZone = $wpdb->get_results("select * from $schedule_tableName where isrun = 0 ");

		if(!empty($timeZone)){
			if(empty($timeZone[0]->time_zone) || $timeZone[0]->time_zone == 'NULL' ){
				$offset = get_option('gmt_offset');
				list($hours, $minutes) = explode(':', $offset);
				$seconds = $hours * 60 * 60 + $minutes * 60;
				// Get timezone name from seconds
				$tz = timezone_name_from_abbr('', $seconds, 1);
				// Workaround for bug #44780
				if($tz === false) $tz = timezone_name_from_abbr('', $seconds, 0);
				// Set timezone
				date_default_timezone_set($tz);
				$timeZone[0]->time_zone = $tz;
				$wpdb->query("update $schedule_tableName set time_zone = '{$timeZone[0]->time_zone}' where id = '{$timeZone[0]->id}'");
			}
			$date = new \DateTime('now', new \DateTimeZone($timeZone[0]->time_zone));
			$current_timestamp=$date->format('Y-m-d H:i:s');
			$scheduleList = $wpdb->get_results("select * from $schedule_tableName where isrun = 0 and nexrun <= '$current_timestamp'");
			
		}
		if (!empty($scheduleList)) {
			$date = new \DateTime('now', new \DateTimeZone($timeZone[0]->time_zone));
			$current_timestamp=$date->format('Y-m-d H:i:s');
			foreach ($scheduleList as $scheduledEvent) {
				$wpdb->query("update $schedule_tableName set isrun = 1 where id = '{$scheduledEvent->id}'");
				$templateid=$scheduledEvent->templateid;
				$runSchedule = false;
				$data = array();
				$frequency = $scheduledEvent->frequency;
				$startDate = strtotime($scheduledEvent->lastrun);
				$startDate = $scheduledEvent->scheduleddate . ' ' . $scheduledEvent->scheduledtimetorun;
				$startDate = strtotime($startDate);
				if($frequency == 0) {
					$nextDate =  date("Y-m-d H:i:s", $startDate);
					if($nextDate <= $current_timestamp){
						$runSchedule = true;
					}
					$nextRun = $nextDate;
				}
				elseif ($frequency == 1) {          // Daily
					$endDate = strtotime("+1 day", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+1 day", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+1 day", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} elseif ($frequency == 2) {   // Weekly
					$endDate = strtotime("+1 week", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+1 week", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+1 week", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} elseif ($frequency == 3) {   // Monthly
					$endDate = strtotime("+1 month", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+1 month", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+1 month", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				}
				elseif ($frequency == 4) {   // Hourly
					$endDate = strtotime("+1 hour", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+1 hour", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+1 hour", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				}
				elseif ($frequency == 5) {
					$endDate = strtotime("+30 minutes", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+30 minutes", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+30 minutes", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} elseif ($frequency == 6) {
					$endDate = strtotime("+15 minutes", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+15 minutes", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+15 minutes", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} elseif ($frequency == 7) {
					$endDate = strtotime("+10 minutes", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+10 minutes", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+10 minutes", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} elseif ($frequency == 8) {
					$endDate = strtotime("+5 minutes", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+5 minutes", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+5 minutes", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} elseif ($frequency == 9) {   //Every 2 hours
					$endDate = strtotime("+120 minutes", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+120 minutes", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+120 minutes", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} elseif ($frequency == 10) {   //Every 4 hours
					$endDate = strtotime("+240 minutes", $startDate);
					$nextDate = date("Y-m-d H:i:s", $endDate);
					if($nextDate <= $current_timestamp) {
						$runSchedule = true;
					}
					$nextDate = strtotime($current_timestamp);
					$nextRun = strtotime("+240 minutes", $nextDate);
					$nextRun = date("Y-m-d H:i:s", $nextRun);
					if($nextRun <= $current_timestamp) {
						$nextRun = strtotime("+240 minutes", $current_timestamp);
						$nextRun = date("Y-m-d H:i:s", $nextRun);
					}
				} 
				$hashkey = $wpdb->get_results("select eventkey from {$wpdb->prefix}ultimate_csv_importer_mappingtemplate where id = $templateid");
				$array=json_decode(json_encode($hashkey),true);
				$hashkeys=$array[0]['eventkey'];
				$data['start_limit'] = $scheduledEvent->start_limit;
				$data['end_limit'] = $scheduledEvent->end_limit;
				$data['eventkey'] = $scheduledEvent->event_key;
				$data1['eventkey'] = $hashkeys;
				$data['import_limit'] = $scheduledEvent->import_limit;
				$data['import_row_ids'] = !empty($scheduledEvent->import_row_ids) ? unserialize($scheduledEvent->import_row_ids) : '';
				$data['nexrun'] = $nextRun;
				$data['lastrun'] = $current_timestamp;
				$data['frequency'] = $scheduledEvent->frequency;
				$data['module'] = $scheduledEvent->module;
				$data['duplicate_headers'] = $scheduledEvent->duplicate_headers;
				$data['extension'] = $scheduledEvent->file_type;
				$data['import_mode'] = $scheduledEvent->import_mode;
				$data['csv_name'] = $scheduledEvent->event_key;
				$data['scheduled_by_user'] = $scheduledEvent->scheduled_by_user;
				$data['template_id'] = $scheduledEvent->templateid;
				$data['id'] = $scheduledEvent->id;
				$runSchedule = true ;
				if ($runSchedule === true && $scheduledEvent->importbymethod == 'url') {
					$external_scheduleList = $wpdb->get_results("select filename, file_url from {$wpdb->prefix}ultimate_csv_importer_external_file_schedules where schedule_id = $scheduledEvent->id");

					if ($external_scheduleList[0]->filename != '') {
						$external_url = $external_scheduleList[0]->file_url;
						$returnData = self::$url_upload->externalfile_handling($external_url);
						if ($returnData['success'] === false) {
							$proceed_scheduling = 0;
							$wpdb->get_results("UPDATE $schedule_tableName SET  cron_status = 'Failed' WHERE event_key = '{$data['eventkey']}'");
						} else {
							$data['csv_name'] = $returnData['hashkey'];
							$data['filename'] = $returnData['filename'];
							$data['uploaded_name'] = $returnData['filename'];
							$data['version'] = $returnData['version'];
							$data['extension'] = $returnData['extension'];
							$wpdb->query("update {$exturl_schedule_table} set filename = '{$returnData['hashkey']}' where schedule_id = '{$scheduledEvent->id}'");
							$template_info = $wpdb->get_results("select mapping , module from {$wpdb->prefix}ultimate_csv_importer_mappingtemplate where eventKey = '{$data1['eventkey'] }'",ARRAY_A);
							$wpdb->query("UPDATE $schedule_table SET mapping = '$template_info[0]['mapping']' WHERE event_key = '".$data['eventkey']."'");
							$save_template = "insert into {$wpdb->prefix}ultimate_csv_importer_mappingtemplate (mapping ,module,eventKey) values('{$template_info[0]['mapping']}','{$template_info[0]['module']}' , '{$data['csv_name']}' )";
							$wpdb->query($save_template);
							$wpdb->query("UPDATE $file_table_name SET hash_key = '".$returnData['hashkey']."' WHERE hash_key = '".$data['eventkey']."'");
							//$wpdb->query("UPDATE $schedule_table SET hash_key = '".$returnData['hashkey']."' WHERE hash_key = '".$data['eventkey']."'");
							$data['eventkey'] = $returnData['hashkey'];
						    $wpdb->query("UPDATE $schedule_tableName SET event_key = '".$returnData['hashkey']."' WHERE ID = ".$scheduledEvent->id);
						}
					}
				}
				elseif ($runSchedule === true && $scheduledEvent->importbymethod == 'ftp') {
					$ftp_scheduleList = $wpdb->get_results("select * from {$wpdb->prefix}ultimate_csv_importer_ftp_schedules where schedule_id = $scheduledEvent->id");
					if ($ftp_scheduleList[0]->filename != '') {
						$data['HostName'] = $ftp_scheduleList[0]->hostname;
						$data['HostPort'] = $ftp_scheduleList[0]->port_no;
						$data['HostPath'] = $ftp_scheduleList[0]->initial_path;
						$data['HostUserName'] = $ftp_scheduleList[0]->username;
						$data['HostPassword'] = $ftp_scheduleList[0]->password;
						$returnData = self::$ftp_upload->ftp_upload($data);
						if ($returnData['success'] === false) {
							$proceed_scheduling = 0;
							$wpdb->get_results("UPDATE $schedule_tableName SET  cron_status = 'Failed' WHERE event_key = '{$data['eventkey']}'");
						} else {
							$data['csv_name'] = $returnData['hashkey'];
							$data['filename'] = $returnData['filename'];
							$data['uploaded_name'] = $returnData['filename'];
							$wpdb->query("update {$ftp_schedule_table} set filename = '{$returnData['hashkey']}' where schedule_id = '{$scheduledEvent->id}'");
							$template_info = $wpdb->get_results("select mapping , module from {$wpdb->prefix}ultimate_csv_importer_mappingtemplate where eventKey = '{$data['eventkey']}'",ARRAY_A);
							$save_template = "insert into {$wpdb->prefix}ultimate_csv_importer_mappingtemplate (mapping ,module,eventKey) values('{$template_info[0]['mapping']}','{$template_info[0]['module']}' , '{$data['csv_name']}' )";
							$wpdb->query($save_template);
							
						//	$wpdb->query("UPDATE $file_table_name SET hash_key = '".$returnData['hashkey']."' WHERE hash_key = '".$data['eventkey']."'");
							$data['eventkey'] = $returnData['hashkey'];
							$wpdb->query("UPDATE $schedule_tableName SET event_key = '".$returnData['hashkey']."' WHERE ID = ".$scheduledEvent->id);
						}
					}
				}

				elseif ($runSchedule === true && $scheduledEvent->importbymethod == 'sftp') {
					$ftp_scheduleList = $wpdb->get_results("select * from {$wpdb->prefix}ultimate_csv_importer_ftp_schedules where schedule_id = $scheduledEvent->id");
					if ($ftp_scheduleList[0]->filename != '') {
						$data['HostName'] = $ftp_scheduleList[0]->hostname;
						$data['HostPort'] = $ftp_scheduleList[0]->port_no;
						$data['HostPath'] = $ftp_scheduleList[0]->initial_path;
						$data['HostUserName'] = $ftp_scheduleList[0]->username;
						$data['HostPassword'] = $ftp_scheduleList[0]->password;
						$returnData = self::$sftp_upload->sftp_upload($data);
						if ($returnData['success'] === false) {
							$proceed_scheduling = 0;
							$wpdb->get_results("UPDATE $schedule_tableName SET  cron_status = 'Failed' WHERE event_key = '{$data['eventkey']}'");
						} else {
							$data['csv_name'] = $returnData['hashkey'];
							$data['filename'] = $returnData['filename'];
							$data['uploaded_name'] = $returnData['filename'];
							$wpdb->query("update {$ftp_schedule_table} set filename = '{$returnData['hashkey']}' where schedule_id = '{$scheduledEvent->id}'");
							$template_info = $wpdb->get_results("select mapping , module from {$wpdb->prefix}ultimate_csv_importer_mappingtemplate where eventKey = '{$data['eventkey']}'",ARRAY_A);
							$save_template = "insert into {$wpdb->prefix}ultimate_csv_importer_mappingtemplate (mapping ,module,eventKey) values('{$template_info[0]['mapping']}','{$template_info[0]['module']}' , '{$data['csv_name']}' )";
							$wpdb->query($save_template);

							$wpdb->query("UPDATE $file_table_name SET hash_key = '".$returnData['hashkey']."' WHERE hash_key = '".$data['eventkey']."'");
							$data['eventkey'] = $returnData['hashkey'];
							$wpdb->query("UPDATE $schedule_tableName SET event_key = '".$returnData['hashkey']."' WHERE ID = ".$scheduledEvent->id);
						}
					}
				}

				if ($proceed_scheduling == 1 && $runSchedule === true) {
					self::doSchedule($data,$current_timestamp);
				}
			}
		}
	}

	public  function get_wordpress_currentdate($type, $gmt = 0) {
		$date = array();
		$time = current_time($type, $gmt);
		$date['timstamp'] = $time;
		$date['date'] = date('Y-m-d', strtotime($time));
		$date['time'] = date('H:i', strtotime($time));
		$date['day'] = date('l', strtotime($time));
		$date['datetime'] = date('Y-m-d H:i:s', strtotime($time));
		return $date;
	}

	public static function getTemplateInformation($eventKey) {
		global $wpdb;
		$templateid = $wpdb->get_col($wpdb->prepare("select id from {$wpdb->prefix}ultimate_csv_importer_mappingtemplate where eventKey = %s",$eventKey));
		$template_id = $templateid[0];
		return $template_id;
	}

	public  function doSchedule($data,$current_timestamp) {
		global $wpdb;
		$offset = get_option('gmt_offset');
		list($hours, $minutes) = explode(':', $offset);
		$seconds = $hours * 60 * 60 + $minutes * 60;
		$tz = timezone_name_from_abbr('', $seconds, 1);
		if($tz === false) $tz = timezone_name_from_abbr('', $seconds, 0);	
		$datetime = new \DateTime($data->scheduleddate .' '.$data->scheduledtimetorun);
		$zone_time = new \DateTimeZone($tz);
		$datetime->setTimezone($zone_time);
		$admin_scheduled_date = $datetime->format('Y-m-d H:i:s');
		$schedule_tableName = $wpdb->prefix.'ultimate_csv_importer_scheduled_import';
		$offset = '';
		$getScheduling_data = $wpdb->get_results("select cron_status, importbymethod, duplicate_headers from $schedule_tableName where id = {$data['id']}");
		if ($getScheduling_data[0]->cron_status == 'initialized') {
		
		} else {
			$wpdb->query("update $schedule_tableName set cron_status = 'initialized' where id = '{$data['id']}'");
		}
		if ($getScheduling_data[0]->duplicate_headers) {
			$duplicate_headers = unserialize($getScheduling_data[0]->duplicate_headers);
			$duplicate_headers = trim($duplicate_headers);
		}
		$original_file_name = $data['filename'];
		$test = new ScheduleImport();
		$test->schedule_import($data);

		/**************** End Scheduling process ***************/
		$smack_instance = SmackCSV::getInstance();
		$upload_dir = $smack_instance->create_upload_dir();
		$hash_key = $data['eventkey'];
		$eventLogFile = $upload_dir.$hash_key.'/'.$hash_key.'.html';
		$ucisettings = get_option('sm_uci_pro_settings');
		if(isset($ucisettings['send_log_email']) && $ucisettings['send_log_email'] == 'true') {
			require_once(ABSPATH . "wp-includes/pluggable.php");
			$user_info = get_userdata($data['scheduled_by_user']);
			$first_name = $user_info->first_name;
			$last_name = $user_info->last_name;
			$recievermail = $first_name . ' ' . $last_name . "<$user_info->user_email>";
			$subject = "Schedule Log for schedule_id: {$data['id']} & filename:$original_file_name";
			$message = "$subject";
			$message .= 'Scheduled file based on your wp-admin timezone with time:'.$admin_scheduled_date;
			$headers = array();
			$headers[] = "From: $user_info->display_name <$user_info->user_email>" . "\r\n";
			$attachments = array ( $eventLogFile );
			wp_mail( $recievermail, $subject, $message, $headers, $attachments );
		}
		if($data['frequency'] != 0) {
			//$wpdb->query("update $schedule_tableName set isrun = 0 where id = '{$data['id']}'");
			$data_id = $data['id'];
			$wpdb->query("update $schedule_tableName set isrun = 0 where id = $data_id ");
		}

		// check if schedule is completed
		//$data_id = $data['id'];
		// $check_schedule_complete = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}ultimate_csv_importer_scheduled_import WHERE cron_status = 'completed' AND isrun = 1 AND id = $data_id");
		// if(!empty($check_schedule_complete)){
		// 	$timestamp = wp_next_scheduled( 'cron_schedule_function' );
		// 	wp_unschedule_event( $timestamp, 'cron_schedule_function' );
		// }
	}
}

