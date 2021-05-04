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

class ServerUpload implements Uploads{

	private static $instance = null;
	private static $smack_csv_instance = null;

	public function __construct(){

		add_action('wp_ajax_get_server',array($this,'upload_function'));
	}

	public static function getInstance() {
		if (ServerUpload::$instance == null) {
			ServerUpload::$instance = new ServerUpload;
			ServerUpload::$smack_csv_instance = SmackCSV::getInstance();
			return ServerUpload::$instance;
		}
		return ServerUpload::$instance;
	}


	/**
	 * Choose file from Server.
	 */
	public function upload_function(){
		$root = '';
		$_POST['dir'] = urldecode($_POST['dir']);
		if(is_dir($_POST['dir'])){
			if( file_exists($root . $_POST['dir']) ) {
				$files = scandir($root . $_POST['dir']);
				natcasesort($files);
				if( count($files) > 2 ) { /* The 2 accounts for . and .. */
					echo "<ul class=\"jqueryfiletree\" style=\"display: none;\">";
					// All dirs
					foreach( $files as $file ) {
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
							echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
						}
					}
					// All files
					foreach( $files as $file ) {
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
							$ext = preg_replace('/^.*\./', '', $file);
							echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
						}
					}
					echo "</ul>";
				}

			}
		}
		else{
			$this->upload_server($_POST['dir']);
		}
		wp_die();

	}


	/**
	 * Retrieves Directories and Files from server.
	 */
	public function get_dir_function() {

		$root = '';
		$_POST['dir'] = urldecode($_POST['dir']);
		$response = [];
		if( file_exists($root . $_POST['dir']) ) {
			$files = scandir($root . $_POST['dir']);

			natcasesort($files);
			if( count($files) > 2 ) { /* The 2 accounts for . and .. */
				//All dirs
				foreach( $files as $file ) {
					if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
						$directories[] = htmlentities($file);
						}    
				}         
				// All files
				foreach( $files as $file ) {
					if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
						$ext = preg_replace('/^.*\./', '', $file);    
						$base_files[] = htmlentities($file);
					}
				}
			}
			$response['success'] = true;
			$response['directories'] = $directories;
			$response['files'] = $base_files;

		}else{
			$response['success'] = false;
			$response['message'] = "File not exists in server";

		} 
		echo wp_json_encode($response);  
		wp_die();
	}


	/**
	 * Upload file from server.
	 * @param string $file_url - file url or path to file 
	 */
    public function upload_server($file_url){

        $filename = basename($file_url);
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		if(empty($file_extension)){
			$file_extension = 'xml';
		}
        global $wpdb;

        $validate_instance = ValidateFile::getInstance();
        $zip_instance = ZipHandler::getInstance();
        $validate_format = $validate_instance->validate_file_format($filename);

        if($validate_format == 'yes'){

            $upload_dir = ServerUpload::$smack_csv_instance->create_upload_dir();
            if($upload_dir){
                $event_key = ServerUpload::$smack_csv_instance->convert_string2hash_key($filename);

                if($file_extension == 'zip'){
                    $zip_response = [];
                    
                    $path = $upload_dir . $event_key . '.zip';
                    $extract_path = $upload_dir . $event_key;

                    $fp = @fopen($file_url, 'r');
                    $file_read = @fread($fp, @filesize($file_url));
                    @fclose($fp);
                    $fp1 = @fopen($path ,'w');
                    if(@fwrite($fp1, $file_read)){
                        chmod($path, 0777);

                        $zip_response['success'] = true;
                        $zip_response['filename'] =  $filename;
                        $zip_response['file_type'] = 'zip';
                        $zip_response['info'] = $zip_instance->zip_upload($path , $extract_path);

                    }else{
                        $zip_response['success'] = false;
                        $zip_response['message'] = "Cannot download zip file from server";

                    }    
                    echo wp_json_encode($zip_response); 
                    wp_die();
                }

                $upload_dir_path = $upload_dir. $event_key;
                if (!is_dir($upload_dir_path)) {
                    wp_mkdir_p( $upload_dir_path);
                }
                chmod($upload_dir_path, 0777);
    
                $file_table_name = $wpdb->prefix ."smackcsv_file_events";
            
                $wpdb->insert( $file_table_name , array('file_name' => $filename , 'hash_key' => $event_key , 'status' => 'Downloading', 'lock' => true) );
                $last_id = $wpdb->get_results("SELECT id FROM $file_table_name ORDER BY id DESC LIMIT 1",ARRAY_A);
                $lastid = $last_id[0]['id'];

                $path = $upload_dir. $event_key.'/'.$event_key;
        
                $fp = @fopen($file_url, 'r');
				$file_read = @fread($fp, @filesize($file_url));
				@fclose($fp);
				$fp1 = @fopen($path ,'w');
				if(@fwrite($fp1, $file_read)){
					chmod($path, 0777);

					$validate_file = $validate_instance->file_validation($path , $file_extension);

					$file_size = filesize($path);
		            $filesize = $validate_instance->formatSizeUnits($file_size);

					if($validate_file == "yes"){
						$wpdb->get_results("UPDATE $file_table_name SET status='Downloaded',`lock`=false WHERE id = '$lastid'");

						@fclose($fp1);

						$get_result = $validate_instance->import_record_function($event_key , $filename);
						$response['success'] = true;
						$response['filename'] = $filename;
						$response['hashkey'] = $event_key;
						$response['posttype'] = $get_result['Post Type'];
						$response['taxonomy'] = $get_result['Taxonomy'];
						$response['selectedtype'] = $get_result['selected type'];
						$response['file_type'] = $file_extension;
						$response['file_size'] = $filesize;
						$response['message'] = 'success';
						echo wp_json_encode($response); 

					}else{
						$response['success'] = false;
						$response['message'] = $validate_file;
						echo wp_json_encode($response); 
						unlink($path);
						$wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
					}
				}else{
					$response['success'] = false;
					$response['message'] = "Cannot download the file from server";
					echo wp_json_encode($response); 
					$wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
				}
			}else{
				$response['success'] = false;
				$response['message'] = "Please create Upload folder with writable permission";
				echo wp_json_encode($response);
			}
		}else{
			$response['success'] = false;
			$response['message'] = $validate_format;
			echo wp_json_encode($response); 
		}
		wp_die();
	}

}
