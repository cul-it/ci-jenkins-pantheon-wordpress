<?php
/**
 * Plugin Name: Anywhere Elementor
 * Description: Allows you to insert elementor pages and library templates anywhere using shortcodes.
 * Plugin URI: https://www.elementoraddons.com/
 * Author: WPVibes
 * Version: 1.2.3
 * Author URI: https://wpvibes.com/
 * Elementor tested up to: 3.1.0
 * Elementor Pro tested up to: 3.0.10
 * Text Domain: wts_ae
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'AE_VERSION', '1.2.3' );

define( 'WTS_AE__FILE__', __FILE__ );
define( 'WTS_AE_PLUGIN_BASE', plugin_basename( WTS_AE__FILE__ ) );
define( 'WTS_AE_URL', plugins_url( '/', WTS_AE__FILE__ ) );
define( 'WTS_AE_PATH', plugin_dir_path( WTS_AE__FILE__ ) );
define( 'WTS_AE_ASSETS_URL', WTS_AE_URL . 'includes/assets/' );

add_action( 'plugins_loaded', 'wts_ae_load_plugin_textdomain' );


require_once( WTS_AE_PATH . 'includes/post-type.php' );
require_once( WTS_AE_PATH . 'includes/meta-box.php' );
require_once( WTS_AE_PATH . 'includes/bootstrap.php' );


/**
 *  Load gettext translate for our text domain.
 */
function WTS_AE_load_plugin_textdomain(){
    load_plugin_textdomain( 'wts_ae' );
}


function wts_ae_styles_method() {
    $custom_css = "<style type='text/css'> .ae_data .elementor-editor-element-setting {
                        display:none !important;
                }
                </style>";
    echo $custom_css;
}
add_action( 'wp_head', 'wts_ae_styles_method' );
function set_custom_edit_ae_global_templates_posts_columns($columns) {
	//unset( $columns['author'] );
	$columns['ae_shortcode_column'] = __( 'Shortcode', 'wts_ae' );
	return $columns;
}
function add_ae_global_templates_columns( $column, $post_id ) {

	switch ( $column ) {

		case 'ae_shortcode_column' :
			echo '<input type=\'text\' class=\'widefat\' value=\'[INSERT_ELEMENTOR id="'.$post_id.'"]\' readonly="">';
			break;
	}
}

if( !class_exists('Aepro\Aepro')){
    add_filter( 'manage_ae_global_templates_posts_columns', 'set_custom_edit_ae_global_templates_posts_columns' );
    add_action('manage_ae_global_templates_posts_custom_column', 'add_ae_global_templates_columns', 10, 2);
}