<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/kek245
 * @since      1.0.0
 *
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/includes
 * @author     Kevin Kidwell <kek245@cornell.edu>
 */
class Wp_Libcal_Hours_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-libcal-hours',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
