<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/kek245
 * @since      1.0.0
 *
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Libcal_Hours
 * @subpackage Wp_Libcal_Hours/public
 * @author     Kevin Kidwell <kek245@cornell.edu>
 */
class Wp_Libcal_Hours_Public
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
     * array of lids for weekly hours, to render  weekly hours for departments.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $weekly_hours_lid The array of lids if multiple shortcode used with different lid's
     */
    private $weekly_hours_lid = [];
    /**
     * html for calendar navigation buttons.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $hours_nav_html HTML for navigation/buttons for weekly, monthly libcal hours
     */
    private $hours_nav_html = [];
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version, $api_endpoints)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_endpoints = $api_endpoints;
        $this->render_custom_hours_nav_buttons();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-libcal-hours-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
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
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-libcal-hours-public.js', array('jquery'), $this->version, false);
    }

    public function libcal_hours_shortcodes()
    {
        add_shortcode('libcal_header_hours', array($this, 'libcal_header_hours_func'));
        add_shortcode('libcal_hours_today', array($this, 'libcal_hours_today_func'));
        add_shortcode('libcal_hours_weekly', array($this, 'libcal_hours_weekly_func'));
        add_shortcode('libcal_hours_monthly', array($this, 'libcal_hours_monthly_func'));
        add_shortcode('libcal_hours_combined', array($this, 'libcal_hours_combined_func'));
        add_shortcode('libcal_hours_sidebar_today', array($this, 'libcal_hours_sidebar_today_func'));
    }

    private function get_lid($atts)
    {
        if (gettype($atts) == 'array' && array_key_exists('lid', $atts)) {
            $lid = $atts['lid'];
        } else {
            $lid = get_option('libcal_library_id_1');
        }
        return $lid;
    }

    private function set_transients($atts, $type)
    {
        $lid = $this->get_lid($atts);
        if ($type == "today") {
            $transient_key = 'libcal_hours_today_data_' . $lid;
            $endpoint = $this->api_endpoints->today . '&lid=' . $lid;
        } else if ($type == "week") {
            $transient_key = 'libcal_hours_weekly_data';
            $endpoint = $this->api_endpoints->weekly . '&lid=' . $lid . '&weeks=2';
        }
        $data = get_transient($transient_key);
        if ($data === false) {
            $request = wp_remote_get($endpoint);
            if (is_wp_error($request)) {
                return false;
            }
            $body = wp_remote_retrieve_body($request);
            $data = json_decode($body);
            set_transient($transient_key, $data, 60);
        }
        return $data;
    }

    /*
  * First item in list of locations returned from LibCal is always the requested location
  */
    private function get_location_data($atts, $type)
    {
        $data = $this->set_transients($atts, $type);
        return $data->locations[0];
    }

    public function libcal_header_hours_func($atts)
    {
        $lid = $this->get_lid($atts);
        $hours_today = $this->libcal_hours_today_func($atts);
        $location_data = $this->get_location_data($atts, 'today');
        $name = $location_data->name;
        $category = $location_data->category;
        $html = '<span id="header-hours-' . $lid . '" class="' . $category . '">';
        if ($name != NULL) {
            // Display the department name from LibCal if it is a department
            if ($category == "department") {
                $html .= '<i class="fas fa-clock icon-time" aria-hidden="true"><span class="sr-only">Department hours status</span></i>';
                $html .= '<span class="libcal-unit-name">' . $name . ': </span>';
            } else {
                $html .= '<i class="fas fa-clock icon-time" aria-hidden="true"><span class="sr-only">Library hours status</span></i>';
            }
            // Display the Library unit name if it is Olin or Uris
            if ($category == "library" && ($name == ('Olin Library') || $name == ('Uris Library'))) {
                // Remove "Library" from the end of the unit name
                $display_name = preg_replace('/\W\w+\s*(\W*)$/', '$1', $name);
                $html .= '<span class="libcal-unit-name"><strong>' . $display_name . ': </strong></span>';
            }
        }
        $html .= '<span class="libcal-hours-today">' . $hours_today . '</span>';
        $html .= '</span>';
        return $html;
    }

    private function current_status($location_data)
    {
        $status = ($location_data->times->currently_open ? "Open" : "Closed");
        return $status;
    }

    private function set_hours_text($hours, $status)
    {
        if ($status == "Closed") {
            if (!empty($hours)) {
                $set_hours_text = $status . ' - ' . $hours;
            } else {
                $set_hours_text = "Closed";
            }
        } else {
            if ($hours == "24hours") {
                $set_hours_text = "24 hours";
            } else if ($hours == "12am") {
                $set_hours_text = $status . ' until Midnight';
            } else if ($hours == "12pm") {
                $set_hours_text = $status . ' until Noon';
            } else {
                $set_hours_text = $status . ' until ' . $hours;
            }
        }
        return $set_hours_text;
    }

    /*

	* Get the time until open/next open time for a library or a department within
	*/
    public function libcal_hours_today_func($atts)
    {
        date_default_timezone_set('America/New_York');
        $location_data = $this->get_location_data($atts, 'today');
        $status = $this->current_status($location_data);
        if ($status == "Closed" && ($location_data->times->status != "text") && ($location_data->times->status != "ByApp")) {
            $hours_text = $this->set_hours_text($hours, $status);
        } else if (array_key_exists('hours', $location_data->times) && ($status == "Open")) {
            $hours = $location_data->times->hours[0]->to;
            $hours_text = $this->set_hours_text($hours, $status);
        } else if (!empty($location_data->rendered) && (($location_data->times->status == "text") || ($location_data->times->status == "ByApp"))) {
            $hours_text = $location_data->rendered;
        } else if (!empty($location_data->rendered) && ($location_data->times->status == "24hours")) {
            $hours_text = "Open " . $location_data->rendered;
        } else {
            $hours = $location_data->times->text;
            if ($hours != NULL) {
                $hours_text = $this->set_hours_text($hours, $status);
            } else {
                $daynames = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
                $lid = $this->get_lid($atts);
                $data = $this->set_transients($atts, 'week');
                $atts = shortcode_atts(['lid'  => ''], $atts);
                $loc_id = 'loc_' . $lid;
                $weeks = count($data->$loc_id->weeks);
                // Array to hold the next open date and time
                $next_open = array();
                // Iterate over the weeks
                // Get future opening time
                // daynames for iterating through libcal weekly hours
                foreach (range(0, $weeks) as $week) {
                    foreach ($daynames as $dayname) {
                        $today = date("Y-m-d");
                        $tomorrow = date("Y-m-d", strtotime('tomorrow'));
                        $date = reset($data)->weeks[$week]->$dayname->date;
                        if ($date >= $today) {
                            $status = reset($data)->weeks[$week]->$dayname->times->status;
                            #  Check both the status if closed or 24 hours
                            if ($status != 'closed' || $status != '24hours' || $status != 'not-set') {
                                $open_from = reset($data)->weeks[$week]->$dayname->times->hours[0]->from;
                                # Jump to next day if library current time is past opening time
                                if ($status == "open" && $date == $today && $now = time() > strtotime($open_from)) {
                                    continue;
                                }
                                # Format date
                                if ($date == $today) {
                                    $date = "";
                                } else if ($date == $tomorrow) {
                                    $date = "tomorrow";
                                } else {
                                    $date = date('M d', strtotime($date));
                                }
                                # Check status
                                if ($status == "open") {
                                    $next_open = array($date, $open_from);
                                    break;
                                } else if ($status == "ByApp" || $status == "24hours") {
                                    $rendered = reset($data)->weeks[$week]->$dayname->rendered;
                                    $next_open = [$date, $rendered];
                                    break;
                                }
                            }
                        }
                    }
                    # Break out of the weeks loop if next_open is populated, to prevent going to next week
                    if (!empty($next_open)) {
                        break;
                    }
                }
                if (!empty($next_open)) {
                    if ($next_open[1] == "By Appointment" || $next_open[1] == "24 Hours") {
                        if ($next_open[0] == "") {
                            $hours_text = "Closed - Opens " . "today" . ' (' . $next_open[1] . ')';
                        } else {
                            $hours_text = "Closed - Opens " . $next_open[0] . ' (' . $next_open[1] . ')';
                        }
                    } else {
                        if ($next_open[0] == "") {
                            $hours_text = "Closed - Opens at " . $next_open[1];
                        } else {
                            $hours_text = "Closed - Opens " . $next_open[0] . ' at ' . $next_open[1];
                        }
                    }
                } else {
                    $hours_text = NULL;
                }
            }
        }
        return $hours_text;
    }

    /*
	* Weekly hours widget from libcal, pass lid for weekly hours of a department within a library
	*/
    public function libcal_hours_weekly_func($atts)
    {
        $html = $this->hours_nav_html;
        $lid = $this->get_lid($atts);
        array_push($this->weekly_hours_lid, $lid);
        wp_enqueue_script('libcal_weekly_hours', 'https://api3.libcal.com/js/hours_grid.js?002');
        wp_localize_script('libcal_weekly_hours', 'lid_weekly', $this->weekly_hours_lid);
        wp_enqueue_script('semantic_ui_transitions_js', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.js');
        wp_enqueue_style('semantic_ui_transitions_css', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.min.css');
        $html .= '<div id=s-lc-whw-' . $lid . ' ></div>';
        return $html;
    }
    /*
	* Monthly hours widget from libcal
	*/
    public function libcal_hours_monthly_func()
    {
        $html = $this->hours_nav_html;
        $lid = $this->get_lid($atts);
        wp_enqueue_script('libcal_monthly_hours', 'https://api3.libcal.com/js/hours_month.js?002');
        wp_enqueue_script('semantic_ui_transitions_js', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.js');
        wp_enqueue_style('semantic_ui_transitions_css', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.min.css');
        wp_localize_script('libcal_monthly_hours', 'lid_monthly', $atts);
        $html .= '<div id=s_lc_mhw_' . $lid . ' ></div>';
        return $html;
    }
    /*
	* Combined weekly and monthly hours widgets using JS
	*/
    public function libcal_hours_combined_func()
    {
        $html = $this->hours_nav_html;
        $lid = $this->get_lid($atts);
        wp_enqueue_script('libcal_weekly_hours', 'https://api3.libcal.com/js/hours_grid.js?002');
        wp_enqueue_script('libcal_monthly_hours', 'https://api3.libcal.com/js/hours_month.js?002');
        wp_enqueue_script('libcal_monthly_hours', 'https://api3.libcal.com/js/hours_month.js?002');
        wp_enqueue_script('semantic_ui_transitions_js', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.js');
        wp_enqueue_style('semantic_ui_transitions_css', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.min.css');
        wp_localize_script('libcal_monthly_hours', 'lid_combined', $atts);
        $html .= '<div id=s_lc_whw_' . $lid . ' ></div>';
        $html .= '<div id=s_lc_mhw_' . $lid . ' ></div>';
        return $html;
    }

    // Render custom prev next/week month button to navigate the weekly/monthly calendar
    public function render_custom_hours_nav_buttons()
    {
        // Start output buffering.
        ob_start();
        include_once 'partials/libcal_hours_nav_buttons.php';
        // End output buffer and return it.
        return $this->hours_nav_html = ob_get_clean();
    }

    // Create the sidebar display for a single unit for a day's hours
    public function libcal_hours_sidebar_today_func($atts)
    {
        $lid = $this->get_lid($atts);
        $weekly_location_data = $this->set_transients($atts, 'week');
        $loc_id = 'loc_' . $lid;
        $weeks = count($weekly_location_data->$loc_id->weeks);
        $daynames = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        foreach (range(0, $weeks) as $week) {
            if ($week == 0) {
                echo '<div class="week">';
                foreach ($daynames as $dayname) {
                    $hours = reset($weekly_location_data)->weeks[$week]->$dayname->rendered;
                    $date = reset($weekly_location_data)->weeks[$week]->$dayname->date;
                    echo '<div class="list-hours"><h4>';
                    echo $dayname;
                    echo ' - ';
                    echo date("m/d", strtotime($date));
                    echo '</h4><div class="hours">';
                    echo $hours;
                    echo '</div></div>';
                }
                echo '</div>';
            }
        }
    }
}
