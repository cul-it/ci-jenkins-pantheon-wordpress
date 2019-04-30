<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
error_reporting(0);
/**
 * @param $user
 *
 * @return int|null|WP_Error|WP_User
 */
function json_basic_auth_handler( $user ) {
	global $wp_json_basic_auth_error;
	$wp_json_basic_auth_error = null;
	// Don't authenticate twice
	if ( ! empty( $user ) ) {
		return $user;
	}
	// Check that we're trying to authenticate
	if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
		return $user;
	}
	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];
	/**
	 * In multi-site, wp_authenticate_spam_check filter is run on authentication. This filter calls
	 * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
	 * recursion and a stack overflow unless the current function is removed from the determine_current_user
	 * filter during authentication.
	 */
	remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
	$user = wp_authenticate( $username, $password );
	add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
	if ( is_wp_error( $user ) ) {
		$wp_json_basic_auth_error = $user;
		return null;
	}
	$wp_json_basic_auth_error = true;
	$csv_pro_settings = get_option('sm_uci_pro_settings');
	if(in_array($user->roles[0], array('administrator', 'author', 'editor'))) {
		if($csv_pro_settings['author_editor_access'] == 'off' && ($user->roles[0] == 'author' || $user->roles[0] == 'editor')) {
			return "You don't have the permission. Please, Contact your administrator";
		}
		return $user->ID;
	} else {
		return "You don't have the permission. Please, Contact your administrator";
	}
}

/**
 * @param $error
 *
 * @return mixed
 */
function json_basic_auth_error( $error ) {
	// Passthrough other errors
	if ( ! empty( $error ) ) {
		return $error;
	}
	global $wp_json_basic_auth_error;
	return $wp_json_basic_auth_error;
}

/**
 * Register routes to get support from CSV importer API
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'wp-ultimate-csv-importer', '/availableFields/(?P<module>[a-zA-Z0-9-]+)', array(
		'methods' => 'GET',
		'callback' => 'get_available_fields',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/availableFields/CustomPosts', array(
		'methods' => 'POST',
		'callback' => 'get_available_fields',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/getTemplateInfo', array(
		'methods' => 'POST',
		'callback' => 'getTemplateInfo',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/listAllTemplate', array(
		'methods' => 'GET',
		'callback' => 'listAllTemplate',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/listAllFields', array(
		'methods' => 'GET',
		'callback' => 'getAllAvailableFields',
	) ); 
	register_rest_route( 'wp-ultimate-csv-importer', '/saveMapping', array(
		'methods' => 'POST',
		'callback' => 'saveMapping',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/importData', array(
		'methods' => 'POST',
		'callback' => 'pushData',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/assignFeaturedImage', array(
		'methods' => 'POST',
		'callback' => 'setFeaturedImage',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/assignTerms', array(
		'methods' => 'POST',
		'callback' => 'setTerms',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/(?P<module>[\s\S]+)/create', array(
		'methods' => 'POST',
		'callback' => 'pushRowValues',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/(?P<module>[\s\S]+)/update/(?P<id>[\s\S]+)', array(
		'methods' => 'POST',
		'callback' => 'pushRowValues',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/registerField/(?P<module>[\s\S]+)/(?P<type>[\s\S]+)', array(
		'methods' => 'POST',
		'callback' => 'registerField',
	) );
	register_rest_route( 'wp-ultimate-csv-importer', '/fetch/(?P<module>[\s\S]+)/(?P<id>[\s\S]+)', array(
		'methods' => 'POST',
		'callback' => 'fetchRecord',
	) );
} );

/**
 * Function to validate the authentication
 *
 * @return string
 */
function validate_authentication() {
	$user = array();
	$userID = json_basic_auth_handler($user);
	$data = json_basic_auth_error($userID);

	if($data == NULL)
		return 'Provide a valid credentials!';

	return $data;
}

/**
 * Function which helps to get the available fields as group based on the requested post type
 * @param $data
 *
 * @return array|string
 */
function get_available_fields( $data ) {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	// Fetch fields based on the requested module
	global $uci_admin;
	$import_type = isset($data['module']) ? $data['module'] : 'CustomPosts';
	$importAs = $data['module'];
	if(isset($_POST['import_type'])) {
		$import_type = 'CustomPosts';
		$importAs = $_POST['import_type'];
	}
	$possible_widgets = array();
	$possible_widgets = $uci_admin->available_widgets($import_type, $importAs);
	$fieldSet = $data = array();
	if(!empty($possible_widgets)) {
		foreach ( $possible_widgets as $widget_name => $groupName ) {
			$fields = $uci_admin->get_widget_fields( $widget_name, $import_type, $importAs );
			if ( ! empty( $fields[ $groupName ] ) ) {
				foreach ( $fields[ $groupName ] as $key => $val ) {
					$fieldSet[ $groupName ][ $val['name'] ] = $val['name'];
				}
			} else {
				$fieldSet[ $groupName ] = array();
			}
		}
		$data['type'] = 'object';
		$data['fields'] = $fieldSet;
	} else {
		$data['type'] = 'string';
		$data['message'] = 'Please provide a valid a module name.';
	}
	return $data;
}

/**
 * Function which helps to get the template information based on the existing template name
 * @param $data
 *
 * @return array|string
 */
function getTemplateInfo( $data ) {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	#$name = str_replace ( '%20', ' ', $data['name'] );
	$name = $_REQUEST['template_name'];
	$mapping = getMapping($name);
	return $mapping;
}

/**
 * Function which helps to get all the existing template name
 * 
 *
 * @return array|string
 */
function listAllTemplate($data)
{
	global $wpdb;
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	$template = $wpdb->get_results("select templatename from wp_ultimate_csv_importer_mappingtemplate");
	
	return $template;
}

/**
 * Function which helps to get all the existing template name
 * 
 *
 * @return array|string
 */
function getAllAvailableFields($data)
{
	global $uci_admin;
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	$fields = $uci_admin->get_import_post_types();
	$all_fields = array();
	foreach ($fields as $key => $value) {
		$all_fields[] = $value;
	}
	
	return $all_fields;
}

/**
 * Function which helps to fetch the template information
 * @param $name
 *
 * @return array
 */
function getMapping( $name ) {
	global $wpdb;
	$data = array();
	$templateInfo = $wpdb->get_results(
		$wpdb->prepare("select mapping, module, eventKey from wp_ultimate_csv_importer_mappingtemplate where templatename = %s", $name)
	);
	if(!empty($templateInfo)) {
		$data['type'] = 'object';
		$data['mapping']  = unserialize($templateInfo[0]->mapping);
		$data['eventKey'] = $templateInfo[0]->eventKey;
		return $data;
	} else {
		$data['type'] = 'string';
		$data['message'] = 'There is no template available in the given name.';
		$data['description'] = 'Please sanitize your template name';
		return $data;
	}
}

/**
 * Function which helps to save the template information for future reference
 *
 * @return array|string
 */
function saveMapping() {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	global $uci_admin;
	$eventKey      = $uci_admin->convert_string2hash_key( '' );
	$eventMapping = $_REQUEST['mapping'];
	$eventMapping = str_replace('\"', '"', $eventMapping);
	$eventMapping = str_replace('\":', '"', $eventMapping);
	$eventMapping = json_decode($eventMapping);
	$is_template = isset($_REQUEST['is_template']) ? $_REQUEST['is_template'] : 0;
	$template_name = isset($_REQUEST['template_name']) ? $_REQUEST['template_name'] : '';
	$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';
	$result = array();
	if($is_template == 1 && $eventMapping != 0) {
		$template_name = trim($template_name);
		if($template_name != '' && $template_name != null) {
			$result = saveTemplate( $template_name, $eventMapping, $module, $eventKey );
		} else {
			$result['type'] = 'string';
			$result['message'] = "Don't leave empty on \"template_name\".";
		}
	} else {
		if($eventMapping == 0) {
			$result['type'] = 'string';
			$result['message'] = "Provide a valid template information. Please, Validate your given template information.";
			return $result;
		}
		$result['type'] = 'string';
		$result['message'] = 'Assign value as 1 in "is_template".';
	}

	return $result;
}

/**
 * @param $template_name    - Template name - string
 * @param $template_info    - Template information - array - Group based mapping
 * @param $module           - Module name - string
 * @param $eventKey         - Event Key - String
 *
 * @return array
 */
function saveTemplate($template_name, $template_info, $module, $eventKey) {
	global $wpdb, $uci_admin;
	$data = $result = array();
	$data['module'] = $module;
	$get_available_fields = get_available_fields($data);
	$get_available_fields['fields'] = unserialize($get_available_fields['fields']);
	if(isset($get_available_fields['fields']) && !empty($get_available_fields['fields'])) {
		$custom_field_groups = array( 'CORECUSTFIELDS', 'PODS', 'ACF', 'RF', 'TYPES', 'CCTM' );
		if(!empty($template_info)) {
			foreach ( $template_info as $groupIndex => $groupField ) {
				if ( ! array_key_exists( $groupIndex, $get_available_fields['fields'] ) ) {
					$result['type']    = 'string';
					$result['message'] = "Don't use the unknown group name.";

					return $result;
				}
				foreach ( $groupField as $key => $val ) {
					if ( ! array_key_exists( $key, $get_available_fields['fields'][ $groupIndex ] ) && ! in_array( $groupIndex, $custom_field_groups ) ) {
						$result['type']    = 'string';
						$result['message'] = "Can't add custom field in this group" . "\"$groupIndex\"";

						return $result;
					}
				}
			}
			$find_is_exist = $wpdb->get_results( $wpdb->prepare( "select *from wp_ultimate_csv_importer_mappingtemplate where templatename = %s", $template_name ) );
			if ( empty( $find_is_exist ) ) {
				$template_info = maybe_serialize( $template_info );
				$wpdb->insert( 'wp_ultimate_csv_importer_mappingtemplate', array(
					'templatename' => $template_name,
					'mapping'      => $template_info,
					'module'       => $module,
					'eventKey'     => $eventKey,
				), array( '%s', '%s', '%s', '%s' ) );
				$result['type']          = 'object';
				$result['message']       = 'Template added successfully!';
				$result['template_name'] = $template_name;
				$result['id']['type']    = 'integer';
				$result['id']['value']   = $wpdb->insert_id;

				return $result;
			} else {
				$result['type']    = 'string';
				$result['message'] = 'Template Exist!';

				return $result;
			}
		}
	} else {
		return $get_available_fields;
	}
}

/**
 * Function which helps to push the data into WordPress
 * using API from the remote location file
 *
 * @return array
 */
function pushData() {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	global $uci_admin;
	$eventKey = $uci_admin->convert_string2hash_key( '' );
	$data_array = $eventMapping = $result = array();
	$importType = $_REQUEST['import_type'];
	$importMethod = $_REQUEST['import_method'];
	$mode = $_REQUEST['import_mode'];
	$data = $_REQUEST['data'];
	$data = str_replace('\"', '"', $data);
	$data = str_replace('\":', '"', $data);
	$data = str_replace('\\/', '/', $data);
	$data = json_decode($data);
	if(!empty($data)) {
		foreach ( $data as $key => $value ) {
			if ( ! empty( $value ) ) {
				foreach ( $value as $index => $field ) {
					if($index == 'featured_image') {
						$data_array[ $key ][ $index ] = urldecode($field);
					} else {
						$data_array[ $key ][ $index ] = $field;
					}
				}
			}
		}
	}
	$mapping = $_REQUEST['mapping'];
	$mapping = str_replace('\"', '"', $mapping);
	$mapping = str_replace('\":', '"', $mapping);
	$mapping = json_decode($mapping);
	if(!empty($mapping)) {
		foreach ( $mapping as $key => $value ) {
			if ( ! empty( $value ) ) {
				foreach ( $value as $index => $field ) {
					$eventMapping[ $key ][ $index ] = $field;
				}
			}
		}
	}
	$row_no = $_REQUEST['row_id'];
	$affectedRecords = array();
	$mediaConfig = array(
		'eventkey' => $eventKey,
		'download_img_tag_src' => 'on',
		'media_thumbnail_size' => 'on',
		'media_medium_size' => 'on',
		'media_medium_large_size' => 'on',
		'media_large_size' => 'on',
	);
	$importConfig = isset($_REQUEST['import_config']) ? $_REQUEST['import_config'] : array();
	$is_template = isset($_REQUEST['is_template']) ? $_REQUEST['is_template'] : 0;
	$template_name = isset($_REQUEST['template_name']) ? $_REQUEST['template_name'] : '';
	if($is_template == 1) {
		$template_result = saveTemplate( $template_name, $eventMapping, $importType, $eventKey );
		if($template_result['message'] != 'Template added successfully!') {
			return $template_result;
		}
	}
	$uci_admin->importData($eventKey, $importType, $importMethod, $mode, $data_array, $row_no, $eventMapping, $affectedRecords, $mediaConfig, $importConfig);
	if(is_int($uci_admin->getLastImportId())) {
		$result['type'] = 'integer';
		$result['description'] = 'Record affected successfully!';
		$result['id'] = $uci_admin->getLastImportId();
	} else {
		$result['type'] = 'string';
		$result['message'] = 'Please validate your record information.';
	}
	return $result;
}

/**
 * Function which helps to push the data into WordPress
 * and create or update a record using API from the remote location file
 *
 * @param $data
 * @return array
 */
function pushRowValues($data) {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	global $uci_admin;
	$module = $data['module'];
	$id = isset($data['id']) ? $data['id'] : '';
	$eventKey = $uci_admin->convert_string2hash_key( '' );
	$data_array = $eventMapping = $result = array();
	$importMethod = 'import_with_api';
	$row_no = 0;
	if($data['id'] != '') {
		$action = 'Update';
		$data_array['CORE']['ID'] = $id;
		$status = 'updated';
	} else {
		$action = 'Insert';
		$status = 'inserted';
	}
	$data = $_REQUEST['data'];
	$data = str_replace('\"', '"', $data);
	$data = str_replace('\":', '"', $data);
	$data = str_replace('\\/', '/', $data);
	$data = json_decode($data);
	if(!empty($data)) {
		foreach ( $data as $key => $value ) {
			if ( ! empty( $value ) ) {
				foreach ( $value as $index => $field ) {
					if($index == 'featured_image') {
						$data_array[ $key ][ $index ] = urldecode($field);
					} else {
						$data_array[ $key ][ $index ] = $field;
					}
				}
			}
		}
	}
	$is_template = isset($_REQUEST['is_template']) ? $_REQUEST['is_template'] : 0;
	$template_name = isset($_REQUEST['template_name']) ? $_REQUEST['template_name'] : '';
	// if($is_template == 1) {
	// 	$template_result = saveTemplate( $template_name, $eventMapping, $module, $eventKey );
	// 	if($template_result['message'] != 'Template added successfully!') {
	// 		return $template_result;
	// 	}
	// }
	$affectedRecords = array();
	$mediaConfig = array(
		'eventkey' => $eventKey,
		'download_img_tag_src' => 'on',
		'media_thumbnail_size' => 'on',
		'media_medium_size' => 'on',
		'media_medium_large_size' => 'on',
		'media_large_size' => 'on',
	);
	$importConfig = array();
	$is_template = isset($_REQUEST['is_template']) ? $_REQUEST['is_template'] : 0;
	$template_name = isset($_REQUEST['template_name']) ? $_REQUEST['template_name'] : '';
	if($is_template == 1) {
		$template = getTemplateInfo($template_name);
		$map_array = $template['mapping'];
		$data = $_REQUEST['data'];
		$data = str_replace('\"', '"', $data);
		$data = str_replace('\":', '"', $data);
		$data = str_replace('\\/', '/', $data);
		$data = json_decode($data);
		foreach ($map_array as $main => $content) {
			foreach ($content as $key => $value) {
				if (preg_match('/{/',$value) && preg_match('/}/',$value)){
					$value = str_replace('{', '', $value);
					$value = str_replace('}', '', $value);
					$value = $data->$value;
					$map_array[$main][$key] = $value;
				}else{
					$map_array[$main][$key] = $value;
				}
			}
		}
		$data_array = $map_array;	
		if($action == "Update" && $data->ID != ""){
			$data_array['CORE']['ID'] = $data->ID;
		}
	}
	$uci_admin->setEventInstance($module);
	$uci_admin->importData($eventKey, $module, $importMethod, $action, $data_array, $row_no, $eventMapping, $affectedRecords, $mediaConfig, $importConfig, 1, $template_name);
	if(is_int($uci_admin->getLastImportId())) {
		$result['type'] = 'integer';
		$result['description'] = 'Record ' . $status . ' successfully!';
		$result['id'] = $uci_admin->getLastImportId();
	} else {
		$result['type'] = 'string';
		$result['message'] = 'Please validate your record information.';
	}
	return $result;
}

/**
 * Function which helps to assign featured image to the specific post / product
 *
 * @return int|null|WP_Error
 */
function setFeaturedImage() {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	$thumbnailId = null;
	$result = array();
	$imageURL = $_REQUEST['image_url'];
	$postID = $_REQUEST['id'];
	$renameImage = isset($_REQUEST['rename_image']) ? $_REQUEST['rename_image'] : null;
	$mediaSettings = array(
		'eventkey' => '',
		'download_img_tag_src' => 'on',
		'media_thumbnail_size' => 'on',
		'media_medium_size' => 'on',
		'media_medium_large_size' => 'on',
		'media_large_size' => 'on',
	);
	$thumbnailId = SmackUCIMediaScheduler::convert_local_imageURL($imageURL, $postID, $renameImage, $mediaSettings);
	if($thumbnailId != null) {
		set_post_thumbnail( $postID, $thumbnailId );
	}

	if(is_int($thumbnailId)) {
		$result['type'] = 'integer';
		$result['description'] = 'Featured image assigned successfully!';
		$result['id'] = $thumbnailId;
	} else {
		$result['type'] = 'string';
		$result['message'] = 'Please validate your image url.';
	}
	return $result;
}

/**
 * Function which helps to assign the terms with parent -> child hierarchical
 * as any number of depth for a specific post / product
 *
 * @return array|string
 */
function setTerms() {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	global $uci_admin;
	$result = $category_list = array();
	$terms[0] = $_REQUEST['terms'];
	$taxonomy_name = $_REQUEST['taxonomy_name'];
	$pID = $_REQUEST['id'];
	$is_exist_record = get_post($pID);
	if($is_exist_record == null) {
		$result['type'] = 'string';
		$result['message'] = 'Provide a valid a record (Post / Product) number.';
		return $result;
	}
	$category_list = $uci_admin->assignTermsAndTaxonomies($terms, $taxonomy_name, $pID);

	if(!empty($category_list)) {
		$result['type'] = 'object';
		$result['terms'] = $category_list;
		$result['description'] = 'Terms assigned successfully!';
	}

	return $result;
}

function registerField($data) {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	global $uci_admin;
	$field_info = $result = array();
	$active_plugins = get_option('active_plugins');
	$get_field_info = json_decode($data['field_info']);
	foreach($get_field_info as $key => $value) {
		$field_info['field_info'][$key] = $value;
	}
	$field_info['import_type'] = $uci_admin->import_post_types($data['module'], $data['import_type']);
	#print '<pre>'; print_r($field_info); print '</pre>'; die;
	// Register Fields
	if($data['type'] == 'acf') {
		if(!in_array('advanced-custom-fields-pro/acf.php', $active_plugins) && !in_array('advanced-custom-fields/acf.php', $active_plugins)) {
			$result['message'] = "ACF plugin is not installed or activated!";
		} else {
			require_once "includes/class-uci-acf-data-import.php";
			$acfObj = new SmackUCIACFDataImport();
			if ( in_array( 'advanced-custom-fields-pro/acf.php', $active_plugins ) ) {
				$result['message'] = $acfObj->Register_ProFields( $field_info, $data['type'] );
			} else {
				$result['message'] = $acfObj->Register_FreeFields( $field_info, $data['type'] );
			}
		}
	} elseif($data['type'] == 'types') {
		if(!in_array('types/wpcf.php', $active_plugins)) {
			$result['message'] = "Types plugin is not installed or activated!";
		} else {
			require_once "includes/class-uci-types-data-import.php";
			$typesObj = new SmackUCITypesDataImport();
			$result['message'] = $typesObj->Register_Fields($field_info, $data['type']);
		}
	} elseif($data['type'] == 'pods') {
		if(!in_array('pods/init.php', $active_plugins)) {
			$result['message'] = "PODS plugin is not installed or activated!";
		} else {
			require_once "includes/class-uci-pods-data-import.php";
			$podsObj = new SmackUCIPODSDataImport();
			$result['message'] = $podsObj->Register_Fields($field_info, $data['type']);
		}
	} else {
		$result['message'] = 'Unknown plugin!';
	}

	return $result;
}

function fetchRecord($data) {
	$is_valid_user = validate_authentication();
	if(!is_int($is_valid_user)) {
		return $is_valid_user;
	}
	global $uci_admin;
	require_once "includes/class-uci-exporter.php";
	$exportObj = new SmackUCIExporter();
	$importAs = $data['module'];
	$exportObj->module = $uci_admin->import_post_types($data['module'], $importAs);
	switch ($data['module']) {
		case 'Posts':
		case 'Pages':
		case 'CustomPosts':
		case 'WooCommerce':
		case 'MarketPress':
		case 'WooCommerceVariations':
		case 'WooCommerceOrders':
		case 'WooCommerceCoupons':
		case 'WooCommerceRefunds':
		case 'WPeCommerce':
		case 'eShop':
			$exportObj->generateHeaders($data['module'], $exportObj->optionalType);
			$exportObj->data[$data['id']] = $exportObj->getPostsDataBasedOnRecordId($data['id']);
			$exportObj->getPostsMetaDataBasedOnRecordId($data['id'], $exportObj->module, $exportObj->optionalType);
			$exportObj->getTermsAndTaxonomies($data['id'], $exportObj->module, $exportObj->optionalType);
			break;
		case 'Users':
			$exportObj->FetchUsers('webservice');
			break;
		case 'Comments':
			$exportObj->FetchComments('webservice');
			break;
		case 'Taxonomies':
			$exportObj->FetchTaxonomies('webservice');
			break;
		case 'CustomerReviews':
			$exportObj->FetchCustomerReviews('webservice');
			break;
		case 'Categories':
			$exportObj->FetchCategories('webservice');
			break;
		case 'Tags':
			$exportObj->FetchTags('webservice');
			break;
	}
	#print '<pre>';
	#print_r($exportObj->data[$data['id']]);
	#print '</pre>';
	return $exportObj->data[$data['id']];
}
