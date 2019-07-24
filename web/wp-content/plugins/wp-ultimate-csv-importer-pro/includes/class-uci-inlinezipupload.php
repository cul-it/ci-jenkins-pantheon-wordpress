<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
global $wpdb;
$uploadDir = wp_upload_dir();
if(isset($_FILES['file'])) {
	if (0 < $_FILES['file']['error']) {
		if ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2) {
			echo 'Uploaded file size exceeds the MAX Size in php.ini';
		} else {
			echo 'Error: ' . $_FILES['file']['error'] . '<br>';
		}
	} else {
		if (!empty($_FILES['file']['name'])) {
			$uploaded_compressedFile = '';
			$eventkey = sanitize_key($_REQUEST['eventkey']);
			$uploaded_compressedFile = $_FILES['file']['tmp_name'];
			$uploadeddir = SM_UCI_IMPORT_DIR .'/'. $eventkey . '/inline_zip_uploads';
			if (!is_dir($uploadeddir)) {
				wp_mkdir_p($uploadeddir);
			}
			$location_to_extract = $uploadeddir;
			if (class_exists('ZipArchive')) {
				$zip = new ZipArchive;
				if ($zip->open($uploaded_compressedFile) === TRUE) {
					for($i = 0; $i < $zip->numFiles; $i++)
					 {
						$filterfiles = $zip->getNameIndex($i);
						if (!preg_match('#\.(html|php|js|zip|xml)$#i', $filterfiles))
						{
						    $zip->extractTo($location_to_extract,$filterfiles);
						}
					}
					$zip->close();
					echo 'success';
				} else {
					echo 'failure';
				}
			}
		}
	}
}
die();
