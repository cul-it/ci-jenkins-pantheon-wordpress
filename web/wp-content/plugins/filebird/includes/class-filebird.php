<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ninjateam.org
 * @since      1.0.0
 *
 * @package    FileBird
 * @subpackage FileBird/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    FileBird
 * @subpackage FileBird/includes
 * @author     Ninja Team <support@ninjateam.org>
 */
class FileBird
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      FileBird_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        $this->plugin_name = 'filebird';
        $this->version = NJT_FILEBIRD_VERSION;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - FileBird_Loader. Orchestrates the hooks of the plugin.
     * - FileBird_i18n. Defines internationalization functionality.
     * - FileBird_Admin. Defines all hooks for the admin area.
     * - FileBird_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'translation/filebird-js-translation.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/filebird-walkers.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-topbar.php';

        /**
         * The class create feedback.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-feedback.php';

        /**
         * The class create notification.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-notification.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-i18n.php';

        //user permission
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-user-folder.php';
        //helpers
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-helpers.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-convert.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-filebird-admin.php';

        /**
         * The class responsible for defining all actions that occur in the setting area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-filebird-setting.php';

        /**
         * The class support Multi Language.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-WPML.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filebird-PolyLang.php';

        $this->loader = new FileBird_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the FileBird_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new FileBird_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new FileBird_Admin($this->get_plugin_name(), $this->get_version());
        $plugin_setting = new FileBird_Setting($this->get_plugin_name(), $this->get_version());
        $feedback = new FileBird_Feedback();

        $user_folder = new FileBird_User_Folder();
        new FileBird_Convert();
        new FileBird_Notification($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_menu', $plugin_setting, 'create_admin_sub_menu');

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'nt_upload');

        $this->loader->add_action('init', $plugin_admin, 'filebird_add_folder_to_attachments');

        $this->loader->add_action('admin_footer-upload.php', $plugin_admin, 'filebird_add_init_media_manager');
        $this->loader->add_action('wp_ajax_filebird_ajax_get_folder_list', $plugin_admin, 'filebird_ajax_get_folder_list_callback');
        $this->loader->add_action('wp_ajax_filebird_ajax_update_folder_list', $plugin_admin, 'filebird_ajax_update_folder_list_callback');
        $this->loader->add_action('wp_ajax_filebird_ajax_delete_folder_list', $plugin_admin, 'filebird_ajax_delete_folder_list_callback');
        $this->loader->add_action('wp_ajax_filebird_ajax_update_folder_position', $plugin_admin, 'filebird_ajax_update_folder_position_callback');
        $this->loader->add_action('wp_ajax_filebird_ajax_get_child_folders', $plugin_admin, 'filebird_ajax_get_child_folders_callback');
        $this->loader->add_action('wp_ajax_filebird_ajax_save_splitter', $plugin_admin, 'filebird_ajax_save_splitter');
        $this->loader->add_action('wp_ajax_filebird_ajax_refresh_folder', $plugin_admin, 'filebird_ajax_refresh_folder');
        $this->loader->add_action('wp_ajax_filebird_ajax_treeview_folder', $plugin_admin, 'filebird_add_init_media_manager');
        $this->loader->add_filter('pre-upload-ui', $plugin_admin, 'filebird_pre_upload_ui');

        //Support for WPML
        $wpml = new NJT_FB_WPML($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $wpml, 'init', 9);

        //Support for PolyLang
        $PolyLang = new NJT_FB_PolyLang($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $PolyLang, 'init');

        //Support Elementor
        if (defined('ELEMENTOR_VERSION')) {
            add_action('elementor/editor/after_enqueue_scripts', function () {
                global $pagenow;

                $taxonomy = NJT_FILEBIRD_FOLDER;
                $taxonomy = apply_filters('filebird_taxonomy', $taxonomy);

                $dropdown_options = array(
                  'taxonomy' => $taxonomy,
                  'hide_empty' => false,
                  'hierarchical' => true,
                  'orderby' => 'name',
                  'show_count' => true,
                  'walker' => new filebird_walker_category_mediagridfilter(),
                  'value' => 'id',
                  'echo' => false,
                );
                $attachment_terms = wp_dropdown_categories($dropdown_options);
                $attachment_terms = preg_replace(array("/<select([^>]*)>/", "/<\/select>/"), "", $attachment_terms);
                $all_count = wp_count_posts('attachment')->inherit;

                echo '<script type="text/javascript">';
                echo '/* <![CDATA[ */';
                echo 'var filebird_folder = "' . NJT_FILEBIRD_FOLDER . '";';
                echo 'var filebird_taxonomies = {"folder":{"list_title":"' . html_entity_decode(__('All categories', NJT_FILEBIRD_TEXT_DOMAIN), ENT_QUOTES, 'UTF-8') . '","term_list":[{"term_id":"-1","term_name":"' . __('Uncategorized', NJT_FILEBIRD_TEXT_DOMAIN) . '"},' . substr($attachment_terms, 2) . ']}};';
                echo '/* ]]> */';
                echo '</script>';

                wp_enqueue_style('njt-filebird-treeview', NJT_FILEBIRD_PLUGIN_URL . '/admin/css/filebird-treeview.css', array(), $this->version);
                wp_register_script('njt-filebird-upload-localize', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/filebird-util.js', array('jquery', 'jquery-ui-draggable', 'jquery-ui-droppable'), $this->version, false);
                wp_localize_script('njt-filebird-upload-localize', 'filebird_translate', FileBird_JS_Translation::get_translation());
                wp_localize_script('njt-filebird-upload-localize', 'njtFBV', NJT_FB_V);
                wp_localize_script('njt-filebird-upload-localize', 'njt_fb_nonce', wp_create_nonce('ajax-nonce'));
                wp_enqueue_script('njt-filebird-upload-localize');
                wp_enqueue_script('filebird-admin-topbar', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/filebird-admin-topbar.js', array('media-views'), $this->version, true);
                wp_enqueue_script('filebird-droppable-elementor', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/droppable.min.js', array('jquery'), $this->version, false);
            });

            $this->loader->add_action('elementor/editor/after_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('elementor/editor/after_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
            $this->loader->add_action('elementor/editor/after_enqueue_scripts', $plugin_admin, 'nt_upload');
        }

        //Support Thrive
        // if (defined('TVE_IN_ARCHITECT') || class_exists('Thrive_Quiz_Builder')) {
        //     add_action('tcb_main_frame_enqueue', function(){
        //         global $pagenow;

        //         $taxonomy = NJT_FILEBIRD_FOLDER;
        //         $taxonomy = apply_filters('filebird_taxonomy', $taxonomy);

        //         $dropdown_options = array(
        //           'taxonomy' => $taxonomy,
        //           'hide_empty' => false,
        //           'hierarchical' => true,
        //           'orderby' => 'name',
        //           'show_count' => true,
        //           'walker' => new filebird_walker_category_mediagridfilter(),
        //           'value' => 'id',
        //           'echo' => false,
        //         );
        //         $attachment_terms = wp_dropdown_categories($dropdown_options);
        //         $attachment_terms = preg_replace(array("/<select([^>]*)>/", "/<\/select>/"), "", $attachment_terms);
        //         $all_count = wp_count_posts('attachment')->inherit;

        //         echo '<script type="text/javascript">';
        //         echo '/* <![CDATA[ */';
        //         echo 'var filebird_folder = "' . NJT_FILEBIRD_FOLDER . '";';
        //         echo 'var filebird_taxonomies = {"folder":{"list_title":"' . html_entity_decode(__('All categories', NJT_FILEBIRD_TEXT_DOMAIN), ENT_QUOTES, 'UTF-8') . '","term_list":[{"term_id":"-1","term_name":"' . __('Uncategorized', NJT_FILEBIRD_TEXT_DOMAIN) . '"},' . substr($attachment_terms, 2) . ']}};';
        //         echo '/* ]]> */';
        //         echo '</script>';

        //         wp_enqueue_style('njt-filebird-treeview', NJT_FILEBIRD_PLUGIN_URL . '/admin/css/filebird-treeview.css', array(), $this->version);
        //         wp_register_script('njt-filebird-upload-localize', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/filebird-util.js', array('jquery', 'jquery-ui-draggable', 'jquery-ui-droppable'), $this->version, false);
        //         wp_localize_script('njt-filebird-upload-localize', 'filebird_translate', FileBird_JS_Translation::get_translation());
        //         wp_localize_script('njt-filebird-upload-localize', 'njtFBV', NJT_FB_V);
        //         wp_localize_script('njt-filebird-upload-localize', 'njt_fb_nonce', wp_create_nonce('ajax-nonce'));
        //         wp_enqueue_script('njt-filebird-upload-localize');
        //         wp_enqueue_script('filebird-admin-topbar', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/filebird-admin-topbar.js', array('media-views'), $this->version, true);
        //         wp_enqueue_script('filebird-droppable-elementor', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/droppable.min.js', array('jquery'), $this->version, false);
        //     });

        //     $this->loader->add_action('elementor/editor/after_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        //     $this->loader->add_action('elementor/editor/after_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        //     $this->loader->add_action('elementor/editor/after_enqueue_scripts', $plugin_admin, 'nt_upload');
        // }
        add_action('tcb_main_frame_enqueue', function(){
                $taxonomy = NJT_FILEBIRD_FOLDER;
                $taxonomy = apply_filters('filebird_taxonomy', $taxonomy);

                $dropdown_options = array(
                  'taxonomy' => $taxonomy,
                  'hide_empty' => false,
                  'hierarchical' => true,
                  'orderby' => 'name',
                  'show_count' => true,
                  'walker' => new filebird_walker_category_mediagridfilter(),
                  'value' => 'id',
                  'echo' => false,
                );
                $attachment_terms = wp_dropdown_categories($dropdown_options);
                $attachment_terms = preg_replace(array("/<select([^>]*)>/", "/<\/select>/"), "", $attachment_terms);
                $all_count = wp_count_posts('attachment')->inherit;

                echo '<script type="text/javascript">';
                echo '/* <![CDATA[ */';
                echo 'var filebird_folder = "' . NJT_FILEBIRD_FOLDER . '";';
                echo 'var filebird_taxonomies = {"folder":{"list_title":"' . html_entity_decode(__('All categories', NJT_FILEBIRD_TEXT_DOMAIN), ENT_QUOTES, 'UTF-8') . '","term_list":[{"term_id":"-1","term_name":"' . __('Uncategorized', NJT_FILEBIRD_TEXT_DOMAIN) . '"},' . substr($attachment_terms, 2) . ']}};';
                echo '/* ]]> */';
                echo '</script>';
            wp_enqueue_script('filebird-admin-topbar', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/filebird-admin-topbar.js', array('media-views'), $this->version, true);
        });
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    FileBird_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
