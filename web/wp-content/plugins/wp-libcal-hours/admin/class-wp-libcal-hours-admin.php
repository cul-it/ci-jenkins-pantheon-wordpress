<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/kek245
 * @since      1.0.0
 *
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/admin
 * @author     Kevin Kidwell <kek245@cornell.edu>
 */
class Wp_Libcal_Hours_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version, $api_endpoints)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_endpoints = $api_endpoints;
    }

    /**
     * Get a list of locations from LibCal hours & cache for 2 minutes.
     *
     * @since    3.0.0
     * @return   mixed
     * @access   private
     */
    private function get_libcal_hours_locations()
    {
        // Use cached data if it exists
        if (false === ($data = get_transient('libcal_hours_locations'))) {
            // Transient cache wasn't available, so fetch from LibCal API & save in cache
            $request = wp_remote_get($this->api_endpoints->today);
            if (is_wp_error($request)) {
                return false;
            }

            $body = wp_remote_retrieve_body($request);
            $data = $this->prep_options(json_decode($body)->locations);

            // Store the location data in the transient cache for 2 minutes max
            set_transient('libcal_hours_locations', $data, 2 * MINUTE_IN_SECONDS);
        }

        return $data;
    }

    /**
     * Build associative array of lid --> location_name.
     *
     * @since    3.0.0
     * @return   array
     * @access   private
     */
    private function prep_options($locations)
    {
        $options = array();

        foreach ($locations as $loc) {
            // Prefix all departments to indicate hierarchy in select list
            $name = isset($loc->parent_lid) ? "-- $loc->name" : $loc->name;
            $options[$loc->lid] = $name;
        }

        return $options;
    }

    /**
     * Menu for plugin admin settings page.
     *
     * @since    1.0.0
     * @access   public
     */
    public function libcal_hours_plugin_menu()
    {
        add_submenu_page(
            "options-general.php",
            "LibCal Hours",            // Page title
            "LibCal Hours",            // Menu title
            "manage_options",       // Minimum capability (manage_options is an easy way to target administrators)
            $this->plugin_name,
            array($this, 'libcal_hours_plugin_options')
        );
    }

    /**
     * Render admin settings page if user can access.
     *
     * @since    1.0.0
     * @access   public
     */
    public function libcal_hours_plugin_options()
    {
        if (!current_user_can("manage_options")) {
            wp_die(__("You do not have sufficient permissions to access this page."));
        }
        include_once 'partials/wp-libcal-hours-admin-display.php';
    }

    /**
     * Configure plugin admin settings page.
     *
     * @since    1.0.0
     * @access   public
     */
    public function libcal_hours_plugin_settings()
    {
        $section_name =  'wp_libcal_hours_general';
        add_settings_section(
            $section_name,
            'LibCal Settings Section',
            null,
            $this->plugin_name
        );

        $options = [
            'libcal_library_id_1' => 'Required Library Name',
            'libcal_library_id_2' => 'Optional: Library Department'
        ];

        foreach ($options as $name => $label) {
            add_settings_field(
                $name,
                $label,
                array($this, $name . '_cb'),
                $this->plugin_name,
                $section_name,
                array('label_for' => $name)
            );
            register_setting($this->plugin_name, $name, 'sanitize_text_field');
        };
    }

    /**
     * Callback for libcal_library_id_1 plugin setting.
     *
     * @since    2.0.0
     * @access   public
     */
    public function libcal_library_id_1_cb()
    {
        $opt = 'libcal_library_id_1';
        $selected = get_option($opt);
        $select_options = $this->get_libcal_hours_locations();

        echo $this->render_select_list($opt, $selected, $select_options);
    }

    /**
     * Callback for libcal_library_id_2 plugin setting.
     *
     * @since    2.0.0
     * @access   public
     */
    public function libcal_library_id_2_cb()
    {
        $opt = 'libcal_library_id_2';
        $selected = get_option($opt);
        $select_options = $this->get_libcal_hours_locations();

        echo $this->render_select_list($opt, $selected, $select_options);
    }

    /**
     * Render locations from LibCal Hours as select list.
     *
     * @since    3.0.0
     * @param    string    $name       The name for the select element.
     * @param    string    $selected   The selected option.
     * @param    array     $options    The list of locations.
     * @return   string
     * @access   private
     */
    private function render_select_list($name, $selected, $options)
    {
        $markup = <<<EOT
      <select name="$name">
        <option value="">Select Library</option>
EOT;
        foreach ($options as $lid => $name) {
            $chosen = $lid == $selected ? "selected" : "";
            $markup .= <<<EOT
        <option value="$lid" $chosen>$name</option>
EOT;
        }
        $markup .= "</select>";

        return $markup;
    }

    /**
     * Filter-callback function that adds links to the list of links displayed on the plugins page.
     *
     * @param    array  $actions array List of existing links.
     * @return   array The updated list of links.
     * @link     https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
     * @since    1.0.0
     * @return   array
     * @access   public
     */
    public function add_action_links($actions)
    {
        $settings = '<a href="' . esc_attr(get_admin_url(
            null,
            'options-general.php?page=wp_libcal_hours'
        )) . '">' . __('Settings', 'General') . '</a>';
        array_unshift($actions, $settings);
        return $actions;
    }
    /**
     * Update option callback on the "Ignore Cache" setting.
     * Clears out any LibCal data that may be in the transient cache if this option's value changes.
     *
     * @link     https://developer.wordpress.org/reference/hooks/update_option_option/
     * @since    1.0.0
     * @access   public
     */
    public function update_option_ignore_cache()
    {
        $lid = get_option('libcal_library_id_1');
        $lid2 = get_option('libcal_library_id_2');
        delete_transient('libcal_hours_today_data_' . $lid);
        delete_transient('libcal_hours_today_data_' . $lid2);
        delete_transient('libcal_hours_weekly_data');
        delete_transient('libcal_hours_locations');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Libcal_Hours_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Libcal_Hours_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-libcal-hours-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Libcal_Hours_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Libcal_Hours_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-libcal-hours-admin.js', array('jquery'), $this->version, false);
    }
}
