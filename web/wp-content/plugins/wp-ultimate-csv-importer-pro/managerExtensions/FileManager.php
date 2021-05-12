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

class FileManager {

    private static $instance = null;
    private static $smack_csv_instance = null;

    public function __construct(){
        add_action('wp_ajax_download_file',array($this,'downloadFile'));
        add_action('wp_ajax_download_all_file',array($this,'downloadAllFiles'));
		add_action('wp_ajax_delete_file',array($this,'deleteFiles'));
		add_action('wp_ajax_delete_all_records',array($this,'delete_all_records'));
		add_action('wp_ajax_delete_all_file',array($this,'delete_all_files'));
		add_action('wp_ajax_trash_records',array($this,'trash_records'));
		add_action('wp_ajax_display_events',array($this,'display_events'));
    }

    public static function getInstance() {
		if (FileManager::$instance == null) {
			FileManager::$instance = new FileManager;
            FileManager::$smack_csv_instance = SmackCSV::getInstance();
			return FileManager::$instance;
		}
		return FileManager::$instance;
    }


	/**
	 * Saves event logs in database.
	 * @param  string $hash_key - File hash key
     * @param  string $selected_type - Post type
	 * @param  string $file_name - File name
	 * @param  string $total_rows - Total rows in file
	 */
    public function manage_records($hash_key ,$selected_type , $file_name , $total_rows){
        global $wpdb;
        $log_table_name = $wpdb->prefix ."import_detail_log";

		$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

		if(empty($file_extension)){
			$file_extension = 'xml';
		}

        $file_extn = '.' . $file_extension;
        $get_local_filename = explode($file_extn, $file_name);
        $extension_object = new ExtensionHandler;
        $import_type = $extension_object->import_name_as($selected_type);

        $imported_on = date('Y-m-d h:i:s');
		$month = date("M", strtotime($imported_on));
        $year = date("Y", strtotime($imported_on));
        $file_path = '/smack_uci_uploads/imports/' . $hash_key . '/' . $hash_key;
        
        $get_name = $wpdb->get_results( "SELECT original_file_name FROM smackuci_events " );

        if(!empty($get_name)){
			foreach($get_name as $name_values){
				$inserted_name_values[] = $name_values->original_file_name;
            }
            if(in_array($file_name , $inserted_name_values)){
                $get_revision = $wpdb->get_results( "SELECT revision FROM smackuci_events WHERE original_file_name = '$file_name' " );
				foreach($get_revision as $value){
                    $last_version_id = $value->revision;
                }
                $revision = $last_version_id + 1;
                $name = $get_local_filename[0] .'-'. $revision . $file_extn;
            }    
			else{
				$name = $get_local_filename[0] . '-1' . $file_extn;
                $revision = 1;
            }
        }
        else{
          	$name = $get_local_filename[0] . '-1' . $file_extn;
            $revision = 1;
        }

        $get_data =  $wpdb->get_results("SELECT skipped , created , updated FROM $log_table_name WHERE hash_key = '$hash_key' ");
			$skipped_count = $get_data[0]->skipped;
			$created_count = $get_data[0]->created;
			$updated_count = $get_data[0]->updated;

        $wpdb->insert('smackuci_events', array(
            'revision' => $revision,
            'name' => "{$name}",
            'original_file_name' => "{$file_name}",
            'import_type' => "{$import_type}",
            'filetype' => "{$file_extension}",
            'filepath' => "{$file_path}",
            'eventKey' => "{$hash_key}",
            'registered_on' => $imported_on,
            'processing' => 1,
            'count' => $total_rows,
            'processed' => $created_count,
            'created' => $created_count,
            'updated' => $updated_count,
            'skipped' => $skipped_count,
            'last_activity' => $imported_on,
            'month' => $month,
            'year' => $year
        ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%s','%s','%s')
        );
    }


	/**
	 * Downloads file based on revision.
	 */
    public function downloadFile()
	{
		global $wpdb;
        $response = [];
        $filename = $_POST['filename'];
        $revision = $_POST['revision'];

        $upload = wp_upload_dir();
        $upload_dir = $upload['baseurl'];
        $upload_url = $upload_dir . '/smack_uci_uploads/imports/';
        
        $upload_path = FileManager::$smack_csv_instance->create_upload_dir();
		$get_event_key = $wpdb->get_results($wpdb->prepare("select original_file_name,name, filepath, filetype , eventKey from smackuci_events where revision = %d and original_file_name = %s", $revision , $filename));
		if(empty($get_event_key)) {
			$response['success'] = false;
            $response['message'] = 'file not exists';
		}
		else {

			$filePath = $upload_path .$get_event_key[0]->eventKey . '/' . $get_event_key[0]->eventKey;

			if (file_exists($filePath)) :

				$downloadableFilePath = $upload_path . $get_event_key[0]->eventKey . '/' . $get_event_key[0]->original_file_name;
				copy($filePath, $downloadableFilePath);
				$filelink = $upload_url . $get_event_key[0]->eventKey . '/' . $get_event_key[0]->original_file_name;
				
				$response['success'] = true;
				$response['file_link'] = $filelink;	
			else :
				$response['success'] = false;
				$response['message'] = 'file not exists';		
			endif;
		}
        
        echo wp_json_encode($response); 
        wp_die();
    }
	
	
	/**
	 * Downloads all revisions of a file in zip format .
	 */
    public function downloadAllFiles(){
        global $wpdb;
        $response = [];
        $filename = $_POST['filename'];
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
		$pathList = array();$name = array();
        $downloadableFilePath = array();
        $upload_path = FileManager::$smack_csv_instance->create_upload_dir();
        $upload = wp_upload_dir();
        $upload_dir = $upload['baseurl'];
        $upload_url = $upload_dir . '/smack_uci_uploads/imports/';
		$Details = $wpdb->get_results($wpdb->prepare("select name,revision from smackuci_events where original_file_name = %s",$filename));
		$eventkeys = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
		if(isset($eventkeys)){
			foreach($eventkeys as $keys=>$values){
				$path = $upload_path . $values;
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
				$downloadableFile = $upload_url . $eventkeys[$i] . '/' . $name[$i] ;
				$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
				if(empty($file_extension)){
					$file_extension = 'xml';
				}
				
				$file_content = file_get_contents($filePath);
				
			else :
				$response['success'] = false;
			    $response['message'] =  "File Not Exists";
			endif;
		}
		$zipname = $filename;
		$zip_path = $upload_path . $zipname . '.zip';
		if(is_array($pathList)) :
			if(file_exists($zip_path)) :
				$response['notice'] = " File Already exists";
				unlink($zip_path);
            endif;
            
            $zipped_file = $this->create_zip($downloadableFilePath, $zip_path , $newfile , false);
            
		else :
			unlink($zip_path);
		endif;
        if($zipped_file) :
			$zip_path = $upload_url . $zipname . '.zip'; 
			$response['success'] = true;    
			$response['ziplink'] = $zip_path;
		else :
			$response['success'] = false;
			$response['message'] = "File Not Exists";
			
		endif;
        echo wp_json_encode($response);
        wp_die();
    }


	/**
	 * Creates zip file containing all revisions of a file.
	 * @return boolean
	 */
    public function create_zip($files_list = array(), $file_path = '', $newfile ,$overwrite = false){
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
			$zip = new \ZipArchive();
			if ($zip->open($file_path, $overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE) !== true) :
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
	 * Deletes file based on revision.
	 */
    public function deleteFiles()
	{
        global $wpdb;
        $version = $_POST['revision'];
        $filename = $_POST['filename'];
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
        $upload_path = FileManager::$smack_csv_instance->create_upload_dir();
        $response = [];
		$filekey = $wpdb->get_results($wpdb->prepare("select eventKey,id from smackuci_events where revision=%d and original_file_name=%s", $version,$filename));
		if(is_array($filekey)) {
			$hash_key = $filekey[0]->eventKey;
			$id = $filekey[0]->id;
        }
		$file_path = $upload_path . $hash_key .'/'. $hash_key;
		$revision = '';
		if (!empty($version)) {
			$template_id = $wpdb->get_col($wpdb->prepare("select id from {$wpdb->prefix}ultimate_csv_importer_mappingtemplate where eventKey = %s", $hash_key));
			if (!empty($template_id)) {
				$schedule_detail = $wpdb->get_results($wpdb->prepare("select id,frequency from {$wpdb->prefix}ultimate_csv_importer_scheduled_import where templateid = %d", $template_id[0]));
				if (!empty($schedule_detail) && $schedule_detail[0]->frequency != 0) {
					$return_data['notice'] = "File was scheduled.";
					$return_data['schedule_id'] = $schedule_detail[0]->id;
					$return_data['revision'] = $revision;
					$return_data['file_path'] = $file_path;
					echo wp_json_encode($return_data);
					wp_die();
				} else {
					$wpdb->delete("{$wpdb->prefix}ultimate_csv_importer_scheduled_import", array('templateid' => $template_id[0]));
				}
			}
			if(file_exists($file_path)){
				$response['notice'] = $this->deleteEventDetails($file_path , $id );
			}else{
				$response['notice'] = 'File does not exists';
			}
			
		}
		else {
			$response['notice'] = 'File does not exists';
		}
		echo wp_json_encode($response);
		wp_die();
    }
	
	
	/**
	 * Deletes file from folder and database.
	 * @param  string $file_path - path to file
     * @param  string $id - database file id 
	 * @return string
	 */
    public function deleteEventDetails($file_path , $id) {
		global $wpdb;
		if(file_exists($file_path)):
			array_map('unlink', glob("$file_path"));
			$wpdb->delete("smackuci_events", array('id' => $id));
			
		endif;
		return "Deleted Successfully";
	}
	

	/**
	 * Deletes all revisions of a file.
	 */
    public function delete_all_files() {

        global $wpdb;
        $filename = $_POST['filename'];
        $module = $_POST['type'];
        $response = [];
        $importas = $module;
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
        $upload_path = FileManager::$smack_csv_instance->create_upload_dir();
		$importas = $module;
		$get_eventkey = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
			
		$get_id = $wpdb->get_col($wpdb->prepare("select id from smackuci_events where original_file_name = %s",$filename));		
		$count_id = count($get_id);

		$details = array(); $array = array();
		$count = count($get_eventkey);
		$file_path= array();
		$file_txt_path = array();
		for($i=0;$i<$count;$i++){
			$details[$i] = $upload_path . $get_eventkey[$i] .'/'. $get_eventkey[$i] .'.txt';
			
			if(file_exists($details[$i])) {
				$array[$i] = isset($details[$i]) ? file_get_contents($details[$i]) : '';
				$file_path[] = $upload_path . $get_eventkey[$i];	
			}
		}	
		$merge = implode(',',$array);
		$replace = str_replace(']','',$merge);
		$replace = str_replace('[','',$replace);
		$records = explode(',',$replace);
		$mod_array = Array('Posts'=>'post','Pages'=>'page','WooCommerceCoupons'=>'shop_coupon','WooCommerceVariations'=>'product_variation','WooCommerceOrders'=>'shop_order','WooCommerceRefunds'=>'shop_refund');
            if(array_key_exists($module,$mod_array)){    
                $module = $mod_array[$module];
            }
            else{
                $module = $module;
			}				
		if(!empty($records[0]) && !empty($module)):
			$this->delete_wp_records($records, $module, $importas);
			if(is_array($file_path)) {
				$count = count($file_path);
			}
			if(!empty($file_path)) {
				for($i=0;$i<$count;$i++) {		
					if(file_exists($file_path[$i])) {
						$file_path = $file_path[$i];
						array_map('unlink', glob("$file_path/*.*"));
						array_map('unlink', glob("$file_path/**"));
						rmdir($file_path);			
					}
				}
				for($i = 0 ; $i<$count_id ; $i++){
					$ids = $get_id[$i];
					$wpdb->delete("smackuci_events", array('id' => $ids));
				}

			}
			$response['notice'] = "Deleted Successfully";
		else:
			$response['notice'] = "Record doesnot Exists or Module doesnot Exists";
		endif;
		echo wp_json_encode($response);
		wp_die();
	}


	/**
	 * Deletes all records(posts, pages, custom posts, etc.) created by a file.
	 */
	public function delete_all_records(){

		global $wpdb;
		$filename = $_POST['filename'];
		$module = $_POST['type'];
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
		$upload_path = FileManager::$smack_csv_instance->create_upload_dir();
		$response = [];
		
		$get_eventkey = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
		$get_recordDetails = array();
		$details=array();$array = array();
		$count = count($get_eventkey);
		$file_path= array();
		for($i=0;$i<$count;$i++){
			$details[$i] = $upload_path . $get_eventkey[$i] .'/'. $get_eventkey[$i] .'.txt';
			if(file_exists($details[$i])) {
				$array[$i] = isset($details[$i]) ? file_get_contents($details[$i]) : '';
				$file_path = $upload_path . $get_eventkey[$i] . '/' . $get_eventkey[$i];
			}
		}
		$merge = implode(',',$array);
		$replace = str_replace(']','',$merge);
		$replace = str_replace('[','',$replace);
		$records = explode(',',$replace);
		$post_details = array();
		$count1 = count($records);
		$total = $records[0] + $count1;
				
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

		if(!empty($records[0])&&!empty($module)):
			$recordDetails = $this->delete_wp_records($records, $module, $importas);
			$response['success'] = true;
			$response['message'] = "Deleted Successfully";
		else:
			$response['success'] = false;
			$response['message'] = "Record Not Exists or Module doesnot Exists";
		endif;
		echo wp_json_encode($response);
		wp_die();
	}


	/**
	 * Deletes all records(posts, pages, custom posts, etc) cretaed by a file, from database.
	 * @param  string $records - created records(posts, pages, custom posts) id 
     * @param  string $module - post type
	 * @param  string $importas - import type
	 * @return array
	 */
    public function delete_wp_records($records,$module,$importas) {
		
        global $wpdb;
        $extension_instance = new ExtensionHandler;
		$postTypes = get_post_types();
		
		$module = $extension_instance->import_post_types($module);	
		if($module == 'category' || $module == 'customtaxonomy' || $module == 'tags'){
			$all_taxonomies = get_taxonomies();
			if(array_key_exists($importas,$all_taxonomies)){
				foreach($records as $key => $categoryId){
					wp_delete_term($categoryId, $importas);
				}
			}
		}
		else if($module == 'user'){
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
	 * Trash all records(posts, pages, custom posts, etc) created by a file.
	 */
	public function trash_records() {
		global $wpdb;
		$filename = $_POST['filename'];
		$module = $_POST['type'];
		$action = 'trash';

		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
		$upload_path = FileManager::$smack_csv_instance->create_upload_dir();
		$extension_instance = new ExtensionHandler;
		$module = $extension_instance->import_post_types($module);
		$postType = get_post_types();

		$get_eventkey = $wpdb->get_col($wpdb->prepare("select eventkey from smackuci_events where original_file_name = %s",$filename));
		
		$get_recordDetails = array();
		$details = array(); $array = array();
		$count = count($get_eventkey);
		$file_path = array();

		for($i=0;$i<$count;$i++){
			$details[$i] = $upload_path . $get_eventkey[$i] .'/'. $get_eventkey[$i] . '.txt';
			if(file_exists($details[$i])) {
				$array[$i] = isset($details[$i]) ? file_get_contents($details[$i]) : '';
				$file_path = $upload_path . $get_eventkey[$i]. '/' . $get_eventkey[$i] ;
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
 
					$post_status = $wpdb->get_results($wpdb->prepare("select post_status from {$wpdb->prefix}posts where ID= %d ",$record_id));
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
		echo wp_json_encode($msg);
		wp_die();
	}


	/**
	 * Retrieves and display the file history.
	 */
	public function display_events(){
		global $wpdb;
		$response = [];
		$value = [];
		$fileInfo = [];
		$distinctEvents = $wpdb->get_results("select distinct(original_file_name) From smackuci_events order by id desc");
		
		if(empty($distinctEvents)){
			$response['success'] = false;
			$response['message'] = "No events found";
		}else{
			foreach($distinctEvents as $key => $eventData) {
				$csvName = $eventData->original_file_name;
				$eventsInformation = $wpdb->get_results("select * from smackuci_events where original_file_name = '{$csvName}' order by id desc");	
				$event_id = array();
                    foreach ( $eventsInformation as $eventIndex => $eventInfo ){
                        $file_revisions[ $eventInfo->revision ] = $eventInfo->filepath;
						$event_id[ $eventInfo->id ] = $eventInfo->revision;
                        $eventId = $eventInfo->id;
                        $eventPurpose  = $eventInfo->import_type;
                        $eventKey      = $eventInfo->eventKey;
                        $insertedCount = $eventInfo->created;
                        $updatedCount  = $eventInfo->updated;
                        $skippedCount  = $eventInfo->skipped;
                        $eventHappened = $eventInfo->event_started_at;
						$isDeleted     = $eventInfo->deleted;
					}
					$revise = [];
					foreach($event_id as $val){
						array_push($revise , $val);
					}			
					$fileInfo['filename'] = $csvName;
					$fileInfo['date'] = $eventHappened;
					$fileInfo['purpose'] = $eventPurpose;
					$fileInfo['inserted'] = $insertedCount;
					$fileInfo['updated'] = $updatedCount;
					$fileInfo['skipped'] = $skippedCount;
					$fileInfo['revisions'] = $revise;	
					array_push($value , $fileInfo);		
			}
			$response['success'] = true;
			$response['info'] = $value;	
		}
		echo wp_json_encode($response);
		wp_die();
	}
}