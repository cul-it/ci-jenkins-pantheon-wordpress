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

class ProductMetaImport {
    private static $product_meta_instance = null;

    public static function getInstance() {
		
		if (ProductMetaImport::$product_meta_instance == null) {
			ProductMetaImport::$product_meta_instance = new ProductMetaImport;
			return ProductMetaImport::$product_meta_instance;
		}
		return ProductMetaImport::$product_meta_instance;
    }

    function set_product_meta_values($header_array ,$value_array , $map ,$maps, $post_id ,$type , $line_number , $mode , $core_map){
        global $wpdb;

        $woocommerce_meta_instance = WooCommerceMetaImport::getInstance();
		$wpecommerce_instance = WPeCommerceImport::getInstance();
		$marketpress_instance = MarketPressImport::getInstance();
		$eshop_instance = EshopImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		$data_array = [];
        $data_array = $helpers_instance->get_header_values($map , $header_array , $value_array);
        $core_array = $helpers_instance->get_header_values($core_map , $header_array , $value_array);
        $image_meta = $helpers_instance->get_meta_values($maps , $header_array , $value_array);
        if(($type == 'WooCommerce Product') || ($type == 'WooCommerce Product Variations') || ($type == 'WooCommerce Orders') || ($type == 'WooCommerce Coupons') || ($type == 'WooCommerce Refunds')){
            $woocommerce_meta_instance->woocommerce_meta_import_function($data_array,$image_meta, $post_id , $type , $line_number , $mode, $header_array, $value_array , $core_array);
        }
        if($type == 'WPeCommerce Products'){
            $wpecommerce_instance->wpecommerce_meta_import_function($data_array, $post_id , $line_number,$header_array,$value_array);
        }
		if(($type == 'MarketPress Product') || ($type == 'MarketPress Product Variations')){
            $marketpress_instance->marketpress_meta_import_function($data_array, $post_id , $type , $line_number );
		}
		if($type == 'eShop Products'){
            $eshop_instance->eshop_meta_import_function($data_array, $post_id);
        }
    }

}