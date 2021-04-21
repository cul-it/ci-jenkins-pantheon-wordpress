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

class ScheduleManager {

    private static $instance = null;
    private static $smack_csv_instance = null;

    public function __construct(){
        add_action('wp_ajax_display_schedule',array($this,'display_schedule'));
        add_action('wp_ajax_delete_schedule',array($this,'delete_schedule'));
        add_action('wp_ajax_edit_schedule',array($this,'edit_schedule'));
        add_action('wp_ajax_update_schedule',array($this,'update_schedule'));
    }

    public static function getInstance() {
		if (ScheduleManager::$instance == null) {
			ScheduleManager::$instance = new ScheduleManager;
            ScheduleManager::$smack_csv_instance = SmackCSV::getInstance();
			return ScheduleManager::$instance;
		}
		return ScheduleManager::$instance;
    }


    /**
	 * Displays Scheduled files history.
	 */
    public function display_schedule(){
        $type = $_POST['Type'];
        $details = [];
        $info = [];
        $response = [];

        if($type == 'Import'){
            $scheduleList = $this->get_scheduleData();
        }else{
            $scheduleList = $this->get_scheduleExportData();
        }
        
        if(!empty($scheduleList)) {
            foreach($scheduleList as $schedule_data) {
               
                if($type == 'Import'){
                    $details['filename'] = $schedule_data->csvname;
                }else{
                    $details['filename'] = $schedule_data->file_name;
                }
                
                $details['module'] = $schedule_data->module;
                $details['scheduled_date'] = $schedule_data->scheduleddate;
                $details['scheduled_time'] = $schedule_data->scheduledtimetorun;
                $details['status'] = $schedule_data->cron_status;
                $details['created_time'] = $schedule_data->createdtime;
                $details['frequency'] = $schedule_data->frequency;
                if($details['frequency'] != 0){
                    $details['last_run'] = $schedule_data->lastrun;
                    $details['next_run'] = $schedule_data->nexrun;
                }
                array_push($info , $details);
            }
            $response['success'] = true;
            $response['info'] = $info;
        }else{
            $response['success'] = false;
            $response['message'] = "You haven’t scheduled any event";
        }
        echo wp_json_encode($response);
        wp_die();

    }


    /**
	 * Retrieves exported schedule files from database.
	 * @return array
	 */
    public function get_scheduleExportData() {
        global $wpdb;
        $schedule_export_table = $wpdb->prefix . "ultimate_csv_importer_scheduled_export";
        $schedule_data = $wpdb->get_results("select schedule_table.id, schedule_table.file_name, schedule_table.file_type, schedule_table.createdtime, schedule_table.scheduledtimetorun,  schedule_table.frequency, schedule_table.scheduleddate, schedule_table.module, schedule_table.cron_status from $schedule_export_table schedule_table order by id desc");
        return $schedule_data;
    }


    /**
	 * Retrieves imported schedule files from database.
	 * @return array
	 */
    public function get_scheduleData() {
        global $wpdb;
        $schedule_import_table = $wpdb->prefix . "ultimate_csv_importer_scheduled_import";
        $mapping_table = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";
        $schedule_data = $wpdb->get_results("select template_table.csvname, template_table.templatename, template_table.eventKey, schedule_table.id, schedule_table.createdtime, schedule_table.frequency,  schedule_table.lastrun, schedule_table.nexrun, schedule_table.scheduledtimetorun, schedule_table.scheduleddate, schedule_table.module, schedule_table.cron_status from $schedule_import_table schedule_table, $mapping_table template_table where schedule_table.templateid = template_table.id order by id desc");
        return $schedule_data;
    }


    /**
	 * Deletes schedule files from database.
	 */
    public function delete_schedule(){
        global $wpdb;
        $schedule_message = [];
        $schedule_import_table = $wpdb->prefix ."ultimate_csv_importer_scheduled_import";
        $ftp_schedule_table = $wpdb->prefix . "ultimate_csv_importer_ftp_schedules";
        $url_schedule_table = $wpdb->prefix . "ultimate_csv_importer_external_file_schedules";
        $schedule_export_table = $wpdb->prefix ."ultimate_csv_importer_scheduled_export";

        $time = $_POST['CreatedTime'];
        $type = $_POST['Type'];

        if($type == 'Import'){
            $get_id = $wpdb->get_results("select id from $schedule_import_table where createdtime = '{$time}' ");
            $id = $get_id[0]->id;

            $delete_schedule = $wpdb->delete( $schedule_import_table , array( 'id' => $id ) );
            $delete_ftp_schedule = $wpdb->delete( $ftp_schedule_table , array( 'schedule_id' => $id ) );
            $delete_url_schedule = $wpdb->delete( $url_schedule_table , array( 'schedule_id' => $id ) );           


        }else{

            $get_id = $wpdb->get_results("select id from $schedule_export_table where createdtime = '{$time}' ");
            $id = $get_id[0]->id;

            $delete_schedule = $wpdb->delete( $schedule_export_table , array( 'id' => $id ) );
        }
       
        if($delete_schedule){
            $schedule_message['success'] = true;
            $schedule_message['message'] = "Deleted Successfully";
        }
        else {
            $schedule_message['success'] = false;
            $schedule_message['message'] = "Error Occurred While Deleting";
        }
        echo wp_json_encode($schedule_message);
        wp_die();
    }


    /**
	 * Edits schedule file data.
	 */
    public function edit_schedule(){
        global $wpdb;
        $schedule_message = [];

        $schedule_import_table = $wpdb->prefix ."ultimate_csv_importer_scheduled_import";
        $schedule_export_table = $wpdb->prefix ."ultimate_csv_importer_scheduled_export";

        $time = $_POST['CreatedTime'];
        $type = $_POST['Type'];

        if($type == 'Import'){
            $get_id = $wpdb->get_results("select id , scheduledtimetorun , scheduleddate , frequency from $schedule_import_table where createdtime = '{$time}' ");
        }else{
            $get_id = $wpdb->get_results("select id , scheduledtimetorun , scheduleddate , frequency from $schedule_export_table where createdtime = '{$time}' ");
        }
        
        $id = $get_id[0]->id;
        $scheduled_time = $get_id[0]->scheduledtimetorun;
        $scheduled_date = $get_id[0]->scheduleddate;
        $get_frequency = $get_id[0]->frequency;

        switch ($get_frequency) {
            case 0:
                $frequency = 'OneTime';
                break;
            case 1:
                $frequency = 'Daily';
                break;
            case 2:
                $frequency = 'Weekly';
                break;
            case 3:
                $frequency = 'Monthly';
                break;
            case 4:
                $frequency = 'Hourly';
                break;
            case 5:
                $frequency = 'Every 30 mins';
                break;
            case 6:
                $frequency = 'Every 15 mins';
                break;
            case 7:
                $frequency = 'Every 10 mins';
                break;
            case 8:
                $frequency = 'Every 5 mins';
                break;
            case 9:
                $frequency = 'Every 2 hours';
                break;
            case 10:
                $frequency = 'Every 4 hours';
                break;
        }

        $schedule_message['scheduled_time'] = $scheduled_time;
        $schedule_message['scheduled_date'] = $scheduled_date;
        $schedule_message['frequency'] = $frequency;

        echo wp_json_encode($schedule_message);
        wp_die();
    }


    /**
	 * Updates schedule file data.
	 */
    public function update_schedule(){
        global $wpdb;
        $schedule_message = [];
        $schedule_export_table = $wpdb->prefix ."ultimate_csv_importer_scheduled_export";
        $schedule_import_table = $wpdb->prefix ."ultimate_csv_importer_scheduled_import";

        $type = $_POST['Type'];
        $time = $_POST['CreatedTime'];
        $scheduled_time = $_POST['ScheduledTime'];
        $scheduled_date = $_POST['ScheduledDate'];
        $scheduled_frequency = $_POST['Frequency'];

        if($type == 'Import'){
            $get_id = $wpdb->get_results("select id from $schedule_import_table where createdtime = '{$time}' ");
            $id = $get_id[0]->id;
        }else{
            $get_id = $wpdb->get_results("select id from $schedule_export_table where createdtime = '{$time}' ");
            $id = $get_id[0]->id;
        }
       

        switch ($scheduled_frequency) {
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
        if($type == 'Import'){
            $update_schedule = $wpdb->update( $schedule_import_table, array(
                'scheduledtimetorun' => $scheduled_time,
                'scheduleddate'      => $scheduled_date,
                'frequency'          => $frequency
            ), array( 'id' => $id ) );
        }else{
            $update_schedule = $wpdb->update( $schedule_export_table, array(
                'scheduledtimetorun' => $scheduled_time,
                'scheduleddate'      => $scheduled_date,
                'frequency'          => $frequency
            ), array( 'id' => $id ) );
        }
        

        if($update_schedule){
            $schedule_message['success'] = true;
            $schedule_message['message'] =  "Updated Successfully";
        }
        else {
            $schedule_message['success'] = false;
            $schedule_message['message'] = "Error Occurred while Updating";
        }
        echo wp_json_encode($schedule_message);
        wp_die();

    }
}