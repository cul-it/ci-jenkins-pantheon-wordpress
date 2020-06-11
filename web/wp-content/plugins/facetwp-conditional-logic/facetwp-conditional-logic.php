<?php
/*
Plugin Name: FacetWP - Conditional Logic
Description: Toggle facets based on certain conditions
Version: 1.3.0
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-conditional-logic
*/

defined( 'ABSPATH' ) or exit;

class FacetWP_Conditional_Logic_Addon
{

    public $rules;
    public $facets = array();
    public $templates = array();


    function __construct() {

        define( 'FWPCL_VERSION', '1.3.0' );
        define( 'FWPCL_DIR', dirname( __FILE__ ) );
        define( 'FWPCL_URL', plugins_url( '', __FILE__ ) );
        define( 'FWPCL_BASENAME', plugin_basename( __FILE__ ) );

        add_action( 'init', array( $this, 'init' ), 12 );
    }


    function init() {
        if ( ! function_exists( 'FWP' ) ) {
            return;
        }

        load_plugin_textdomain( 'facetwp-conditional-logic', false, basename( FWPCL_DIR ) . '/languages' );

        $this->facets = FWP()->helper->get_facets();
        $this->templates = FWP()->helper->get_templates();

        // load settings
        $rulesets = get_option( 'fwpcl_rulesets' );
        $this->rulesets = empty( $rulesets ) ? array() : json_decode( $rulesets, true );

        $this->admin_i18n = array(
          'Saving' => __( 'Saving', 'facetwp-conditional-logic' ),
          'Importing' => __( 'Importing', 'facetwp-conditional-logic' ),
          'Changes saved' => __( 'Changes saved', 'facetwp-conditional-logic' ),
          'OR' => __( 'OR', 'facetwp-conditional-logic' ),
          'IF' => __( 'IF', 'facetwp-conditional-logic' ),
          'AND' => __( 'AND', 'facetwp-conditional-logic' ),
          'THEN' => __( 'THEN', 'facetwp-conditional-logic' ),
          'Delete this ruleset?' => __( 'Delete this ruleset?', 'facetwp-conditional-logic' ),
        );

        // register front assets
        wp_register_script( 'fwpcl-front', FWPCL_URL . '/assets/js/front.js', array( 'jquery' ), FWPCL_VERSION, true );
        wp_register_style( 'fwpcl-front', FWPCL_URL . '/assets/css/front.css', array(), FWPCL_VERSION );

        // register admin assets
        wp_register_script( 'fwpcl-admin', FWPCL_URL . '/assets/js/admin.js', array( 'jquery' ), FWPCL_VERSION, false );
        wp_register_style( 'fwpcl-admin', FWPCL_URL . '/assets/css/admin.css', array(), FWPCL_VERSION );
        wp_register_style( 'fwp-admin', FACETWP_URL . '/assets/css/admin.css', array(), FACETWP_VERSION );

        // ajax
        add_action( 'wp_ajax_fwpcl_import', array( $this, 'import' ) );
        add_action( 'wp_ajax_fwpcl_save', array( $this, 'save_rules' ) );

        // wp hooks
        add_action( 'wp_footer', array( $this, 'render_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }


    function import() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $rulesets = stripslashes( $_POST['import_code'] );
        update_option( 'fwpcl_rulesets', $rulesets );
        _e( 'All done!', 'facetwp-conditional-logic' );
        exit;
    }


    function save_rules() {
        if ( current_user_can( 'manage_options' ) ) {
            $rulesets = stripslashes( $_POST['data'] );
            $json_test = json_decode( $rulesets, true );

            // check for valid JSON
            if ( is_array( $json_test ) ) {
                update_option( 'fwpcl_rulesets', $rulesets );
                _e( 'Rules saved', 'facetwp-conditional-logic' );
            }
            else {
                _e( 'Error: invalid JSON', 'facetwp-conditional-logic' );
            }
        }
        exit;
    }


    function admin_menu() {
        add_options_page( 'FacetWP Logic', 'FacetWP Logic', 'manage_options', 'fwpcl-admin', array( $this, 'settings_page' ) );
    }


    function enqueue_scripts( $hook ) {
        if ( 'settings_page_fwpcl-admin' == $hook ) {
            wp_enqueue_script( 'fwpcl-admin' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_style( 'media-views' );
            wp_enqueue_style( 'fwp-admin' );
            wp_enqueue_style( 'fwpcl-admin' );
            wp_localize_script( 'fwpcl-admin', 'FWPCL', array( 'rulesets' => $this->rulesets, 'i18n' => $this->admin_i18n ) );
        }
    }


    function settings_page() {
        include( dirname( __FILE__ ) . '/page-settings.php' );
    }


    function render_assets() {
        wp_enqueue_style( 'fwpcl-front' );
        wp_enqueue_script( 'fwpcl-front' );
        wp_localize_script( 'fwpcl-front', 'FWPCL', array( 'rulesets' => $this->rulesets ) );
    }
}


new FacetWP_Conditional_Logic_Addon();
