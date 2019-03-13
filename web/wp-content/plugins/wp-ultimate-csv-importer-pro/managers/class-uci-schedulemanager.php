<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCIScheduleManager
{
    /**
     * @param $schedule_actions
     * @param null $schedule_data
     * @param null $id
     */
    public function scheduleActions($schedule_actions,$schedule_data = null,$id = null) {
        switch ($schedule_actions){
            case 'edit':
                $this->editSchedule($schedule_data);
                break;
            case 'delete':
                $this->deleteSchedule($id, $schedule_data['type']);
                break;
        }
    }

    /**
     * @param $schedule_data
     */
    public function editSchedule($schedule_data) {
        global $wpdb;
        if(isset($schedule_data['frequency'])) {
            switch ($schedule_data['frequency']) {
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
            }
        }
        if($schedule_data['type'] == 'scheduled_import') {
            $update_schedule = $wpdb->update( 'wp_ultimate_csv_importer_scheduled_import', array(
                'scheduledtimetorun' => $schedule_data['time'],
                'scheduleddate'      => $schedule_data['date'],
                'frequency'          => $frequency
            ), array( 'id' => $schedule_data['id'] ) );
        } else {
            $update_schedule = $wpdb->update( 'wp_ultimate_csv_importer_scheduled_export', array(
                'scheduledtimetorun' => $schedule_data['time'],
                'scheduleddate'      => $schedule_data['date'],
                'frequency'          => $frequency
            ), array( 'id' => $schedule_data['id'] ) );
        }
        if($update_schedule){
            $schedule_message['msg'] =  "Updated Successfully";
            $schedule_message['msgclass'] = "success";
        }
        else {
            $schedule_message['msg'] = "Error Occurred while Updating";
            $schedule_message['msgclass'] = "danger";
        }
        print_r(json_encode($schedule_message));
        die;
    }

    /**
     * @param $id
     * @param $type
     */
    public function deleteSchedule($id, $type){
        global $wpdb;
        if($type == 'scheduled_import') {
            $delete_schedule = $wpdb->delete( 'wp_ultimate_csv_importer_scheduled_import', array( 'id' => $id ) );
        } else {
            $delete_schedule = $wpdb->delete( 'wp_ultimate_csv_importer_scheduled_export', array( 'id' => $id ) );
        }
        if($delete_schedule){
            $schedule_message['msg'] = "Deleted Successfully";
            $schedule_message['msgclass'] = "success";
        }
        else {
            $schedule_message['msg'] = "Error Occurred While Deleting";
            $schedule_message['msgclass'] = "danger";
        }
        print_r(json_encode($schedule_message));
        die;
    }

    function get_scheduleData() {
        global $wpdb;
        $schedule_data = $wpdb->get_results("select template_table.csvname, template_table.templatename, template_table.eventKey, schedule_table.id, schedule_table.createdtime, schedule_table.scheduledtimetorun, schedule_table.scheduleddate, schedule_table.module, schedule_table.cron_status from wp_ultimate_csv_importer_scheduled_import schedule_table, wp_ultimate_csv_importer_mappingtemplate template_table where schedule_table.templateid = template_table.id");
        return $schedule_data;
    }

    function get_scheduleExportData() {
        global $wpdb;
        $schedule_data = $wpdb->get_results("select schedule_table.id, schedule_table.file_name, schedule_table.file_type, schedule_table.createdtime, schedule_table.scheduledtimetorun, schedule_table.scheduleddate, schedule_table.module, schedule_table.cron_status from wp_ultimate_csv_importer_scheduled_export schedule_table");
        return $schedule_data;
    }

    /**
     *
     */
    public function generatescheduleView() { ?>
        <div class="col-md-12" style="">
            <div class="col-md-3 col-md-offset-0 col-sm-3 col-sm-offset-0 col-xs-offset-1 mb10" style="">
            <label class="schedule_display_label"><?php echo esc_html__('Schedule Date');?></label>
            <input type='text' name='datetoschedule' id='datetoschedule' readonly='readonly' class="form-control" /></div>
            <div class="col-md-3 col-md-offset-1 col-sm-4 col-sm-offset-0 col-xs-offset-0 mb15" style="padding-left:30px;">
            <label class = ""><?php echo esc_html__('Schedule Frequency');?></label>
            <select name='schedule_frequency' class="selectpicker" id='schedule_frequency'>
                <option><?php echo esc_html__('OneTime','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Daily','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Weekly','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Monthly','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Hourly','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Every 30 mins','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Every 15 mins','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Every 10 mins','wp-ultimate-csv-importer-pro');?></option>
                <option><?php echo esc_html__('Every 5 mins','wp-ultimate-csv-importer-pro');?></option>
            </select>

            <input type='hidden' name='schedule_limit' id='schedule_limit' value='1' /></div>


            <div class="col-md-3 col-md-offset-2 col-sm-4 col-sm-offset-1 col-xs-offset-1">  <label class=""><?php echo esc_html__('Schedule Time','wp-ultimate-csv-importer-pro');?></label>
            <select name = 'timetoschedule' id = 'timetoschedule' class="selectpicker">
                <?php for ($hours = 0; $hours < 24; $hours++) {
                    for ($mins = 0; $mins < 60; $mins += 30) {
                        $datetime = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);?>
                        <option value = '<?php echo $datetime;?>'> <?php echo $datetime;?> </option>";
                    <?php }
                }
                ?>
            </select></div>
        </div>
        <script type = 'text/javascript'> jQuery(document).ready(function() {
                jQuery('#datetoschedule').datepicker({
                    dateFormat : 'yy-mm-dd'
                });
            });
        </script>
    <?php }

    /**
     * @return mixed
     */
    public function saveEventInformationToSchedule() {
        global $wpdb;
	//print_r($_POST);die;
        global $uci_admin;
        $records = array();
        $eventKey = sanitize_key($_POST['eventkey']);
        $post_values = $uci_admin->GetPostValues($eventKey);
        $import_mode = 'Schedule';
        $filename = $post_values[$eventKey]['import_file']['file_name'];
        $import_module = $post_values[$eventKey]['import_file']['posttype'];
        $file_type = $post_values[$eventKey]['import_file']['file_extension'];
        $import_method = $post_values[$eventKey]['import_file']['import_method'];
        $templateId = $this->getTemplateInformation($eventKey, $post_values);
        //echo '<pre>'; print_r($templateid); echo '</pre>'; die('iff');
        #echo '<pre>'; print_r($_POST); print($templateId);
        // Import limit for each server request to be used for scheduling
        $import_limit = !empty($_POST['configData']['limit']) ? sanitize_text_field($_POST['configData']['limit']) : 1;
        // Import specific records if user defined any values
        $scheduleRows = !empty($_POST['configData']['offset']) ? sanitize_text_field($_POST['configData']['offset']) : '';
        // Handle the duplicates based on the mentioned fields
        $duplicateHeaders = !empty($_POST['configData']['headers']) ? serialize($_POST['configData']['headers']) : '';

        // Set frequency to schedule
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
        }
        $nextRun = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['date']) . ' ' . (sanitize_text_field($_POST['time']))));
        #print ($nextRun);
        # Code ends imported as
        $currentDate = current_time('mysql', 0);
        #print ($currentDate); die;
        $currentUser = wp_get_current_user();
        $eventSchedulerId = $currentUser->ID; // Get current user id
        /***** Store the event information in smart scheduler *****/
        #        $storeSchedule = "insert into wp_ultimate_csv_importer_scheduled_import (templateid, createdtime, scheduledtimetorun, scheduleddate, module, event_key, importbymethod, import_limit, import_row_ids, frequency, nexrun, scheduled_by_user,import_mode,duplicate_headers) values ($templateId, '{$currentDate}', '{$_POST['time']}', '{$_POST['date']}', '{$import_module}', '{$eventKey}', '{$import_method}', '{$import_limit}', '{$scheduleRows}', '{$frequency}', '{$nextRun}', '{$eventSchedulerId}','{$import_mode}','{$duplicateHeaders}')";
        $timestamp = strtotime($_POST['date']);
        $dbdate = date("Y-m-d", $timestamp);
        $storeSchedule = $wpdb->insert('wp_ultimate_csv_importer_scheduled_import',
            array('templateid' => $templateId,
                  'createdtime' => $currentDate,
                  'scheduledtimetorun' => sanitize_text_field($_POST['time']),
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
                  // Mari added
                  'duplicate_headers' => $duplicateHeaders
            ),
            //array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%d')
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%d', '%s','%d', '%s', '%s', '%s', '%s')
        );
        #$result = $wpdb->query($storeSchedule);
        #if ($result == 1) {
        if(!is_wp_error($wpdb->insert_id)) {
            if ($import_method == 'url') {
                $save_externalfile = "insert into wp_ultimate_csv_importer_external_file_schedules (schedule_id, file_url, filename) values ('{$wpdb->insert_id}', '{$post_values[$eventKey]["import_file"]["extrnfileurl"]}', '{$eventKey}')";
                $wpdb->query($save_externalfile);

            } else {
                if ($import_method == 'ftp') {
                    # Save ftp file details
                    $save_ftpfile = "insert into wp_ultimate_csv_importer_ftp_schedules (schedule_id, hostname, username, password, initial_path, filename) values ('{$wpdb->insert_id}', '{$post_values[$eventKey]['import_file']['host_name']}', '{$post_values[$eventKey]['import_file']['host_username']}', '{$post_values[$eventKey]['import_file']['host_password']}', '{$post_values[$eventKey]['import_file']['host_path']}', '{$eventKey}')";
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
            $data['notification'] = 'Scheduled CSV successfully';
            $data['notification_class'] = 'success';
        } else {
            $data['notification'] = 'Error while inserting into table';
            $data['notification_class'] = 'danger';
        }
        #print_r($data); die;
        return $data;
        print_r(wp_send_json($data));
        die;
    }

    public static function getTemplateInformation($eventKey,$post_values) {
        global $wpdb;
        global $uci_admin;
        $templatename = $post_values[$eventKey]['mapping_config']['templatename'];
        $templateid = $wpdb->get_col($wpdb->prepare("select id from wp_ultimate_csv_importer_mappingtemplate where templatename = %s",$templatename));
        if(!empty($templateid)) {
            return $templateid[0];
        }
        else {
            $filename = isset($post_values[$eventKey]['import_file']['file_name']) ? $post_values[$eventKey]['import_file']['file_name'] : '';
            $filename = explode('-', $filename);
            $templatename = $filename[0].'_'.current_time('Y-m-d h:i:s');
            $uci_admin->saveTemplate($uci_admin,$templatename);
            $lastid = $wpdb->insert_id;
            return $lastid;
        }
    }

    public static function add_cron_schedules_for_every_seconds() {
        return array(
            'wp_ultimate_csv_importer_scheduled_csv_data' => array(
                'interval' => 1, // seconds
                'display' => __('Check scheduled events on every second', SM_UCI_SLUG)
            ),
            'wp_ultimate_csv_importer_scheduled_export_data' => array(
                'interval' => 1, // seconds
                'display' => __('Check scheduled events on every second', SM_UCI_SLUG)
            ),
            'wp_ultimate_csv_importer_scheduled_images' => array(
                'interval' => 1,
                'display' => __('Schedule images on every second', SM_UCI_SLUG)
            ),
            'wp_ultimate_csv_importer_scheduled_emails' => array(
                'interval' => 1,
                'display' => __('Schedule emails on every second', SM_UCI_SLUG)
            ),
        );
    }

    public static function smack_uci_cron_scheduler() {

        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) {
            return false;
        }
        global $wpdb, $uci_admin;
        global $scheduleObj;
        $endDate = '';
        $exturl_schedule_table = 'wp_ultimate_csv_importer_external_file_schedules';
        $schedule_tableName = 'wp_ultimate_csv_importer_scheduled_import';
        $ftp_schedule_table = 'wp_ultimate_csv_importer_ftp_schedules';
        $proceed_scheduling = 1;
        $nextDate = null;
        $wp_date = $scheduleObj->get_wordpress_currentdate('mysql', 0);
        $date = $wp_date['date'];
        $time = $wp_date['time'];
        $hour_time = $wp_date['datetime'];
        $current_timestamp = $wp_date['timstamp'];
        $scheduleList = $wpdb->get_results("select * from $schedule_tableName where isrun = 0 and nexrun <= '$current_timestamp'");

        /****************** Generate Schedule Data *******************/
        #TODO include_once external/ftp handler
        include_once(SM_UCI_PRO_DIR . 'includes/class-uci-external-file-handler.php');
        include_once(SM_UCI_PRO_DIR . 'includes/class-uci-ftp-handler.php');

        $externalObj = new SmackUCIExternal_FileHandler();
        $ftpObj = new SmackUCIFtpHandler();
        
        if (!empty($scheduleList)) {
            foreach ($scheduleList as $scheduledEvent) {
                $runSchedule = false;
                $data = array();
                $frequency = $scheduledEvent->frequency;
                $startDate = strtotime($scheduledEvent->lastrun);
                if ($startDate == '-62169984000') {
                    $startDate = $scheduledEvent->scheduleddate . ' ' . $scheduledEvent->scheduledtimetorun;
                    $startDate = strtotime($startDate);
                }
                if($frequency == 0) {
                    $nextDate = date("Y-m-d H:i:s", $startDate);
                    if($nextDate <= $current_timestamp){
                        $runSchedule = true;
                    }
                    $nextRun = $nextDate;
                } elseif ($frequency == 1) {          // Daily
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
                } elseif ($frequency == 4) {   // Hourly
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
                } elseif ($frequency == 5) {
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
                }

                $template_data = $scheduleObj->getTemplateInfo($scheduledEvent->templateid);
                $data['start_limit'] = $scheduledEvent->start_limit;
                $data['end_limit'] = $scheduledEvent->end_limit;
                $data['eventkey'] = $scheduledEvent->event_key;
                $data['import_limit'] = $scheduledEvent->import_limit;
                $data['import_row_ids'] = !empty($scheduledEvent->import_row_ids) ? unserialize($scheduledEvent->import_row_ids) : '';
                $data['nexrun'] = $nextRun;
                $data['lastrun'] = $wp_date['datetime'];
                $data['frequency'] = $scheduledEvent->frequency;
                $data['module'] = $scheduledEvent->module;
                $data['extension'] = $scheduledEvent->file_type;
                $data['import_mode'] = $scheduledEvent->import_mode;
                $data['csv_name'] = $scheduledEvent->event_key;

                $event_information = $uci_admin->GetPostValues($scheduledEvent->event_key);
                $data['filename'] = $event_information[$scheduledEvent->event_key]['import_file']['file_name'];
                $data['uploaded_name'] = $event_information[$scheduledEvent->event_key]['import_file']['uploaded_name'];
                $data['version'] = $event_information[$scheduledEvent->event_key]['import_file']['file_version'];

                $data['scheduled_by_user'] = $scheduledEvent->scheduled_by_user;
                $data['template_id'] = $scheduledEvent->templateid;
                $data['id'] = $scheduledEvent->id;
                /*************** Other type of uploads *******************/
                if ($runSchedule === true && $scheduledEvent->importbymethod == 'url') {
                    $external_scheduleList = $wpdb->get_results("select filename, file_url from wp_ultimate_csv_importer_external_file_schedules where schedule_id = $scheduledEvent->id");
                    if ($external_scheduleList[0]->filename != '') {
                        $external_url = $external_scheduleList[0]->file_url;
                        $returnData = $externalObj->ExternalFile_Handling($external_url, 'external_import');
                        #print '<pre>'; print_r($returnData); die;
                        if (isset($returnData['Failure'])) {
                            $proceed_scheduling = 0;
                        } else {
                            $data['csv_name'] = $returnData['eventkey'];
                            $data['filename'] = $returnData['filename'];
                            $data['uploaded_name'] = $returnData['uploaded_name'];
                            $data['version'] = $returnData['version'];
                            $data['extension'] = $returnData['extension'];
                            /* $wpdb->insert('smackuci_events',
                                array(
                                    'revision' => $returnData['file_version'],
                                    'name'     => $returnData['uploaded_name'],
                                    'original_file_name' => $returnData['filename'],
                                    'import_type' => $scheduledEvent->module,
                                    'filetype' => $returnData['extension'],
                                    'filepath' => SM_UCI_IMPORT_DIR . '/' . $returnData['eventkey'] . '/' . $returnData['eventkey'],
                                    'eventKey' => $returnData['eventkey'],
                                    'event_started_at' => $current_timestamp,

                                ), array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
                            ); */
                            $wpdb->query("update {$exturl_schedule_table} set filename = '{$returnData['eventkey']}' where schedule_id = '{$scheduledEvent->id}'");
                            #$wpdb->query("update {$schedule_tableName} set imported_as = '{$returnData['eventkey']}' where id = '{$scheduledEvent->id}'");
                            /* $wpdb->insert("wp_ultimate_csv_importer_filemanager", array(
                                'sdm_id' => $scheduledEvent->importid,
                                'imported_on' => $current_timestamp,
                                'version_id' => $returnData['file_version'],
                                'hash_key' => $returnData['hash_name'],
                                'status' => 1,
                            )); */
                        }
                    }
                } elseif ($runSchedule === true && $scheduledEvent->importbymethod == 'ftp') {
                    $ftp_scheduleList = $wpdb->get_results("select * from wp_ultimate_csv_importer_ftp_schedules where schedule_id = $scheduledEvent->id");
                    if ($ftp_scheduleList[0]->filename != '') {
                        $ftp_hostname = $ftp_scheduleList[0]->hostname;
                        $ftp_port = $ftp_scheduleList[0]->port_no;
                        $ftp_hostpath = $ftp_scheduleList[0]->initial_path;
                        $ftp_username = $ftp_scheduleList[0]->username;
                        $ftp_password = $ftp_scheduleList[0]->password;
                        //$ftp_filename = $ftp_scheduleList[0]->filename;
                        $ftp_filename = basename($ftp_scheduleList[0]->initial_path);
                        $returnData = $ftpObj->ftpfile_handling($ftp_hostname, $ftp_port, $ftp_username, $ftp_password, $ftp_filename, $ftp_hostpath);
                        
                        if (isset($returnData['Failure'])) {
                            $proceed_scheduling = 0;
                        } else {
                            $data['csv_name'] = $returnData['eventkey'];
                            $data['filename'] = $returnData['filename'];
                            $data['uploaded_name'] = $returnData['uploaded_name'];
                            $data['version'] = $returnData['version'];
                            $data['extension'] = $returnData['extension'];
                            $wpdb->query("update {$ftp_schedule_table} set filename = '{$returnData['eventkey']}' where schedule_id = '{$scheduledEvent->id}'");
                            #$wpdb->query("update {$schedule_tableName} set imported_as = '{$returnData['eventkey']}' where id = '{$scheduledEvent->id}'");
                        }
                    }
                }

                /****************** End other types of upload ******************/
                /****************** Read Schedule File *****************/
                if ($proceed_scheduling == 1 && $runSchedule === true) {
                    /********* Call function for Schedule process *******/
                    SmackUCIScheduleManager::doSchedule($data);

                }
                /***************** End Read of Schedule file ****************/
            }
        }
        /************** End Schedule Data Generation **************/
    }

    public static function smack_uci_cron_scheduled_export() {
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) {
            return false;
        }
        global $wpdb, $uci_admin;
        global $scheduleObj;
        $endDate = '';
        $schedule_tableName = 'wp_ultimate_csv_importer_scheduled_export';
        $proceed_scheduling = 1;
        $nextDate = null;
        $wp_date = $scheduleObj->get_wordpress_currentdate('mysql', 0);
        $date = $wp_date['date'];
        $time = $wp_date['time'];
        $hour_time = $wp_date['datetime'];
        $current_timestamp = $wp_date['timstamp'];
        $scheduleList = $wpdb->get_results("select * from $schedule_tableName where isrun = 0 and nexrun <= '$current_timestamp'");

        /****************** Generate Schedule Data *******************/
        if (!empty($scheduleList)) {
            foreach ($scheduleList as $scheduledEvent) {
                $runSchedule = false;
                $data = array();
                $frequency = $scheduledEvent->frequency;
                $startDate = strtotime($scheduledEvent->lastrun);
                if ($startDate == '-62169984000') {
                    $startDate = $scheduledEvent->scheduleddate . ' ' . $scheduledEvent->scheduledtimetorun;
                    $startDate = strtotime($startDate);
                }
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
                        $wpdb->query( "update $schedule_tableName set cron_status = 'initialized' where id = '{$scheduledEvent->id}'" );
                    }
                    /********* Call function for Schedule process *******/
                    require_once  SM_UCI_PRO_DIR . "includes/class-uci-exporter.php";
                    $exportObj = new SmackUCIExporter();
                    $exportObj->module = $scheduledEvent->module;
                    $exportObj->exportType  = $scheduledEvent->file_type;
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
                    $exportObj->conditions  = $conditions;
                    $exportObj->optionalType = $scheduledEvent->optionalType;
                    $get_exclusions = json_decode($scheduledEvent->exclusions);
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
                    $exportObj->eventExclusions = $exclusions;
                    $exportObj->fileName = $scheduledEvent->file_name;
                    $exportObj->offset   = $scheduledEvent->start_limit;
                    $exportObj->limit    = $scheduledEvent->end_limit;
                    $exportObj->export_mode = 'FTP';
                    $exportObj->delimiter = $exportObj->setDelimiter($conditions['delimiter']);
                    $exportObj->exportData();
                    $result = $exportObj->export_log;

                    $startLimit = $result['new_offset'] + 1;

                    if($startLimit > $result['total_row_count'] && $scheduledEvent->frequency == 0) {
                        $wpdb->query("update {$schedule_tableName} set isrun = 1 where id = $scheduledEvent->id");
                    }
                    if($startLimit > $result['total_row_count']) {
                        $wpdb->query( "update {$schedule_tableName} set start_limit = 0, end_limit = '{$scheduledEvent->end_limit}', lastrun = '{$wp_date['datetime']}',nexrun = '{$nextRun}', cron_status = 'completed' where id = $scheduledEvent->id" );
                        /** Send exported file to the FTP location **/
                        self::sendExportedFile2FTPLocation($scheduledEvent, $result);
                    } else {
                        $wpdb->query( "update {$schedule_tableName} set start_limit = '{$startLimit}', lastrun = '{$current_timestamp}' where id = '{$scheduledEvent->id}'" );
                    }
                }
                /***************** End Read of Schedule file ****************/
            }
        }
        /************** End Schedule Data Generation **************/
    }

    public static function sendExportedFile2FTPLocation($scheduledEvent, $result) {
        $server = $scheduledEvent->host_name;
        $username = $scheduledEvent->host_username;
        $password = $scheduledEvent->host_password;
        $port = $scheduledEvent->host_port;
        $path = $scheduledEvent->host_path;
        include_once(SM_UCI_PRO_DIR . 'includes/class-uci-ftp-handler.php');
        $ftp = new SmackUCISFTP($server, $username, $password, $port);
        $remote_file = $path . $scheduledEvent->file_name . '.' . $scheduledEvent->file_type;
        $local_file = $result['exported_path'];
        try {
            // connect to FTP server
            if ( $ftp->connect() ) {
                $ftp->put($remote_file, $local_file);
            } else {
                throw new Exception( "Connection failed: " . $ftp->error );
            }
        } catch (Exception $e) {
            $returnData["Failure"] = $e->getMessage();
        }
        // Send notification after uploading the file into FTP location
        $ucisettings = get_option('sm_uci_pro_settings');
        if(isset($ucisettings['send_log_email']) && $ucisettings['send_log_email'] == 'on') {
            require_once(ABSPATH . "wp-includes/pluggable.php");
            $user_info = get_userdata($scheduledEvent->scheduled_by_user);
            $admin_email = $user_info->user_email;
            $subject = "Scheduled export done!";
            $message = "Hi " . $user_info->data->display_name . ',' . "\r\n";
            $message .= "$subject" . "\r\n";
            $message .= "Please check the exported file on your FTP location." . "\r\n";
            $message .= "Filename: {$remote_file}";
            $headers = array();
            $headers[] = "From: {$user_info->data->display_name} <{$user_info->user_email}>" . "\r\n";
            #$headers .= "Cc: $user_info->display_name <$user_info->user_email>" . "\r\n";
            //$attachments = array ( $local_file );
            $attachments = array();
            $res =  wp_mail( $admin_email, $subject, $message, $headers, $attachments );
        }
    }

    public static function doSchedule($data) {
        global $wpdb;
        global $uci_admin;
        $noofrecords = '';
        $resultArr = array();
        $module = $data['module'];
        $schedule_tableName = 'wp_ultimate_csv_importer_scheduled_import';
        $eventKey = $data['csv_name'];
        $offset = $limit = '';
        /************* Change Schedule Status ***********/
        $getScheduling_data = $wpdb->get_results("select cron_status, importbymethod, duplicate_headers from $schedule_tableName where id = {$data['id']}");
        if ($getScheduling_data[0]->cron_status == 'initialized') {
            //return false;
        } else {
            $wpdb->query("update $schedule_tableName set cron_status = 'initialized' where id = '{$data['id']}'");
        }
        /************ End Schedule Status ************/
        if ($getScheduling_data[0]->duplicate_headers) {
            // Mari added
            $duplicate_headers = unserialize($getScheduling_data[0]->duplicate_headers);
            $duplicate_headers = trim($duplicate_headers);
            // Allowed Duplicate headers are ID, post_title, PRODUCTSKU, post_name
        }
        // Set the import mode for scheduling, it was used in CORE_Imp_fields function
        $import_method = $getScheduling_data[0]->importbymethod;
        /************ Get File data *************/
        #TODO: Get template id and proceed the scheduling.
        $get_template_info = $wpdb->get_results($wpdb->prepare('select *from wp_ultimate_csv_importer_mappingtemplate where id = %d', $data['template_id']));
        $eventData = $uci_admin->GetPostValues($get_template_info[0]->eventKey);
        $file_name = $data['uploaded_name'];
        $original_file_name = $data['filename'];
        $version = $data['version'];
        $fileType = $data['extension'];
        #TOdo: Get and assign file extension for fileType
        if($fileType == 'xml') {
            $parserObj = new SmackXMLParser();
            $eventFile = SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
            $tagname = $eventData[$eventKey]['mapping_config']['xml_tag_name'];
            $doc = new DOMDocument();
            $doc->load($eventFile);
            $nodes=$doc->getElementsByTagName($tagname);
            $total_row_count = $nodes->length;
        } elseif($fileType == 'csv' || $fileType == '') {
            $parserObj = new SmackCSVParser();
            $eventFile = SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
            $recordList = $parserObj->parseCSV($eventFile, 0, -1);
            $total_row_count = $parserObj->total_row_cont - 1;
        }

        
        $import_row_id = $data['import_row_ids'];
        /************ End getting file data *************/
        $totRecords = $total_row_count;
        $startLimit = 1;
        $endLimit = $totRecords;
        
        $limit = 1;
        $import_mode = 'Schedule';
        if($uci_admin->getMode() != 'Update')
            $uci_admin->setMode('Update');
        $eventMapping = isset($eventData[$data['eventkey']]['mapping_config']) ? $eventData[$data['eventkey']]['mapping_config'] : '';
        $mediaHandling = isset($eventData[$data['eventkey']]['media_handling']) ? $eventData[$data['eventkey']]['media_handling'] : '';
        $importConfig = isset($eventData[$data['eventkey']]['import_config']) ? $eventData[$data['eventkey']]['import_config'] : '';
        if(!empty($eventData)) {
            foreach ( $eventData as $key => $value ) {
                $uci_admin->setEventInformation( $key, $value );
            }
        }

        $importAs = $uci_admin->import_post_types($module);
        $uci_admin->setEventInstance($module);
        /****************** Start scheduling process *************/
        $uciEventLogger = new SmackUCIEventLogging();
        for ($i = $startLimit; $i <= $endLimit; $i++) {
            // Call for impoting the scheduled data
            switch($fileType) {
                case 'xml':
                    // $eventFile = $uci_admin->getUploadDirectory($parserObj) . '/' . $eventKey;
                    // $root_element = $parserObj->getNodeOccurrences($eventFile);
                    // $xml_arr = $parserObj->readData($eventFile, $offset, $limit);
                    // $recordList = $uci_admin->xml_file_data($xml_arr, $data);
                $recordList = array();
                    break;
                case 'csv':
                default:
                    $eventFile = SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
                    $recordList = $parserObj->parseCSV($eventFile, $startLimit, $limit);
                    //$total_row_count = $parserObj->total_row_cont - 1;
                    #TODO $data[$limit] improvement
                    break;
            }

            #Todo: import_method,mode,filedata need to assign the required values now they have null vlaues
            $logfilename = $eventFile.'.log';
            $uci_admin->importData($eventKey, $module, $import_method, $import_mode, $recordList[$startLimit], $i, $eventMapping, null, $mediaHandling, $importConfig, $duplicate_headers);

            $manage_records[$import_mode][] = $uci_admin->getLastImportId();
            $uciEventLogger->lfile("$logfilename");
            if($startLimit == 1) {
                $uciEventLogger->lwrite("File has been used for this event: " . $original_file_name, false);
                $uciEventLogger->lwrite("Type of the imported file: " . $fileType, false);
                $uciEventLogger->lwrite("Key for the event (Unique key): " . $eventKey, false);
                $uciEventLogger->lwrite("Revision of the which is used: " . $version, false);
                $uciEventLogger->lwrite("Mode of event: " . $import_mode, false);
                $uciEventLogger->lwrite("Total no of records: " . $totRecords, false);
                $uciEventLogger->lwrite("Rows handled on each iterations (Based on your server configuration): " . $limit, false);
                $uciEventLogger->lwrite("File used to import data into: " . $importAs . ' (' . $module . ')', false);
            }
                foreach ( $uci_admin->detailed_log as $lkey => $lvalue ) {
                    $eventLog = '<div style="margin-left:10px; margin-right:10px;"><table>';
                    foreach ( $lvalue as $lindex => $lresult ) {
                        $eventLog .= '<tr><td><p>' . $lresult . '</td><p></tr>';
                    }
                }
                $eventLog .= '</table></div>';
                $uciEventLogger->lwrite( $eventLog );
            /***************** Store Log Details ******************/
            /**************** End Log Details Storage ****************/
            $next_limit = $i;
            $wp_date = self::get_wordpress_currentdate('mysql', 0);
            $current_timestamp = $wp_date['timstamp'];
            $startLimit = $startLimit + 1;
            $wpdb->query("update {$schedule_tableName} set start_limit = '{$startLimit}', lastrun = '{$current_timestamp}' where id = '{$data['id']}'");
        }
        /**************** End Scheduling process ***************/

        /**************** Generate User Details *******************/
        /*************** Sending scheduled log to admin email id ******************/
        $ucisettings = get_option('sm_uci_pro_settings');
        if(isset($ucisettings['send_log_email']) && $ucisettings['send_log_email'] == 'on') {
            require_once(ABSPATH . "wp-includes/pluggable.php");
            $user_info = get_userdata($data['scheduled_by_user']);
            $username = $user_info->user_login;
            $first_name = $user_info->first_name;
            $last_name = $user_info->last_name;
            $recievermail = $first_name . ' ' . $last_name . "<$user_info->user_email>";
            $subject = "Schedule Log for schedule_id: {$data['id']} & filename: {$original_file_name}";
            $message = "$subject";
            $headers = array();
            $headers[] = "From: $user_info->display_name <$user_info->user_email>" . "\r\n";
            #$headers .= "Cc: $user_info->display_name <$user_info->user_email>" . "\r\n";
            $attachments = array ( $logfilename );
            $res =  wp_mail( $recievermail, $subject, $message, $headers, $attachments );
        }
        // End Import process
        if($startLimit > $totRecords && $data['frequency'] == 0) {
            $wpdb->query("update {$schedule_tableName} set isrun = 1 where id = '{$data['id']}'");
        }
        if($startLimit > $totRecords) {
            $wpdb->query( "update {$schedule_tableName} set start_limit = 0, end_limit = '{$endLimit}', lastrun = '{$data['lastrun']}',nexrun = '{$data['nexrun']}', cron_status = 'completed' where id = '{$data['id']}'" );
            $fileInfo = array(
                'file_name' => $file_name,
                'original_file_name' => $original_file_name,
                'file_type' => $fileType,
                'revision'  => $version,
            );
            $eventInfo = array(
                'count' => $totRecords,
                'processed' => $uci_admin->getProcessedRowCount(),
                'inserted' => $uci_admin->getInsertedRowCount(),
                'updated'  => $uci_admin->getUpdatedRowCount(),
                'skipped'  => $uci_admin->getSkippedRowCount(),
                'eventLog' => $eventLog
            );
            $uci_admin->manage_records($manage_records, $fileInfo, $eventKey, $module, $import_mode, $eventInfo);
        }

        /************** Log File Generation **************/
        /************** End Log File Generation ******************/
        /******************** Need to add entry in dashboard chart *****************/
    }


    public static function get_wordpress_currentdate($type, $gmt = 0) {
        $date = array();
        $time = current_time($type, $gmt);
        $date['timstamp'] = $time;
        $date['date'] = date('Y-m-d', strtotime($time));
        $date['time'] = date('H:i', strtotime($time));
        $date['day'] = date('l', strtotime($time));
        $date['datetime'] = date('Y-m-d H:i:s', strtotime($time));
        return $date;
    }

    public static function getTemplateInfo($templateid) {
        global $wpdb;
        $template_data = array();
        $template_data = $wpdb->get_results($wpdb->prepare("select * from wp_ultimate_csv_importer_mappingtemplate where id = %d",$templateid));
        if ($template_data) {
            $template_data = $template_data[0];
        }
        return $template_data;
    }
}
global $scheduleObj;
$scheduleObj = new SmackUCIScheduleManager();
