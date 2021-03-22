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

class UsersExtension extends ExtensionHandler{
		private static $instance = null;

    public static function getInstance() {		
				if (UsersExtension::$instance == null) {
                    UsersExtension::$instance = new UsersExtension;
				}
				return UsersExtension::$instance;
    }

    /**
	* Provides Users fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data){        
        $response = [];
        if(is_plugin_active('woocommerce/woocommerce.php')){
             $billing_fields = array(
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
        }
        if(is_plugin_active('marketpress/marketpress.php') || is_plugin_active('wordpress-ecommerce/marketpress.php')){
                 $billing_fields = array(
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
        }
        $billing_value = $this->convert_static_fields_to_array($billing_fields);
        $response['billing_and_shipping_information'] = $billing_value;
        if(is_plugin_active('wp-members/wp-members.php')){
            $wp_members_fields = $this->custom_fields_by_wp_members();
            $response['custom_fields_wp_members'] = $wp_members_fields;
               
        }
        if(is_plugin_active('members/members.php')){
            $members_fields = $this->custom_fields_by_members();
            $response['custom_fields_members'] =  $members_fields;
                
        } 
        if(is_plugin_active('ultimate-member/ultimate-member.php')){
            $members_fields = $this->custom_fields_by_ultimate_member();
            $response['custom_ultimate_members'] =  $members_fields;   
        } 
		return $response;	
    }

    public function custom_fields_by_wp_members () {
        $WPMemberFields = array();      
        $get_WPMembers_fields = get_option('wpmembers_fields');       
        $search_array = array('Choose a Username', 'First Name', 'Last Name', 'Email', 'Confirm Email', 'Website', 'Biographical Info', 'Password', 'Confirm Password', 'Terms of Service');

		if (is_array($get_WPMembers_fields) && !empty($get_WPMembers_fields)) {
			foreach ($get_WPMembers_fields as $get_fields) {
                    foreach($search_array as $search_values){                         
                        if(is_array($get_fields)){   
                            if(in_array($search_values , $get_fields)){
                                unset($get_fields);
                            }
                        }
                    }
                if(!empty($get_fields[2])){
                    $WPMemberFields['WPMEMBERS'][$get_fields[2]]['label'] = $get_fields[1];
                    $WPMemberFields['WPMEMBERS'][$get_fields[2]]['name'] = $get_fields[2];
                }
            }
        }
        
        $wp_mem_fields = $this->convert_fields_to_array($WPMemberFields);
        return $wp_mem_fields;
    }

    public function custom_fields_by_members () {
		$MemberFields = array();
		$MemberFields['MULTIROLE']['multi_user_role']['label'] = 'Multi User Role';
        $MemberFields['MULTIROLE']['multi_user_role']['name'] = 'multi_user_role';
        $mem_fields = $this->convert_fields_to_array($MemberFields);
		return $mem_fields;
    }
    
    public function custom_fields_by_ultimate_member () {
		$WPUltimateMember = array();
		$get_WPUltimateMember = get_option('um_fields');
		if(is_array($get_WPUltimateMember) && !empty($get_WPUltimateMember)) {
			foreach($get_WPUltimateMember as $get_fields) {
				$WPUltimateMember['ULTIMATEMEMBER'][$get_fields['metakey']]['label'] = $get_fields['label'];
				$WPUltimateMember['ULTIMATEMEMBER'][$get_fields['metakey']]['name'] = $get_fields['metakey'];
			}
        }
        $ultimate_member_fields = $this->convert_fields_to_array($WPUltimateMember);
		return $ultimate_member_fields;
	}
    
    /**
	* Users extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		if($import_type == 'Users'){
            return true;
        }
	}
        
 }