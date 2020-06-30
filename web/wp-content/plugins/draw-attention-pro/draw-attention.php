<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   DrawAttention
 * @author    Nathan Tyler <support@tylerdigital.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Tyler Digital
 *
 * @wordpress-plugin
 * Plugin Name:       Draw Attention Pro
 * Plugin URI:        https://wpdrawattention.com
 * Description:       Create interactive images in WordPress
 * Version:           1.9.12
 * Author:            N Squared
 * Author URI:        https://nsqua.red
 * Text Domain:       drawattention
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/tylerdigital/drawattention/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function da_deactivate_free_version() {
	if ( !function_exists( 'deactivate_plugins' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	deactivate_plugins( 'draw-attention/draw-attention.php' );
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-drawattention.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-drawattention.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace DrawAttention with the name of the class defined in
 *   `class-drawattention.php`
 */
register_activation_hook( __FILE__, array( 'DrawAttention', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'DrawAttention', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace DrawAttention with the name of the class defined in
 *   `class-drawattention.php`
 */
add_action( 'plugins_loaded', array( 'DrawAttention', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-drawattention-admin.php` with the name of the plugin's admin file
 * - replace DrawAttention_Admin with the name of the class defined in
 *   `class-drawattention-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-drawattention-admin.php' );
	add_action( 'plugins_loaded', array( 'DrawAttention_Admin', 'get_instance' ) );

}
