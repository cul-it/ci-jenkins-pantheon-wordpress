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

class SftpUpload implements Uploads{

    private static $instance = null;
    private static $smack_csv_instance = null;

    private function __construct(){
            add_action('wp_ajax_get_sftp_url',array($this,'upload_function'));
            add_action('wp_ajax_get_sftp_details',array($this,'getSftpDetails'));            
    }

    public static function getInstance() {
        if (SftpUpload::$instance == null) {
            SftpUpload::$instance = new SftpUpload;
            SftpUpload::$smack_csv_instance = SmackCSV::getInstance();
            return SftpUpload::$instance;
        }
        return SftpUpload::$instance;
    }
    

    /**
	 * Upload file from SFTP.
	 */
    public function upload_function(){
        $host_name = $_POST['HostName'];
        $host_port = $_POST['HostPort'];
        $host_username = $_POST['HostUserName'];
        $host_password = $_POST['HostPassword'];
        $host_path = $_POST['HostPath'];
        $action = $_POST['action'];
        update_option('sm_sftp_hostname', $host_name);
        update_option('sm_sftp_hostport', $host_port);
        update_option('sm_sftp_hostusername', $host_username);
        update_option('sm_sftp_hostpath', $host_path);
        update_option('sm_sftp_hostpassword', $host_password);
        update_option('action', $action);

        global $wpdb;

        $file_table_name = $wpdb->prefix ."smackcsv_file_events";
    
        $connection = ssh2_connect($host_name, $host_port);
        $response = [];
        if(!$connection = ssh2_connect($host_name, $host_port)){
            $response['success'] = false;
            $response['message'] = 'Failed to connect';
            echo wp_json_encode($response);
        }else{
            $name = basename($host_path);
            $file_extension = pathinfo($name, PATHINFO_EXTENSION);

            $validate_instance = ValidateFile::getInstance();
            $zip_instance = ZipHandler::getInstance();
            $validate_format = $validate_instance->validate_file_format($name);

            if($validate_format == 'yes'){
                $upload_dir = SftpUpload::$smack_csv_instance->create_upload_dir();
                if($upload_dir){

                    if(ssh2_auth_password($connection, $host_username, $host_password)){
                        $sftp = ssh2_sftp($connection);
                        if($sftp){
                            $event_key = SftpUpload::$smack_csv_instance->convert_string2hash_key($name);
                           
                            if($file_extension == 'zip'){
                                $zip_response = [];
                
                                $path = $upload_dir . $event_key . '.zip';
                                $extract_path = $upload_dir . $event_key;
                                $server_file = $host_path;
                            
                                $resFile = fopen("ssh2.sftp://{$sftp}/".$server_file, 'r');
                                $srcFile = fopen($path, 'w');
                                $writtenBytes = stream_copy_to_stream($resFile, $srcFile);
                                fclose($resFile);
                                fclose($srcFile);

                                if($writtenBytes !== FALSE)
                                {
                                    chmod($path, 0777);
                                    $zip_response['success'] = true;
                                    $zip_response['filename'] = $name;
                                    $zip_response['file_type'] = 'zip';
                                    $zip_response['info'] = $zip_instance->zip_upload($path , $extract_path);
                                }else{
                                    $zip_response['success'] = false;
                                    $zip_response['info'] = 'Cannot Download the file';
                                }
                                echo wp_json_encode($zip_response); 
                                wp_die();
                            }

                            $upload_dir_path = $upload_dir. $event_key;
                            if (!is_dir($upload_dir_path)) {
                                wp_mkdir_p( $upload_dir_path);
                            }
                            chmod($upload_dir_path, 0777);

                            $wpdb->insert( $file_table_name , array( 'file_name' => $name , 'hash_key' => $event_key , 'status' => 'Downloading' , 'lock' => true ) );
                            $last_id = $wpdb->get_results("SELECT id FROM $file_table_name ORDER BY id DESC LIMIT 1",ARRAY_A);
                            $lastid=$last_id[0]['id'];
                            $local_file = $upload_dir.$event_key.'/'.$event_key;
                            $remote_file = $host_path; 

                            $resFile = fopen("ssh2.sftp://{$sftp}/".$remote_file, 'r');

                            $srcFile = fopen($local_file, 'w');
                            $writtenBytes = stream_copy_to_stream($resFile, $srcFile);
                            fclose($resFile);
                            fclose($srcFile);

                            if($writtenBytes !== FALSE)
                            {
                                chmod($local_file, 0777);
                                $validate_file = $validate_instance->file_validation($local_file, $file_extension);

                                $file_size = filesize($local_file);
		                        $filesize = $validate_instance->formatSizeUnits($file_size);

                                if($validate_file == "yes"){

                                    $wpdb->get_results("UPDATE $file_table_name SET status='Downloaded',`lock`=false WHERE id = '$lastid'");
                                    $get_result = $validate_instance->import_record_function($event_key , $name);
                                    $response['success'] = true;
                                    $response['filename'] = $name;
                                    $response['hashkey'] = $event_key;
                                    $response['posttype'] = $get_result['Post Type'];
                                    $response['taxonomy'] = $get_result['Taxonomy'];
                                    $response['selectedtype'] = $get_result['selected type'];
                                    $response['file_type'] = $file_extension;
                                    $response['file_size'] = $filesize;
                                    $response['message'] = 'Downloaded Successfully';
                                    
                                }else{
                                    $response['success'] = false;
                                    $response['message'] = $validate_file;
                                    
                                    unlink($path);
                                    $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                                }
                            }else{
                                $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                                $response['success'] = false;
                                $response['message'] = 'Cannot Download the file';
                                
                            }
                        }else{
                            $response['success'] = false;
                            $response['message'] = "Could not initialize SFTP subsystem.";
                            
                        }
                    }else{
                        $response['success'] = false;
                        $response['message'] = "Could not authenticate with username and password ";
                        
                    }
                }else{
                    $response['success'] = false;
                    $response['message'] = "Please create Upload folder with writable permission";
                    
                }
            }else{
                $response['success'] = false;
                $response['message'] = $validate_format;
               
            }
        }
        echo wp_json_encode($response);
        wp_die();
    }

    public function getSftpDetails(){
        $result['HostName'] = get_option('sm_sftp_hostname');
        $result['HostPort'] = get_option('sm_sftp_hostport');
        $result['HostUserName'] = get_option('sm_sftp_hostusername');
        $result['HostPath'] = get_option('sm_sftp_hostpath');
        $result['HostPassword'] = get_option('sm_sftp_hostpassword');
        $result['action'] = get_option('action');
        echo wp_json_encode($result);
        wp_die();
    }


    public function sftp_upload($data = null){

        $host_name = $data['HostName'];
        $host_port = $data['HostPort'];
        $host_username = $data['HostUserName'];
        $host_password = $data['HostPassword'];
        $host_path = $data['HostPath'];
        global $wpdb;

        $file_table_name = $wpdb->prefix ."smackcsv_file_events";
    
        $connection = ssh2_connect($host_name, $host_port);
        $response = [];
        if(!$connection = ssh2_connect($host_name, $host_port)){
            $response['success'] = false;
            $response['message'] = 'Failed to connect';
            echo wp_json_encode($response);
        }else{
            $name = basename($host_path);
            $file_extension = pathinfo($name, PATHINFO_EXTENSION);

            $validate_instance = ValidateFile::getInstance();
            $zip_instance = ZipHandler::getInstance();
            $validate_format = $validate_instance->validate_file_format($name);

            if($validate_format == 'yes'){
                $upload_dir = SftpUpload::$smack_csv_instance->create_upload_dir();
                if($upload_dir){

                    if(ssh2_auth_password($connection, $host_username, $host_password)){
                        $sftp = ssh2_sftp($connection);
                        if($sftp){
                            $event_key = SftpUpload::$smack_csv_instance->convert_string2hash_key($name);
                            
                            if($file_extension == 'zip'){
                                $zip_response = [];
                
                                $path = $upload_dir . $event_key . '.zip';
                                $extract_path = $upload_dir . $event_key;
                                $server_file = $host_path;
                            
                                $resFile = fopen("ssh2.sftp://{$sftp}/".$server_file, 'r');
                                $srcFile = fopen($path, 'w');
                                $writtenBytes = stream_copy_to_stream($resFile, $srcFile);
                                fclose($resFile);
                                fclose($srcFile);

                                if($writtenBytes !== FALSE)
                                {
                                    chmod($path, 0777);
                                    $zip_response['success'] = true;
                                    $zip_response['filename'] = $name;
                                    $zip_response['file_type'] = 'zip';
                                    $zip_response['info'] = $zip_instance->zip_upload($path , $extract_path);
                                }else{
                                    $zip_response['success'] = false;
                                    $zip_response['info'] = 'Cannot Download the file';
                                }
                                echo wp_json_encode($zip_response); 
                                wp_die();
                            }

                            $upload_dir_path = $upload_dir. $event_key;
                            if (!is_dir($upload_dir_path)) {
                                wp_mkdir_p( $upload_dir_path);
                            }
                            chmod($upload_dir_path, 0777);

                            $mode_of_action = $wpdb->get_var("SELECT mode FROM $file_table_name where file_name = '".$name."' order by id desc limit 1");
					
                            $wpdb->insert( $file_table_name , array( 'file_name' => $name , 'hash_key' => $event_key , 'status' => 'Downloading' , 'lock' => true, 'mode' => $mode_of_action ) );
                            $last_id = $wpdb->get_results("SELECT id FROM $file_table_name ORDER BY id DESC LIMIT 1",ARRAY_A);
                            $lastid=$last_id[0]['id'];
                            $local_file = $upload_dir.$event_key.'/'.$event_key;
                            $remote_file = $host_path;

                            $resFile = fopen("ssh2.sftp://{$sftp}/".$remote_file, 'r');

                            $srcFile = fopen($local_file, 'w');
                            $writtenBytes = stream_copy_to_stream($resFile, $srcFile);
                            fclose($resFile);
                            fclose($srcFile);

                            if($writtenBytes !== FALSE)
                            {
                                chmod($local_file, 0777);
                                $validate_file = $validate_instance->file_validation($local_file, $file_extension);

                                $file_size = filesize($local_file);
		                        $filesize = $validate_instance->formatSizeUnits($file_size);

                                if($validate_file == "yes"){

                                    $wpdb->get_results("UPDATE $file_table_name SET status='Downloaded',`lock`=false WHERE id = '$lastid'");
                                    $get_result = $validate_instance->import_record_function($event_key , $name);
                                    $response['success'] = true;
                                    $response['filename'] = $name;
                                    $response['hashkey'] = $event_key;
                                    $response['posttype'] = $get_result['Post Type'];
                                    $response['taxonomy'] = $get_result['Taxonomy'];
                                    $response['selectedtype'] = $get_result['selected type'];
                                    $response['file_type'] = $file_extension;
                                    $response['file_size'] = $filesize;
                                    $response['message'] = 'Downloaded Successfully';
                                    
                                }else{
                                    $response['success'] = false;
                                    $response['message'] = $validate_file;
                                    
                                    unlink($path);
                                    $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                                }
                            }else{
                                $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                                $response['success'] = false;
                                $response['message'] = 'Cannot Download the file';
                                
                            }
                        }else{
                            $response['success'] = false;
                            $response['message'] = "Could not initialize SFTP subsystem.";
                            
                        }
                    }else{
                        $response['success'] = false;
                        $response['message'] = "Could not authenticate with username and password ";
                        
                    }
                }else{
                    $response['success'] = false;
                    $response['message'] = "Please create Upload folder with writable permission";
                    
                }
            }else{
                $response['success'] = false;
                $response['message'] = $validate_format;
               
            }
        }
        return $response;
    }

}