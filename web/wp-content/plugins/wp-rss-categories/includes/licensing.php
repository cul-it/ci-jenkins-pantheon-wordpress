<?php

/**
 * Registers the add-on in the core.
 *
 * @since 1.3
 * @param array $addons The registered add-ons
 * @return array The registered add-ons, now also including an entry for this addon.
 */
add_filter( 'wprss_register_addon', function($addons) {
	$addons['c'] = WPRSS_C_SL_ITEM_NAME;
	return $addons;
});

// Enqueue admin_init hook after licensing system initialization
add_action( 'wprss_init_licensing', function() {
	add_action( 'admin_init', 'wprss_c_init_updater' );
});

/**
 * Creates and initializes the updater for this addon.
 *
 * @uses Aventura\Wprss\Core\Licensing\Manager::initUpdaterInstance() To initialize the updater instance
 */
function wprss_c_init_updater() {
	if ( method_exists(wprss_licensing_get_manager(), 'initUpdaterInstance') ) {
		wprss_licensing_get_manager()->initUpdaterInstance('c', WPRSS_C_SL_ITEM_NAME, WPRSS_C_VERSION, WPRSS_C_PATH, WPRSS_C_SL_STORE_URL);
	}
}
