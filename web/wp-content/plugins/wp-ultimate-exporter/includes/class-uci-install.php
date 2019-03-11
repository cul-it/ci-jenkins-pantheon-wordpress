<?php

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmUCIExpInstall {
	/**
	 * Install UCI Pro
	 */

	/** @var array DB updates that need to be run */
	private static $db_updates = array(
		
	);

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( __CLASS__, 'smack_uci_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		#add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
	}

	
	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	private static function create_options() {


	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 * smackuci_events             - Table for storing all the events information's
	 * wp_ultimate_csv_importer_manageshortcodes       - Table for storing all short-code information's to the specific event
	 * wp_ultimate_csv_importer_shortcodes_statusrel   - Table for storing all short-code relational status to the specific event
	 * wp_ultimate_csv_importer_log_values      - Table for storing all log values to the specific events
	 *
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/** Removed: Insert all custom field controls of ACF,PODS and TYPES */
	}

	/**
	 * Table schema for Dashboards, Import Events, Scheduling Events, Logs, File Manager, Short-code & Exclusions.
	 * @return string
	 */

	/**
	 * Todo: add PHP docs
	 */
	public static function remove_options() {
		//delete_option('ULTIMATE_CSV_IMP_VERSION');
		//delete_option('ULTIMATE_CSV_IMPORTER_UPGRADE_VERSION');
	}

	/**
	 * Create files/directories.
	 */
	private static function create_files() {
		// Install files and folders for uploading files and prevent hot linking

		$files = array(
			array(
				'base'          => SM_UCIEXP_FILE_MANAGING_DIR,
				'file'          => '.htaccess',
				'content'       => ''
			),
			array(
				'base'          => SM_UCIEXP_FILE_MANAGING_DIR,
				'file'          => 'index.html',
				'content'       => ''
			),
			array(
				'base'          => SM_UCIEXP_LOG_DIR,
				'file'          => '.htaccess',
				'content'       => 'deny from all'
			),
			array(
				'base'          => SM_UCIEXP_LOG_DIR,
				'file'          => 'index.html',
				'content'       => ''
			),
			array(
				'base'          => SM_UCIEXP_EXPORT_DIR,
				'file'          => '.htaccess',
				'content'       => ''
			),
			array(
				'base'          => SM_UCIEXP_EXPORT_DIR,
				'file'          => 'index.html',
				'content'       => ''
			)
		);
		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				@chmod($file['base'], 0777);
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * @param $links
	 *
	 * @return array
	 */
	public function smack_uci_action_links( $links ) {
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=sm-uci-settings') ) .'">Settings</a>';
		$links[] = '<a href="http://wp-buddy.com" target="_blank">More plugins by WP-Buddy</a>';
		return $links;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param       mixed $links Plugin Row Meta
	 * @param       mixed $file  Plugin Base file
	 * @return      array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file == SM_UCIEXP_PLUGIN_BASENAME ) {
			$row_meta = array(
				
				'support' => '<a href="' . esc_url( apply_filters( 'sm_uci_support_url', admin_url() . 'admin.php?page=sm-uci-support' ) ) . '" title="' . esc_attr( __( 'Contact Support', 'wp-ultimate-exporter' ) ) . '" target="_blank">' . __( 'Support', 'wp-ultimate-exporter' ) . '</a>',
			);
			unset( $links['edit'] );
			//unset($links['View details']);

			return array_merge( $row_meta, $links );
		}

		return (array) $links;
	}

	public static function showUpgradeNotification($currentPluginMetadata, $newPluginMetadata){
		// check "upgrade_notice"
		if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
			echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>'.esc_html__('Important Upgrade Notice:','wp-ultimate-exporter').'</strong> ';
			echo esc_html($newPluginMetadata->upgrade_notice), '</p>';
		}
	}

}
