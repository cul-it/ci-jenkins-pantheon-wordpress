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
 * Class Security
 * @package Smackcoders\WCSV
 */
class LicenseManager {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	/**
	 * LicenseManager constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 *   LicenseManager hooks.
	 */
	public function doHooks(){
		add_action('wp_ajax_license_tab', array($this, 'licenseTabActivation'));
		add_action('wp_ajax_verify_license', array($this, 'verifyLicense'));
    }
    public static function licenseTabActivation(){
	
		$tab_activation = get_option('LICENSE_TAB');
	
		if($tab_activation == 'true' ){
			echo wp_json_encode(['response' => ['license_tab' => true, 'status' => 200, 'success' => true]]);
		}
		else{
			echo wp_json_encode(['response' => ['license_tab' => false, 'status' => 200, 'success' => true]]);
		}
		wp_die();
	}
	public static function verifyLicense(){
		$license_key = $_POST['license_key'];
		$product_id = $_POST['product_id'];
		//$plugin_name = self::$instance->getAddonName($plugin_slug);
		$urlparts = parse_url(home_url());
		$domain_name = $urlparts['host'];
		$url ='https://www.smackcoders.com/?rest_route=/licensemanager/v1/isvalid&product_id='.$product_id.'&key='.$license_key.'&domain_url='.$domain_name;
		//$url ='https://www.smackcoders.com/?rest_route=/licensemanager/v1/isvalid&product_slug='.$plugin_slug.'&key='.$license_key.'&domain_url='.$domain_name;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		$result = curl_exec($ch);
		$result_array = json_decode($result,TRUE);
		curl_close($ch);
		if($result_array['success'] == 'true'){
			$get_domain_products = self::$instance->getDomainProducts($license_key,$domain_name);
			$get_products = $get_domain_products['productinfo'];
			if($get_domain_products['success'] == 1){
				foreach($get_products as $prod_key => $prod_val){
					$plugin_name = $prod_val['product_name']; 
					$get_slug = self::$instance->getPluginSlug($plugin_name);
					$plugin_slug = $get_slug['url'];
					if ( self::is_plugin_installed( $plugin_slug ) ) {
						//$activate_addon = self::$instance->activateAddon($addon_slug,$license_key);
						$response_message[$prod_key] = 'License Key Verified for '.' '.$plugin_name;
					}
					else{
						$response_message[$prod_key] = 'License Key Verified for '.' '.$plugin_name.' '.'and Could not be activated';
					}
				}
				update_option('LICENSE_TAB_ACTIVE', 'false');
				echo wp_json_encode(['response' => '' ,'is_license_vefified' => true, 'message' => $response_message, 'status' => 200, 'success' => true]);					
			}
			else{
				echo wp_json_encode(['response' => '' ,'is_license_vefified' => true, 'message' => 'Invalid License Key', 'status' => 200, 'success' => false]);					
			}
		}
		else{
			echo wp_json_encode(['response' => '','is_license_vefified' => false, 'message' => $result_array['message'], 'status' => 200, 'success' => false]);	
		}
		wp_die();	
    }
    public static function getDomainProducts($license_key,$domain_name){
		$url ='https://www.smackcoders.com/?rest_route=/licensemanager/v1/getproducts&key='.$license_key.'&domain_url='.$domain_name;
		$ch = curl_init($url);		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		$result = curl_exec($ch);
		$result_array = json_decode($result,TRUE);
		curl_close($ch);
		return $result_array;
	}
	public static function getPluginSlug($plugin_name){
		if($plugin_name  == ' WP Ultimate CSV Importer Pro'){
			$plugin['url'] = 'wp-ultimate-csv-importer-pro/wp-utlimate-csv-importer-pro.php';
			$plugin['slug'] = 'wp-ultimate-csv-importer-pro';
		}

		return $plugin;
	}
	public function is_plugin_installed( $slug ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		if ( !empty( $all_plugins[$slug] ) ) {
			return true;
		} else {
			return false;
		}
	}
	

}
?>