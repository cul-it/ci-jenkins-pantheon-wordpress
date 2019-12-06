<?php

/**
 * Plugin Name: WP RSS Aggregator - Templates
 * Plugin URI: https://www.wprssaggregator.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpraplugin
 * Description: Adds premium templates to WP RSS Aggregator.
 * Version: 0.1
 * Author: RebelCode
 * Author URI: https://www.wprssaggregator.com
 * Text Domain: wprss
 * Domain Path: /languages/
 * License: GPLv3
 */

if (!defined('WPRSS_TEMPLATES')) {
    define('WPRSS_TEMPLATES', __FILE__);
}

if (!defined('WPRSS_TEMPLATES_ADDON_NAME')) {
    define('WPRSS_TEMPLATES_ADDON_NAME', 'WP RSS Aggregator - Templates');
}

if (!defined('WPRSS_TEMPLATES_CORE_NAME')) {
    define('WPRSS_TEMPLATES_CORE_NAME', 'WP RSS Aggregator');
}

if (!defined('WPRSS_TEMPLATES_MIN_PHP_VERSION')) {
    define('WPRSS_TEMPLATES_MIN_PHP_VERSION', '5.4');
}

if (!defined('WPRSS_TEMPLATES_MIN_WP_VERSION')) {
    define('WPRSS_TEMPLATES_MIN_WP_VERSION', '4.8');
}

if (!defined('WPRSS_TEMPLATES_MIN_WPRA_VERSION')) {
    define('WPRSS_TEMPLATES_MIN_WPRA_VERSION', '4.15');
}

// The licensing system in Core depends on constants in the form of "WPRSS_%s_SL_ITEM_NAME" and "WPRSS_%s_SL_STORE_URL"
// Once that system is reworked, these constants should be removed
if (!defined('WPRSS_TMP_SL_ITEM_NAME')) {
    define('WPRSS_TMP_SL_ITEM_NAME', 'Templates');
}
if (!defined('WPRSS_TMP_SL_STORE_URL')) {
    define('WPRSS_TMP_SL_STORE_URL', 'https://www.wprssaggregator.com/edd-sl-api/');
}

// Set constant URI to the plugin URL.
// The template type classes use this. Should be removed when those classes get injected with the asset objects
if (!defined('WPRSS_TEMPLATES_URL')) {
    define('WPRSS_TEMPLATES_URL', plugin_dir_url(__FILE__));
}

// Stop if PHP version is not satisfied
if (version_compare(PHP_VERSION, WPRSS_TEMPLATES_MIN_PHP_VERSION, '<')) {
    return;
}

// Loads the autoloader if it exists
$autoloadFile = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadFile)) {
    require_once $autoloadFile;
}

register_activation_hook(__FILE__, 'wpra_templates_activate');
register_deactivation_hook(__FILE__, 'wpra_templates_deactivate');

/**
 * Register the addon's modules with WP RSS Aggregator
 *
 * @since 0.1
 *
 * @param array $modules The original list of modules.
 *
 * @return array The filtered list of modules.
 */
function wpra_templates_register_modules($modules) {
    $modules['templates/addon'] = new RebelCode\Wpra\Templates\AddonModule(__FILE__);
    $modules['templates/licensing'] = new RebelCode\Wpra\Templates\LicensingModule();

    return $modules;
}

// Check for WPRA and its version
// @since 0.1
add_action('plugins_loaded', function () {
    if (!defined('WPRSS_VERSION')) {
        add_action('admin_notices', function () {
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                sprintf(
                    __('You need to install and activate the %1$s plugin for the %2$s addon to work.', 'wprss'),
                    sprintf('<b>%s</b>', WPRSS_TEMPLATES_CORE_NAME),
                    sprintf('<b>%s</b>', WPRSS_TEMPLATES_ADDON_NAME)
                )
            );
        });

        return;
    }

    if (version_compare(WPRSS_VERSION, WPRSS_TEMPLATES_MIN_WPRA_VERSION, '<')) {
        add_action('admin_notices', function () {
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                sprintf(
                    __('You need to update the %1$s plugin to at least version %2$s for the %3$s addon to work.', 'wprss'),
                    sprintf('<b>%s</b>', WPRSS_TEMPLATES_CORE_NAME),
                    sprintf('<b>%s</b>', WPRSS_TEMPLATES_MIN_WPRA_VERSION),
                    sprintf('<b>%s</b>', WPRSS_TEMPLATES_ADDON_NAME)
                )
            );
        });

        return;
    }

    // Register the modules only if all dependencies are satisfied
    add_filter('wpra_plugin_modules', 'wpra_templates_register_modules');
});

/**
 * The deactivation handler for the WP RSS Aggregator Templates addon.
 *
 * @since 0.1
 */
function wpra_templates_activate()
{
    if (version_compare(get_bloginfo('version'), WPRSS_TEMPLATES_MIN_WP_VERSION, '<')) {
        deactivate_plugins(basename(__FILE__));
        wp_die(
            sprintf(
                __('The %1$s plugin requires WordPress at version %2$s or higher.', 'wprss'),
                sprintf('<b>%s</b>', WPRSS_TEMPLATES_ADDON_NAME),
                sprintf('<b>%s</b>', WPRSS_TEMPLATES_MIN_WP_VERSION)
            ),
            WPRSS_TEMPLATES_ADDON_NAME,
            array('back_link' => true)
        );
    }
}

/**
 * The deactivation handler for the WP RSS Aggregator Templates addon.
 *
 * @since 0.1
 */
function wpra_templates_deactivate()
{
}
