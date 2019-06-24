<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly


class SmackUCICCTMDataImport {

	public function push_cctm_data($data_to_import) {
		global $uci_admin;
		$data_array = $data_to_import['CCTM'];
		if($uci_admin->groupName == 'CCTM' && !empty($data_array)) {
			if ( in_array( 'custom-content-type-manager/index.php', $uci_admin->get_active_plugins() ) ) {
				$this->importDataForCCTMFields( $data_array, $uci_admin->importAs, $uci_admin->last_import_id );
			}
		}
	}

	public function importDataForCCTMFields ($data_array, $importas,$pID) {
		$createdFields = array();
		foreach ($data_array as $custom_key => $custom_value) {
			$createdFields[] = $custom_key;
			update_post_meta($pID, $custom_key, $custom_value);
		}
		return $createdFields;
	}
}

global $cctmHelper;
$cctmHelper = new SmackUCICCTMDataImport();
#return new SmackUCICCTMDataImport();
