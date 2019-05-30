<?php
class SmackUCICMB2DataImport {

	public function push_cmb2_data($data_to_import) {
		global $uci_admin;
		$cmb2data = $data_to_import;
		$data_array = $cmb2data['CMB2'];
		if(!empty($cmb2data)) {
			if(in_array('cmb2/init.php', $uci_admin->get_active_plugins()) )  {
				$this->importDataCMB2Fields($data_array, $uci_admin->getImportAs(), $uci_admin->getLastImportId());
			}
		}
	}

	public function importDataCMB2Fields ($data_array, $importas, $pID) {
		$createdFields = $cmb2Data = array();
		global $uci_admin;
		$get_csvpro_settings = get_option('sm_uci_pro_settings');
		$prefix = $get_csvpro_settings['cmb2'];
		foreach ($data_array as $dkey => $dvalue) {
			$createdFields[] = $dkey;
			if($dkey == $prefix.'image'){
				$darray[$prefix.'image_id'] = $uci_admin->set_featureimage($dvalue, $pID);
				$darray[$prefix.'image'] = wp_get_attachment_url($darray[$prefix.'image_id']);
			}
			elseif($dkey == $prefix.'file_list'){
				$exploded_file_items = explode('|', $dvalue);
				foreach($exploded_file_items as $ekey => $evalue){
					$imageid =  $uci_admin->set_featureimage($evalue, $pID);
					$files[$imageid] = wp_get_attachment_url($imageid);
				}
				$darray[$prefix.'file_list'] = $files;
			}elseif($dkey == $prefix.'group_demo'){
				$exploded_group_items = explode('|', $dvalue);
				foreach($exploded_group_items as $gkey => $gvalue){
					$exploded_line_items = explode(',', $gvalue);
					$rep_group[$gkey]['title'] = $exploded_line_items[0];
					$rep_group[$gkey]['description'] = $exploded_line_items[1];
					$rep_group[$gkey]['image'] = $exploded_line_items[2];
					$rep_group[$gkey]['image_id'] = $uci_admin->set_featureimage($exploded_line_items[2], $pID);
					$rep_group[$gkey]['image_caption'] = $exploded_line_items[3];
				}
				$darray[$prefix.'group_demo'] = $rep_group;
			}
			elseif($dkey == $prefix.'multicheckbox'){
				$darray[$prefix.'multicheckbox'] = explode('|', $dvalue);
			}
			elseif($dkey == $prefix.'checkbox'){
				$darray[$prefix.'checkbox'] = explode('|', $dvalue);
			}
			else{
				$darray[$dkey] = $dvalue;
			}
		}
		if($darray){
			foreach($darray as $mkey => $mval){
				update_post_meta($pID, $mkey, $mval);
			}
		}
		return $createdFields;
	}
}

global $cmb2Helper;
$cmb2Helper = new SmackUCICMB2DataImport();