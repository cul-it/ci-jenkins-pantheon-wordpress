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

class CMB2Import {
    private static $cmb2_instance = null;

    public static function getInstance() {		
		if (CMB2Import::$cmb2_instance == null) {
			CMB2Import::$cmb2_instance = new CMB2Import;
			return CMB2Import::$cmb2_instance;
		}
		return CMB2Import::$cmb2_instance;
    }
    function set_cmb2_values($header_array ,$value_array , $map, $post_id , $type){	
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();	
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->cmb2_import_function($post_values, $post_id , $header_array , $value_array);

    }
    public function cmb2_import_function ($data_array,$pID,$header_array,$value_array) {
		$createdFields = array();
		$media_instance = MediaHandling::getInstance();
		
		$get_csvpro_settings = get_option('sm_uci_pro_settings');
		$prefix = $get_csvpro_settings['cmb2'];

		foreach ($data_array as $dkey => $dvalue) {
			$createdFields[] = $dkey;
			if($dkey == $prefix.'image'){
				$darray[$prefix.'image_id'] = $media_instance->media_handling($dvalue, $pID, $data_array,'','','',$header_array,$value_array);	
				$darray[$prefix.'image'] = wp_get_attachment_url($darray[$prefix.'image_id']);
			}
			elseif($dkey == $prefix.'file_list'){
				$exploded_file_items = explode('|', $dvalue);
				foreach($exploded_file_items as $ekey => $evalue){
					$imageid =  $media_instance->media_handling($evalue, $pID, $data_array,'','','',$header_array,$value_array);
					$files[$imageid] = wp_get_attachment_url($imageid);
				}
				$darray[$prefix.'file_list'] = $files;
			}elseif($dkey == $prefix.'repeat_group'){
				$exploded_group_items = explode(',', $dvalue);
				foreach($exploded_group_items as $gkey => $gvalue){
					$exploded_line_items = explode('|', $gvalue);
					$rep_group[$gkey]['title'] = $exploded_line_items[0];
					$rep_group[$gkey]['description'] = $exploded_line_items[1];
					$rep_group[$gkey]['image'] = $exploded_line_items[2];
					$rep_group[$gkey]['image_id'] = $media_instance->media_handling($exploded_line_items[2], $pID ,  $data_array,'','','',$header_array,$value_array);
					$rep_group[$gkey]['image_caption'] = $exploded_line_items[3];
				}
				$darray[$prefix.'repeat_group'] = $rep_group;
			}
			elseif($dkey == $prefix.'multicheckbox'){
				$darray[$prefix.'multicheckbox'] = explode('|', $dvalue);
			}
			elseif($dkey == $prefix.'checkbox'){
				$darray[$prefix.'checkbox'] = explode('|', $dvalue);
			}

			elseif($dkey == $prefix.'textdate_timestamp'){
				$darray[$prefix.'textdate_timestamp'] = strtotime($dvalue);
			}

			elseif($dkey == $prefix.'datetime_timestamp'){
				$darray[$prefix.'datetime_timestamp'] = strtotime($dvalue);
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