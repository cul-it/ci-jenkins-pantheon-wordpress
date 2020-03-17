<?php
/**
 * Plugin Name.
 *
 * @package   DrawAttention
 * @author    Nathan Tyler <support@tylerdigital.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Tyler Digital
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-drawattention-admin.php`
 *
 *
 * @package DrawAttention
 * @author  Nathan Tyler <support@tylerdigital.com>
 */
if ( !class_exists( 'DrawAttention' ) ) {
	class DrawAttention {

		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 *
		 * @since   1.0.0
		 *
		 * @var     string
		 */
		const VERSION = '1.9.12';
		const file = __FILE__;
		const name = 'Draw Attention';
		const slug = 'drawattention';
		/**
		 * @TODO - Rename "hotspots" to the name of your plugin
		 *
		 * Unique identifier for your plugin.
		 *
		 *
		 * The variable name is used as the text domain when internationalizing strings
		 * of text. Its value should match the Text Domain file header in the main
		 * plugin file.
		 *
		 * @since    1.0.0
		 *
		 * @var      string
		 */
		public $plugin_slug = 'drawattention';

		/**
		 * Instance of this class.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		protected static $instance = null;

		/**
		 * Instance of class to register CPT and taxonomies
		 * @var DrawAttention_CPT
		 */
		public $cpt;
		public $custom_fields;
		public $pro;
		public $block_image;
		public $updater;
		public $import_export;
		public $themes;

		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since     1.0.0
		 */
		private function __construct() {
			add_filter( 'da_description', 'wpautop' );

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Activate plugin when new blog is added
			add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

			// Load public-facing style sheet and JavaScript.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1 );

			// Shortcode for displaying the image map
			add_shortcode( 'drawattention', array( $this, 'shortcode' ) );

			add_action( 'admin_notices', array( $this, 'php_52_notice' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_shortcode_metabox' ) );

			add_action( 'template_include', array( $this, 'single_template' ) );

			add_filter( 'cmb2_meta_box_url', array( $this, 'cmb2_meta_box_url' ) );

		/**
		 * @TODO - Uncomment requried features
		 *
		 * Various functionality is separated into external files
		 */
			include_once( 'includes/bb/bb.php' );

			include_once( 'includes/cpt.php' );
			$this->cpt = new DrawAttention_CPT( $this );

			include_once( 'includes/custom_fields.php' );
			$this->custom_fields = new DrawAttention_CustomFields( $this );

			include_once( 'includes/themes.php' );
			$this->themes = new DrawAttention_Themes( $this );

			include_once( 'includes/pro/pro.php' );
			$this->pro = new DrawAttention_Pro( $this );

			include_once( 'includes/pro/updater.php' );
			$this->updater = new DrawAttention_Updater( $this );

			include_once( 'includes/pro/import-export.php' );
			$this->import_export = new DrawAttention_ImportExport( $this );

			include_once( 'includes/class-block-image.php' );
			$this->block_image = new DrawAttention_Block_Image( $this );

		}

		/**
		 * Return the plugin slug.
		 *
		 * @since    1.0.0
		 *
		 * @return    Plugin slug variable.
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since     1.0.0
		 *
		 * @return    object    A single instance of this class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Fired when the plugin is activated.
		 *
		 * @since    1.0.0
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Activate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       activated on an individual blog.
		 */
		public static function activate( $network_wide ) {

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {

				if ( $network_wide  ) {

					// Get all blog ids
					$blog_ids = self::get_blog_ids();

					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_activate();

						restore_current_blog();
					}

				} else {
					self::single_activate();
				}

			} else {
				self::single_activate();
			}

		}

		/**
		 * Fired when the plugin is deactivated.
		 *
		 * @since    1.0.0
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Deactivate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       deactivated on an individual blog.
		 */
		public static function deactivate( $network_wide ) {

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {

				if ( $network_wide ) {

					// Get all blog ids
					$blog_ids = self::get_blog_ids();

					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_deactivate();

						restore_current_blog();

					}

				} else {
					self::single_deactivate();
				}

			} else {
				self::single_deactivate();
			}

		}

		/**
		 * Fired when a new site is activated with a WPMU environment.
		 *
		 * @since    1.0.0
		 *
		 * @param    int    $blog_id    ID of the new blog.
		 */
		public function activate_new_site( $blog_id ) {

			if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
				return;
			}

			switch_to_blog( $blog_id );
			self::single_activate();
			restore_current_blog();

		}

		/**
		 * Get all blog ids of blogs in the current network that are:
		 * - not archived
		 * - not spam
		 * - not deleted
		 *
		 * @since    1.0.0
		 *
		 * @return   array|false    The blog ids, false if no matches.
		 */
		private static function get_blog_ids() {

			global $wpdb;

			// get an array of blog ids
			$sql = "SELECT blog_id FROM $wpdb->blogs
				WHERE archived = '0' AND spam = '0'
				AND deleted = '0'";

			return $wpdb->get_col( $sql );

		}

		/**
		 * Fired for each blog when the plugin is activated.
		 *
		 * @since    1.0.0
		 */
		private static function single_activate() {
			flush_rewrite_rules();
		}

		/**
		 * Fired for each blog when the plugin is deactivated.
		 *
		 * @since    1.0.0
		 */
		private static function single_deactivate() {
			// @TODO: Define deactivation functionality here
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			$domain = $this->plugin_slug;
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
			wp_register_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
		}

		/**
		 * Register and enqueues public-facing JavaScript files.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			wp_register_script( $this->plugin_slug . '-leaflet', plugins_url( 'assets/js/leaflet.js', __FILE__ ), array(), '1.3.4', $in_footer = true );
			wp_register_script( $this->plugin_slug . '-leaflet-rrose', plugins_url( 'assets/js/leaflet.rrose-min.js', __FILE__ ), array( $this->plugin_slug . '-leaflet' ), '0.2.0', $in_footer = true );
			wp_register_script( $this->plugin_slug . '-featherlight', plugins_url( 'assets/js/featherlight.min.js', __FILE__ ), array(), '1.7.13', $in_footer = true );
			wp_register_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( $this->plugin_slug . '-leaflet-rrose', 'jquery', $this->plugin_slug . '-featherlight' ), self::VERSION, true );

			wp_localize_script( $this->plugin_slug . '-plugin-script', 'drawattentionData', array(
				'isLoggedIn' => is_user_logged_in(),
				'isAdmin' => current_user_can( 'administrator' )
			) );

			$enqueue = apply_filters( 'da_enqueue_scripts_everywhere', false );
			if ( !empty( $enqueue ) ) {
				wp_enqueue_script( $this->plugin_slug . '-plugin-script' );
			}
		}

		function php_52_notice() {
			global $pagenow;
			if ( $pagenow != 'post.php' ) return;
			if ( get_post_type() != 'da_image' ) return;

			if ( version_compare( phpversion(), '5.2.99') <= 0 ) {
				$class = "error";
				$message = "<p>
				<h3>Your server is out of date</h3>
				Draw Attention (and many other WP plugins) <strong>requires PHP version 5.3 or higher</strong>. PHP 5.2 was released back in 2006 and support was officially terminated in 2011.
				</p>
				<p>

				<h3>Please contact your hosting company and ask to be upgraded to PHP 5.3 or higher</h3>
				<p>Most hosts run PHP 5.5+, there shouldn't be any charge for this upgrade. If your host won't upgrade your PHP version, it's worth considering another host since there are
				also security implications to running outdated PHP versions. If you contact us at <a href='mailto: support@tylerdigital.com'>support@tylerdigital.com</a> we'll be happy to provide
				you with a list of hosts who run PHP 5.3+ and will help you migrate your site from your current hosting provider.</p>
				<h4>Additional info:</h4>
				<ul>
					<li><a href='http://w3techs.com/technologies/details/pl-php/5/all'>http://w3techs.com/technologies/details/pl-php/5/all</a></li>
					<li><a href='http://php.net/releases/'>http://php.net/releases/</a></li>
				</ul>
				</p>";
				echo"<div class=\"$class\"> <p>$message</p></div>";
			}
		}

		/**
		 * Shortcode for displaying the image map
		 *
		 * @since    1.0.0
		 */
		public function shortcode( $atts ) {
			// Pro version uses shortcode defined in pro.php

			return 'drawattention shortcode error';


		}

		function add_shortcode_metabox() {
			add_meta_box( 'da_shortcode', __('Copy Shortcode', 'draw-attention' ), array( $this, 'display_shortcode_metabox' ), $this->cpt->post_type, 'side', 'low');
		}

		function display_shortcode_metabox() {
			echo '[drawattention]';
		}

		public function single_template( $template ) {
			if ( is_singular( $this->cpt->post_type ) ) {
				$template = self::locate_template( 'single-da_image.php' );
			}

			return $template;
		}

		public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
			if ( ! $template_path ) {
				$template_path = self::template_path();
			}

			if ( ! $default_path ) {
				$default_path = self::get_plugin_dir() . '/public/views/';
			}

			// Look within passed path within the theme - this is priority
			$template = locate_template(
				array(
					trailingslashit( $template_path ) . $template_name,
					$template_name
					)
				);

			// Get default template
			if ( ! $template ) {
				$template = $default_path . $template_name;
			}
			// Return what we found
			return apply_filters( self::slug.'_locate_template', $template, $template_name, $template_path );
		}

		public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
			if ( $args && is_array( $args ) ) {
				extract( $args );
			}

			$located = self::locate_template( $template_name, $template_path, $default_path );

			if ( ! file_exists( $located ) ) {
				_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
				return;
			}

			// Allow 3rd party plugin filter template file from their plugin
			$located = apply_filters( self::slug.'get_template', $located, $template_name, $args, $template_path, $default_path );

			do_action( self::slug.'_before_template_part', $template_name, $template_path, $located, $args );

			include( $located );

			do_action( self::slug.'_after_template_part', $template_name, $template_path, $located, $args );
		}

		public static function get_template_part( $slug, $name = '' ) {
			$template = '';

			// Look in yourtheme/slug-name.php and yourtheme/drawattention/slug-name.php
			if ( $name ) {
				$template = locate_template( array( "{$slug}-{$name}.php", self::template_path() . "{$slug}-{$name}.php" ) );
			}

			// Get default slug-name.php
			if ( ! $template && $name && file_exists( self::get_plugin_dir() . "/templates/{$slug}-{$name}.php" ) ) {
				$template = self::get_plugin_dir() . "/templates/{$slug}-{$name}.php";
			}

			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/drawattention/slug.php
			if ( ! $template ) {
				$template = locate_template( array( "{$slug}.php", self::template_path() . "{$slug}.php" ) );
			}

			// Allow 3rd party plugin filter template file from their plugin
			if ( $template ) {
				$template = apply_filters( self::slug.'_get_template_part', $template, $slug, $name );
			}

			if ( $template ) {
				load_template( $template, false );
			}
		}

		public static function template_path() {
			return self::slug . '/';
		}

		public static function get_plugin_dir() {
			return dirname( dirname( __FILE__ ) );
		}

		public static function get_plugin_url() {
			return dirname( plugin_dir_url( __FILE__ ) );
		}

		public function cmb2_meta_box_url( $url ) {
			$url = self::get_plugin_url().'/public/includes/lib/cmb2/';
			return $url;
		}

	}
}