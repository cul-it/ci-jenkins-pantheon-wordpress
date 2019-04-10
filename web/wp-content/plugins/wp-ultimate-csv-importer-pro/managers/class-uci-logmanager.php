<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCILogManager
{
    /**
     * @param $fileid
     */
    public function logDownload($fileid){
        global $wpdb;
        global $fileObj;
        $pathList = array();
        $recordDetails = $wpdb->get_results($wpdb->prepare("select *from smackuci_events where id = %d", $fileid['id']));
        foreach($recordDetails as $record_data) {
            $filepath = maybe_unserialize($record_data->revision);
            foreach($filepath as $path) {
                $key_data = explode('/',$path);
                $key_data = $key_data[count($key_data) - 1];
                $eventkey = str_replace('.txt','',$key_data);
                $log_path = SM_UCI_LOG_DIR . $eventkey;
                if(file_exists($log_path)) {
                    $pathList[] = $log_path;
                }
            }
        }
        $zip_path = SM_UCI_LOG_DIR . $recordDetails[0]->filename . 'zip';
        if(file_exists($zip_path)) :
            unlink($zip_path);
        endif;
        $zipped_file = $fileObj->create_zip($pathList, $zip_path , false);
        if($zipped_file) :
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=" . basename($zip_path));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zip_path);
            die;
        else :
            $msg['notice'] =  "File Not Exists";
            print_r(json_encode($msg));
            die;
        endif;
    }

}
global $log_managerObj;
$log_managerObj = new SmackUCILogManager();