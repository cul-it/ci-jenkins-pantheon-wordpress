<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackUCITemplateManager
{
	public function templateActions($template_actions,$fileData = null,$id){
		switch($template_actions){
			case 'edit':
				$this->editTemplate($fileData,$id);
				break;
			case 'delete':
				$this->deleteTemplate($id);
				break;
		}
	}
	public function editTemplate($fileData){
		global $wpdb;
		global $uci_admin;
		$importAs = '';
		$eventkey = $fileData['eventkey'];
		parse_str($fileData['postdata'],$mapped_data);

		$template_data = $wpdb->get_col($wpdb->prepare("select module from wp_ultimate_csv_importer_mappingtemplate where eventKey = %s",$eventkey));
		$available_group = $uci_admin->available_widgets($template_data[0],$importAs);
		foreach ($available_group as $groupname => $groupvalue) {
			foreach ($mapped_data as $mapping_key => $mapping_value) {
				$current_mapped_group_mapkey = explode($groupvalue . '__mapping', $mapping_key);
				$current_mapped_group_key = explode($groupvalue . '__fieldname', $mapping_key);
				$current_static_group_key = explode($groupvalue . '_statictext_mapping', $mapping_key);
				$current_formula_group_key = explode($groupvalue . '_formulatext_mapping', $mapping_key);
				if (is_array($current_mapped_group_mapkey) && count($current_mapped_group_mapkey) == 2) {
					$set_mapping_groups[$groupvalue][] = $mapping_value;
				}
				if (is_array($current_mapped_group_key) && count($current_mapped_group_key) == 2) {
					$set_fields_group[$groupvalue][] = $mapping_value;
					$current_row_val = $mapping_value;
				}
				//static and formula features
				if(is_array($current_static_group_key) && count($current_static_group_key) == 2){
					$set_static_group[$groupvalue][$current_row_val] = $mapping_value;
				}
				if(is_array($current_formula_group_key) && count($current_formula_group_key) == 2){
					$set_formula_group[$groupvalue][$current_row_val] = $mapping_value;
				}

			}
			if (!empty($set_fields_group[$groupvalue]) && !empty($set_mapping_groups[$groupvalue])) {
				$new_mapped_array[$groupvalue] = array_combine($set_fields_group[$groupvalue], $set_mapping_groups[$groupvalue]);
			}
			//static and formula features
			if(!empty($set_static_group[$groupvalue] )) {
				foreach ($set_static_group[$groupvalue] as $grp => $val) {
					if (array_key_exists($grp, $new_mapped_array[$groupvalue])) {
						$new_mapped_array[$groupvalue][$grp] = $val;
					}
				}
			}
			if(!empty($set_formula_group[$groupvalue] )) {
				foreach ($set_formula_group[$groupvalue] as $grp => $val) {
					if (array_key_exists($grp, $new_mapped_array[$groupvalue])) {
						$new_mapped_array[$groupvalue][$grp] = $val;
					}
				}
			}
		}
		$update_fields = array(
			'mapping' => maybe_serialize($new_mapped_array),
			'createdtime' => date('Y-m-d h:i:s'),
			'templatename' => $mapped_data['templatename'],
		);
		if($mapped_data['templatename'] == ''){
			array_pop($update_fields);
		}
		$update_result = $wpdb->update('wp_ultimate_csv_importer_mappingtemplate',$update_fields,array('eventKey' => $eventkey));
		if($update_result) {
			$template_msg['notification'] = "Updated Successfully";
			$template_msg['notificationclass'] = 'alert alert-success';
		}
		else {
			$template_msg['notification'] = 'Id not passed.';
			$template_msg['notificationclass'] = 'alert alert-danger';
		}
		print_r(json_encode($template_msg));
		die;
	}

	public function deleteTemplate($id){

		global $wpdb;
		if($this->checkTemplate_scheduled($id)){
			// Check whether scheduled template count want to show or not. In pro Count was showed but not here.
			$return_message['msg'] = 'Template assigned to Scheduler. So cant delete it.';
			$return_message['msgclass'] = 'danger';
			print_r(json_encode($return_message));
			die;
		}
		$delete_response = $wpdb->delete('wp_ultimate_csv_importer_mappingtemplate',array('id' => $id));
		if($delete_response){
			$return_message['msg'] = 'Deleted Successfully';
			$return_message['msgclass'] = 'success';
		}
		else {
			$return_message['msg'] = 'Error occured while deleting';
			$return_message['msgclass'] = 'danger';
		}
		print_r(json_encode($return_message));
		die;
	}

	public function checkTemplate_scheduled($id){
		global $wpdb;
		$get_templateDetails = $wpdb->get_results($wpdb->prepare("select count(*) from wp_ultimate_csv_importer_scheduled_import where templateid = %d and isrun = %d",$id,0));
		foreach($get_templateDetails[0] as $key => $value) {
			if($value != "0"){
				return true;
			}
			else{
				return false;
			}
		}}
}
global $templateObj;
$templateObj = new SmackUCITemplateManager();
