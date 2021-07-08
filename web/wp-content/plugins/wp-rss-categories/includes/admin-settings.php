<?php    

	add_action( 'wprss_admin_init', 'wprss_c_add_settings' );
	/**
	 * Adds some more settings fields pertaining to categories
	 * @since 1.0
	 */    
	function wprss_c_add_settings() {

		// Use old licensing settings fields if core is older than v4.5
		if ( version_compare(WPRSS_VERSION, '4.5', '<') ) {
			add_settings_section(
				'wprss_settings_c_licenses_section',
				__( 'Categories License', WPRSS_TEXT_DOMAIN ),
				'wprss_c_settings_license_callback',
				'wprss_settings_license_keys'
			);

			add_settings_field(
				'wprss-settings-license',
				__( 'License Key', WPRSS_TEXT_DOMAIN ),
				'wprss_c_setting_license_callback',
				'wprss_settings_license_keys',
				'wprss_settings_c_licenses_section'
			);

			add_settings_field(
				'wprss-settings-license-activation',
				__( 'Activate License', WPRSS_TEXT_DOMAIN ),
				'wprss_c_setting_license_activation_callback',
				'wprss_settings_license_keys',
				'wprss_settings_c_licenses_section'
			);
		}

	}


    //add_action( 'wprss_add_settings_fields_sections', 'wprss_c_add_settings_fields_sections', 10, 1 );
    /** 
     * Add settings fields and sections for categories
     *
     * @since 1.0
     */
   /* function wprss_c_add_settings_fields_sections( $active_tab ) {
            
        if ( $active_tab == 'c_settings' ) {         
            settings_fields( 'wprss_settings_c' );
            do_settings_sections( 'wprss_settings_c' ); 
        }
    }*/


    /** 
     * Draw the licenses settings section header
     * @since 1.0
     */
    function wprss_c_settings_license_callback() {
        //  echo '<p>' . ( 'License details' ) . '</p>';
    }  


    /** 
     * Set license
     * @since 1.0
     */
    function wprss_c_setting_license_callback( $args ) {
        $license_keys = get_option( 'wprss_settings_license_keys' ); 
        $c_license_key = ( isset( $license_keys['c_license_key'] ) ) ? $license_keys['c_license_key'] : FALSE;      
        echo "<input id='wprss-c-license-key' name='wprss_settings_license_keys[c_license_key]' type='text' value='" . esc_attr( $c_license_key ) ."' />";
        echo "<label class='description' for='wprss-c-license-key'>" . __( 'Enter your license key', WPRSS_TEXT_DOMAIN ) . '</label>';                   
    }    


    /** 
     * License activation button and indicator
     * @since 1.0
     */
    function wprss_c_setting_license_activation_callback( $args ) {
        $license_keys = get_option( 'wprss_settings_license_keys' ); 
        $license_statuses = get_option( 'wprss_settings_license_statuses' ); 
        $c_license_key = ( isset( $license_keys['c_license_key'] ) ) ? $license_keys['c_license_key'] : FALSE;
        $c_license_status = ( isset( $license_statuses['c_license_status'] ) ) ? $license_statuses['c_license_status'] : FALSE;
    
		if ( $c_license_status != FALSE && $c_license_status == 'valid' ) { ?>
            <span style="color:green;"><?php _e( 'active', WPRSS_TEXT_DOMAIN ); ?></span>
            <?php wp_nonce_field( 'wprss_c_license_nonce', 'wprss_c_license_nonce' ); ?>
            <input type="submit" class="button-secondary" name="wprss_c_license_deactivate" value="<?php _e( 'Deactivate License', WPRSS_TEXT_DOMAIN ); ?>"/>
        <?php } 
        else {
            wp_nonce_field( 'wprss_c_license_nonce', 'wprss_c_license_nonce' ); ?>
            <input type="submit" class="button-secondary" name="wprss_c_license_activate" value="<?php _e( 'Activate License', WPRSS_TEXT_DOMAIN ); ?>"/>
        <?php }
    }


    add_filter( 'wprss_show_settings_tabs_condition', 'wprss_c_force_show_settings_tab' );
    /**
     * Alters the boolean flag that determines whether or not the settings tabs are to be shown.
     *
     * @see wp-rss-aggregator/includes/admin-options.php => wprss_settings_page_display()
     * @since 1.0
     */
    function wprss_c_force_show_settings_tab( $boolean ) {
        return TRUE;
    }
