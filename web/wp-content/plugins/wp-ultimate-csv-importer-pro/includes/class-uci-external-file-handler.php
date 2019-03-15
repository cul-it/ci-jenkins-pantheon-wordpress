<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCIExternal_FileHandler {

	public static function init(){
		$fileurl = $_POST['postdata'][0]['external_file_url'];
		$import_method = $_POST['postdata'][0]['import_method'];
		$result = self::ExternalFile_Handling($fileurl, $import_method);
		echo json_encode($result);
		die();
	}

	/**
	 * ExternalFile_Handling - Returns the CSV header
	 * @param $file_url
	 * @param $import_method
	 *
	 * @return mixed
	 */
	public static function ExternalFile_Handling($file_url, $import_method) {
		global $uci_admin;
		global $wpdb;
		if(!strstr($file_url, 'https://www.dropbox.com/')) {

			$file_url   = self::get_original_url($file_url);
		}
		try{
			$get_local_filename = array();
			# Google Drive File URL - Get file ID
			if(strstr($file_url, 'https://docs.google.com/')) {
				$get_file_id = explode('/', $file_url);
				$external_file = 'google-sheet-' . $get_file_id[count($get_file_id) - 2];
				$file_extension = explode('output=', $get_file_id[count($get_file_id) - 1]);
				$file_extension = $file_extension[1];
				$file_extn = '.' . $file_extension;
				$external_file = $external_file . '.' . $file_extension;
				$get_local_filename[0] = $external_file;
			#Dropbox url(https://www.dropbox.com/s/k9zzx7mm1tj9rza/Posts.csv?dl=1)
			}elseif(strstr($file_url, 'https://www.dropbox.com/')) {
				$filename = basename($file_url);
				$get_local_filename = explode('?', $filename);
				$external_file = $get_local_filename[0];	
			}else { # Other URL's except google spreadsheets
				$external_file = basename($file_url);
				$file_extension = pathinfo($external_file, PATHINFO_EXTENSION);
				$file_extn = '.' . $file_extension;
				$get_local_filename = explode($file_extn, $external_file);
			}
			if ( ! preg_match('%\W(txt|csv|xml|zip)$%i', trim($external_file))) {
				throw new Exception('Unsupported file format');
			}

			$all_file_names = $wpdb->get_results($wpdb->prepare("select id, revision from smackuci_events where original_file_name = %s order by id desc limit 1", $external_file));
			if($all_file_names){
				if (is_array($all_file_names) && isset($all_file_names[0]->id)) {
					$last_version_id = $all_file_names[0]->revision;
					$version = $last_version_id + 1;
					$local_file_name = $get_local_filename[0] . '-' . $version . $file_extn;
				}
			} else {
				$local_file_name = $get_local_filename[0] . '-1' . $file_extn;
				$version = 1;
			}
			$event_key = $uci_admin->convert_string2hash_key($local_file_name);
			$local_dir = SM_UCI_IMPORT_DIR . '/' .$event_key;
			$local_file = SM_UCI_IMPORT_DIR . '/' . $event_key . '/' . $event_key;
			if(!is_dir($local_dir)) {
				wp_mkdir_p($local_dir);
			}
			if (isset($import_method) && $import_method == 'external_import' || $import_method == 'desktop' || $import_method == 'url' ||  $import_method == 'ftp' || $import_method == 'server'){
				$ch = curl_init($file_url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$rawdata = curl_exec($ch);
				$error = curl_error($ch);
				if (strpos($rawdata, 'Not Found') != 0) {
					$rawdata = false;
				}
				if ($rawdata != false) {
					$fp = @fopen($local_file, 'w');
					@fwrite($fp, $rawdata);
					@fclose($fp);
					$filesize = @filesize($local_file);
					if ($filesize > 1024 && $filesize < (1024 * 1024)) {
						$fileSize = round(($filesize / 1024), 2) . ' kb';
					} else {
						if ($filesize > (1024 * 1024)) {
							$fileSize = round(($filesize / (1024 * 1024)), 2) . ' mb';
						} else {
							$fileSize = $filesize . ' byte';
						}
					}
					//mime type check
					$totalrows = $uci_admin->get_total_rows($local_file);
					$mime = mime_content_type($local_file);
					// $filemimes = array('text/csv','text/plain');
     //                    		if(!in_array($mime,$filemimes)){
					// 	throw new Exception('Unsupported file format');
					// }
					//mime type check
					//file size check with upload_max_size
					$upload_max_size = $uci_admin->get_config_bytes(ini_get('upload_max_filesize'));
                        		if ($upload_max_size && ($filesize > $upload_max_size)) {
						throw new Exception('The uploaded file exceeds the upload_max_filesize directive in php.ini');
					}
					//file size check with upload_max_size
					$returnData['Success'] = 'Success!';
				} else {
					throw new Exception('File not found!');
				}
				curl_close($ch);
				//csv validation
                                if($file_extension == 'csv' || $file_extension == 'txt'){
                                      $valid_csv = $uci_admin->CheckCSV($local_file);
				      $returnData['isutf8'] = $valid_csv['isutf8'];
				      $returnData['isvalid'] = $valid_csv['isvalid'];
				      if($valid_csv['isvalid'] == 'No'){
					unset($returnData['Success']);
					throw new Exception('Your csv file columns and values are mismatch');
				      }
				}
				//csv validation
			} elseif(isset($import_method) && $import_method == 'server_import') {
				$fp = @fopen($file_url, 'r');
				$file_read = @fread($fp, @filesize($file_url));
				@fclose($fp);
				$fp1 = @fopen($local_file, 'w');
				@fwrite($fp1, $file_read);
				@fclose($fp1);
				$filesize = @filesize($local_file);
				if ($filesize > 1024 && $filesize < (1024 * 1024)) {
					$fileSize = round(($filesize / 1024), 2) . ' kb';
				} else {
					if ($filesize > (1024 * 1024)) {
						$fileSize = round(($filesize / (1024 * 1024)), 2) . ' mb';
					} else {
						$fileSize = $filesize . ' byte';
					}
				}
				//mime type check
				$mime = mime_content_type($local_file);
				$filemimes = array('text/csv','text/plain');
				// if(!in_array($mime,$filemimes)){
				// 	throw new Exception('Unsupported file format');
				// }
				//mime type check
				//file size check with upload_max_size
				$upload_max_size = $uci_admin->get_config_bytes(ini_get('upload_max_filesize'));
				if ($upload_max_size && ($filesize > $upload_max_size)) {
					throw new Exception('The uploaded file exceeds the upload_max_filesize directive in php.ini');
				}
				//file size check with upload_max_size
				//csv validation
                                if($file_extension == 'csv' || $file_extension == 'txt'){
                                      $valid_csv = $uci_admin->CheckCSV($local_file);
                                      $returnData['isutf8'] = $valid_csv['isutf8'];
                                      $returnData['isvalid'] = $valid_csv['isvalid'];
                                      if($valid_csv['isvalid'] == 'No'){
                                        throw new Exception('Your csv file columns and values are mismatch');
                                      }
                                }
                                //csv validation
				$returnData['Success'] = 'Success!';
			}
			$returnData['filename'] = $external_file;
			$returnData['uploaded_name'] = $local_file_name;
			$returnData['version'] = $version;
			$returnData['filesize'] = $fileSize;
			$returnData['extension'] = $file_extension;
			$returnData['eventkey'] = $event_key;
		}catch (Exception $e) {
			$returnData["Failure"] = $e->getMessage();
		}
		return $returnData;
	}

	public static function get_original_url($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_HEADER,true); // Get header information
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,false);
		$header = curl_exec($ch);

		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header)); // Parse information

		for($i=0;$i<count($fields);$i++)
		{
			if(strpos($fields[$i],'Location') !== false)
			{
				$url = str_replace("Location: ","",$fields[$i]);
			}
		}
		$url = str_replace(' ', '%20', $url);
		return $url;
	}
}

if(isset($_POST['postdata'])) {
	SmackUCIExternal_FileHandler::init();
}

