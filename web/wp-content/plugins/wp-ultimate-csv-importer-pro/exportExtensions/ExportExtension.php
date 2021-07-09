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

class ExportExtension {

	public $response = array();
	public $headers = array();
	public $module;	
	public $exportType = 'csv';
	public $optionalType = null;	
	public $conditions = array();	
	public $eventExclusions = array();
	public $fileName;	
	public $data = array();	
	public $heading = true;	
	public $delimiter = ',';
	public $enclosure = '"';
	public $auto_preferred = ",;\t.:|";
	public $output_delimiter = ',';
	public $linefeed = "\r\n";
	public $export_mode;
	public $export_log = array();
	public $limit;
	protected static $instance = null,$mapping_instance,$export_handler,$post_export,$woocom_export,$review_export,$ecom_export;
	protected $plugin,$activateCrm,$crmFunctionInstance;
	public $plugisnScreenHookSuffix=null;

	/**
	 * ExportExtension constructor.
	 * Set values into global variables based on post value
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			ExportExtension::$mapping_instance = MappingExtension::getInstance();
			ExportExtension::$export_handler = ExportHandler::getInstance();
			ExportExtension::$post_export = PostExport::getInstance();
			ExportExtension::$woocom_export = WooCommerceExport::getInstance();
			ExportExtension::$review_export = CustomerReviewExport::getInstance();
			ExportExtension::$ecom_export = EComExport::getInstance();
			self::$instance->doHooks();
		}
		return self::$instance;
	}	

	public  function doHooks(){
		add_action('wp_ajax_parse_data',array($this,'parseData'));
		add_action('wp_ajax_total_records', array($this, 'totalRecords'));
	}

	public function totalRecords(){
		global $wpdb;
		$module = $_POST['module'];
		$optionalType = $_POST['optionalType'];

		if(empty($optionalType)){
			$check_for_template = $wpdb->get_results("SELECT filename FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE module = '$module' ");
		}else{
			$check_for_template = $wpdb->get_results("SELECT filename FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE module = '$module' AND optional_type = '$optionalType' ");
		}
		
		$response = [];
		if(empty($check_for_template)){
			$response['show_template'] = false;
		}else{
			$response['show_template'] = true;
		}

		if ($module == 'WooCommerceOrders') {
			$module = 'shop_order';
		}
		elseif ($module == 'WooCommerceCoupons') {
			$module = 'shop_coupon';
		}
		elseif ($module == 'Marketpress') {
			$module = 'product';
		}
		elseif ($module == 'WooCommerceRefunds') {
			$module = 'shop_order_refund';
		}
		elseif ($module == 'WooCommerceVariations') {
			$module = 'product_variation';
		}
		elseif($module == 'WPeCommerceCoupons'){
			$query = $wpdb->get_col("SELECT * FROM {$wpdb->prefix}wpsc_coupon_codes");
			$response['count'] = count($query);
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'Comments'){
			$response['count'] = $this->commentsCount();
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'Images'){
			$get_images = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts where post_type='attachment'");
			$response['count'] = count($get_images);
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'Users'){
			$get_available_user_ids = "select DISTINCT ID from {$wpdb->prefix}users u join {$wpdb->prefix}usermeta um on um.user_id = u.ID";
			$availableUsers = $wpdb->get_col($get_available_user_ids);
			$response['count'] = count($availableUsers);
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'Tags'){
			$get_all_terms = get_tags('hide_empty=0');
			$response['count'] = count($get_all_terms);
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'Categories'){
			$get_all_terms = get_categories('hide_empty=0');
			$response['count'] = count($get_all_terms);
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'Taxonomies'){
			$query = "SELECT * FROM {$wpdb->prefix}terms t INNER JOIN {$wpdb->prefix}term_taxonomy tax 
				ON  `tax`.term_id = `t`.term_id WHERE `tax`.taxonomy =  '{$optionalType}'";         
			$get_all_taxonomies =  $wpdb->get_results($query);
			$response['count'] = count($get_all_taxonomies);
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'CustomPosts' && $optionalType == 'nav_menu_item'){
			$get_menu_ids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms AS t LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'nav_menu' ", ARRAY_A);
			$response['count'] = count($get_menu_ids);
			echo wp_json_encode($response);
			wp_die();
		}
		elseif($module == 'CustomPosts' && $optionalType == 'widgets'){
			$response['count'] = 1;
			echo wp_json_encode($response);
			wp_die();
		}
		else {
			if($module == 'CustomPosts') {
				$optional_type = $optionalType;
			}
			$module = ExportExtension::$post_export->import_post_types($module,$optional_type);
		}
		$get_post_ids = "select DISTINCT ID from {$wpdb->prefix}posts";
		$get_post_ids .= " where post_type = '$module'";
		if($module == 'shop_order'){
			$get_post_ids .= " and post_status in ('wc-completed','wc-cancelled','wc-on-hold','wc-processing','wc-pending')";
		}elseif ($module == 'shop_coupon') {
			$get_post_ids .= " and post_status in ('publish','draft','pending')";
		}elseif ($module == 'shop_order_refund') {

		}
		elseif($module == 'lp_order'){
			$get_post_ids .= " and post_status in ('lp-pending', 'lp-processing', 'lp-completed', 'lp-cancelled', 'lp-failed')";
		}
		else{
			$get_post_ids .= " and post_status in ('publish','future','private','pending')";
		}
		$get_total_row_count = $wpdb->get_col($get_post_ids);
		$total = count($get_total_row_count);

		$response['count'] = $total;
		echo wp_json_encode($response);
		wp_die();
	}

	public  function parseData(){
		if(!empty($_POST)) {
			$categorybased = $_POST['categoryName'];
			$this->module          = $_POST['module'];
			$this->exportType      = isset( $_POST['exp_type'] ) ? sanitize_text_field( $_POST['exp_type'] ) : 'csv';
			$conditions =  str_replace("\\" , '' , $_POST['conditions']);
			$conditions = json_decode($conditions, True);
			
			$conditions['specific_period']['to'] = date("Y-m-d", strtotime($conditions['specific_period']['to']) );
			$conditions['specific_period']['from'] = date("Y-m-d", strtotime($conditions['specific_period']['from']) );
			$this->conditions      = isset( $conditions ) && ! empty( $conditions ) ? $conditions : array();
			if($this->module == 'Taxonomies' || $this->module == 'CustomPosts' ){
				$this->optionalType    = $_POST['optionalType'];
			}
			else{
				$this->optionalType    = $this->getOptionalType($this->module);
			}
			$eventExclusions = str_replace("\\" , '' , $_POST['eventExclusions']);
			$eventExclusions = json_decode($eventExclusions, True);
			$this->eventExclusions = isset( $eventExclusions ) && ! empty( $eventExclusions ) ? $eventExclusions : array();
			$this->fileName        = isset( $_POST['fileName'] ) ? sanitize_text_field( $_POST['fileName'] ) : '';
			if(empty($_POST['offset'] ) || $_POST['offset']== 'undefined'){
				$this->offset = 0 ;
			}
			else{
				$this->offset          = isset( $_POST['offset'] ) ? sanitize_text_field( $_POST['offset'] ) : 0;
			}
			if(!empty($_POST['limit'] )){
				$this->limit           = isset( $_POST['limit'] ) ? sanitize_text_field( $_POST['limit'] ) : 1000;
			}
			else{
				$this->limit           = 50;
			}
			if(!empty($this->conditions['delimiter']['optional_delimiter'])){
				$this->delimiter = $this->conditions['delimiter']['optional_delimiter'] ? $this->conditions['delimiter']['optional_delimiter']: ',';
			}
			elseif(!empty($this->conditions['delimiter']['delimiter'])){
				$this->delimiter = $this->conditions['delimiter']['delimiter'] ? $this->conditions['delimiter']['delimiter'] : ',';
				if($this->delimiter == '{Tab}'){
					$this->delimiter = " ";
				}
				elseif($this->delimiter == '{Space}'){
					$this->delimiter = " ";	
				}
			}

			$this->export_mode = 'normal';
			$this->checkSplit = isset( $_POST['is_check_split'] ) ? sanitize_text_field( $_POST['is_check_split'] ) : 'false';
			
			$time = date('Y-m-d h:i:s');
			$export_conditions = serialize($conditions);
			$export_event_exclusions = serialize(($eventExclusions));
			global $wpdb;


			$file_post_name = $_POST['fileName'];
			$post_module = $_POST['module'];
			$post_optional = $_POST['optionalType'];

			if(empty($post_optional)){
				$check_for_existing_template = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE filename = '$file_post_name' AND module = '$post_module' ");
			}
			else{
				$check_for_existing_template = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}ultimate_csv_importer_export_template WHERE filename = '$file_post_name' AND module = '$post_module' AND optional_type = '$post_optional' ");
			}

			if(empty($check_for_existing_template)){
				$wpdb->insert($wpdb->prefix.'ultimate_csv_importer_export_template',
					array('filename' => $file_post_name,
						'module' => $post_module,
						'optional_type' => $_POST['optionalType'],
						'export_type' => $_POST['exp_type'],
						'split' => $_POST['is_check_split'],
						'split_limit' => $_POST['limit'],
						'category_name' => $_POST['categoryName'],
						'conditions' => $export_conditions,
						'event_exclusions' => $export_event_exclusions,
						'export_mode' => 'normal',
						'createdtime' => $time,
						'offset' => $_POST['offset'],
						'actual_start_date' => $_POST['actual_start_date'],
						'actual_end_date' => $_POST['actual_end_date']
					),
					array('%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
				);
			}
			else{
				$id = $check_for_existing_template[0]->id;

				$wpdb->update( 
					$wpdb->prefix.'ultimate_csv_importer_export_template', 
					array(
						'export_type' => $_POST['exp_type'],
						'split' => $_POST['is_check_split'],
						'split_limit' => $_POST['limit'],
						'category_name' => $_POST['categoryName'],
						'conditions' => $export_conditions,
						'event_exclusions' => $export_event_exclusions,
						'export_mode' => 'normal',
						'createdtime' => $time,
						'offset' => $_POST['offset'],
						'actual_start_date' => $_POST['actual_start_date'],
						'actual_end_date' => $_POST['actual_end_date']
					),
					array( 'id' => $id )
				);
			}
			$this->exportData($this->module,$categorybased);

		}
	}


	public function commentsCount() {
		global $wpdb;
		self::generateHeaders($this->module, $this->optionalType);
		$get_comments = "select * from {$wpdb->prefix}comments";
		// Check status
		if($this->conditions['specific_status']['is_check'] == 'true') {
			if($this->conditions['specific_status']['status'] == 'Pending')
				$get_comments .= " where comment_approved = '0'";
			elseif($this->conditions['specific_status']['status'] == 'Approved')
				$get_comments .= " where comment_approved = '1'";
			else
				$get_comments .= " where comment_approved in ('0','1')";
		}
		else
			$get_comments .= " where comment_approved in ('0','1')";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			if($this->conditions['specific_period']['from'] == $this->conditions['specific_period']['to']){
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "'";
			}else{
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "' and comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
			}
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == '1') {
			if(isset($this->conditions['specific_authors']['author'])) {
				$get_comments .= " and comment_author_email = '".$this->conditions['specific_authors']['author']."'"; 
			}
		}
		$get_comments .= " order by comment_ID";
		$comments = $wpdb->get_results( $get_comments );
		$totalRowCount = count($comments);
		return $totalRowCount;
	}

	public function getOptionalType($module){
		if($module == 'Tags'){
			$optionalType = 'post_tag';
		}
		elseif($module == 'Posts'){
			$optionalType = 'posts';
		}
		elseif($module == 'Pages'){
			$optionalType = 'pages';
		} 
		elseif($module == 'Categories'){
			$optionalType = 'category';
		} 
		elseif($module == 'Users'){
			$optionalType = 'users';
		}
		elseif($module == 'Comments'){
			$optionalType = 'comments';
		}
		elseif($module == 'Images'){
			$optionalType = 'images';
		}
		elseif($module == 'CustomerReviews'){
			$optionalType = 'wpcr3_review';
		}
		elseif($module == 'WooCommerce' || $module == 'WooCommerceOrders' || $module == 'WooCommerceCoupons' || $module == 'WooCommerceRefunds' || $module == 'WooCommerceVariations' || $module == 'Marketpress' ){
			$optionalType = 'product';
		}
		elseif($module == 'WooCommerce'){
			$optionalType = 'product';
		}
		elseif($module == 'WPeCommerce'){
			$optionalType = 'wpsc-product';
		}
		elseif($module == 'WPeCommerce' ||$module == 'WPeCommerceCoupons'){
			$optionalType = 'wpsc-product';
		}
		return $optionalType;
	}

	/**
	 * set the delimiter
	 */
	public function setDelimiter($conditions)
	{		
		if (isset($conditions['optional_delimiter']) && $conditions['optional_delimiter'] != '') {
			return $conditions['optional_delimiter'];
		}
		elseif(isset($conditions['delimiter']) && $conditions['delimiter'] != 'Select'){
			if($conditions['delimiter'] == '{Tab}')
				return "\t";
			elseif ($conditions['delimiter'] == '{Space}')
				return " ";
			else
				return $conditions['delimiter'];
		}
		else{
			return ',';
		}
	}

	/**
	 * Export records based on the requested module
	 */
	public function exportData($mod = '',$cat = '') {
		switch ($this->module) {
		case 'Posts':
		case 'Pages':
		case 'CustomPosts':
		case 'WooCommerce':
		case 'Marketpress':
		case 'WooCommerceVariations':
		case 'WooCommerceOrders':
		case 'WooCommerceCoupons':
		case 'WooCommerceRefunds':
		case 'WPeCommerce':
		case 'WPeCommerceCoupons':
		case 'eShop':
			case 'Images':
			self::FetchDataByPostTypes($mod,$cat);
			break;
		case 'Users':
			self::FetchUsers();
			break;
		case 'Comments':
			self::FetchComments();
			break;
		
		
		case 'CustomerReviews':
			ExportExtension::$review_export->FetchCustomerReviews($this->module,$this->mode, $this->optionalType, $this->conditions,$this->offset,$this->limit);
			break;
		case 'Categories':
			ExportExtension::$post_export->FetchCategories($this->mode,$this->module,$this->optionalType);
			break;
		case 'Tags':
			ExportExtension::$post_export->FetchTags($this->mode,$this->module,$this->optionalType);
			break;
		case 'Taxonomies':
			ExportExtension::$woocom_export->FetchTaxonomies($this->mode,$this->module,$this->optionalType);
			break;

		}
	}

	/**
	 * Fetch users and their meta information
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchUsers($mode = null) {
		global $wpdb;
		self::generateHeaders($this->module, $this->optionalType);
		$get_available_user_ids = "select DISTINCT ID from {$wpdb->prefix}users u join {$wpdb->prefix}usermeta um on um.user_id = u.ID";
		if($this->conditions['specific_period']['is_check'] == 'true') {
			if($this->conditions['specific_period']['from'] == $this->conditions['specific_period']['to']){
				$get_available_user_ids .= " where u.user_registered >= '" . $this->conditions['specific_period']['from'] . "'";
			}else{
				$get_available_user_ids .= " where u.user_registered >= '" . $this->conditions['specific_period']['from'] . "' and u.user_registered <= '" . $this->conditions['specific_period']['to'] . "'";
			}
		}
		$availableUsers = $wpdb->get_col($get_available_user_ids);
		$this->totalRowCount = count($availableUsers);
		$get_available_user_ids .= " order by ID asc limit $this->offset, $this->limit";
		$availableUserss = $wpdb->get_col($get_available_user_ids);
		if(!empty($availableUserss)) {
			$whereCondition = '';
			foreach($availableUserss as $userId) {
				if($whereCondition != ''){
					$whereCondition = $whereCondition . ',' . $userId;
				}else{
					$whereCondition = $userId;
				}
				// Prepare the user details to be export
				$query_to_fetch_users = "SELECT * FROM {$wpdb->prefix}users where ID in ($whereCondition);";
				$users = $wpdb->get_results($query_to_fetch_users);
				if(!empty($users)) {
					foreach($users as $userInfo) {
						foreach($userInfo as $userKey => $userVal) {
							$this->data[$userId][$userKey] = $userVal;
						}
					}
				}
				// Prepare the user meta details to be export
				$query_to_fetch_users_meta = $wpdb->prepare("SELECT user_id, meta_key, meta_value FROM  {$wpdb->prefix}users wp JOIN {$wpdb->prefix}usermeta wpm  ON wpm.user_id = wp.ID where ID= %d", $userId);
				$userMeta = $wpdb->get_results($query_to_fetch_users_meta);

				if(!empty($userMeta)) {
					foreach($userMeta as $userMetaInfo) {
						if($userMetaInfo->meta_key == 'wp_capabilities') {
							$userRole = $this->getUserRole($userMetaInfo->meta_value);
							$this->data[ $userId ][ 'role' ] = $userRole;
						}
						elseif($userMetaInfo->meta_key == 'description') {
							$this->data[ $userId ][ 'biographical_info' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'comment_shortcuts') {
							$this->data[ $userId ][ 'enable_keyboard_shortcuts' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'show_admin_bar_front') {
							$this->data[ $userId ][ 'show_toolbar' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'rich_editing') {
							$this->data[ $userId ][ 'disable_visual_editor' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'locale') {
							$this->data[ $userId ][ 'language' ] = $userMetaInfo->meta_value;
						}
						else {
							$this->data[ $userId ][ $userMetaInfo->meta_key ] = $userMetaInfo->meta_value;
						}
					}	
					ExportExtension::$post_export->getPostsMetaDataBasedOnRecordId($userId, $this->module, $this->optionalType);
				}
			}
		}
		$result = self::finalDataToExport($this->data, $this->module ,$this->optionalType);
		if($mode == null)
			self::proceedExport($result);
		else
			return $result;
	}

	/**
	 * Fetch all Comments
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchComments($mode = null) {
		global $wpdb;
		self::generateHeaders($this->module, $this->optionalType);
		$get_comments = "select * from {$wpdb->prefix}comments";
		// Check status
		if($this->conditions['specific_status']['is_check'] == 'true') {
			if($this->conditions['specific_status']['status'] == 'Pending')
				$get_comments .= " where comment_approved = '0'";
			elseif($this->conditions['specific_status']['status'] == 'Approved')
				$get_comments .= " where comment_approved = '1'";
			else
				$get_comments .= " where comment_approved in ('0','1')";
		}
		else
			$get_comments .= " where comment_approved in ('0','1')";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			if($this->conditions['specific_period']['from'] == $this->conditions['specific_period']['to']){
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "'";
			}else{
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "' and comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
			}
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == '1') {
			if(isset($this->conditions['specific_authors']['author'])) {
				$get_comments .= " and comment_author_email = '".$this->conditions['specific_authors']['author']."'"; 
			}
		}
		$comments = $wpdb->get_results( $get_comments );
		$this->totalRowCount = count($comments);
		$get_comments .= " order by comment_ID asc limit $this->offset, $this->limit";
		$limited_comments = $wpdb->get_results( $get_comments );
		if(!empty($limited_comments)) {
			foreach($limited_comments as $commentInfo) {
				$user_id=$commentInfo->user_id;
				if(!empty($user_id)) {
					$users_login =  $wpdb->get_results("SELECT user_login FROM {$wpdb->prefix}users WHERE ID = '$user_id'");		
					foreach($users_login as $users_key => $users_value){
						foreach($users_value as $u_key => $u_value){
							$users_id=$u_value;
						}
					}
				}
				foreach($commentInfo as $commentKey => $commentVal) {
					$this->data[$commentInfo->comment_ID][$commentKey] = $commentVal;
					$this->data[$commentInfo->comment_ID]['user_id'] = $users_id;
				}
				$get_comment_rating = get_comment_meta($commentInfo->comment_ID, 'rating', true);
				if(!empty($get_comment_rating)){
					$this->data[$commentInfo->comment_ID]['comment_rating'] = $get_comment_rating;
				}
			}
		}
		$result = self::finalDataToExport($this->data, $this->module ,$this->optionalType);
		if($mode == null)
			self::proceedExport($result);
		else
			return $result;
	}
	
	/**
	 * Generate CSV headers
	 *
	 * @param $module       - Module to be export
	 * @param $optionalType - Exclusions
	 */
	public function generateHeaders ($module, $optionalType) {
		
		if($module == 'CustomPosts' || $module == 'Tags' || $module == 'Categories' || $module == 'Taxonomies'){
			if($optionalType == 'event'){
				$optionalType = 'Events';
			}elseif($optionalType == 'location'){
				$optionalType = 'Event Locations';
			}elseif($optionalType == 'event-recurring'){
				$optionalType = 'Recurring Events';
			}
			if(empty($optionalType)){
				$default = ExportExtension::$mapping_instance->get_fields($module);
			}
		
		else
			$default = ExportExtension::$mapping_instance->get_fields($optionalType);
		}
		else{
			$default = ExportExtension::$mapping_instance->get_fields($module);
			
		}
		$headers = [];
		foreach ($default as $key => $fields) {
			foreach($fields as $groupKey => $fieldArray) {
				foreach ( $fieldArray as $fKey => $fVal ) {
					if (is_array($fVal) || is_object($fVal)){
						foreach ( $fVal as $rKey => $rVal ) {
							if(!in_array($rVal['name'], $headers))
								$headers[] = $rVal['name'];

						}
					}
				}

			}
		}

		if(isset($this->eventExclusions['is_check']) && $this->eventExclusions['is_check'] == 'true') {
			$headers_with_exclusion = self::applyEventExclusion($headers);
			$this->headers = $headers_with_exclusion;
	
		}else{
			$this->headers = $headers;
			
		}
		
	}

	/**
	 * Fetch data by requested Post types
	 * @param $mode
	 * @return array
	 */
	public function FetchDataByPostTypes ($exp_mod,$exp_cat) {
		if(empty($this->headers))
			$this->generateHeaders($this->module, $this->optionalType);
		$recordsToBeExport = ExportExtension::$post_export->getRecordsBasedOnPostTypes($this->module, $this->optionalType, $this->conditions,$this->offset,$this->limit,$exp_mod,$exp_cat);
		
		if(!empty($recordsToBeExport)) {
			foreach($recordsToBeExport as $postId) {
				$this->data[$postId] = $this->getPostsDataBasedOnRecordId($postId);

				$exp_module = $this->module; 
				if($exp_module == 'Posts' || $exp_module =='WooCommerce' || $exp_module == 'CustomPosts' || $exp_module == 'Categories' || $exp_module == 'Tags' || $exp_module == 'Taxonomies' || $exp_module == 'Pages'){
					$this->getWPMLData($postId,$this->optionalType,$exp_module);
				}
				if($exp_module == 'Posts' ||  $exp_module == 'CustomPosts' ||$exp_module == 'Pages'){
					$this->getPolylangData($postId,$this->optionalType,$exp_module);
				}				
				ExportExtension::$post_export->getPostsMetaDataBasedOnRecordId($postId, $this->module, $this->optionalType);
				$this->getTermsAndTaxonomies($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerce')
					ExportExtension::$woocom_export->getProductData($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceRefunds')
					ExportExtension::$woocom_export->getWooComCustomerUser($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceOrders')
					ExportExtension::$woocom_export->getWooComOrderData($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceVariations')
					ExportExtension::$woocom_export->getProductData($postId, $this->module, $this->optionalType);
				if($this->module == 'WPeCommerce')
					ExportExtension::$ecom_export->getEcomData($postId, $this->module, $this->optionalType);
				if($this->module == 'WPeCommerceCoupons')
					ExportExtension::$ecom_export->getEcomCouponData($postId, $this->module, $this->optionalType);

				if($this->optionalType == 'lp_course')
					ExportExtension::$woocom_export->getCourseData($postId);
				if($this->optionalType == 'lp_lesson')
					ExportExtension::$woocom_export->getLessonData($postId);
				if($this->optionalType == 'lp_quiz')
					ExportExtension::$woocom_export->getQuizData($postId);
				if($this->optionalType == 'lp_question')
					ExportExtension::$woocom_export->getQuestionData($postId);
				if($this->optionalType == 'lp_order')
					ExportExtension::$woocom_export->getOrderData($postId);

				if($this->optionalType == 'nav_menu_item')
					ExportExtension::$woocom_export->getMenuData($postId);

				if($this->optionalType == 'widgets')
					self::$instance->getWidgetData($postId,$this->headers);	

			}
		}
		$result = self::finalDataToExport($this->data, $this->module ,$this->optionalType);

		if($mode == null)
			self::proceedExport( $result );
		else
			return $result;
	}	

	public function getWidgetData($postId, $headers){
		global $wpdb;
		$get_sidebar_widgets = get_option('sidebars_widgets');
		$total_footer_arr = [];
	
		foreach($get_sidebar_widgets as $footer_key => $footer_arr){
			if($footer_key != 'wp_inactive_widgets' || $footer_key != 'array_version'){
				if( strpos($footer_key, 'sidebar') !== false ){
					$get_footer = explode('-', $footer_key);
					$footer_number = $get_footer[1];

					foreach($footer_arr as $footer_values){
						$total_footer_arr[$footer_values] = $footer_number;
					}
				}
			}
		}
		
		foreach ($headers as $key => $value){
			$get_widget_value[$value] = $wpdb->get_row("SELECT option_value FROM {$wpdb->prefix}options where option_name = '{$value}'", ARRAY_A);
			
			$header_key = explode('widget_', $value);
			
			if ($value == 'widget_recent-posts'){
				$recent_posts = unserialize($get_widget_value[$value]['option_value']); 
				$recent_post = '';
				foreach($recent_posts as $dk => $dv){
					if($dk != '_multiwidget'){
						$post_key = $header_key[1].'-'.$dk;
						$recent_post .= $dv['title'].','.$dv['number'].','.$dv['show_date'].'->'.$total_footer_arr[$post_key].'|';
					}
				}
				$recent_post = rtrim($recent_post , '|');
			}
			elseif ($value == 'widget_pages'){
				$recent_pages = unserialize($get_widget_value[$value]['option_value']); 
				$recent_page = '';
				foreach($recent_pages as $dk => $dv){
					if(isset($dv['exclude'])){
						$exclude_value = str_replace(',', '/', $dv['exclude']);
					}

					if($dk != '_multiwidget'){
						$page_key = $header_key[1].'-'.$dk;
						$recent_page .= $dv['title'].','.$dv['sortby'].','.$exclude_value.'->'.$total_footer_arr[$page_key].'|';
					}
				}
				$recent_page = rtrim($recent_page , '|');
			}
			elseif ($value == 'widget_recent-comments'){
				$recent_comments = unserialize($get_widget_value[$value]['option_value']); 
				$recent_comment = '';
				foreach($recent_comments as $dk => $dv){
					if($dk != '_multiwidget'){
						$comment_key = $header_key[1].'-'.$dk;
						$recent_comment .= $dv['title'].','.$dv['number'].'->'.$total_footer_arr[$comment_key].'|';
					}
				}
				$recent_comment = rtrim($recent_comment , '|');
			}
			elseif ($value == 'widget_archives'){
				$recent_archives = unserialize($get_widget_value[$value]['option_value']); 
				$recent_archive = '';
				foreach($recent_archives as $dk => $dv){
					if($dk != '_multiwidget'){
						$archive_key = $header_key[1].'-'.$dk;
						$recent_archive .= $dv['title'].','.$dv['count'].','.$dv['dropdown'].'->'.$total_footer_arr[$archive_key].'|';
					}
				}
				$recent_archive = rtrim($recent_archive , '|');
			}
			elseif ($value == 'widget_categories'){
				$recent_categories = unserialize($get_widget_value[$value]['option_value']); 
				$recent_category = '';
				foreach($recent_categories as $dk => $dv){
					if($dk != '_multiwidget'){
						$cat_key = $header_key[1].'-'.$dk;
						$recent_category .= $dv['title'].','.$dv['count'].','.$dv['hierarchical'].','.$dv['dropdown'].'->'.$total_footer_arr[$cat_key].'|';
					}
				}
				$recent_category = rtrim($recent_category , '|');
			}
		}
			
		$this->data[$postId]['widget_recent-posts'] = $recent_post;
		$this->data[$postId]['widget_pages'] = $recent_page;
		$this->data[$postId]['widget_recent-comments'] = $recent_comment;
		$this->data[$postId]['widget_archives'] = $recent_archive;
		$this->data[$postId]['widget_categories'] = $recent_category;
	}

	/**
	 * Function used to fetch the Terms & Taxonomies for the specific posts
	 *
	 * @param $id
	 * @param $type
	 * @param $optionalType
	 */
	public function getTermsAndTaxonomies ($id, $type, $optionalType) {
		$TermsData = array();
		if($type == 'WooCommerce' || $type == 'Marketpress' || ($type == 'CustomPosts' && $type == 'WooCommerce')) {
			$type = 'product';
			$postTags = '';
			$taxonomies = get_object_taxonomies($type);
			$get_tags = get_the_terms( $id, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name . ',';
				}
			}
			$postTags = substr($postTags, 0, -1);
			$this->data[$id]['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				$postCategory = '';
				if($taxonomy == 'product_cat' || $taxonomy == 'product_category'){
					$get_categories = get_the_terms( $id, $taxonomy );
					if($get_categories){
						foreach($get_categories as $category){
							$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy) . ',';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}else{
					$get_categories = get_the_terms( $id, $taxonomy );
					if($get_categories){
						foreach($get_categories as $category){
							$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy) . ',';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id][$taxonomy] = $postCategory;
				}
			}
			if(($type == 'WooCommerce' && $type != 'CustomPosts') || $type == 'Marketpress' ) {
				$product = wc_get_product	($id);
				$pro_type = $product->get_type();
				switch ($pro_type) {
				case 'simple':
					$product_type = 1;
					break;
				case 'grouped':
					$product_type = 2;
					break;
				case 'external':
					$product_type = 3;
					break;
				case 'variable':
					$product_type = 4;
					break;
				case 'subscription':
					$product_type = 5;
					break;
				case 'variable-subscription':
					$product_type = 6;
					break;
				default:
					$product_type = 1;
					break;
				}
				$this->data[$id]['product_type'] = $product_type;
			}
			$shipping = get_the_terms( $id, 'product_shipping_class' );
			if(!(is_wp_error($shipping))){
				if($shipping){
					$taxo_shipping = $shipping[0]->name;	
					$this->data[$id][ 'product_shipping_class' ] = $taxo_shipping;
				}
			}
			
		} else if($type == 'WPeCommerce') {
			$type = 'wpsc-product';
			$postTags = $postCategory = '';
			$taxonomies = get_object_taxonomies($type);
			$get_tags = get_the_terms( $id, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name.',';
				}
			}
			$postTags = substr($postTags,0,-1);
			$this->data[$id]['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				$postCategory = '';
				if($taxonomy == 'wpsc_product_category'){
					$get_categories = wp_get_post_terms( $id, $taxonomy );
					if($get_categories){
						foreach($get_categories as $category){
							$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy).',';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}else{
					$get_categories = wp_get_post_terms( $id, $taxonomy );
					if($get_categories){
						foreach($get_categories as $category){
							$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy).',';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}
			}
		} else {
			global $wpdb;
			$postTags = $postCategory = '';
			$taxonomyId = $wpdb->get_col($wpdb->prepare("select term_taxonomy_id from {$wpdb->prefix}term_relationships where object_id = %d", $id));
			if(!empty($taxonomyId)) {
				foreach($taxonomyId as $taxonomy) {
					$taxonomyType = $wpdb->get_col($wpdb->prepare("select taxonomy from {$wpdb->prefix}term_taxonomy where term_taxonomy_id = %d", $taxonomy));
					if(!empty($taxonomyType)) {
						foreach($taxonomyType as $taxanomy_name) {
							if($taxanomy_name == 'category'){
								$termName = 'post_category';
							}else{
								$termName = $taxanomy_name;
							}
							if(in_array($termName, $this->headers)) {
								if($termName != 'post_tag') {

									$taxonomyData = $wpdb->get_col($wpdb->prepare("select name from {$wpdb->prefix}terms where term_id = %d",$taxonomy));
									if(!empty($taxonomyData)) {

										if(isset($TermsData[$termName])){
											$this->data[$id][$termName] = $TermsData[$termName] . ',' . $taxonomyData[0];
										}else{
											$get_exist_data = $this->data[$id][$termName];
										}

										if( $get_exist_data == '' ){
											$this->data[$id][$termName] = $taxonomyData[0];
										}else {
											$taxonomyID = $wpdb->get_col($wpdb->prepare("select term_id from {$wpdb->prefix}terms where name = %s",$taxonomyData[0]));
											$this->data[$id][$termName] = $get_exist_data . ',' . $this->hierarchy_based_term_name(get_term($taxonomy), $taxanomy_name);
										}

									}
								} else {
									if(!isset($TermsData['post_tag'])) {
										$get_tags = wp_get_post_tags($id, array('fields' => 'names'));
										foreach ($get_tags as $tags) {
											$postTags .= $tags . ',';
										}
										$postTags = substr($postTags, 0, -1);
										if( $this->data[$id][$termName] == '' ) {
											$this->data[$id][$termName] = $postTags;
										}
									}
								}
								if(!isset($TermsData['category'])){
									$get_categories = wp_get_post_categories($id, array('fields' => 'names'));
									foreach ($get_categories as $category) {
										$postCategory .= $category . ',';
									}
									$postCategory = substr($postCategory, 0, -1);
									$this->data[$id]['category'] = $postCategory;
								}

							}
							else{
								$this->data[$id][$termName] = '';
							}
						}
					}					
				}
			}
		}
	}

	/**
	 * Get user role based on the capability
	 * @param null $capability  - User capability
	 * @return int|string       - Role of the user
	 */
	public function getUserRole ($capability = null) {
		if($capability != null) {
			$getRole = unserialize($capability);
			foreach($getRole as $roleName => $roleStatus) {
				$role = $roleName;
			}
			return $role;
		} else {
			return 'subscriber';
		}
	}

	public function array_to_xml( $data, &$xml_data ) {
		foreach( $data as $key => $value ) {
			if( is_numeric($key) ){
				$key = 'item'; 
			}
			if( is_array($value) ) {
				$subnode = $xml_data->addChild($key);
				$this->array_to_xml($value, $subnode);
			} else {
				$xml_data->addChild("$key",htmlspecialchars("$value"));
			}
		}
	}

	/**
	 * Export Data
	 * @param $data
	 */
	public function proceedExport ($data) {
	
		$upload_dir = WP_CONTENT_DIR . '/uploads/smack_uci_uploads/exports/';
		if(!is_dir($upload_dir)) {
			wp_mkdir_p($upload_dir);
		}
		$base_dir = wp_upload_dir();
		$upload_url = $base_dir['baseurl'].'/smack_uci_uploads/exports/';
		chmod($upload_dir, 0777);
		if($this->checkSplit == 'true'){
			$i = 1;
			while ( $i != 0) {
				$file = $upload_dir . $this->fileName .'_'.$i.'.' . $this->exportType;
				if(file_exists($file)){
					$allfiles[$i] = $file;
					$i++;
				}
				else
					break;
			}
			$fileURL = $upload_url . $this->fileName.'_'.$i.'.' .$this->exportType;
		}
		else{
			$file = $upload_dir . $this->fileName .'.' . $this->exportType;
			$fileURL = $upload_url . $this->fileName.'.' .$this->exportType;
		}
		if ($this->offset == 0) {
			if(file_exists($file))
				unlink($file);
		}

		$checkRun = "no";
		if($this->checkSplit == 'true' && ($this->totalRowCount - $this->offset) > 0){
			$checkRun = 'yes';
		}
		if($this->checkSplit != 'true'){
			$checkRun = 'yes';
		}

		if($checkRun == 'yes'){
			if($this->exportType == 'xml'){
				$xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
				$this->array_to_xml($data,$xml_data);
				$result = $xml_data->asXML($file);
			}else{
				if($this->exportType == 'json')
					$csvData = json_encode($data);
				else
					$csvData = $this->unParse($data, $this->headers);
				try {
					file_put_contents( $file, $csvData, FILE_APPEND | LOCK_EX );
			} catch (\Exception $e) {
			}
			}
			}

			$this->offset = $this->offset + $this->limit;

			$filePath = $upload_dir . $this->fileName . '.' . $this->exportType;
			$filename = $fileURL;
			if(($this->offset) > ($this->totalRowCount) && $this->checkSplit == 'true'){
				$allfiles[$i] = $file;
				$zipname = $upload_dir . $this->fileName .'.' . 'zip';
				$zip = new \ZipArchive;
				$zip->open($zipname, \ZipArchive::CREATE);
				foreach ($allfiles as $allfile) {
					$newname = str_replace($upload_dir, '', $allfile);
					$zip->addFile($allfile, $newname);
			}
			$zip->close();
			$fileURL = $upload_url . $this->fileName.'.'.'zip';
			foreach ($allfiles as $removefile) {
				unlink($removefile);
			}
			$filename = $upload_url . $this->fileName.'.'.'zip';
			}
			if($this->checkSplit == 'true' && !($this->offset) > ($this->totalRowCount)){
				$responseTojQuery = array('success' => false, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $zipname, 'exported_path' => $zipname,'export_type'=>$this->exportType);
			}
			elseif($this->checkSplit == 'true' && (($this->offset) > ($this->totalRowCount))){
				$responseTojQuery = array('success' => true, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $fileURL, 'exported_path' => $fileURL,'export_type'=>$this->exportType);
			}
			elseif(!(($this->offset) > ($this->totalRowCount))){
				$responseTojQuery = array('success' => false, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $filename, 'exported_path' => $filePath,'export_type'=>$this->exportType);
			}
			else{
				$responseTojQuery = array('success' => true, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $filename, 'exported_path' => $filePath,'export_type'=>$this->exportType);
			}

			if($this->export_mode == 'normal'){
				echo wp_json_encode($responseTojQuery);
				wp_die();
			}
			else{
				$this->export_log = $responseTojQuery;
			}
			}

			/**
			 * Get post data based on the record id
			 * @param $id       - Id of the records
			 * @return array    - Data based on the requested id.
			 */
			public function getPostsDataBasedOnRecordId ($id) {
				global $wpdb;
				$PostData = array();
				$query1 = $wpdb->prepare("SELECT wp.* FROM {$wpdb->prefix}posts wp where ID=%d", $id);
				$result_query1 = $wpdb->get_results($query1);
				if (!empty($result_query1)) {
					foreach ($result_query1 as $posts) {
                    if(is_numeric($posts->post_parent) && $posts->post_parent !=='0'){
						$tit=get_the_title($posts->post_parent);
						$posts->post_parent=$tit;	
						}
						if($posts->post_type =='event' ||$posts->post_type =='event-recurring'){

							$loc=get_post_meta($id , '_location_id' , true);
							$event_id=get_post_meta($id , '_event_id' , true);
							$res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}em_locations WHERE location_id='$loc' "); 
			
							if($res){
								foreach($res as $location){
								$posts=array_merge((array)$posts,(array)$location);
								}
							}

								$ticket = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}em_tickets WHERE event_id='$event_id' "); 
								$count=count($ticket);
								if($count>1){
									$ticknamevalue = '';
									$tickidvalue = '';
									$eventidvalue = '';
									$tickdescvalue = '';
									$tickpricevalue = '';
									$tickstartvalue = '';
									$tickendvalue = '';
									$tickminvalue = '';
									$tickmaxvalue = '';
									$tickspacevalue = '';
									$tickmemvalue = '';
									$tickmemrolevalue = '';
									//$tickmemroles = '';
									$tickguestvalue = '';
									$tickreqvalue = '';
									$tickparvalue = '';
									$tickordervalue = '';
									$tickmetavalue = '';
									
									foreach($ticket as $tic => $ticval){
										$ticknamevalue .= $ticval->ticket_name . ', ';
										$tickidvalue .=$ticval->ticket_id . ', ';
										$eventidvalue .=$ticval->event_id . ', ';
										$tickdescvalue .=$ticval->ticket_description . ', ';
										$tickpricevalue .=$ticval->ticket_price . ', ';
										$tickstartvalue .=$ticval->ticket_start . ', ';
										$tickendvalue .=$ticval->ticket_end . ', ';
										$tickminvalue .=$ticval->ticket_min . ', ';
										$tickmaxvalue .=$ticval->ticket_max . ', ';
										$tickspacevalue .=$ticval->ticket_spaces . ', ';
										$tickmemvalue .=$ticval->ticket_members . ', ';
										$tickmemroles =unserialize($ticval->ticket_members_roles);
										$tickmemroleval=implode('| ',$tickmemroles);
										$tickmemrolevalue .=$tickmemroleval . ', ';
									
										
										$tickguestvalue .=$ticval->ticket_guests . ', ';
										$tickreqvalue .=$ticval->ticket_required . ', ';
										$tickparvalue .=$ticval->ticket_parent . ', ';
										$tickordervalue .=$ticval->ticket_order . ', ';
										$tickmetavalue .=$ticval->ticket_meta . ', ';
									
										$ticknamevalues = rtrim($ticknamevalue, ', ');
										$tickidvalues = rtrim($tickidvalue, ', ');
										$eventidvalues=rtrim($eventidvalue, ', ');
										$tickdescvalues=rtrim($tickdescvalue, ', ');
										$tickpricevalues =rtrim($tickpricevalue, ', ');
										$tickstartvalues   =rtrim($tickstartvalue, ', ');
										$tickendvalues   =rtrim($tickendvalue, ', ');
										$tickminvalues   =rtrim($tickminvalue, ', ');
										$tickmaxvalues =rtrim($tickmaxvalue, ', ');
										$tickspacevalues =rtrim($tickspacevalue, ', ');	
										$tickmemvalues	=rtrim($tickmemvalue, ', ');
										$tickmemrolevalues	=rtrim($tickmemrolevalue, ', ');
										$tickguestvalues	=rtrim($tickguestvalue, ', ');
										$tickreqvalues	=rtrim($tickreqvalue, ', ');
										$tickparvalues	=rtrim($tickparvalue, ', ');
										$tickordervalues	=rtrim($tickordervalue, ', ');	
										$tickmetavalues	=rtrim($tickmetavalue, ', ');	
										
										$tic_key1 = array('ticket_id', 'event_id', 'ticket_name','ticket_description','ticket_price','ticket_start','ticket_end','ticket_min','ticket_max','ticket_spaces','ticket_members','ticket_members_roles','ticket_guests','ticket_required','ticket_parent','ticket_order','ticket_meta');
								        $tic_val1 = array($tickidvalues,$eventidvalues, $ticknamevalues,$tickdescvalues,$tickpricevalues,$tickstartvalues,$tickendvalues,$tickminvalues,$tickmaxvalues,$tickspacevalues,$tickmemvalues,$tickmemrolevalues,$tickguestvalues,$tickreqvalues,$tickparvalues,$tickordervalues,$tickmetavalues);
								        $tickets1 = array_combine($tic_key1,$tic_val1);
										$posts=array_merge((array)$posts,(array)$tickets1);
										$ticket_start[] = $ticval->ticket_start;
										//$tickval  = array_values($ticket_start );
										$ticket_start_date = '';
										$ticket_start_time ='';
						                foreach(  $ticket_start as $loc =>$locval){
											$date = strtotime($locval);
											$ticket_start_date .= date('Y-m-d', $date) . ', ';
											$ticket_start_time .= date('H:i:s',$date) .', ';		  
			
										}
										$ticket_start_times = rtrim($ticket_start_time, ', ');
										$ticket_start_dates = rtrim($ticket_start_date, ', ');
										$ticket_end[] = trim($ticval->ticket_end);
										$ticket_end_time = '';
										$ticket_end_date = '';
										foreach($ticket_end as $loc => $locvalend){
											//$ticket_end=implode(',', $location1);
											$time = strtotime($locvalend);
											$ticket_end_date .= date('Y-m-d', $time) .', ';
											$ticket_end_time .= date('H:i:s',$time) .', ';
										   
						
										}	   
										$ticket_end_times = rtrim($ticket_end_time, ', ');
										$ticket_end_dates = rtrim($ticket_end_date, ', ');
										$tic_key = array('ticket_start_date', 'ticket_start_time', 'ticket_end_date','ticket_end_time');
										$tic_val = array($ticket_start_dates,$ticket_start_times, $ticket_end_dates,$ticket_end_times);
										$tickets = array_combine($tic_key,$tic_val);
					                    $posts=array_merge((array)$posts,(array)$tickets);
					
										
									}

								}
								else{
								    foreach($ticket as $tic => $ticval){
										$posts=array_merge((array)$posts,(array)$ticval);
										$ticket_start=$ticval->ticket_start;
										if($ticket_start != null){
											$date = strtotime($ticket_start);
											//$date=implode(',', $date1);
											$ticket_start_date = date('Y-m-d', $date);
											$ticket_start_time= date('H:i:s',$date);
											$ticket_end=$ticval->ticket_end;
											$time = strtotime($ticket_end);
											$ticket_end_date = date('Y-m-d', $time);
											$ticket_end_time= date('H:i:s',$time);
											$tic_key = array('ticket_start_date', 'ticket_start_time', 'ticket_end_date','ticket_end_time');
											$tic_val = array($ticket_start_date,$ticket_start_time, $ticket_end_date,$ticket_end_time);
											$tickets = array_combine($tic_key,$tic_val);
											$posts=array_merge((array)$posts,(array)$tickets);
										}
									}
								}
							
						}
						$p_type=$posts->post_type;
						$posid = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts  where post_name='$p_type' and post_type='_pods_pod'");
						foreach($posid as $podid){
							$pods_id=$podid->ID;
							$storage = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta  where post_id=$pods_id AND meta_key='storage'");
							foreach($storage as $pod_storage){
								$pod_stype=$pod_storage->meta_value;
							}

						}
						if($pod_stype=='table'){
							$tab='pods_'.$p_type;
							$tab_val = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}$tab where id=$id");
							foreach($tab_val as $table_key =>$table_val ){
								$posts=array_merge((array)$posts,(array)$table_val);

							}

						}
						foreach ($posts as $post_key => $post_value) {
							if ($post_key == 'post_status') {
								if (is_sticky($id)) {
									$PostData[$post_key] = 'Sticky';
									$post_status = 'Sticky';
								} else {
									$PostData[$post_key] = $post_value;
									$post_status = $post_value;
								}
							} else {
								$PostData[$post_key] = $post_value;

							}
							if ($post_key == 'post_password') {
								if ($post_value) {
									$PostData['post_status'] = "{" . $post_value . "}";
								} else {
									$PostData['post_status'] = $post_status;
								}
							}	
							if($post_key == 'post_author'){
								$user_info = get_userdata($post_value);
								$PostData['post_author'] = $user_info->user_login;
							}
						}




					}
				}
				return $PostData;
			} 
			
			public function getWPMLData ($id,$optional_type,$exp_module) {
				global $wpdb;
				global $sitepress;
				if($sitepress != null) {
					$icl_translation_table = $wpdb->prefix.'icl_translations';
					$get_language_code = $wpdb->get_var("select language_code from {$icl_translation_table} where element_id ='{$id}'");
					$get_source_language = $wpdb->get_var("select source_language_code from {$icl_translation_table} where element_id ='{$id}'");
					$get_trid = $wpdb->get_var("select trid from {$icl_translation_table} where element_id ='{$id}'");
					if(!empty($get_source_language)){
						$original_element_id_prepared = $wpdb->prepare(
							"SELECT element_id
							FROM {$wpdb->prefix}icl_translations
							WHERE trid=%d
							AND source_language_code IS NULL
							LIMIT 1",$get_trid
						);
						$element_id = $wpdb->get_var( $original_element_id_prepared );
						if($exp_module == 'Posts' || $exp_module == 'WooCommerce' || $exp_module == 'CustomPosts' || $exp_module == 'Pages'){
							$element_title = get_the_title( $element_id );
							$this->data[$id]['translated_post_title'] = $element_title;
						}
						else{
							$element_title =  $wpdb->get_var("select name from $wpdb->terms where term_id ='{$element_id}'");
							$this->data[$id]['translated_taxonomy_title'] = $element_title;
						}
					}
				    $this->data[$id]['language_code'] = $get_language_code;	
					return $this->data[$id];
				}	
			}
            public function getPolylangData ($id,$optional_type,$exp_module) {
				global $wpdb;
				global $sitepress;
				$terms=$wpdb->get_results("select term_taxonomy_id from $wpdb->term_relationships where object_id ='{$id}'");
				$terms_id=json_decode(json_encode($terms),true);
				foreach($terms_id as $termkey => $termvalue){
					$termids=$termvalue['term_taxonomy_id'];
					$check=$wpdb->get_var("select taxonomy from $wpdb->term_taxonomy where term_id ='{$termids}'");
					if($check == 'category'){
						$category=$wpdb->get_var("select name from $wpdb->terms where term_id ='{$termids}'");
						//$this->data[$id]['post_category'] = $category;
					}
					elseif($check =='language'){
						$language=$wpdb->get_var("select description from $wpdb->term_taxonomy where term_id ='{$termids}'");
						$lang=unserialize($language);
						$langcode=explode('_',$lang['locale']);
						$lang_code=$langcode[0];
						$this->data[$id]['language_code'] = $lang_code;
					}
					elseif($check == 'post_translations'){
						 $description=$wpdb->get_var("select description from $wpdb->term_taxonomy where term_id ='{$termids}'");
						 $desc=unserialize($description);
						 $post_id=array_values($desc);
						 $postid=min($post_id);
						 
						 $post_title=$wpdb->get_var("select post_title from $wpdb->posts where ID ='{$postid}'");
						 $this->data[$id]['translated_post_title'] = $post_title;
					}
					elseif($check == 'post_tag'){
						$tag=$wpdb->get_var("select name from $wpdb->terms where term_id ='{$termids}'");
						//$this->data[$id]['post_tag'] = $tag;
						
					}
				}
			}
			public function getAttachment($id)
			{
				global $wpdb;
				$get_attachment = $wpdb->prepare("select guid from {$wpdb->prefix}posts where ID = %d AND post_type = %s", $id, 'attachment');
				$attachment = $wpdb->get_results($get_attachment);
				$attachment_file = $attachment[0]->guid;
				return $attachment_file;
			}

			public function getRepeater($parent)
			{
				global $wpdb;
				$get_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts where post_parent = %d", $parent), ARRAY_A);
				$i = 0;
				foreach ($get_fields as $key => $value) {
					$array[$i] = $value['post_excerpt'];
					$i++;
				}
				return $array;	
			}

			/**
			 * Get types fields
			 * @return array    - Types fields
			 */
			public function getTypesFields() {
				$getWPTypesFields = get_option('wpcf-fields');
				$typesFields = array();
				if(!empty($getWPTypesFields) && is_array($getWPTypesFields)) {
					foreach($getWPTypesFields as $fKey){
						$typesFields[$fKey['meta_key']] = $fKey['name'];
					}
				}
				return $typesFields;
			}

			/**
			 * Final data to be export
			 * @param $data     - Data to be export based on the requested information
			 * @return array    - Final data to be export
			 */
			public function finalDataToExport ($data, $module = false , $optionalType = false) {
				
				global $wpdb;
				$result = array();
				foreach ($this->headers as $key => $value) {
					if($value == 'price'){
						unset($this->headers[$key]);	
					}
					if(empty($value)){
						unset($this->headers[$key]);
					}
				}
				
				// Fetch Category Custom Field Values
				if($module){
					if($module == 'Categories'){
						return $this->fetchCategoryFieldValue($data, $this->module);
					}
				}

				$toolset_relationship_fieldnames = ['types_relationship', 'relationship_slug', 'intermediate'];
				foreach ( $data as $recordId => $rowValue ) {
	
					foreach ($this->headers as $hKey) {
						if(is_array($rowValue) && array_key_exists($hKey, $rowValue) && (!empty($rowValue[$hKey])) ){
							if(is_array($this->typeOftypesField) && array_key_exists('wpcf-'.$hKey, $this->typeOftypesField)){
								if($rowValue[$hKey] == 'Array'){
									$result[$recordId][$hKey] = $this->getToolsetRepeaterFieldValue($hKey, $recordId, $rowValue[$hKey]);
								}else{
									$result[$recordId][$hKey] = $this->returnMetaValueAsCustomerInput($rowValue[$hKey], $hKey);
								}
							}
							
							elseif($optionalType == 'elementor_library'){
								if($hKey == '_elementor_data'){
									$unserialize = json_decode($rowValue[$hKey]);
									$serialize = json_encode($unserialize);
									$result[$recordId][$hKey] = $serialize;	
								}
								elseif ($hKey ==''){
									$result[$recordId][$hKey] = $rowValue[$hKey];
								}
							}

							else{
								$result[$recordId][$hKey] = $this->returnMetaValueAsCustomerInput($rowValue[$hKey], $hKey);
							}
						}	
						else{
							$key = $hKey;
							$key = $this->replace_prefix_aioseop_from_fieldname($key);
						
							$key = $this->replace_prefix_yoast_wpseo_from_fieldname($key);
							$key = $this->replace_prefix_wpcf_from_fieldname($key);
							$key = $this->replace_prefix_wpsc_from_fieldname($key);
							$key = $this->replace_underscore_from_fieldname($key);
							$key = $this->replace_wpcr3_from_fieldname($key);
							// Change fieldname depends on the post type
							$key = $this->change_fieldname_depends_on_post_type($rowValue['post_type'], $key);			
							if(is_array($this->typeOftypesField) && array_key_exists('wpcf-'.$key, $this->typeOftypesField)){
								$rowValue[$key] = $this->getToolsetRepeaterFieldValue($key, $recordId);
							}else if($key == 'Parent_Group'){
								$rowValue[$key] = $this->getToolsetRepeaterParentValue($module);
							}else if($toolset_group_title = $this->hasToolsetRelationship($key, $recordId)){
								$rowValue[$key] = $toolset_group_title;
							}else if(isset($rowValue['wpcr3_'.$key])){
								$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['wpcr3_'.$key], $hKey);
							}else{
								if(is_array($this->allacf) && array_key_exists($key, $this->allacf)){
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue[$this->allacf[$key]], $hKey);
								}
								elseif($optionalType == 'elementor_library'){

								}
								else if(isset($rowValue['_yoast_wpseo_'.$key])){ // Is available in yoast plugin
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['_yoast_wpseo_'.$key]);
								}
								else if(isset($rowValue['_aioseop_'.$key])){ // Is available in all seo plugin
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['_aioseop_'.$key]);
								}
								else if(isset($rowValue['_'.$key])){ // Is wp custom fields
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['_'.$key], $hKey);
								}
								else if($fieldvalue = $this->getWoocommerceMetaValue($key, $rowValue['post_type'], $rowValue)){
									$rowValue[$key] = $fieldvalue;
								}
								else{
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue[$key], $hKey);
								}
							}
							global  $wpdb;
							if(in_array($hKey, $toolset_relationship_fieldnames)){

								if(in_array($hKey,['relationship_slug', 'intermediate'])){
									$toolset_fieldvalues = $this->getToolSetIntermediateFieldValue($rowValue['ID']);
								}elseif(in_array($hKey,['relationship_slug', 'types_relationship'])){
									$toolset_fieldvalues = $this->getToolSetRelationshipValue($rowValue['ID']);
								}

								$rowValue['types_relationship'] = $toolset_fieldvalues['types_relationship'];
								$rowValue['relationship_slug'] = $toolset_fieldvalues['relationship_slug'];
								$rowValue['intermediate'] = $toolset_fieldvalues['intermediate'];
							}

							//Added for user export
							if($key =='user_login')
							{
								$wpsc_query = $wpdb->prepare("select ID from {$wpdb->prefix}users where user_login =%s", $rowValue['user_login']);
								$wpdb->get_results($wpsc_query,ARRAY_A);
							}	
					
							if($rowValue['post_excerpt']||$rowValue['_wp_attachment_image_alt']||$rowValue['post_content']||$rowValue['guid']||$rowValue['post_title']||$rowValue['_wp_attached_file']){
								if($key='caption'){
									$rowValue[$key]=$rowValue['post_excerpt'];
								}
								if($key='alt_text'){
									$rowValue[$key]=$rowValue['_wp_attachment_image_alt'];
								}
								if($key='description'){
									$rowValue[$key]=$rowValue['post_content'];
								}
								if($key='title'){
									$rowValue[$key]=$rowValue['post_title'];
								}
								if($key='file_name'){
						
									$file_names = explode('/', $rowValue['_wp_attached_file']);
									$file_name= $file_names[2];
									
									$rowValue[$key]=$file_name;
								}
							}
							if($rowValue['_bbp_forum_type'] =='forum'||$rowValue['_bbp_forum_type']=='category' ){
								if($key =='Visibility'){
									$rowValue[$key]=$rowValue['post_status'];
								}
							}
							if($key =='topic_status' ||$key =='author' ||$key =='topic_type' ){
								$rowValue['topic_status']=$rowValue['post_status'];
								$rowValue['author']=$rowValue['post_author'];
								if($key =='topic_type'){
									$Topictype =get_post_meta($rowValue['_bbp_forum_id'],'_bbp_sticky_topics');
									$topic_types = get_option('_bbp_super_sticky_topics');
									$rowValue['topic_type']='Normal';
									if($Topictype){
										foreach($Topictype as $t_type){
											if($t_type['0']== $recordId){
												$rowValue['topic_type']='sticky';
											}
										}
									}elseif(!empty($topic_types)){
										foreach($topic_types as $top_type){
											if($top_type == $rowValue['ID']){
												$rowValue['topic_type']='super sticky';
											}
										}
									}
								}	
							}if($key =='reply_status'||$key =='reply_author'){
							$rowValue['reply_status']=$rowValue['post_status'];
							$rowValue['reply_author']=$rowValue['post_author'];
								}
							
							if(array_key_exists($hKey, $rowValue)){
								if($hKey=='focus_keyword'){
									$rowValue[$hKey]= $rowValue['_yoast_wpseo_focuskw'];
	
								}
								elseif($hKey=='meta_desc') {
									  $rowValue[$hKey]=$rowValue['_yoast_wpseo_metadesc'];
								} 
								$result[$recordId][$hKey] = $rowValue[$hKey];
								}else{
									$result[$recordId][$hKey] = '';
								}
							
						}
					}		
				}
				
				return $result;
			}

			public function hasToolsetRelationship($fieldname, $post_id){
				global $wpdb;
				if(is_plugin_active('types/wpcf.php')){
					include_once( 'wp-admin/includes/plugin.php' );
		            $plugins = get_plugins();
				    $plugin_version = $plugins['types/wpcf.php']['Version'];
				    if($plugin_version < '3.4.1'){
						$toolset_relationship_id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."toolset_relationships WHERE slug = '".$fieldname."'");
						if(!empty($toolset_relationship_id)){
							$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE relationship_id = ".$toolset_relationship_id." AND parent_id = ".$post_id);
							$relationship_title = '';
							foreach($child_ids as $child_id){
								$relationship_title.= $wpdb->get_var("SELECT post_title FROM ".$wpdb->prefix."posts WHERE ID = ".$child_id->child_id).'|';
							}
							return rtrim($relationship_title, '|');
						}
					}
					else{
						$relationstitle = $this->hasToolsetRelationshipNew($fieldname, $post_id);
						return $relationstitle;
					}
				}
				
			}

			public function hasToolsetRelationshipNew($fieldname, $post_id){
				global $wpdb;
				if(is_plugin_active('types/wpcf.php')){
					$toolset_relationship_id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."toolset_relationships WHERE slug = '".$fieldname."'");
					if(!empty($toolset_relationship_id)){
						$post_par_id = $wpdb->get_row("SELECT group_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE element_id = ".$post_id );
						$post_par_ids = $post_par_id->group_id;
						$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE relationship_id = ".$toolset_relationship_id." AND parent_id = ".$post_par_ids);
						$relationship_title = '';
						foreach($child_ids as $child_id){
							$post_child_id = $wpdb->get_row("SELECT element_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE group_id = ".$child_id->child_id );
							$post_child_ids = $post_child_id->element_id;
							$relationship_title.= $wpdb->get_var("SELECT post_title FROM ".$wpdb->prefix."posts WHERE ID = ".$post_child_ids).'|';
						}
						return rtrim($relationship_title, '|');
					}
				}
			}

			public function getToolsetRepeaterParentValue($modes){
				global $wpdb;	
				$check_group_names = '';
				$mode = ExportExtension::$post_export->import_post_types($modes);

				if($modes == 'CustomPosts'){
					$mode = $this->optionalType;
				}

				$get_group = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}posts WHERE post_type = 'wp-types-group' AND post_status = 'publish' ");
				foreach($get_group as $get_group_values){
					$check_group = get_post_meta($get_group_values->id , '_wp_types_group_post_types' , true);
					$check_group = explode(',' , $check_group);
					if(in_array( $mode , $check_group)){
						$check_group_names .= $wpdb->get_var("SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = $get_group_values->id") . "|";
					}
				}
				return rtrim($check_group_names , '|');
			}

			public function getToolsetRepeaterFieldValue($fieldname, $post_id, $fieldvalue = false){
				global $wpdb;
				include_once( 'wp-admin/includes/plugin.php' );
				$plugins = get_plugins();
				$plugin_version = $plugins['types/wpcf.php']['Version'];
					if($plugin_version < '3.4.1'){
					
						switch($this->alltoolsetfields[$fieldname]['type']){	
						case 'textfield':
						case 'textarea':
						case 'image':
						case 'audio':
						case 'colorpicker':
						case 'image':
						case 'file':
						case 'embed':
						case 'email':
						case 'numeric':
						case 'phone':
						case 'skype':
						case 'url':
						case 'video':
						case 'wysiwyg':
						case 'checkbox':
							$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_id );
							$toolset_fieldvalue = '';
							foreach($child_ids as $child_id){		
								$meta_value = get_post_meta($child_id->child_id, 'wpcf-'.$fieldname, true);
								$toolset_fieldvalue.=$this->returnMetaValueAsCustomerInput($meta_value).'|';
							}
							$toolset_fieldvalue = ltrim($toolset_fieldvalue, '|');
							if(empty($toolset_fieldvalue)){
								return $fieldvalue;
							}
							return rtrim($toolset_fieldvalue, '|');
						case 'radio': 
						case 'select':
						case 'checkboxes':
							$toolset_fieldvalue = '';
							$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_id );	
							foreach($child_ids as $child_id){		
								$meta_value = get_post_meta($child_id->child_id, 'wpcf-'.$fieldname, true);	
								$toolset_fieldvalue.=$this->returnMetaValueAsCustomerInput($meta_value, '', $this->alltoolsetfields[$fieldname]['type']).'|';
							}
							$toolset_fieldvalue = ltrim($toolset_fieldvalue, '|');
							if(empty($toolset_fieldvalue)){
								return $fieldvalue;
							}
							return rtrim($toolset_fieldvalue, '|');

						case 'date':
							$toolset_fieldvalue = '';
							$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_id );
							foreach($child_ids as $child_id){
								$meta_value = get_post_meta($child_id->child_id, 'wpcf-'.$fieldname, true);
								$meta_value = date('m/d/Y', $meta_value);
								$toolset_fieldvalue.=$this->returnMetaValueAsCustomerInput($meta_value).'|';
							}
							$toolset_fieldvalue = ltrim($toolset_fieldvalue, '|');
							if(empty($toolset_fieldvalue)){
								return $fieldvalue;
							}
							return rtrim($toolset_fieldvalue, '|');		
						}
					}
					else{
						$toolsetfields =$this->getToolsetRepeaterFieldValueNew($fieldname, $post_id, $fieldvalue = false);
						return $toolsetfields;
					}
				return false;
			}


			public function getToolsetRepeaterFieldValueNew($fieldname, $post_id, $fieldvalue = false){
				global $wpdb;
				
				$post_ids = $wpdb->get_row("SELECT group_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE element_id = ".$post_id );
				$post_par_ids = $post_ids->group_id;
			
				switch($this->alltoolsetfields[$fieldname]['type']){	
				case 'textfield':
				case 'textarea':
				case 'image':
				case 'audio':
				case 'colorpicker':
				case 'image':
				case 'file':
				case 'embed':
				case 'email':
				case 'numeric':
				case 'phone':
				case 'skype':
				case 'url':
				case 'video':
				case 'wysiwyg':
				case 'checkbox':
					
					$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_par_ids );
				
					$toolset_fieldvalue = '';
					foreach($child_ids as $child_id){	
						$post_child_id = $wpdb->get_row("SELECT element_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE group_id = ".$child_id->child_id );
						$post_child_ids = $post_child_id->element_id;	
						$meta_value = get_post_meta($post_child_ids, 'wpcf-'.$fieldname, true);
						$toolset_fieldvalue.=$this->returnMetaValueAsCustomerInput($meta_value).'|';
					}
					$toolset_fieldvalue = ltrim($toolset_fieldvalue, '|');
					if(empty($toolset_fieldvalue)){
						return $fieldvalue;
					}
					return rtrim($toolset_fieldvalue, '|');
				case 'radio': 
				case 'select':
				case 'checkboxes':
					$toolset_fieldvalue = '';
					//$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_id );	
					$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_par_ids );	
					foreach($child_ids as $child_id){
						$post_child_id = $wpdb->get_row("SELECT element_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE group_id = ".$child_id->child_id );
						$post_child_ids = $post_child_id->element_id;	
						$meta_value = get_post_meta($post_child_ids, 'wpcf-'.$fieldname, true);	
						$toolset_fieldvalue.=$this->returnMetaValueAsCustomerInput($meta_value, '', $this->alltoolsetfields[$fieldname]['type']).'|';
					}
					$toolset_fieldvalue = ltrim($toolset_fieldvalue, '|');
					if(empty($toolset_fieldvalue)){
						return $fieldvalue;
					}
					return rtrim($toolset_fieldvalue, '|');

				case 'date':
					$toolset_fieldvalue = '';
					//$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_id );
					$child_ids = $wpdb->get_results("SELECT child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = ".$post_par_ids );
					foreach($child_ids as $child_id){
						$post_child_id = $wpdb->get_row("SELECT element_id FROM ".$wpdb->prefix."toolset_connected_elements WHERE group_id = ".$child_id->child_id );
						$post_child_ids = $post_child_id->element_id;	
						$meta_value = get_post_meta($post_child_ids, 'wpcf-'.$fieldname, true);
						$meta_value = date('m/d/Y', $meta_value);
						$toolset_fieldvalue.=$this->returnMetaValueAsCustomerInput($meta_value).'|';
					}
					$toolset_fieldvalue = ltrim($toolset_fieldvalue, '|');
					if(empty($toolset_fieldvalue)){
						return $fieldvalue;
					}
					return rtrim($toolset_fieldvalue, '|');		
				}
			
				return false;
			}

			public function getWoocommerceMetaValue($fieldname, $post_type, $post){
				if($post_type == 'shop_order_refund'){
					switch ($fieldname) {
					case 'REFUNDID':
						return $post['ID'];
					default:
						return $post[$fieldname];
					}
				}else if($post_type == 'shop_order'){
					switch ($fieldname) {
					case 'ORDERID':
						return $post['ID'];
					case 'order_status':
						return $post['post_status'];
					case 'customer_note':
						return $post['post_excerpt'];
					case 'order_date':
						return $post['post_date'];
					default:
						return $post[$fieldname];
					}
				}else if($post_type == 'shop_coupon'){
					switch ($fieldname) {
					case 'COUPONID':
						return $post['ID'];
					case 'coupon_status':
						return $post['post_status'];
					case 'description':
						return $post['post_excerpt'];
					case 'coupon_date':
						return $post['post_date'];
					case 'coupon_code':
						return $post['post_title'];
					case 'expiry_date':
						$timeinfo=date('m/d/Y',$post['date_expires']);
						return $timeinfo;		
					default:
						return $post[$fieldname];
					}
				}else if($post_type == 'product_variation'){
					switch ($fieldname) {
					case 'VARIATIONID':
						return $post['ID'];
					case 'PRODUCTID':
						return $post['post_parent'];
					case 'VARIATIONSKU':
						return $post['sku'];
					default:
						return $post[$fieldname];
					}
				}
				return false;
			}

			/**
			 * Create CSV data from array
			 * @param array $data       2D array with data
			 * @param array $fields     field names
			 * @param bool $append      if true, field names will not be output
			 * @param bool $is_php      if a php die() call should be put on the first
			 *                          line of the file, this is later ignored when read.
			 * @param null $delimiter   field delimiter to use
			 * @return string           CSV data (text string)
			 */
			public function unParse ( $data = array(), $fields = array(), $append = false , $is_php = false, $delimiter = null) {
				if ( !is_array($data) || empty($data) ) $data = &$this->data;
				if ( !is_array($fields) || empty($fields) ) $fields = &$this->titles;
				if ( $this->delimiter === null ) $this->delimiter = ',';

				$string = ( $is_php ) ? "<?php header('Status: 403'); die(' '); ?>".$this->linefeed : '' ;
				$entry = array();
				// create heading
				if ($this->offset == 0 || $this->checkSplit == 'true') {
					if ( $this->heading && !$append && !empty($fields) ) {
						foreach( $fields as $key => $value ) {
							$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($this->delimiter, $entry).$this->linefeed;
			$entry = array();
			}
			}

			// create data
			foreach( $data as $key => $row ) {
				foreach( $row as $field => $value ) {
					$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($this->delimiter, $entry).$this->linefeed;
			$entry = array();
			}
			return $string;
			}

			/**
			 * Enclose values if needed
			 *  - only used by unParse()
			 * @param null $value
			 * @return mixed|null|string
			 */
			public function _enclose_value ($value = null) {
				if ( $value !== null && $value != '' ) {
					$delimiter = preg_quote($this->delimiter, '/');
					$enclosure = preg_quote($this->enclosure, '/');
					if($value[0]=='=') $value="'".$value; # Fix for the Comma separated vulnerabilities.
					if ( preg_match("/".$delimiter."|".$enclosure."|\n|\r/i", $value) || ($value{0} == ' ' || substr($value, -1) == ' ') ) {
						$value = str_replace($this->enclosure, $this->enclosure.$this->enclosure, $value);
						$value = $this->enclosure.$value.$this->enclosure;
					}
					else
						$value = $this->enclosure.$value.$this->enclosure;
				}
				return $value;
			}

		/**
		 * Apply exclusion before export
		 * @param $headers  - Apply exclusion headers
		 * @return array    - Available headers after applying the exclusions
		 */
			public function applyEventExclusion ($headers) {
				$header_exclusion = array();
				foreach ($headers as $hVal) {
					if(array_key_exists($hVal, $this->eventExclusions['exclusion_headers']['header'])) {
						$header_exclusion[] = $hVal;
					}
				}
				return $header_exclusion;
			}

			public function replace_prefix_aioseop_from_fieldname($fieldname){
				if(preg_match('/_aioseop_/', $fieldname)){
					return preg_replace('/_aioseop_/', '', $fieldname);
				}

				return $fieldname;
			}

			public function replace_prefix_pods_from_fieldname($fieldname){
				if(preg_match('/_pods_/', $fieldname)){
					return preg_replace('/_pods_/', '', $fieldname);
				}
				return $fieldname;
			}

			public function replace_prefix_yoast_wpseo_from_fieldname($fieldname){

				if(preg_match('/_yoast_wpseo_/', $fieldname)){
					$fieldname = preg_replace('/_yoast_wpseo_/', '', $fieldname);

					if($fieldname == 'focuskw') {
						$fieldname = 'focus_keyword';
					}else if($fieldname == 'bread-crumbs-title') { // It is comming as bctitle nowadays
						$fieldname = 'bctitle';
					}elseif($fieldname == 'metadesc') {
						$fieldname = 'meta_desc';
					}
				}

				return $fieldname;
			}

			public function replace_prefix_wpcf_from_fieldname($fieldname){
				if(preg_match('/_wpcf/', $fieldname)){
					return preg_replace('/_wpcf/', '', $fieldname);
				}
				return $fieldname;
			}

			public function replace_prefix_wpsc_from_fieldname($fieldname){
				if(preg_match('/_wpsc_/', $fieldname)){
					return preg_replace('/_wpsc_/', '', $fieldname);
				}
				return $fieldname;
			}

			public function replace_wpcr3_from_fieldname($fieldname){
				if(preg_match('/wpcr3_/', $fieldname)){
					$fieldname = preg_replace('/wpcr3_/', '', $fieldname);
				}
				return $fieldname;
			}

			public function change_fieldname_depends_on_post_type($post_type, $fieldname){
				if($post_type == 'wpcr3_review'){
					switch ($fieldname) {
					case 'ID':
						return 'review_id';
					case 'post_status':
						return 'status';
					case 'post_content':
						return 'review_text';
					case 'post_date':
						return 'date_time';
					default:
						return $fieldname;
					}
				}
				if($post_type == 'shop_order_refund'){
					switch ($fieldname) {
					case 'ID':
						return 'REFUNDID';
					default:
						return $fieldname;
					}
				}else if($post_type == 'shop_order'){
					switch ($fieldname) {
					case 'ID':
						return 'ORDERID';
					case 'post_status':
						return 'order_status';
					case 'post_excerpt':
						return 'customer_note';
					case 'post_date':
						return 'order_date';
					default:
						return $fieldname;
					}
				}else if($post_type == 'shop_coupon'){
					switch ($fieldname) {
					case 'ID':
						return 'COUPONID';
					case 'post_status':
						return 'coupon_status';
					case 'post_excerpt':
						return 'description';
					case 'post_date':
						return 'coupon_date';
					case 'post_title':
						return 'coupon_code';
					default:
						return $fieldname;
					}
				}else if($post_type == 'product_variation'){
					switch ($fieldname) {
					case 'ID':
						return 'VARIATIONID';
					case 'post_parent':
						return 'PRODUCTID';
					case 'sku':
						return 'VARIATIONSKU';
					default:
						return $fieldname;
					}
				}
				return $fieldname;
			}

			public function replace_underscore_from_fieldname($fieldname){
				if(preg_match('/_/', $fieldname)){
					$fieldname = preg_replace('/^_/', '', $fieldname);
				}
				return $fieldname;
			}

			public function fetchCategoryFieldValue($categories){
				global $wpdb;
				$bulk_category = [];
				foreach($categories as $category_id => $category){
					$term_meta = get_term_meta($category_id);
					$single_category = [];
					foreach($this->headers as $header){
						if($header == 'name'){
							$single_category[$header] = $this->hierarchy_based_term_name(get_term($category_id), 'category');
							continue;
						}

						if(array_key_exists($header, $category)){
							$single_category[$header] = $category[$header];
						}else{
							if(isset($term_meta[$header])){
								$single_category[$header] = $this->returnMetaValueAsCustomerInput($term_meta[$header]);
							}else{
								$single_category[$header] = null;
							}
						}
					}
					array_push($bulk_category, $single_category);
				}
				return $bulk_category;
			}

			public function returnMetaValueAsCustomerInput($meta_value, $header = false , $data_type = false){

				if(is_array($meta_value)){
					if($data_type == 'checkboxes'){	
						$metas_value = '';
						foreach($meta_value as $key => $meta_values){
							$meta_value = $meta_values[0];
							if(!empty($meta_value)){
								$metas_value .= $meta_value . ',';
							}
						}
						return rtrim($metas_value , ',');
					}			
					$meta_value = $meta_value[0];
					if(!empty($meta_value)){
						if(is_serialized($meta_value)){
							return unserialize($meta_value);
						}else if(is_array($meta_value)){
							return implode('|', $meta_value);
						}else if(is_string($meta_value)){
							return $meta_value;
						}else if($this->isJSON($meta_value) === true){
							return json_decode($meta_value);
						}
						return $meta_value;
					}
					return $meta_value;
				}else{
					if(is_serialized($meta_value)){
						$meta_value = unserialize($meta_value);
						if(is_array($meta_value)){
							return implode('|', $meta_value);	
						}
						return $meta_value;
					}else if(is_array($meta_value)){
						return implode('|', $meta_value);
					}else if(is_string($meta_value)){
						return rtrim($meta_value , '|');
						//return $meta_value;
					}else if($this->isJSON($meta_value) === true){
						return json_decode($meta_value);
					}
				}
				return $meta_value;
			}

			public function isJSON($meta_value) {
				$json = json_decode($meta_value);
				return $json && $meta_value != $json;
			}

			public function hierarchy_based_term_name($term, $taxanomy_type){
				$temp_hierarchy_terms = [];
				if(!empty($term->parent)){
					$temp_hierarchy_terms[] = $term->name;
					$hierarchy_terms = $this->call_back_to_get_parent($term->parent, $taxanomy_type, $temp_hierarchy_terms);
					return $this->split_terms_by_arrow($hierarchy_terms);

				}else{
					return $term->name;
				}
			}

			public function call_back_to_get_parent($term_id, $taxanomy_type, $temp_hierarchy_terms = []){
				$term = get_term($term_id, $taxanomy_type);
				if(!empty($term->parent)){
					$temp_hierarchy_terms[] = $term->name;
					$temp_hierarchy_terms = $this->call_back_to_get_parent($term->parent, $taxanomy_type, $temp_hierarchy_terms);
				}else{
					$temp_hierarchy_terms[] = $term->name;
				}
				return $temp_hierarchy_terms;
			}

			public function split_terms_by_arrow($hierarchy_terms){
				krsort($hierarchy_terms);
				return implode('>', $hierarchy_terms);
			}

			public function getToolSetRelationshipValue($post_id){

				include_once( 'wp-admin/includes/plugin.php' );
				$plugins = get_plugins();
				$plugin_version = $plugins['types/wpcf.php']['Version'];
				if($plugin_version < '3.4.1'){
					global $wpdb;
					$toolset_relation_values = array();
					$toolset_intermadiate_values = array();
					$toolset_fieldvalues = array();
					$get_slug = "SELECT distinct relationship_id FROM {$wpdb->prefix}toolset_associations WHERE parent_id ='{$post_id}'";
					$relat_slug = $wpdb->get_results($get_slug,ARRAY_A);
					$get_slug1 = "SELECT distinct relationship_id FROM {$wpdb->prefix}toolset_associations WHERE child_id ='{$post_id}'";
					$relat_slug1 = $wpdb->get_results($get_slug1,ARRAY_A);
					$rel_slug = (object) array_merge( (array) $relat_slug, (array) $relat_slug1); 
					foreach($rel_slug as $relkey=>$relvalue)
					{
						$relationship_id = $relvalue['relationship_id'];
						if(!empty($relationship_id)){
							$slug_id="SELECT slug FROM {$wpdb->prefix}toolset_relationships WHERE id IN ($relationship_id) AND origin = 'wizard' ";
							$relationship=$wpdb->get_results($slug_id,ARRAY_A);
						}
	
						if(is_array($relationship)){
							foreach($relationship as $keys=>$values) {
								$toolset_relation_values['relationship_slug'] .= $values['slug'] . '|';
							}	
						}
						$relationships_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}toolset_relationships WHERE id = $relationship_id AND origin = 'wizard' ");
	
						$parents_post = "SELECT post_title FROM {$wpdb->prefix}toolset_associations INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}toolset_associations.child_id WHERE {$wpdb->prefix}toolset_associations.parent_id={$post_id} AND {$wpdb->prefix}toolset_associations.relationship_id={$relationships_id} AND post_status = 'publish'";
						$parent_title1 = $wpdb->get_results($parents_post,ARRAY_A);
	
						$parents_post1 = "SELECT post_title FROM {$wpdb->prefix}toolset_associations INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}toolset_associations.parent_id WHERE {$wpdb->prefix}toolset_associations.child_id={$post_id} AND {$wpdb->prefix}toolset_associations.relationship_id={$relationships_id} AND post_status = 'publish'";
						$parent_title2 = $wpdb->get_results($parents_post1,ARRAY_A);
	
						$parent_title = array_merge($parent_title1, $parent_title2);
	
						$parent_value = '';
						for($i = 0 ; $i<count($parent_title) ; $i++){
							$parent_value .= $parent_title[$i]['post_title'] . ",";
						}
						$parent_value = rtrim($parent_value , ",");
						$toolset_intermadiate_values['types_relationship'] .= $parent_value . "|";
	
					}
					if(is_array($toolset_relation_values)){
						foreach($toolset_relation_values as $relation_value){
							$toolset_fieldvalues['relationship_slug'] = rtrim($relation_value , "|");
						}
					}	
					foreach($toolset_intermadiate_values as $types_value){
						$types_value = ltrim($types_value , "|");
						$toolset_fieldvalues['types_relationship'] = rtrim($types_value , "|");
					}
					return $toolset_fieldvalues;
				}
	            else{
					global $wpdb;
					$toolset_relation_values = array();
					$toolset_intermadiate_values = array();
					$toolset_fieldvalues = array();
					
					$get_con_slug = "SELECT id FROM {$wpdb->prefix}toolset_connected_elements WHERE element_id ='{$post_id}'";
					$relat_con_slug = $wpdb->get_results($get_con_slug,ARRAY_A);
					$con_id =$relat_con_slug[0]['id'];
				
					$get_slug = "SELECT distinct relationship_id FROM {$wpdb->prefix}toolset_associations WHERE parent_id ='{$con_id}'";
					$relat_slug = $wpdb->get_results($get_slug,ARRAY_A);
					
					
					$get_slug1 = "SELECT distinct relationship_id FROM {$wpdb->prefix}toolset_associations WHERE child_id ='{$con_id}'";
					$relat_slug1 = $wpdb->get_results($get_slug1,ARRAY_A);
					$parent_value2 = '';
				
					$rel_slug = (object) array_merge( (array) $relat_slug, (array) $relat_slug1); 
					
					foreach($rel_slug as $relkey=>$relvalue)
					{
						
						$relationship_id = $relvalue['relationship_id'];
					
						if(!empty($relationship_id)){
							$slug_id="SELECT slug FROM {$wpdb->prefix}toolset_relationships WHERE id IN ($relationship_id) AND origin = 'wizard' ";
							$relationship=$wpdb->get_results($slug_id,ARRAY_A);
							
	
						}
					
						if(is_array($relationship)){
							foreach($relationship as $keys=>$values) {
								$toolset_relation_values['relationship_slug'] .= $values['slug'] . '|';
							}	
						}
						
						$relationships_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}toolset_relationships WHERE id = $relationship_id AND origin = 'wizard' ");
						
						$get_child_slug = "SELECT distinct child_id FROM {$wpdb->prefix}toolset_associations WHERE parent_id ='{$con_id}' and relationship_id ='{$relationships_id}'";
						$relat_child_slug = $wpdb->get_results($get_child_slug,ARRAY_A);
						
						$parent_value1 = '';
					
						if($relat_child_slug){
							foreach($relat_child_slug as $chiildkey => $childvalue){
								$childconid = $childvalue['child_id'];
							
								$get_child_slug1 = "SELECT distinct element_id FROM {$wpdb->prefix}toolset_connected_elements WHERE id ='{$childconid}'";
								$relat_child_slug1 = $wpdb->get_results($get_child_slug1,ARRAY_A);
								$childid = $relat_child_slug1[0]['element_id'];
							
								$parents_post = "SELECT post_title FROM {$wpdb->prefix}posts WHERE ID ={$childid} AND post_status = 'publish'";
								$parent_title1 = $wpdb->get_results($parents_post,ARRAY_A);
							   
								$parent_value1 .= $parent_title1[0]['post_title'] . ",";
			
							}
							$parent_value = rtrim($parent_value1 , ",");
		
						}



                        $get_par_slug = "SELECT distinct parent_id FROM {$wpdb->prefix}toolset_associations WHERE child_id ='{$con_id}' and relationship_id ='{$relationships_id}'";
						$relat_par_slug = $wpdb->get_results($get_par_slug,ARRAY_A);
						$parent_value2 = '';
					
						if($relat_par_slug){
							
							foreach($relat_par_slug as $chiildkey => $childvalue){
								$childconid = $childvalue['parent_id'];
								
								$get_child_slug1 = "SELECT distinct element_id FROM {$wpdb->prefix}toolset_connected_elements WHERE id ='{$childconid}'";
								$relat_child_slug1 = $wpdb->get_results($get_child_slug1,ARRAY_A);
								$childid = $relat_child_slug1[0]['element_id'];
								
								$parents_post = "SELECT post_title FROM {$wpdb->prefix}posts WHERE ID ={$childid} AND post_status = 'publish'";
								$parent_title1 = $wpdb->get_results($parents_post,ARRAY_A);
							
								$parent_value2 .= $parent_title1[0]['post_title'] . ",";
								
			
							}
							$parent_value = rtrim($parent_value2 , ",");
		
						}

						$toolset_intermadiate_values['types_relationship'] .= $parent_value . "|";
	
					}
					if(is_array($toolset_relation_values)){
						foreach($toolset_relation_values as $relation_value){
							$toolset_fieldvalues['relationship_slug'] = rtrim($relation_value , "|");
							
						}
					}	
					foreach($toolset_intermadiate_values as $types_value){
						$types_value = ltrim($types_value , "|");
						$toolset_fieldvalues['types_relationship'] = rtrim($types_value , "|");
					}
				
					return $toolset_fieldvalues;
				
				}

			}

			public function getToolSetIntermediateFieldValue($post_id){
				global $wpdb;
				include_once( 'wp-admin/includes/plugin.php' );
				$plugins = get_plugins();
				$plugin_version = $plugins['types/wpcf.php']['Version'];
				if($plugin_version < '3.4.1'){
					$toolset_fieldvalues = [];
					$intermediate_rel=$wpdb->get_var("select relationship_id from {$wpdb->prefix}toolset_associations where intermediary_id ='{$post_id}'");
					if(!empty($intermediate_rel)){
						$intermediate_slug=$wpdb->get_var("select slug from {$wpdb->prefix}toolset_relationships where  id IN ($intermediate_rel)");
					}
					$intern_rel=$intern_relationship=$rel_intermediate=$related_posts= $related_title='';

					if(!empty($intermediate_slug)){
						$toolset_fieldvalues['relationship_slug'] = $intermediate_slug;
						$intermediate_post = "select parent_id,child_id,post_title from {$wpdb->prefix}toolset_associations INNER JOIN {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID = {$wpdb->prefix}toolset_associations.child_id WHERE {$wpdb->prefix}toolset_associations.intermediary_id='{$post_id}' AND post_status = 'publish'";

						$related_ids = $wpdb->get_results($intermediate_post,ARRAY_A);

						foreach($related_ids as $keyd=>$valued)
						{
							$parent_id = $valued['parent_id'];
							$child_id = $valued['child_id'];
							if(!empty($parent_id)){
								$related_posts = $wpdb->get_var("select post_title from {$wpdb->prefix}posts where ID = $parent_id AND post_status = 'publish'");
							}
							if(!empty($child_id)){
								$related_title = $wpdb->get_var("select post_title from {$wpdb->prefix}posts where ID = $child_id AND post_status = 'publish'");
							}
							$rel_intermediate .= $related_posts.','.$related_title;
							$intern_rel =  $rel_intermediate;
							$intern_relationship=rtrim($intern_rel,"| ");   
							$toolset_fieldvalues['intermediate']= $intern_relationship;
						}
					}
			    }
				else{
					global $wpdb;
					$toolset_fieldvalues = [];
					
					$get_con_slug = "SELECT id FROM {$wpdb->prefix}toolset_connected_elements WHERE element_id ='{$post_id}'";
					$relat_con_slug = $wpdb->get_results($get_con_slug,ARRAY_A);
					$con_id =$relat_con_slug[0]['id'];
					
					if(!empty($con_id)){
						$intermediate_rel=$wpdb->get_var("select relationship_id from {$wpdb->prefix}toolset_associations where intermediary_id ='{$con_id}'");
					}
					
					if(!empty($intermediate_rel)){
						$intermediate_slug=$wpdb->get_var("select slug from {$wpdb->prefix}toolset_relationships where  id IN ($intermediate_rel)");
					}
					$intern_rel=$intern_relationship=$rel_intermediate=$related_posts= $related_title='';

					if(!empty($intermediate_slug)){
						$toolset_fieldvalues['relationship_slug'] = $intermediate_slug;
						$intermediate_post = "select parent_id,child_id from {$wpdb->prefix}toolset_associations where intermediary_id='{$con_id}' and relationship_id = '{$intermediate_rel}'";

						$related_ids = $wpdb->get_results($intermediate_post,ARRAY_A);

						foreach($related_ids as $keyd=>$valued)
						{
							$parent_con_id = $valued['parent_id'];
							$child_con_id = $valued['child_id'];
							$get_par_con = "SELECT element_id FROM {$wpdb->prefix}toolset_connected_elements WHERE id ='{$parent_con_id}'";
							$relat_par_con = $wpdb->get_results($get_par_con,ARRAY_A);
							$parent_id =$relat_par_con[0]['element_id'];
							$get_child_con = "SELECT element_id FROM {$wpdb->prefix}toolset_connected_elements WHERE id ='{$child_con_id}'";
							$relat_child_con = $wpdb->get_results($get_child_con,ARRAY_A);
							$child_id =$relat_child_con[0]['element_id'];
							if(!empty($parent_id)){
								$related_posts = $wpdb->get_var("select post_title from {$wpdb->prefix}posts where ID = $parent_id AND post_status = 'publish'");
							}
							if(!empty($child_id)){
								$related_title = $wpdb->get_var("select post_title from {$wpdb->prefix}posts where ID = $child_id AND post_status = 'publish'");
							}
							$rel_intermediate .= $related_posts.','.$related_title;
							$intern_rel =  $rel_intermediate;
							$intern_relationship=rtrim($intern_rel,"| ");   
							$toolset_fieldvalues['intermediate']= $intern_relationship;
						}
					}
					return $toolset_fieldvalues;
				}
			}

		}

		return new exportExtension();
