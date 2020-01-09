<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/tap87
 * @since      1.0.0
 *
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/includes
 * @author     Tahir Poduska <tahir.poduska@cornell.edu>
 */
class Wp_Libcal_Hours_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_transient( 'libcal_hours_today_data' );
	}

}
