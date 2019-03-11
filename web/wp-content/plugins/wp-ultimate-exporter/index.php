<?php
/*******************************************************************************
 * Plugin Name: WP Ultimate Exporter
 * Description: Backup tool to export all your WordPress data as CSV file. eCommerce data of WooCommerce, MarketPress, eCommerce, eShop, Custom Post and Custom field informations along with default WordPress modules.
 * Version: 1.4.3
 * Author: Smackcoders
 * Text Domain: wp-ultimate-exporter
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'ExpSmCSVHandler' ) ) :
	/**
	 * Main WPUltimateCSVImporter Class.
	 *
	 * @class WPUltimateCSVImporter Class
	 * @version     5.0
	 */
	class ExpSmCSVHandler {

		public $version = '1.4.3';

		/**
		 * The single instance of the class.
		 *
		 * @var $_instance
		 * @since 5.0
		 */
		protected static $_instance = null;

		/**
		 * Main WPUltimateCSVImporter Instance.
		 *
		 * Ensures only one instance of WPUltimateCSVImporter is loaded or can be loaded.
		 *
		 * @since 5.0
		 * @static
		 * @see WC()
		 * @return ExpSmCSVHandler - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * ExpSmCSVHandler Constructor.
		 */
		public function __construct() {
			include_once ( 'includes/class-uci-install.php' );
			//include_once ( 'uninstall.php' );

			do_action( 'wp_ultimate_csv_importer_loaded' );
			add_filter( 'plugin_row_meta', array('SmUCIExpInstall', 'plugin_row_meta'), 10, 2 );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array('SmUCIExpInstall', 'plugin_row_meta'), 10, 2 );			

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
		
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 * @since  5.0
		 */
		private function init_hooks() {
			//register_activation_hook( __FILE__, array( 'SmUCIExpInstall', 'install' ) );
			add_action( 'plugins_loaded', array( $this, 'init' ), 0 );

			function admin_notice_exporter_free() {
				global $pagenow;
				$active_plugins = get_option( "active_plugins" );
			    if ( $pagenow == 'plugins.php' && !in_array('wp-ultimate-csv-importer/index.php', $active_plugins) ) {
				    ?>
				    <div class="notice notice-warning is-dismissible" >
				        <p> WP Ultimate Exporter is an addon of <a href="https://goo.gl/fwqMnZ" target="blank" style="cursor: pointer;text-decoration:none">WP Ultimate CSV Importer</a> plugin, kindly install it to continue using WP ultimate exporter. </p>
				        <p>
				    </div>
				    <?php 
			   }
			}
        

		add_action( 'admin_notices', 'admin_notice_exporter_free' );
		}

		/**
		 * Define SmUCIExp Constants.
		 */
		public function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'SM_UCIEXP_PLUGIN_FILE', __FILE__ );
			$this->define( 'SM_UCIEXP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'SM_UCIEXP_VERSION', $this->version );
			$this->define( 'SM_UCIEXP_DELIMITER', ',' );
			$this->define( 'SM_UCIEXP_PRO_DIR', plugin_dir_path(__FILE__));
			$this->define( 'SM_UCIEXP_PRO_URL', plugins_url().'/wp-ultimate-exporter');
			$this->define( 'SM_UCIEXP_LOG_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/import_logs/' );
			$this->define( 'SM_UCIEXP_DEFAULT_UPLOADS_DIR', $upload_dir['basedir'] );
			$this->define( 'SM_UCIEXP_DEFAULT_UPLOADS_URL', $upload_dir['baseurl'] );
			$this->define( 'SM_UCIEXP_FILE_MANAGING_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/' );
			$this->define( 'SM_UCIEXP_IMPORT_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/imports' );
			$this->define( 'SM_UCIEXP_IMPORT_URL', $upload_dir['baseurl'] . '/smack_uci_uploads/imports' );
			$this->define( 'SM_UCIEXP_EXPORT_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/exports/' );
			$this->define( 'SM_UCIEXP_EXPORT_URL', $upload_dir['baseurl'] . '/smack_uci_uploads/exports/' );
			$this->define( 'SM_UCIEXP_ZIP_FILES_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/zip_files/' );
			$this->define( 'SM_UCIEXP_INLINE_IMAGE_DIR', $upload_dir['basedir'] . '/smack_inline_images/' );
			$this->define( 'SM_UCIEXP_SCREENS_DATA',$upload_dir['basedir'].'/smack_uci_uploads/screens_data');
			$this->define( 'SM_UCIEXP_SESSION_CACHE_GROUP', 'smack_uci_session_id' );
			$this->define( 'SM_UCIEXP_SETTINGS', 'wp-ultimate-exporter-free' );
			$this->define( 'SM_UCIEXP_NAME', 'Wp Ultimate Exporter' );
			$this->define( 'SM_UCIEXP_SLUG', 'wp-ultimate-exporter' );
			$this->define( 'SM_UCIEXP_DEBUG_LOG', $upload_dir['basedir'] . '/wp-ultimate-exporter.log');
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		public function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			include_once ( 'includes/class-uci-helper.php' );
			include_once ('includes/class-uci-exporter.php');
			include_once ( 'admin/class-uci-admin.php' );
		}


		/**
		 * Init ExpSmCSVHandlerPro when WordPress Initialises.
		 */
		public function init() {
			if(is_admin()) :
				// Init action.
				do_action( 'uci_init' );
				if(is_admin()) {
					#$this->includes();
					//SmUCIExpAdminAjax::smuci_ajax_events();
					# Removed: De-Register the media sizes
				}
			endif;
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get Ajax URL.
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * Email Class.
		 * @return SM_UCIEXP_Emails
		 */
		public function mailer() {
			return SM_UCIEXP_Emails::instance();
		}
	}
endif;


/**
 * Main instance of WPUltimateCSVImporterPro.
 *
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  5.0
 * @return WPUltimateCSVImporterPro
 */
function SmUCIExp() {
	return ExpSmCSVHandler::instance();
}
// Global for backwards compatibility.
$GLOBALS['wp_ultimate_csv_importer'] = SmUCIExp();