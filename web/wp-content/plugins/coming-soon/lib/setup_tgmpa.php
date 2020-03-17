<?php
    /**
	 * Recommend WPForms Lite using TGM Activation.
	 *
	 * @since 5.0.20
	 */
	function coming_soon_init_recommendations() {
		// Recommend only for new installs.
		if ( ! coming_soon_is_new_install() ) {
			return;
		}
		// Specify a plugin that we want to recommend.
		$plugins = apply_filters( 'coming_soon_recommendations_plugins', array(
			array(
				'name'        => 'RafflePress',
				'slug'        => 'rafflepress',
				'required'    => false,
				'is_callable' => 'rafflepress', // This will target the Pro version as well, not only the one from WP.org repository.
			),
		) );
		/*
		 * Array of configuration settings.
		 */
		$config = apply_filters( 'coming_soon_recommendations_config', array(
			'id'           => 'coming-soon',          // Unique ID for hashing notices for multiple instances of TGMPA.
			'menu'         => 'coming-soon-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
				/* translators: 1: plugin name(s). */
				'notice_can_install_recommended'  => _n_noop(
					'Thanks for using Coming Soon Page & Maintenance Mode by SeedProd. We also recommend using %1$s. It\'s the best Giveaway and Contest plugin to help you grow your email list and social media followings.',
					'Thanks for using Coming Soon Page & Maintenance Mode by SeedProd. We also recommend using %1$s. It\'s the best Giveaway and Contest plugin to help you grow your email list and social media followings.',
					'coming-soon'
				),
				/* translators: 1: plugin name(s). */
				'notice_can_activate_recommended' => _n_noop(
					'Thanks for using Coming Soon Page & Maintenance Mode by SeedProd. We also recommend using %1$s. It\'s the best Giveaway and Contest plugin to help you grow your email list and social media followings.',
					'Thanks for using Coming Soon Page & Maintenance Mode by SeedProd. We also recommend using %1$s. It\'s the best Giveaway and Contest plugin to help you grow your email list and social media followings.',
					'coming-soon'
				),
				'install_link'                    => _n_noop(
					'Install RafflePress Now',
					'Begin installing plugins',
					'coming-soon'
				),
				'activate_link'                   => _n_noop(
					'Activate RafflePress',
					'Begin activating plugins',
					'coming-soon'
				),
				'nag_type'                        => 'notice-info',
			),
		) );
		\ComingSoon\tgmpa( (array) $plugins, (array) $config );
    }

    function coming_soon_is_new_install() {
		/*
		 * No previously installed 0.*.
		 * 'wp_mail_smtp_initial_version' option appeared in 1.3.0. So we make sure it exists.
		 * No previous plugin upgrades.
		 */
		if (
			get_option( 'seed_csp4_initial_version', false ) &&
			version_compare( SEED_CSP4_VERSION, get_option( 'seed_csp4_initial_version' ), '=' )
		) {
			return true;
		}

		return false;
    }
    
    function coming_soon_wpforms_upgrade_link( $medium ) {
        // track cross referrals to Awesome Motive products
        $medium = 'seedprod';
        return $medium;
	}
	
	$seed_csp4_wpforms = get_option('seed_csp4_wpforms');
    if(!empty($seed_csp4_wpforms)){
        add_filter( 'wpforms_upgrade_link_medium', 'coming_soon_wpforms_upgrade_link' );
    }
