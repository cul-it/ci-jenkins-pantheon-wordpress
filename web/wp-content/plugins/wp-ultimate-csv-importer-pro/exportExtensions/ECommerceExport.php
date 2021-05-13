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

/**
 * Class EComExport
 * @package Smackcoders\WCSV
 */
class EComExport {

	protected static $instance = null,$mapping_instance,$export_handler,$export_instance;
	public $totalRowCount;	
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$export_instance = ExportExtension::getInstance();
		}
		return self::$instance;
	}

	/**
	 * EComExport constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 * Export WP-ECommerce products
	 * @param $id
	 */
	public function getEcomData($id)
	{
		global $wpdb;	
		$meta = unserialize(self::$export_instance->data[$id]['_wpsc_product_metadata']);
		foreach ($meta as $key => $value) {
			if(is_array($value))
			{
				foreach ($value as $key1 => $value1) {
					self::$export_instance->data[$id][$key1] = $value1;
					if($key1 == 'quantity')
						self::$export_instance->data[$id][$key1] = implode(',',$value1);
					if($key1 == 'table_price')
						self::$export_instance->data[$id][$key1] = implode(',',$value1);
					if($key1 == 'local')
						$local = $value1.'|';	
					if($key1 == 'international' )
						self::$export_instance->data[$id]['shipping'] = $local . $value1;	
				}
			}
			else{
				if($key == 'dimension_unit'){
					self::$export_instance->data[$id]['height_unit'] = $value;
					self::$export_instance->data[$id]['length_unit'] = $value;
					self::$export_instance->data[$id]['width_unit'] = $value;
				}
				if($key == 'price'){
					self::$export_instance->data[$id]['sale_price'] = $value;
				}
				self::$export_instance->data[$id][$key] = $value;	


			}	
		}
		$get_downloadfiles = $wpdb->prepare("select guid from {$wpdb->prefix}posts where post_parent = %d AND post_type = %s", $id, 'wpsc-product-file');
		$attachment = $wpdb->get_results($get_downloadfiles,ARRAY_A);
		if(is_array($attachment)){
			foreach($attachment as $k => $guid){
				$download_filedata[$k] = $guid['guid'];
			}
		}
		self::$export_instance->data[$id]['download_product_image'] = implode('|',$download_filedata);
		self::$export_instance->data[$id]['purchase_donation'] = isset(self::$export_instance->data[$id]['_wpsc_is_donation']) ? self::$export_instance->data[$id]['_wpsc_is_donation'] : "";
		self::$export_instance->data[$id]['short_description'] = get_the_excerpt($id);
		self::$export_instance->data[$id]['PRODUCTSKU'] = isset(self::$export_instance->data[$id]['_wpsc_sku']) ? self::$export_instance->data[$id]['_wpsc_sku'] : "";
		self::$export_instance->data[$id]['sale_price'] = isset(self::$export_instance->data[$id]['_wpsc_price']) ? self::$export_instance->data[$id]['_wpsc_price'] : "";
		self::$export_instance->data[$id]['taxable_amount'] = isset(self::$export_instance->data[$id]['wpec_taxes_taxable_amount']) ? self::$export_instance->data[$id]['wpec_taxes_taxable_amount'] : "";
		self::$export_instance->data[$id]['is_taxable'] = isset(self::$export_instance->data[$id]['wpec_taxes_taxable']) ? self::$export_instance->data[$id]['wpec_taxes_taxable'] : 0;
		self::$export_instance->data[$id]['enable_comments'] = isset(self::$export_instance->data[$id]['comment_status']) ? self::$export_instance->data[$id]['comment_status'] : 0;
		$img_id = unserialize(self::$export_instance->data[$id]['_wpsc_product_gallery']);
		$img_link = $this->getAttachment($img_id[0]);
		self::$export_instance->data[$id]['image_gallery'] = $img_link;
		$currency = isset(self::$export_instance->data[$id]['_wpsc_currency']) ? unserialize(self::$export_instance->data[$id]['_wpsc_currency']) : '';
		$money = '';
		foreach ($currency as $country => $amount) {
			$money .= $country.'|'.$amount.',';
		}
		if($money){
			$money = rtrim($money,",");
		}
		self::$export_instance->data[$id]['alternative_currencies_and_price'] = $money;
	}

	/**
	 * Code to get attachment data
	 * @param $id
	 * @return mixed
	 */
	public function getAttachment($id)
	{
		global $wpdb;
		$get_attachment = $wpdb->prepare("select guid from {$wpdb->prefix}posts where ID = %d AND post_type = %s", $id, 'attachment');
		$attachment = $wpdb->get_results($get_attachment);
		$attachment_file = $attachment[0]->guid;
		return $attachment_file;
	}

	/**
	 * Export WP-ECommerce Coupons
	 * @param $id
	 */
	public function getEcomCouponData($id){
		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpsc_coupon_codes where Id = %d", $id);
		$result = $wpdb->get_results($query,ARRAY_A);
		foreach ($result as $key => $value) {
			foreach ($value as $key1 => $value1) {
				if($key1 == 'value'){
					self::$export_instance->data[$id]['discount'] = $value1;
				}elseif($key1 == 'use-once'){
					self::$export_instance->data[$id]['use_once'] = $value1;
				}elseif($key1 == 'is-percentage'){
					self::$export_instance->data[$id]['discount_type'] = $value1;
				}elseif($key1 == 'is-used'){
					self::$export_instance->data[$id]['is_used'] = $value1;
				}elseif($key1 == 'active'){
					self::$export_instance->data[$id]['is_active'] = $value1;
				}elseif($key1 == 'every_product'){
					self::$export_instance->data[$id]['apply_on_all_products'] = $value1;
				}elseif($key1 == 'condition'){
					$conditions = unserialize($value1);
					$cond = '';
					foreach($conditions as $k => $v){
						$cond .= implode('|',$v).','; 	
					}
					self::$export_instance->data[$id]['conditions'] = rtrim($cond,",");
				}elseif($key1 == 'id'){
					self::$export_instance->data[$id]['COUPONID'] = $value1;
				}else{
					self::$export_instance->data[$id][$key1] = $value1;
				}
			}
		}
	}
}
