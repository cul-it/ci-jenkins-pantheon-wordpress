<?php

/**
 * Created by PhpStorm.
 * User: sujin
 * Date: 02/03/16
 * Time: 7:29 PM
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackUCIUnInstall {
	/**
	 * UnInstall UCI Pro.
	 */
	public static function uninstall() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$ucisettings = get_option('sm_uci_pro_settings');
		$droptable = isset($ucisettings['drop_table']) ? $ucisettings['drop_table'] : '';
		if(!empty($droptable) && $droptable == 'on'){
			SmackUCIInstall::remove_options();
			$tables[] = 'drop table smack_field_types';
			$tables[] = 'drop table smackuci_events';
			$tables[] = 'drop table smackuci_history';
			$tables[] = 'drop table wp_ultimate_csv_importer_acf_fields';
			$tables[] = 'drop table wp_ultimate_csv_importer_external_file_schedules';
			$tables[] = 'drop table wp_ultimate_csv_importer_ftp_schedules';
			$tables[] = 'drop table wp_ultimate_csv_importer_log_values';
			$tables[] = 'drop table wp_ultimate_csv_importer_manageshortcodes';
			$tables[] = 'drop table wp_ultimate_csv_importer_mappingtemplate';
			$tables[] = 'drop table wp_ultimate_csv_importer_scheduled_import';
			$tables[] = 'drop table wp_ultimate_csv_importer_shortcodes_statusrel';
			$tables[] = 'drop table wp_ultimate_csv_importer_uploaded_file_schedules';
			$tables[] = 'drop table wp_ultimate_csv_importer_scheduled_export';

			foreach($tables as $table) {
				$wpdb->query($table, array());
			}
		}
	}
}
