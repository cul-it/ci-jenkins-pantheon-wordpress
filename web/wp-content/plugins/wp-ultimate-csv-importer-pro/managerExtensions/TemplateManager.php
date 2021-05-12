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

class TemplateManager {

    private static $instance = null;
    private static $smack_csv_instance = null;

    public function __construct(){
        add_action('wp_ajax_displayTemplates',array($this,'display_templates'));
        add_action('wp_ajax_saveTemplate',array($this,'save_template'));
        add_action('wp_ajax_deleteTemplate',array($this,'delete_template'));
    }

    public static function getInstance() {
		if (TemplateManager::$instance == null) {
			TemplateManager::$instance = new TemplateManager;
            TemplateManager::$smack_csv_instance = SmackCSV::getInstance();
			return TemplateManager::$instance;
		}
		return TemplateManager::$instance;
    }


    /**
	 * Save template details.
	 */
    public function save_template(){
        global $wpdb;
        $type          = $_POST['Types'];
		$map_fields    = $_POST['MappedFields'];	
        $template_name = $_POST['TemplateName'];
        $new_template_name = $_POST['NewTemplate'];
        $mapping_type = $_POST['MappingType'];
        $response = [];
        $template_table_name = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";
        $get_detail   = $wpdb->get_results( "SELECT eventKey FROM $template_table_name WHERE templatename = '$template_name'" );
        $hash_key = $get_detail[0]->eventKey;        
        $mapped_fields = str_replace( "\\", "", $map_fields );
        $mapped_fields = json_decode( $mapped_fields, true );
        $mapping_fields = serialize( $mapped_fields );
        $time = date('Y-m-d h:i:s');
        $wpdb->get_results("UPDATE $template_table_name SET templatename = '$new_template_name' , mapping ='$mapping_fields' , createdtime = '$time' , module = '$type' , mapping_type = '$mapping_type' WHERE eventKey = '$hash_key'");	
        $response['success'] = true;
		echo wp_json_encode($response); 	
		wp_die();
    }


    /**
	 * Deletes Template.
	 */
    public function delete_template(){
        $template_name = $_POST['TemplateName'];
    
        $return_message = [];
        global $wpdb;

        $template_table_name = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";
        $get_detail   = $wpdb->get_results( "SELECT id FROM $template_table_name WHERE templatename = '$template_name'" );
        $id = $get_detail[0]->id;

		if($this->checkTemplate_scheduled($id)){
			// Check whether scheduled template count want to show or not. In pro Count was showed but not here.
			$return_message['message'] = 'Template assigned to Scheduler. So can not delete it.';
			$return_message['success'] = false;
			echo wp_json_encode($return_message); 	
		    wp_die();
		}
		$delete_response = $wpdb->delete($template_table_name ,array('id' => $id));
		if($delete_response){
			$return_message['message'] = 'Deleted Successfully';
			$return_message['success'] = true;
		}
		else {
			$return_message['message'] = 'Error occured while deleting';
			$return_message['success'] = false;
		}
		echo wp_json_encode($return_message); 	
		wp_die();
    }


    /**
	 * Checks whether a template has been scheduled.
	 * @param  int $id - template id
	 * @return boolean
	 */
    public function checkTemplate_scheduled($id){
		global $wpdb;
		$get_templateDetails = $wpdb->get_results($wpdb->prepare("select count(*) from {$wpdb->prefix}ultimate_csv_importer_scheduled_import where templateid = %d and isrun = %d",$id,0));
		foreach($get_templateDetails[0] as $key => $value) {
			if($value != "0"){
				return true;
			}
			else{
				return false;
			}
        }
    }


    /**
	 * Retrieves and display template details
	 */
    public function display_templates(){
        global $wpdb;
        $response = [];
        $details = [];
        $info = [];        
        $template_table_name = $wpdb->prefix."ultimate_csv_importer_mappingtemplate";
		$get_result = $wpdb->get_results("SELECT templatename , createdtime , module FROM $template_table_name WHERE templatename != '' order by id desc ");
       
        if(!empty($get_result)){
            foreach($get_result as $value){
                $template_name = $value->templatename;    
                $created_time = $value->createdtime;
                $module = $value->module;
                
                $details['template_name'] = $template_name;
                $details['module'] = $module;
                $details['created_time'] = $created_time;   
                array_push($info , $details);
                
            }
            $response['success'] = true;
			$response['info'] = $info;
        }else{
            $response['success'] = false;
            $response['message'] = "No Templates Found";
        }
        echo wp_json_encode($response);
		wp_die();
    }
}
