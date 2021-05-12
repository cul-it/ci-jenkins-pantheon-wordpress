<?php

/**
 * Plugin Name: WP RSS Aggregator - Categories
 * Plugin URI: https://www.wprssaggregator.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpraplugin
 * Description: Adds categories capability to WP RSS Aggregator.
 * Version: 1.3.3
 * Author: RebelCode
 * Author URI: https://www.wprssaggregator.com
 * Text Domain: wprss
 * Domain Path: /languages/
 * License: GPLv3
 */

use RebelCode\Wpra\Categories\Modules\AddonModule;
use RebelCode\Wpra\Categories\Modules\TemplatesModule;

/**
 * Copyright (C) 2012-2019 RebelCode Ltd.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* Set the version number of the plugin. */
if (!defined('WPRSS_C_VERSION')) {
    define('WPRSS_C_VERSION', '1.3.3');
}

/* The minimum version of core required */
if (!defined('WPRSS_CAT_CORE_MIN_VERSION')) {
    define('WPRSS_CAT_CORE_MIN_VERSION', '4.8');
}

/* Set the database version number of the plugin. */
if (!defined('WPRSS_C_DB_VERSION')) {
    define('WPRSS_C_DB_VERSION', '3');
}

// Set constant path to the plugin directory.
if (!defined('WPRSS_C_DIR')) {
    define('WPRSS_C_DIR', plugin_dir_path(__FILE__));
}

// Set constant URI to the plugin URL.
if (!defined('WPRSS_C_URI')) {
    define('WPRSS_C_URI', plugin_dir_url(__FILE__));
}

/* Set constant path to the main plugin file. */
if (!defined('WPRSS_C_PATH')) {
    define('WPRSS_C_PATH', __FILE__);
}

// Set the constant path to the plugin's includes directory.
if (!defined('WPRSS_C_INC')) {
    define('WPRSS_C_INC', WPRSS_C_DIR . trailingslashit('includes'));
}

define('WPRSS_C_SL_STORE_URL', 'https://www.wprssaggregator.com/edd-sl-api/');

define('WPRSS_C_SL_ITEM_NAME', 'Categories');

/**
 * Load required files.
 */

// Adding autoload paths
add_action('plugins_loaded', function () {
    $coreActive = defined('WPRSS_VERSION');

    if (!$coreActive || version_compare(WPRSS_VERSION, WPRSS_CAT_CORE_MIN_VERSION, '<')) {
        add_action('admin_notices', 'wprss_cat_missing_core_notice');

        return;
    }

    // Load Composer autoloader if it exists
    if (file_exists(WPRSS_C_DIR . 'vendor/autoload.php')) {
        require_once WPRSS_C_DIR . 'vendor/autoload.php';
    }

    // Make sure the addon module exists
    define('WPRSS_CAT_ACTIVE', class_exists('RebelCode\Wpra\Categories\Modules\AddonModule'));

    if (!WPRSS_CAT_ACTIVE) {
        return;
    }

    wprss_cat_load_textdomain();

    // Load licensing loader file
    require_once WPRSS_C_INC . 'licensing.php';
    // Load required files
    require_once WPRSS_C_INC . 'functionality.php';
    require_once WPRSS_C_INC . 'custom-taxonomy.php';
    require_once WPRSS_C_INC . 'admin-settings.php';
    require_once WPRSS_C_INC . 'admin-display.php';

    // Load modules
    wpra_load_module('categories/addon', new AddonModule());
    wpra_load_module('categories/templates', new TemplatesModule());
});

register_activation_hook(__FILE__, 'wprss_cat_activate');
/**
 * Plugin activation procedure
 *
 * @since  1.0
 * @return void
 */
function wprss_cat_activate()
{
    /* Prevents activation of plugin if compatible version of WordPress not found */
    if (version_compare(get_bloginfo('version'), '3.3', '<')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        deactivate_plugins(basename(__FILE__));     // Deactivate plugin
        wp_die(__('This plugin requires WordPress version 3.3 or higher.'), 'WP RSS Aggregator Categories',
            array('back_link' => true));
    }

    // Add the database version setting.
    update_option('wprss_c_db_version', WPRSS_C_DB_VERSION);
    update_option('wprss_c_check_existing_feeds', '1');

    if (!defined('WPRSS_VERSION')) {
        return;
    }

    if (function_exists('wprss_c_licenses_settings_initialize')) {
        wprss_c_licenses_settings_initialize();
    }

    // Register the custom taxonomy and flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Loads the plugin's translated strings.
 *
 * @since  1.2.9
 * @return void
 */
function wprss_cat_load_textdomain()
{
    load_plugin_textdomain('wprss', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

/**
 * Throw an error if WP RSS Aggregator is not installed.
 *
 * @since 1.0
 */
function wprss_cat_missing_core_notice()
{
    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        sprintf(
            __(
                'The <b>WP RSS Aggregator - Categories</b> add-on requires the <b>WP RSS Aggregator</b> plugin to be installed and activated, at version <b>%s</b> or higher.',
                'wprss'
            ),
            '<b>' . WPRSS_CAT_CORE_MIN_VERSION . '</b>'
        )
    );
}
