<?php
/********************************************************************************************
 * Plugin Name: WP Ultimate CSV Importer Pro
 * Description: Seamlessly create / update posts, custom posts, pages, products, media, SEO and contents  of premium plugins from your CSV data with ease.  The import / update can be scheduled at regular intervals. The Importer can pull files from remote servers via ftp or http protocol.
 * Version: 5.5
 * Text Domain: wp-ultimate-csv-importer-pro
 * Domain Path: /languages
 * Author: Smackcoders
 * Plugin URI: https://goo.gl/kKWPui
 * Author URI: https://goo.gl/kKWPui
 *
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'SM_WPUltimateCSVImporterPro' ) ) :
	/**
	 * Main WPUltimateCSVImporterPro Class.
	 *
	 * @class WPUltimateCSVImporterPro Class
	 * @version     5.0
	 */
	class SM_WPUltimateCSVImporterPro {

		public $version = '5.5';

		/**
		 * The single instance of the class.
		 *
		 * @var $_instance
		 * @since 5.0
		 */
		protected static $_instance = null;

		/**
		 * Main WPUltimateCSVImporterPro Instance.
		 *
		 * Ensures only one instance of WPUltimateCSVImporterPro is loaded or can be loaded.
		 *
		 * @since 4.5
		 * @static
		 * @return SM_WPUltimateCSVImporterPro - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * SM_WPUltimateCSVImporterPro Constructor.
		 */
		public function __construct() {
			include_once ( 'includes/class-uci-install.php' );
			include_once ( 'uninstall.php' );
			do_action( 'wp_ultimate_csv_importer_pro_loaded' );
			add_filter( 'cron_schedules', array('SmackUCIInstall', 'cron_schedules'));
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array('SmackUCIInstall', 'plugin_row_meta'), 10, 2 );

			# Custom content after plugin row meta starts
			add_action('after_plugin_row_' . plugin_basename( __FILE__ ), array('SmackUCIInstall', 'after_plugin_row_meta'), 10, 3);
			# Custom content after plugin row meta ends

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active('wp-ultimate-csv-importer-pro/index.php') ) {
				add_action( 'admin_notices', array( 'SmackUCIInstall', 'wp_ultimate_csv_importer_notice' ) );
				add_action( 'admin_notices', array('SmackUCIInstall', 'important_cron_notice') );
			}
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
			$ucisettings = get_option('sm_uci_pro_settings');
			if($ucisettings['enable_s3'] == 'on'){
				include_once ( 'includes/class-aws-s3-fimage.php' );
				new UCI_S3_Feat_Image();	
			}
		}

		/**
		 * Hook into actions and filters.
		 * @since  4.5
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'SmackUCIInstall', 'install' ) );
			add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
			add_action( 'init', array( $this, 'smack_uci_enqueue_scripts') );
			add_action('wp_dashboard_setup', array($this,'uci_pro_add_dashboard_widgets'));
			add_action('smack_uci_email_scheduler', array('SmackUCIEmailScheduler', 'send_login_credentials_to_users'));
			add_action('smack_uci_image_scheduler', array('SmackUCIMediaScheduler', 'populateFeatureImages'));
			add_action('smack_uci_cron_scheduler', array('SmackUCIScheduleManager', 'smack_uci_cron_scheduler'));
			add_action('smack_uci_cron_scheduled_export', array('SmackUCIScheduleManager', 'smack_uci_cron_scheduled_export'));
			register_deactivation_hook( __FILE__, array( 'SmackUCIUnInstall', 'uninstall' ) );
		}

		/**
		 * Define SmackUCI Constants.
		 */
		public function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'SM_UCI_PLUGIN_FILE', __FILE__ );
			$this->define( 'SM_UCI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'SM_UCI_VERSION', $this->version );
			$this->define( 'SM_UCI_DELIMITER', ',' );
			$this->define( 'SM_UCI_PRO_DIR', plugin_dir_path(__FILE__));
			$this->define( 'SM_UCI_PRO_URL', plugins_url().'/wp-ultimate-csv-importer-pro');
			$this->define( 'SM_UCI_LOG_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/import_logs/' );
			$this->define( 'SM_UCI_DEFAULT_UPLOADS_DIR', $upload_dir['basedir'] );
			$this->define( 'SM_UCI_FILE_MANAGING_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/' );
			$this->define( 'SM_UCI_IMPORT_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/imports' );
			$this->define( 'SM_UCI_IMPORT_URL', $upload_dir['baseurl'] . '/smack_uci_uploads/imports' );
			$this->define( 'SM_UCI_EXPORT_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/exports/' );
			$this->define( 'SM_UCI_EXPORT_URL', $upload_dir['baseurl'] . '/smack_uci_uploads/exports/' );
			$this->define( 'SM_UCI_ZIP_FILES_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/zip_files/' );
			$this->define( 'SM_UCI_INLINE_IMAGE_DIR', $upload_dir['basedir'] . '/smack_inline_images/' );
			$this->define( 'SM_UCI_SCREENS_DATA',$upload_dir['basedir'].'/smack_uci_uploads/screens_data');
			$this->define( 'SM_UCI_SESSION_CACHE_GROUP', 'smack_uci_session_id' );
			$this->define( 'SM_UCI_SETTINGS', 'WP Ultimate CSV Importer PRO' );
			$this->define( 'SM_UCI_NAME', 'Ultimate CSV Importer PRO' );
			$this->define( 'SM_UCI_SLUG', 'wp-ultimate-csv-importer-pro' );
			$this->define( 'SM_UCI_DEBUG_LOG', $upload_dir['basedir'] . '/wp-ultimate-csv-importer-pro.log');
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
			foreach ( glob( plugin_dir_path( __FILE__ ) . "helpers/*.php" ) as $file ) {
				include_once ("$file");
			}
			include_once ( 'includes/class-uci-helper.php' );
			include_once ( 'libs/parsers/SmackCSVParser.php' );
			include_once ( 'libs/parsers/SmackXMLParser.php' );
			include_once ( 'libs/parsers/SmackNewXMLParser.php' );
			include_once ( 'includes/class-uci-admin-ajax.php' );
			include_once ( 'includes/class-uci-event-logging.php' );
			// We are in admin mode
			include_once ( 'admin/class-uci-admin.php' );
			include_once ( 'includes/class-uci-email-scheduler.php' );
			include_once ( 'includes/class-uci-media-scheduler.php' );
			include_once ( 'managers/class-uci-schedulemanager.php' );
			include_once ( 'SmackUCIWebServices.php' );
		}

		public function smack_uci_enqueue_scripts() {
			// Register / Enqueue the plugin scripts & style
			$uciPages = array('sm-uci-dashboard', 'sm-uci-import', 'sm-uci-managers', 'sm-uci-export', 'sm-uci-settings', 'sm-uci-support');
			if (isset($_REQUEST['page']) && in_array(sanitize_text_field($_REQUEST['page']), $uciPages)) {
				// Register & Enqueue the plugin styles
				wp_enqueue_style( 'ultimate-css', plugins_url( 'assets/css/ultimate-importer.css', __FILE__ ) );
				wp_enqueue_style( 'boot.css', plugins_url( 'assets/css/bootstrap.css', __FILE__ ) );
				wp_enqueue_style( 'Icomoon Icons', plugins_url( 'assets/css/icomoon.css', __FILE__ ) );
				wp_enqueue_style( 'Animate CSS', plugins_url( 'assets/css/animate.css', __FILE__ ) );
				wp_enqueue_style( 'jquery-fileupload.css', plugins_url( 'assets/css/jquery.fileupload.css', __FILE__ ) );
				wp_enqueue_style( 'jquery-style', plugins_url( 'assets/css/jquery-ui.css', __FILE__ ) );
				wp_enqueue_style('icheck', plugins_url('assets/css/icheck/green.css', __FILE__));
				wp_enqueue_style( 'file-tree-css', plugins_url( 'assets/css/jqueryfiletree.css', __FILE__ ) );
				// WaitMe CSS & JS for blur the page and show the progressing loader
				wp_enqueue_style('waitme-css', plugins_url('assets/css/waitMe.css', __FILE__));
				wp_enqueue_style('sweet-alert-css', plugins_url('assets/css/sweetalert.css', __FILE__));
				wp_enqueue_style('custom-style', plugins_url('assets/css/custom-style.css', __FILE__));
				//new files include
				wp_enqueue_style('custom-new-style', plugins_url('assets/css/custom-new-style.css', __FILE__));
				wp_enqueue_style( 'bootstrap-select-css', plugins_url( 'assets/css/bootstrap-select.css', __FILE__ ));
				// Register & Enqueue the plugin scripts
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'icheck-js', plugins_url( 'assets/js/icheck.min.js', __FILE__ ) );
				wp_register_script( 'ultimate-importer-js', plugins_url( 'assets/js/ultimate-importer.js', __FILE__ ) );
				wp_enqueue_script( 'ultimate-importer-js' );
				//wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_register_script( 'bootstrap-datepicker-js', plugins_url( 'assets/js/bootstrap-datepicker.js', __FILE__ ) );
				wp_enqueue_script( 'bootstrap-datepicker-js' );
				wp_enqueue_style( 'bootstrap-datepicker-css', plugins_url('assets/css/bootstrap-datepicker.css', __FILE__ ) );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script('jquery-ui-draggable');
				wp_enqueue_script('jquery-ui-droppable');
				wp_enqueue_script('jquery-ui-core');
				#wp_enqueue_script( 'jquery-ui-core' );
				#wp_register_script( 'uci-jquery-ui-min', plugins_url('assets/js/jquery-ui.min.js', __FILE__) );
				#wp_enqueue_script( 'uci-jquery-ui-min' );
				#wp_register_script( 'uci-jquery-min', plugins_url('assets/js/jquery.min.js', __FILE__) );
				#wp_enqueue_script( 'uci-jquery-min' );
				#wp_enqueue_script( '' );
				wp_enqueue_script( 'file-tree', plugins_url( 'assets/js/jqueryfiletree.js', __FILE__ ) );
				wp_localize_script( 'ultimate-importer-js', 'uci_importer', array(
					'adminurl' => admin_url(),
					'siteurl'  => site_url(),
					'requestpage' => $_REQUEST['page'],
					'db_orphanedMsg' => __('no of Orphaned Post/Page meta has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_tagMsg' => __('no of Unassigned tags has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_revisionMsg' => __('no of Post/Page revisions has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_draftMSg' => __('no of Auto drafted Post/Page has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_trashMsg' => __('no of Post/Page in trash has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_spamMsg' => __('no of Spam comments has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_commentTrashMsg' => __('no of Comments in trash has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_unapprovedMsg' => __('no of Unapproved comments has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_pingbackMsg' => __('no of Pingback comments has been removed.', 'wp-ultimate-csv-importer-pro'),
					'db_trackbackMsg' => __('no of Trackback comments has been removed.', 'wp-ultimate-csv-importer-pro'),

				) );
				wp_register_script('bootstrap-js', plugins_url('assets/js/bootstrap.js', __FILE__));
				wp_enqueue_script('bootstrap-js');
				wp_register_script('bootstrap-select-js', plugins_url('assets/js/bootstrap-select.js', __FILE__));
				wp_enqueue_script('bootstrap-select-js');
				// Sidebar Sticky JS
				wp_register_script('stickySidebar-js', plugins_url('assets/js/stickySidebar.js', __FILE__));
				wp_enqueue_script('stickySidebar-js');
				//new files include close
				wp_register_script('waitme-js', plugins_url('assets/js/waitMe.js', __FILE__));
				wp_enqueue_script('waitme-js');
				// Sweet Alert Js
				wp_register_script('sweet-alert-js', plugins_url('assets/js/sweetalert-dev.js', __FILE__));
				wp_enqueue_script('sweet-alert-js');
				// Tinymce Editor Js
				wp_register_script('ckeditor-js', plugins_url('assets/js/ckeditor-js/ckeditor.js', __FILE__));
				wp_enqueue_script('ckeditor-js');
				//MODAL POP UP JS
				wp_enqueue_script('pop-up',plugins_url('assets/js/modal.js',__FILE__));
				// Morris chart CSS & JS for dashboard
				if(isset($_REQUEST['page']) && sanitize_text_field($_REQUEST['page']) == 'sm-uci-dashboard') {
					wp_enqueue_script( 'chart-utils-js', plugins_url('assets/js/chart-js/utils.js', __FILE__) );
					wp_enqueue_script( 'uci-dashboard', plugins_url('assets/js/chart-js/Chart.bundle.js', __FILE__) );
					wp_enqueue_script( 'uci-dashboard-chart', plugins_url( 'assets/js/chart-js/dashchart.js', __FILE__ ) );
				}
			}
			wp_enqueue_style('style-maintenance', plugins_url('assets/css/style-maintenance.css', __FILE__));
		}

		/**
		 * Init SM_WPUltimateCSVImporterPro when WordPress Initialises.
		 */
		public function init() {
			if(is_admin()) :
				// Init action.
				do_action( 'uci_init' );
				if(is_admin()) {
					SmackUCIAdminAjax::smuci_ajax_events();
					remove_image_size( 'thumbnail' );
					remove_image_size( 'medium' );
					remove_image_size( 'medium_large' );
					remove_image_size( 'large' );
				}
			endif;
		}

		public function uci_pro_add_dashboard_widgets(){
			wp_enqueue_script( 'chart-utils-js', plugins_url('assets/js/chart-js/utils.js', __FILE__) );
			wp_enqueue_script( 'uci-wp-dash-chart-js', plugins_url('assets/js/chart-js/Chart.bundle.js', __FILE__) );
			wp_enqueue_script( 'uci-dashboard-chart-widget', plugins_url( 'assets/js/chart-js/dashchart-widget.js', __FILE__ ) );
			// Add widget on WordPress Dashboard
			$get_current_user = wp_get_current_user();
			$role = $get_current_user->roles[0];
			if( $role == "administrator" ) {
				wp_add_dashboard_widget( 'uci_pro_dashboard_linechart', 'Ultimate-CSV-Importer-Pro-Activity', array(
					'SmackUCIAdmin',
					'LineChart'
				), $screen = get_current_screen(), 'advanced', 'high' );
				wp_add_dashboard_widget( 'uci_pro_dashboard_piechart', 'Ultimate-CSV-Importer-Pro-Statistics', array(
					'SmackUCIAdmin',
					'PieChart'
				), $screen = get_current_screen(), 'advanced', 'high' );
			}
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
		 * @return SM_UCI_Emails
		 */
		public function mailer() {
			return SM_UCI_Emails::instance();
		}
	}
endif;

add_action('plugins_loaded','SmackCSVImporterLoadLanguages');
function SmackCSVImporterLoadLanguages(){
	$wp_csv_importer_pro_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	load_plugin_textdomain( SM_UCI_SLUG , false, $wp_csv_importer_pro_lang_dir );
}

/**
 * Main instance of WPUltimateCSVImporterPro.
 *
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  4.5
 * @return WPUltimateCSVImporterPro
 */
function SmackUCI() {
	return SM_WPUltimateCSVImporterPro::instance();
}
// Global for backwards compatibility.
$GLOBALS['wp_ultimate_csv_importer_pro'] = SmackUCI();

//Maintenance mode
$options = get_option('sm_uci_pro_settings');
$enable_main_mode = isset($options['enable_main_mode']) ? $options['enable_main_mode'] : '';
$maintainance_text = isset($options['main_mode_text']) ? $options['main_mode_text'] : '';

if($maintainance_text == "")
 $maintainance_text = "Site is under maintenance mode. Please wait few min!";


function activate_maintenance_mode() { 
	include(ABSPATH . "wp-includes/pluggable.php");
	global $maintainance_text;
	if(!current_user_can('manage_options')) {
    ?> 
    <div class="main-mode-front"> <span> <?php echo $maintainance_text; ?> </span> </div> 
    <?php }
} 

function admin_bar_menu(){
            global $wp_admin_bar;
                $wp_admin_bar->add_menu( array(
                    'id'     => 'debug-bar',
                    'href' => admin_url().'admin.php?page=sm-uci-import',
                    'parent' => 'top-secondary',
                    'title'  => apply_filters( 'debug_bar_title', __('Maintenance Mode', 'ultimate-maintenance-mode') ),
                    'meta'   => array( 'class' => 'smack-main-mode' ),
                ) );
        }

if($enable_main_mode == "on"){
  add_action( 'admin_bar_menu', 'admin_bar_menu' );
  add_action('wp_head', 'activate_maintenance_mode');
}
