<?php

// Enqueue admin_init hook after licensing system initialization
add_action( 'wprss_init_licensing', function() {
	add_action( 'admin_init', 'wprss_kf_init_updater' );
});

add_filter( 'wprss_register_addon', function($addons) {
	$addons['kf'] = WPRSS_KF_SL_ITEM_NAME;
	return $addons;
});

/**
 * Creates and initializes the updater for this addon.
 *
 * @uses Aventura\Wprss\Core\Licensing\Manager::initUpdaterInstance() To initialize the updater instance
 */
function wprss_kf_init_updater() {
	if ( method_exists(wprss_licensing_get_manager(), 'initUpdaterInstance') ) {
		wprss_licensing_get_manager()->initUpdaterInstance('kf', WPRSS_KF_SL_ITEM_NAME, WPRSS_KF_VERSION, WPRSS_KF_PATH, WPRSS_KF_SL_STORE_URL);
	}
}
