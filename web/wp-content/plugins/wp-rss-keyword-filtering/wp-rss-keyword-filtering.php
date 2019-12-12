<?php
/**
 * Plugin Name: WP RSS Aggregator - Keyword Filtering
 * Plugin URI: https://www.wprssaggregator.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpraplugin
 * Description: Adds keyword filtering capabilities to WP RSS Aggregator.
 * Version: 1.6.3
 * Author: RebelCode
 * Author URI: https://www.wprssaggregator.com
 * Text Domain: wprss
 * Domain Path: /languages/
 * License: GPLv3
 */

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
if (!defined('WPRSS_KF_VERSION')) {
    define('WPRSS_KF_VERSION', '1.6.3');
}

/* Set the database version number of the plugin. */
if (!defined('WPRSS_KF_DB_VERSION')) {
    define('WPRSS_KF_DB_VERSION', '1');
}

/* The minimum version of core required */
if (!defined('WPRSS_KF_CORE_MIN_VERSION')) {
    define('WPRSS_KF_CORE_MIN_VERSION', '4.8');
}

/* Set constant path to the plugin directory. */
if (!defined('WPRSS_KF_DIR')) {
    define('WPRSS_KF_DIR', plugin_dir_path(__FILE__));
}

/* Set constant path to the plugin includes directory. */
if (!defined('WPRSS_KF_INC_DIR')) {
    define('WPRSS_KF_INC_DIR', WPRSS_KF_DIR . 'includes/');
}

/* Set constant URI to the plugin URL. */
if (!defined('WPRSS_KF_URI')) {
    define('WPRSS_KF_URI', plugin_dir_url(__FILE__));
}

/* Set constant path to the main plugin file. */
if (!defined('WPRSS_KF_PATH')) {
    define('WPRSS_KF_PATH', __FILE__);
}

/* Set constant store URL */
if (!defined('WPRSS_KF_SL_STORE_URL')) {
    define('WPRSS_KF_SL_STORE_URL', 'https://www.wprssaggregator.com/edd-sl-api/');
}

/* Set the constant item name of the plugin */
if (!defined('WPRSS_KF_SL_ITEM_NAME')) {
    define('WPRSS_KF_SL_ITEM_NAME', 'Keyword Filtering');
}

/* Set the constants for filtering modes */
if (!defined('WPRSS_KF_NORMAL_FILTER_MODE')) {
    define('WPRSS_KF_NORMAL_FILTER_MODE', 0);
}

if (!defined('WPRSS_KF_NOT_FILTER_MODE')) {
    define('WPRSS_KF_NOT_FILTER_MODE', 1);
}

// Adding autoload paths
add_action('plugins_loaded', function () {
    $coreActive = defined('WPRSS_VERSION');

    if (!$coreActive || version_compare(WPRSS_VERSION, WPRSS_KF_CORE_MIN_VERSION, '<')) {
        add_action('admin_notices', 'wprss_kf_missing_core_notice');

        return;
    }

    define('WPRSS_KF_ACTIVE', true);

    wprss_autoloader()->add('Aventura\\Wprss\\KeywordFiltering', WPRSS_KF_INC_DIR);

    wprss_kf_load_textdomain();

    // Load licensing loader file
    require_once WPRSS_KF_INC_DIR . 'licensing.php';
    /* Load required files */
    require_once WPRSS_KF_INC_DIR . 'addon-module.php';
    require_once WPRSS_KF_INC_DIR . 'assets.php';
    require_once WPRSS_KF_INC_DIR . 'functionality.php';
    require_once WPRSS_KF_INC_DIR . 'admin-settings.php';
    require_once WPRSS_KF_INC_DIR . 'admin-metaboxes.php';
});

register_activation_hook(__FILE__, 'wprss_kf_activate');
/**
 * Plugin activation procedure
 *
 * @since  1.0
 */
function wprss_kf_activate()
{
    /* Prevents activation of plugin if compatible version of WordPress not found */
    if (version_compare(get_bloginfo('version'), '3.3', '<')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        deactivate_plugins(basename(__FILE__));     // Deactivate plugin
        wp_die(
            __('The <b>WP RSS Aggregator - Keyword Filtering</b> add-on requires WordPress at version <b>3.3</b> or later.'),
            'WP RSS Aggregator - Keyword Filtering',
            array('back_link' => true)
        );
    }

    // Add the database version setting.
    update_option('wprss_kf_db_version', WPRSS_KF_DB_VERSION);
}

/**
 * Loads the plugin's translated strings.
 *
 * @since  1.5.2
 */
function wprss_kf_load_textdomain()
{
    load_plugin_textdomain('wprss', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

/**
 * Shows an error if WP RSS Aggregator is not updated to the latest version.
 *
 * @since 1.0
 */
function wprss_kf_missing_core_notice()
{
    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        sprintf(
            __(
                'The <b>WP RSS Aggregator - Keyword Filtering</b> add-on requires the <b>WP RSS Aggregator</b> plugin to be installed and activated, at version <b>%s</b> or higher.',
                'wprss'
            ),
            '<b>' . WPRSS_KF_CORE_MIN_VERSION . '</b>'
        )
    );
}
