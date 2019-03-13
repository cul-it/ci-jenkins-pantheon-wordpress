<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
/**
 * Class SmackUCIHelper
 *
 * Helps you to import the data from your uploaded CSV into your WordPress
 * Also, Can assign the featured images, terms & taxonomies.
 */
class SmackUCIHelper {

	/**
	 * Event information
	 * @var array
	 */
	private $event_information = array();

	/**
	 * SmackUCIHelper constructor.
	 */
	public function __construct() {
		$this->event_information['active_plugins'] = get_option('active_plugins');
	}

	/**
	 * Set event key
	 *
	 * @param $eventKey - Event Key
	 */
	public function setEventKey($eventKey) {
		$this->event_information['event_key'] = $eventKey;
	}

	/**
	 * Set last inserted id
	 *
	 * @param $record_id    - Record Id
	 */
	public function setLastImportId($record_id) {
		$this->event_information['last_import_id'] = $record_id;
	}

	/**
	 * Set template information
	 *
	 * @param $mapping_template - Template Information
	 */
	public function setRowMapping($mapping_template) {
		$this->event_information['csv_row_mapping'] = array();
		if(!empty($mapping_template))
			$this->event_information['csv_row_mapping'] = $mapping_template;
	}

	/**
	 * Set event file information
	 *
	 * @param $file_info    - File Information
	 */
	public function setEventFileInformation($file_info) {
		$this->event_information['import_file_info'] = $file_info;
	}

	/**
	 * Set mapping configuration
	 *
	 * @param $mapping_info     - Mapping Information
	 */
	public function setMappingConfiguration($mapping_info) {
		$this->event_information['mapping_config'] = $mapping_info;
	}

	/**
	 * Set media configuration
	 *
	 * @param $media_info   - Media Configuration
	 */
	public function setMediaConfiguration($media_info) {
		$this->event_information['media_handling'] = $media_info;
	}

	/**
	 * Set import configuration
	 *
	 * @param $import_config    - Import Configuration
	 */
	public function setImportConfiguration($import_config) {
		$this->event_information['import_config'] = $import_config;
	}

	/**
	 * Set import method
	 *
	 * @param $importMethod - Import Method
	 */
	public function setImportMethod($importMethod) {
		$this->event_information['import_method'] = $importMethod;
	}

	/**
	 * Set import type
	 *
	 * @param $importType   - Import Type
	 */
	public function setImportType($importType) {
		$this->event_information['import_type'] = $importType;
	}

	/**
	 * Set import as
	 *
	 * @param $importAs - Import As
	 */
	public function setImportAs($importAs) {
		$this->event_information['import_as'] = $importAs;
	}

	/**
	 * Set total no of processed rows
	 *
	 * @param $processed    - Processed Count
	 */
	public function setProcessedRowCount($processed) {
		$this->event_information['processed'] = $processed;
	}

	/**
	 * Set total no of inserted rows
	 * @param $inserted - Inserted Count
	 */
	public function setInsertedRowCount($inserted) {
		$this->event_information['inserted'] = $inserted;
	}

	/**
	 * Set total no of updated rows
	 *
	 * @param $updated  - Updated Count
	 */
	public function setUpdatedRowCount($updated) {
		$this->event_information['updated'] = $updated;
	}

	/**
	 * Set total no of skipped rows
	 *
	 * @param $skipped  - Skipped Count
	 */
	public function setSkippedRowCount($skipped) {
		$this->event_information['skipped'] = $skipped;
	}

	/**
	 * Set mode of import
	 *
	 * @param $mode - Mode
	 */
	public function setMode($mode) {
		$this->event_information['mode'] = $mode;
	}

	/**
	 * Set detailed log for the current event
	 *
	 * @param $detailed_log - Detailed Log
	 */
	public function setDetailedLog($detailed_log) {
		$this->event_information['detailed_log'] = $detailed_log;
	}

	/**
	 * Set uploaded file type
	 *
	 * @param $fileType - File Type
	 */
	public function setFileType($fileType) {
		$this->event_information['fileType'] = $fileType;
	}

	/**
	 * Set affected records
	 *
	 * @param $id   - Record ID
	 */
	public function setAffectedRecords($id) {
		if(!isset($this->event_information['affected_records']))
			$this->event_information['affected_records'][] = $id;
		if(!in_array($id, $this->event_information['affected_records']))
			$this->event_information['affected_records'][] = $id;
	}

	/**
	 * Set event logs
	 *
	 * @param $key  - Key
	 * @param $log  - Event Log
	 */
	public function setEventLog($key, $log) {
		$this->event_information['detailed_log'][$key] = $log;
	}

	/**
	 * Set event information
	 *
	 * @param $index    - Index
	 * @param $data     - Data
	 */
	public function setEventInformation($index, $data) {
		$this->event_information[$index] = $data;
	}

	/**
	 * Set event object based on the requested module
	 *
	 * @param $module
	 *
	 * @return mixed
	 */
	public function setEventInstance($module) {
		switch ($module) {
			case 'Users':
				#require_once "class-uci-user-data-import.php";
				$helperObj   = new SmackUCIUserDataImport();
				break;
			case 'CustomerReviews':
				#require_once "class-uci-customer-reviews-data-import.php";
				$helperObj = new SmackUCICustomerReviews();
				break;
			case 'Tags':
			case 'Categories':
			case 'Taxonomies':
				#require_once "class-uci-taxonomies-helper.php";
				$helperObj = new SmackUCITaxonomyHelper();
				break;
			case 'Comments':
				#require_once "class-uci-comments-helper.php";
				$helperObj = new SmackUCICommentsHelper();
				break;
			case 'WooCommerce':
			case 'WooCommerceVariations':
			case 'WooCommerceOrders':
			case 'WooCommerceCoupons':
			case 'WooCommerceRefunds':
				#require_once "class-uci-woocommerce-helper.php";
				$helperObj = new SmackUCIWooCommerceHelper();
				break;
			case 'WPeCommerce':
			case 'WPeCommerceCoupons':
				#require_once "class-uci-wpcommerce-helper.php";
				$helperObj = new SmackUCIWPCommerceHelper();
				break;
			case 'MarketPress':
			case 'MarketPressVariations':
				#require_once "class-uci-marketpress-helper.php";
				$helperObj  = new SmackUCIMarketPressHelper();
				break;
			case 'eShop':
				#require_once "class-uci-eshop-helper.php";
				$helperObj = new SmackUCIeShopHelper();
				break;
			default:
				$this->event_information['instance'] = $this;
				return $this;
				break;
		}
		$this->event_information['instance'] = $helperObj;
		return $helperObj;
	}


	public function getEventInstance() {
		if(!empty($this->event_information['instance']))
			return $this->event_information['instance'];

		$this->event_information['instance'] = null;
		return $this->event_information['instance'];
	}

	/**
	 * Get affected records
	 *
	 * @return mixed
	 */
	public function getAffectedRecords() {
		if(!empty($this->event_information['affected_records']))
			return $this->event_information['affected_records'];

		$this->event_information['affected_records'] = array();
		return $this->event_information['affected_records'];
	}

	/**
	 * Get list of active plugins
	 *
	 * @return mixed|void
	 */
	public function getActivePlugins() {
		if(!empty($this->event_information['active_plugins']))
			return $this->event_information['active_plugins'];
		else
			return get_option('active_plugins');
	}

	/**
	 * Get last inserted id
	 *
	 * @return string
	 */
	public function getLastImportId() {
		return isset($this->event_information['last_import_id']) ? $this->event_information['last_import_id'] : '';
	}

	/**
	 * Get event information
	 *
	 * @return array
	 */
	public function getEventInformation() {
		return $this->event_information;
	}

	/**
	 * Get import type
	 *
	 * @return mixed
	 */
	public function getImportType() {
		return $this->event_information['import_type'];
	}

	/**
	 * Get import method
	 *
	 * @return mixed
	 */
	public function getImportMethod() {
		return $this->event_information['import_method'];
	}

	/**
	 * Get import as
	 *
	 * @return mixed
	 */
	public function getImportAs() {
		return $this->event_information['import_as'];
	}

	/**
	 * Event file information
	 *
	 * @param $key  - Group Key
	 *
	 * @return null
	 */
	public function getEventFileInformation($key) {
		if(isset($this->event_information['import_file_info'][$key]))
			return $this->event_information['import_file_info'][$key];

		return null;
	}

	/**
	 * Mode
	 *
	 * @return null
	 */
	public function getMode() {
		if(isset($this->event_information['mode']))
			return $this->event_information['mode'];
		return null;
	}

	/**
	 * File Type
	 *
	 * @return mixed
	 */
	public function getFileType() {
		return $this->event_information['fileType'];
	}

	/**
	 * Mapping Configuration
	 *
	 * @return mixed
	 */
	public function getMappingConfiguration() {
		return $this->event_information['mapping_config'];
	}

	/**
	 * Media Configuration
	 *
	 * @return mixed
	 */
	public function getMediaConfiguration() {
		return $this->event_information['media_handling'];
	}

	/**
	 * Import configuration
	 *
	 * @return mixed
	 */
	public function getImportConfiguration() {
		return $this->event_information['import_config'];
	}

	/**
	 * Get event key
	 *
	 * @return mixed
	 */
	public function getEventKey() {
		return $this->event_information['event_key'];
	}

	/**
	 * Get detailed log information
	 *
	 * @return mixed
	 */
	public function getDetailedLog() {
		return $this->event_information['detailed_log'];
	}

	/**
	 * Get a single row mapping
	 *
	 * @param null $key - Group Key
	 *
	 * @return mixed
	 */
	public function getRowMapping($key = null) {
		if(empty($this->event_information['csv_row_mapping']))
			$this->event_information['csv_row_mapping'] = array();

		if(!empty($this->event_information['csv_row_mapping'][$key]) && $key != null)
			return $this->event_information['csv_row_mapping'][$key];

		return $this->event_information['csv_row_mapping'];
	}

	/**
	 * Get a single CSV row with group based mapping
	 *
	 * @param null $key - Group Key
	 *
	 * @return mixed
	 */
	public function getRowData($key = null) {
		if(empty($this->event_information['csv_row_data']))
			$this->event_information['csv_row_data'] = array();

		if(!empty($this->event_information['csv_row_data'][$key]) && $key != null)
			return $this->event_information['csv_row_data'][$key];

		return $this->event_information['csv_row_data'];
	}

	/**
	 * Total no of processed records
	 *
	 * @return mixed
	 */
	public function getProcessedRowCount() {
		if(!isset($this->event_information['processed']))
			$this->event_information['processed'] = 0;

		return $this->event_information['processed'];

	}

	/**
	 * Total no of inserted records
	 *
	 * @return mixed
	 */
	public function getInsertedRowCount() {
		if(!isset($this->event_information['inserted']))
			$this->event_information['inserted'] = 0;

		return $this->event_information['inserted'];
	}

	/**
	 * Total no of updated records
	 *
	 * @return mixed
	 */
	public function getUpdatedRowCount() {
		if(!isset($this->event_information['updated']))
			$this->event_information['updated'] = 0;

		return $this->event_information['updated'];
	}

	/**
	 * Total no of skipped records
	 *
	 * @return mixed
	 */
	public function getSkippedRowCount() {
		if(!isset($this->event_information['skipped']))
			$this->event_information['skipped'] = 0;

		return $this->event_information['skipped'];
	}

	/**
	 * Current processing node
	 *
	 * @var
	 */
	public $processing_row_id;

	/**
	 * Detailed Log
	 *
	 * @var array
	 */
	public $detailed_log = array();

	/**
	 * Billing & Shipping fields for MarketPress store
	 *
	 * @var array
	 */
	public $def_mpCols = array(
		'Shipping Email' => 'msi_email',
		'Shipping Name' => 'msi_name',
		'Shipping Address1' => 'msi_address1',
		'Shipping Address2' => 'msi_address2',
		'Shipping City' => 'msi_city',
		'Shipping State' => 'msi_state',
		'Shipping Zip' => 'msi_zip',
		'Shipping Country' => 'msi_country',
		'Shipping Phone' => 'msi_phone',
		'Billing Email' => 'mbi_email',
		'Billing Name' => 'mbi_name',
		'Billing Address1' => 'mbi_address1',
		'Billing Address2' => 'mbi_address2',
		'Billing City' => 'mbi_city',
		'Billing State' => 'mbi_state',
		'Billing Zip' => 'mbi_zip',
		'Billing Country' => 'mbi_country',
		'Billing Phone' => 'mbi_phone'
	);

	/**
	 * Billing & Shipping fields for WooCommerce store
	 *
	 * @var array
	 */
	public $def_wcCols = array(
		'Billing First Name' => 'billing_first_name',
		'Billing Last Name' => 'billing_last_name',
		'Billing Company' => 'billing_company',
		'Billing Address1' => 'billing_address_1',
		'Billing Address2' => 'billing_address_2',
		'Billing City' => 'billing_city',
		'Billing PostCode' => 'billing_postcode',
		'Billing State' => 'billing_state',
		'Billing Country' => 'billing_country',
		'Billing Phone' => 'billing_phone',
		'Billing Email' => 'billing_email',
		'Shipping First Name' => 'shipping_first_name',
		'Shipping Last Name' => 'shipping_last_name',
		'Shipping Company' => 'shipping_company',
		'Shipping Address1' => 'shipping_address_1',
		'Shipping Address2' => 'shipping_address_2',
		'Shipping City' => 'shipping_city',
		'Shipping PostCode' => 'shipping_postcode',
		'Shipping State' => 'shipping_state',
		'Shipping Country' => 'shipping_country',
		'API Consumer Key' => 'woocommerce_api_consumer_key',
		'API Consumer Secret' => 'woocommerce_api_consumer_secret',
		'API Key Permissions' => 'woocommerce_api_key_permissions',
		'Shipping Region' => '_wpsc_shipping_region' ,
		'Billing Region' => '_wpsc_billing_region',
		'Cart' => '_wpsc_cart'
	);

	/**
	 * Get list of active plugin
	 *
	 * @return mixed|void
	 */
	public function get_active_plugins() {
		$active_plugins = get_option('active_plugins');
		return $active_plugins;
	}

	/**
	 * Overwriting the global values
	 *
	 * @param $data
	 */
	public function overwrite_globals($data) {
		global $smack_uci_globals;
		$GLOBALS['smack_uci_globals'] = $data;
	}

	/**
	 * Get core fields based on the module
	 *
	 * @param $type - Type
	 *
	 * @return array
	 */
	public function coreFields($type) {
		$defCols = $coreFields = array();
		// Core fields for Posts / Pages / CustomPosts / WooCommerce / MarketPress / WPeCommerce / eShop
		$check_custpost = array('Posts' => 'post', 'Pages' => 'page', 'Users' => 'users', 'Comments' => 'comments', 'CustomerReviews' =>'wpcr3_review', 'Categories' => 'categories', 'Tags' => 'tags', 'eShop' => 'post', 'WooCommerce' => 'product', 'WPeCommerce' => 'wpsc-product','WPeCommerceCoupons' => 'wpsc-product', 'MarketPress' => 'product', 'MarketPressVariations' => 'mp_product_variation','WooCommerceVariations' => 'product', 'WooCommerceOrders' => 'product', 'WooCommerceCoupons' => 'product', 'WooCommerceRefunds' => 'product', 'CustomPosts' => 'CustomPosts');
	    
		$taxonomies = get_taxonomies();
		if (in_array($type, $taxonomies)) {
			if($type == 'category' || $type == 'product_category' || $type == 'product_cat' || $type == 'wpsc_product_category' || $type == 'event-categories'):
				$type = 'Categories';
			elseif($type == 'product_tag' || $type == 'event-tags' || $type == 'post_tag'):
				$type = 'Tags';
			elseif($type == 'comments'):
				$type = 'Comments';
			else:
				$type = 'Taxonomies';
			endif;
		}
		if ($type != 'Users' && $type != 'Taxonomies' && $type != 'CustomerReviews' && $type != 'Categories' && $type != 'Comments' && $type != 'MarketPressVariations') {
			$defCols = array(
				'Title' => 'post_title',
				'ID' => 'ID',
				'Content' => 'post_content',
				'Short Description' => 'post_excerpt',
				'Publish Date' => 'post_date',
				'Slug' => 'post_name',
				'Author' => 'post_author',
				'Status' => 'post_status',
				'Featured Image' => 'featured_image',
				'Nexgen Gallery' => 'nextgen-gallery',
			);
			if ($type === 'Posts' || $type === 'CustomPosts') {
				$defCols['Format'] = 'post_format';
				$defCols['Comment Status'] = 'comment_status';
				$defCols['Ping Status'] = 'ping_status';
			}
			if ($type === 'Pages') {
				$defCols['Parent'] = 'post_parent';
				$defCols['Order'] = 'menu_order';
				$defCols['Page Template'] = 'wp_page_template';
				$defCols['Comment Status'] = 'comment_status';
				$defCols['Ping Status'] = 'ping_status';
			}
			if (in_array('sitepress-multilingual-cms/sitepress.php', $this->get_active_plugins())) {
				$defCols['Language Code'] = 'language_code';
				$defCols['Translated Post Title'] = 'translated_post_title';
			}
			if (!array_key_exists($type, $check_custpost)) {
	    	$defCols['Parent'] = 'post_parent';
	   		}
	   	}
		// Core fields for Users
		if($type === 'Users') {
			$defCols = array(
				'User Login' => 'user_login',
				'User Pass' => 'user_pass',
				'First Name' => 'first_name',
				'Last Name' => 'last_name',
				'Nick Name' => 'nickname',
				'User Email' => 'user_email',
				'User URL' => 'user_url',
				'User Nicename' => 'user_nicename',
				'User Registered' => 'user_registered',
				'User Activation Key' => 'user_activation_key',
				'User Status' => 'user_status',
				'Display Name' => 'display_name',
				'User Role' => 'role',
				'Biographical Info' => 'biographical_info',
				'Disable Visual Editor' => 'disable_visual_editor',
				'Admin Color Scheme' => 'admin_color',
				'Enable Keyboard Shortcuts' => 'enable_keyboard_shortcuts',
				'Show Toolbar' => 'show_toolbar',
'ID' => 'ID'
			);
		}
		if(in_array('events-manager/events-manager.php', $this->get_active_plugins()) && $type === 'event' || $type === 'event-recurring' || $type === 'location' ){
			$customarray = array(
				'Event_start_date' => 'event_start_date',
				'Event_end_date' => 'event_end_date',
				'Event_start_time' => 'event_start_time',
				'Event_end_time' => 'event_end_time',
				'Event_all_day' => 'event_all_day',
				'Event_rsvp_date' => 'event_rsvp_date',
				'Event_rsvp_time' => 'event_rsvp_time',
				'Event_rsvp_spaces' => 'event_rsvp_spaces',
				'Event_spaces' => 'event_spaces',
				'Recurrence_interval' => 'recurrence_interval',
				'Recurrence_freq' => 'recurrence_freq',
				'Recurrence_byday' => 'recurrence_byday',
				'Recurrence_byweekno'=>'recurrence_byweekno',
				'Recurrence_days' => 'recurrence_days',
				'Recurrence_rsvp_days' => 'recurrence_rsvp_days',
				'Location_name' => 'location_name',
				'Location_address' => 'location_address',
				'Location_town' =>'location_town',
				'Location_state' => 'location_state',
				'Location_postcode' => 'location_postcode',
				'Location_region' => 'location_region',
				'Location_country' => 'location_country',
				'Ticket_name' => 'ticket_name',
				'Ticket_description' => 'ticket_description',
				'Ticket_price' => 'ticket_price',
				'Ticket_start_date' => 'ticket_start_date',
				'Ticket_end_date' => 'ticket_end_date',
				'Ticket_start_time' => 'ticket_start_time',
				'Ticket_end_time' => 'ticket_end_time',
				'Ticket_min' => 'ticket_min',
				'Ticket_max' => 'ticket_max',
				'Ticket_spaces' => 'ticket_spaces',
				'Ticket_members' => 'ticket_members',
				'Ticket_members_roles' =>'ticket_members_roles',
				'Ticket_guests' => 'ticket_guests',
				'Ticket_required' => 'ticket_required',
			);
			foreach($customarray as $key => $value){
				$defCols[$key] = $value;
			}
		}

		//Tickets Fields
		if(in_array('events-manager/events-manager.php', $this->get_active_plugins()) && $type === 'ticket'){
			$customarray = array(
				'Ticket_name' => 'ticket_name',
				'Ticket_description' => 'ticket_description',
				'Ticket_price' => 'ticket_price',
				'Ticket_start_date' => 'ticket_start_date',
				'Ticket_end_date' => 'ticket_end_date',
				'Ticket_start_time' => 'ticket_start_time',
				'Ticket_end_time' => 'ticket_end_time',
				'Ticket_min' => 'ticket_min',
				'Ticket_max' => 'ticket_max',
				'Ticket_spaces' => 'ticket_spaces',
				'Ticket_members' => 'ticket_members',
				'Ticket_members_roles' =>'ticket_members_roles',
				'Ticket_guests' => 'ticket_guests',
				'Ticket_required' => 'ticket_required',
			);
			foreach($customarray as $key => $value){
				$defCols[$key] = $value;
			}
		}

		// Core fields for Taxonomies
		if($type === 'Taxonomies') {
			$defCols = array(
				'Taxonomy Name' => 'name',
				'Taxonomy Slug' => 'slug',
				'Taxonomy Description' => 'description',
				/* 'SEO Title' => 'wpseo_title',
				'SEO Description' => 'wpseo_desc',
				'Canonical' => 'wpseo_canonical',
				'Noindex this category' => 'wpseo_noindex',
				'Include in sitemap?' => 'wpseo_sitemap_include', */
				'Term ID' => 'TERMID',
			);
			if (in_array('sitepress-multilingual-cms/sitepress.php', $this->get_active_plugins())) {
				$defCols['Language Code'] = 'language_code';
				$defCols['Translated Post Title'] = 'translated_post_title';
			}
		}
		if($type === 'Categories') {
			$defCols = array(
				'Category Name' => 'name',
				'Category Slug' => 'slug',
				'Category Description' => 'description',
				/* 'SEO Title' => 'wpseo_title',
				'SEO Description' => 'wpseo_desc',
				'Canonical' => 'wpseo_canonical',
				'Noindex this category' => 'wpseo_noindex',
				'Include in sitemap?' => 'wpseo_sitemap_include', */
				'Term ID' => 'TERMID',
			);
			if (in_array('sitepress-multilingual-cms/sitepress.php', $this->get_active_plugins())) {
				$defCols['Language Code'] = 'language_code';
				$defCols['Translated Post Title'] = 'translated_post_title';
			}
		}
		if($type === 'Tags') {
			$defCols = array(
				'Tag Name' => 'name',
				'Tag Slug' => 'slug',
				'Tag Description' => 'description',
				/* 'SEO Title' => 'wpseo_title',
				'SEO Description' => 'wpseo_desc',
				'Canonical' => 'wpseo_canonical',
				'Noindex this category' => 'wpseo_noindex',
				'Include in sitemap?' => 'wpseo_sitemap_include', */
				'Term ID' => 'TERMID',
			);
			if (in_array('sitepress-multilingual-cms/sitepress.php', $this->get_active_plugins())) {
				$defCols['Language Code'] = 'language_code';
				$defCols['Translated Post Title'] = 'translated_post_title';
			}
		}
		// Core fields for CustomerReviews
		if($type === 'CustomerReviews') {
			if(in_array('wp-customer-reviews/wp-customer-reviews-3.php', $this->get_active_plugins()) || in_array('wp-customer-reviews/wp-customer-reviews.php', $this->get_active_plugins())) {
				$defCols = array(
					'Review Date Time' => 'date_time',
					'Reviewer Name' => 'reviewer_name',
					'Reviewer Email' => 'reviewer_email',
					'Reviewer IP' => 'reviewer_ip',
					'Review Format' => 'review_format',
					'Review Title' => 'review_title',
					'Review Text' => 'review_text',
					'Review Response' => 'review_response',
					'Review Status' => 'status',
					'Review Rating' => 'review_rating',
					'Review URL' => 'reviewer_url',
					'Review to Post/Page Id' => 'page_id',
					'Custom Field #1' => 'custom_field1',
					'Custom Field #2' => 'custom_field2',
					'Custom Field #3' => 'custom_field3',
					'Review ID' => 'review_id',
				);
			}
		}
		if($type === 'Comments') {
			$defCols = array(
				'Comment Post Id' => 'comment_post_ID',
				'Comment Author' => 'comment_author',
				'Comment Author Email' => 'comment_author_email',
				'Comment Author URL' => 'comment_author_url',
				'Comment Content' => 'comment_content',
				'Comment Author IP' => 'comment_author_IP',
				'Comment Date' => 'comment_date',
				'Comment Approved' => 'comment_approved',
			);
		}
		if($type === 'WooCommerceVariations'){
			$defCols = array(
				'Product Id' => 'PRODUCTID',
				'Parent Sku' => 'PARENTSKU',
				'Variation Sku' => 'VARIATIONSKU',
				'Variation ID' => 'VARIATIONID',
				'Featured Image' => 'featured_image',
			);
		}
		if($type === 'WooCommerceCoupons'){
			$defCols = array(
				'Coupon Code' => 'coupon_code',
				'Description' => 'description',
				'Date' => 'coupon_date',
				'Status' => 'coupon_status',
				'Coupon Id' =>'COUPONID');
		}
		if($type === 'WooCommerceOrders'){
			$defCols = array(
				'Customer Note' => 'customer_note',
				'Order Status' => 'order_status',
				'Order Date' => 'order_date',
				'Order Id' => 'ORDERID');
		}
		if($type === 'WooCommerceRefunds'){
			$defCols = array(
				'Post Parent' => 'post_parent',
				'Post Excerpt' => 'post_excerpt',
				'Refund Id' => 'REFUNDID');
		}

		if($type == 'WPeCommerceCoupons') {
			$defCols = array(
				'Coupon Code' => 'coupon_code',
				'Coupon Id' => 'COUPONID',
				'Description' => 'description',
				'Status' => 'coupon_status',
				'Discount' => 'discount',
				'Discount Type' => 'discount_type',
				'Start' => 'start',
				'Expiry' => 'expiry',
				'Use Once' => 'use_once',
				'Apply On All Products' => 'apply_on_all_products',
				'Conditions' => 'conditions'
			);
		}

		if($type === 'MarketPressVariations'){
			$defCols['Product Id'] = 'PRODUCTID';
			$defCols['Variation ID'] = 'VARIATIONID';
		}
		if($type === 'MarketPress' || $type == 'WooCommerce' || $type == 'WPeCommerce' || $type == 'eShop'){
            //Commented for removing the sku field in core fields mapping section
			//$defCols['PRODUCT SKU'] = 'PRODUCTSKU';
		}
		foreach ($defCols as $key => $val) {
			$coreFields['CORE'][$key]['label'] = $key;
			$coreFields['CORE'][$key]['name'] = $val;
		}
		// Unset the Product Author for MarketPress / WooCommerce
		if($type == 'MarketPress' || $type == 'WooCommerceRefunds' || $type == 'WooCommerceOrders' || $type == 'WooCommerceCoupons' || $type == 'WooCommerceVariations' || $type == 'WooCommerce') {
			unset($coreFields['CORE']['Author']);
		}
		return $coreFields;
	}

	/**
	 * Get module name based on the module (or) post type (or) taxonomy type
	 *
	 * @param $import_type      - Import Type
	 * @param null $importAs    - Import As
	 *
	 * @return mixed
	 */
	public function import_post_types($import_type, $importAs = null) {
		$module = array('Posts' => 'post', 'Pages' => 'page', 'Users' => 'users', 'Comments' => 'comments', 'Taxonomies' => $importAs, 'CustomerReviews' =>'wpcr3_review', 'Categories' => 'categories', 'Tags' => 'tags', 'eShop' => 'post', 'WooCommerce' => 'product', 'WPeCommerce' => 'wpsc-product','WPeCommerceCoupons' => 'wpsc-product', 'MarketPress' => 'product', 'MarketPressVariations' => 'mp_product_variation','WooCommerceVariations' => 'product', 'WooCommerceOrders' => 'product', 'WooCommerceCoupons' => 'product', 'WooCommerceRefunds' => 'product', 'CustomPosts' => $importAs);
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

	/**
	 * Available post types
	 *
	 * @return array
	 */
	public function get_import_post_types(){
		$custom_array = array('post', 'page', 'product', 'wpsc-product', 'product_variation', 'shop_order', 'shop_coupon', 'shop_order_refund','mp_product_variation');
		$other_posttypes = array('attachment','revision','nav_menu_item','wpsc-product-file','mp_order','shop_webhook');
		$importas = array(
			'Posts' => 'Posts',
			'Pages' => 'Pages',
			'Users' =>'Users',
			'Comments' => 'Comments'
		);
		$all_post_types = get_post_types();
		foreach($other_posttypes as $ptkey => $ptvalue) {
			if (in_array($ptvalue, $all_post_types)) {
				unset($all_post_types[$ptvalue]);
			}
		}
		foreach($all_post_types as $key => $value) {
			if(!in_array($value, $custom_array)) {
				if($value == 'event') {
					$importas['Events'] = $value;
				} elseif($value == 'event-recurring') {
					$importas['Recurring Events'] = $value;
				} elseif($value == 'location') {
					$importas['Event Locations'] = $value;
				} else {
					$importas[$value] = $value;
				}
				$custompost[$value] = $value;
			}
		}
		//Ticket import
		if(in_array('events-manager/events-manager.php', $this->get_active_plugins()) ) {
			$importas['Tickets'] = 'ticket';
		}

		if(in_array('wp-customer-reviews/wp-customer-reviews-3.php', $this->get_active_plugins()) ||  in_array('wp-customer-reviews/wp-customer-reviews.php', $this->get_active_plugins())) {
			$importas['Customer Reviews'] = 'CustomerReviews';
			if(isset($importas['wpcr3_review'])) {
				unset($importas['wpcr3_review']);
			}
		}
		if(in_array('wordpress-ecommerce/marketpress.php', $this->get_active_plugins()) || in_array('marketpress/marketpress.php', $this->get_active_plugins())) {
			$importas['MarketPress Product'] = 'MarketPress';
		}
		if(in_array('marketpress/marketpress.php', $this->get_active_plugins())) {
			$importas['MarketPress Product Variations'] = 'MarketPressVariations';
		}

		if(in_array('eshop/eshop.php',$this->get_active_plugins())) {
			$importas['eShop Products'] = 'eShop';
		}
		if(in_array('woocommerce/woocommerce.php', $this->get_active_plugins())){
			$importas['WooCommerce Product'] ='WooCommerce';
			$importas['WooCommerce Product Variations'] ='WooCommerceVariations';
			$importas['WooCommerce Orders'] = 'WooCommerceOrders';
			$importas['WooCommerce Coupons'] = 'WooCommerceCoupons';
			$importas['WooCommerce Refunds'] = 'WooCommerceRefunds';

		}
		if(in_array('wp-e-commerce/wp-shopping-cart.php', $this->get_active_plugins())){
			$importas['WPeCommerce Products'] ='WPeCommerce';
			$importas['WPeCommerce Coupons'] = 'WPeCommerceCoupons';
		}
		return $importas;
	}

	/**
	 * Available custom post types
	 *
	 * @return array
	 */
	public function get_import_custom_post_types(){
		$custompost = array();
		$custom_array = array('post', 'page', 'product', 'wpsc-product', 'product_variation', 'shop_order', 'shop_coupon', 'shop_order_refund','mp_product_variation');
		$other_posttypes = array('attachment','revision','nav_menu_item','wpsc-product-file','mp_order','shop_webhook');
		$all_post_types = get_post_types();
		foreach($other_posttypes as $ptkey => $ptvalue) {
			if (in_array($ptvalue, $all_post_types)) {
				unset($all_post_types[$ptvalue]);
			}
		}
		foreach($all_post_types as $key => $value){
			if(!in_array($value,$custom_array)){
				$custompost[$value] = $value;
			}
		}
		return $custompost;
	}

	/**
	 * WordPress default custom fields
	 *
	 * @param $import_type  - Import Type
	 * @param $importAs     - Import As
	 * @param null $mode    - Mode
	 *
	 * @return array
	 */
	public function WPCustomFields($import_type, $importAs, $mode = null) {
		global $wpdb;
		if($mode == 'export') :
			$import_type = $importAs;
		endif;
		$module = $this->import_post_types($import_type);
		$commonMetaFields = array();
		if($module != 'users') {
			$keys = $wpdb->get_col( "SELECT pm.meta_key FROM $wpdb->posts p
									JOIN $wpdb->postmeta pm
									ON p.ID = pm.post_id
									WHERE p.post_type = '{$module}'
									GROUP BY meta_key
									HAVING meta_key NOT LIKE '\_%' and meta_key NOT LIKE 'field_%' and meta_key NOT LIKE 'wpcf-%'
									ORDER BY meta_key" );
		} else {
			$keys = $wpdb->get_col( "SELECT um.meta_key FROM $wpdb->users u
									JOIN $wpdb->usermeta um
									ON u.ID = um.user_id
									GROUP BY meta_key
									HAVING meta_key NOT LIKE '\_%' and meta_key NOT LIKE 'field_%' and meta_key NOT LIKE 'wpcf-%'
									ORDER BY meta_key" );
		}
		foreach ($keys as $val) {
			$commonMetaFields['CORECUSTFIELDS'][$val]['label'] = $val;
			$commonMetaFields['CORECUSTFIELDS'][$val]['name'] = $val;
		}
		return $commonMetaFields;
	}

	/**
	 * Active eCommerce meta fields
	 * @param $module   - Module
	 *
	 * @return array
	 */
	public function ecommerceMetaFields($module) {
		$ecommerceMetaFields = array();
		$MetaFields = array();
		if($module === 'eShop') {
			$MetaFields = array(
				'SKU' => 'sku',
				'Product Options' => 'products_option',
				'Sale Price' => 'sale_price',
				'Regular Price' => 'regular_price',
				'Description' => 'description',
				'Shipping Rate' => 'shiprate',
				'Featured Product' => 'featured_product',
				'Product in sale' => 'product_in_sale',
				'Stock Available' => 'stock_available',
				'Show Options as' => 'cart_option'
			);
		}
		if($module === 'WPeCommerce') {
			$MetaFields = array(
				'Stock' => 'stock',
				'Price' => 'price',
				'Sale Price' => 'sale_price',
				'SKU' => 'sku',
				'Notify Stock Runs Out' => 'notify_when_none_left',
				'UnPublish If Stock Runs' => 'unpublish_when_none_left',
				'Taxable Amount' => 'taxable_amount',
				'Is Taxable' => 'is_taxable',
				'Download File' => 'download_file',
				'External Link' => 'external_link',
				'External Link Text' => 'external_link_text',
				'External Link Target' => 'external_link_target',
				'Can Have Uploaded Image' => 'can_have_uploaded_image',
				'Engraved' => 'engraved',
				'No Shipping' => 'no_shipping',
				'Weight' => 'weight',
				'Weight Unit' => 'weight_unit',
				'Height' => 'height',
				'Height Unit' => 'height_unit',
				'Width' => 'width',
				'Width Unit' => 'width_unit',
				'Length' => 'length',
				'Length Unit'  => 'length_unit',
				'Dimension Unit' => 'dimension_unit',
				'Shipping' => 'shipping',
				'Custom Name' => 'custom_name',
				'Custom Description' => 'custom_desc',
				'Custom Meta' => 'custom_meta',
				'Merchant Notes' => 'merchant_notes',
				'Enable Comments' => 'enable_comments',
				'Quantity Limited' => 'quantity_limited',
				'Special' => 'special',
				'Display Weight As' => 'display_weight_as',
				'State' => 'state',
				'Quantity' => 'quantity',
				'Table Price' => 'table_price',
				'Alternative Currencies and Price' => 'alternative_currencies_and_price',
				'Google Prohibited' => 'google_prohibited',
				'Discussion' => 'discussion',
				'Comments' => 'comments',
				'Attributes' => 'attributes',
				'Taxes' => 'taxes',
				'Image Gallery' => 'image_gallery',
				'Short Description' => 'short_description',
				'Meta Data' => 'meta_data',
				'Variations' => 'variations'
			);
		}
		if($module === 'WPeCommerceCoupons') {
			$MetaFields = array(
				/*	'Discount' => 'discount',
					'Discount Type' => 'discount_type',
					'Start' => 'start',
					'End' => 'end',
					'Active' => 'active',
					'Use Once' => 'use_once',
					'Apply On All Products' => 'apply_on_all_products',
					'Conditions' => 'conditions'*/
			);
		}
		if($module === 'WooCommerce') {
			$MetaFields = array(
				'Product Shipping Class' => 'product_shipping_class',
				'Visibility' => 'visibility',
				'Tax Status' => 'tax_status',
				'Product Type' => 'product_type',
				'Product Attribute Name' => 'product_attribute_name',
				'Product Attribute Value' => 'product_attribute_value',
				'Product Attribute Visible' => 'product_attribute_visible',
				'Product Attribute Variation' => 'product_attribute_variation',
				'Product Attribute Position' => 'product_attribute_position',
				'Featured Product' => 'featured_product',
				'Product Attribute Taxonomy' => 'product_attribute_taxonomy',
				'Tax Class' => 'tax_class',
				'File Paths' => 'file_paths',
				'Edit Last' => 'edit_last',
				'Edit Lock' => 'edit_lock',
				'Thumbnail Id' => 'thumbnail_id',
				'Manage Stock' => 'manage_stock',
				'Stock' => 'stock',
				'Stock Status' => 'stock_status',
				'Stock Quantity' => 'stock_qty',
				'Total Sales' => 'total_sales',
				'Downloadable' => 'downloadable',
				'Virtual' => 'virtual',
				'Regular Price' => 'regular_price',
				'Sale Price' => 'sale_price',
				'Purchase Note' => 'purchase_note',
				'Menu Order' => 'menu_order',
				'Enable Reviews' => 'comment_status',
				'Weight' => 'weight',
				'Length' => 'length',
				'Width' => 'width',
				'Height' => 'height',
				'SKU' => 'sku',
				'UpSells ID' => 'upsell_ids',
				'CrossSells ID' => 'crosssell_ids',
				'Grouping ID' => 'grouping_product',
				'Sales Price Date From' => 'sale_price_dates_from',
				'Sales Price Date To' => 'sale_price_dates_to',
				'Price' => 'price',
				'Sold Individually' => 'sold_individually',
				'Backorders' => 'backorders',
				'Product Image Gallery' => 'product_image_gallery',
				'Product URL' => 'product_url',
				'Button Text' => 'button_text',
				'Featured' => 'featured',
				'Downloadable Files' => 'downloadable_files',
				'Download Limit' => 'download_limit',
				'Download Expiry' => 'download_expiry',
				'Download Type' => 'download_type',
				'Default Attributes' => 'default_attributes',
				'Custom Attributes' => 'custom_attributes',
				'_subscription_period' => '_subscription_period',
				'_subscription_period_interval' => '_subscription_period_interval',
				'_subscription_length' => '_subscription_length',
				'_subscription_trial_period' => '_subscription_trial_period',
				'_subscription_trial_length' => '_subscription_trial_length',
				'_subscription_price' => '_subscription_price',
				'_subscription_sign_up_fee' => '_subscription_sign_up_fee',
			);
		}
		if($module === 'WooCommerceVariations'){
			$MetaFields = array(
				'Product Attribute Name' => 'product_attribute_name',
				'Product Attribute Value' => 'product_attribute_value',
				'Product Attribute Visible' => 'product_attribute_visible',
				'Product Attribute Variation' => 'product_attribute_variation',
				'Product Attribute Position' => 'product_attribute_position',
				'Featured' => 'featured',
				'Downloadable Files' => 'downloadable_files',
				'Download Limit' => 'download_limit',
				'Download Expiry' => 'download_expiry',
				'Price' => 'price',
				'Sales Price Date From' => 'sale_price_dates_from',
				'Sales Price Date To' => 'sale_price_dates_to',
				'Regular Price' => 'regular_price',
				'Sale Price' => 'sale_price',
				'Purchase Note' => 'purchase_note',
				'Default Attributes' => 'default_attributes',
				'Custom Attributes' => 'custom_attributes',
				'Weight' => 'weight',
				'Length' => 'length',
				'Width' => 'width',
				'Height' => 'height',
				'Downloadable' => 'downloadable',
				'Virtual' => 'virtual',
				'Stock' => 'stock',
				'Stock Status' => 'stock_status',
				'Stock Quantity' => 'stock_qty',
				'Sold Individually' => 'sold_individually',
				'Manage Stock' => 'manage_stock',
				'Backorders' => 'backorders',
				'SKU' => 'sku',
				'Thumbnail Id' => 'thumbnail_id',
				'_subscription_period' => '_subscription_period',
				'_subscription_period_interval' => '_subscription_period_interval',
				'_subscription_length' => '_subscription_length',
				'_subscription_trial_period' => '_subscription_trial_period',
				'_subscription_trial_length' => '_subscription_trial_length',
				'_subscription_price' => '_subscription_price',
				'_subscription_sign_up_fee' => '_subscription_sign_up_fee',
			);
		}
		if($module === 'WooCommerceOrders') {
			$MetaFields = array(
				'Recorded Sales'          => 'recorded_sales',
				'Payment Method Title'    => 'payment_method_title',
				'Payment Method'          => 'payment_method',
				'Transaction Id'          => 'transaction_id',
				'Billing First Name'      => 'billing_first_name',
				'Billing Last Name'       => 'billing_last_name',
				'Billing Company'         => 'billing_company',
				'Billing Address1'        => 'billing_address_1',
				'Billing Address2'        => 'billing_address_2',
				'Billing City'            => 'billing_city',
				'Billing PostCode'        => 'billing_postcode',
				'Billing State'           => 'billing_state',
				'Billing Country'         => 'billing_country',
				'Billing Phone'           => 'billing_phone',
				'Billing Email'           => 'billing_email',
				'Shipping First Name'     => 'shipping_first_name',
				'Shipping Last Name'      => 'shipping_last_name',
				'Shipping Company'        => 'shipping_company',
				'Shipping Address1'       => 'shipping_address_1',
				'Shipping Address2'       => 'shipping_address_2',
				'Shipping City'           => 'shipping_city',
				'Shipping PostCode'       => 'shipping_postcode',
				'Shipping State'          => 'shipping_state',
				'Shipping Country'        => 'shipping_country',
				'Customer User'           => 'customer_user',
				'Order Key'               => 'order_key',
				'Order Currency'          => 'order_currency',
				'Order Shipping Tax'      => 'order_shipping_tax',
				'Order Tax'               => 'order_tax',
				'Order Total'             => 'order_total',
				'Cart Discount Tax'       => 'cart_discount_tax',
				'Cart Discount'           => 'cart_discount',
				'Order Shipping'          => 'order_shipping',
				'ITEM: name'              => 'item_name',
				'ITEM: type'              => 'item_type',
				'ITEM: variation_id'      => 'item_variation_id',
				'ITEM: product_id'        => 'item_product_id',
				'ITEM: line_subtotal'     => 'item_line_subtotal',
				'ITEM: line_subtotal_tax' => 'item_line_subtotal_tax',
				'ITEM: line_total'        => 'item_line_total',
				'ITEM: line_tax'          => 'item_line_tax',
				'ITEM: line_tax_data'     => 'item_line_tax_data',
				'ITEM: tax_class'         => 'item_tax_class',
				'ITEM: qty'               => 'item_qty',
				'FEE: name'               => 'fee_name',
				'FEE: type'               => 'fee_type',
				'FEE: tax_class'          => 'fee_tax_class',
				'FEE: line_total'         => 'fee_line_total',
				'FEE: line_tax'           => 'fee_line_tax',
				'FEE: line_tax_data'      => 'fee_line_tax_data',
				'FEE: line_subtotal'      => 'fee_line_subtotal',
				'FEE: line_subtotal_tax'  => 'fee_line_subtotal_tax',
				'SHIPMENT: name'          => 'shipment_name',
				'SHIPMENT: method_id'     => 'shipment_method_id',
				'SHIPMENT: cost'          => 'shipment_cost',
				'SHIPMENT: taxes'         => 'shipment_taxes',
			);
		}
		if($module === 'WooCommerceCoupons') {
			$MetaFields = array(
				'Discount Type' => 'discount_type',
				'Coupon Amount' => 'coupon_amount',
				'Individual Use' => 'individual_use',
				'Product Ids' => 'product_ids',
				'Exclude Product Ids' => 'exclude_product_ids',
				'Usage Limit' => 'usage_limit',
				'Usage Limit Per User' => 'usage_limit_per_user',
				'Limit Usage' => 'limit_usage_to_x_items',
				'Expiry Date' => 'expiry_date',
				'Free Shipping' => 'free_shipping',
				'Exclude Sale Items' => 'exclude_sale_items',
				'Product Categories' => 'product_categories',
				'Exclude Product Categories' => 'exclude_product_categories',
				'Minimum Amount' => 'minimum_amount',
				'Maximum Amount' => 'maximum_amount',
				'Customer Email' => 'customer_email'
			);
		}
		if($module === 'WooCommerceRefunds') {
			$MetaFields = array(
				'Recorded Sales' => 'recorded_sales',
				'Refund Amount' => 'refund_amount',
				'Order Shipping Tax' => 'order_shipping_tax',
				'Order Tax' => 'order_tax',
				'Order Shipping' => 'order_shipping',
				'Cart Discount' => 'cart_discount',
				'Cart Discount Tax' => 'cart_discount_tax',
				'Order Total' => 'order_total',
				'Customer User' =>'customer_user'
			);
		}
		if($module === 'MarketPress') {
			if(in_array('wordpress-ecommerce/marketpress.php', $this->get_active_plugins())){
				$MetaFields = array(
					'Variation' => 'variation',
					'SKU' => 'sku',
					'Regular Price' => 'regular_price',
					'Is Sale' => 'is_sale',
					'Sale Price' => 'sale_price',
					'Track Inventory' => 'track_inventory',
					'Inventory' => 'inventory',
					'Track Limit' => 'track_limit',
					'Limit Per Order' => 'limit_per_order',
					'Product Link' => 'product_link',
					'Is Special Tax' => 'is_special_tax',
					'Special Tax' => 'special_tax',
					'Sales Count' => 'sales_count',
					'Extra Shipping Cost' => 'extra_shipping_cost',
					'File URL' => 'file_url',
				);
			}
			if(in_array('marketpress/marketpress.php', $this->get_active_plugins())){
				$MetaFields = array(
					'Product Type' => 'product_type',
					'SKU' => 'sku',
					'Regular Price' => 'regular_price',
					'Variation Name' => 'variation_name',
					'Variation Value' => 'variation_value',
					'Variation Image' => 'variation_image',
					'Thumbnail Id' => 'thumbnail_id',
					'Sale Price' => 'sale_price',
					'Per Order Limit' => 'per_order_limit',
					'Has Sale' => 'has_sale',
					'Sale Price Start Date' => 'sale_price_start_date',
					'Sale Price End Date' => 'sale_price_end_date',
					'File URL' => 'file_url',
					'External URL' => 'external_url',
					'Is Featured' => 'is_featured',
					'Special Tax Rate' => 'special_tax_rate',
					'Charge Tax' => 'charge_tax',
					'Charge Shipping' => 'charge_shipping',
					'Weight Pounds' => 'weight_pounds',
					'Weight Ounces' => 'weight_ounces',
					'Weight Extra Shipping Cost' => 'weight_extra_shipping_cost',
					'Inventory Tracking' => 'inventory_tracking',
					'Quantity' => 'quantity',
					'Inv Out_Of Stock Purchase' => 'inv_out_of_stock_purchase',
					'Related Products' => 'related_products',
					'Product Images' => 'product_images',
				);
			}
		}
		if($module === 'MarketPressVariations') {
			if(in_array('marketpress/marketpress.php', $this->get_active_plugins())){
				$MetaFields = array(
					'SKU' => 'sku',
					'Variation Image' => 'mp_variation_image',
					'Sale Price' => 'sale_price',
					'Regular Price' => 'regular_price',
					'Per Order Limit' => 'per_order_limit',
					'Has Sale' => 'has_sale',
					'Percentage Discount' => 'sale_price_percentage',
					'Sale Price Start Date' => 'sale_price_start_date',
					'Sale Price End Date' => 'sale_price_end_date',
					'File URL' => 'file_url',
					'Special Tax Rate' => 'special_tax_rate',
					'Charge Tax' => 'charge_tax',
					'Charge Shipping' => 'charge_shipping',
					'Weight Pounds' => 'weight_pounds',
					'Weight Ounces' => 'weight_ounces',
					'Weight Extra Shipping Cost' => 'weight_extra_shipping_cost',
					'Inventory Tracking' => 'inventory_tracking',
					'Quantity' => 'quantity',
					'Inv Out_Of Stock Purchase' => 'inv_out_of_stock_purchase',
					'Variation Name' => 'mp_variation_name',
					'Variation Value' => 'mp_variation_value',
					'Has Variation Content' => 'has_variation_content',
					'Variation Content Type' => 'variation_content_type',
					'Variation Content Desc' => 'variation_content_desc',
				);
			}
		}
		if(!empty($MetaFields)){
			foreach($MetaFields as $key => $val) {
				$ecommerceMetaFields['ECOMMETA'][$val]['label'] = $key;
				$ecommerceMetaFields['ECOMMETA'][$val]['name'] = $val;
			}
		}
		return $ecommerceMetaFields;
	}

	/**
	 * WooCommerce Chained Product fields
	 * @return array
	 */
	public function WooCommerceChainedProductFields() {
		$ecommerceMetaFields = array();
		$chain_product = array(
			'Chained Product Detail' => 'chained_product_detail',
			'Chained Product Manage Stock' => 'chained_product_manage_stock',
		);
		foreach($chain_product as $key => $val) {
			$ecommerceMetaFields[$val]['label'] = $key;
			$ecommerceMetaFields[$val]['name'] = $val;
		}
		return $ecommerceMetaFields;
	}

	/**
	 * WooCommerce Product Retailer fields
	 * @return array
	 */
	public function WooCommerceProductRetailerFields() {
		$ecommerceMetaFields = array();
		$retailers = array(
			'Retailers Only Purchase' => 'wc_product_retailers_retailer_only_purchase',
			'Retailers Use Buttons' => 'wc_product_retailers_use_buttons',
			'Retailers Product Button Text' => 'wc_product_retailers_product_button_text',
			'Retailers Catalog Button Text' => 'wc_product_retailers_catalog_button_text',
			'Retailers Id' => 'wc_product_retailers_id',
			'Retailers Price' => 'wc_product_retailers_price',
			'Retailers URL' => 'wc_product_retailers_url',
		);
		foreach($retailers as $key => $val) {
			$ecommerceMetaFields[$val]['label'] = $key;
			$ecommerceMetaFields[$val]['name'] = $val;
		}
		return $ecommerceMetaFields;
	}

	/**
	 * WooCommerce Product Add-ons fields
	 * @return array
	 */
	public function WooCommerceProductAddOnsFields() {
		$ecommerceMetaFields = array();
		$product_Addons = array(
			'Product Addons Exclude Global' => 'product_addons_exclude_global',
			'Product Addons Group Name' => 'product_addons_group_name',
			'Product Addons Group Description' => 'product_addons_group_description',
			'Product Addons Type' => 'product_addons_type',
			'Product Addons Position' => 'product_addons_position',
			'Product Addons Required' => 'product_addons_required',
			'Product Addons Label Name' => 'product_addons_label_name',
			'Product Addons Price' => 'product_addons_price',
			'Product Addons Minimum' => 'product_addons_minimum',
			'Product Addons Maximum' => 'product_addons_maximum',
		);
		foreach($product_Addons as $key => $val) {
			$ecommerceMetaFields[$val]['label'] = $key;
			$ecommerceMetaFields[$val]['name'] = $val;
		}
		return $ecommerceMetaFields;
	}

	/**
	 * WooCommerce Warranty fields
	 * @return array
	 */
	public function WooCommerceWarrantyFields() {
		$ecommerceMetaFields = array();
		$warranty = array(
			'Warranty Label' => 'warranty_label',
			'Warranty Type' => 'warranty_type',
			'Warranty Length' => 'warranty_length',
			'Warranty Value' => 'warranty_value',
			'Warranty Duration' => 'warranty_duration',
			'Warranty Addons Amount' => 'warranty_addons_amount',
			'Warranty Addons Value' => 'warranty_addons_value',
			'Warranty Addons Duration' => 'warranty_addons_duration',
			'No Warranty Option' => 'no_warranty_option',
		);
		foreach($warranty as $key => $val) {
			$ecommerceMetaFields[$val]['label'] = $key;
			$ecommerceMetaFields[$val]['name'] = $val;
		}
		return $ecommerceMetaFields;
	}

	/**
	 * WooCommerce Pre-Order fields
	 * @return array
	 */
	public function WooCommercePreOrderFields() {
		$ecommerceMetaFields = array();
		$pre_orders = array(
			'Pre-Orders Enabled' => 'preorders_enabled',
			'Pre-Orders Fee' => 'preorders_fee',
			'Pre-Orders When to Charge' => 'preorders_when_to_charge',
			'Pre-Orders Availabilty Datetime' => 'preorders_availability_datetime'
		);
		foreach($pre_orders as $key => $val) {
			$ecommerceMetaFields[$val]['label'] = $key;
			$ecommerceMetaFields[$val]['name'] = $val;
		}
		return $ecommerceMetaFields;
	}

	/**
	 * WPeCommerce custom fields
	 *
	 * @return array
	 */
	public function WPeCommerceCustomFields() {
		$WPeComCustomFields = array();
		$get_wpecom_custom_fields = get_option('wpsc_cf_data');
		$wpecom_custom_fields = maybe_unserialize($get_wpecom_custom_fields);
		if(!empty($wpecom_custom_fields)) {
			foreach($wpecom_custom_fields as $key => $val) {
				$WPeComCustomFields['WPECOMMETA'][$val['slug']]['label'] = $val['name'];
				$WPeComCustomFields['WPECOMMETA'][$val['slug']]['name'] = $val['slug'];
			}
		}
		return $WPeComCustomFields;
	}

	public function fetchACFProRepeaterFields($repeater_field) {
		global $wpdb;
		$customFields = $rep_customFields = array();
		$repeater_field_arr = '';
		$get_sub_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in (%s)", array($repeater_field) ) );
		foreach ( $get_sub_fields as $get_sub_key ) {
			$get_sub_field_content = unserialize( $get_sub_key->post_content );
			if ( $get_sub_field_content['type'] == 'repeater' ) {
				$repeater_field_arr .= $get_sub_key->ID . ",";
			}
		}
		$repeater_field_arr = substr( $repeater_field_arr, 0, - 1 );
		$repeater_fields = explode(',', $repeater_field_arr);
		$repeater_field_placeholders = array_fill(0, count($repeater_fields), '%s');
		$placeholdersForRepeaterFields = implode(', ', $repeater_field_placeholders);
		if ( ! empty( $repeater_field_arr ) ) {
			$query = "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in ($placeholdersForRepeaterFields)";
			$get_acf_repeater_fields = $wpdb->get_results( $wpdb->prepare( $query, $repeater_fields ) );
		}

		if ( ! empty( $get_acf_repeater_fields ) ) {
			foreach ( $get_acf_repeater_fields as $acf_pro_repeater_fields ) {
				$rep_customFields[ $acf_pro_repeater_fields->post_title ] = $acf_pro_repeater_fields->post_excerpt;
				$check_exist_key = "ACF: " . $acf_pro_repeater_fields->post_title;
				if ( array_key_exists( $check_exist_key, $customFields ) ) {
					unset( $customFields[ $check_exist_key ] );
				}
				$customFields[ $acf_pro_repeater_fields->post_excerpt ]['label'] = $acf_pro_repeater_fields->post_title;
				$customFields[ $acf_pro_repeater_fields->post_excerpt ]['name']  = $acf_pro_repeater_fields->post_excerpt;
			}
		}
		return $customFields;
	}

	/**
	 * ACF Pro custom fields
	 *
	 * @param $import_type  - Import Type
	 * @param $importAs     - Import As
	 * @param $mode         - Mode
	 * @param $group        - Group of the fields ( ACF | RF )
	 *
	 * @return array
	 */
	public function ACFProCustomFields($import_type, $importAs, $mode, $group) {
		global $wpdb;
		global $uci_admin;
		$repeater_field_arr = $flexible_field_arr = "";
		$group_id_arr = $customFields = $rep_customFields = array();
		$get_acf_groups = $wpdb->get_results( $wpdb->prepare("select ID, post_content from $wpdb->posts where post_type = %s", 'acf-field-group'));
		// Get available ACF group id
		foreach ( $get_acf_groups as $item => $group_rules ) {
			$rule = maybe_unserialize($group_rules->post_content);
			if(!empty($rule)) {
				foreach($rule['location'] as $key => $value) {
					if($value[0]['operator'] == '==' && $value[0]['value'] == $this->import_post_types($import_type, $importAs)) {
						$group_id_arr[] = $group_rules->ID; #. ',';
					}elseif( $value[0]['operator'] == '==' && $value[0]['param'] == 'user_role'){
						$group_id_arr[] = $group_rules->ID;
					}
				}
			}
		}
		#TODO
		if ( !empty($group_id_arr) ) {
			foreach($group_id_arr as $groupId) {
				$get_acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in (%s)", array($groupId) ) );
				if ( ! empty( $get_acf_fields ) ) {
					foreach ( $get_acf_fields as $acf_pro_fields ) {
						$get_field_content = unserialize( $acf_pro_fields->post_content );
						if ( $get_field_content['type'] == 'repeater' ) {
							//$repeater_field_arr .= $acf_pro_fields->ID . ",";
							$repeater_field_arr[]= $acf_pro_fields->ID;
							foreach($repeater_field_arr as $repeater_field ) {
							//multi sup repeater
							//$repeater_field = substr( $repeater_field_arr, 0, - 1 );
							$get_sub_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in (%s)", array($repeater_field) ) );
							foreach ( $get_sub_fields as $get_sub_key ) {
								$get_sub_field_content = unserialize( $get_sub_key->post_content );
								if ( $get_sub_field_content['type'] == 'repeater' ) {
									$repeater_field_arr .= $get_sub_key->ID . ",";
								}
							}
						}
							//multi sup repeater  by priya..
						}
						if ( $get_field_content['type'] == 'group' ) {

							$group_field_arr .= $acf_pro_fields->ID . ",";
							$group_field = substr( $group_field_arr, 0, - 1 );
							$get_sub_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in (%s)", array($group_field) ) );
							foreach ( $get_sub_fields as $get_sub_key ) {
								$get_sub_field_content = unserialize( $get_sub_key->post_content );
								if ( $get_sub_field_content['type'] == 'text' ) {
								$group_field_arr .= $get_sub_key->ID . ",";
								}
							}
						} 
						else if ( $get_field_content['type'] == 'flexible_content' ) {
							$flexible_field_arr .= $acf_pro_fields->ID . ",";
						} else if ( $get_field_content['type'] == 'message' || $get_field_content['type'] == 'tab' ) {
							$customFields["ACF"][ $acf_pro_fields->post_name ]['label'] = $acf_pro_fields->post_title;
							$customFields["ACF"][ $acf_pro_fields->post_name ]['name']  = $acf_pro_fields->post_name;
						} else {
							if ( $acf_pro_fields->post_excerpt != null || $acf_pro_fields->post_excerpt != '' ) {
								$customFields["ACF"][ $acf_pro_fields->post_excerpt ]['label'] = $acf_pro_fields->post_title;
								$customFields["ACF"][ $acf_pro_fields->post_excerpt ]['name']  = $acf_pro_fields->post_excerpt;
							}
						}
					}
				}
				//$repeater_field_arr = substr( $repeater_field_arr, 0, - 1 );//priya..
				$group_field_arr = substr( $group_field_arr, 0, - 1 );
				$flexible_field_arr = substr( $flexible_field_arr, 0, - 1 );
				//$repeater_fields = explode(',', $repeater_field_arr); //priya..
				$repeater_fields = $repeater_field_arr;
				$flexible_fields = explode(',', $flexible_field_arr);
				$group_fields=explode(',', $group_field_arr);
				$repeater_field_placeholders = array_fill(0, count($repeater_fields), '%s');
				$group_field_placeholders = array_fill(0, count($group_fields), '%s');
				$flexible_field_placeholders = array_fill(0, count($flexible_fields), '%s');
				// Put all the placeholders in one string ‘%s, %s, %s, %s, %s,…’
				$placeholdersForGroupFields = implode(', ', $group_field_placeholders);
				$placeholdersForRepeaterFields = implode(', ', $repeater_field_placeholders);
				$placeholdersForFlexibleFields = implode(', ', $flexible_field_placeholders);
				if ( ! empty( $repeater_field_arr ) ) {
					$query = "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in ($placeholdersForRepeaterFields)";
					$get_acf_repeater_fields = $wpdb->get_results( $wpdb->prepare( $query, $repeater_fields ) );
				}
				if ( ! empty( $group_field_arr ) ) {
					$query1 = "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in ($placeholdersForGroupFields)";
					$get_acf_group_fields = $wpdb->get_results( $wpdb->prepare( $query1, $group_fields ) );
								}
				if ( ! empty( $get_acf_repeater_fields ) ) {
					foreach ( $get_acf_repeater_fields as $acf_pro_repeater_fields ) {
						# TODO: Get sub fields of repeater fields
						$get_sub_field_content = unserialize( $acf_pro_repeater_fields->post_content );
						if($get_sub_field_content['type'] == 'repeater') {
							$repeaterSubFields = $this->fetchACFProRepeaterFields($acf_pro_repeater_fields->ID);
							$customFields['RF'] = array_merge($repeaterSubFields, $customFields['RF']);
						} else {
							$rep_customFields[ $acf_pro_repeater_fields->post_title ] = $acf_pro_repeater_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_repeater_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["RF"][ $acf_pro_repeater_fields->post_excerpt ]['label'] = $acf_pro_repeater_fields->post_title;
							$customFields["RF"][ $acf_pro_repeater_fields->post_excerpt ]['name']  = $acf_pro_repeater_fields->post_excerpt;
						}
					}
				}
				if ( ! empty( $get_acf_group_fields ) ) {
					foreach ( $get_acf_group_fields as $acf_pro_group_fields ) {
						# TODO: Get sub fields of repeater fields
						$get_sub_field_content = unserialize( $acf_pro_group_fields->post_content );
						if($get_sub_field_content['type'] == 'text') {
							
							$rep_customFields[ $acf_pro_group_fields->post_title ] = $acf_pro_group_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_group_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['label'] = $acf_pro_group_fields->post_title;
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['name']  = $acf_pro_group_fields->post_excerpt;
							$customFields["ACF"]['groupfield_slug']['label'] = 'groupfield_slug';
							$customFields["ACF"]['groupfield_slug']['name'] = 'groupfield_slug';
							
						
					}
					if($get_sub_field_content['type'] == 'image') {
							
							$rep_customFields[ $acf_pro_group_fields->post_title ] = $acf_pro_group_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_group_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['label'] = $acf_pro_group_fields->post_title;
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['name']  = $acf_pro_group_fields->post_excerpt;
						
					}
					if($get_sub_field_content['type'] == 'select') {
							
							$rep_customFields[ $acf_pro_group_fields->post_title ] = $acf_pro_group_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_group_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['label'] = $acf_pro_group_fields->post_title;
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['name']  = $acf_pro_group_fields->post_excerpt;
						
					}
					if($get_sub_field_content['type'] == 'taxonomy') {
							
							$rep_customFields[ $acf_pro_group_fields->post_title ] = $acf_pro_group_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_group_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['label'] = $acf_pro_group_fields->post_title;
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['name']  = $acf_pro_group_fields->post_excerpt;
						
					}
					if($get_sub_field_content['type'] == 'user') {
							
							$rep_customFields[ $acf_pro_group_fields->post_title ] = $acf_pro_group_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_group_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['label'] = $acf_pro_group_fields->post_title;
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['name']  = $acf_pro_group_fields->post_excerpt;
						
					}
					if($get_sub_field_content['type'] == 'page_link') {
							
							$rep_customFields[ $acf_pro_group_fields->post_title ] = $acf_pro_group_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_group_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['label'] = $acf_pro_group_fields->post_title;
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['name']  = $acf_pro_group_fields->post_excerpt;
						
					}
					if($get_sub_field_content['type'] == 'date_time_picker') {
							
							$rep_customFields[ $acf_pro_group_fields->post_title ] = $acf_pro_group_fields->post_excerpt;
							$check_exist_key = "ACF: " . $acf_pro_group_fields->post_title;
							if ( array_key_exists( $check_exist_key, $customFields ) ) {
								unset( $customFields[ $check_exist_key ] );
							}
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['label'] = $acf_pro_group_fields->post_title;
							$customFields["ACF"][ $acf_pro_group_fields->post_excerpt ]['name']  = $acf_pro_group_fields->post_excerpt;
						
					}

					}
				}

				if ( ! empty( $flexible_field_arr ) ) {
					$query =  "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in ($placeholdersForFlexibleFields)";
					$get_acf_flexible_content_fields = $wpdb->get_results( $wpdb->prepare( $query, $flexible_fields ) );
				}
				if ( ! empty( $get_acf_flexible_content_fields ) ) {
					foreach ( $get_acf_flexible_content_fields as $acf_pro_fc_fields ) {
						$fc_customFields[ $acf_pro_fc_fields->post_title ] = $acf_pro_fc_fields->post_excerpt;
						$check_exist_key = "ACF: " . $acf_pro_fc_fields->post_title;
						if ( array_key_exists( $check_exist_key, $customFields ) ) {
							unset( $customFields[ $check_exist_key ] );
						}
						$customFields["RF"][ $acf_pro_fc_fields->post_excerpt ]['label'] = $acf_pro_fc_fields->post_title;
						$customFields["RF"][ $acf_pro_fc_fields->post_excerpt ]['name']  = $acf_pro_fc_fields->post_excerpt;
					}
				}
			}
		}
		$requested_group_fields = array();
		if(!empty($customFields[$group]))
			$requested_group_fields[$group] = $customFields[$group];
		return $requested_group_fields;
	}

	/**
	 * ACF custom fields
	 *
	 * @param $import_type  - Import Type
	 * @param $importAs     - Import As
	 * @param $mode         - Mode
	 *
	 * @return array
	 */
	public function ACFCustomFields($import_type, $importAs, $mode) {



global $wpdb;
                $get_acf_fields = $customFields = array();
 $group_id_arr=array();       
 $repeater_field_arr = $flexible_field_arr = "";

$get_acf_groups = $wpdb->get_results( $wpdb->prepare("select ID, post_content from $wpdb->posts where post_type = %s", 'acf-field-group'));
                // Get available ACF group id
                foreach ( $get_acf_groups as $item => $group_rules ) {
                        $rule = maybe_unserialize($group_rules->post_content);
                        if(!empty($rule)) {
                                foreach($rule['location'] as $key => $value) {
                                        if($value[0]['operator'] == '==' && $value[0]['value'] == $this->import_post_types($import_type, $importAs)) {
                                                $group_id_arr[] = $group_rules->ID; #. ',';
                                        }elseif( $value[0]['operator'] == '==' && $value[0]['param'] == 'user_role'){
                                                $group_id_arr[] = $group_rules->ID;
                                        }
                                }
                        }
                }

	
if ( !empty($group_id_arr) ) {
                        foreach($group_id_arr as $groupId) {
                                $get_acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in (%s)", array($groupId) ) );
if ( ! empty( $get_acf_fields ) ) {
                                        foreach ( $get_acf_fields as $acf_pro_fields ) {
                                                $get_field_content = unserialize( $acf_pro_fields->post_content );
if ( $acf_pro_fields->post_excerpt != null || $acf_pro_fields->post_excerpt != '' ) {
                                                                $customFields["ACF"][ $acf_pro_fields->post_excerpt ]['label'] = $acf_pro_fields->post_title;
                                                                $customFields["ACF"][ $acf_pro_fields->post_excerpt ]['name']  = $acf_pro_fields->post_excerpt;
                                                        }
}

}
}

}

 return $customFields;
}

	/**
	 * ACF repeater fields
	 *
	 * @param $import_type  - Import Type
	 * @param $importAs     - Import As
	 * @param $mode         - Mode
	 *
	 * @return array
	 */
	public function ACFRepeaterFields($import_type, $importAs, $mode) {
		global $wpdb;
		$get_repeater_fields = $customFields = array();

		$get_acf_groups     = $wpdb->get_results( $wpdb->prepare( "select p.ID, pm.meta_value from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = %s and pm.meta_key = %s", 'acf', 'rule' ) );
		$group_id_arr       = $repeater_field_arr = $flexible_field_arr = "";

		// Get available ACF group id
		foreach ( $get_acf_groups as $item => $group_rules ) {
			$rule = maybe_unserialize($group_rules->meta_value);
			if(!empty($rule) && $rule['operator'] == '==' && $rule['value'] == $this->import_post_types($import_type, $importAs)) {
				$group_id_arr .= $group_rules->ID . ',';
			}
		}
		if($group_id_arr != '') {
			$group_id_arr = substr( $group_id_arr, 0, - 1 );

			// Get available ACF fields based on the import type and group id
			$get_repeater_fields = $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta
														WHERE post_id IN ($group_id_arr)
                                                        GROUP BY meta_key
                                                        HAVING meta_key LIKE 'field_%'
                                                        ORDER BY meta_key" );
		}
		if (!empty($get_repeater_fields)) {
			foreach( $get_repeater_fields as $repFieldDet ) {
				$repeaterFields = unserialize($repFieldDet);
				foreach( $repeaterFields as $fieldKey => $fieldVal ) {
					if($fieldKey == 'sub_fields') {
						for($a=0; $a<count($fieldVal); $a++) {
							$customFields['RF'][$fieldVal[$a]['name']]['label'] = $fieldVal[$a]['label'];
							$customFields['RF'][$fieldVal[$a]['name']]['name']  = $fieldVal[$a]['name'];
						}
					}
				}
			}
		}
		return $customFields;
	}

	/**
	 * PODS fields
	 *
	 * @param $import_type  - Import Type
	 * @param $importAs     - Import As
	 * @param $mode         - Mode
	 *
	 * @return array
	 */
	public function PODSCustomFields($import_type, $importAs, $mode) {
		global $wpdb;
		$podsFields = array();
		$import_type = $this->import_post_types($import_type, $importAs);
		$post_id = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_name= %s and post_type = %s", $import_type, '_pods_pod'));
		if(!empty($post_id)) {
			$lastId          = $post_id[0]->ID;
			$get_pods_fields = $wpdb->get_results( $wpdb->prepare( "SELECT post_title, post_name FROM $wpdb->posts where post_parent = %d AND post_type = %s", $lastId, '_pods_field' ) );
			if ( ! empty( $get_pods_fields ) ) :
				foreach ( $get_pods_fields as $pods_field ) {
					$podsFields["PODS"][ $pods_field->post_name ]['label'] = $pods_field->post_title;
					$podsFields["PODS"][ $pods_field->post_name ]['name']  = $pods_field->post_name;
				}
			endif;
		}
		return $podsFields;
	}

	/**
	 * Types fields
	 *
	 * @param $import_type - Import Type
	 * @param $importAs    - Import As
	 * @param $mode        - Mode
	 *
	 * @return array|string
	 */
	public function TypesCustomFields($import_type, $importAs, $mode) {
		global $wpdb;
		$typesFields = array();
		if($import_type == 'Users') {

			$getUserMetaFields = get_option('wpcf-usermeta');
			if(is_array($getUserMetaFields)) {
				foreach ($getUserMetaFields as $optKey => $optVal) {
					$typesFields["TYPES"][$optVal['slug']]['label'] = $optVal['name'];
					$typesFields["TYPES"][$optVal['slug']]['name'] = $optVal['slug'];
				}
			}
			$typesFields['TYPES']['Parent_group']['label'] = 'Parent Group';
			$typesFields['TYPES']['Parent_group']['name'] = 'Parent_Group';
			$typesFields['TYPES']['Parent_group']['slug'] = 'Parent_Group';

		} else {

			$import_type = $this->import_post_types($import_type, $importAs);
			$get_groups = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_type = %s", 'wp-types-group'));
                        $get_groupsc = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_type = %s", 'wp-types-term-group'));
			if(!empty($get_groupsc)) {
				foreach($get_groupsc as $item => $group) {
					$lastId       = $group->ID;
					$rule_groups  =$import_type;
					$rule_groups = trim($rule_groups,',');
					$rules = explode(',', $rule_groups);
					if(in_array($import_type, $rules)) {
						$fields       = get_post_meta( $lastId, '_wp_types_group_fields', true );
						$fields       = trim($fields, ',');
#$trim         = substr( $fields, 1, - 1 );
						$types_fields = explode( ',', $fields );
						$count        = count( $types_fields );
						if ( is_array( $types_fields ) ) {
							for ( $i = 0; $i < $count; $i ++ ) {
								foreach ( $types_fields as $key => $value ) {
									$typesFields['TYPES'][ $value ]['name']  = $value;
									$typesFields['TYPES'][ $value ]['slug']  = $value;
									$typesFields['TYPES'][ $value ]['label'] = $value;
								}
							}
						}
					}
				}
			}
			if(!empty($get_groups)) {
				foreach($get_groups as $item => $group) {
					$lastId       = $group->ID;

					$rule_groups  = get_post_meta( $lastId, '_wp_types_group_post_types', true );


					$rule_groups = trim($rule_groups,',');
					$rules = explode(',', $rule_groups);

					$fields       = get_post_meta( $lastId, '_wp_types_group_fields', true );
					$fields       = trim($fields, ',');
#$trim         = substr( $fields, 1, - 1 );
					$types_fields = explode( ',', $fields );
					//print_r($types_fields);
					$count        = count( $types_fields );
					if ( is_array( $types_fields ) ) {
						for ( $i = 0; $i < $count; $i ++ ) {
							foreach ( $types_fields as $key => $value ) {
								//change repeatable_group to user readable format 
								$value=$this->changeRepeatableGroupName($value);

								$typesFields['TYPES'][ $value ]['name']  = $value;
								$typesFields['TYPES'][ $value ]['slug']  = $value;
								$typesFields['TYPES'][ $value ]['label'] = $value;
							}
						}
					}
				}

			} 
			$typesFields['TYPES']['types_relationship']['label'] = 'types_relationship';
			$typesFields['TYPES']['types_relationship']['name'] = 'types_relationship';
			$typesFields['TYPES']['types_relationship']['slug'] = 'types_relationship';
			$typesFields['TYPES']['intermediate']['label'] = 'intermediate';
			$typesFields['TYPES']['intermediate']['name'] = 'intermediate';
			$typesFields['TYPES']['intermediate']['slug'] = 'intermediate';

			$typesFields['TYPES']['relationship_slug']['label'] = 'relationship_slug';
			$typesFields['TYPES']['relationship_slug']['name'] = 'relationship_slug';
			$typesFields['TYPES']['relationship_slug']['slug'] = 'relationship_slug';

			$typesFields['TYPES']['Parent_group']['label'] = 'Parent Group';
			$typesFields['TYPES']['Parent_group']['name'] = 'Parent_Group';
			$typesFields['TYPES']['Parent_group']['slug'] = 'Parent_Group';
		}
		return $typesFields;
	}
	/**
	 * CCTM fields
	 * @return array
	 */
       public function changeRepeatableGroupName($value)
       {
        global $wpdb;
        $explode = explode('_',$value);
        if (count($explode)>1) {
            if (in_array('repeatable',$explode)) {
                $name = $wpdb->get_results("SELECT post_name FROM ".$wpdb->prefix."posts WHERE id ='{$explode[3]}'");
                return $name[0]->post_name;
            }
        }else{
            return $value;
        }
    
}
	public function CCTMCustomFields() {
		$cctmFields = array();
		$getOptions = get_option('cctm_data');
		$get_cctm_fields = $getOptions['custom_field_defs'];
		if(!empty($get_cctm_fields)) :
			foreach ($get_cctm_fields as $optKey => $optVal) {
				$cctmFields["CCTM"][$optVal['name']]['label'] = $optVal['label'];
				$cctmFields["CCTM"][$optVal['name']]['name'] = $optVal['name'];
			}
		endif;
		return $cctmFields;
	}

	/**
	 * All in One SEO fields
	 * @return array
	 */
	public function AIOSEOFields() {
		$aioseoFields = array();
		$seoFields = array(
			'Keywords' => 'keywords',
			'Description' => 'description',
			'Title' => 'title',
			'NOINDEX' => 'noindex',
			'NOFOLLOW' => 'nofollow',
			'Canonical URL' => 'custom_link',
			'Title Atr' => 'titleatr',
			'Menu Label' => 'menulabel',
			'Disable' => 'disable',
			'Disable Analytics' => 'disable_analytics',
			'NOODP' => 'noodp',
			'NOYDIR' => 'noydir'
		);
		foreach ($seoFields as $key => $val) {
			$aioseoFields['AIOSEO'][$val]['label'] = $key;
			$aioseoFields['AIOSEO'][$val]['name'] = $val;
		}
		return $aioseoFields;
	}

	/**
	 * WordPress Yoast SEO fields
	 * @return array
	 */
	public function YoastSEOFields() {
		$yoastseoFields = array();
		$seoFields = array(
			'SEO Title' => 'title',
			'Meta Description' => 'meta_desc',
			'Meta Robots Index' => 'meta-robots-noindex',
			'Meta Robots Follow' => 'meta-robots-nofollow',
			'Meta Robots Advanced' => 'meta-robots-adv',
			'Breadcrumbs Title'  => 'bread-crumbs-title',
			'Include in Sitemap' => 'sitemap-include',
			'Sitemap Priority' => 'sitemap-prio',
			'Canonical URL' => 'canonical',
			'301 Redirect' => 'redirect',
			'Facebook Title' => 'opengraph-title',
			'Facebook Description' => 'opengraph-description',
			'Facebook Image' => 'opengraph-image',
			'Twitter Title' => 'twitter-title',
			'Twitter Description' => 'twitter-description',
			'Twitter Image' => 'twitter-image',
			'Google+ Title' => 'google-plus-title',
			'Google+ Description' => 'google-plus-description',
			'Google+ Image' => 'google-plus-image',
			'Focus Keyword' => 'focus_keyword'
		);
		foreach ($seoFields as $key => $val) {
			$yoastseoFields['YOASTSEO'][$val]['label'] = $key;
			$yoastseoFields['YOASTSEO'][$val]['name'] = $val;
		}
		return $yoastseoFields;
	}

	/**
	 * WordPress CMB2 fields
	 * @return array
	 */
	public function CMB2Fields() {
		global $wpdb;
		$get_csvpro_settings = get_option('sm_uci_pro_settings');
		$prefix = $get_csvpro_settings['cmb2'];
		$get_meta_info = $wpdb->get_results(("select distinct(meta_key) from {$wpdb->prefix}postmeta where meta_key like '%{$prefix}%'"), ARRAY_A);
		foreach($get_meta_info as $key => $val){
			$meta_key = str_replace($prefix," ",$get_meta_info[$key]['meta_key']);
			$cmb2Fields[$meta_key] = $get_meta_info[$key]['meta_key'];
		}
		$cmbFields = array();

		foreach ($cmb2Fields as $key => $val){
			$cmbFields['CMB2'][$val]['label'] = $key;
			$cmbFields['CMB2'][$val]['name'] = $val;
		}
		return $cmbFields;
	}

	public function CFSFields(){
		global $wpdb;
		$customFields = $cfs_field = array();
		$get_cfs_groups = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_type = %s", 'cfs'),ARRAY_A);
		$group_id_arr = '';
		foreach ( $get_cfs_groups as $item => $group_rules ) {
			$group_id_arr .= $group_rules['ID'] . ',';
		}
		if($group_id_arr != '') {
			$group_id_arr = substr( $group_id_arr, 0, - 1 );
			// Get available CFS fields based on the import type and group id
			$get_cfs_fields = $wpdb->get_results( $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE post_id IN (%s) and meta_key =%s ",$group_id_arr,'cfs_fields'), ARRAY_A);
		}
		// Available CFS fields
		if (!empty($get_cfs_fields)) {
			foreach ($get_cfs_fields as $key => $value) {
				$get_cfs_field = @unserialize($value['meta_value']);
				foreach($get_cfs_field as $fk => $fv){
					$customFields["CFS"][$fv['name']]['label'] = $fv['label'];
					$customFields["CFS"][$fv['name']]['name'] = $fv['name'];
					$customFields["CFS"][$fv['name']]['type'] = $fv['type'];
					$customFields["CFS"][$fv['name']]['fieldid'] = $fv['id'];
					$cfs_field[] = $fv['name'];
				}
			}
		}
		return $customFields;
	}

	/**
	 * Custom fields by WP Members for users
	 * @return array
	 */
	public function custom_fields_by_wp_members () {
		$WPMemberFields = array();
		$get_WPMembers_fields = get_option('wpmembers_fields');
		if (is_array($get_WPMembers_fields) && !empty($get_WPMembers_fields)) {
			foreach ($get_WPMembers_fields as $get_fields) {
				$WPMemberFields['WPMEMBERS'][$get_fields[2]]['label'] = $get_fields[1];
				$WPMemberFields['WPMEMBERS'][$get_fields[2]]['name'] = $get_fields[2];
			}
		}
		return $WPMemberFields;
	}

	public function custom_fields_by_members () {
		$MemberFields = array();
		$MemberFields['MULTIROLE']['multi_user_role']['label'] = 'Multi User Role';
		$MemberFields['MULTIROLE']['multi_user_role']['name'] = 'multi_user_role';
		return $MemberFields;
	}

	/**
         * Custom fields by Ultimate Members for users
         * @return array
         */
	public function custom_fields_by_ultimate_member () {
		$WPUltimateMember = array();
		$get_WPUltimateMember = get_option('um_fields');
		if(is_array($get_WPUltimateMember) && !empty($get_WPUltimateMember)) {
			foreach($get_WPUltimateMember as $get_fields) {
				$WPUltimateMember['ULTIMATEMEMBER'][$get_fields['metakey']]['label'] = $get_fields['label'];
				$WPUltimateMember['ULTIMATEMEMBER'][$get_fields['metakey']]['name'] = $get_fields['metakey'];
			}
		}
		return $WPUltimateMember;
	}

	/**
	 * Get billing & shipping field information for Users
	 * @return array
	 */
	public function billing_information_for_users () {
		$billing_and_shipping_info = array();
		if(in_array( 'marketpress/marketpress.php', $this->get_active_plugins() ) || in_array( 'wordpress-ecommerce/marketpress.php', $this->get_active_plugins() )) {
			foreach($this->def_mpCols as $mp_key => $mp_val) {
				$billing_and_shipping_info['BSI'][$mp_val]['label'] = $mp_key;
				$billing_and_shipping_info['BSI'][$mp_val]['name'] = $mp_val;
			}
		}
		if(in_array( 'woocommerce/woocommerce.php', $this->get_active_plugins() )) {
			foreach($this->def_wcCols as $woo_key => $woo_val) {
				$billing_and_shipping_info['BSI'][$woo_val]['label'] = $woo_key;
				$billing_and_shipping_info['BSI'][$woo_val]['name'] = $woo_val;
			}
		}
		return $billing_and_shipping_info;
	}

	/**
	 * Terms & Taxonomies based on the post types
	 *
	 * @param $type         - Type
	 * @param $optionalType - Optional Type
	 * @param $mode         - Mode
	 *
	 * @return array
	 */
	public function terms_and_taxonomies($type, $optionalType = null, $mode = null) {
		$term_taxonomies = array();
		$importas = $this->import_post_types($type);
		$taxonomies = get_object_taxonomies( $importas, 'names' );
		if(!empty($taxonomies)) {
			foreach ($taxonomies as $key => $value) {
				$get_taxonomy_label = get_taxonomy($value);
				#$taxonomy_label = $get_taxonomy_label->labels->singular_name;
				$taxonomy_label = $get_taxonomy_label->name;
				if($value == 'wpsc_product_category' || $value == 'product_cat'){
					$value = 'product_category';
				}elseif($value == 'category'){
					$value = 'post_category';
				}
				$term_taxonomies['TERMS'][$key]['label'] = $taxonomy_label;
				$term_taxonomies['TERMS'][$key]['name'] = $value;
			}
		}
		return $term_taxonomies;
	}

	/**
	 * Convert file name to hash key ( event )
	 *
	 * @param $value
	 *
	 * @return false|string
	 */
	public function convert_string2hash_key($value) {
		$file_name = hash_hmac('md5', "$value", 'secret');
		return $file_name;
	}

	/**
	 * Get (Logs / Uploads / Zip uploads) directory
	 *
	 * @param $parserObj
	 * @param string $check
	 *
	 * @return string
	 */
	public function getUploadDirectory($parserObj, $check = 'plugin_uploads') {
		$upload_dir = wp_upload_dir(); // WordPress upload directory
		if ($check == 'plugin_uploads') {
			return $upload_dir ['basedir'] . "/" . $parserObj->uploadDir;
		} else if ($check == 'ftpdownload') {
			return $upload_dir ['basedir'] . "/ultimate_importer_ftpfiles";
		}
		else if ($check == 'Export_csv_log') {
			return $upload_dir ['basedir'] . "/" . $parserObj->exportDir;
		}
		else if ($check == 'zip_uploads') {
			return $upload_dir ['basedir'] . "/" . $parserObj->zipDir;
		}
		else {
			return $upload_dir ['basedir'];
		}
	}

	/**
	 * Get event mapping information
	 *
	 * @param $eventKey
	 *
	 * @return mixed
	 */
	public function getEventMapping($eventKey) {
		$parserObj = new SmackCSVParser();
		$screen_info_file = $this->getUploadDirectory($parserObj, 'plugin_uploads') . "/" . $parserObj->screenDataDir . '/' . $eventKey; #ToDo: Need to create the ScreenData directory and save all post values in the event file
		$get_screen_data = fopen($screen_info_file, 'r');
		$get_screen_data = fread($get_screen_data, filesize($screen_info_file));
		@fclose($get_screen_data);
		$screen_data = unserialize($get_screen_data);
		return $screen_data['mapping_config'];
	}

	/**
	 * Save screen information for the event
	 *
	 * @param null $eventKey
	 * @param null $values
	 */
	public function SetPostValues($eventKey = null, $values = null){
		$uploadPath = SM_UCI_IMPORT_DIR . '/' . $eventKey;
		if (!is_dir($uploadPath)) {
			wp_mkdir_p($uploadPath);
		}
		$filename = $uploadPath . '/screenInfo.txt';
		$myfile = fopen($filename, "w") or die("Unable to open file!");
		$post_values[$eventKey] = $values;
		$post_values = serialize($post_values);
		fwrite($myfile, $post_values);
		fclose($myfile);
		$_SESSION[$eventKey] = $values;
	}

	/**
	 * Retrieve the screen information for the event
	 *
	 * @param $eventKey
	 *
	 * @return array|mixed
	 */
	public function GetPostValues($eventKey){
		$uploadPath = SM_UCI_IMPORT_DIR . '/' . $eventKey;
		$screen_info_file = $uploadPath.'/screenInfo.txt';
		$screen_data = array();
		if(file_exists($screen_info_file)) {
			$get_screen_data = fopen( $screen_info_file, 'r' );
			$get_screen_data = fread( $get_screen_data, filesize( $screen_info_file ) );
			@fclose( $get_screen_data );
			$screen_data = unserialize( $get_screen_data );
		}
		return $screen_data;
	}

	/**
	 * Import Data
	 *
	 * @param $eventKey     - Event Key
	 * @param $importType   - Import Type
	 * @param $importMethod - Import Method
	 * @param $mode         - Mode
	 * @param $data         - Data Array
	 * @param $currentLimit - Current Limit
	 * @param $eventMapping - Event Mapping
	 * @param $affectedRecords  - Affected Records
	 * @param $mediaConfig  - Media Configuration
	 * @param $importConfig - Import Configuration
	 * @param $duplicate_headers - Find Duplicate with this
	 */
	public function importData($eventKey, $importType, $importMethod, $mode, $data, $currentLimit, $eventMapping, $affectedRecords, $mediaConfig, $importConfig, $duplicate_headers=array()) {	
		
		global $wpdb, $uci_admin;
		#$uci_admin->setImportAs($uci_admin->import_post_types($importType));
		$uci_admin->setEventInformation('csv_row_data', $data);
		$uci_admin->processing_row_id = $currentLimit;
		/* $customPosts = $uci_admin->get_import_custom_post_types();
		if (in_array($importType, get_taxonomies())) {
			if($importType == 'category' || $importType == 'product_category' || $importType == 'product_cat' || $importType == 'wpsc_product_category' || $importType == 'event-categories'):
				$importType = 'Categories';
			elseif($importType == 'product_tag' || $importType == 'event-tags' || $importType == 'post_tag'):
				$importType = 'Tags';
			else:
				$importType = 'Taxonomies';
			endif;
		}
		if (in_array($importType, $customPosts)) {
			$importType = 'CustomPosts';
		} */

		//Assigning the data array to the global variable (based on groups)
		$result = array();
		$available_groups_type = $uci_admin->available_widgets($importType, $this->event_information['import_as']);
                if(in_array('types/wpcf.php', $this->get_active_plugins()))
                {
               	$available_groups_type=array('CORE','TYPES');
                }
		$getRowMapping = $uci_admin->getRowMapping();
		$mapping_config = $uci_admin->getMappingConfiguration();
		$event_information = $uci_admin->getEventInformation();
		if(empty($event_information)) {
			$screen_data = $uci_admin->GetPostValues( $eventKey );
		} else {
			$screen_data = $uci_admin->getEventInformation();
		}

		$mapping_method = isset($mapping_config['smack_uci_mapping_method']) ? $mapping_config['smack_uci_mapping_method'] : $screen_data[$eventKey]['mapping_config']['smack_uci_mapping_method'];



		$currentMapping = $this->generateDataArrayBasedOnGroups( $available_groups_type, $eventMapping, $data, $mapping_method);
		
		if((isset($screen_data['import_file_info']['file_extension']) && $screen_data['import_file_info']['file_extension'] == 'xml') || ( isset($screen_data[$eventKey]['import_file']['file_extension']) && $screen_data[$eventKey]['import_file']['file_extension'])){

				$tag = $screen_data['mapping_config']['xml_tag_name'];

				$xmlparse = new SmackNewXMLImporter();
				$file = SM_UCI_IMPORT_DIR.'/'.$eventKey.'/'.$eventKey;
				$doc = new DOMDocument();
				$doc->load($file);
				
				foreach ($currentMapping as $field => $value) {
					foreach ($value as $head => $val) {
						if (preg_match('/{/',$val) && preg_match('/}/',$val)){
							$val = str_replace('{', '', $val);
							$val = str_replace('}', '', $val);
							$val = preg_replace("(".$tag."[+[0-9]+])", $tag."[".$currentLimit."]", $val);
							$currentMapping[$field][$head] = $xmlparse->parse_element($doc,$val);
						} 
						else{
							$currentMapping[$field][$head] = $val;
						}
						
					}
				}
				if(empty($getRowMapping) && empty($currentMapping)) {
				$uci_admin->setRowMapping( $data );
			} else {
				$uci_admin->setRowMapping($this->generateDataArrayBasedOnGroups($available_groups_type, $eventMapping, $data, $mapping_method));
			}
			$data_to_import = $currentMapping;
		}
		else{
			if(empty($getRowMapping) && empty($currentMapping)) {
			$uci_admin->setRowMapping( $data );
		} else {
			$uci_admin->setRowMapping($this->generateDataArrayBasedOnGroups($available_groups_type, $eventMapping, $data, $mapping_method));
		}
			$data_to_import = $uci_admin->getRowMapping();
		}

		// Added this foreach to prevent space issue for advanced mapping
		foreach ($data_to_import as $datakey => $datavalue) {
			foreach ($datavalue as $mainkey => $mainvalue) {
				$data_to_import[$datakey][$mainkey] = trim($mainvalue);
			}
		}		

		if($duplicate_headers){
			$duplicateHandling = array(
				'is_duplicate_handle' => 'On',
				'conditions' => array($duplicate_headers),
				'action' => 'update',
				'media_handling' => ''
			);
		}else{
			$duplicateHandling = array(
				'is_duplicate_handle' => isset($screen_data['import_config']['duplicate']) ? $screen_data['import_config']['duplicate'] : '',
				'conditions' => !empty($screen_data['import_config']['duplicate_conditions']) ? $screen_data['import_config']['duplicate_conditions'] : array(),
				'action' => isset($screen_data['import_config']['handle_duplicate']) ? $screen_data['import_config']['handle_duplicate'] : '',
				'media_handling' => isset($mediaConfig) ? $mediaConfig : ''
			);
		}
		

		// Import data with fast mode added by Fredrick Marks
		foreach (array('transition_post_status', 'save_post', 'pre_post_update', 'add_attachment', 'edit_attachment', 'edit_post', 'post_updated', 'wp_insert_post') as $act) {
			remove_all_actions($act);
		}

		foreach ($data_to_import as $groupName => $groupValue) {			
			switch ($groupName) {
				case 'CORE':
					$result = $this->importDataForCoreFields($groupValue, $importType, $mode, $eventKey, $duplicateHandling, $mediaConfig, $importConfig);

					#Todo: Assign the last imported record id.
					$last_import_id = isset($result['ID']) ? $result['ID'] : '';
					$this->setLastImportId($last_import_id);
					#$mode_of_affect = isset($result['MODE']) ? $result['MODE'] : '';
					#$assign_author = isset($result['AUTHOR']) ? $result['AUTHOR'] : '';
					#$error_msg = isset($result['ERROR_MSG']) ? $result['ERROR_MSG'] : '';
					if($importType == 'Taxonomies' || $importType == 'Categories' || $importType == 'Tags'|| $importType == 'Comments')
						$importType = 'Term';
					if ($this->getLastImportId() != '') {
						// Push event logs in database
						$wpdb->insert('wp_ultimate_csv_importer_log_values',
							array(
								'eventKey' => $eventKey,
								'recordId' => $this->getLastImportId(),
								'module' => $importType,
								'mode_of_import' => $mode
							), array('%s', '%d', '%s', '%s')
						);
						if($mode != 'Schedule') {
							// Generate link for Web view & Admin view
							if ( $importType == 'Posts' || $importType == 'CustomPosts' || $importType == 'Pages' || $importType == 'WooCommerce' || $importType == 'MarketPress' || $importType == 'eShop' || $importType == 'WPeCommerce' || $importType == 'ticket') {
								if ( ! isset( $groupValue['post_title'] ) ) {
									$groupValue['post_title'] = '';
								}
								$this->detailed_log[$currentLimit]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_permalink( $this->getLastImportId() ) . "' target='_blank' title='" . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $groupValue['post_title'] ) ) . "'rel='permalink'>Web View</a> | <a href='" . get_edit_post_link( $this->getLastImportId(), true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
							}elseif ($importType == 'WooCommerceVariations') {
								//No log for variation
								//$this->detailed_log[$currentLimit]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_edit_post_link( $this->getLastImportId(), true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
							}
							 elseif($importType == 'WooCommerceOrders' || $importType == 'WooCommerceCoupons'){
								$this->detailed_log[$currentLimit]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_edit_post_link( $this->getLastImportId(), true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
							}
							elseif( $importType == 'Users'){
							$this->detailed_log[$currentLimit]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_edit_user_link( $this->getLastImportId() , true ) . "' target='_blank' title='" . esc_attr( 'Edit this item' ) . "'> User Profile </a>";
							}
						} 
					}
					break;
				case 'ECOMMETA':					
					$helperObj = $event_information['instance'];
					$helperObj->importMetaInformation($groupValue, $this->getLastImportId());
					break;
				case 'CORECUSTFIELDS':
					$serialize_info = $uci_admin->getRowMapping();
                                        $core_serialize_info = $serialize_info['SerializeVal'];
					$this->importDataForWPMetaFields($groupValue, $this->getLastImportId(), $importType , $core_serialize_info);
					break;
				case 'ACF':
					$acf_row_mapping = $this->getRowMapping('ACF');
					if(empty($acf_row_mapping) && !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-acf-data-import.php')) {
						break;
					}
					global $acfHelper;
					$acfHelper->push_acf_data($data_to_import, $duplicateHandling, $mediaConfig);
					break;
				case 'RF':
					$acf_rf_row_mapping = $this->getRowMapping('RF');
					if(empty($acf_rf_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-acf-data-import.php'))
						break;
					global $acfHelper;
					$acfHelper->push_acf_data($data_to_import);
					break;
				case 'TYPES':
					$types_row_mapping = $this->getRowMapping('TYPES');
					if(empty($types_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-types-data-import.php'))
						break;
					global $typesHelper;
					$typesHelper->push_types_data($data_to_import,$mode);
					break;
				case 'PODS':
					$pods_row_mapping = $this->getRowMapping('PODS');
					if(empty($pods_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-pods-data-import.php'))
						break;
					global $podsHelper;
					$podsHelper->push_pods_data($data_to_import);
					break;
				case 'CCTM':
					$cctm_row_mapping = $this->getRowMapping('CCTM');
					if(empty($cctm_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-cctm-data-import.php'))
						break;
					global $cctmHelper;
					$cctmHelper->push_cctm_data($data_to_import);
					break;
				case 'AIOSEO':
					$aio_seo_row_mapping = $this->getRowMapping('AIOSEO');
					if(empty($aio_seo_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-aioseo-data-import.php'))
						break;
					global $aioseoHelper;
					$aioseoHelper->push_aioseo_data($data_to_import);
					break;
				case 'YOASTSEO':
					$yoast_row_mapping = $this->getRowMapping('YOASTSEO');
					if(empty($yoast_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-yoastseo-data-import.php'))
						break;
					global $yoastseoHelper;
					$yoastseoHelper->push_yoastseo_data($data_to_import);
					break;
				case 'CMB2':
					$cmb_row_mapping = $this->getRowMapping('CMB2');
					if(empty($cmb_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-cmb2-data-import.php')) {
						break;
					}
					global $cmb2Helper;
					$cmb2Helper->push_cmb2_data($data_to_import);
					break;
				case 'CFS':
					$cfs_row_mapping = $this->getRowMapping('CFS');
					if(empty($cfs_row_mapping) || !file_exists(SM_UCI_PRO_DIR . 'helpers/class-uci-cfs-data-import.php')) {
						break;
					}
					global $cfsHelper;
					$cfsHelper->push_cfs_data($data_to_import);
					break;
				case 'TERMS':
					$terms_row_mapping = $this->getRowMapping('TERMS');
					if(empty($terms_row_mapping))
						break;
					$this->importTermsAndTaxonomies($groupValue, $this->getLastImportId(), $importType);
					break;
				case 'WPMEMBERS':
					$helperObj = $event_information['instance'];
					$helperObj->importDataForUsers_WPMembers($groupValue, $this->getLastImportId());
					break;
				case 'MULTIROLE':
					$helperObj = $event_information['instance'];
					$helperObj->importDataForUsers_MembersMulitiRole($groupValue, $this->getLastImportId());
				break;
				case 'ULTIMATEMEMBER':
					$helperObj = $event_information['instance'];
                                        $helperObj->importDataForUsers_UltimateMember($groupValue, $this->getLastImportId());
                                        break;
				case 'BSI':
					$helperObj = $event_information['instance'];
					$helperObj->importDataForUsers_BillingShipping($groupValue, $this->getLastImportId());
					break;
			}
		}
	}

	/**
	 * Import core fields
	 *
	 * @param $data_array
	 * @param $importType
	 * @param $mode
	 * @param $eventKey
	 * @param $duplicateHandling
	 * @param $mediaConfig
	 * @param $importConfig
	 *
	 * @return array
	 */
	public function importDataForCoreFields ($data_array, $importType, $mode, $eventKey, $duplicateHandling, $mediaConfig, $importConfig){

		$returnArr = array();
		global $wpdb, $uci_admin;
		$mode_of_affect = 'Inserted';
		$assigned_author = '';
		#TODO: Check the mode & conditions based on import configuration values.
		if(!$data_array['post_format'])
		{
			if($data_array['post_format_option'])
				$data_array['post_format']=$data_array['post_format_option'];
		}

		$event_info = $uci_admin->getEventInformation();
		$helperObj = $event_info['instance'];
		// Import the core fields based on the import type
		if($importType != 'ticket') {
			switch ($importType) {
				case 'Users':
					$returnArr = $helperObj->importUserInformation($data_array, $mode, $eventKey, $duplicateHandling);
					break;
				case 'CustomerReviews':
					$returnArr = $helperObj->importDataForCustomerReviews($data_array, $mode, $eventKey, $duplicateHandling);
					break;
				case 'Tags':
				case 'Categories':
				case 'Taxonomies':
					$returnArr = $helperObj->importBulkTermsAndTaxonomies($data_array, $mode, $event_info['import_type'], $event_info['import_as'], $eventKey, $duplicateHandling);
					break;
				case 'Comments':
					$returnArr = $helperObj->importComments($data_array);
					break;
				case 'WooCommerce':
					$returnArr = $helperObj->importWooCommerceProducts($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'WooCommerceVariations':
					$returnArr = $helperObj->importWooCommerceVariations($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'WooCommerceOrders':
					$returnArr = $helperObj->importWooCommerceOrders($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'WooCommerceCoupons':
					$returnArr = $helperObj->importWooCommerceCoupons($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'WooCommerceRefunds':
					$returnArr = $helperObj->importWooCommerceRefunds($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'WPeCommerce':
					$returnArr = $helperObj->importWPCommerceProducts($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'WPeCommerceCoupons':
					$returnArr = $helperObj->importWPCommerceCoupons($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'MarketPress':
					$returnArr = $helperObj->importMarketPressProducts($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'MarketPressVariations':
					$returnArr = $helperObj->importDataForMarketPress_Variation($data_array, $importType, $mode, $eventKey, $duplicateHandling);
					break;
				case 'eShop':
					$returnArr = $helperObj->importeShopProducts($data_array,$importType,$mode,$eventKey, $duplicateHandling);
					break;
				default:
					$conditions = $duplicateHandling['conditions'];
					$duplicate_action = $duplicateHandling['action'];
					// Assign post type
					$data_array['post_type'] = $this->import_post_types( $importType, $this->event_information['import_as'] ); #TODO: Use getImportAs function
					if($duplicate_action == 'Update' || $duplicate_action == 'Skip'):
						$mode = 'Update';
					endif;
					$is_update = false;
					if($mode != 'Insert' && !empty($conditions) || $mode == 'Insert' && !empty($conditions)):
						if (in_array('ID', $conditions)) {
							$whereCondition = " ID = '{$data_array['ID']}'";
							$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
							$is_update = true;
						}  elseif (in_array('post_title', $conditions)) {
							$whereCondition = " post_title = \"{$data_array['post_title']}\"";
							$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
							$is_update = true;
						}elseif(in_array('post_name', $conditions)){
							$whereCondition = " post_name = \"{$data_array['post_name']}\"";
                            $duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
							$is_update = true;	
						}
					endif;
					if($mode == 'Schedule'){
						if($data_array['ID']){
							$whereCondition = " ID = '{$data_array['ID']}'";
							$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
                                                        $is_update = true;	
						}elseif($data_array['post_title']){
							$whereCondition = " post_title = \"{$data_array['post_title']}\"";
							$duplicate_check_query = "select ID from $wpdb->posts where ($whereCondition) and (post_type = '{$data_array['post_type']}') and (post_status != 'trash') order by ID DESC";
							$is_update = true;	
						}else if($data_array['PRODUCTSKU']){ // Mari added
							$duplicate_check_query = "select DISTINCT p.ID from $wpdb->posts p join $wpdb->postmeta pm on p.ID = pm.post_id where p.post_type = 'product' and p.post_status != 'trash' and pm.meta_value = '{$data_array['PRODUCTSKU']}'";
							$is_update = true;
						}
                                         }
					/* Post date options */
					if(!isset( $data_array['post_date'] )) {
						$data_array['post_date'] = current_time('Y-m-d H:i:s');
					} else {
						if(strtotime( $data_array['post_date'] )) {
							$data_array['post_date'] = date( 'Y-m-d H:i:s', strtotime( $data_array['post_date'] ) );
						} else {
							$data_array['post_date'] = current_time('Y-m-d H:i:s');
						}
					}

					/* Post author options */
					if(!isset($data_array['post_author'])) {
						$data_array['post_author'] = 1;
					} else {
						if(isset( $data_array['post_author'] )) {
							$user_records = $this->get_from_user_details( $data_array['post_author'] );
							$data_array['post_author'] = $user_records['user_id'];
							$assigned_author = $user_records['message'];
						}
					}

					/* Post Format Options */
					$post_format_array=array('post-format-aside','post-format-image','post-format-video','post-format-audio','post-format-quote','post-format-link','post-format-gallery','aside','image','video','audio','quote','link','gallery');
					if ( ! empty( $data_array['post_format'] ) ) {
						if ( ! is_numeric( $data_array['post_format'] ) ) {
							if(in_array(trim($data_array['post_format']),$post_format_array)){
								$post_format = $data_array['post_format'];
							}
							else
								unset($data_array['post_format']);

						} else {
							switch ( $data_array ['post_format'] ) {
								case 1 :
									$post_format = 'post-format-aside';
									break;
								case 2 :
									$post_format = 'post-format-image';
									break;
								case 3 :
									$post_format = 'post-format-video';
									break;
								case 4 :
									$post_format = 'post-format-audio';
									break;
								case 5 :
									$post_format = 'post-format-quote';
									break;
								case 6 :
									$post_format = 'post-format-link';
									break;
								case 7 :
									$post_format = 'post-format-gallery';
									break;
							}
						}
						$data_array['post_format'] = $post_format;
					}

					/* Post Status Options */
					if ( !empty($data_array['post_date']) ) {
						$data_array = $this->assign_post_status( $data_array );
					}

					// Assign post type

					// Import data with fast mode added by Fredrick Marks
					foreach (array('transition_post_status', 'save_post', 'pre_post_update', 'add_attachment', 'edit_attachment', 'edit_post', 'post_updated', 'wp_insert_post') as $act) {
						remove_all_actions($act);
					}
					// Initiate the action to insert / update the record
					if ($mode == 'Insert') {
						unset($data_array['ID']);
						$ID_result = $wpdb->get_results($duplicate_check_query);
                                                if (is_array($ID_result) && !empty($ID_result)) {
							$this->setSkippedRowCount($this->getSkippedRowCount() + 1);
							$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Duplicate found can not insert this";
							 return array('MODE' => $mode, 'ERROR_MSG' => 'Duplicate found can not insert this');
						}else{
							#Replacing post title instead of ID for post_parent added by sajitha
							if(!empty($data_array['post_parent'])){ //priya
							if(!intval($data_array['post_parent'])){
							$pquery = "select * from $wpdb->posts where post_title = '{$data_array['post_parent']}' and post_status != 'trash'";
						$post = $wpdb->get_results($pquery,ARRAY_A);

						$data_array['post_parent']=$post[0]['ID'];
					    }
					}
					$data_array['post_content']=htmlspecialchars_decode($data_array['post_content']);
					$retID = wp_insert_post($data_array); // Insert the core fields for the specific post type.
						}
						if(is_wp_error($retID) || $retID == '') {
							$this->setSkippedRowCount($this->getSkippedRowCount() + 1);
							if(is_wp_error($retID)) {
								$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this " . $data_array['post_type'] . ". " . $retID->get_error_message();
								return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
							}
							else {
								$uci_admin->detailed_log[ $uci_admin->processing_row_id ]['Message'] =  "Can't insert this " . $data_array['post_type'];
								return array( 'MODE' => $mode, 'ERROR_MSG' => "Can't insert this " . $data_array['post_type'] );
							}
							$this->setSkippedRowCount($this->getSkippedRowCount() + 1);

							#TODO Exception
						} else {
							// WPML support on post types
							global $sitepress;
							if($sitepress != null) {
								$this->UCI_WPML_Supported_Posts($data_array, $retID);
							}
							$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
                                                	$this->setInsertedRowCount($this->getInsertedRowCount() + 1);
                                                	$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted ' . $data_array['post_type'] . ' ID: ' . $retID . ', ' . $assigned_author;
						}
						//$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
						//$this->setInsertedRowCount($this->getInsertedRowCount() + 1);
						//$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted ' . $data_array['post_type'] . ' ID: ' . $retID . ', ' . $assigned_author;
					}
					else {
						if ( ($mode == 'Update' || $mode == 'Schedule') && $is_update == true ) {
						
							$ID_result = $wpdb->get_results($duplicate_check_query);
							
							if (is_array($ID_result) && !empty($ID_result)) {
								$retID = $ID_result[0]->ID;
								$data_array['ID'] = $retID;
 // for update image existing image, caption..
                                                                $pquery = $wpdb->get_results($wpdb->prepare("SELECT meta_value from wp_postmeta where  post_id = %d AND meta_key = %s",$retID,'_thumbnail_id'));

								$retmeta = $pquery[0]->meta_value;
								if (!empty($retmeta)) {
									
									$pdelte = $wpdb->get_results($wpdb->prepare("DELETE from wp_posts where ID =%s",$retmeta));

								}  
								wp_update_post($data_array);
								$mode_of_affect = 'Updated';
								$_SESSION[$eventKey]['summary']['updated'][] = $retID;
								$this->setUpdatedRowCount($this->getUpdatedRowCount() + 1);
								$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Updated ' . $data_array['post_type'] . ' ID: ' . $retID . ', ' . $assigned_author;
							} else {
								$retID = wp_insert_post($data_array);
								if(is_wp_error($retID) || $retID == '') {
									$this->setSkippedRowCount($this->getSkippedRowCount() + 1);
									$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = "Can't insert this " . $data_array['post_type'] . ". " . $retID->get_error_message();
									return array('MODE' => $mode, 'ERROR_MSG' => $retID->get_error_message());
									#TODO Exception
								}
								$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
								$this->setInsertedRowCount($this->getInsertedRowCount() + 1);
								$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted ' . $data_array['post_type'] . ' ID: ' . $retID . ', ' . $assigned_author;
							}
						} else {
							$retID = wp_insert_post($data_array);
							$_SESSION[$eventKey]['summary']['inserted'][] = $retID;
							$this->setInsertedRowCount($this->getInsertedRowCount() + 1);
							$uci_admin->detailed_log[$uci_admin->processing_row_id]['Message'] = 'Inserted ' . $data_array['post_type'] . ' ID: ' . $retID . ', ' . $assigned_author;
						}
					}

					if (!empty($post_format)) {
						wp_set_object_terms($retID, $post_format, 'post_format');
					}
					$media_handle = array();
					$shortcodes = '';
					$media_handle = isset($duplicateHandling['media_handling']) ? $duplicateHandling['media_handling'] : '';

					/* Page template */
					if($data_array['post_type'] == 'page') {
						if(isset($data_array['wp_page_template'])) {
							$page_template = $data_array['wp_page_template'];
						}
						else {
							$page_template = "";
						}
						update_post_meta($retID, '_wp_page_template', $page_template);
					}

					#TODO: Need to import the media for scheduler
					/* Set Featured Image */
					if(isset($data_array['featured_image'])) {
						if ( preg_match_all( '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $data_array['featured_image'], $matchedlist, PREG_PATTERN_ORDER ) ) {
							$nextGenInfo           = array();
							$media_settings = $this->parse_media_settings($mediaConfig, $data_array);
							$featured_image = explode('|', $data_array['featured_image']);
							$featured_image_info = array(
								'value'           => trim($featured_image[0]),
								'nextgen_gallery' => $nextGenInfo,
								'media_settings'  => $media_settings
							);
							update_option( 'smack_featured_' . $retID, $featured_image_info );
						}
					}
					// Media handling on the inline images
					if ( !empty($data_array['post_content']) ) {
						$shortcodes = $this->capture_shortcodes($data_array['post_content'], $retID, 'Inline', $media_handle);
						if(!empty($media_handle['download_img_tag_src']) && $media_handle['download_img_tag_src'] == 'on'){
							$this->convert_local_image_src($data_array['post_content'], $retID, $media_handle);
						}
						if(!empty($shortcodes)) {
							$this->convert_shortcode_to_image($shortcodes, $retID, 'Inline', $media_handle, $eventKey);
						}
					}
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Status'] = $data_array['post_status'];
					$returnArr['ID'] = $retID;
					$returnArr['MODE'] = $mode_of_affect;
					if (!empty($data_array['post_author'])) {
						$returnArr['AUTHOR'] = isset($assigned_author) ? $assigned_author : '';
					}
					break;
			}
		}//Import type Not a ticket

		if($importType == 'ticket') {
			$data_array['post_type'] = 'ticket';
			$retID = $data_array['ID'];
			$returnArr['ID'] = $retID;
			$returnArr['MODE'] = $mode_of_affect;
		}

		if(isset($data_array['post_type'])) {
			if($data_array['post_type'] == 'event' || $data_array['post_type'] == 'event-recurring' || $data_array['post_type'] == 'location' || $data_array['post_type'] == 'ticket'){
				require_once SM_UCI_PRO_DIR."helpers/class-uci-events-manager-data-import.php";
				new SmackUCIEventManagerDataImport($data_array, $importType, $retID , $mode);
			}
		}
		return $returnArr;
	}

	/**
	 * Import post & user meta information
	 *
	 * @param $data_array   - Data Array
	 * @param $pID          - Record ID
	 *
	 * @return array
	 */
	public function importDataForWPMetaFields ($data_array, $pID, $importType , $core_serialize_info) {
		global $wpdb;
		$createdFields = array();
		if(!empty($data_array)) {
                        foreach ($data_array as $custom_key => $custom_value) {
                                $createdFields[] = $custom_key;
                                if( $importType != 'Users'){
                                        //POSTMETA
                                        if( isset($core_serialize_info[$custom_key]) && $core_serialize_info[$custom_key] == 'on'){
                                                //Check entry in postmeta table
                                                $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}postmeta where post_id=%d and meta_key=%s" , $pID , $custom_key ), ARRAY_A);
                                                if( !empty($get_meta_info)){
                                                        $wpdb->update($wpdb->prefix.'postmeta' , array('meta_value' => $custom_value ) , array('meta_key' => $custom_key , 'post_id' => $pID ));
                                                }else{
                                                        $wpdb->insert($wpdb->prefix.'postmeta' , array('meta_key'=> $custom_key , 'meta_value' => $custom_value , 'post_id' => $pID ));
                                                }
                                        }else{
                                                update_post_meta($pID, $custom_key, $custom_value);
                                        }
                                }else{
                                        //USERMETA
                                        if( isset($core_serialize_info[$custom_key]) && $core_serialize_info[$custom_key] == 'on'){
                                                //Check entry in usermeta table
                                                $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id=%d and meta_key=%s" , $pID , $custom_key ), ARRAY_A);
                                                if( !empty($get_meta_info)){
                                                        $wpdb->update($wpdb->prefix.'usermeta' , array('meta_value' => $custom_value ) , array('meta_key' => $custom_key , 'user_id' => $pID ));
                                                }else{
                                                        $wpdb->insert($wpdb->prefix.'usermeta' , array('meta_key'=> $custom_key , 'meta_value' => $custom_value , 'user_id' => $pID ));
                                                }
                                        }else{
                                                update_user_meta($pID, $custom_key, $custom_value);
                                        }
                                }
                        }
                }
		return $createdFields;

	}

	/**
	 * Assign terms & taxonomies
	 *
	 * @param $categories       - Terms
	 * @param $category_name    - Taxonomy Name
	 * @param $pID              - Record ID
	 *
	 * @return array
	 */
	public function assignTermsAndTaxonomies($categories, $category_name, $pID) {
		$get_category_list = $category_list = array();
		// Create / Assign categories to the post types
		if (!empty($categories)) {
			foreach ( $categories as $cat_key => $cat_value ) {
				if (strpos($cat_value, '|') !== false) {
					$get_category_list = explode('|', $cat_value);
				} elseif (strpos($cat_value, ',') !== false) {
					$get_category_list = explode(',', $cat_value);
				} else {
					$get_category_list[] = $cat_value;
				}
			}
		}
		if(!empty($get_category_list)) {
			$i = 0;
			foreach($get_category_list as $key => $value) {
				if (strpos($value, '->') !== false) {
					$split_line = explode('->', $value);
					if(is_array($split_line)) {
						foreach($split_line as $category) {
							$category_list[$i][] = $category;
						}
					}
				} else {
					$category_list[$i][] = $value;
				}
				$i++;
			}
		}
		foreach($category_list as $index => $category_set) {
			foreach ( $category_set as $item => $category_value ) {
				$term_children_options= get_option( "$category_name" . "_children" );
				$parentTerm           = $item;
				$termName             = trim( $category_value );
				$_name                = (string) $termName;
				$_slug                = preg_replace( '/\s\s+/', '-', strtolower( $_name ) );
				$checkAvailable       = array();
				$checkSuperParent     = $checkParent1 = $checkParent2 = null;
				$super_parent_term_id = $parent_term_id1 = $parent_term_id2 = 0;
				if ( $parentTerm != 0 ) {
					if ( isset( $category_set[ $item - 1 ] ) ) {
						$checkParent1 = trim( $category_set[ $item - 1 ] );
						$checkParent1 = (string) $checkParent1;
						$parent_term  = term_exists( "$checkParent1", "$category_name" );
						if ( isset( $parent_term['term_id'] ) ) {
							$parent_term_id1 = $parent_term['term_id'];
						}
					}
					if ( isset( $category_set[ $item - 2 ] ) ) {
						$parent_term_id1   = 0;
						$checkSuperParent  = trim( $category_set[ $item - 2 ] );
						$checkSuperParent  = (string) $checkSuperParent;
						$super_parent_term = term_exists( "$checkSuperParent", "$category_name" );
						if ( isset( $super_parent_term['term_id'] ) ) {
							$super_parent_term_id = $super_parent_term['term_id'];
						}
						$checkParent2 = trim( $category_set[ $item - 1 ] );
						$checkParent2 = (string) $checkParent2;
						$parent_term  = term_exists( "$checkParent2", "$category_name", $super_parent_term_id );
						if ( isset( $parent_term['term_id'] ) ) {
							$parent_term_id2 = $parent_term['term_id'];
						}
					}
				}
				if ( $super_parent_term_id != 0 ) {
					if ( $parent_term_id2 == 0 ) {
						$checkAvailable = term_exists( "$checkParent2", "$category_name" );
						if ( ! is_array( $checkAvailable ) ) {
							$taxonomyID          = wp_insert_term( "$checkParent2", "$category_name", array(
								'description' => '',
								'slug'        => $_slug,
								'parent'      => $super_parent_term_id
							) );
							$parent_term_id2 = $retID = $taxonomyID['term_id'];
							wp_set_object_terms( $pID, $retID, $category_name, true );
						} else {
							$exist_term_id = array( $checkAvailable['term_id'] );
							$exist_term_id = array_map( 'intval', $exist_term_id );
							$exist_term_id = array_unique( $exist_term_id );
							$parent_term_id2 = $checkAvailable['term_id'];
							wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
						}
					}
					unset( $checkAvailable );
					$checkAvailable = term_exists( "$_name", "$category_name", $parent_term_id2 );
					if ( ! is_array( $checkAvailable ) ) {
						$taxonomyID = wp_insert_term( "$_name", "$category_name", array(
							'description' => '',
							'slug'        => $_slug,
							'parent'      => $parent_term_id2
						) );
						$retID  = $taxonomyID['term_id'];
						wp_set_object_terms( $pID, $retID, $category_name, true );
					} else {
						$exist_term_id = array( $checkAvailable['term_id'] );
						$exist_term_id = array_map( 'intval', $exist_term_id );
						$exist_term_id = array_unique( $exist_term_id );
						wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
					}
					unset( $checkAvailable );
				}
				elseif ( $parent_term_id1 != 0 ) {
					$checkAvailable = term_exists( "$_name", "$category_name", $parent_term_id1 );
					if ( ! is_array( $checkAvailable ) ) {
						$taxonomyID = wp_insert_term( "$_name", "$category_name", array(
							'description' => '',
							'slug'        => $_slug,
							'parent'      => $parent_term_id1
						) );
						$retID  = $taxonomyID['term_id'];
						wp_set_object_terms( $pID, $retID, $category_name, true );
					} else {
						$exist_term_id = array( $checkAvailable['term_id'] );
						$exist_term_id = array_map( 'intval', $exist_term_id );
						$exist_term_id = array_unique( $exist_term_id );
						wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
					}
					unset( $checkAvailable );
				}
				elseif ( $super_parent_term_id == 0 && $parent_term_id2 == 0 && $parent_term_id1 == 0 ) {
					$checkAvailable = term_exists( "$_name", "$category_name" );
					if ( !is_array( $checkAvailable ) ) {
						$taxonomyID = wp_insert_term( "$_name", "$category_name", array(
							'description' => '',
							'slug'        => $_slug,
						) );
						$retID  = $taxonomyID['term_id'];
						wp_set_object_terms( $pID, $retID, $category_name, true );
					} else {
						$exist_term_id = array( $checkAvailable['term_id'] );
						$exist_term_id = array_map( 'intval', $exist_term_id );
						$exist_term_id = array_unique( $exist_term_id );
						wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
					}
					unset( $checkAvailable );
				}
				#if ( ! is_wp_error( $retID ) ) {
				update_option( "$category_name" . "_children", $term_children_options );
				delete_option( $category_name . "_children" );
				#}
				$categoryData[] = (string) $category_value;
			}
		}

		return $categoryData;
	}

	/**
	 * Import terms & taxonomies
	 *
	 * @param $data_array   - Data Array
	 * @param $pID          - Record ID
	 * @param $type         - Module
	 */
	public function importTermsAndTaxonomies ($data_array, $pID, $type) {
		global $uci_admin;
		unset($data_array['post_format']);
		unset($data_array['product_type']);
		$categories = $tags = array();
		foreach ($data_array as $termKey => $termVal) {
			$smack_taxonomy = array();
			switch ($termKey) {
				case 'post_category' :
					$categories [$termKey] = $data_array [$termKey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Categories'] = $data_array[$termKey];
					$category_name = 'category';
					// Create / Assign categories to the post types
					if(isset($categories[$termKey]) && $categories[$termKey] != '')
						$this->assignTermsAndTaxonomies($categories, $category_name, $pID);
					//Get Default Category id
					$default_category_id = get_option('default_category');
					//Get Default Category Name
					$default_category_details = get_term_by('id', $default_category_id , 'category');
					//Remove Default Category
					$categories = wp_get_object_terms($pID, 'category');
					if (count($categories) > 1) {
						foreach ($categories as $key => $category) {
							if ($category->name == $default_category_details->name ) {
								wp_remove_object_terms($pID, $default_category_details->name , 'category');
							}
						}
					}
					break;
				case 'post_tag' :
					$tags [$termKey] = $data_array [$termKey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Tags'] = $data_array[$termKey];
					$tag_name = 'post_tag';
					break;
				case 'product_tag':
					$tags [$termKey] = $data_array [$termKey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Tags'] = $data_array[$termKey];
					$tag_name = 'product_tag';
					break;
				case 'product_category':
					if($type === 'MarketPress')
						$category_name = 'product_category';
					if($type == 'WooCommerce')
						$category_name = 'product_cat';
					if($type == 'WPeCommerce')
						$category_name = 'wpsc_product_category';
					$categories [$termKey] = $data_array [$termKey];
					$uci_admin->detailed_log[$uci_admin->processing_row_id]['Categories'] = $data_array[$termKey];
					// Create / Assign categories to the post types
					if(isset($categories[$termKey]) && $categories[$termKey] != '')
						$this->assignTermsAndTaxonomies($categories, $category_name, $pID);
					break;
				case 'event_tags':
					$eventtags [$termKey] = $data_array [$termKey];
					if(!empty($eventtags)){
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Tags'] = $data_array[$termKey];
						foreach($eventtags as $e_key => $e_value){
							if(!empty($e_value)){
								if (strpos($e_value, '|') !== false) {
									$split_etag = explode('|', $e_value);
								} elseif (strpos($e_value, ',') !== false) {
									$split_etag = explode(',', $e_value);
								} else {
									$split_etag = $e_value;
								}
								if(is_array($split_etag)) {
									foreach($split_etag as $item) {
										$etagData[] = (string)$item;
									}
								} else {
									$etagData = (string)$split_etag;
								}
								wp_set_object_terms($pID, $etagData,'event-tags');
							}
						}
					}
					break;
				case 'event_categories':
					$event_categories [$termKey] = $data_array [$termKey];
					if(!empty($event_categories)) {
						$uci_admin->detailed_log[$uci_admin->processing_row_id]['Categories'] = $data_array[$termKey];
						foreach($event_categories as $ec_key => $ec_value){
							if(!empty($ec_value)) {
								if (strpos($ec_value, '|') !== false) {
									$split_ecat = explode('|', $ec_value);
								} elseif (strpos($ec_value, ',') !== false) {
									$split_ecat = explode(',', $ec_value);
								} else {
									$split_ecat = $ec_value;
								}
								if(is_array($split_ecat)) {
									foreach($split_ecat as $item) {
										$ecatData[] = (string)$item;
									}
								} else {
									$ecatData = (string)$split_ecat;
								}
								wp_set_object_terms($pID, $ecatData,'event-categories');
							}
						}
					}
					break;
				default :
					$smack_taxonomy[$termKey] = $data_array[$termKey];

					if($termKey != 'post_format')
						$uci_admin->detailed_log[$uci_admin->processing_row_id][$termKey] = $data_array[$termKey];

					$taxonomy_name = $termKey;

					// Create / Assign taxonomies to the post types
					if(isset($smack_taxonomy[$termKey]) && $smack_taxonomy[$termKey] != '')
						$this->assignTermsAndTaxonomies($smack_taxonomy, $taxonomy_name, $pID);
					break;
			}
		}

		// Create / Assign tags to the post types
		if (!empty ($tags)) {
			foreach ($tags as $tag_key => $tag_value) {
				if (!empty($tag_value)) {
					if (strpos($tag_value, '|') !== false) {
						$split_tag = explode('|', $tag_value);
					} elseif (strpos($tag_value, ',') !== false) {
						$split_tag = explode(',', $tag_value);
					} else {
						$split_tag = $tag_value;
					}
					if(is_array($split_tag)) {
						foreach($split_tag as $item) {
							$tag_list[] = $item;
						}
					} else {
						$tag_list = $split_tag;
					}
					wp_set_object_terms($pID, $tag_list, $tag_name);
				}
			}
		}
	}

	/**
	 * Generate data array based on available groups
	 *
	 * @param null $available_groups_type
	 * @param null $mapping_records
	 * @param null $data_rows
	 * @param $method
	 *
	 * @return array
	 * @throws Exception
	 */
	public function generateDataArrayBasedOnGroups($available_groups_type = null, $mapping_records = null, $data_rows = null, $method = 'advanced'){
		//new mapped array
		$import_dataArr = $current_mapped_check_serialize = array();
		if(!empty($available_groups_type)) {
			if($method == 'normal' || $method == ''):
				foreach ($available_groups_type as $groupname => $groupvalue) {
					if(!empty($mapping_records)) {
						foreach ( $mapping_records as $mapping_key => $mapping_value ) {
							$current_mapped_group_mapkey = explode( $groupvalue . '__mapping', $mapping_key );
							$current_mapped_group_key    = explode( $groupvalue . '__fieldname', $mapping_key );
							//Serialize check
							if( $groupvalue == 'CORECUSTFIELDS' ){
								$current_mapped_check_serialize = explode( $groupvalue . '__SerializeVal', $mapping_key );
							}
							$current_static_group_key    = explode( $groupvalue . '_statictext_mapping', $mapping_key );
							$current_formula_group_key   = explode( $groupvalue . '_formulatext_mapping', $mapping_key );
							if ( is_array( $current_mapped_group_mapkey ) && count( $current_mapped_group_mapkey ) == 2 ) {
								$set_mapping_groups[ $groupvalue ][] = $mapping_value;
							}
							if ( is_array( $current_mapped_group_key ) && count( $current_mapped_group_key ) == 2 ) {
								$set_fields_group[ $groupvalue ][] = $mapping_value;
								$current_row_val                   = $mapping_value;
							}
							//serialize
							if ( is_array( $current_mapped_check_serialize ) && count( $current_mapped_check_serialize ) == 2 ) {
								$serialize_index = substr($mapping_key, strpos($mapping_key, "SerializeVal") + 12);
								$set_fields_serialize[ 'SerializeVal' ][$serialize_index] = $mapping_value;
								$current_row_val                   = $mapping_value;
							}
							//static and formula features
							if ( is_array( $current_static_group_key ) && count( $current_static_group_key ) == 2 ) {
								$set_static_group[ $groupvalue ][ $current_row_val ] = $mapping_value;
							}
							if ( is_array( $current_formula_group_key ) && count( $current_formula_group_key ) == 2 ) {
								$set_formula_group[ $groupvalue ][ $current_row_val ] = $mapping_value;
							}

						}
					}
					if (!empty($set_fields_group[$groupvalue]) && !empty($set_mapping_groups[$groupvalue])) {
						$new_mapped_array[$groupvalue] = array_combine($set_fields_group[$groupvalue], $set_mapping_groups[$groupvalue]);
					}
					//static and formula features
					if(!empty($set_static_group[$groupvalue] )) {
						foreach ($set_static_group[$groupvalue] as $grp => $val) {
							if (!empty($new_mapped_array[$groupvalue]) && array_key_exists($grp, $new_mapped_array[$groupvalue])) {
								$new_mapped_array[$groupvalue][$grp] = $val;
							}
						}
					}
					if(!empty($set_formula_group[$groupvalue] )) {
						foreach ($set_formula_group[$groupvalue] as $grp => $val) {
							if (!empty($new_mapped_array[$groupvalue]) && array_key_exists($grp, $new_mapped_array[$groupvalue])) {
								$new_mapped_array[$groupvalue][$grp] = $val;
							}
						}
					}
					//serialized
					if(!empty($set_fields_serialize['SerializeVal'] )) {
						foreach ($set_fields_serialize['SerializeVal'] as $grp => $val) {
							$new_mapped_array['SerializeVal'][$set_fields_group['CORECUSTFIELDS'][$grp]] = $val;
						}
						$available_groups_type['SerializeVal'] = 'SerializeVal';
					}
				}
			elseif($method == 'advanced'):
				foreach ($available_groups_type as $groupname => $groupvalue) {
					if(!empty($mapping_records)) {
						foreach ( $mapping_records as $mapping_key => $mapping_value ) {
							$current_mapping_field = explode($groupvalue . '__', $mapping_key);
							if (is_array($current_mapping_field) && count($current_mapping_field) == 2) {
								$new_mapped_array[ $groupvalue ][ $current_mapping_field[1] ] = $mapping_value;
								$set_static_group[ $groupvalue ][ $current_mapping_field[1] ] = $mapping_value;
							}
						}
					}
				}
			endif;
			//Empty rows checking
			foreach ($available_groups_type as $groupname => $groupvalue) {
				if (!empty($new_mapped_array[$groupvalue])) {
					foreach ($new_mapped_array[$groupvalue] as $mpkey => $mpval) {
						if ($mpval != '-- Select --' && $mpval != 'select') {
							if(isset($set_static_group[$groupvalue]) && in_array($mpval, $set_static_group[$groupvalue])) {
								$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
								preg_match_all($pattern, $mpval, $results, PREG_PATTERN_ORDER);
								for($i=0; $i<count($results[2]); $i++) {
									$oldWord = $results[0][$i];
									$get_val = $results[2][$i];
									//TODO xml
									if(isset($data_rows[$get_val])) {
										$newWord = $data_rows[$get_val];
									} else {
										$newWord = $get_val;
									}
									$mpval = str_replace($oldWord, ' ' . $newWord, $mpval);
								}
								//$mpval = str_replace('+',' ', $mpval);
								if(trim($mpval) != ''){
									// $evaluation = $this->evalmath($mpval);
									// if($evaluation == 'false')
									// 	$evaluation = str_replace('+',' ', $mpval);
									// commented above code for  nested category import 
									$import_dataArr[$groupvalue][$mpkey] = $mpval;
								}
							} elseif(isset($set_formula_group[$groupvalue]) && in_array($mpval, $set_formula_group[$groupvalue])) {
								$pattern = "/({([\w]+)(.*?)(}))/";
								preg_match_all($pattern, $mpval, $results, PREG_PATTERN_ORDER);
								for($i=0; $i<count($results[2]); $i++) {
									$oldWord = $results[0][$i];
									$get_val = $results[2][$i];
									//TODO xml
									if(isset($data_rows[$get_val])) {
										$newWord = $data_rows[$get_val];
									} else {
										$newWord = $get_val;
									}
									$mpval = str_replace($oldWord, $newWord, $mpval);
								}
								if(!empty($mpval) && $mpval != '' && $mpval != null) {
									$result = $this->evalmath($mpval);
								} else {
									$result = 0;
								}
								if($result == "false"){
									$result = $mpval;
								}
								if($result != 0)
									$import_dataArr[$groupvalue][$mpkey] = $result;
							} else {
								if ($mpval == 'publish') {
									$result = 'publish';
								}else{
									if($groupvalue == 'SerializeVal'){
										$import_dataArr['SerializeVal'][$mpkey] = $mpval;
										$result = '';
									}else{
										if (!empty($data_rows) && array_key_exists($mpval, $data_rows)) {
											$result = $data_rows[$mpval];
										} else {
											$result = '';
										}
									}
								}
								if($result != '')
									$import_dataArr[$groupvalue][$mpkey] = $result;
							}

						}
					}
				}
			}
		}
		return $import_dataArr;
	}

	public function saveAdvancedTemplate($uci_admin, $templateName = null) {
		global $wpdb;
		$eventkey = isset($_REQUEST['eventkey']) ? sanitize_title($_REQUEST['eventkey']) : '';
		$post_values = $uci_admin->GetPostValues($eventkey);
		if(isset($post_values[$eventkey]['mapping_config']['xml_tag_name']))
		$xmltag = $post_values[$eventkey]['mapping_config']['xml_tag_name'];
		else
		$xmltag = "";
		$filename = $post_values[$eventkey]['import_file']['uploaded_name'];
		$module = $post_values[$eventkey]['import_file']['posttype'];
		if($templateName == null)
			$templateName = $post_values[$eventkey]['mapping_config']['templatename'];
		$templateId = isset($_REQUEST['templateid']) ? intval($_REQUEST['templateid']) : null;
		$time = date('Y-m-d h:i:s');
		$importAs = '';
		$available_group = $uci_admin->available_widgets($module, $importAs);
		foreach ($available_group as $groupname => $groupvalue) {
			foreach ( $post_values[ $eventkey ]['mapping_config'] as $mapping_key => $mapping_value ) {
				$current_mapping_field = explode($groupvalue . '__', $mapping_key);
				if (is_array($current_mapping_field) && count($current_mapping_field) == 2) {
					$current_mapping_by_groups[$groupvalue][$current_mapping_field[1]] = $mapping_value;
				}
			}
		}
		$current_mapping_by_groups['XMLTAGNAME'] = $xmltag;
		$mapped_array = maybe_serialize($current_mapping_by_groups);
		if ( $templateName != '' & $templateId == null ) {
			$wpdb->insert( 'wp_ultimate_csv_importer_mappingtemplate', array(
				'templatename' => $templateName,
				'mapping'      => $mapped_array,
				'createdtime'  => $time,
				'mapping_type' => $post_values[$eventkey]['mapping_config']['smack_uci_mapping_method'],
				'module'       => $module,
				'csvname'      => $filename,
				'eventKey'     => $eventkey
			), array( '%s', '%s', '%s', '%s', '%s', '%s' ) );
		} elseif ( $templateName != '' && $templateId != null ) {
			$wpdb->update( 'wp_ultimate_csv_importer_mappingtemplate', array(
				'templatename' => $templateName,
				'mapping'      => $mapped_array,
				'createdtime'  => $time,
				'mapping_type' => $post_values[$eventkey]['mapping_config']['smack_uci_mapping_method'],
				'module'       => $module,
				'csvname'      => $filename,
				'eventKey'     => $eventkey
			), array( 'id' => $templateId ), array( '%s', '%s', '%s', '%s', '%s', '%s' ), array( '%d' ) );
		}
	}

	/**
	 * Save template information
	 *
	 * @param $uci_admin       - Global Variable
	 * @param $templateName    - Template Name
	 */
	public function saveTemplate ($uci_admin, $templateName = null) {
		global $wpdb;
		$mapped_array = $new_mapped_array = array();
		$eventkey = isset($_REQUEST['eventkey']) ? sanitize_title($_REQUEST['eventkey']) : '';
		$post_values = $uci_admin->GetPostValues($eventkey);
		$filename = $post_values[$eventkey]['import_file']['uploaded_name'];
		$module = $post_values[$eventkey]['import_file']['posttype'];
		if($templateName == null)
			$templateName = $post_values[$eventkey]['mapping_config']['templatename'];
		$templateId = isset($_REQUEST['templateid']) ? intval($_REQUEST['templateid']) : null;
		$time = date('Y-m-d h:i:s');
		$importAs = '';
		$available_group = $uci_admin->available_widgets($module, $importAs);
		foreach ($available_group as $groupname => $groupvalue) {
			foreach ($post_values[$eventkey]['mapping_config'] as $mapping_key => $mapping_value) {
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
					if (!empty($new_mapped_array) && array_key_exists($grp, $new_mapped_array[$groupvalue])) {
						$new_mapped_array[$groupvalue][$grp] = $val;
					}
				}
			}
			if(!empty($set_formula_group[$groupvalue] )) {
				foreach ($set_formula_group[$groupvalue] as $grp => $val) {
					if (!empty($new_mapped_array) && array_key_exists($grp, $new_mapped_array[$groupvalue])) {
						$new_mapped_array[$groupvalue][$grp] = $val;
					}
				}
			}
		}
		$mapped_array = maybe_serialize($new_mapped_array);
		if ( $templateName != '' & $templateId == null ) {
			$wpdb->insert( 'wp_ultimate_csv_importer_mappingtemplate', array(
				'templatename' => $templateName,
				'mapping'      => $mapped_array,
				'createdtime'  => $time,
				'mapping_type' => $post_values[$eventkey]['mapping_config']['smack_uci_mapping_method'],
				'module'       => $module,
				'csvname'      => $filename,
				'eventKey'     => $eventkey
			), array( '%s', '%s', '%s', '%s', '%s', '%s' ) );
		} elseif ( $templateName != '' && $templateId != null ) {
			$wpdb->update( 'wp_ultimate_csv_importer_mappingtemplate', array(
				'templatename' => $templateName,
				'mapping'      => $mapped_array,
				'createdtime'  => $time,
				'mapping_type' => $post_values[$eventkey]['mapping_config']['smack_uci_mapping_method'],
				'module'       => $module,
				'csvname'      => $filename,
				'eventKey'     => $eventkey
			), array( 'id' => $templateId ), array( '%s', '%s', '%s', '%s', '%s', '%s' ), array( '%d' ) );
		}
	}

	/**
	 * @param $module
	 * @param $post_values
	 *
	 * @return mixed
	 */
	public function get_mapping_screendata($module,$post_values){
		global $uci_admin;
		$available_group = $uci_admin->available_widgets($module, $importAs);
		foreach ($available_group as $groupname => $groupvalue) {
			foreach ($post_values as $mapping_key => $mapping_value) {
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
					if (!empty($new_mapped_array) && array_key_exists($grp, $new_mapped_array[$groupvalue])) {
						$new_mapped_array[$groupvalue][$grp] = $val;
					}
				}
			}
			if(!empty($set_formula_group[$groupvalue] )) {
				foreach ($set_formula_group[$groupvalue] as $grp => $val) {
					if (!empty($new_mapped_array) && array_key_exists($grp, $new_mapped_array[$groupvalue])) {
						$new_mapped_array[$groupvalue][$grp] = $val;
					}
				}
			}
		}
		return $new_mapped_array;
	}
	/**
	 * Get real name of the uploaded file
	 *
	 * @param $filename - File Name
	 * @param $version  - Version
	 * @return string
	 */
	public function get_realname($filename,$version) {
		$extension = explode('.',$filename);
		$extension = $extension[count($extension) - 1];
		$filename = explode('-'.$version,$filename);
		return $filename[0] . '.' . $extension;
	}

	/**
	 * Set priority
	 *
	 * @param null $filename    - File Name
	 * @param null $eventkey    - Event Key
	 * @param null $uci_admin   - Global Variable
	 * @param array $headers    - Headers
	 *
	 * @return array
	 */
	public function setPriority($filename = null, $eventkey = null, $uci_admin = null, $headers = array()) {
		global $wpdb;
		$template_data = array();
		$mappingList = array();
		$eventkey = isset($_REQUEST['eventkey']) ? sanitize_key($_REQUEST['eventkey']) : $eventkey;
		$filepath = SM_UCI_IMPORT_DIR . '/' . $eventkey . '/' . $eventkey;
		$get_FileList = $wpdb->get_results($wpdb->prepare("select templatename, mapping from wp_ultimate_csv_importer_mappingtemplate where csvname = %s", $filename));
		if(empty($headers)) {
			$parserObj = new SmackCSVParser();
			$parserObj->parseCSV( $filepath, 0, - 1 );
			$headers = $parserObj->get_CSVheaders();
			$headers = $headers[0];
		}
		$merge_array = array();
		if (!empty($get_FileList)) {
			foreach ($get_FileList as $filekey => $filevalue) {
				$mappingList[$filevalue->templatename] = maybe_unserialize($filevalue->mapping);
			}
			foreach ($mappingList as $templatename => $group) {
				foreach ($group as $mapped_array) {
					if(is_array($mapped_array))
						$merge_array = array_merge($merge_array, $mapped_array);
				}
				$commonHeaders[$templatename] = count(array_intersect($headers, $merge_array));
			}
			if (!empty($commonHeaders)) {
				arsort($commonHeaders);
				$count = count($commonHeaders);
				if ($count > 6) {
					$template_data = array_slice($commonHeaders, 0, 6, true);
				} else {
					$template_data = array_slice($commonHeaders, 0, $count, true);
				}
				return $template_data;
			} else {
				return $template_data;
			}
		} else {
			return $template_data;
		}
	}

	/**
	 * Filter template
	 *
	 * @param $filterdata   - Filtered Data
	 *
	 * @return string
	 */
	public function filter_template($filterdata) {
		$startDate = $filterdata['from-date'];
		$endDate = $filterdata['to-date'];
		$templateName = $filterdata['search'];
		$filterclause = '';
		if (!empty($startDate) && !empty($endDate)) {
			$filterclause .= "createdtime >= '$startDate' and createdtime <= '$endDate' and";
		} else {
			if (!empty($startDate)) {
				$filterclause .= "createdtime >= '$startDate' and";
			} else {
				if (!empty($endDate)) {
					$filterclause .= "createdtime <= '$endDate' and";
				}
			}
		}
		if (!empty($templateName)) {
			$filterclause .= " templatename like '%$templateName%' and";
		}

		if (!empty($filterclause)) {
			$filterclause = "where $filterclause";
			$filterclause = substr($filterclause, 0, -3);
		}

		return $filterclause;
	}

	/**
	 * Get user information
	 *
	 * @param $request_user - User Name (or) User ID
	 *
	 * @return mixed
	 */
	public function get_from_user_details($request_user) {
		global $wpdb;
		$authorLen = strlen($request_user);
		$checkpostuserid = intval($request_user);
		$postAuthorLen = strlen($checkpostuserid);

		if ($authorLen == $postAuthorLen) {
			$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where ID = %s", $request_user));
			if (empty($postauthor) || !$postauthor[0]->ID) { // If user name are numeric Ex: 1300001
				$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where user_login = \"{%s}\"",$request_user));
			}
		} else {
			$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where user_login = %s", $request_user));
		}
		if (empty($postauthor) || !$postauthor[0]->ID) {
			$request_user = 1;
			$admindet = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where ID = %d", 1));
			$message = " , <b>Author :- </b> not found (assigned to <b>" . $admindet[0]->user_login . "</b>)";
		} else {
			$request_user = $postauthor[0]->ID;
			$admindet = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where ID = %s", $request_user));
			$message = " , <b>Author :- </b>" . $admindet[0]->user_login;
		}
		$userDetails['user_id'] = $request_user;
		$userDetails['user_login'] = $admindet[0]->user_login;
		$userDetails['message'] = $message;
		return $userDetails;
	}

	/**
	 * Function to get roles for users
	 * @param bool $capability  - Capability
	 *
	 * @return array
	 */
	public function getRoles($capability = null) {
		global $wp_roles;
		$roles = array();
		if($capability != null) {
			foreach ( $wp_roles->roles as $rkey => $rval ) {
				$roles[ $rkey ] = '';
				for ( $cnt = 0; $cnt < count( $rval['capabilities'] ); $cnt ++ ) {
					$findval = "level_" . $cnt;
					if ( array_key_exists( $findval, $rval['capabilities'] ) ) {
						$roles[ $rkey ] = $roles[ $rkey ] . $cnt . ',';
					}
				}
			}
		} else {
			if ( ! isset( $wp_roles ) )
				$wp_roles = new WP_Roles();

			$roles = $wp_roles->get_names();
		}
		return $roles;
	}

	/**
	 * Function to get the google_map address
	 *
	 * @param null $address - Address of the Location
	 * @param null $region  - Region
	 *
	 * @return bool|string
	 */
	public function get_latitude_longitude($address = null, $region = null) {
		$address = str_replace(" ", "+", $address);
		$json = @file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
		$json = json_decode($json);
		if(!empty($json->results)) {
			$lat  = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
			return $lat . ',' . $long;
		} else {
			return false;
		}
	}

	/**
	 * Get requested term details
	 *
	 * @param $post_id  - Record ID
	 * @param $term     - Term Name
	 *
	 * @return mixed
	 */
	public function get_requested_term_details ($post_id, $term) {
		$termLen = strlen($term);
		$checktermid = intval($term);
		$verifiedTermLen = strlen($checktermid);
		if($termLen == $verifiedTermLen) {
			return $term;
		} else {
			$reg_term_id = wp_set_object_terms($post_id, $term, 'category');
			$term_id = $reg_term_id[0];
			return $term_id;
		}
	}

	/**
	 * Assign post status
	 *
	 * @param $data_array   - Data Array
	 *
	 * @return mixed
	 */
	public function assign_post_status($data_array) {
		if (isset($data_array['is_post_status']) && $data_array['is_post_status'] != 'on') {
			$data_array ['post_status'] = $data_array['is_post_status'];
			unset($data_array['is_post_status']);
		}
		if (isset($data_array ['post_type']) && $data_array ['post_type'] == 'page') {
			$data_array ['post_status'] = 'publish';
		} else {
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
				else {
				}
			} else {
				$data_array['post_status'] = 'publish';
			}
		}
		return $data_array;
	}

	/**
	 * Push event logs into Database
	 *
	 * @param $manage_records   - Records
	 * @param $fileInfo         - File Information
	 * @param $eventKey         - Event Key
	 * @param $type_of_import   - Type of Import
	 * @param $mode             - Mode
	 * @param $eventInfo        - Event Information
	 *
	 * @return int
	 */
	public function manage_records($manage_records, $fileInfo, $eventKey, $type_of_import, $mode, $eventInfo){
		global $wpdb;
		$uploadpath = SM_UCI_IMPORT_DIR;
		$imported_on = date('Y-m-d h:i:s');
		$file_name = $fileInfo['file_name'];
		$version = $fileInfo['revision'];
		$month = date("M", strtotime($imported_on));
		$year = date("Y", strtotime($imported_on));
		$versionFile = '/smack_uci_uploads/imports/' . $eventKey . '/' . $eventKey; //.'.txt';
		$module = $this->import_post_types($type_of_import, '');
		$dbrecords = $wpdb->get_results($wpdb->prepare("select *from smackuci_events where eventKey = %s", $eventKey ));
		if(count($dbrecords) == 0){
			$serialized_version = serialize($versionFile);
			$serialized_records = serialize($manage_records);
			$wpdb->insert('smackuci_events', array(
				'revision' => $fileInfo['revision'],
				'name' => "{$fileInfo['file_name']}",
				'original_file_name' => "{$fileInfo['original_file_name']}",
				'import_type' => "{$type_of_import}",
				'filetype' => "{$fileInfo['file_type']}",
				'filepath' => "{$versionFile}",
				'eventKey' => "{$eventKey}",
				'registered_on' => $imported_on,
				'processing' => 1,
				'count' => $eventInfo['count'],
				'processed' => $eventInfo['processed'],
				'created' => $eventInfo['inserted'],
				'updated' => $eventInfo['updated'],
				'skipped' => $eventInfo['skipped'],
				'last_activity' => $imported_on,
				'month' => $month,
				'year' => $year
			),
				array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%s','%s','%s')
			);
			$id = $wpdb->insert_id;
		} else {
			$id = $dbrecords[0]->id;
			$details = array(
				'processed' => $eventInfo['processed'],
				'created' => $eventInfo['inserted'],
				'updated' => $eventInfo['updated'],
				'skipped' => $eventInfo['skipped']
			);
			$condition = array('id' => $id);
			$wpdb->update('smackuci_events', $details, $condition, array('%d', '%d', '%d', '%d'), array('%d'));
		}
		return $id;
	}

	/**
	 * Time taken & Elapsed time on the time of import
	 *
	 * @return array
	 */
	public function serverReq_data(){
		$record_arr = array();
		$split_range = array();
		$initial_recordcount = 1000;
		$minimum_record = 0;
		$filesize = 8000;
		$tot_records = 10;
		$executiontime = ini_get('max_execution_time');
		$memorysize = ini_get('memory_limit');
		if($tot_records < $initial_recordcount) {
			$current_processtime = ($tot_records / 20) * 60;
		}
		else {
			$current_processtime = (($tot_records / 1000) * 5) * 60;
		}
		if($executiontime < $current_processtime){
			$time_in_minute = round($executiontime / 60 , 3);
			if($executiontime > 300) {
				$server_req = 1000 * ($time_in_minute / 5); // 1000 record takes 5 minutes i.e 300s
			}
			else {
				$server_req = 150 * $time_in_minute; // 150 record takes 1 minute.
			}
			$server_req = $server_req / 2;
		}
		else {
			$server_req = $tot_records / 2;
		}
		if($server_req != 0){
			// convert to neareast 10.
			$quotient_val = (int)$server_req / 10;
			$remainder_val = (int)$server_req % 10;
			if($remainder_val != 0){
				$server_req = (intval($quotient_val) + 1) * 10;
			}
			// Split the range
			$count = 0;
			for($i = $server_req; $count < 10; $i++) {
				if($i > 20) {
					$split_range[] = $i - 20;
					$i -= 20;
				}
				else {
					$split_range[] = $i;
					break;
				}
				$count++;
			}
		}
		return $split_range;
	}

	/**
	 * Convert slug
	 *
	 * @param null $Name    - Slug Name
	 *
	 * @return mixed|string
	 */
	public function convert_slug($Name =null){
		$label = trim($Name);
		$slug = strtolower($label);
		$slug = preg_replace("/[^a-zA-Z0-9._\s]/", "", $slug);
		$slug = preg_replace('/\s/', '-', $slug);
		return $slug;

	}

	/**
	 * Capture short codes
	 *
	 * @param $post_content     - Post Content
	 * @param $pID              - Record ID
	 * @param $mode             - Mode of Import
	 * @param $media_handle     - Media settings
	 *
	 * @return array
	 */
	public function capture_shortcodes($post_content,$pID,$mode,$media_handle){
		global $wpdb;
		if($mode == 'Inline') {
			$pattern = "/([WPIMPINLINE:([\w]+)(.*?)(])/";
			$shortcode_prefix = "[WPIMPINLINE:";
		} else if($mode == 'Featured') {
			$pattern = "/([WPIMPFEATURED:([\w]+)(.*?)(])/";
			$shortcode_prefix = "[WPIMPFEATURED:";
		}
		$post_content = str_replace("\n", "<br />", $post_content);
		preg_match_all($pattern, $post_content, $results, PREG_PATTERN_ORDER);
		$inlineimg_shortcodes = array();
		$shortcodelist = array();
		$inline_shortcode_count = 0;
		if($mode == 'Inline') {
			for($i=0; $i<count($results[0]); $i++){
				$get_shortcode_pos = strpos($results[0][$i], $shortcode_prefix);
				$inlineimg_shortcodes[] = substr($results[0][$i], $get_shortcode_pos);
			}
		} elseif($mode == 'Featured') {
			$inline_shortcode_count = count($results[0]);
			if($inline_shortcode_count == 1) {
				$inlineimg_shortcodes[] = $results[0][0];
			}
		}
		if(!empty($inlineimg_shortcodes)) {
			foreach($inlineimg_shortcodes as $shortkey => $shortcode) {
				$shortcodelist[$pID][$shortkey] = $shortcode;
			}
		}
		return $shortcodelist;

	}

	/**
	 * Covert short code to image
	 *
	 * @param $shortcodelist    - Shortcode List
	 * @param $postID           - Record ID
	 * @param $shortcode_mode   - Mode of Shortcode
	 * @param $media_handle     - Media Settings
	 * @param $eventkey         - Event Key
	 */
	public function convert_shortcode_to_image($shortcodelist,$postID,$shortcode_mode,$media_handle,$eventkey){
		global $wpdb;
		/* Image available in media */
		$useexistingimages = 'false';
		if(!empty($media_handle['imageprocess']) && $media_handle['imageprocess'] == 'duplicateimageoption'){
			$useexistingimages = 'true';
		}
		if(is_array($shortcodelist) && !empty($shortcodelist)) {
			foreach($shortcodelist as $postID => $shortcodes) {
				$get_post_content = $wpdb->get_results($wpdb->prepare("select post_content from $wpdb->posts where ID = %d", $postID));
				$post_content = $get_post_content[0]->post_content;
				foreach($shortcodes as $shortcode) {
					if($shortcode_mode == 'Inline') {
						$get_inlineimage_val = substr($shortcode, "13", -1);
						$image_attribute = explode('|',$get_inlineimage_val);
						$get_inlineimage_val = $image_attribute[0];
					} else if($shortcode_mode == 'Featured') {
						$get_inlineimage_val = substr($shortcode, "15", -1);
					}
					$uploadDir = wp_upload_dir();
					$inlineimageDir = $uploadDir['basedir'] .'/smack_uci_uploads/imports/'.$eventkey. '/inline_zip_uploads';
					$inlineimageURL = $uploadDir['baseurl'] .'/smack_uci_uploads/imports/'.$eventkey. '/inline_zip_uploads';
					$get_media_settings = get_option('uploads_use_yearmonth_folders');
					if ($get_media_settings == 1) {
						$dirname = date('Y') . '/' . date('m');
						$full_path = $uploadDir['basedir'] . '/' . $dirname;
						$baseurl = $uploadDir['baseurl'] . '/' . $dirname;
					} else {
						$full_path = $uploadDir['basedir'];
						$baseurl = $uploadDir['baseurl'];
					}
					$wp_media_path = $full_path;
					$inlineimageDirpath = $inlineimageDir;
					$imagelist = $this->scanDirectories($inlineimageDirpath);
					if(empty($imagelist)) {

					}else{
						$currentLoc = '';
						foreach($imagelist as $imgwithloc) {
							if(strpos($imgwithloc, $get_inlineimage_val)){
								$currentLoc = $imgwithloc;
							}
						}
						$exploded_currentLoc = explode("inline_zip_uploads", $currentLoc);
						if(!empty($exploded_currentLoc))
							$inlimg_curr_loc = isset($exploded_currentLoc[1]) ? $exploded_currentLoc[1] : '';
						$inlineimageURL = $inlineimageURL . $inlimg_curr_loc;
						if ($useexistingimages == 'false') {
							$get_inlineimage_val = wp_unique_filename($wp_media_path, trim($get_inlineimage_val));
						}
						$this->get_images_from_url($inlineimageURL, $wp_media_path, $get_inlineimage_val);
						$wp_media_path = $wp_media_path . "/" . $get_inlineimage_val;
						if (@getimagesize($wp_media_path)) {
							$inline_file ['guid'] = $baseurl . "/" . $get_inlineimage_val;
							$inline_file ['post_title'] = $get_inlineimage_val;
							$inline_file ['post_content'] = '';
							$inline_file ['post_status'] = 'attachment';
							$wp_upload_dir = wp_upload_dir();
							$attachment = array('guid' => $inline_file ['guid'], 'post_mime_type' => 'image/jpeg', 'post_title' => preg_replace('/\.[^.]+$/', '', @basename($inline_file ['guid'])), 'post_content' => '', 'post_status' => 'inherit');
							if ($get_media_settings == 1) {
								$generate_attachment = $dirname . '/' . $get_inlineimage_val;
							} else {
								$generate_attachment = $get_inlineimage_val;
							}
							$uploadedImage = $wp_upload_dir['path'] . '/' . $get_inlineimage_val;
							$real_image_name = $attachment['post_title'];
							//duplicate check for media
							global $wpdb;
							$existing_attachment = array();
							$query = $wpdb->get_results($wpdb->prepare("select post_title from $wpdb->posts where post_type = %s and post_mime_type = %s",'attachment','image/jpeg'));
							if ( ! empty( $query ) ) {
								foreach($query as $key){
									$existing_attachment[] = $key->post_title;
								}
							}
							//duplicate check for media
							if($shortcode_mode == 'Inline'){
								if(!empty($media_handle['imageprocess']) && $media_handle['imageprocess'] == 'duplicateimageoption') {
									if(!in_array($attachment['post_title'] ,$existing_attachment)){
										$attach_id = wp_insert_attachment($attachment, $generate_attachment, $postID);
										$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
										wp_update_attachment_metadata($attach_id, $attach_data);
									}
								}else{
									$attach_id = wp_insert_attachment($attachment, $generate_attachment, $postID);
									$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
									wp_update_attachment_metadata($attach_id, $attach_data);
								}
							}

							if($shortcode_mode == 'Featured'){

								if(!empty($media_handle['imageprocess']) && $media_handle['imageprocess'] == 'duplicateimageoption') {
									if( !in_array($attachment['post_title'] ,$existing_attachment)){
										$attach_id = wp_insert_attachment($attachment, $generate_attachment, $postID);
										$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
										wp_update_attachment_metadata($attach_id, $attach_data);
										set_post_thumbnail($postID, $attach_id);
									}else{
										$query2 = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_type = %s",$real_image_name,'attachment'));
										foreach($query2 as $key2){
											$attach_id = $key2->ID;
										}
										set_post_thumbnail($postID, $attach_id);
									}
								}else{
									$attach_id = wp_insert_attachment($attachment, $generate_attachment, $postID);
									$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
									wp_update_attachment_metadata($attach_id, $attach_data);
									set_post_thumbnail($postID, $attach_id);

								}
							}
							if($shortcode_mode == 'Inline') {
								$oldWord = $shortcode;
								$imgattr1 = isset($image_attribute[1]) ? $image_attribute[1] : '' ;
								$imgattr2 = isset($image_attribute[2]) ? $image_attribute[2] : '' ;
								$imgattr3 = isset($image_attribute[3]) ? $image_attribute[3] : '' ;
								$newWord = '<img src="' . $inline_file['guid'] . '" '.$imgattr1.' '.$imgattr2.' '.$imgattr3.' />';
								$post_content = str_replace($oldWord , $newWord , $post_content);
							}
						}else{

							$inline_file = false;
						}
					}
				}
				if($shortcode_mode == 'Inline') {
					$update_content['ID'] = $postID;
					$update_content['post_content'] = $post_content;
					wp_update_post($update_content);
				}
			}
		}

	}

	/**
	 * Scan root directory
	 *
	 * @param $rootDir          - Root Directory
	 * @param array $allData    - Data
	 *
	 * @return array|bool
	 */
	public function scanDirectories($rootDir, $allData=array()) {
		// set filenames invisible if you want
		$invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
		// run through content of root directory
		if(!is_dir($rootDir))
			return false;
		$dirContent = scandir($rootDir);
		foreach($dirContent as $key => $content) {
			// filter all files not accessible
			$path = $rootDir.'/'.$content;
			if(!in_array($content, $invisibleFileNames)) {
				// if content is file & readable, add to array
				if(is_file($path) && is_readable($path)) {
					// save file name with path
					$allData[] = $path;
					// if content is a directory and readable, add path and name
				}elseif(is_dir($path) && is_readable($path)) {
					// recursive callback to open new directory
					$allData = $this->scanDirectories($path, $allData);
				}
			}
		}
		return $allData;
	}

	/**
	 * Convert external image into local image
	 *
	 * @param $content      - Post Content
	 * @param $post_id      - Record ID
	 * @param $media_handle - Media Settings
	 */
	public function convert_local_image_src($content, $post_id, $media_handle) {
		if(trim($content) != '') {
			$content = "<p>".$content."</p>";
			$doc = new DOMDocument();
			#$doc->preserveWhiteSpace = false;
			if(function_exists('mb_convert_encoding')) {
				@$doc->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
			}else{
				@$doc->loadHTML( $content);
			}
			$searchNode = $doc->getElementsByTagName( "img" );
			if ( ! empty( $searchNode ) ) {
				foreach ( $searchNode as $searchNode ) {
					$orig_img_src = $searchNode->getAttribute( 'src' );
					$attachid     = $this->set_featureimage( $orig_img_src, $post_id, $media_handle );
					$new_img_src  = wp_get_attachment_url( $attachid );
					$searchNode->setAttribute( 'src', $new_img_src );
				}
				$post_content                   = $doc->saveHTML();
				$update_content['ID']           = $post_id;
				$update_content['post_content'] = $post_content;
				wp_update_post( $update_content );
			}
		}
	}

	/**
	 * Assign featured image
	 *
	 * @param $f_img        - Image URL
	 * @param $post_id      - Record ID
	 * @param null $media_handle   - Media Settings
	 *
	 * @return integer
	 */
	public function set_featureimage($f_img, $post_id, $media_handle = null){
		/* Image available in media */
		$useexistingimages = 'false';
		if(!empty($media_handle['imageprocess']) && $media_handle['imageprocess'] == 'duplicateimageoption' || !empty($media_handle['imageprocess']) && $media_handle['imageprocess'] =='use_existing_images'){
			$useexistingimages = 'true';
		}
		$dir = wp_upload_dir();
		$get_media_settings = get_option('uploads_use_yearmonth_folders');
		if ($get_media_settings == 1) {
			$dirname = date('Y') . '/' . date('m');
			$uploaddir_path = $dir ['basedir'] . '/' . $dirname;
			$uploaddir_url = $dir ['baseurl'] . '/' . $dirname;
		} else {
			$uploaddir_path = $dir ['basedir'];
			$uploaddir_url = $dir ['baseurl'];
		}

		$fimg_name = @basename($f_img);
		$fimg_name = str_replace(' ', '-', trim($fimg_name));
		$fimg_name = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $fimg_name);
		$fimg_name = urlencode($fimg_name);
		$path_parts = pathinfo($f_img);
		if (!isset($path_parts['extension'])) {
			$fimg_name = $fimg_name . '.jpg';
		}
		$featured_image = trim($path_parts['filename']);
		if ($useexistingimages == 'false') {
			$fimg_name = wp_unique_filename($uploaddir_path, trim($fimg_name));
		}

		$this->get_images_from_url($f_img, $uploaddir_path, $fimg_name);
		$filepath = $uploaddir_path . "/" . $fimg_name;
		$attach_id = '';
		if (@getimagesize($filepath)) {
			$file ['guid'] = $uploaddir_url . "/" . $fimg_name;
			$file ['post_title'] = $featured_image;
			$file ['post_content'] = '';
			$file ['post_status']  = 'inherit';
			$file ['post_type'] = 'attachment';
		} else {
			$file = false;
		}

		$fimg_type = wp_check_filetype( $f_img, null );

		if (!empty($fimg_type['type'])) {
                $mime_type = $fimg_type['type'];
        } elseif (!empty($fimg_type['ext'])) {
                $mime_type = $fimg_type['ext'];
        }
          if ($url = parse_url($f_img)) { 
               $type = pathinfo($url['path'], PATHINFO_EXTENSION);
                 if($type='jpg')
                   {
                   	$type='jpeg';
                   }
                   $mime_type ="image/".$type;
                }
		//if (!empty ($file)) {
			global $wpdb;
			$attachment = array('guid' => $file ['guid'], 'post_mime_type' => $mime_type, 'post_title' => preg_replace('/\.[^.]+$/', '', @basename(isset($file ['guid']) ? $file['guid'] : $uploaddir_url . "/" . $fimg_name)), 'post_content' => '', 'post_status' => 'inherit');
			if ($get_media_settings == 1) {
				$generate_attachment = $dirname . '/' . $fimg_name;
			} else {
				$generate_attachment = $fimg_name;
			}

			$uploadedImage = $dir['path'] . '/' . $fimg_name;
			$existing_attachment = array();
			$query = $wpdb->get_results($wpdb->prepare("select post_title from $wpdb->posts where post_type = %s and post_mime_type = %s",'attachment','image/jpeg'));
			if(!empty($query)) {
				foreach ( $query as $key ) {
					$existing_attachment[] = $key->post_title;
				}
			}
			if(!empty($media_handle['imageprocess']) && $media_handle['imageprocess'] == 'duplicateimageoption' || !empty($media_handle['imageprocess']) && $media_handle['imageprocess'] =='use_existing_images') {

				if ( ! in_array( $attachment['post_title'], $existing_attachment ) ) {
					$attach_id   = wp_insert_attachment( $attachment, $generate_attachment, $post_id );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $uploadedImage );
					wp_update_attachment_metadata( $attach_id, $attach_data );
				} else {
					$query2 = $wpdb->get_results( $wpdb->prepare( "select ID from $wpdb->posts where post_title = %s and post_type = %s", $attachment['post_title'], 'attachment' ) );
					foreach ( $query2 as $key2 ) {
						$attach_id = $key2->ID;
					}
				}
			}
			elseif(!empty($media_handle['imageprocess']) && $media_handle['imageprocess'] == 'imagenamechangeoption'){
				
				$renamevalue = $media_handle['renamevalue'];
				$query1 = $wpdb->get_results($wpdb->prepare("select {$renamevalue} from $wpdb->posts where ID = %d",$post_id));
				foreach($query1 as $key){
					if(!empty($key->post_title)) {
						$rename_file = preg_replace('/[^ \w]+/', "", $key->post_title);
					}
					else {
						$rename_file = $key->post_name;
					}
					$rename_file = preg_replace('/\s/', '-',$rename_file);
				}
				if(!in_array($rename_file ,$existing_attachment)){
					$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
					$post = get_post($post_id);
					$file = get_attached_file($attach_id);
					$path = pathinfo($file);
					$newfile = $dir['path'] . '/'.$rename_file.".".$path['extension'];
					rename($file, $newfile);
					update_attached_file( $attach_id, $newfile );
					$updateguid = $uploaddir_url . "/" .$rename_file.".".$path['extension'];
					$wpdb->update($wpdb->posts,array('post_content' => '', 'post_title' => preg_replace('/\.[^.]+$/', '', @basename($updateguid)),'post_status' => 'inherit','guid' => $updateguid, 'post_mime_type' => 'image/jpeg','post_type' => 'attachment'),array('ID'=>$attach_id));
					$attach_data = wp_generate_attachment_metadata($attach_id, $newfile);
					wp_update_attachment_metadata($attach_id, $attach_data);
				}else{
					$query2 = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_type = %s",$rename_file,'attachment'));
					foreach($query2 as $key2){
						$attach_id = $key2->ID;
					}

				}
			}
			else {
				
				$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
				// Mari commented
				// $attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
				// wp_update_attachment_metadata($attach_id, $attach_data);
			}
		//}
		return $attach_id;
	}

	/**
	 * Download image from external URL
	 *
	 * @param $f_img            - Image URL
	 * @param $uploaddir_path   - Upload Path
	 * @param $fimg_name        - Image Name
	 *
	 * @return null
	 */
	function get_images_from_url($f_img, $uploaddir_path, $fimg_name){

		$f_img = str_replace(" ","%20",$f_img);
		if ($uploaddir_path != "" && $uploaddir_path) {
			$uploaddir_path = $uploaddir_path . "/" . $fimg_name;
		}
		// Removed curl and added wordpress http api
		$response = wp_remote_get($f_img);				
		$rawdata =  wp_remote_retrieve_body($response);
		$http_code = wp_remote_retrieve_response_code($response);

		if ( $http_code != 200 && strpos( $rawdata, 'Not Found' ) != 0 ) {
			$rawdata = false;
		}

		if ($rawdata == false) {
			return null;
		} else {		

			if (file_exists($uploaddir_path)) {
				unlink($uploaddir_path);
			}
			$fp = fopen($uploaddir_path, 'x');
			fwrite($fp, $rawdata);
			fclose($fp);
		}
	}

	/**
	 * Evaluate the given expression using math function
	 *
	 * @param $equation     - Equation to be evaluate
	 *
	 * @return int|mixed|string
	 * @throws Exception
	 */
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
			throw new Exception("Unable to calculate equation");
		}
		if($result === FALSE){
			$result = 'false';
		}

		return $result;
	}

	/**
	 * Get file size
	 *
	 * @param $file - File
	 *
	 * @return int|string
	 */
	public function getFileSize($file) {
		$fileSize = filesize($file);

		if ($fileSize > 1024 && $fileSize < (1024 * 1024)) {
			$fileSize = round(($fileSize / 1024), 2) . ' kb';
		}
		else if ($fileSize > (1024 * 1024)) {
			$fileSize = round(($fileSize / (1024 * 1024)), 2) . ' mb';
		}
		else {
			$fileSize = $fileSize . ' bytes';
		}

		return $fileSize;
	}

	/**
	 * Get required custom fields
	 *
	 * @param $field_info   - Field Information
	 * Check the Mandatory Custom Fields
	 */
	public function Required_CF_Fields($field_info) {
		if(!class_exists('SmackUCIACFDataImport'))
			require_once(SM_UCI_PRO_DIR . "includes/class-uci-acf-data-import.php");
		if(!class_exists('SmackUCIPODSDataImport'))
			require_once(SM_UCI_PRO_DIR . "includes/class-uci-pods-data-import.php");
		if(!class_exists('SmackUCITypesDataImport'))
			require_once(SM_UCI_PRO_DIR . "includes/class-uci-types-data-import.php");
		$acfObj = new SmackUCIACFDataImport();
		$podsObj = new SmackUCIPODSDataImport();
		$typesObj = new SmackUCITypesDataImport();
		$acf_required_fields = $acfObj->ACF_RequiredFields($field_info['import_type']);
		$pods_required_fields = $podsObj->PODS_RequiredFields($field_info['import_type']);
		$types_required_fields = $typesObj->TYPES_RequiredFields($field_info['import_type']);
		$required_fields  = array_merge($acf_required_fields,$pods_required_fields,$types_required_fields);
		print_r(json_encode($required_fields));
		die;
	}

	/**
	 * WPML support on posts
	 * @param $data_array   - Data Array
	 * @param $pId          - Record ID
	 *
	 * @return bool
	 */
	public function UCI_WPML_Supported_Posts ($data_array, $pId) {
		global $sitepress, $wpdb;
		$get_trid = $wpdb->get_results("select trid from {$wpdb->prefix}icl_translations ORDER BY translation_id DESC limit 1");
		$trid = $get_trid[0]->trid;
		if(empty($data_array['translated_post_title']) && !empty($data_array['language_code'])){
			$wpdb->insert( $wpdb->prefix.'icl_translations', array('element_type' => 'post_'.$data_array['post_type'],'language_code' => $data_array['language_code'],'element_id' => $pId , 'trid' => $trid + 1));
		}
		elseif(!empty($data_array['language_code']) && !empty($data_array['translated_post_title'])){
			$update_query = $wpdb->prepare("select ID,post_type from $wpdb->posts where post_title = %s and post_status=%s order by ID DESC",$data_array['translated_post_title'] , 'publish');
			$ID_result = $wpdb->get_results($update_query);
			if(is_array($ID_result) && !empty($ID_result)) {
				$element_id = $ID_result[0]->ID;
				$post_type = $ID_result[0]->post_type;
			}else{
				return false;
			}
			$trid_id = $sitepress->get_element_trid($element_id,'post_'.$post_type);
			$translate_lcode = $sitepress->get_language_for_element($element_id,'post_'.$post_type);
			$wpdb->insert( $wpdb->prefix.'icl_translations', array( 'element_type' => 'post_'.$data_array['post_type'],'trid' => $trid_id, 'language_code' => $data_array['language_code'], 'source_language_code' => $translate_lcode ,'element_id' => $pId));
		}
	}

	/**
	 * Get CSV headers
	 *
	 * @param $field_name - Field Name
	 *
	 * @return string
	 */
	public function getCSVHeader($field_name) {
		$eventKey = isset($_REQUEST['eventkey']) ? sanitize_key($_REQUEST['eventkey']) : '';
		$headers = array();
		$filePath = SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;

		if(file_exists($filePath)) {
			$parserObj = new SmackCSVParser();
			$parserObj->parseCSV( $filePath, 0, - 1 );
			$headers = $parserObj->get_CSVheaders();
			$headers = $headers[0];
			$prefix = 'img_seo_'; #$mapping_count = 0;

			$header_fields = "<select style='width:35%; height:47px;float: right;margin: -32px 22px;' class='search_dropdown_mapping selectpicker' id='" . $prefix . "mapping_for_" . $field_name . "' name='" . $prefix . "mapping_for_" . $field_name . "' disabled='disabled'>";
			$header_fields .= "<option> --Select-- </option>";
			foreach($headers as $hKey) {
				$header_fields .= "<option value='" . $hKey . "'>" . $hKey . "</option>";
			}
			$header_fields .= "</select>";
		}
		return $header_fields;
	}

	/**
	 * Parse media settings
	 *
	 * @param $get_media_settings   - Media Settings
	 * @param $data_array           - Data Array
	 *
	 * @return array
	 */
	public function parse_media_settings($get_media_settings, $data_array) {
		  $get_ngg_options = get_option('ngg_options');
		$get_gallery_path = explode('/', $get_ngg_options['gallerypath']);
		$gallery_name=$data_array['nextgen-gallery'];
		if ( isset( $data_array['nextgen-gallery'] )) {
				$nextGenInfo = array(
				'status'    => 'enabled',
				'directory' => $data_array['nextgen-gallery'],
			);
			
		}
                if(in_array('nextgen-gallery/nggallery.php', $this->get_active_plugins()))
		{
		$path_parts = pathinfo($data_array['featured_image']);
		$real_fImg_name=$path_parts['filename'];
		$fImg_name = @basename($data_array['featured_image']);
		$fImg_name = str_replace(' ', '-', trim($fImg_name));
		$fImg_name = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $fImg_name);
		$fImg_name = urlencode($fImg_name);
		$id=$data_array['nextgen-gallery'];
		global $wpdb;
		$gallery_table = $wpdb->prefix . 'ngg_gallery';
                $get_gallery_id = $wpdb->get_col($wpdb->prepare("select gid from $gallery_table where name='$id'"));
		$gallery_id = $get_gallery_id[0];		
		$img_import_date = date('Y-m-d H:i:s');
		global $wpdb;
		$wpdb->insert( $wpdb->prefix .'ngg_pictures', array(
				'image_slug' => $real_fImg_name,
				'galleryid'  => $gallery_id,
				'filename'   => $fImg_name,
				'alttext'    => $real_fImg_name,
				'imagedate'  => $img_import_date,

			)
			);
                        $gallery_dir = WP_CONTENT_DIR . '/' . $get_gallery_path[1] . '/' . $gallery_name;
        		$image_id = $wpdb->insert_id;
			$storage  = C_Gallery_Storage::get_instance();
			$params = array('watermark' => false, 'reflection' => false);
			$result = $storage->generate_thumbnail($image_id, $params);
			$post_args = array('post_id' => $post_id);
			$copy_image = TRUE;

			$upload_dir = wp_upload_dir();
			$basedir = $upload_dir['basedir'];
			$gallery_abspath = $storage->get_gallery_abspath($gallery_id);
			$image_abspath = $storage->get_full_abspath($image_id);
			$url = $storage->get_full_url($image_id);
			$target_basename = M_I18n::mb_basename($image_abspath);

			$image = $storage->_image_mapper->find($image_id);

			if (strpos($image_abspath, $gallery_abspath) === 0) {
				$target_relpath = substr($image_abspath, strlen($gallery_abspath));
			} else {
				if ($gallery_id) {
					$target_relpath = path_join(strval($gallery_id), $target_basename);
				} else {
					$target_relpath = $target_basename;
				}
			}
			$target_relpath = trim($target_relpath, '\\/');
			$target_path = path_join($gallery_dir, $target_relpath);
			$image= file_get_contents($data_array['featured_image']);
			file_put_contents(ABSPATH .'wp-content/gallery/'.$gallery_name.'/'.$fImg_name,$image);
			file_put_contents(ABSPATH .'wp-content/gallery/'.$gallery_name.'/thumbs/'.'thumbs_'.$fImg_name,$image);
			$max_count = 100;
			$count = 0;
			while (@file_exists($target_path) && $count <= $max_count) {
				$count++;
				$pathinfo = M_I18n::mb_pathinfo($target_path);
				$dirname = $pathinfo['dirname'];
				$filename = $pathinfo['filename'];
				$extension = $pathinfo['extension'];
				$rand = mt_rand(1, 9999);
				$basename = $filename . '_' . sprintf('%04d', $rand) . '.' . $extension;
				$target_path = path_join($dirname, $basename);
			}
			$target_dir = dirname($target_path);
						if ($copy_image) {
				@copy($image_abspath, $target_path);
				if (!$attachment_id) {
					$size = @getimagesize($target_path);
					$image_type = $size ? $size['mime'] : 'image/jpeg';
					$title = sanitize_file_name($image->alttext);
					$caption = sanitize_file_name($image->description);
					$attachment = array('post_title' => $title, 'post_content' => $caption, 'post_status' => 'attachment', 'post_parent' => 0, 'post_mime_type' => $image_type, 'guid' => $url);
					$attachment_id = wp_insert_attachment($attachment, $target_path);
				}
				update_post_meta($attachment_id, '_ngg_image_id', $image_id);
				wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $target_path));
			}
			wp_mkdir_p($target_dir);
}
		$media_settings = array();
		if(isset($get_media_settings['imageprocess']) && $get_media_settings['imageprocess'] == 'use_existing_images') {
			$media_settings['media_process'] = 'use_existing_images';
		} elseif(isset($get_media_settings['imageprocess']) && $get_media_settings['imageprocess'] == 'overwrite_existing_images') {
			$media_settings['media_process'] = 'overwrite_existing_images';
		}
		if(isset($get_media_settings['media_seo_title']) && $get_media_settings['media_seo_title'] == 'on') {
			$media_settings['title'] = $data_array[$get_media_settings['img_seo_mapping_for_title']];
		}
		if(isset($get_media_settings['media_seo_caption']) && $get_media_settings['media_seo_caption'] == 'on') {
			$media_settings['caption'] = $data_array[$get_media_settings['img_seo_mapping_for_caption']];
		}
		if(isset($get_media_settings['media_seo_alttext']) && $get_media_settings['media_seo_alttext'] == 'on') {
			$media_settings['alttext'] = $data_array[$get_media_settings['img_seo_mapping_for_alttext']];
		}
		if(isset($get_media_settings['media_seo_description']) && $get_media_settings['media_seo_description'] == 'on') {
			$media_settings['description'] = $data_array[$get_media_settings['img_seo_mapping_for_description']];
		}
		if(isset($get_media_settings['change_media_file_name']) && $get_media_settings['change_media_file_name'] == 'on') {
			$media_settings['imageName'] = $data_array[$get_media_settings['img_seo_mapping_for_imageName']];
		}
		if(isset($get_media_settings['media_thumbnail_size']) && $get_media_settings['media_thumbnail_size'] == 'thumbnail') {
			$media_settings['thumbnail'] = 'on';
		}
		if(isset($get_media_settings['media_medium_size']) && $get_media_settings['media_medium_size'] == 'medium') {
			$media_settings['medium'] = 'on';
		}
		if(isset($get_media_settings['media_medium_large_size']) && $get_media_settings['media_medium_large_size'] == 'medium_large') {
			$media_settings['medium_large'] = 'on';
		}
		if(isset($get_media_settings['media_large_size']) && $get_media_settings['media_large_size'] == 'large') {
			$media_settings['large'] = 'on';
		}
		if(isset($get_media_settings['media_custom_size']) && $get_media_settings['media_custom_size'] == 'custom') {
			$media_settings['custom'] = 'on';
		}
		//modified a forloop for getting custom sizes
		foreach ($get_media_settings as $key => $value) {
			if(preg_match("/custom_slug_/", $key) || preg_match("/custom_width_/", $key) || preg_match("/custom_height_/", $key) ) {
				$media_settings[$key] = $value;
			} 
		}
		return $media_settings;
	}

	/**
	 * Push core fields data into database
	 *
	 * @param $mode         - Mode
	 * @param $data_array   - Data Array
	 */
	public function core_information_into_db ($mode, $data_array) {

	}

	public function CheckCSV($csv){
		  $delimiter = $this->getFileDelimiter($csv, 5);
		  $utf8 = 'Yes';
		  if(function_exists('mb_check_encoding')) {
			  if ( ! mb_check_encoding( file_get_contents( $csv ), 'UTF-8' ) ) {
				  $utf8 = 'No';
			  }
		  }
		  $valid_csv = $this->validateCSV($csv, $delimiter);
		  $result['isvalid'] = $valid_csv;
		  $result['isutf8'] = $utf8;
		  return $result;
        }

	public function getFileDelimiter($file, $checkLines = 2){
		$file = new SplFileObject($file);
		$delimiters = array(
		  ',',
		  '\t',
		  ';',
		  '|',
		  ':',
		  '&nbsp'
		);
		$results = array();
		$i = 0;
		 while($file->valid() && $i <= $checkLines){
		    $line = $file->fgets();
		    foreach ($delimiters as $delimiter){
			$regExp = '/['.$delimiter.']/';
			$fields = preg_split($regExp, $line);
			if(count($fields) > 1){
			    if(!empty($results[$delimiter])){
				$results[$delimiter]++;
			    } else {
				$results[$delimiter] = 1;
			    }
			}
		    }
		   $i++;
		}
		$results = array_keys($results, max($results));
		return $results[0];
	}

	public function validateCSV($filename='', $delimiter=',')
        {
                if(!file_exists($filename) || !is_readable($filename))
                  return FALSE;
                $header = NULL;
                $data = array();
                if (($handle = fopen($filename, 'r')) !== FALSE)
                {
                        while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE)
                        {
                                if(!$header)
                                        $header = $row;
                                else{
                                        $data[] = array_combine($header, $row);
                                        break;
                                }
                        }
                        $handle = fopen($filename, 'r');
                        if(array_key_exists(null,$data[0])){
                                $valid = 'No';
                        }else{
                                $valid = 'Yes';
                        }
			if(empty($data[0])){
				$valid = 'No';
			}
                        fclose($handle);
                }
                return $valid;
        }
		
	public function get_config_bytes($val) {
                        $val = trim($val);
                        $last = strtolower($val[strlen($val) - 1]);
                        switch ($last) {
                                case 'g':
                                        $val *= 1024;
                                case 'm':
                                        $val *= 1024;
                                case 'k':
                                        $val *= 1024;
                        }
                        return $this->fix_integer_overflow($val);
         }

	protected function fix_integer_overflow($size) {
                        if ($size < 0) {
                                $size += 2.0 * (PHP_INT_MAX + 1);
                        }
                        return $size;
        }

	public function get_total_rows($file){
		$parserObj = new SmackCSVParser();
		$parserObj->parseCSV($file, 0, -1);
		$total_row_count = $parserObj->total_row_cont - 1;
		return $total_row_count;
	}

	public function get_rollback_tables($type){
                if($type == 'Users'){
                        $tables = array('users','usermeta');
                }elseif($type == 'Comments'){
                        $tables = array('comments','commentmeta');
                }elseif($type == 'CustomerReviews'){
                        $tables = array('posts','postmeta');
                        if(in_array('wp-customer-reviews/wp-customer-reviews-3.php', $this->get_active_plugins())){
                                //array_push($tables,'wpcreviews');
                        }
                }elseif($type == 'Events' || $type == 'location' || $type == 'ticket'){
                        if(in_array('events-manager/events-manager.php', $this->get_active_plugins())) {
                                $tables = array('posts','postmeta','em_locations','em_tickets','em_events');
                        }
                }else{
                        $tables = array('posts','postmeta','termmeta','terms','term_relationships','term_taxonomy','options','usermeta','comments','commentmeta');
                        if(in_array('sitepress-multilingual-cms/sitepress.php', $this->get_active_plugins())){
                                array_push($tables,'icl_translations');
                        }
                        if($type == 'MarketPress' || $type == 'MarketPressVariations'){
                                array_push($tables,'mp_product_attributes');
                        }
                        if($type == 'WPeCommerce'){
                                array_push($tables,'wpsc_coupon_codes');
                        }
                        if(in_array('custom-field-suite/cfs.php',$this->get_active_plugins())){
                                array_push($tables,'cfs_values');
                        }
                }
                $sqltables = array_map(function($tables) {
                        global $wpdb;
                        return $wpdb->prefix . $tables;
                }, $tables);
                return $sqltables;
        }
	
	public function set_backup_restore($tables = null,$eventkey,$type){
		$dbname = DB_NAME;
		$dbuser = DB_USER;
		$dbpass = DB_PASSWORD;
		$upload_dir = wp_upload_dir();
		//$date = date('Y-m-d-H:i:s');
		$uploadpath = SM_UCI_FILE_MANAGING_DIR ."rollback_files/". $eventkey;
		$filename = 'Backup_'.$eventkey.'.sql';
		if (!is_dir($uploadpath)) {
                        wp_mkdir_p($uploadpath);
                }
		$filepath = $uploadpath.'/'.$filename;
		if($type == 'backup'){
			$backtabs = implode(' ',$tables);
			$command = "mysqldump -u{$dbuser}  -p{$dbpass} {$dbname} {$backtabs} > {$filepath}";
			exec($command,$output,$return);
			if(!$return){
				return 'Backup Completed';
			}else{
				return 'Not Completed';
			}
		}
		if($type == 'restore'){
			if(file_exists($filepath)) {
				$command = "mysql -u{$dbuser}  -p{$dbpass} {$dbname} < {$filepath}";
				exec($command,$output,$return);
				if(!$return){
					return 'Rollback Completed';
				}else{
					return 'Not Completed';
				}
			}
		}
		if($type == 'delete'){
			if (!unlink($filepath)){
				return 'Error Deleting'.$filename;
			}else{
				rmdir($uploadpath); 
				return 'Deleted'.$filename;
			}
		}
	}

	public function updateMaintenance($value)
	{
		$mode = array();
		$mode['enable_main_mode'] = $value;
		update_option('sm_uci_pro_settings', $mode);
	}

	public function tableNode($node)
		{
				  if($node->nodeName != '#text'){ 
				  if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ 
				    $newVal = str_replace('/', '_', $node->getNodePath());
				    $newVal = str_replace('[', '', $newVal);
				    $newVal = str_replace(']', '', $newVal);
				  ?>
				<ul>
				<!-- <li id="data"> <label style="color:#8B008B" id='lbtext<?php echo $newVal ?>' onclick="expColXml(this.id)"> <b><?php echo '+&lt;'.$node->nodeName.'&gt;'; ?> </b></label><div id='divlbtext<?php echo $newVal ?>' > -->
				<?php } //echo '<pre>'; print_r($node->childNodes); 
				     if ($node->hasChildNodes()) {
				    foreach ($node->childNodes as $child){
				        $this->tableNode($child);
				    }
				    //get all attributes
				    if($node->hasAttributes()){
				      for ($i = 0; $i <= $node->attributes->length; ++$i) {
				        $attr_nodes = $node->attributes->item($i);
				        if($attr_nodes->nodeName && $attr_nodes->nodeValue) 
				        $attrs[$node->nodeName][$attr_nodes->nodeName] = $attr_nodes->nodeValue;
				      }
				    }    
				    //get all attributes
				    if($node->nodeValue || $node->nodeValue == 0){ 
				      if($node->childNodes->length == 1){?>
				<ul>
				
				<div class="uci_mapping_csv_column">
					<li style="color: #00A699; font-weight: 600;cursor: move;width: 50%;" draggable="true" ondragstart="drag(event)" class="uci_csv_column_header" title='<?php echo $node->getNodePath(); ?>'> <b><?php echo $node->nodeName; ?> </b></li>
				 
				<li class="uci_csv_column_val" title='<?php echo $node->getNodePath(); ?>'>
				 <?php if(strlen($node->nodeValue) > 150) 
				 	echo substr($node->nodeValue, 0, 150)."<span style='color: red;'> [more]</span>";
				 else
				 	echo $node->nodeValue; ?></li>
				</div>
				
				<div class="clearfix"></div>
				
				
				</ul>
				<?php }
				    }
				   }  
				  if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ ?>
				<!-- </div><label style="color:#8B008B"> <b><?php echo '&lt;/'.$node->nodeName.'&gt;'; ?></b></label>
				</li> -->
				</ul>
				<?php }
				   }
		}

	public function treeNode($node)
		{
				  if($node->nodeName != '#text'){ 
				  if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ 
				    $newVal = str_replace('/', '_', $node->getNodePath());
				    $newVal = str_replace('[', '', $newVal);
				    $newVal = str_replace(']', '', $newVal);
				  ?>
				<ul>
				<li id="data"> <label class="node-tag" id='lbtext<?php echo $newVal ?>' onclick="expColXml(this.id)"> <b><span class="tree-icon"><i id="icon_lbtext<?php echo $newVal ?>" class="glyphicon glyphicon-minus-sign"></i><?php echo '&lt;'.$node->nodeName.'&gt;'; ?> </span></b></label><div id='divlbtext<?php echo $newVal ?>' >
				<?php } //echo '<pre>'; print_r($node->childNodes); 
				     if ($node->hasChildNodes()) {
				    foreach ($node->childNodes as $child){
				        $this->treeNode($child);
				    }
				    //get all attributes
				    if($node->hasAttributes()){
				      for ($i = 0; $i <= $node->attributes->length; ++$i) {
				        $attr_nodes = $node->attributes->item($i);
				        if($attr_nodes->nodeName && $attr_nodes->nodeValue) 
				        $attrs[$node->nodeName][$attr_nodes->nodeName] = $attr_nodes->nodeValue;
				      }
				    }    
				    //get all attributes
				    if($node->nodeValue || $node->nodeValue == 0){ 
				      if($node->childNodes->length == 1){?>
				<ul>
				<li>
				<label class="node-tag"  style="cursor: move;" draggable="true" ondragstart="drag(event)" class="uci_csv_column_header" title='<?php echo $node->getNodePath(); ?>'> <b><?php echo '&lt;'.$node->nodeName.'&gt;'; ?> </b></label>
				 
				<span style="cursor: move;" draggable="true" ondragstart="drag(event)" class="uci_csv_column_header" title='<?php echo $node->getNodePath(); ?>'> <?php echo $node->nodeValue; ?></span>
				<label class="node-tag"> <b><?php echo '&lt;/'.$node->nodeName.'&gt;'; ?> </b></label>
				</li>
				</ul>
				<?php }
				    }
				   }  
				  if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ ?>
				</div><label class="node-tag"> <b><?php echo '&lt;/'.$node->nodeName.'&gt;'; ?></b></label>
				</li>
				</ul>
				<?php }
				   }
		}
	
}
