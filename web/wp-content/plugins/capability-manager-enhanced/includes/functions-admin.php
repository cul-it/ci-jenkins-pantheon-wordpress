<?php

// perf enhancement: display submenu links without loading framework and plugin code
function cme_submenus() {
	$cap_name = ( is_super_admin() ) ? 'manage_capabilities' : 'restore_roles';

    $permissions_title = __('Capabilities', 'capsman-enhanced');

    $menu_order = 72;

    if (defined('PUBLISHPRESS_PERMISSIONS_MENU_GROUPING')) {
        foreach (get_option('active_plugins') as $plugin_file) {
            if ( false !== strpos($plugin_file, 'publishpress.php') ) {
                $menu_order = 27;
            }
        }
    }

    add_menu_page(
        $permissions_title,
        $permissions_title,
        $cap_name,
        'capsman',
        'cme_fakefunc',
        'dashicons-admin-network',
		$menu_order
    );

    add_submenu_page('capsman',  __('Backup', 'capsman-enhanced'), __('Backup', 'capsman-enhanced'), $cap_name, 'capsman' . '-tool', 'cme_fakefunc');
}
