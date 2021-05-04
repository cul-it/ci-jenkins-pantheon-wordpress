<?php
/*
 * Plugin Name: Easy Notification Bar
 * Plugin URI: https://wordpress.org/plugins/easy-notification-bar/
 * Description: Easily display a notice at the top of your site.
 * Author: WPExplorer
 * Author URI: https://www.wpexplorer.com/
 * Version: 1.4.1
 *
 * Text Domain: easy-notification-bar
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Easy_Notification_Bar Class.
 *
 * @since 1.0
 */
if ( ! class_exists( 'Easy_Notification_Bar' ) ) {

	final class Easy_Notification_Bar {

		/**
		 * @var Holds the plugin version.
		 * @since 1.4
		 */
		public $version = '1.4';

		/**
		 * @var Holds the plugin default settings.
		 * @since 1.0
		 */
		public $default_settings = array();

		/**
		 * @var Holds the plugin user based settings.
		 * @since 1.0
		 */
		public $settings = array();

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
				'allow_collapse'            => false,
				'is_sticky'                 => false,
				'front_page_only'           => false,
				'enable_system_font_family' => true,
				'close_icon'                => 'plain',

				'message'                   => esc_html( 'This is your default message which you can use to announce a sale or discount.', 'easy-notification-bar' ),

				'button_text'               => esc_html( 'Get started', 'easy-notification-bar' ),
				'button_link'               => 'https://wordpress.org/plugins/easy-notification-bar/',
				'button_nofollow'           => false,
				'button_sponsored'          => false,
				'button_target_blank'       => false,

				'background_color'          => '',
				'text_color'                => '',
				'text_align'                => 'center',
				'font_size'                 => '',

			) ) ;

			// Add notification to the site.
			add_action( 'wp', array( $this, 'add_notification' ) );

			// Add body class if notification bar is enabled.
			add_filter( 'body_class', array( $this, 'add_body_class' ) );

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

				// Define hook priority for the add_action functions used to display the notification.
				$hook_priority = apply_filters( 'easy_notification_bar_hook_priority', 10 );

				// Apply filters to the notification bar message for sanitization.
				add_filter( 'easy_notification_bar_message', 'wp_kses_post'      );
				add_filter( 'easy_notification_bar_message', 'shortcode_unautop' );
				add_filter( 'easy_notification_bar_message', 'do_shortcode', 11  );

				// Display Notification Bar.
				add_action( 'wp_body_open', array( $this, 'display_notification' ), $hook_priority );

				// Add Support for AMP Leagacy mode theme.
				if ( function_exists( 'is_amp_endpoint' )
					&& is_amp_endpoint()
					&& function_exists( 'amp_is_legacy' )
					&& amp_is_legacy()
				) {

					// Add Notification to AMP leagacy mode.
					add_action( 'amp_post_template_body_open', array( $this, 'display_notification' ), $hook_priority );

					// Add inline CSS.
					add_action( 'amp_post_template_css', array( $this, 'amp_reader_mode_leagacy_theme' ) );

				}

				// Enqueue Notification Scripts.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			}

		}

		/**
		 * Add body class if notification bar is enabled.
		 *
		 * @since  1.2
		 * @access public
		 * @return array $class
		 */
		public function add_body_class( $class ) {

			if ( $this->is_enabled() ) {
				$class[] = 'has-easy-notification-bar';
			}

			return $class;
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

			if ( $this->is_enabled() ) {

				$wrap_class = array( 'easy-notification-bar' );

				if ( $align = $this->get_setting( 'text_align', true ) ) {
					$wrap_class[] = 'easy-notification-bar--align_' . sanitize_html_class( $align );
				}

				if ( true === $this->get_setting( 'is_sticky' ) ) {
					$wrap_class[] = 'easy-notification-bar--sticky';
				}

				if ( true === $this->get_setting( 'allow_collapse' ) ) {
					if ( ! is_customize_preview() ) {
						$wrap_class[] = 'easy-notification-bar--hidden';
					}
					$wrap_class[] = 'easy-notification-bar--collapsible';
				}

				?>

				<div class="<?php echo esc_attr( join( ' ', $wrap_class ) ); ?>">

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
								<a href="<?php echo esc_url( $button_link ); ?>"<?php $this->button_rel() . $this->button_target_blank(); ?>><?php echo wp_kses_post( $this->get_setting( 'button_text' ) ); ?></a>
							</div><!-- .easy-notification-bar-button -->
						<?php } ?>

					</div>

					<?php if ( true === $this->get_setting( 'allow_collapse' ) ) { ?>
						<a class="easy-notification-bar__close" href="#" aria-label="<?php esc_html_e( 'Close notification', 'easy-notification-bar' ); ?>"><?php $this->close_icon(); ?></a>
					<?php } ?>

				</div><!-- .easy-notification-bar -->

			<?php

			}

			if ( $is_customize_preview ) {
				echo '</div>';
			}

		}

		/**
		 * Adds Notification bar CSS to AMP Legacy theme.
		 *
		 * @since  1.3
		 * @access public
		 * @return void
		 */
		public function amp_reader_mode_leagacy_theme() {
			$easy_notification_bar_css = file_get_contents( __DIR__ . '/assets/css/easy-notification-bar.css' );
			echo $easy_notification_bar_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$inline_css = $this->inline_css();
			if ( ! empty( $inline_css ) ) {
				echo $inline_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

			$classes[] = 'enb-text' . sanitize_html_class( $this->get_setting( 'text_align', true ) );

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
					plugins_url( '/assets/css/easy-notification-bar.css', ENB_MAIN_FILE_PATH ),
					array(),
					$this->version
				);

				if ( $inline_css = $this->inline_css() ) {
					wp_add_inline_style( 'easy-notification-bar', $inline_css );
				}

			}

			if ( true === $this->get_setting( 'allow_collapse' ) ) {
				wp_enqueue_script(
					'easy-notification-bar',
					plugins_url( '/assets/js/easy-notification-bar.js', ENB_MAIN_FILE_PATH ),
					array(),
					$this->version,
					true
				);
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
				'transport'         => 'refresh',
			) );

			$wp_customize->add_control( 'easy_nb_enable', array(
				'label'       => esc_html__( 'Enable Notification Bar', 'easy-notification-bar' ),
				'section'     => 'easy_nb',
				'settings'    => 'easy_nb[enable]',
				'type'        => 'checkbox',
				'description' => esc_html__( 'Note: If you do not see the bar on your site your theme has not been updated to include the "wp_body_open" action hook required since WordPress 5.2.0.', 'easy-notification-bar' ),
			) );

			/* Close/Collapse */
			$wp_customize->add_setting( 'easy_nb[allow_collapse]', array(
				'default'           => $this->default_settings[ 'allow_collapse' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			) );

			$wp_customize->add_control( 'easy_nb_allow_collapse', array(
				'label'       => esc_html__( 'Show close icon?', 'easy-notification-bar' ),
				'section'     => 'easy_nb',
				'settings'    => 'easy_nb[allow_collapse]',
				'type'        => 'checkbox',
				'description' => esc_html__( 'Makes use of localStorage (not cookies) so when a user clicks to hide the notifcation bar they will not see it again until they clear their browser cache.', 'easy-notification-bar' ),
			) );

			/* Close Icon */
			$wp_customize->add_setting( 'easy_nb[close_icon]', array(
				'default'           => $this->default_settings[ 'close_icon' ],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			) );

			$wp_customize->add_control( 'easy_nb_close_icon', array(
				'label'    => esc_html__( 'Close Icon', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[close_icon]',
				'type'     => 'radio',
				'choices'  => array(
					'plain'   => esc_html__( 'Plain', 'easy-notification-bar' ),
					'outline' => esc_html__( 'Outline', 'easy-notification-bar' ),
				),
			) );

			/* Sticky */
			$wp_customize->add_setting( 'easy_nb[is_sticky]', array(
				'default'           => $this->default_settings[ 'is_sticky' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			) );

			$wp_customize->add_control( 'easy_nb_is_sticky', array(
				'label'       => esc_html__( 'Enable Sticky?', 'easy-notification-bar' ),
				'section'     => 'easy_nb',
				'settings'    => 'easy_nb[is_sticky]',
				'type'        => 'checkbox',
				'description' => esc_html__( 'This option uses the modern "sticky" CSS position so it will only work in modern browsers and could cause conflicts with your theme\'s build in sticky functions so be sure to test accordingly and include the proper offsets.', 'easy-notification-bar' ),
			) );

			/* Homepage Only */
			$wp_customize->add_setting( 'easy_nb[front_page_only]', array(
				'default'           => $this->default_settings[ 'front_page_only' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
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

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'easy_nb_background_color', array(
				'label'    => esc_html__( 'Background', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[background_color]',
				'type'     => 'color',
			) ) );

			/* Notification Color */
			$wp_customize->add_setting( 'easy_nb[text_color]', array(
				'default'           => $this->default_settings[ 'text_color' ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'easy_nb_text_color', array(
				'label'    => esc_html__( 'Text Color', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[text_color]',
				'type'     => 'color',
			) ) );

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
				'label'    => esc_html__( 'Add rel="nofollow" to button?', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[button_nofollow]',
				'type'     => 'checkbox',
			) );

			/* Notification Button Sponsored */
			$wp_customize->add_setting( 'easy_nb[button_sponsored]', array(
				'default'           => $this->default_settings[ 'button_sponsored' ],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( 'easy_nb_button_sponsored', array(
				'label'    => esc_html__( 'Add rel="sponsored" to button?', 'easy-notification-bar' ),
				'section'  => 'easy_nb',
				'settings' => 'easy_nb[button_sponsored]',
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
					'easy_nb[message]',
					'easy_nb[background_color]',
					'easy_nb[text_color]',
					'easy_nb[text_align]',
					'easy_nb[font_size]',
					'easy_nb[enable_system_font_family]',
					'easy_nb[button_link]',
					'easy_nb[button_text]',
					'easy_nb[button_nofollow]',
					'easy_nb[button_sponsored]',
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
		public function button_rel() {

			$rel = array();

			if ( wp_validate_boolean( $this->get_setting( 'button_nofollow' ) ) ) {
				if ( wp_validate_boolean( $this->get_setting( 'button_target_blank' ) ) ) {
					$rel[] = 'nofollow';
					$rel[] = 'noopener';
				} else {
					$rel[] = 'nofollow';
				}
			}

			if ( wp_validate_boolean( $this->get_setting( 'button_sponsored' ) ) ) {
				$rel[] = 'sponsored';
			}

			if ( ! empty( $rel ) && is_array( $rel ) ) {
				$rel = apply_filters( 'easy_notification_bar_button_rel', implode( ' ' , $rel ) );
				echo ' rel="'. esc_attr( $rel ) . '"';
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

		/**
		 * Display the close icon.
		 *
		 * @since  1.4
		 * @access public
		 */
		public function close_icon() {
			$icon_style = $this->get_setting( 'close_icon', true );

			switch ( $icon_style ) {
				case 'outline':
					$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none" opacity=".87"/><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.59-13L12 10.59 8.41 7 7 8.41 10.59 12 7 15.59 8.41 17 12 13.41 15.59 17 17 15.59 13.41 12 17 8.41z"/></svg>';
					break;
				case 'plain':
				default:
					$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>';
					break;
			}
			$icon_size = 24;
			$icon = str_replace( '<svg', '<svg width="' . absint( $icon_size ) .'px" height="' . absint( $icon_size ) .'px"', $icon );
			echo apply_filters( 'easy_notification_bar_close_icon', $icon );
		}

	}

	new Easy_Notification_Bar;

}