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

class ProductImageMetaExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (ProductImageMetaExtension::$instance == null) {
			ProductImageMetaExtension::$instance = new ProductImageMetaExtension;
		}
		return ProductImageMetaExtension::$instance;
	}
	
	/**
	* Provides Product Image Meta mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = [];
        $product_image_meta_Fields = array(
			        'Caption' => 'product_caption',
					'Alt text' => 'product_alt_text',
					'Description' => 'product_description',
					'File Name' => 'product_file_name',
					'Title' => 'product_title',
        );
		$product_image_meta_value = $this->convert_static_fields_to_array($product_image_meta_Fields);
		$response['product_image_meta_fields'] = $product_image_meta_value ;
		return $response;
		
    }

	/**
	* Product Image Meta Fields extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type){
        if(is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active('wordpress-ecommerce/marketpress.php') || is_plugin_active('marketpress/marketpress.php') || is_plugin_active('eshop/eshop.php') || is_plugin_active('wp-e-commerce/wp-shopping-cart.php')){
            $import_type = $this->import_name_as($import_type);
            if($import_type == 'WooCommerce' || $import_type == 'MarketPress' || $import_type == 'WPeCommerce' || $import_type == 'eShop' || $import_type == 'WooCommerceVariations' || $import_type == 'WooCommerceOrders' || $import_type == 'WooCommerceCoupons' || $import_type == 'WooCommerceRefunds' || $import_type == 'MarketPressVariations') { 
                return true;
            }else{
                return false;
            }
        }
    }
}