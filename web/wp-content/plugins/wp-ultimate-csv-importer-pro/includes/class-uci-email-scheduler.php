<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly


class SmackUCIEmailScheduler {

	public static function send_login_credentials_to_users() {
		global $wpdb;
		require_once(ABSPATH . "wp-includes/pluggable.php");
		$ucisettings = get_option('sm_uci_pro_settings');
		if($ucisettings['send_user_password'] == "on") {
			$get_user_meta_info = $wpdb->get_results( $wpdb->prepare("select *from {$wpdb->prefix}usermeta where meta_key like %s", '%' . 'smack_uci_import' . '%') );
			if(!empty($get_user_meta_info)) {
				foreach($get_user_meta_info as $key => $value) {
					$data_array = maybe_unserialize($value->meta_value);
					$currentUser             = wp_get_current_user();
					$admin_email             = $currentUser->user_email;
					$em_headers              = "From: Administrator <$admin_email>"; # . "\r\n";
					$message                 = "Hi,You've been invited with the role of " . $data_array['role'] . ". Here, your login details." . "\n" . "username: " . $data_array['user_login'] . "\n" . "userpass: " . $data_array['user_pass'] . "\n" . "Please click here to login " . wp_login_url();
					$emailaddress            = $data_array['user_email'];
					$subject                 = 'Login Details';
					if( wp_mail( $emailaddress, $subject, $message) ){
						delete_user_meta($value->user_id, 'smack_uci_import');
					}
				}
			}
		}
	}
}

