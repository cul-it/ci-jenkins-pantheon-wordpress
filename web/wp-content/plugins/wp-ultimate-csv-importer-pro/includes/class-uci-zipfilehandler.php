<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if(!defined('ABSPATH'))
	exit();

$import_method = isset($_POST['import_method']) ? sanitize_text_field($_POST['import_method']) : '';
$eventkey = isset($_POST['eventkey']) ? sanitize_key($_POST['eventkey']) : '';
$get_upload_url = wp_upload_dir();
$put_zip_url = $get_upload_url['baseurl'] . '/smack_uci_uploads/imports/'. $eventkey . '/' . $eventkey;
$put_zip_path = $get_upload_url['basedir'] . '/smack_uci_uploads/imports/'. $eventkey . '/' . $eventkey;
$oldpath = $put_zip_path;
$target_path = $get_upload_url['basedir'] . '/smack_uci_uploads/imports/'. $eventkey;
$newpath = $target_path . '.zip';
copy($oldpath, $newpath);
unlink($put_zip_path);
if (class_exists('ZipArchive')) {
	$zip = new ZipArchive;
	$res = $zip->open($newpath);
	if ($res === TRUE) {
		$content = wp_csv_importer_generate_content($zip, $target_path, $import_method);
	}
	else {
		$returnData['return_message'] = 'Error Occured while extracting zip file.';
	}
	$returnData['data'] = $content;
	$returnData['import_method'] = $import_method;
	$returnData['path'] = $put_zip_url;
}else{
	echo 'ZipArchive class not exists';
}
echo json_encode($returnData);die;
function wp_csv_importer_generate_content($zip, $dir, $import_method)
{
	$content = "";
	$get_upload_dir = wp_upload_dir();
	for($i = 0; $i < $zip->numFiles; $i++)
	{
		$filterfiles = $zip->getNameIndex($i);
		if (!preg_match('#\.(html|php|js|zip|xml)$#i', $filterfiles))
		{
			$zip->extractTo($dir,$filterfiles);
		}
	}
	$ext_files = scandir($dir);
	$filesAndFoldersPath = array();
	$zipExtractFolder = $dir;
	$get_upload_dirpath =  $get_upload_dir['basedir'];
	$get_upload_dirurl =  $get_upload_dir['baseurl'];
	$filesList = wp_csv_importer_fetch_all_files($zipExtractFolder);
	foreach($filesList as $singleFile)      {
		$get_file_name = explode('/',$singleFile);
		$c = count($get_file_name);
		$temp_file_name = $get_file_name[$c - 1];
		$file_extension = pathinfo($temp_file_name, PATHINFO_EXTENSION);
		$getFileRealPath = explode($get_upload_dirpath,$singleFile);
		$getFileRealPath = $get_upload_dirurl.$getFileRealPath[1];
		if($file_extension == 'csv' || $file_extension == 'xml' || $file_extension == 'txt')  {
			$content .= "<label> <span class = 'radio label_model_radio'> <input type ='radio' id = '$temp_file_name' name = 'list' onclick='choose_file_from_zip(this.id, \"$getFileRealPath\",\"$import_method\");'/>";
			$content .= "<span class = 'label label-default label_model_radio_text'> $temp_file_name </span> </label> <br/>";
		}
	}
	$zip->close();
	return $content;
}
/**
 * scan folder directory and return all files
 * @param string $dir
 * @return array $files
 */
function wp_csv_importer_fetch_all_files($dir)
{
	$root = scandir($dir);
	foreach($root as $value)
	{
		if($value === '.' || $value === '..')
			continue;

		if(is_file("$dir/$value"))      {
			$files[] = "$dir/$value";continue;
		}

		foreach(wp_csv_importer_fetch_all_files("$dir/$value") as $value)
		{
			$files[] = $value;
		}
	}
	return $files;
}