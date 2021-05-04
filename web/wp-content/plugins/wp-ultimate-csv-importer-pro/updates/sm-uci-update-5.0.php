<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
function smackuci_upgrade_schema() {
	global $wpdb;

	$collate = '';

	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	$tables = array(
			"CREATE TABLE IF NOT EXISTS  `wp_ultimate_csv_importer_log_values` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`eventKey` varchar(50) NOT NULL,
			`recordId` int(10) NOT NULL,
			`module` varchar(50) NOT NULL,
			`method_of_import` varchar(50) NOT NULL,
			`log_message` blob NOT NULL,
			`imported_time` varchar(100) NOT NULL,
			`mode_of_import` varchar(100) NOT NULL,
			`sequence` varchar(100) NOT NULL,
			`status` varchar(100) NOT NULL,
			`assigned_user_id` int(10) NOT NULL,
			`imported_by` int(100) NOT NULL,
			PRIMARY KEY (`id`)
		       ) $collate;",
		       "CREATE TABLE IF NOT EXISTS `smackuci_events` (
		       `id` bigint(20) NOT NULL AUTO_INCREMENT,
		       `revision` bigint(20) NOT NULL default 0,
		       `name` varchar(255),
		       `original_file_name` varchar(255),
		       `friendly_name` varchar(255),
		       `import_type` varchar(32),
		       `filetype` text,
		       `filepath` text,
		       `eventKey` varchar(32),
		       `registered_on` datetime NOT NULL default '0000-00-00 00:00:00',
		       `parent_node` varchar(255),
		       `processing` tinyint(1) NOT NULL default 0,
		       `executing` tinyint(1) NOT NULL default 0,
		       `triggered` tinyint(1) NOT NULL default 0,
		       `event_started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		       `count` bigint(20) NOT NULL default 0,
		       `processed` bigint(20) NOT NULL default 0,
		       `created` bigint(20) NOT NULL default 0,
		       `updated` bigint(20) NOT NULL default 0,
		       `skipped` bigint(20) NOT NULL default 0,
		       `deleted` bigint(20) NOT NULL default 0,
		       `is_terminated` tinyint(1) NOT NULL default 0,
		       `terminated_on` datetime NOT NULL default '0000-00-00 00:00:00',
		       `last_activity` datetime NOT NULL default '0000-00-00 00:00:00',
		       `siteid` int(11) NOT NULL DEFAULT 1,
		       `month` varchar(60) DEFAULT NULL,
		       `year` varchar(60) DEFAULT NULL,
		       PRIMARY KEY ( id )
			       ) $collate;",
		       "CREATE TABLE IF NOT EXISTS `smackuci_history` (
			       `id` bigint(20) NOT NULL AUTO_INCREMENT,
		       `event_id` bigint(20) NOT NULL,
		       `time_taken` text,
		       `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		       `summary` text,
		       PRIMARY KEY (id)
			       ) $collate;",
		       );

	return $tables;
}

// We assign the default option data for the fresh instalization
$settings = array('debug_mode' => 'off',
		'send_log_email' => 'on',
		'drop_table' => 'off',
		'author_editor_access' => 'off',
		'woocomattr' => 'off'
		);

add_option('sm_uci_pro_settings', $settings);
global $wpdb;
$affected_records = '';
$upload_dir = wp_upload_dir();

$wpdb->hide_errors();

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

foreach(smackuci_upgrade_schema() as $table) {
	dbDelta($table);
}

$upload_dir = wp_upload_dir();

if(!is_dir($upload_dir['basedir'] . '/smack_uci_uploads/imports')) {
	wp_mkdir_p( $upload_dir['basedir'] . '/smack_uci_uploads/imports' );
	@chmod($upload_dir['basedir'] . '/smack_uci_uploads/imports', 0777);
}

$wpdb->query("ALTER TABLE `{$wpdb->prefix}ultimate_csv_importer_mappingtemplate` MODIFY COLUMN csvname VARCHAR (150) DEFAULT NULL;");
$wpdb->query("ALTER TABLE `{$wpdb->prefix}ultimate_csv_importer_scheduled_import` ADD COLUMN `file_type` varchar(10) NOT NULL after module;");
$wpdb->query("ALTER TABLE `{$wpdb->prefix}ultimate_csv_importer_scheduled_import` ADD COLUMN `event_key` varchar(100) DEFAULT NULL after version;");
$wpdb->query("ALTER TABLE `{$wpdb->prefix}ultimate_csv_importer_scheduled_import` ADD COLUMN `duplicate_headers` blob DEFAULT NULL after import_mode");
$wpdb->query("ALTER TABLE `{$wpdb->prefix}ultimate_csv_importer_scheduled_import` DROP COLUMN `imported_as`");

// Migrating from "smack_dashboard_manager" table into "smackuci_events"
$get_manager_records = $wpdb->get_results($wpdb->prepare("select *from smack_dashboard_manager", array()));
if(!empty($get_manager_records)) {
	foreach ( $get_manager_records as $index => $data ) {
		$get_versions  = maybe_unserialize( $data->version );
		$get_file_name = explode( '-', $data->csv_name );
		$get_file_type = wp_check_filetype( $data->csv_name, null );
		$file_type     = $get_file_type['ext'];
		$index_count   = count( $get_file_name );
		$get_site_id   = explode( '.', $get_file_name[ $index_count - 1 ] );
		$get_site_id   = explode( 'blog', $get_site_id[0] );
		$site_id       = $get_site_id[1];
		$index_count   = $index_count - 2;
		$file_name     = '';
		for ( $i = 0; $i < $index_count; $i ++ ) {
			$file_name .= $get_file_name[ $i ];
		}
		$name             = $file_name . '.' . $file_type;
		$event_started_at = $data->imported_on;
		$last_activity    = $data->modified_on;
		$imported_as      = $data->keyword;
		if ( $data->keyword == 'post' ) {
			$imported_as = 'Posts';
		} elseif ( $data->keyword == 'page' ) {
			$imported_as = 'Pages';
		} elseif ( $data->keyword == 'product' ) {
			if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
				$imported_as = 'WooCommerce';
			} elseif ( in_array( 'marketpress/marketpress.php', get_option( 'active_plugins' ) ) || in_array( 'wordpress-ecommerce/marketpress.php', get_option( 'active_plugins' ) ) ) {
				$imported_as = 'MarketPress';
			}
		} elseif ( $data->keyword == 'wpsc-product' ) {
			$imported_as = 'WPeCommerce';
		} elseif ( $data->keyword == 'Users' ) {
			$imported_as = 'Users';
		} elseif ( $data->keyword == 'Customer-Reviews' ) {
			$imported_as = 'CustomerReviews';
		}
		$get_affected_count = maybe_unserialize( $data->created_records );
		$affected_count     = ! empty( $get_affected_count ) ? count( $get_affected_count[ $data->keyword ] ) : 0;
		if ( isset( $get_affected_count[ $data->keyword ] ) ) {
			$affected_records = json_encode( $get_affected_count[ $data->keyword ] );
		}
		if(!empty($get_versions)) {
			foreach ( $get_versions as $key => $val ) {
				$get_event_key        = explode( '/', $val );
				$eventKey             = $get_event_key[ count( $get_event_key ) - 1 ];
				$get_available_events = $wpdb->get_results( $wpdb->prepare( "select revision from smackuci_events where name like %s ORDER BY revision DESC", $name ) );
				$revision             = 1;
				if ( ! empty( $get_available_events ) ) {
					$revision = $get_available_events[0]->revision;
					$revision = $revision + 1;
				}
				if ( ! is_dir( $upload_dir['basedir'] . '/smack_uci_uploads/imports' . '/' . $eventKey ) ) {
					wp_mkdir_p( $upload_dir['basedir'] . '/smack_uci_uploads/imports' . '/' . $eventKey );
					chmod( $upload_dir['basedir'] . '/smack_uci_uploads/imports' . '/' . $eventKey, 0777 );
				}
				$orig_file_path       = $upload_dir['basedir'] . '/ultimate_importer/' . $eventKey;
				$orig_log_path        = $upload_dir['basedir'] . '/ultimate_importer_logfiles/' . $eventKey . '.log';
				$new_file_path        = $upload_dir['basedir'] . '/smack_uci_uploads/imports' . '/' . $eventKey . '/' . $eventKey;
				$new_log_path         = $upload_dir['basedir'] . '/smack_uci_uploads/imports' . '/' . $eventKey . '/' . $eventKey . '.log';
				$original_file_name   = $file_name . '-' . $revision . '.' . $file_type;
				$affected_log_entries = $upload_dir['basedir'] . '/smack_uci_uploads/imports' . '/' . $eventKey . '/' . $eventKey . '.txt';

				$file_path = '/smack_uci_uploads/imports/' . $eventKey . '/' . $eventKey;

				if ( file_exists( $orig_file_path ) ) {
					copy( $orig_file_path, $new_file_path );
				}
				if ( file_exists( $orig_log_path ) ) {
					copy( $orig_log_path, $new_log_path );
				}

				// Write the contents to the file,
				file_put_contents( $affected_log_entries, $affected_records );

				$month = date( 'M', strtotime( $last_activity ) );
				$year  = date( 'Y', strtotime( $last_activity ) );

				$wpdb->insert( 'smackuci_events', array(
							'revision'           => $revision,
							'name'               => $name,
							'original_file_name' => $original_file_name,
							'import_type'        => $imported_as,
							'filetype'           => $file_type,
							'filepath'           => $file_path,
							'eventKey'           => $eventKey,
							'event_started_at'   => $event_started_at,
							'count'              => $affected_count,
							'created'            => $affected_count,
							'last_activity'      => $last_activity,
							'siteid'             => $site_id,
							'month'              => $month,
							'year'               => $year,
							), array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%d', '%s', '%d' ) );

			}
		}
	}
}

// Migrating 'wp_ultimate_csv_importer_scheduled_import' table

$get_scheduled_events = $wpdb->get_results($wpdb->prepare("select id, importid, module, file_type, version, importbymethod, import_mode from wp_ultimate_csv_importer_scheduled_import", array()));
if(!empty($get_scheduled_events)) {
	foreach ( $get_scheduled_events as $index => $event_data ) {
		$scheduled_event_id = $event_data->id;
		$module = $event_data->module;
		if ( $event_data->module == 'post' ) {
			$module = 'Posts';
		} elseif ( $event_data->module == 'page' ) {
			$module = 'Pages';
		} elseif ( $event_data->module == 'product' ) {
			if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
				$module = 'WooCommerce';
			} elseif ( in_array( 'marketpress/marketpress.php', get_option( 'active_plugins' ) ) || in_array( 'wordpress-ecommerce/marketpress.php', get_option( 'active_plugins' ) ) ) {
				$module = 'MarketPress';
			}
		} elseif ( $event_data->module == 'wpsc-product' ) {
			$module = 'WPeCommerce';
		} elseif ( $event_data->module == 'Users' ) {
			$module = 'Users';
		} elseif ( $event_data->module == 'Customer-Reviews' ) {
			$module = 'CustomerReviews';
		}

		$get_manager_info = $wpdb->get_results( $wpdb->prepare( "select csv_name, version from smack_dashboard_manager where id = %d", $event_data->importid ) );
		if ( ! empty( $get_manager_info ) ) {
			$get_file_type = wp_check_filetype( $get_manager_info[0]->csv_name, null );
			$file_type     = $get_file_type['ext'];

			$get_versions = maybe_unserialize( $get_manager_info[0]->version );
			foreach ( $get_versions as $key => $val ) {
				if ( $key == $event_data[0]->version ) {
					$get_event_key        = explode( '/', $val );
					$eventKey             = $get_event_key[ count( $get_event_key ) - 1 ];
					$get_available_events = $wpdb->get_results( $wpdb->prepare( "select id, revision from smackuci_events where eventKey like %s", $eventKey ) );
					$importid             = $get_available_events[0]->id;
					$revision             = $get_available_events[0]->revision;
				}
			}
			$import_method = $event_data->importbymethod;
			if ( $import_method == 'dwnldextrfile' ) {
				$import_method = 'server';
			} elseif ( $import_method == 'uploadfilefromcomputer' ) {
				$import_method = 'desktop';
			} elseif ( $import_method == 'dwnldftpfile' ) {
				$import_method = 'ftp';
			} elseif ( $import_method == 'fromexternalurl' ) {
				$import_method = 'url';
			}

			$schedule_table = $wpdb->prefix . "ultimate_csv_importer_scheduled_import";
			$wpdb->update( $schedule_table , array(
						'importid'       => $importid,
						'module'         => $module,
						'file_type'      => $file_type,
						'version'        => $revision,
						'importbymethod' => $import_method
						), array( 'id' =>  $scheduled_event_id ) );
		}
	}
}

// Remove unwanted tables

$wpdb->query("drop table smack_csv_manager;");
$wpdb->query("drop table SmackUCI_manage_records;");
$wpdb->query("drop table SmackUCI_event_informations;");
$wpdb->query("drop table smackcsv_status_log;");
$wpdb->query("drop table {$wpdb->prefix}ultimate_csv_importer_filemanager;");
$wpdb->query("drop table {$wpdb->prefix}ultimate_csv_importer_multisite_details;");
$wpdb->query("drop table {$wpdb->prefix}ultimate_csv_importer_eventkey_manager;");
$wpdb->query("drop table {$wpdb->prefix}ultimate_csv_importer_exclusion_lists;");

// Move unwanted files
$old_files_dir = plugin_dir_path(__FILE__) . '/old_files';
if(!is_dir($old_files_dir)) {
	wp_mkdir_p( $old_files_dir );
	@chmod($old_files_dir, 0777);
}
@rename(plugin_dir_path(__FILE__) . '/Ultimatecsvimporter.pot', $old_files_dir . '/Ultimatecsvimporter.pot');
@rename(plugin_dir_path(__FILE__) . '/css', $old_files_dir . '/css');
@rename(plugin_dir_path(__FILE__) . '/images', $old_files_dir . '/images');
@rename(plugin_dir_path(__FILE__) . '/modules', $old_files_dir . '/modules');
@rename(plugin_dir_path(__FILE__) . '/templates', $old_files_dir . '/templates');
@rename(plugin_dir_path(__FILE__) . '/config', $old_files_dir . '/config');
@rename(plugin_dir_path(__FILE__) . '/fonts', $old_files_dir . '/fonts');
@rename(plugin_dir_path(__FILE__) . '/js', $old_files_dir . '/js');
@rename(plugin_dir_path(__FILE__) . '/lib', $old_files_dir . '/lib');
@rename(plugin_dir_path(__FILE__) . '/plugins', $old_files_dir . '/plugins');
@rename(plugin_dir_path(__FILE__) . '/upgrade', $old_files_dir . '/upgrade');
if(!is_dir($old_files_dir . '/includes')) {
	wp_mkdir_p( $old_files_dir . '/includes');
	@chmod($old_files_dir . '/includes', 0777);
}
@rename(plugin_dir_path(__FILE__) . '/includes/Array2XML.php', $old_files_dir . '/includes/Array2XML.php');
@rename(plugin_dir_path(__FILE__) . '/includes/Importer.php', $old_files_dir . '/includes/Importer.php');
@rename(plugin_dir_path(__FILE__) . '/includes/WPUltimateCSVImporter.php', $old_files_dir . '/includes/WPUltimateCSVImporter.php');
@rename(plugin_dir_path(__FILE__) . '/includes/csv_woocommerce_support.php', $old_files_dir . '/includes/csv_woocommerce_support.php');
@rename(plugin_dir_path(__FILE__) . '/includes/smackLogging.php', $old_files_dir . '/includes/smackLogging.php');
@rename(plugin_dir_path(__FILE__) . '/includes/ImportLib.php', $old_files_dir . '/includes/ImportLib.php');
@rename(plugin_dir_path(__FILE__) . '/includes/WPImporter_includes_helper.php', $old_files_dir . '/includes/WPImporter_includes_helper.php');
@rename(plugin_dir_path(__FILE__) . '/includes/XML2Array.php', $old_files_dir . '/includes/XML2Array.php');
@rename(plugin_dir_path(__FILE__) . '/includes/schedulehelper.php', $old_files_dir . '/includes/schedulehelper.php');
@rename(plugin_dir_path(__FILE__) . '/includes/smackcsv_importer_helper.php', $old_files_dir . '/includes/smackcsv_importer_helper.php');
