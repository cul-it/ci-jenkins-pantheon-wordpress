<?php

/**
 * Save Settings
 */
function seedprod_lite_save_settings() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! empty( $_POST['settings'] ) ) {
			$settings = stripslashes_deep( $_POST['settings'] );

			$s = json_decode( $settings );
			$s->api_key = sanitize_text_field($s->api_key);
			$s->enable_coming_soon_mode = sanitize_text_field($s->enable_coming_soon_mode);
			$s->enable_maintenance_mode = sanitize_text_field($s->enable_maintenance_mode);
			$s->enable_login_mode = sanitize_text_field($s->enable_login_mode);
			$s->enable_404_mode = sanitize_text_field($s->enable_404_mode);


			// Get old settings to check if there has been a change
			$settings_old = get_option( 'seedprod_settings' );
			$s_old        = json_decode( $settings_old );

			// Key is for $settings, Value is for get_option()
			$settings_to_update = array(
				'enable_coming_soon_mode' => 'seedprod_coming_soon_page_id',
				'enable_maintenance_mode' => 'seedprod_maintenance_mode_page_id',
				'enable_login_mode'       => 'seedprod_login_page_id',
				'enable_404_mode'         => 'seedprod_404_page_id',
			);

			foreach ( $settings_to_update as $setting => $option ) {
				$has_changed = ( $s->$setting !== $s_old->$setting ? true : false );
				if ( ! $has_changed ) {
					continue; } // Do nothing if no change

				$id = get_option( $option );

				$post_exists = ! is_null( get_post( $id ) );
				if ( ! $post_exists ) {
					update_option( $option, null );
					continue;
				}

				$update       = array();
				$update['ID'] = $id;

				// Publish page when active
				if ( $s->$setting === true ) {
					$update['post_status'] = 'publish';
					wp_update_post( $update );
				}

				// Unpublish page when inactive
				if ( $s->$setting === false ) {
					$update['post_status'] = 'draft';
					wp_update_post( $update );
				}
			}

			update_option( 'seedprod_settings', $settings );

			$response = array(
				'status' => 'true',
				'msg'    => __( 'Settings Updated', 'coming-soon' ),
			);
		} else {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'Error Updating Settings', 'coming-soon' ),
			);
		}

		// Send Response
		wp_send_json( $response );
		exit;
	}
}


function seedprod_lite_save_app_settings() {
	if ( check_ajax_referer( 'seedprod_lite_save_app_settings' ) ) {

		if ( ! empty( $_POST['app_settings'] ) ) {

			$app_settings = stripslashes_deep( $_POST['app_settings'] );
			if ( isset( $app_settings['disable_seedprod_button'] ) && $app_settings['disable_seedprod_button'] == 'true' ) {
				$app_settings['disable_seedprod_button'] = true;
			} else {
				$app_settings['disable_seedprod_button'] = false;
			}
			$app_settings['facebook_g_app_id'] = sanitize_text_field($app_settings['facebook_g_app_id']);
			$app_settings_encode = wp_json_encode( $app_settings );

			update_option( 'seedprod_app_settings', $app_settings_encode );
			$response = array(
				'status' => 'true',
				'msg'    => __( 'App Settings Updated', 'coming-soon' ),
			);

		} else {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'Error Updating App Settings', 'coming-soon' ),
			);
		}
			// Send Response
			wp_send_json( $response );
			exit;

	}
}
