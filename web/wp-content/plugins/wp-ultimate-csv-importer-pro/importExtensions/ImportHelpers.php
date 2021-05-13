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

class ImportHelpers {
	private static $helpers_instance = null;

	public static function getInstance() {

		if (ImportHelpers::$helpers_instance == null) {
			ImportHelpers::$helpers_instance = new ImportHelpers;
			return ImportHelpers::$helpers_instance;
		}
		return ImportHelpers::$helpers_instance;
	}

	public function get_requested_term_details ($post_id, $term) {
		$termLen = strlen($term);
		$checktermid = intval($term);
		$verifiedTermLen = strlen($checktermid);
		if($termLen == $verifiedTermLen) {
			return $term;
		} else {
			$reg_term_id = wp_set_object_terms($post_id, $term, 'category');
			if(isset($reg_term_id[0])){
				$term_id = $reg_term_id[0];
			}
			return $term_id;
		}
	}

	public function get_from_user_details($request_user) {
		global $wpdb;
		$authorLen = strlen($request_user);
		$checkpostuserid = intval($request_user);
		$postAuthorLen = strlen($checkpostuserid);

		if ($authorLen == $postAuthorLen) {
			$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from {$wpdb->prefix}users where ID = %s", $request_user));
			if (empty($postauthor) || !$postauthor[0]->ID) { // If user name are numeric Ex: 1300001
				$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from {$wpdb->prefix}users where user_login = \"{%s}\"",$request_user));
			}
		} else {
			$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from {$wpdb->prefix}users where user_login = %s", $request_user));
		}
		if (empty($postauthor) || !$postauthor[0]->ID) {
			$request_user = 1;
			$admindet = $wpdb->get_results($wpdb->prepare("select ID,user_login from {$wpdb->prefix}users where ID = %d", 1));
			$message = " <b>Author :- </b> not found (assigned to <b>" . $admindet[0]->user_login . "</b>)";
		} else {
			$request_user = $postauthor[0]->ID;
			$admindet = $wpdb->get_results($wpdb->prepare("select ID,user_login from {$wpdb->prefix}users where ID = %s", $request_user));
			$message = " <b>Author :- </b>" . $admindet[0]->user_login;
		}
		$userDetails['user_id'] = $request_user;
		$userDetails['user_login'] = $admindet[0]->user_login;
		$userDetails['message'] = $message;
		return $userDetails;
	}

	public function assign_post_status($data_array) {
		global $wpdb;
		if (isset($data_array['is_post_status']) && $data_array['is_post_status'] != 'on') {
			$data_array ['post_status'] = $data_array['is_post_status'];
			unset($data_array['is_post_status']);
		}
		if (isset($data_array ['post_type']) && $data_array ['post_type'] == 'page') {
			$data_array ['post_status'] = 'publish';
		}elseif($data_array['post_status'] == 'trash'){
				$title=$data_array['post_title'];
			$trash = $wpdb->get_results(
			"UPDATE {$wpdb->prefix}posts SET post_status = 'trash' WHERE post_title = '$title'"	
				);
		$wpdb->query($trash);		
		}elseif($data_array['post_status'] == 'delete'){
				$post_title=$data_array['post_title'];
		$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_status = 'trash' and post_title= '$post_title' ");	
		$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_status = 'delete' ");		
	}
		 else {
			if(isset($data_array['post_status']) || isset($data_array['coupon_status'])) {
				if(isset($data_array['post_status'])) {
					$data_array['post_status'] = strtolower( $data_array['post_status'] );
				} else {
					$data_array['post_status'] = strtolower( $data_array['coupon_status'] );
				}
				$data_array['post_status'] = trim($data_array['post_status']);
				if ($data_array['post_status'] != 'publish' && $data_array['post_status'] != 'private' && $data_array['post_status'] != 'draft' && $data_array['post_status'] != 'pending' && $data_array['post_status'] != 'sticky') {
					$stripPSF = strpos($data_array['post_status'], '{');
					if ($stripPSF === 0) {
						$poststatus = substr($data_array['post_status'], 1);
						$stripPSL = substr($poststatus, -1);
						if ($stripPSL == '}') {
							$postpwd = substr($poststatus, 0, -1);
							$data_array['post_status'] = 'publish';
							$data_array ['post_password'] = $postpwd;
						} else {
							$data_array['post_status'] = 'publish';
							$data_array ['post_password'] = $poststatus;
						}
					} else {
						$data_array['post_status'] = 'publish';
					}
				}
				if ($data_array['post_status'] == 'sticky') {
					$data_array['post_status'] = 'publish';
					$sticky = true;
					
				}
				
			} else {
				$data_array['post_status'] = 'publish';
			}
		}
		return $data_array;
	}

	public function import_post_types($import_type, $importAs = null) {	
		$import_type = trim($import_type);

		$module = array('Posts' => 'post', 'Pages' => 'page', 'Users' => 'user', 'Comments' => 'comments', 'Taxonomies' => $importAs, 'CustomerReviews' =>'wpcr3_review', 'Categories' => 'categories', 'Tags' => 'tags', 'eShop' => 'post', 'WooCommerce' => 'product', 'WPeCommerce' => 'wpsc-product','WPeCommerceCoupons' => 'wpsc-product', 'MarketPress' => 'product', 'MarketPressVariations' => 'mp_product_variation','WooCommerceVariations' => 'product', 'WooCommerceOrders' => 'product', 'WooCommerceCoupons' => 'product', 'WooCommerceRefunds' => 'product', 'CustomPosts' => $importAs);
		foreach (get_taxonomies() as $key => $taxonomy) {
			$module[$taxonomy] = $taxonomy;
		}
		if(array_key_exists($import_type, $module)) {
			return $module[$import_type];
		}
		else {
			return $import_type;
		}
	}

	public function get_header_values($map , $header_array , $value_array){

		$post_values = [];
		if (is_array($map)) {
		foreach($map as $key => $value){	
			$csv_value= trim($map[$key]);

			if(!empty($csv_value)){
				$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
				if(preg_match_all($pattern, $csv_value, $matches, PREG_PATTERN_ORDER)){		
					$csv_element = $csv_value;
					foreach($matches[2] as $value){

						$get_key = array_search($value , $header_array);
						if(isset($value_array[$get_key])){
							$csv_value_element = $value_array[$get_key];	
							//}
							$value = '{'.$value.'}';
							$csv_element = str_replace($value, $csv_value_element, $csv_element);
						}
					}
					$math = 'MATH';
					if (strpos($csv_element, $math) !== false) {
								
						$equation = str_replace('MATH', '', $csv_element);
						$csv_element = $this->evalmath($equation);
					}
					$wp_element= trim($key);
					if(!empty($csv_element) && !empty($wp_element)){
						$post_values[$wp_element] = $csv_element;
					}	
				}

				elseif(!in_array($csv_value , $header_array)){
					$wp_element= trim($key);
					$post_values[$wp_element] = $csv_value;
				}

				else{
					$get_key = array_search($csv_value , $header_array);

					if(isset($value_array[$get_key])){
						$csv_element = $value_array[$get_key];		
						//}
						$wp_element = trim($key);
						if(isset($csv_element) && !empty($wp_element)){
							$post_values[$wp_element] = $csv_element;
						}
					}
				}
			}
		}
	}

		return $post_values;
	}

	public function get_meta_values($map , $header_array , $value_array){

		$post_values = [];
		if (is_array($map)) {
		foreach($map as $key => $value){	
			$csv_value= trim($map[$key]);

			if(!empty($csv_value)){
				$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
				if(preg_match_all($pattern, $csv_value, $matches, PREG_PATTERN_ORDER)){		
					$csv_element = $csv_value;
					foreach($matches[2] as $value){

						$get_key = array_search($value , $header_array);
						if(isset($value_array[$get_key])){
							$csv_value_element = $value_array[$get_key];	
							//}
							$value = '{'.$value.'}';
							$csv_element = str_replace($value, $csv_value_element, $csv_element);
						}
					}
					$math = 'MATH';
					if (strpos($csv_element, $math) !== false) {
								
						$equation = str_replace('MATH', '', $csv_element);
						$csv_element = $this->evalmath($equation);
					}
					$wp_element= trim($key);
					if(!empty($csv_element) && !empty($wp_element)){
						$post_values[$wp_element] = $csv_element;
					}	
				}

				elseif(!in_array($csv_value , $header_array)){
					$wp_element= trim($key);
					$post_values[$wp_element] = $csv_value;
				}

				else{
					$get_key = array_search($csv_value , $header_array);

					if(isset($value_array[$get_key])){
						$csv_element = $value_array[$get_key];	
						$csv_ele1=explode('|',$csv_element)	;
						//foreach($csv_ele1 as $key => $val){
							$wp_element = trim($key);
							if(isset($csv_element) && !empty($wp_element)){
								$post_values[$wp_element] = $csv_ele1;
							}
						//}
						//}
						
					}
				}
			}
		}
	}

		return $post_values;
	}

public function evalmath($equation) {
	$result = 0;

	// sanitize imput
	$equation = preg_replace("/[^0-9+\-.*\/()%]/","",$equation);

	// convert percentages to decimal
	$equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation);
	$equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation);
	//$equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation);

	if ( $equation != "" )
	{
		$result = @eval("return " . $equation . ";" );
	}

	if ($result === null)
	{
		//throw new Exception("Unable to calculate equation");
		$result = "Unable to calculate equation";
	}
	if($result === FALSE){
		$result = 'false';
	}

	return $result;	
}

public function get_post_ids($post_id , $eventKey){
	$smack_instance = SmackCSV::getInstance();
	$recordId = array($post_id);
	$upload_dir = $smack_instance->create_upload_dir();
	$eventInfoFile = $upload_dir.$eventKey.'/'.$eventKey.'.txt';
	if(file_exists($eventInfoFile)) {
		$handle   = fopen( $eventInfoFile, 'r' );
		$contents = json_decode( fread( $handle, filesize( $eventInfoFile ) ) );
		fclose( $handle );
	}
	$fp = fopen($eventInfoFile, 'w+');
	if(!empty($contents) && $contents != null) {
		$contents = array_merge( $contents, $recordId );
		$contents = json_encode( $contents );
	} else {
		$contents = json_encode( $recordId );
	}
	fwrite($fp, $contents);
	fclose($fp);
}

public function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}

	return $bytes;
}

public function update_count($hash_key){
	$response = [];
	global $wpdb;
	$log_table_name = $wpdb->prefix ."import_detail_log";
	$get_data =  $wpdb->get_results("SELECT skipped , created , updated FROM $log_table_name WHERE hash_key = '$hash_key' ");
	$skipped = $get_data[0]->skipped;
	$response['skipped'] = $skipped + 1;
	$created = $get_data[0]->created;
	$response['created'] = $created + 1;
	$updated = $get_data[0]->updated;
	$response['updated'] = $updated + 1;

	return $response;
}

}
