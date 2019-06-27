<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCIAdminAjax {

	public static function smuci_ajax_events() {
		$ajax_actions = array(
			'upload_from_url_check' => false,
			'upload_actions' => false,
			'ftp_actions' => false,
			'uci_picklist_handler' => false,
			'external_file_actions' => false,
			'file_treeupload' => false,
			'parseDataToImport' => false,
			'parseDataToExport' => false,
			'parseDataToScheduleExport' => false,
			'static_formula_method_handler' => false,
			'check_templatename' => false,
			'delete_template' => false,
			'update_template' => false,
			'delete_schedule' => false,
			'edit_schedule' => false,
			'filter_template' => false,
			'search_template' => false,
			'get_mediaimg_size' => false,
			'get_headerData' => false,
			'update_event' => false,
			'selectrevision' => false,
			'downloadFile' => false,
			'download_AllFiles' => false,
			'deleteFileEvent' => false,
			'deleteRecordEvent' => false,
			'deleteAllEvents' => false,
			'deleteScheduledFile' => false,
			'deleteAllScheduledEvent' => false,
			'trashRecords' => false,
			'downloadLog' => false,
			'register_acfpro_fields' => false,
			'register_acf_free_fields' => false,
			'delete_acf_pro_fields' => false,
			'delete_acf_free_fields' => false,
			'register_pods_fields' => false,
			'delete_pods_fields' => false,
			'add_bidirectional_fields' => false,
			'register_types_fields' => false,
			'delete_types_fields' => false,
			'check_CFRequiredFields' => false,
			'schedule_your_current_event' => false,
			'inlineimage_upload' => false,
			'set_post_types' => false,
			'FetchPieChartData' => false,
			'FetchBarStackedChartData' => false,
			'FetchLineChartData' => false,
			'options_savein_ajax' => false,
			'database_optimization_settings' => false,
			'database_optimization_process' => false,
			'upload_zipfile_handler' => false,
			'get_schedule_event_info' => false,
			'dismiss_notices' => false,
			'sendmail' => false,
			'send_subscribe_email' =>false,
			'retrieve_record' => false,
			'preview_record' => false,
			'rollback_now' => false,
			'clear_rollback' => false,
			'parseXmlDataToShow' => false,
			'treeNode' => false,
			'tableNode' => false,
			'aws_validation' => false,
                        'aws_bucket_options' => false,
                        'aws_save_bucketname' => false,
                        'sync_s3' => false,
		);
		foreach($ajax_actions as $action => $value ){
			add_action('wp_ajax_'.$action, array(__CLASS__, $action));
		}
	}

	public static function parseXmlDataToShow()
	{
		global $uci_admin;
		$namespace = explode(":", $_POST['id']);
		if(isset($namespace[1]))
		$n = $namespace[1];
		else
		$n = $_POST['id'];

		$file = $_POST['path'];
		$treetype = $_POST['treetype'];
		$doc = new DOMDocument();
		$doc->load($file);

		  $nodes=$doc->getElementsByTagName($n);
		 // print_r($nodes);
		if($nodes->length < $_POST['pag'])
		 die('<div style="color:red;padding:20px">Maximum Limit Exceed!<div>');

		if(isset($_POST['pag']))
		  $i = $_POST['pag'] - 1;
		else
		  $i = 0;
		if($i < 0)
		  die('<div style="color:red;padding:20px">Node not available!<div>');

		while (is_object($finance = $doc->getElementsByTagName($n)->item($i))) {
			if($treetype == 'table')
		    $uci_admin->tableNode($finance);
			else
			$uci_admin->treeNode($finance);
		    die();
		    $i++;
		}
		die();
	}

	public static function dismiss_notices() {
		update_option('smack_uci_' . $_POST['notice'], 'off');
	}

	public static function get_schedule_event_info() {
		global $wpdb;
		$scheduled_event_info = array();
		$get_scheduled_event_info = $wpdb->get_results($wpdb->prepare("select scheduledtimetorun, scheduleddate, frequency from wp_ultimate_csv_importer_scheduled_import where id = %d", $_POST['id']));
		$scheduled_event_info['scheduledTime'] = $get_scheduled_event_info[0]->scheduledtimetorun;
		$scheduled_event_info['scheduledDate'] = $get_scheduled_event_info[0]->scheduleddate;
		$scheduled_event_info['frequency'] = $get_scheduled_event_info[0]->frequency;
		$scheduled_event_info['event_id'] = $_POST['id'];
		$scheduled_event_info = json_encode($scheduled_event_info);
		print_r($scheduled_event_info);
		die();
	}

	public static function check_requiredfields() {
		global $wpdb;
		$req_arr = array();
		$wobj = new WPClassifyFields();
		$i = 0;
		$import_type = isset($_REQUEST['import_type']) ? sanitize_text_field($_REQUEST['import_type']) : '' ;
		//TYPES Fields
		$types_fields = $wobj->TypesCustomFields();
		if($import_type == 'users')
			$getOptions = get_option('wpcf-usermeta');
		else
			$getOptions = get_option('wpcf-fields');
		if(!empty($types_fields) && is_array($types_fields) && array_key_exists('TYPES',$types_fields)){
			foreach($types_fields['TYPES'] as $key => $value){
				$pt_title = $value['label'];
				if(is_array($getOptions)) {
					if(array_key_exists($pt_title,$getOptions)){
						foreach($getOptions[$pt_title] as $okey => $ovalue){
							if(is_array($ovalue)){
								if(array_key_exists('validate',$ovalue)){
									foreach($ovalue['validate'] as $typeskey => $typesval){
										if($typeskey == 'required'){
											if(is_array($typesval)){
												if(array_key_exists('active',$typesval)){
													if($typesval['active'] == 1){
														$req_arr[$i] = $value['name'];
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}

			}
		}

		//ACF Fields
		$acf_fields = $wobj->ACFCustomFields();
		if(!empty($acf_fields) && is_array($acf_fields) && array_key_exists('ACF',$acf_fields)){
			foreach($acf_fields['ACF'] as $key => $value){
				$pt_title = $value['label'];
				$acf_postcont = $wpdb->get_col($wpdb->prepare("SELECT post_content FROM $wpdb->posts where post_title = %s",$pt_title));
				if(!empty($acf_postcont)){
					$acf_postcont = unserialize($acf_postcont[0]);
					if($acf_postcont['required'] == 1){
						$req_arr[$i] = $value['name'];
						$i++;
					}
				}
			}
		}

		//ACF Fields (Free)
		$getacf_fields = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta
                                                        GROUP BY meta_key
                                                        HAVING meta_key LIKE 'field_%'
                                                        ORDER BY meta_key");
		if(!empty($getacf_fields) && is_array($getacf_fields)){
			foreach($getacf_fields as $acfkey =>$acfval){
				$acf_arr = @unserialize($acfval);
				if($acf_arr !== false){
					if(array_key_exists('required',$acf_arr) && $acf_arr['required'] == 1){
						$req_arr[$i] = $acf_arr['label'];
						$i++;
					}
				}
			}
		}

		//PODS Fields
		$pods_fields = $wobj->PODSCustomFields();
		if(!empty($pods_fields) && is_array($pods_fields) && array_key_exists('PODS',$pods_fields)){
			foreach($pods_fields['PODS'] as $key => $value){
				$pt_title = $value['label'];
				$pods_postid = $wpdb->get_col($wpdb->prepare("SELECT id FROM $wpdb->posts where post_title = %s",$pt_title));
				if(!empty($pods_postid)){
					$pods_postid = $pods_postid[0];
					$pods_reqval = $wpdb->get_col($wpdb->prepare("SELECT meta_value from $wpdb->postmeta where meta_key = %s and post_id = %d",'required',$pods_postid));
					if(!empty($pods_reqval)){
						if($pods_reqval[0] == 1){
							$req_arr[$i] = $value['name'];
							$i++;
						}
					}
				}
			}
		}
		$req_arr = json_encode($req_arr);
		print_r($req_arr);
		die();
	}

	public static function upload_actions() {
		include_once('class-uci-upload-handler.php');
		die();
	}

	public static function ftp_actions() {
		include_once('class-uci-ftp-handler.php');
	}

	public static function  external_file_actions() {
		include_once('class-uci-external-file-handler.php');
	}

	public static function file_treeupload() {
		include_once('class-uci-file-tree-upload.php');
	}

	public static function  upload_from_url_check() {
		$url_tocheck = $_POST['url_tocheck'];
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url_tocheck);
	    curl_setopt($ch, CURLOPT_HEADER, 1);
	    curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($ch);
	    $headers = curl_getinfo($ch);

	    $check_url_status =  $headers['http_code'];
	    if ($check_url_status == '200')
   		echo "200";
		else
   		echo "404";
	    curl_close($ch);
		die();
	}

	public static function parseDataToImport() {
		global $uci_admin;
		$event_information = $uci_admin->getEventInformation();
		if(empty($event_information)) {
			$uci_admin->setEventKey(sanitize_text_field($_POST['postData']['event_key']));
			$uci_admin->setImportType(sanitize_text_field($_POST['postData']['import_type']));
			$uci_admin->setImportMethod(sanitize_text_field($_POST['postData']['importMethod']));
			$uci_admin->setInsertedRowCount(intval( $_POST['postData']['inserted'] ));
			$uci_admin->setUpdatedRowCount(intval( $_POST['postData']['updated'] ));
			$uci_admin->setSkippedRowCount(intval( $_POST['postData']['skipped'] ));
			$additional_event_info = $uci_admin->GetPostValues($uci_admin->getEventKey());
			$uci_admin->setEventFileInformation($additional_event_info[$uci_admin->getEventKey()]['import_file']);
			$uci_admin->setMappingConfiguration($additional_event_info[$uci_admin->getEventKey()]['mapping_config']);
			$uci_admin->setMediaConfiguration($additional_event_info[$uci_admin->getEventKey()]['media_handling']);
			$uci_admin->setImportConfiguration($additional_event_info[$uci_admin->getEventKey()]['import_config']);
			$uci_admin->setFileType(pathinfo($additional_event_info[$uci_admin->getEventKey()]['import_file']['uploaded_name'], PATHINFO_EXTENSION));

			// Assign import as
			$uci_admin->setImportAs($uci_admin->import_post_types(sanitize_text_field($_POST['postData']['import_type'])));
			// Assign import type
			#$importType = $_POST['postData']['import_type'];
			$importType = $uci_admin->getImportType();
			$customPosts = $uci_admin->get_import_custom_post_types();
			if (in_array($importType, get_taxonomies())) {
				if($importType == 'category' || $importType == 'product_category' || $importType == 'product_cat' || $importType == 'wpsc_product_category' || $importType == 'event-categories'):
					$importType = 'Categories';
				elseif($importType == 'product_tag' || $importType == 'event-tags' || $importType == 'post_tag'):
					$importType = 'Tags';
				else:
					$importType = 'Taxonomies';
				endif;
			}
			if (in_array($importType, $customPosts)) {
				$importType = 'CustomPosts';
			}

			// Get mode of the current event
			$mode = $uci_admin->getEventFileInformation('import_mode');

			if($mode == 'new_items') {
				$uci_admin->setMode('Insert');
			} else {
				$uci_admin->setMode('Update');
			}
			$uci_admin->setEventInstance($importType);
		}
		$startLimit = intval( $_POST['postData']['startLimit'] );
		$endLimit = intval( $_POST['postData']['endLimit'] );
		$limit = intval( $_POST['postData']['Limit'] );
		$totalCount = intval( $_POST['postData']['totalcount'] );
		$affectedRecords = array(
			'inserted'  => intval( $_POST['postData']['inserted'] ),
			'updated'   => intval( $_POST['postData']['updated'] ),
			'skipped'   => intval( $_POST['postData']['skipped'] )
		);
		$totalCount = intval( $_POST['postData']['totalcount'] );
		$data = $dataToBeImport = array();

		$eventMapping = $uci_admin->getMappingConfiguration();
		$mediaConfig = $uci_admin->getMediaConfiguration();
		$importConfig = $uci_admin->getImportConfiguration();
		$original_file_name = $uci_admin->getEventFileInformation('uploaded_name');
		$file_name = $uci_admin->getEventFileInformation('file_name');
		$version = $uci_admin->getEventFileInformation('file_version');
		$mode = $uci_admin->getMode();
		$fileType = $uci_admin->getFileType();
		$importMethod = $uci_admin->getImportMethod();
		$eventKey = $uci_admin->getEventKey();
		$eventDir = SM_UCI_IMPORT_DIR . '/' . $eventKey;
		$eventLog = '';
		// Mapped array for the event by group
		// Read file based on the $fileType, $offset & $limit
		switch($fileType) {
			case 'xml':
				// $parserObj = new SmackXMLParser();
				// $eventFile = $uci_admin->getUploadDirectory($parserObj) . '/' . $eventKey . '/' . $eventKey;
				// $root_element = $parserObj->getNodeOccurrences($eventFile);
				// $xml_arr = $parserObj->readData($eventFile, $startLimit, $limit);
				// $data = $uci_admin->xml_file_data($xml_arr, $data);
			// $parserObj = new SmackNewXMLImporter();
			// $eventFile = $uci_admin->getUploadDirectory($parserObj) . '/' . $eventKey . '/' . $eventKey;
				$data = array();
				break;
			case 'csv':
			default:
				$parserObj = new SmackCSVParser();
				$eventFile = $eventDir . '/' . $eventKey;
				$data = $parserObj->parseCSV($eventFile, $startLimit , $limit);
				break;
		}
		for ($i = $startLimit; $i < $endLimit; $i++) {
			try {
				$uci_admin->importData($eventKey, $importType, $importMethod, $mode, $data[$i], $i, $eventMapping, $affectedRecords, $mediaConfig, $importConfig);
				$manage_records[$mode][] = $uci_admin->getLastImportId();
				$detailed_log = $uci_admin->detailed_log;
				if (!empty($detailed_log)) {
					$uciEventLogger = new SmackUCIEventLogging();
					$eventLogFile = $eventDir . '/'.$eventKey.'.log';
					$eventInfoFile = $eventDir . '/'.$eventKey.'.txt';
					$recordId = array($uci_admin->getLastImportId());
					$contents = array();
					if(file_exists($eventInfoFile)) {
						$handle   = fopen( $eventInfoFile, 'r' );
						$contents = json_decode( fread( $handle, filesize( $eventInfoFile ) ) );
						fclose( $handle );
					}
					$fp = fopen($eventInfoFile, 'w+');
					if(!empty($contents) && $contents != null) {
						$contents = array_merge( $contents, $recordId );
						$contents = json_encode( $contents );
					} else {
						$contents = json_encode( $recordId );
					}
					fwrite($fp, $contents);
					fclose($fp);
					$uciEventLogger->lfile("$eventLogFile");
					if($startLimit == 1) {
						$uciEventLogger->lwrite(__("File has been used for this event: ") . $original_file_name, false);
						$uciEventLogger->lwrite(__("Type of the imported file: ") . $fileType, false);
						$uciEventLogger->lwrite(__("Revision of the which is used: ") . $version, false);
						$uciEventLogger->lwrite(__("Mode of event: ") . $mode, false);
						$uciEventLogger->lwrite(__("Total no of records: ") . $totalCount, false);
						$uciEventLogger->lwrite(__("Rows handled on each iterations (Based on your server configuration): ") . $limit, false);
						$uciEventLogger->lwrite(__("File used to import data into: ") . $uci_admin->getImportAs() . ' (' . $importType . ')', false);
						#$fp = fopen($eventInfoFile, 'w+');
					}
					foreach ($uci_admin->detailed_log as $lkey => $lvalue) {
						$eventLog = '<div style="margin-left:10px; margin-right:10px;"><table>';
						$verify_link = '';
						foreach ($lvalue as $lindex => $lresult) {
							if($lindex != 'VERIFY')
								$eventLog .= '<tr><td><p><b>' . $lindex . ': </b>' . $lresult . ' </td><p></tr>';
							else
								$verify_link = '<tr><td><p>' . $lresult . ' </td><p></tr>';
						}
						$eventLog .= $verify_link;
					}
					$eventLog .= '</table></div>';
					$uciEventLogger->lwrite($eventLog);
					$uci_admin->setProcessedRowCount($i);
					echo json_encode(array(
							'total_no_of_rows' => $totalCount,
							'processed' => $uci_admin->getProcessedRowCount(),
							'inserted' => $uci_admin->getInsertedRowCount(),
							'updated'  => $uci_admin->getUpdatedRowCount(),
							'skipped'  => $uci_admin->getSkippedRowCount(),
							'eventLog' => $eventLog)
					);
				//skip empty row in csv file check starts
				}else{
					$eventLog = '<div style="margin-left:10px; margin-right:10px;"><table>';
                                        $eventLog .= '<tr><td><p><b>Message:</b> Skip empty row</p></td><p></tr>';
                                        $eventLog .= '</table></div>';
                                        echo json_encode(array(
                                                        'total_no_of_rows' => $totalCount,
                                                        'processed' => 0,
                                                        'inserted' => 0,
                                                        'updated'  => 0,
                                                        'skipped'  => 1,
                                                        'eventLog' => $eventLog)
                                        );
				}
				//skip empty row in csv file check ends 
			} catch (Exception $e) {
				$parserObj->logE('ERROR:', $e);
			}
		}

		$fileInfo = array(
			'file_name' => $file_name,
			'original_file_name' => $original_file_name,
			'file_type' => $fileType,
			'revision'  => $version,
		);
		$eventInfo = array(
			'count' => $totalCount,
			'processed' => $uci_admin->getProcessedRowCount(),
			'inserted' => $uci_admin->getInsertedRowCount(),
			'updated'  => $uci_admin->getUpdatedRowCount(),
			'skipped'  => $uci_admin->getSkippedRowCount(),
			'eventLog' => $eventLog
		);
		$uci_admin->manage_records($manage_records, $fileInfo, $eventKey, $importType, $mode, $eventInfo);
		die();
	}

	public static function parseDataToExport() {
		global $wpdb, $uci_admin;
		require_once ('class-uci-exporter.php');
		die();
	}

	public static function parseDataToScheduleExport() {
		global $wpdb, $uci_admin;
		$currentUser = wp_get_current_user();
		$schedulerId = $currentUser->ID;
		$currentDate = current_time('mysql', 0);
		$nextRun = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['date']) . ' ' . (sanitize_text_field($_POST['time']))));
		switch ($_POST['scheduled_info']['schedule_frequency']) {
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
		$wpdb->insert('wp_ultimate_csv_importer_scheduled_export',
			array(
				'module' => $_POST['module'],
				'export_mode' => 'FTP',
				'optionalType' => $_POST['optionalType'],
				'conditions' => json_encode($_POST['conditions']),
				'exclusions' => json_encode($_POST['eventExclusions']),
				'file_name' => $_POST['fileName'],
				'scheduleddate' => $_POST['scheduled_info']['date'],
				'frequency' => $frequency,
				'scheduledtimetorun' => $_POST['scheduled_info']['schedule_time'],
				'host_name' => $_POST['scheduled_info']['host_name'],
				'host_port' => $_POST['scheduled_info']['host_port'],
				'host_username' => $_POST['scheduled_info']['host_username'],
				'host_password' => $_POST['scheduled_info']['host_password'],
				'host_path' => $_POST['scheduled_info']['host_path'],
				'file_type' => 'csv',
				'nexrun' => $nextRun,
				'scheduled_by_user' => $schedulerId,
				'createdtime' => $currentDate,
			),
			array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
		);
		print json_encode(array('msg' => 'Export scheduled successfully!'));
		die;
	}

	public static function static_formula_method_handler(){
		require_once(SM_UCI_PRO_DIR . "admin/views/form-static-formula-views.php");
		die();
	}

	public static function uci_picklist_handler() {
		require_once(SM_UCI_PRO_DIR . "admin/views/form-add-custom-field.php");
		die();
	}

	public static function check_templatename() {
		global $wpdb;
		$where = '';
		$templatename = addslashes($_POST['templatename']);
		$templateid = isset($_REQUEST['templateid']) ? intval($_REQUEST['templateid']) : '';
		if ($templateid) {
			$where = " and id != $templateid";
		}
		$template_count = $wpdb->get_results("select count(*) as count from wp_ultimate_csv_importer_mappingtemplate where templatename = '{$templatename}' $where");
		print_r($template_count[0]->count);
		die();
	}

	public static function delete_template() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-templatemanager.php");
		global $templateObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$templateObj->deleteTemplate($_POST['templateid']);
		die;
	}

	public static function update_template() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-templatemanager.php");
		global $templateObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$templateObj->editTemplate($_POST);
		die;
	}

	public static function delete_schedule() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-schedulemanager.php");
		global $scheduleObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$scheduleObj->deleteSchedule($_POST['scheduleid'], $_POST['type']);
		die;
	}

	public static function edit_schedule() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-schedulemanager.php");
		global $scheduleObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$scheduleObj->editSchedule($_POST['schedule_data']);
		die;
	}

	public static function get_mediaimg_size() {
		global $_wp_additional_image_sizes;
		$sizes = array();
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'large','mediumlarge','custom') ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			}
		}
		print_r(json_encode($sizes));
		die;
	}

	/**
	 * Filter the templates
	 */
	public static function filter_template() {
		global $wpdb;
		$filename = sanitize_text_field($_POST['filename']);
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$templateName = sanitize_text_field($_POST['search']);
		$offset = intval($_POST['offset']);
		$limit = intval($_POST['limit']);
		$filterclause = '';
		if (!empty($startDate) && !empty($endDate)) {
			$filterclause .= "createdtime between '$startDate%' and '$endDate%' and";
			$filterclause = substr($filterclause, 0, -3);
		} else {
			if (!empty($startDate)) {
				$filterclause .= "createdtime >= '%$startDate%' and";
				$filterclause = substr($filterclause, 0, -3);
			} else {
				if (!empty($endDate)) {
					$filterclause .= "createdtime <= '%$endDate%' and";
					$filterclause = substr($filterclause, 0, -3);
				}
			}
		}
		if (!empty($templateName)) {
			$filterclause .= " templatename like '%$templateName%'";
		}
		if (!empty($filterclause)) {
			$filterclause = "where $filterclause";
		}
		$templateCount = $wpdb->get_results("select count(*) from wp_ultimate_csv_importer_mappingtemplate  " .$filterclause.  " and  csvname = '" . $filename ."'");
		foreach($templateCount[0] as $key => $value) {
			$count = $value;
		}
		if($count < $offset) {
			print_r('Count Exceeded.');
			die;
		}
		$templateList = $wpdb->get_results("select * from wp_ultimate_csv_importer_mappingtemplate ".$filterclause." and csvname = '".$filename ."' limit ".$offset.",".$limit."");

		$template_detail = array();
		if(empty($templateList)) {
			print_r("Templates Not Found");
			die;
		} else {
			foreach($templateList as $templatedata) {
				$use_template = "<a href = ".esc_url(admin_url(). "admin.php?page=sm-uci-import&step=mapping_config&eventkey=". $templatedata->eventKey . "&templateid=" . $templatedata->id)." class='btn btn-success'>Use Template</a>";
				$template_detail[] = array('rowcount' => $templateCount[0],'id' => $templatedata->id,'name' => $templatedata->templatename,'file' => $templatedata->csvname,'module' => $templatedata->module,'createdat' => $templatedata->createdtime, 'use_template' => $use_template );
			}}
		print_r(json_encode($template_detail));
		die;
	}

	public static function search_template() {
		global $wpdb;
		$filename = sanitize_text_field($_POST['filename']);
		$templatename = sanitize_text_field($_POST['templatename']);
		$templateList = $wpdb->get_results("select * from wp_ultimate_csv_importer_mappingtemplate where templatename like '".$templatename."%' and csvname= '".$filename."'");
		$template_detail = array();
		if(empty($templateList)) {
			print_r("Templates Not Found");
		}
		else {
			foreach($templateList as $templatedata) {
				$use_template = "<a href = ".esc_url(admin_url(). "admin.php?page=sm-uci-import&step=mapping_config&eventkey=". $templatedata->eventKey . "&templateid=" . $templatedata->id)." class='btn btn-success'>Use Template</a>";
				$template_detail[] = array('id' => $templatedata->id,'name' => $templatedata->templatename,'file' => $templatedata->csvname,'module' => $templatedata->module,'createdat' => $templatedata->createdtime, 'use_template' => $use_template );
			}}
		print_r(json_encode($template_detail));
		die;
	}

	public static function get_headerData() {
		global $uci_admin;
		$eventkey = sanitize_text_field($_POST['eventkey']);
		$headers = $_POST['headers'];
		$post_values = $uci_admin->GetPostValues($eventkey);
		$core_group = array('Core Fields' => 'CORE');
		$import_data = $uci_admin->generateDataArrayBasedOnGroups($core_group,$post_values[$eventkey]['mapping_config']);
		$headers = array_intersect_key($import_data['CORE'],$headers);
		print_r(json_encode($headers));
		die;
	}

	public static function schedule_your_current_event() {
		global $scheduleObj;
		$schedule_msg = $scheduleObj->saveEventInformationToSchedule(); //generateSchedule();
		print_r(json_encode($schedule_msg));
		die;
	}

	public static function update_event() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->updateEvent($_POST);
		die;
	}

	public static function downloadFile() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->downloadFile($_POST['event_id'],$_POST['revision'],$_POST['filename']);
		die;
	}

	public static function selectrevision() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->selectrevisiondetails($_POST['event_id'],$_POST['revision'],$_POST['filename']);
		die;
	}


	public static function deleteFileEvent() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->deleteFiles($_POST['path'],$_POST['id'],$_POST['filename'],$_POST['version']);
		die;
	}

	public static function deleteRecordEvent() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->deleteRecords($_POST['filename'],$_POST['version'],$_POST['module'],$_POST['importas']);
		die;
	}

	public static function deleteAllEvents() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->deleteAll($_POST['id'],$_POST['filename'],$_POST['module']);
		die;
	}

	public static function deleteAllScheduledEvent() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->deleteAll_scheduledEvent($_POST['schedule_idList'],$_POST['file_id']);
		die;
	}

	public static function trashRecords() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->transactRecords($_POST['id'],$_POST['module'],$_POST['filename'],$_POST['status']);
		die;
	}

	public static function deleteScheduledFile() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->deleteSchedule_Files($_POST);
		die;
	}

	public static function downloadLog() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-logmanager.php");
		global $log_managerObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$log_managerObj->logDownload($_POST);
		die;
	}

	public static function download_AllFiles() {
		require_once(SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$fileObj->downloadAllFiles($_POST['id']);
		die;
	}

	/** Third party custom field Registration & Deletion **/
	public static function register_acfpro_fields() {
		global $acfHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$acfHelper->Register_ProFields($_POST);
	}

	public static function register_acf_free_fields() {
		global $acfHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$acfHelper->Register_FreeFields($_POST);
	}

	public static function register_pods_fields() {
		global $podsHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$podsHelper->Register_Fields($_POST);
	}

	public static function register_types_fields() {
		global $typesHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$typesHelper->Register_Fields($_POST);
	}

	public static function delete_acf_pro_fields() {
		global $acfHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$acfHelper->Delete_ProFields($_POST);
	}

	public static function delete_acf_free_fields() {
		global $acfHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$acfHelper->Delete_FreeFields($_POST);
	}

	public static function delete_pods_fields() {
		global $podsHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$podsHelper->Delete_Fields($_POST);
	}

	public static function delete_types_fields() {
		global $typesHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$typesHelper->Delete_Fields($_POST);
	}

	public  static function add_bidirectional_fields() {
		global $podsHelper;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$podsHelper->Relational_Fields($_POST);
	}
	/** End Field Registration */

	public static function check_CFRequiredFields() {
		global $uci_admin;
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$uci_admin->Required_CF_Fields($_POST);
	}

	public static function inlineimage_upload(){
		require_once(SM_UCI_PRO_DIR . "includes/class-uci-inlinezipupload.php");
	}

	public static function set_post_types() {
		global $uci_admin;
		$parserObj = new SmackCSVParser();
		$eventKey = isset($_POST['filekey']) ? sanitize_key($_POST['filekey']) : '';
		$uploadedname = isset($_POST['uploadedname']) ? sanitize_text_field($_POST['uploadedname']) : '';
		$file = SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
		$parserObj->parseCSV($file, 0, -1);
		$Headers = $parserObj->get_CSVheaders();
		$Headers = $Headers[0];
		$type = 'Posts';
		if(in_array('wp_page_template', $Headers) && in_array('menu_order', $Headers)){
			$type = 'Pages';
		} elseif(in_array('user_login', $Headers) || in_array('role', $Headers) || in_array('user_email', $Headers) ){
			$type = 'Users';
		} elseif(in_array('comment_author', $Headers) || in_array('comment_content', $Headers) ||  in_array('comment_approved', $Headers) ){
			$type = 'Comments';
		} elseif( in_array('reviewer_name', $Headers) || in_array('reviewer_email', $Headers)){
			$type = 'CustomerReviews';
		} elseif( in_array('event_start_date', $Headers) || in_array('event_end_date', $Headers)){
			$type = 'event';
		}
		elseif( in_array('ticket_start_date', $Headers) || in_array('ticket_end_date', $Headers) && !in_array('event_start_date' , $Headers)){
			$type = 'ticket';
		}
		elseif( in_array('location_name', $Headers) || in_array('location_address', $Headers)){
			$type = 'location';
		} elseif( in_array('hide_on_screen', $Headers) || in_array('position', $Headers) || in_array('layout', $Headers)){
			if(in_array('advanced-custom-fields/acf.php', $uci_admin->get_active_plugins())) {
				$type = 'acf';
			} elseif( in_array('advanced-custom-fields-pro/acf.php', $uci_admin->get_active_plugins())) {
				$type = 'acf-field-group';
			}
		} elseif( in_array('recurrence_freq', $Headers) || in_array('recurrence_interval', $Headers) || in_array('recuurence_days', $Headers)){
			$type = 'event-recurring';
		} elseif( in_array('name', $Headers) && in_array('slug', $Headers)){
			$type = 'category';
		} elseif(in_array('woocommerce/woocommerce.php', $uci_admin->get_active_plugins())){
			if(in_array('PARENTSKU', $Headers) || in_array('VARIATIONSKU', $Headers) || in_array('PRODUCTID', $Headers) || in_array('VARIATIONID', $Headers)){
				$type = 'WooCommerceVariations';
			} elseif(in_array('coupon_code', $Headers) || in_array('COUPONID', $Headers) || in_array('coupon_amount', $Headers)){
				$type = 'WooCommerceCoupons';
			} elseif(in_array('ORDERID', $Headers) || in_array('payment_method', $Headers)){
				$type = 'WooCommerceOrders';
			} elseif(in_array('REFUNDID', $Headers)){
				$type = 'WooCommerceRefunds';
			} elseif(in_array('sku', $Headers)){
				$type = 'WooCommerce';
			}
		} elseif(in_array('wordpress-ecommerce/marketpress.php', $uci_admin->get_active_plugins()) || in_array('marketpress/marketpress.php', $uci_admin->get_active_plugins())){
			if(in_array('VARIATIONID', $Headers) || in_array('PRODUCTID', $Headers)){
				$type = 'MarketPressVariations';
			} elseif(in_array('sku', $Headers) || in_array('PRODUCTSKU', $Headers)){
				$type = 'MarketPress';
			}
		} elseif(in_array('wp-e-commerce/wp-shopping-cart.php', $uci_admin->get_active_plugins())){
			if(in_array('coupon_code', $Headers) || in_array('COUPONID', $Headers)){
				$type = 'WPeCommerceCoupons';
			} elseif(in_array('sku', $Headers)){
				$type = 'WPeCommerce';
			}
		}
		$result = $template_order = array();
		$template_order = $uci_admin->setPriority($uploadedname, $eventKey, null, $Headers);
		$result['is_template'] = 'no';
		if(!empty($template_order)){
			$result['is_template'] = 'yes';
		}
		$result['type'] = $type;
		print_r(json_encode($result));
		die();
	}

	public static function FetchBarStackedChartData() {
		global $wpdb, $uci_admin;
		$available_types = array();
		foreach($uci_admin->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col($wpdb->prepare("select distinct( import_type ) from smackuci_events", array()));
		$count = 1;
		foreach($get_list_of_imported_types as $import_type) {
			$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as created, sum(updated) as updated, sum(skipped) as skipped from smackuci_events where import_type = %s", $import_type, $import_type ) );
			if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}

			if($get_chart_data[0]->created) {
				$returnArray[ $import_type_data ]['created'] = $get_chart_data[0]->created;
			} else {
				$returnArray[ $import_type_data ]['created'] = 0;
			}
			if($get_chart_data[0]->updated) {
				$returnArray[ $import_type_data ]['updated'] = $get_chart_data[0]->updated;
			} else {
				$returnArray[ $import_type_data ]['updated'] = 0;
			}
			if($get_chart_data[0]->skipped) {
				$returnArray[ $import_type_data ]['skipped'] = $get_chart_data[0]->skipped;
			} else {
				$returnArray[ $import_type_data ]['skipped'] = 0;
			}
			$count++;
		}
		echo json_encode($returnArray);
		die();
	}

	public static function FetchPieChartData() {
		global $wpdb, $uci_admin;
		$available_types = array();
		foreach($uci_admin->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col($wpdb->prepare("select distinct( import_type ) from smackuci_events", array()));
		$count = 1;
		foreach($get_list_of_imported_types as $import_type) {
			$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as %s from smackuci_events where import_type = %s", $import_type, $import_type ) );
			if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}
			if($get_chart_data[0]->$import_type) {
				$data = $get_chart_data[0]->$import_type;
				$returnArray[ $count ][ $import_type_data ] = $data;
			} else {
				$returnArray[ $count ][ $import_type_data ] = 0;
			}
			$count++;
		}
		echo json_encode($returnArray);
		die();
	}

	public static function FetchLineChartData() {
		global $wpdb, $uci_admin;
		$available_types = array();
		foreach($uci_admin->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col($wpdb->prepare("select distinct( import_type ) from smackuci_events", array()));
		foreach($get_list_of_imported_types as $import_type) {
			$data = '';
			for($i = 0; $i <= 11; $i++) {
				$count = 0;
				$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as %s from smackuci_events where import_type = %s and month = %s and year = %d", $import_type, $import_type, $month[$i], $year[$i] ) );
				if($get_chart_data[0]->$import_type) {
					$data .= $get_chart_data[0]->$import_type . ',';
				} else {
					$data .= $count . ',';
				}
			}
			if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}

			$returnArray[ $import_type_data ] = substr($data, 0, -1);
		}
		echo json_encode($returnArray);
		die();
	}

	public static function options_savein_ajax(){
		$ucisettings = get_option('sm_uci_pro_settings');
		$option = sanitize_text_field($_REQUEST['option']);
		$value = sanitize_text_field($_REQUEST['value']);
		foreach ($ucisettings as $key => $val) {
			$settings[$key] = $val;
		}
		$settings[$option] = $value;
		update_option('sm_uci_pro_settings', $settings);
	}

	public static function database_optimization_settings(){
		$get_optimize = get_option('sm_uci_pro_optimization');
		if (is_array($get_optimize)) {
			foreach($get_optimize as $key => $value) {
				if(isset($key))
					$optimize_settings[$key] = $value;
			}
		}
		$optimize_settings[sanitize_text_field($_POST['option'])] = sanitize_text_field($_POST['value']);
		update_option('sm_uci_pro_optimization', $optimize_settings);
	}

	public static function database_optimization_process(){
		require_once(SM_UCI_PRO_DIR . "includes/class-uci-dboptimizer.php");
		$dboptimizeObj = new SmackUCIDBOptimizer();
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$dboptimizeObj->Process_dboptimizer($_POST);

	}

	public static function upload_zipfile_handler(){
		require_once(SM_UCI_PRO_DIR . "includes/class-uci-zipfilehandler.php");
	}

	public static function sendmail(){
		if($_POST){
                        add_filter( 'wp_mail_content_type','SmackUCIAdminAjax::set_content_type');
			$email = $_POST['email'];
			$url = get_option('siteurl');
			$site_name = get_option('blogname');
			$headers = "From: " . $site_name . "<$email>" . "\r\n";
			$headers.= 'MIME-Version: 1.0' . "\r\n";
			$headers= array( "Content-type: text/html; charset=UTF-8");
			$to = 'support@smackcoders.com';
			$subject = $_POST['query'];
			$message = "Site URL: " . $url . "\r\n";
			$message .= "Plugin Name: " . SM_UCI_SETTINGS . "\r\n";
			$message .= "Message: "."\r\n" . $_POST['message'] . "\r\n";
			//send email
			if(wp_mail($to, $subject, $message, $headers)) {
				echo 'Mail Sent!';
			} else {
				echo "Please draft a mail to support@smackcoders.com. If you doesn't get any acknowledgement within an hour!";
			} //This method sends the mail.
                         remove_filter( 'wp_mail_content_type', 'SmackUCIAdminAjax::set_content_type' );
			die;
		}
	}
         function set_content_type($message) {
                return 'text/plain';
        }

	public static function send_subscribe_email(){
		if($_POST){
			$email = $_POST['subscribe_email'];
			$url = get_option('siteurl');
			$site_name = get_option('blogname');
			$headers = "From: " . $site_name . "<$email>" . "\r\n";
			$headers.= 'MIME-Version: 1.0' . "\r\n";
			$headers.= "Content-type: text/html; charset=iso-8859-1 \r\n";
			$to = 'support@smackcoders.com';
			$subject = 'Newsletter Subscription';
			$message = "Site URL: " . $url . "\r\n";
			$message .= "Plugin Name: " . SM_UCI_SETTINGS . "\r\n";
			$message .= "Message: Hi Team, I want to subscribe to your newsletter." . "\r\n";
			//send email
			if(wp_mail($to, $subject, $message, $headers)) {
				echo 'Mail Sent!';
			} else {
				echo "Please draft a mail to support@smackcoders.com. If you doesn't get any acknowledgement within an hour!";
			} //This method sends the mail.
			die;
		}
	}

	public function rollback_now(){
		global $uci_admin;
		$eventKey = $_POST['eventkey'];
		$importtype = $_POST['importtype'];
		$tables = '';	
		$result = $uci_admin->set_backup_restore($tables,$eventKey,'restore');	
		print_r(json_encode($result));
		die;
	}

	public function clear_rollback(){
		global $uci_admin;
                $eventKey = $_POST['eventkey'];
                $importtype = $_POST['importtype'];
                $tables = '';
                $result = $uci_admin->set_backup_restore($tables,$eventKey,'delete');
                print_r(json_encode($result));
                die;
	}

	public function retrieve_record() {
		$parserObj = new SmackCSVParser();
		if($_POST) {
			$file = SM_UCI_IMPORT_DIR . '/' . $_POST['event_key'] . '/' . $_POST['event_key'];
			$csv_row = $parserObj->parseCSV($file, $_POST['row_no']);
			print_r(json_encode($csv_row[$_POST['row_no']]));
		}
		die;
	}

	public static function preview_record() {
		$parserObj = new SmackCSVParser();
		$modified_result = array();
		$result = '';
		if($_POST) {
			$file = SM_UCI_IMPORT_DIR . '/' . $_POST['event_key'] . '/' . $_POST['event_key'];
			if($_POST['is_xml'] == 1){
				$mapping = array('title' => $_POST['title'], 'content' => $_POST['content'], 'excerpt' => $_POST['excerpt'], 'image' => $_POST['image']);
				$xmlparse = new SmackNewXMLImporter();
				$doc = new DOMDocument();
				$doc->load($file);
				$tag = $_POST['xmltag'];
				foreach ($mapping as $key => $val) {
					if($val!=""){
						$val = str_replace('{', '', $val);
					$val = str_replace('}', '', $val);
					$val = str_replace('<p>', '', $val);
					$val = str_replace('</p>', '', $val);
					$val = preg_replace("(".$tag."[+[0-9]+])", $tag."[".$_POST['row_no']."]", $val);
					$modified_result[$key] = $xmlparse->parse_element($doc,$val);
					}
				}	
			}
			else{
				$csv_row = $parserObj->parseCSV($file, $_POST['row_no']);
				$data = $csv_row[$_POST['row_no']];
				$mapping = array('title' => $_POST['title'], 'content' => $_POST['content'], 'excerpt' => $_POST['excerpt'], 'image' => $_POST['image']);
				foreach($mapping as $key => $val) {
					$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
					preg_match_all($pattern, $val, $results, PREG_PATTERN_ORDER);
					for($i=0; $i<count($results[2]); $i++) {
						$oldWord = $results[0][$i];
						$get_val = $results[2][$i];
						//TODO xml
						if(isset($data[$get_val])) {
							$newWord = $data[$get_val];
						} else {
							$newWord = $get_val;
						}
						$val = str_replace($oldWord, ' ' . $newWord, $val);
					}
					$modified_result[$key] = $val;
				}
			}
			
			$result .= '<table class="table table-striped">';
			$result .= '<tr>';
			#$result .= '<td><label>Post Title</label></td>';
			$result .= '<td><p><b>' . $modified_result['title'] . '</b></p></td>';
			$result .= '</tr>';
			$result .= '<tr>';
			#$result .= '<td><label>Post Content</label></td>';
			$result .= '<td><p>' . $modified_result['content'] . '</p></td>';
			$result .= '</tr>';
			$result .= '<tr>';
			$result .= '<tr>';
			#$result .= '<td><label>Featured Image</label></td>';
			$result .= '<td><p><img src="' . $modified_result['image'] . '" width="50" height="50" /></p></td>';
			$result .= '</tr>';
			#$result .= '<td><label>Short Description</label></td>';
			$result .= '<td><p>' . $modified_result['excerpt'] . '</p></td>';
			$result .= '</tr>';
			$result .= '</table>';
			print $result;
		}
		die;
	}
	public static function aws_validation(){
		$key = $_POST['key'];
		$secretkey = $_POST['secretkey'];
		$region = $_POST['region'];
		global $uci_admin;
		// print_r($uci_admin);
		$uciEventLogger = new SmackUCIEventLogging();
		$eventLog = '';
		$eventLogFile = SM_UCI_DEBUG_LOG;
		fopen($eventLogFile , 'w+');
		$uciEventLogger->lfile("$eventLogFile");
		$eventLog1 = 'Amazon S3 Access Key :' .$key;
		$eventLog2 = 'Amazon S3 Secret Key : ' .$secretkey;
		$eventLog3 = 'Amazon S3 region :' . $region;
		$uciEventLogger->lwrite($eventLog1);
		$uciEventLogger->lwrite($eventLog2);
		$uciEventLogger->lwrite($eventLog3);
		require_once SM_UCI_PRO_DIR.'libs/aws/aws-validate.php';
		if($response == 'success'){
			$eventLog4 = 'The credentials provide are verified and authenticated successfully';
		} else {
			$eventLog4 = 'The credentials provided are invalid and authentication failed';
		}
		$uciEventLogger->lwrite($eventLog4);
		update_option('aws_key' , $key );
		update_option('aws_secret_key' , $secretkey);
		update_option('aws_region' , $region);
		die();
	}

	public static function aws_bucket_options(){
        	$option = $_POST['options'];
	        update_option('aws_bucket_options' , $option);
        	print_r($option);
	        die();
	}

	public static function aws_save_bucketname(){
        	$bucketname = $_POST['name'];
	        update_option('aws_bucket_name' , $bucketname);
	}

	public static function sync_s3(){
		global $wpdb;
		$slimit = $_REQUEST['slimit'];
		$elimit = $slimit + 5;
		$count = $wpdb->get_results("SELECT COUNT(*) as COUNT FROM $wpdb->posts WHERE post_type = 'attachment' and post_parent != 0",ARRAY_A); 
		$total = $count[0]['COUNT'];
		if($total <= 5){
  		          $total = 7;
        	}
		if($elimit > $total){
			echo json_encode(array(
                                                        'percent' => '100%',
                                                        'slimit' => $slimit,
                                                        'elimit' => $elimit,
                                                        'max_limit_reach' => 'yes'

                                                        )
                                        );
			die;

		}else{
			$exist_images = $wpdb->get_results($wpdb->prepare("select ID,post_parent from $wpdb->posts where post_type = %s and post_parent != %s limit $slimit,$elimit",'attachment',0),ARRAY_A);
			include_once SM_UCI_PRO_DIR.'/libs/aws/aws-upload.php';
			include_once SM_UCI_PRO_DIR.'/libs/aws/aws-autoloader.php';
			$s3 = new uci_aws_s3_helper();
			$uciEventLogger = new SmackUCIEventLogging();
			foreach($exist_images as $key => $value){
				$postID = $value['post_parent'];
				$attachid = $value['ID'];
				$image_info = wp_get_attachment_image_src($attachid);
				$fimg_path = $image_info[0]; 
				$fimg_name = basename($fimg_path);
				if(@getimagesize($fimg_path)){
				$s3imgurl = $s3->aws_image_upload($postID,$fimg_path,$fimg_name,$uciEventLogger);	
				update_post_meta($postID, '_uci_s3_img', $s3imgurl);
				$eventLog = '';
				$eventLogFile = SM_UCI_DEBUG_LOG;
				fopen($eventLogFile , 'w+');
				$uciEventLogger->lfile($eventLogFile);
				$uciEventLogger->lwrite('PostId :' .$postID .' External Image Url : '.$s3imgurl);
				}
			}
			$percent = intval($elimit/$total * 100)."%";
			echo json_encode(array(
								'percent' => $percent,
								'slimit' => $slimit,
								'elimit' => $elimit

								)
						); 
			die;
		}
	}
}
