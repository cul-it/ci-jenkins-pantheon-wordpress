<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackUCIFileManager extends SmackUCIHelper
{
	/**
	 * @param $fileactions
	 * @param $id
	 * @param null $filename
	 * @param null $filepath
	 * @param null $module
	 * @param null $action
	 */
	public function fileActions($fileactions,$id,$filename = null,$filepath = null,$module = null,$action = null) {
		switch ($fileactions) {
			case 'filter':
				break;
			case 'download_file':
				$this->downloadFile($id,$filepath);
				break;
			case 'download_all_files':
				$this->downloadAllFiles($id);
				break;
			case 'delete_file':
				$this->deleteFiles($filepath,$id,$module);
				break;
			case 'delete_files_and_records':
			case 'delete_records':
				$this->deleteAll($filename,$id,$module,$action);
				break;
			case 'trash_records':
				$this->transactRecords($id,$module,$action);
				break;
			case 'update':
				break;
		}
	}

	/**
	 * @param $event_id
	 * @param $revision
	 * @param $filename
	 * @return string
	 * Doc: Get the imported date,inserted count,updated count and skipped count details based on revision.
	 */
	public function selectrevisiondetails($event_id,$revision,$filename) {
		global $wpdb;
		$get_details = $wpdb->get_results($wpdb->prepare("select created,updated,skipped,event_started_at from smackuci_events where revision = %d and original_file_name = %s", $revision,$filename));
		$details = $get_details[0];
		print_r(json_encode($details));
	}

	/**
	 * @param $event_id
	 * @param $revision
	 * @param $filename
	 * @return string
	 * Doc: Download a single File based on its version
	 */
	public function downloadFile($event_id,$revision,$filename)
	{
		global $wpdb;
		$id = $_POST['event_id'];
		$filename = $_POST['filename'];
		$get_event_key = $wpdb->get_results($wpdb->prepare("select original_file_name,name, filepath, eventKey from smackuci_events where revision = %d and original_file_name = %s", $revision , $filename));
		if(empty($get_event_key)) {
			print_r("File not Exists");
		}
		else {
			$filePath = SM_UCI_IMPORT_DIR . '/' . $get_event_key[0]->eventKey  . '/' . $get_event_key[0]->eventKey;
		}
		if (file_exists($filePath)) :
			$downloadableFilePath = SM_UCI_IMPORT_DIR . '/' . $get_event_key[0]->eventKey . '/' . $get_event_key[0]->original_file_name;
			$rename_file = exec("cp -r $filePath $downloadableFilePath");
			$downloadableFile = SM_UCI_IMPORT_URL . '/' . $get_event_key[0]->eventKey . '/' . $get_event_key[0]->original_file_name; 
			print $downloadableFile;die;
			$file_extension = pathinfo($downloadableFile, PATHINFO_EXTENSION);
			$file_extension = pathinfo($filename,PATHINFO_EXTENSION);
			ob_start();
			header("Content-type:application/".$file_extension);
			header("Content-Transfer-Encoding: binary");
			header("Content-Disposition: attachment; filename= " . $downloadableFile);
			header("Pragma: no-cache");
			header("Expires: 0");
			$file_content = file_get_contents($filePath,true);
			ob_flush();
			print $file_content;
			die;
		else :
			return "File Not Exists";
		endif;

	}

	/**
	 * @param $id
	 * @return string
	 * Doc: Download all Files based on File Id.
	 */
	public function downloadAllFiles($id){
		global $wpdb;
		$pathList = array();$name = array();
		$downloadableFilePath = array();
		$recordDetails = $wpdb->get_results($wpdb->prepare("select * from smackuci_events where id = %d",$id));
		$filename = $recordDetails[0]->original_file_name;
		$Details = $wpdb->get_results($wpdb->prepare("select name,revision from smackuci_events where original_file_name = %s",$filename));
		$eventkeys = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
		if(isset($eventkeys)){
			foreach($eventkeys as $keys=>$values){
				$path = SM_UCI_IMPORT_DIR . '/' . $values;
				$pathList[] = $path;
			}
		}
		foreach($Details as $key => $value) {
			$name[] = $value->name;
		}
		$newfile = array();
		$count = count($pathList);
		for($i=0;$i<$count;$i++) {
			$filePath= $pathList[$i] . '/' . $eventkeys[$i];
			$newfile[$i] = $name[$i];
			if (file_exists($filePath)) :
				$downloadableFilePath[$i] = $pathList[$i] . '/' . $name[$i];
				$path = $downloadableFilePath[$i];
				copy($filePath, $path);
				$downloadableFile = SM_UCI_IMPORT_URL . '/' . $eventkeys[$i] . '/' . $name[$i];
				$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
				ob_start();
				header("Content-type:application/" . $file_extension);
				header("Content-Transfer-Encoding: binary");
				header("Content-Disposition: attachment; filename= " . $downloadableFile);
				header("Pragma: no-cache");
				header("Expires: 0");
				$file_content = file_get_contents($filePath);
				ob_flush();
			else :
				return "File Not Exists";
			endif;
		}
		$zipname = $recordDetails[0]->original_file_name;
		$zip_path = SM_UCI_IMPORT_DIR .'/'. $zipname . '.zip';
		if(is_array($pathList)) :
			if(file_exists($zip_path)) :
				$msg['notice'] = " File Already exists";
				unlink($zip_path);
			endif;
			$zipped_file = $this->create_zip($downloadableFilePath, $zip_path , $newfile , false);
		else :
			unlink($zip_path);
		endif;
		if($zipped_file) :
			$zip_path = SM_UCI_IMPORT_URL .'/'. $zipname . '.zip';
			print $zip_path;die;
			ob_start();
			header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename=" . basename($zip_path));
			header("Pragma: no-cache");
			header("Expires: 0");
			$content = readfile($zip_path);
			ob_flush();
			print $content;
			die;
		else :
			$msg['notice'] = "File Not Exists";
			print_r(json_encode($msg));
		endif;
		print_r(json_encode($msg));
	}

	/**
	 * @param array $files_list
	 * @param string $file_path
	 * @param $newfile
	 * @param bool|false $overwrite
	 * @return bool
	 * Doc: Create a zip file for download all option.
	 */
	function create_zip($files_list = array(), $file_path = '', $newfile ,$overwrite = false){
		$valid_files = array();
		if(is_array($files_list)) :
			foreach($files_list as  $files) :
				if(file_exists($files)) :
					$valid_files[] = $files;
				endif;
			endforeach;
		endif;
		if(count($valid_files)) {
			if(file_exists($file_path)) :
				unlink($file_path);
			endif;
			$zip = new ZipArchive();
			if ($zip->open($file_path, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) :
				return false;
			endif;
			foreach($valid_files as $files) :
				foreach($newfile as $new):
					$zip->addFile($files,$new);
				endforeach;
			endforeach;
			$zip->close();
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * @param $file_path
	 * @param $id
	 * @param $filename
	 * @param $version
	 * @return string
	 * Removed files based on its version
	 */
	function deleteFiles($file_path,$id,$filename,$version)
	{
		global $wpdb;
		$filekey = $wpdb->get_col($wpdb->prepare("select eventKey from smackuci_events where revision=%d and original_file_name=%s", $version,$filename));
		if(is_array($filekey)) {
			$filekey = $filekey[0];}
		$file_path = SM_UCI_IMPORT_DIR . '/' . $filekey . '/'. $filekey;
		$revision = '';
		$eventkey = $this->findKey($filename, $version);
		if (!empty($version)) {
			$template_id = $wpdb->get_col($wpdb->prepare("select id from wp_ultimate_csv_importer_mappingtemplate where eventKey = %s", $filekey));
			if (!empty($template_id)) {
				$schedule_detail = $wpdb->get_results($wpdb->prepare("select id,frequency from wp_ultimate_csv_importer_scheduled_import where templateid = %d", $template_id[0]));
				if (!empty($schedule_detail) && $schedule_detail[0]->frequency != 0) {
					$return_data['notice'] = "File was scheduled.";
					$return_data['schedule_id'] = $schedule_detail[0]->id;
					$return_data['revision'] = $revision;
					$return_data['file_path'] = $file_path;
					print_r(json_encode($return_data));
					die;
				} else {
					$wpdb->delete("wp_ultimate_csv_importer_scheduled_import", array('templateid' => $template_id[0]));
				}
			}
			$msg['notice'] = $this->deleteEventDetails($file_path,$revision,$id);
		}
		else {
			$msg['notice'] = 'File does not exists';
		}
		print_r(json_encode($msg));
		die;
	}

	/**
	 * @param $file_path
	 * @param $revision
	 * @param $id
	 * @return string
	 * Doc: Delete the files, it was used by deleteFiles function.
	 */
	function deleteEventDetails($file_path,$revision,$id) {
		global $wpdb;
		if(file_exists($file_path)):
			array_map('unlink', glob("$file_path"));
		endif;

		return "Deleted Successfully";
	}

	/**
	 * @param $filename
	 * @param $version
	 * @param $module
	 * @param $importas
	 * Doc: Removed the records in wordpress based on file version.
	 */
	public function deleteRecords($filename,$version,$module,$importas){
		global $wpdb;
		$get_eventkey = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
		$get_recordDetails = array();
		$details=array();$array = array();
		$count = count($get_eventkey);
		$file_path= array();
		for($i=0;$i<$count;$i++){
			$details[$i] = SM_UCI_IMPORT_DIR . '/' . $get_eventkey[$i] . '/'. $get_eventkey[$i] . '.txt';
			if(file_exists($details[$i])) {
				$array[$i] = isset($details[$i]) ? file_get_contents($details[$i]) : '';
				$file_path = SM_UCI_IMPORT_DIR . '/' . $get_eventkey[$i] . '/'. $get_eventkey[$i];
			}
		}
		$merge = implode(',',$array);
		$replace = str_replace(']','',$merge);
		$replace = str_replace('[','',$replace);
		$records = explode(',',$replace);
		$post_details=array();
		$count1 = count($records);
		$total = $records[0] + $count1;
		$file = file_get_contents(SM_UCI_IMPORT_DIR . '/' . $get_eventkey[0].'/screenInfo.txt', true);
                $file=unserialize($file);
                $module=$file[$get_eventkey[0]]['import_file']['posttype'];
                $importas = $module;

                $mod_array = Array('Posts'=>'post','Pages'=>'page','WooCommerceCoupons'=>'shop_coupon','WooCommerceVariations'=>'product_variation','WooCommerceOrders'=>'shop_order','WooCommerceRefunds'=>'shop_refund');
                if(array_key_exists($module,$mod_array))
                {
                        $module=$mod_array[$module];
                }

                else
                {
                        $module=$module;
                }

		if(!empty($records)&&!empty($module)):
			$recordDetails = $this->delete_wp_records($records, $module, $importas);
			$record_count = $this->findCount($recordDetails);
			$msg['notice'] = "Deleted Successfully";
		else:
			$msg['notice'] = "Record Not Exists or Module doesnot Exists";
		endif;
		print_r(json_encode($msg));
		die;
	}

	/**
	 * @param $records
	 * @return array
	 * Doc: Returns inserted,updated,skipped count, it was used by deleteRecords.
	 */
	public function findCount($records) {
		$modeCount = array();
		if(!empty($records)) {
			foreach ($records as $recordIndex => $recordId) {
				$modeCount[$recordIndex] = count($recordIndex);
			}
		}
		else {
			$modeCount = array_keys($records);
			$modeCount = array_flip($modeCount);
		}
		return $modeCount;
	}

	/**
	 * @param $records
	 * @param $module
	 * @param $importas
	 * @return mixed
	 * Doc: Removed the WordPress records like post,page.. and it was used by deleteRecords function
	 */
	public function delete_wp_records($records,$module,$importas) {
		global $wpdb;
		$postTypes = get_post_types();
		$module = $this->import_post_types($module);
		if($module == 'category' || $module == 'customtaxonomy' || $module == 'tags'){
			$all_taxonomies = get_taxonomies();
			if(array_key_exists($importas,$all_taxonomies)){
				foreach($records as $key => $categoryId){
					wp_delete_term($categoryId, $importas);
				}
			}
		}
		else if($module == 'users'){
			foreach($records as $key => $userId) {
				$wpdb->delete($wpdb->users, array('ID' => $userId), array('%d'));
				$wpdb->delete($wpdb->usermeta, array('user_id' => $userId), array('%d'));
			}
		}
		else if($module == 'comments'){
                        foreach($records as $key => $userId) {
                                $wpdb->delete($wpdb->comments, array('' => $userId), array('%d'));
                        }
                }
		else{
                        if(array_key_exists($module,$postTypes)):
                                foreach($records as $key => $recordId){
                                        $wpdb->delete($wpdb->posts, array('ID' => $recordId, 'post_type' => $module), array('%d', '%s'));
                                        $wpdb->delete($wpdb->postmeta, array('post_id' => $recordId), array('%d'));
                                }

                        else:
                                foreach($records as $key => $userId) {
                                        $wpdb->delete($wpdb->terms, array('' => $userId), array('%d'));
                                }

                        endif;
                }

		return $records;
	}

	/**
	 * @param $id
	 * @param $module
	 * @param $filename
	 * @param $action
	 * @return string
	 * It done restore and trash actions based on File Id.
	 */
	function transactRecords($id, $module, $filename, $action) {
		global $wpdb;
		$module = $this->import_post_types($module);
		$postType = get_post_types();
		$get_eventkey = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
		$get_recordDetails = array();
		$details = array(); $array = array();
		$count = count($get_eventkey);
		$file_path = array();
		for($i=0;$i<$count;$i++){
			$details[$i] = SM_UCI_IMPORT_DIR . '/' . $get_eventkey[$i] . '/'. $get_eventkey[$i] . '.txt';
			if(file_exists($details[$i])) {
				$array[$i] = isset($details[$i]) ? file_get_contents($details[$i]) : '';
				$file_path = SM_UCI_IMPORT_DIR . '/' . $get_eventkey[$i] . '/'. $get_eventkey[$i];
				array_map('unlink', glob("$file_path/*.txt*"));
			}
		}
		$merge = implode(',',$array);
		$replace = str_replace(']','',$merge);
		$replace = str_replace('[','',$replace);
		$records = explode(',',$replace);
		$post_details=array();
		$count1 = count($records);
		$msg = array();
		if(!empty($records)) {
			foreach ($records as $record_id) {
				if (array_key_exists($module, $postType)) {
					$status = $action;
					$post_status = $wpdb->get_results($wpdb->prepare("select post_status from $wpdb->posts where ID= %d ",$record_id));
					$count = count($post_status);
					if(!empty($post_status)) {
						$Status = $post_status[0]->post_status;
						if($Status == 'publish') {
							$wpdb->update($wpdb->posts, array('post_status' => 'trash'), array('ID' => $record_id, 'post_type' => $module));
							$msg['notice'] = "Records are trashed.";
						}
						if($Status == 'trash') {
							$wpdb->update($wpdb->posts, array('post_status' => 'publish'), array('ID' => $record_id, 'post_type' => $module));
							$msg['notice'] = "Records are restored.";
						}
					} else {
						$msg['notice'] = "Records Not Found";
					}
				} else {
					$msg['notice'] = "Records Not Found";
				}
			}
		} else {
			$msg['notice'] = "Records Not Found";
		}
		print_r(json_encode($msg));
		die;
	}

	/**
	 * @param $id
	 * @param $filename
	 * @param $module
	 * Removed the WordPress records and file details based on the File Id
	 */
	public function deleteAll($id,$filename,$module) {
		global $wpdb;
		$importas = $module;
		$get_eventkey = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
		$details = array(); $array = array();
		$count = count($get_eventkey);
		$file_path= array();
		for($i=0;$i<$count;$i++){
			$details[$i] = SM_UCI_IMPORT_DIR . '/' . $get_eventkey[$i] . '/'. $get_eventkey[$i] . '.txt';
			if(file_exists($details[$i])) {
				$array[$i] = isset($details[$i]) ? file_get_contents($details[$i]) : '';
				$file_path[] = SM_UCI_IMPORT_DIR . '/' . $get_eventkey[$i];
			}
		}
		$merge = implode(',',$array);
		$replace = str_replace(']','',$merge);
		$replace = str_replace('[','',$replace);
		$records = explode(',',$replace);
		$file = file_get_contents(SM_UCI_IMPORT_DIR . '/' . $get_eventkey[0].'/screenInfo.txt', true);
                $file=unserialize($file);
                $module=$file[$get_eventkey[0]]['import_file']['posttype'];
                $importas = $module;
		$mod_array = Array('Posts'=>'post','Pages'=>'page','WooCommerceCoupons'=>'shop_coupon','WooCommerceVariations'=>'product_variation','WooCommerceOrders'=>'shop_order','WooCommerceRefunds'=>'shop_refund');
                if(array_key_exists($module,$mod_array))
                {
                        $module=$mod_array[$module];
                }

                else
                {
                        $module=$module;
                }
		if(!empty($records)&&!empty($module)):
			$this->delete_wp_records($records, $module, $importas);
			if(is_array($file_path)) {
				$count = count($file_path);}
			if(isset($file_path)) {
				for($i=0;$i<$count;$i++) {
					if(file_exists($file_path[$i])) {
						$file_path = $file_path[$i];
						array_map('unlink', glob("$file_path/*.*"));
						array_map('unlink', glob("$file_path/**"));
						rmdir($file_path);
						//	        unlink($file_path);
					}
				}
			}
			$msg['notice'] = "Deleted Successfully";
		else:
			$msg['notice'] = "Record doesnot Exists or Module doesnot Exists";
		endif;
		print_r(json_encode($msg));
		die;
	}

	/**
	 * @param $filename
	 * @param $version
	 * @return bool|string
	 * Doc: It find out the eventkey based on filename and its version
	 */
	function findKey($filename,$version) {
		global $uci_admin;
		$file = explode('.',$filename);
		$file_extension = $file[count($file) - 1];
		$filename = str_replace('.'.$file_extension,'',$filename);
		$file_with_version = $filename . '-' . $version . '.' . $file_extension;
		$eventkey = $uci_admin->convert_string2hash_key($file_with_version);
		return $eventkey;
	}

	/**
	 * Doc:
	 */
	function updateEvent(){
		global $wpdb;
		$eventkey = $this->findKey(sanitize_text_field($_POST['filename']),intval($_POST['version']));
		$template_id = $wpdb->get_col($wpdb->prepare("select id from wp_ultimate_csv_importer_mappingtemplate where eventkey = %s",$eventkey));
		$event_data = array('eventkey' => $eventkey,'id' => $template_id);
		print_r(json_encode($event_data));
		die;
	}

	function fetchDistinctEvents(){
		#Todo: Check whether wp_ultimate_csv_importer_multisite_details table data is needed.
		global $wpdb;
		$events = $wpdb->get_results("select distinct(original_file_name) From smackuci_events");
		return $events;
	}

	function getSummary($recordDetails,$module) {
		$recordDetails = maybe_unserialize($recordDetails);
		$summary_data = array('Inserted', 'Updated', 'Skipped');
		$summary = array();
		foreach ($recordDetails as $module => $report) {
			foreach ($report as $record_key => $record_id) {
				$summary[$record_key] = count($report[$record_key]);
			}
		}
		foreach ($summary_data as $import_actions) {
			if (!array_key_exists($import_actions, $summary)) {
				$summary[$import_actions] = 0;
			}
		}
		return $summary;
	}

	function fetchDataOfDistinctEventsRevision ($csvName) {
		global $wpdb;
		$eventsInformation = $wpdb->get_results("select *from smackuci_events where original_file_name = '{$csvName}' order by id desc;");
		return $eventsInformation;
	}

	/**
	 * @param $filedata
	 * Doc: Removed the scheduled file based on the File version
	 */
	function deleteScheduled_Files($filedata) {
		global $wpdb;
		$wpdb->delete("wp_ultimate_csv_importer_scheduled_import",array('id' => $filedata['schedule_id']));
		$msg['notice'] = $this->deleteEventDetails($filedata['file_path'],$filedata['revision'],$filedata['id']);
		print_r(json_encode($msg));
		die;
	}

	/**
	 * @param $idList
	 * @param $fileId
	 * Doc: Removed the Scheduled file based on the File Id
	 */
	function deleteAll_scheduledEvent($idList, $fileId) {
		global $wpdb;
		foreach($idList as $id) {
			$wpdb->delete('wp_ultimate_csv_importer_scheduled_import',array('id' => $id));
		}
		$filepath = $wpdb->get_col($wpdb->prepare("select revision from smackuci_events where id = %d", $fileId));
		foreach($filepath as $path) {
			$key_data = explode('/',$path);
			$key_data = $key_data[count($key_data) - 1];
			$eventkey = str_replace('.txt','',$key_data);
			if(file_exists($path)) {
				unlink($path);
			}
		}
		$msg['notice'] = "Deleted Successfully";
		print_r(json_encode($msg));
		die;
	}
}
global $fileObj;
$fileObj = new SmackUCIFileManager();
