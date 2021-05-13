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
 * Class ScheduleExport
 * @package Smackcoders\WCSV
 */

class ScheduleExport {

	private static $instance=null;
	protected static $smackcsv_instance = null;
	protected static $core = null,$export_instance;

	public static  function getInstance() {
		if (ScheduleExport::$instance == null) {
			self::$instance = new self;
			ScheduleExport::$export_instance = ExportExtension::getInstance();
			self::$instance->smack_uci_cron_scheduled_export();
			return self::$instance;
		}
		self::$instance->doHooks();
		return self::$instance;
	}

	public  function doHooks(){
		add_action('wp_ajax_parseDataToScheduleExport', array(self::$instance, 'parseDataToScheduleExport'));
	}

	public static function parseDataToScheduleExport() {
		global $wpdb;
		$currentUser = wp_get_current_user();
		$schedulerId = $currentUser->ID;
		$currentDate = current_time('mysql', 0);
		$time_zone = $_POST['UTC'];
		if(empty($time_zone)){
			$time_zone = 'Asia/Kolkata';
		}
		$nextRun = sanitize_text_field($_POST['date']) . ' ' . (sanitize_text_field($_POST['schedule_time']));
		
		$export_schedule_method = $_POST['method'];
		$conditions = str_replace("\\" , '' , $_POST['conditions']);

		$exp_conditions = json_decode($conditions, True);
		$exp_conditions = serialize($exp_conditions);

		$eventExclusions = str_replace("\\" , '' , $_POST['eventExclusions']);

		$exp_eventExclusions = json_decode($eventExclusions, True);
		$exp_eventExclusions = serialize($exp_eventExclusions);

		switch ($_POST['schedule_frequency']) {
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
			default:
				$frequency = 0;
				break;
		}

		$schedule_file_name = $_POST['fileName'];
		$schedule_module_name = $_POST['module'];
		$schedule_optional_name = $_POST['optionalType'];

		if(empty($schedule_optional_name)){
			$check_for_existing_schedule = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}ultimate_csv_importer_scheduled_export WHERE file_name = '$schedule_file_name' AND module = '$schedule_module_name' ");
			$check_for_existing_template = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE filename = '$schedule_file_name' AND module = '$schedule_module_name' ");
		}
		else{
			$check_for_existing_schedule = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}ultimate_csv_importer_scheduled_export WHERE file_name = '$schedule_file_name' AND module = '$schedule_module_name' AND optionalType = '$schedule_optional_name' ");
			$check_for_existing_template = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE filename = '$schedule_file_name' AND module = '$schedule_module_name' AND optional_type = '$schedule_optional_name' ");
		}

		if(empty($check_for_existing_schedule)){
			$wpdb->insert($wpdb->prefix.'ultimate_csv_importer_scheduled_export',
				array(
					'module' => $_POST['module'],
					'export_mode' => 'Schedule',
					'optionalType' => $_POST['optionalType'],
					'conditions' => $conditions,
					'exclusions' => $eventExclusions,
					'file_name' => $_POST['fileName'],
					'scheduleddate' => $_POST['date'],
					'frequency' => $frequency,
					'exportbymethod' => $export_schedule_method,
					'scheduledtimetorun' => $_POST['schedule_time'],
					'host_port' => $_POST['host_port'],
					'host_name' => $_POST['host_name'],
					'host_username' => $_POST['host_username'],
					'host_password' => $_POST['host_password'],
					'host_path' => $_POST['host_path'],
					'file_type' => 'csv',
					'nexrun' => $nextRun,
					'scheduled_by_user' => $schedulerId,
					'createdtime' => $currentDate,
					'time_zone' => $time_zone
				),
				array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
			);
		}
		else{
			$id = $check_for_existing_schedule[0]->id;

			$wpdb->update($wpdb->prefix.'ultimate_csv_importer_scheduled_export',
				array(
					'export_mode' => 'Schedule',
					'conditions' => $conditions,
					'exclusions' => $eventExclusions,
					'scheduleddate' => $_POST['date'],
					'frequency' => $frequency,
					'exportbymethod' => $export_schedule_method,
					'scheduledtimetorun' => $_POST['schedule_time'],
					'host_port' => $_POST['host_port'],
					'host_name' => $_POST['host_name'],
					'host_username' => $_POST['host_username'],
					'host_password' => $_POST['host_password'],
					'host_path' => $_POST['host_path'],
					'file_type' => 'csv',
					'nexrun' => $nextRun,
					'scheduled_by_user' => $schedulerId,
					'createdtime' => $currentDate,
					'time_zone' => $time_zone
				),
				array( 'id' => $id )
			);
		}

		if(empty($check_for_existing_template)){
			$wpdb->insert($wpdb->prefix.'ultimate_csv_importer_export_template',
				array('filename' => $schedule_file_name,
					'module' => $_POST['module'],
					'optional_type' => $_POST['optionalType'],
					'export_type' => $_POST['exp_type'],
					'split' => $_POST['is_check_split'],
					'split_limit' => $_POST['limit'],
					'category_name' => $_POST['categoryName'],
					'conditions' => $exp_conditions,
					'event_exclusions' => $exp_eventExclusions,
					'export_mode' => 'schedule',
					'createdtime' => $currentDate,
					'offset' => $_POST['offset'],
					'actual_start_date' => $_POST['actual_start_date'],
					'actual_end_date' => $_POST['actual_end_date'],
					'actual_schedule_date' => $_POST['actual_schedule_date']
				),
				array('%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
			);
		}
		else{
			$id = $check_for_existing_template[0]->id;

			$wpdb->update( 
				$wpdb->prefix.'ultimate_csv_importer_export_template', 
				array(
					'export_type' => $_POST['exp_type'],
					'split' => $_POST['is_check_split'],
					'split_limit' => $_POST['limit'],
					'category_name' => $_POST['categoryName'],
					'conditions' => $exp_conditions,
					'event_exclusions' => $exp_eventExclusions,
					'export_mode' => 'schedule',
					'createdtime' => $currentDate,
					'offset' => $_POST['offset'],
					'actual_start_date' => $_POST['actual_start_date'],
					'actual_end_date' => $_POST['actual_end_date'],
					'actual_schedule_date' => $_POST['actual_schedule_date']
				),
				array( 'id' => $id )
			);
		}

		echo  wp_json_encode(array('msg' => 'Export scheduled successfully!'));
		wp_die();
	}

	public  function smack_uci_cron_scheduled_export() {
		if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) {
			return false;
		}	
		global $wpdb;
		global $scheduleObj;
		$endDate = '';
		$schedule_tableName = $wpdb->prefix.'ultimate_csv_importer_scheduled_export';
		$proceed_scheduling = 1;
		$nextDate = null;
		$timeZone = $wpdb->get_results("select * from $schedule_tableName where isrun = 0 ");
		if(!empty($timeZone)){
			$date = new \DateTime('now', new \DateTimeZone($timeZone[0]->time_zone));
			$current_timestamp=$date->format('Y-m-d H:i:s');
			$scheduleList = $wpdb->get_results("select * from $schedule_tableName where isrun = 0 and nexrun <= '$current_timestamp'");
			/****************** Generate Schedule Data *******************/
			if (!empty($scheduleList)) {
				foreach ($scheduleList as $scheduledEvent) {
					$runSchedule = false;
					$data = array();
					$frequency = $scheduledEvent->frequency;
					$startDate = strtotime($scheduledEvent->lastrun);
					$startDate = $scheduledEvent->scheduleddate . ' ' . $scheduledEvent->scheduledtimetorun;
					if($frequency == 0) {
						$nextDate = date("Y-m-d H:i:s", $startDate);
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
					}
					elseif ($frequency == 2) {   // Weekly
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
					}
					elseif ($frequency == 3) {   // Monthly
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
					}
					elseif ($frequency == 6) {
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
					}
					elseif ($frequency == 7) {
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
					}
					elseif ($frequency == 8) {	
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
					}
					/****************** Read Schedule File *****************/
					if ($runSchedule === true) {
						if($scheduledEvent->cron_status != 'initialized') {
							$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_export set cron_status = 'initialized' where id = '{$scheduledEvent->id}'" );
						}
						ScheduleExport::$export_instance->module = $scheduledEvent->module;
						ScheduleExport::$export_instance->exportType  = $scheduledEvent->file_type;
						$get_conditions = json_decode($scheduledEvent->conditions);
						if(!empty($get_conditions)) {
							foreach ( $get_conditions as $index => $condObj ) {
								if(!empty($condObj)) {
									foreach ( $condObj as $key => $value ) {
										$conditions[ $index ][ $key ] = $value;
									}
								}
							}
						}
						ScheduleExport::$export_instance->conditions  = $conditions;
						ScheduleExport::$export_instance->optionalType = $scheduledEvent->optionalType;
						$get_exclusions = json_decode($scheduledEvent->exclusions,true);
						if(!empty($get_exclusions)) {
							foreach ( $get_exclusions as $index => $exclusionObj ) {
								if(is_array($exclusionObj) && !empty($exclusionObj)) {
									foreach ( $exclusionObj as $key => $value ) {
										$exclusions[ $index ][ $key ] = $value;
									}
								} else {
									$exclusions[ $index ] = $exclusionObj;
								}
							}
						}

						ScheduleExport::$export_instance->eventExclusions = $exclusions;
						ScheduleExport::$export_instance->fileName = $scheduledEvent->file_name;
						ScheduleExport::$export_instance->offset   = $scheduledEvent->start_limit;
						ScheduleExport::$export_instance->limit    = $scheduledEvent->end_limit;
						ScheduleExport::$export_instance->export_mode = 'Schedule';
						ScheduleExport::$export_instance->delimiter = ScheduleExport::$export_instance->setDelimiter(',');
						ScheduleExport::$export_instance->exportData();
						$result = ScheduleExport::$export_instance->export_log;
						$startLimit = $result['new_offset'] + 1;
						if($startLimit > $result['total_row_count'] && $scheduledEvent->frequency == 0) {
							$wpdb->query("update {$wpdb->prefix}ultimate_csv_importer_scheduled_export set isrun = 1 where id = $scheduledEvent->id");
						}
						if($startLimit > $result['total_row_count']) {
							$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_export set start_limit = 0, end_limit = '{$scheduledEvent->end_limit}', lastrun = '{$current_timestamp}',nexrun = '{$nextRun}', cron_status = 'completed' where id = $scheduledEvent->id" );
							/** Send exported file to the FTP,SFTP location **/
							self::remoteExport($scheduledEvent, $result);
						} else {
							$wpdb->query( "update {$wpdb->prefix}ultimate_csv_importer_scheduled_export set start_limit = '{$startLimit}', lastrun = '{$current_timestamp}' where id = '{$scheduledEvent->id}'" );
						}
					}
					/***************** End Read of Schedule file ****************/
				}
			}
		}
		/************** End Schedule Data Generation **************/
	}

	public  function remoteExport($scheduledEvent, $result) {
		global $wpdb;
		$offset = get_option('gmt_offset');
		list($hours, $minutes) = explode(':', $offset);
		$seconds = $hours * 60 * 60 + $minutes * 60;
		$tz = timezone_name_from_abbr('', $seconds, 1);
		if($tz === false) $tz = timezone_name_from_abbr('', $seconds, 0);	
		$datetime = new \DateTime($scheduledEvent->scheduleddate .' '.$scheduledEvent->scheduledtimetorun);
		$zone_time = new \DateTimeZone($tz);
		$datetime->setTimezone($zone_time);
		$admin_scheduled_date = $datetime->format('Y-m-d H:i:s');
		$local_file = $result['exported_path'];
		$schedule_tableName = $wpdb->prefix.'ultimate_csv_importer_scheduled_export';
		try {
			$this->sendExportedFileToRemoteLocation($scheduledEvent,$local_file);
		} catch (\Exception $e) {
			$wpdb->query( "update $schedule_tableName set cron_status = 'failed' where id = '{$scheduledEvent->id}'" );
		}
		// Send notification after uploading the file into FTP location
		$ucisettings = get_option('sm_uci_pro_settings');
		if(isset($ucisettings['send_log_email']) && $ucisettings['send_log_email'] == 'true') {
			require_once(ABSPATH . "wp-includes/pluggable.php");
			$user_info = get_userdata($scheduledEvent->scheduled_by_user);
			$admin_email = $user_info->user_email;
			$subject = "Scheduled export done!";
			$message = "Hi " . $user_info->data->display_name . ',' . "\r\n";
			$message .= "$subject" . "\r\n";
			$message .= "Please check the exported file on your FTP location." . "\r\n";
			$message .= "Filename: {$remote_file}";
			$message .= 'Scheduled file based on your wp-admin timezone with time:'.$admin_scheduled_date;
			$headers = array();
			$headers[] = "From: {$user_info->data->display_name} <{$user_info->user_email}>" . "\r\n";
			$attachments = array();
			$res =  wp_mail( $admin_email, $subject, $message, $headers, $attachments );
		}
	}

	public function sendExportedFileToRemoteLocation($scheduledEvent,$local_file){
		global $wpdb;
		$server = $scheduledEvent->host_name;
		$username = $scheduledEvent->host_username;
		$password = $scheduledEvent->host_password;
		$port = $scheduledEvent->host_port;
		$path = $scheduledEvent->host_path;
		$schedule_method = $scheduledEvent->exportbymethod;
		$remote_file = $path . $scheduledEvent->file_name . '.' . $scheduledEvent->file_type;
		$schedule_tableName = $wpdb->prefix.'ultimate_csv_importer_scheduled_export';	
		if($schedule_method == 'sftp'){
		$connection = ssh2_connect($server, $port);
	
		ssh2_auth_password($connection, $username, $password);
		$sftp = ssh2_sftp($connection);
	
		$stream = fopen("ssh2.sftp://$sftp$remote_file", 'w');
	 
		$file = file_get_contents($local_file);	
 
		fwrite($stream, $file);
		fclose($stream);
	}
 	elseif($schedule_method == 'ftp') {	
			$ftp_conn = ftp_connect($server);
			if ( $ftp_conn ) {
				$login = ftp_login($ftp_conn, $username, $password);
				ftp_pasv($ftp_conn, true);
				ftp_put($ftp_conn, $remote_file, $local_file, FTP_BINARY);
			} else {
				$wpdb->query( "update $schedule_tableName set cron_status = 'failed' where id = '{$scheduledEvent->id}'" );
				throw new Exception( "Could not connect to " . $server );
			}
			ftp_close($ftp_conn);
		}

	}

}
