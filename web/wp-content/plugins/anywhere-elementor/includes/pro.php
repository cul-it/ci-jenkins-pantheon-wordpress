<?php

if ( ! function_exists( 'wpv_ae' ) ) {
    // Create a helper function for easy SDK access.
    function wpv_ae() {
        global $wpv_ae;

        if ( ! isset( $wpv_ae ) ) {
            // Include Freemius SDK.
            require_once WTS_AE_PATH . '/freemius/start.php';

            $wpv_ae = fs_dynamic_init( array(
                'id'                  => '8276',
                'slug'                => 'anywhere-elementor',
                'type'                => 'plugin',
                'public_key'          => 'pk_43d5f405cc0b87b346911199cff66',
                'is_premium'          => false,
                'premium_suffix'      => 'Pro',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'edit.php?post_type=ae_global_templates',
                    'first-path'     => 'edit.php?post_type=ae_global_templates',
                    'support'        => true,
                    'account'        => false
                ),
                
            ) );
        }

        return $wpv_ae;
    }

    // Init Freemius.
    wpv_ae();
    // Signal that SDK was initiated.
    do_action( 'wpv_ae_loaded' );
}
