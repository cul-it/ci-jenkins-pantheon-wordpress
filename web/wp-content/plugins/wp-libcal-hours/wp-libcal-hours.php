<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/tap87
 * @since             1.0.0
 * @package           Wp_Libcal_Hours
 *
 * @wordpress-plugin
 * Plugin Name:       LibCal Hours
 * Plugin URI:        https://github.com/cul-it/wp-libcal-hours
 * Description:       Display library hours from LibCal.
 * Version:           1.0.0
 * Author:            Tahir Poduska
 * Author URI:        https://github.com/tap87
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-libcal-hours
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-libcal-hours-activator.php
 */
function activate_wp_libcal_hours() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-libcal-hours-activator.php';
	Wp_Libcal_Hours_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-libcal-hours-deactivator.php
 */
function deactivate_wp_libcal_hours() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-libcal-hours-deactivator.php';
	Wp_Libcal_Hours_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_libcal_hours' );
register_deactivation_hook( __FILE__, 'deactivate_wp_libcal_hours' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-libcal-hours.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_libcal_hours() {

	$plugin = new Wp_Libcal_Hours();
	$plugin->run();

}
run_wp_libcal_hours();
