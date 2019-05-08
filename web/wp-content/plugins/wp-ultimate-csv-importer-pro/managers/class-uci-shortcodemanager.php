<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCIShortcodeManager
{
    /**
     * @param $shortcode_actions
     */
    public function shortcodeActions($shortcode_actions){
        switch ($shortcode_actions){
            case 'populate':
                break;
            case 'update':
                break;
        }
    }

    /**
     * @param $id
     * @param $filekey
     */
    public function populateImage($id,$filekey){
        global $wpdb;
        $get_shortcodeMode = $wpdb->get_col($wpdb->prepare("select shortcode_mode from wp_ultimate_csv_importer_shortcode_statusrel where eventkey = %s and id = %d",$filekey,$id));
        $get_shortcodeDetails = $wpdb->get_results($wpdb->prepare("select pID,shortcode from wp_ultimate_csv_importer_manageshortcodes where eventkey = %s and mode_of_code =%s",$filekey,$get_shortcodeMode['shortcode_mode']));
        foreach($get_shortcodeDetails as $shortcode_data) {
            $shortcodeList[$shortcode_data->pID] = $shortcode_data->shortcode;
        }
        if(!empty($shortcodeList)) {
            foreach($shortcodeList as $post_id => $image_shortcodes ) {
                $get_content = $wpdb->get_col($wpdb->prepare("select post_content from $wpdb->posts where ID = %d",$post_id));
                foreach($image_shortcodes as $shortcodes) {
                    if($get_shortcodeMode['shortcode_mode'] == 'Inline'){
                        $inline_imageData = substr($shortcodes,"13",-1);
                        $image_attributes = explode('|',$inline_imageData);
                        $imageData = $image_attributes[0];
                    }
                    else if($get_shortcodeMode['shortcode_mode'] == 'Featured') {
                        $imageData = substr($shortcodes,"15",-1);
                    }
                    else {
                        return false;
                    }
                    $imageList = $this->scanDirectories(SM_UCI_INLINE_IMAGE_DIR . $filekey);
                    $imageDir = $this->upload_imageDir();
                    if($imageList) {
                        foreach($imageList as $imageLocation) {
                            $imageLocation = strpos($imageLocation,$imageData) ? $imageLocation : '';
                        }
                        $currentLocation = explode($filekey,$imageLocation);
                        $currentLocation = isset($currentLocation[1]) ? $currentLocation[1] : '';
                        $imageLocation = SM_UCI_INLINE_IMAGE_DIR . '/' . $filekey . $currentLocation;
                        $this->get_images_from_url($imageLocation,$imageDir['path'],$imageData,'');
                        if(@getimagesize($imageDir['path'] . '/' . $imageData)){
                            // Check $imageSize is needed or not
                            $imageSize = $this->generate_imageSize($imageDir['path'] . '/' . $imageData);
                            $image_fileData['guid'] = $imageDir['url'] . '/' . $imageData;
                            $attachment_data = array('guid' => $image_fileData['guid'], 'post_mime_type' => 'image/jpeg', 'post_title' => preg_replace('/\.[^.]+$/','',@basename($image_fileData['guid'])), 'post_content' => '', 'post_status' => 'inherit');
                            $attachment_path = $imageDir['path'] . '/' . $imageData;
                            $existing_imageId = $this->get_existingImagelist($attachment_data);
                            if($existing_imageId){
                                if($get_shortcodeMode['shortcode_mode'] == 'Featured') {
                                    $recordId = $wpdb->get_col($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_type = %s",$attachment_data['post_title'],'attachment'));
                                    set_post_thumbnail($post_id,$recordId['ID']);
                                }
                                else if($get_shortcodeMode['shortcode_mode'] == 'Inline') {
                                    $image_attribute1 = isset($image_attributes[1]) ? $image_attributes[1] : '';
                                    $image_attribute2 = isset($image_attributes[2]) ? $image_attributes[2] : '';
                                    $image_attribute3 = isset($image_attributes[3]) ? $image_attributes[3] : '';
                                    $imagetag = '<img src="' . $image_fileData['guid'] . '" '.$image_attribute1.' '.$image_attribute2.' '.$image_attribute3.' />';
                                    $post_content = str_replace($shortcodes,$imagetag,$get_content['post_content']);
                                }
                                $default_image = 2;
                                $wpdb->update('wp_ultimate_csv_importer_manageshortcodes',array('populate_status' => 0),array('pID' => $post_id,'shortcode' => $shortcodes,'mode_of_code' => $get_shortcodeMode['shortcode_mode']));
                            }
                            else {
                                $insert_image = $this->insertImage($attachment_data,$attachment_path,$post_id);
                            }
                        }
                        else{
                            return "Image not replaced";
                        }

                    }
                    else {
                        $imagetag = '<img src = "'.SM_UCI_PRO_DIR . 'images/noimage.png" />';
                        $post_content = str_replace($shortcodes,$imagetag,$get_content['post_content']);
                        $wpdb->update('wp_ultimate_csv_importer_manageshortcodes',array('populate_status' => 2), array('pID' => $post_id, 'shortcode' => $shortcodes,'mode_of_code' => $get_shortcodeMode['shortcode_mode'] ));
                        $default_image = 1;
                    }
                }
                if($get_shortcodeMode['shortcode_mode'] == 'Inline') {
                    $update_post['ID'] = $post_id;
                    $update_post['post_content'] = $post_content;
                    wp_update_post($update_post);
                }
            }
        }
        $status = $this->updateImage($id,$filekey,$get_shortcodeMode);
        echo $status;
    }

    /**
     * @param $id
     * @param $eventkey
     * @param $mode
     * @param $default_image
     * @return string
     */
    public function updateImage($id,$eventkey,$mode,$default_image) {
        global $wpdb;
        $get_replacedId = $wpdb->get_col($wpdb->prepare("select count(*) as nonreplaced from wp_ultimate_csv_importer_manageshortcodes where eventkey = %s and mode_of_code = %s and populate_status = %d",$eventkey,$mode['shortcode_mode'],1));
        $get_noimageId = $wpdb->get_col($wpdb->prepare("select count(*) as nonreplaced from wp_ultimate_csv_importer_manageshortcodes where eventkey = %s and mode_of_code = %s and populate_status = %d",$eventkey,$mode['shortcode_mode'],2));
        if($default_image == 2 && $get_replacedId['nonreplaced'] == 0 ){
            $wpdb->update('wp_ultimate_csv_importer_shortcodes_statusrel', array('current_status' => 'Replaced'), array('id' => $id, 'eventkey' => $eventkey));
            return '1';
        }
        else {
            $wpdb->update('wp_ultimate_csv_importer_shortcodes_statusrel', array('current_status' => 'Partially'), array('id' => $id, 'eventkey' => $eventkey));
            return '2';
        }
        if($default_image == 1 && $get_noimageId['nonreplaced'] != 0) {
           return 'Images Not Available';
        }
    }

    /**
     * @param $imagedata
     * @param $imagePath
     * @param $postid
     * @return string
     */
    public function insertImage($imagedata,$imagePath,$postid) {
        $attachId = wp_insert_attachment($imagedata,$imagePath,$postid);
        $attachData = wp_generate_attachment_metadata($attachId,$imagePath);
        wp_update_attachment_metadata($attachId,$attachData);
        set_post_thumbnail($postid,$attachId);
        return "Image Successfully replaced";
    }

    /**
     * @param $imagedata
     * @return array
     */
    public function get_existingImagelist($imagedata){
        global $wpdb;
        $image_title = $imagedata['post_title'];
        $image_id = $wpdb->get_col($wpdb->prepare("select ID from $wpdb->posts where post_type = %s and post_mime_type = %s and post_title = %s,'attachment','image/jpeg',$image_title"));
        return $image_id;
    }

    /**
     * @param $path
     * @return array
     */
    public function generate_imageSize($path){
        // Check whether this function is needed or not.
        $image_data = wp_get_image_editor($path);
        if(!is_wp_error($image_data)) {
           $image_size = array(
               array('width' => 1024, 'height' => 768, 'crop' => true),
               array('width' => 100, 'height' => 100, 'crop' => false),
               array('width' => 300, 'height' => 100, 'crop' => false),
               array('width' => 624, 'height' => 468, 'crop' => false)
           );
           $image_size =  $image_data->multi_resize($image_size);
            return $image_size;
        }
    }

    /**
     * @return mixed
     */
    public function upload_imageDir()
    {
        $uploadDir =  wp_upload_dir();
        $get_mediaSettings = get_option('uploads_use_yearmonth_folders');
        if($get_mediaSettings) {
            $directoryname = date('Y') . '/' . date('m');
            $imageDir['path'] = $uploadDir['basedir'] . '/' . $directoryname;
            $imageDir['url'] = $uploadDir['baseurl'] . '/' . $directoryname;
        }
        else {
            $imageDir['path'] = $uploadDir['basedir'];
            $imageDir['url'] = $uploadDir['baseurl'];
        }
        return $imageDir;
    }

    /**
     * @param $rootDir
     * @param array $allData
     * @return array|bool
     */
    public function scanDirectories($rootDir, $allData=array()) {
        // set filenames invisible if you want
        $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
        // run through content of root directory
        if(!is_dir($rootDir))
            return false;
        $dirContent = scandir($rootDir);
        foreach($dirContent as $key => $content) {
            // filter all files not accessible
            $path = $rootDir.'/'.$content;
            if(!in_array($content, $invisibleFileNames)) {
                if(is_file($path) && is_readable($path)) {
                    // save file name with path
                    $allData[] = $path;
                    // if content is a directory and readable, add path and name
                }
                elseif(is_dir($path) && is_readable($path)) {
                    // recursive callback to open new directory
                    $allData = scanDirectories($path, $allData);
                }
            }
        }
        return $allData;
    }
}