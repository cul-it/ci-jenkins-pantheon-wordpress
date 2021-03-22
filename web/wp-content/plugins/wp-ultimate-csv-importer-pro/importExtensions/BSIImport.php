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

class BSIImport {
    private static $bsi_instance = null;

    public static function getInstance() {		
				if (BSIImport::$bsi_instance == null) {
					BSIImport::$bsi_instance = new BSIImport;
					return BSIImport::$bsi_instance;
				}
				return BSIImport::$bsi_instance;
    }
    function set_bsi_values($header_array ,$value_array , $map, $post_id , $type){

				$post_values = [];
				$helpers_instance = ImportHelpers::getInstance();	
				$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
				
				$this->bsi_import_function($post_values, $post_id);    
    }

    public function bsi_import_function($data_array, $uID){
				foreach( $data_array as $daKey => $daVal ) {
						if(strpos($daKey, 'msi_') === 0) {
							$msi_custom_key = substr($daKey, 4);
							$msi_shipping_array[$msi_custom_key] = $daVal;
						} elseif(strpos($daKey, 'mbi_') === 0) {
							$mbi_custom_key = substr($daKey, 4);
							$mbi_billing_array[$mbi_custom_key] = $daVal;
						} else {
							update_user_meta($uID, $daKey, $daVal);
						}
				}
				//Import MarketPress Shipping Info
				if (!empty ($msi_shipping_array)) {
						$custom_key = 'mp_shipping_info';
						update_user_meta($uID, $custom_key, $msi_shipping_array);
				}
				//Import MarketPress Billing Info
				if (!empty ($mbi_billing_array)) {
						$custom_key = 'mp_billing_info';
						update_user_meta($uID, $custom_key, $mbi_billing_array);
				}
    }
}