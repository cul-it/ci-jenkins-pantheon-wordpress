<?php
/*
 * Plugin Name: Easy Notification Bar
 * Plugin URI: https://wordpress.org/plugins/easy-notification-bar/
 * Description: Easily display a notice at the top of your site.
 * Author: WPExplorer
 * Author URI: https://www.wpexplorer.com/
 * Version: 1.1.2
 *
 * Text Domain: easy-notification-bar
 * Domain Path: /languages/
 *
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Easy_Notification_Bar Class.
 *
 * @since 1.0
 */
if ( ! class_exists( 'Easy_Notification_Bar' ) ) {

	final class Easy_Notification_Bar {

		/**
		 * @var Holds the plugin default settings.
		 * @since 1.0
		 */
		var $default_settings = array();

		/**
		 * @var Holds the plugin user based settings.
		 * @since 1.0
		 */
		var $settings = array();

		/**
		 * Easy_Notification_Bar constructor.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Define plugin constants.
			$this->constants();

			// Add settings link to plugins admin page.
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

			// Define default settings/
			$this->default_settings = apply_filters( 'easy_notification_bar_default_settings', array(

				'enable'                    => true,
				'front_page_only'           => false,
				'enable_system_font_family' => true,

				'message'                   => esc_html( 'This is your default message which you can use to announce a sale or discount.', 'easy-notification-bar' ),

				'button_text'               => esc_html( 'Get started', 'easy-notification-bar' ),
				'button_link'               => 'https://wordpress.org/plugins/easy-notification-bar/',
				'button_nofollow'           => false,
				'button_target_blank'       => false,

				'background_color'          => '',
				'text_color'                => '',
				'text_align'                => 'center',
				'font_size'                 => '',

			) ) ;

			// Add notification to the site.
			add_action( 'wp', array( $this, 'add_notification' ) );

			// Register Customizer settings.
			add_action( 'customize_register', array( $this, 'customize_register' ) );
			add_action( 'customize_register', array( $this, 'customizer_partial_refresh' ) );

		}

		/**
		 * Define plugin constants.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function constants() {
			define( 'ENB_MAIN_FILE_PATH', __FILE__ );
			define( 'ENB_PLUGIN_DIR_PATH', plugin_dir_path( ENB_MAIN_FILE_PATH ) );
		}

		/**
		 * Add settings link to plugins admin page.
		 *
		 * @since  1.0
		 * @access public
		 * @return array | $links
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . esc_url( admin_url( '/customize.php?autofocus[section]=easy_nb' ) ) . '">' . esc_html__( 'Settings', 'easy-notification-bar' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Get plugin settings.
		 *
		 * @since  1.0
		 * @access public
		 * @return $settings | array
		 */
		public function get_settings() {
			if ( ! empty( $this->settings ) && ! is_customize_preview() ) {
				return $this->settings;
			}
			$this->settings = apply_filters( 'easy_notification_bar_settings', get_theme_mod( 'easy_nb' ) );
			$this->settings = wp_parse_args( $this->settings, $this->default_settings );
			return $this->settings;
		}

		/**
		 * Get plugin setting.
		 *
		 * @since  1.0
		 * @access public
		 * @return $settings | array
		 */
		public function get_setting( $name, $fallback = false ) {
			$this->get_settings();
			if ( isset( $this->settings[ $name ] ) ) {
				return $this->settings[ $name ];
			}
			if ( $fallback ) {
				return $this->defaults[ $name ];
			}
		}

		/**
		 * Add notification to the site.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function add_notification() {

			$this->get_settings();

			if ( is_customize_preview() || $this->is_enabled() ) {

				// Apply filters to the notification bar message for sanitization.
				add_filter( 'easy_notification_bar_message', 'wp_kses_post'      );
				add_filter( 'easy_notification_bar_message', 'shortcode_unautop' );
				add_filter( 'easy_notification_bar_message', 'do_shortcode', 11  );

				// Display Notification Bar.
				add_action( 'wp_body_open', array( $this, 'display_notification' ) );

				// Enqueue Notification Scripts.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			}

		}

		/**
		 * Check if the notification bar is enabled.
		 *
		 * @since  1.0
		 * @access public
		 * @return $enabled | bool
		 */
		public function is_enabled() {
			$enabled = wp_validate_boolean( $this->get_setting( 'enable' ) );
			if ( $this->get_setting( 'front_page_only' ) && ! is_front_page() ) {
				$enabled = false;
			}
			$enabled = apply_filters( 'easy_notification_bar_is_enabled', $enabled );
			return (bool) $enabled;
		}

		/**
		 * Display Notification Bar.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function display_notification() {

			$is_customize_preview = is_customize_preview();

			if ( $is_customize_preview ) {

				echo '<div class="easy-notification-bar-customize-selector">';

				// Inline style used for partialRefresh only. See enqueue_scripts for front-end CSS output.
				if ( $inline_css = $this->inline_css() ) {
					echo '<style>' . $inline_css . '</style>';
				}

			}

			if ( $this->is_enabled() ) { ?>

				<div class="easy-notification-bar">

					<div class="<?php echo esc_attr( join( ' ', $this->get_classes() ) ); ?>">

						<?php
						// Display Message
						if ( $message = $this->get_setting( 'message' ) ) { ?>
							<div class="easy-notification-bar-message">
								<?php echo apply_filters( 'easy_notification_bar_message', $message ); ?>
							</div><!-- .easy-notification-bar-message -->
						<?php } ?>

						<?php
						// Display button
						if ( $button_link = $this->get_setting( 'button_link' ) ) { ?>
							<div class="easy-notification-bar-button">
								<a href="<?php echo esc_url( $button_link ); ?>"<?php $this->button_nofollow() . $this->button_target_blank(); ?>><?php echo wp_kses_post( $this->get_setting( 'button_text' ) ); ?></a>
							</div><!-- .easy-notification-bar-button -->
						<?php } ?>

					</div>

				</div><!-- .easy-notification-bar -->

			<?php

			}

			if ( $is_customize_preview ) {
				echo '</div>';
			}

		}

		/**
		 * Get notification bar classes.
		 *
		 * @since  1.0
		 * @access public
		 * @return $classes | array
		 */
		public function get_classes() {

			$classes = array( 'easy-notification-bar-container' );

			$classes[] = 'enb-text' . wp_strip_all_tags( $this->get_setting( 'text_align', true ) );

			if ( ! empty( $this->settings[ 'enable_system_font_family' ] ) ) {
				$classes[] =  'enb-system-font';
			}

			return apply_filters( 'easy_notification_bar_container_class', $classes );

		}

		/**
		 * Enqueue Notification Scripts.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function enqueue_scripts() {

			if ( apply_filters( 'easy_notification_bar_enqueue_css', true ) ) {

				wp_enqueue_style(
					'easy-notification-bar',
					plugins_url( '/assets/css/easy-notification-bar.css', ENB_MAIN_FILE_PATH )
				);

				if ( $inline_css = $this->inline_css() ) {
					wp_add_inline_style( 'easy-notification-bar', $inline_css );
				}

			}

		}

		/**
		 * Return notification bar CSS.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function inline_css() {

			$this->get_settings();

			$all_css = $main_css = '';

			if ( $background_color = $this->get_setting( 'background_color' ) ) {
				$main_css .= 'background:' . sanitize_hex_color( $background_color ) . ';';
			}

			if ( $text_color = $this->get_setting( 'text_color' ) ) {
				$main_css .= 'color:' . sanitize_hex_color( $text_color ) . ';';
			}

			if ( $font_size = $this->get_setting( 'font_size' ) ) {
				$font_size_escaped = is_numeric( $font_size ) ? absint( $font_size ) . 'px' : esc_attr( $font_size );
				$main_css .= 'font-size:' . $font_size_escaped . ';';
			}

			if ( $main_css ) {
				$all_css .= '.easy-notification-bar{' . $main_css . '}';
			}

			return $all_css;

		}

		/**
		 * Register Customizer settings.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function customize_register( $wp_customize ) {

			$wp_customize->add_section( 'easy_nb', array(
				'title'    => esc_html__( 'Easy Notification Bar', 'easy-notification-bar' ),
				'priority' => 1,
			) );

			/* Enable Notification Bar */
			$wp_customize->add_setting( 'easy_nb[enable]', array(
				'default'           => $this->default_settings[ 'enable' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_enable', array(
				'label'       => esc_html__( 'Enable Notification Bar', 'easy-notification-bar' ),
				'section'     => 'easy_nb',
				'settings'    => 'easy_nb[enable]',
				'type'        => 'checkbox',
				'description' => esc_html__( 'Note: If you do not see the bar on your site your theme has not been updated to include the "wp_body_open" action hook required since WordPress 5.2.0.', 'easy-notification-bar' ),
			) );

			/* Homepage Only */
			$wp_customize->add_setting( 'easy_nb[front_page_only]', array(
				'default'           => $this->default_settings[ 'front_page_only' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_front_page_only', array(
				'label'    => esc_html__( 'Display on Front Page Only?', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[front_page_only]',
				'type'     => 'checkbox',
			) );

			/* Notification Message */
			$wp_customize->add_setting( 'easy_nb[message]', array(
				'default'           => $this->default_settings[ 'message' ],
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_message', array(
				'label'    => esc_html__( 'Message', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[message]',
				'type'     => 'textarea',
			) );

			/* Notification Background */
			$wp_customize->add_setting( 'easy_nb[background_color]', array(
				'default'           => $this->default_settings[ 'background_color' ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_background_color', array(
				'label'    => esc_html__( 'Background', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[background_color]',
				'type'     => 'color',
			) );

			/* Notification Color */
			$wp_customize->add_setting( 'easy_nb[text_color]', array(
				'default'           => $this->default_settings[ 'text_color' ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_text_color', array(
				'label'    => esc_html__( 'Text Color', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[text_color]',
				'type'     => 'color',
			) );

			/* Notification Align */
			$wp_customize->add_setting( 'easy_nb[text_align]', array(
				'default'           => $this->default_settings[ 'text_align' ],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_text_align', array(
				'label'    => esc_html__( 'Text Align', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[text_align]',
				'type'     => 'radio',
				'choices'    => array(
					'center' => esc_html__( 'Center', 'easy-notification-bar' ),
					'left'   => esc_html__( 'Left', 'easy-notification-bar' ),
					'right'  => esc_html__( 'Right', 'easy-notification-bar' ),
				),
			) );

			/* Enable System Fonts */
			$wp_customize->add_setting( 'easy_nb[enable_system_font_family]', array(
				'default'           => $this->default_settings[ 'enable_system_font_family' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_enable_system_font_family', array(
				'label'       => esc_html__( 'Apply System Font Family?', 'easy-notification-bar' ),
				'section'     => 'easy_nb',
				'settings'    => 'easy_nb[enable_system_font_family]',
				'type'        => 'checkbox',
				'description' => esc_html__( 'Use the common system UI font stack font family for your notification bar. If disabled it will inherit the font family from your theme.', 'easy-notification-bar' ),
			) );

			/* Font Size */
			$wp_customize->add_setting( 'easy_nb[font_size]', array(
				'default'           => $this->default_settings[ 'font_size' ],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_font_size', array(
				'label'       => esc_html__( 'Font Size', 'easy-notification-bar' ),
				'section'     => 'easy_nb',
				'settings'    => 'easy_nb[font_size]',
				'type'        => 'text',
				'description' => esc_html__( 'If a unit is not specified "px" will be used.', 'easy-notification-bar' ),
			) );

			/* Notification Button Text */
			$wp_customize->add_setting( 'easy_nb[button_text]', array(
				'default'           => $this->default_settings[ 'button_text' ],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_button_text', array(
				'label'    => esc_html__( 'Button Text', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[button_text]',
				'type'     => 'text',
			) );

			/* Notification Button Link */
			$wp_customize->add_setting( 'easy_nb[button_link]', array(
				'default'           => $this->default_settings[ 'button_link' ],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_button_link', array(
				'label'       => esc_html__( 'Button Link', 'easy-notification-bar' ),
				'section'     => 'easy_nb',
				'settings'    => 'easy_nb[button_link]',
				'type'        => 'text',
				'description' => esc_html__( 'Leave Empty to disable.', 'easy-notification-bar' ),
			) );

			/* Notification Button Nofollow */
			$wp_customize->add_setting( 'easy_nb[button_nofollow]', array(
				'default'           => $this->default_settings[ 'button_nofollow' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_button_nofollow', array(
				'label'    => esc_html__( 'Add nofollow tag to button?', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[button_nofollow]',
				'type'     => 'checkbox',
			) );

			/* Notification Button Nofollow */
			$wp_customize->add_setting( 'easy_nb[button_target_blank]', array(
				'default'           => $this->default_settings[ 'button_target_blank' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_button_target_blank', array(
				'label'    => esc_html__( 'Open button link in new tab?', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[button_target_blank]',
				'type'     => 'checkbox',
			) );

		}

		/**
		 * Add Customizer Partial Refresh.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		public function customizer_partial_refresh( $wp_customize ) {

			if ( ! isset( $wp_customize->selective_refresh ) ) {
				return;
			}

			$wp_customize->selective_refresh->add_partial( 'easy_nb[message]', array(
				'selector'            => '.easy-notification-bar-customize-selector',
				'settings'            => array(
					'easy_nb[enable]',
					'easy_nb[front_page_only]',
					'easy_nb[message]',
					'easy_nb[background_color]',
					'easy_nb[text_color]',
					'easy_nb[text_align]',
					'easy_nb[font_size]',
					'easy_nb[enable_system_font_family]',
					'easy_nb[button_link]',
					'easy_nb[button_text]',
					'easy_nb[button_nofollow]',
					'easy_nb[button_target_blank]',
				),
				'primarySetting'      => 'easy_nb[message]',
				'container_inclusive' => true,
				'fallback_refresh'    => true,
				'render_callback'     => array( $this, 'display_notification' ),
			) );

		}

		/**
		 * Echos rel="nofollow" tag for button if enabled.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function button_nofollow() {

			if ( wp_validate_boolean( $this->get_setting( 'button_nofollow' ) ) ) {
				if ( wp_validate_boolean( $this->get_setting( 'button_target_blank' ) ) ) {
					echo ' rel="nofollow noopener"';
				} else {
					echo ' rel="nofollow"';
				}
			}

		}

		/**
		 * Echos target="blank" tag for button if enabled.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function button_target_blank() {

			if ( wp_validate_boolean( $this->get_setting( 'button_target_blank' ) ) ) {
				if ( wp_validate_boolean( $this->get_setting( 'button_nofollow' ) ) ) {
					echo ' target="_blank"';
				} else {
					echo ' rel="noreferrer" target="_blank"';
				}
			}

		}

	}

	new Easy_Notification_Bar;

}