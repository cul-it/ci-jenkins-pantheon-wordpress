<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/tap87
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
 * @author     Tahir Poduska <tahir.poduska@cornell.edu>
 */
class Wp_Libcal_Hours_Public {

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
	 * Hours api json object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $libcal_data	JSON object from libcal API
	 */
	private $libcal_hours_today = NULL;

	/**
	 * LibCal api endpoints.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $libcal_hours_today_endpoint = 'https://api3.libcal.com/api_hours_today.php?iid=973&format=json&systemTime=0&nocache=1';
	private $libcal_hours_weekly_endpoint = 'https://api3.libcal.com/api_hours_grid.php?iid=973&format=json&systemTime=0&nocache=1';

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->get_libcal_hours_today();
		$this->render_custom_hours_nav_buttons();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-libcal-hours-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-libcal-hours-public.js', array( 'jquery' ), $this->version, false );

	}

	public function libcal_hours_shortcodes() {
		add_shortcode( 'libcal_status_now', array( $this, 'libcal_status_now_func') );
		add_shortcode( 'libcal_hours_today', array( $this, 'libcal_hours_today_func') );
		add_shortcode( 'libcal_hours_weekly', array( $this, 'libcal_hours_weekly_func') );
		add_shortcode( 'libcal_hours_monthly', array( $this, 'libcal_hours_monthly_func') );
		add_shortcode( 'libcal_hours_combined', array( $this, 'libcal_hours_combined_func') );
	}

	/*
	* Get the data for todays hours from libcal and cache it for 2 mintues
	* to reduce the number of api calls to libcal
	*/
	private function get_libcal_hours_today() {
		$transient_key = 'libcal_hours_today_data';
		$data = get_transient( $transient_key );
		if ($data === false) {
			$lid = get_option( 'libcal_library_id' );
			$todays_hours_endpoint = $this->libcal_hours_today_endpoint . '&lid=' . $lid;
			$request = wp_remote_get($todays_hours_endpoint);
			if (is_wp_error($request)) {
			  return false;
			}

			$body = wp_remote_retrieve_body($request);
			$data = json_decode($body);
			$expiration = 120; // 2 minutes
			set_transient( $transient_key, $data, $expiration );
		}
		return $this->$libcal_hours_today = $data;
	}

	/*
	* Extract the location data for a particular library using the libcal_library_id setting in wp_admin
	* or the department using the lid passed with the shortcode
	*/
	private function get_location_data($atts) {
		$data = $this->$libcal_hours_today;
		$locations = $data->locations;
		if (gettype($atts) == 'array' && array_key_exists('lid', $atts)) {
			$lid = $atts['lid'];
		} else {
			$lid = get_option( 'libcal_library_id' );
		}
		$location_data_array  = array_values( array_filter( $locations,
			function ( $location_data ) use ( $lid ) {
				return ( $location_data->lid == $lid );
			} ) );
		$location_data =	$location_data_array[0];
		return $location_data;
	}
	/*
	* Get the current status of a library or a department within
	*/
	public function libcal_status_now_func($atts) {
		$location_data = $this->get_location_data($atts);
    $currently_open = $location_data->times->currently_open;
		$status = ($currently_open ? "Open" : "Closed");
		return $status;
	}
	/*
	* Get the time until open/next open time for alibrary or a department within
	*/
	public function libcal_hours_today_func($atts) {
		$hours = NULL;
		$hours_text = NULL;
		$location_data = $this->get_location_data($atts);
		$currently_open = $location_data->times->currently_open;
		$status = ($currently_open ? "Open" : "Closed");
				if ($currently_open) {
					// Check both the hours and status keys, libcal puts 24 hours in status
					if (array_key_exists('hours', $location_data->times)) {
						$hours = $location_data->times->hours[0]->to;
					} else {
						$hours = $location_data->times->status;
					}
					// Format to render friednly hours
					switch ($hours) {
						case "24hours":
							$hours = "24 hours";
							break;
						case "12am":
							$hours = "Midnight";
							break;
						case "12pm":
							$hours = "Noon";
							break;
						default:
							$hours;
							break;
					}
					if ($hours == '24 hours') {
						$hours_text = $hours;
					} else {
						$hours_text = ' until ' . $hours;
					}
				} else {
					$daynames = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
					if (gettype($atts) == 'array' && array_key_exists('lid', $atts)) {
						$lid = $atts['lid'];
					} else {
						$lid = get_option( 'libcal_library_id' );
					}
					$weekly_hours_endpoint = $this->libcal_hours_weekly_endpoint . '&lid=' . $lid . '&weeks=2';
					$request = wp_remote_get($weekly_hours_endpoint);

					if (is_wp_error($request)) {
					  return false;
					}

					$body = wp_remote_retrieve_body($request);
					$data = json_decode($body);
					$atts = shortcode_atts( ['lid'  => ''], $atts );
					$loc_id = 'loc_'.$lid;
					$weeks = count($data->$loc_id->weeks);
					// Array to hold the next open date and time
					$next_open = array();
					// Iterate over the weeks
        	// Get future opening time
          // daynames for iterating through libcal weekly hours
					foreach(range(0,$weeks) as $week) {
						foreach ($daynames as $dayname) {
							$today = date("Y-m-d");
							$tomorrow = date("Y-m-d", strtotime('tomorrow'));
							$date = reset($data)->weeks[$week]->$dayname->date;
							if ($date >= $today) {
								$status = reset($data)->weeks[$week]->$dayname->times->status;
								#  Check both the status if closed or 24 hours
								if ($status !='closed' || $status != '24hours' || $status != 'not-set' ) {
									$open_from = reset($data)->weeks[$week]->$dayname->times->hours[0]->from;
									# Jump to next day if library current time is past opening time
									if ($status == "open" && $date == $today && $now=time() > strtotime($open_from)) {
										continue;
									}
									# Format date
										if ($date == $today ) {
											$date = "";
										} else if ($date == $tomorrow ) {
											$date = "tomorrow";
										}	else {
											$date = date('M d', strtotime($date));
										}
										# Check status
										if($status == "open") {
											$next_open = array($date, $open_from);
											break;
										} else if ($status == "ByApp" || $status == "24hours") {
											$rendered = reset($data)->weeks[$week]->$dayname->rendered;
                    	$next_open = [$date, $rendered];
                    	break;
										} else if ($status == "closed") {
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
						if($next_open[1] == "By Appointment" || $next_open[1] == "24 Hours") {
							if($next_open[0] == "") {
								$hours_text = "Opens " . "today" . ' (' . $next_open[1] . ')';
							} else {
								$hours_text = "Opens " . $next_open[0] . ' (' . $next_open[1] . ')';
							}
						} else {
							if ($next_open[0] == "") {
								$hours_text = "Opens at " . $next_open[1];
							} else {
								$hours_text = "Opens " . $next_open[0] . ' at ' . $next_open[1];
							}
						}
					} else {
						$hours_text = NULL;
					}
				}
				return $hours_text;
	}

	/*
	* Weekly hours widget from libcal, pass lid for weekly hours of a department within a library
	*/
	public function libcal_hours_weekly_func($atts) {
		$html = $this->hours_nav_html;
		if (gettype($atts) == 'array' && array_key_exists('lid', $atts)) {
			$lid = $atts['lid'];
		} else {
			$lid = get_option( 'libcal_library_id' );
		}
		array_push($this->weekly_hours_lid, $lid);
		wp_enqueue_script( 'libcal_weekly_hours', 'https://api3.libcal.com/js/hours_grid.js?002' );
		wp_localize_script( 'libcal_weekly_hours', 'lid_weekly' , $this->weekly_hours_lid );
		wp_enqueue_script( 'semantic_ui_transitions_js', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.js' );
		wp_enqueue_style( 'semantic_ui_transitions_css', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.min.css' );
		// $o = '<div id=s-lc-whw' . $lid . ' ></div>';
		$html .= '<div id=libcal-weekly-hours></div>';
		return $html;
	}
	/*
	* Monthly hours widget from libcal
	*/
	public function libcal_hours_monthly_func() {
		$html = $this->hours_nav_html;
		$lid = get_option( 'libcal_library_id' );
		$atts['libcal_library_id'] = $lid;
		wp_enqueue_script( 'libcal_monthly_hours', 'https://api3.libcal.com/js/hours_month.js?002' );
		wp_enqueue_script( 'semantic_ui_transitions_js', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.js' );
		wp_enqueue_style( 'semantic_ui_transitions_css', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.min.css' );
		wp_localize_script( 'libcal_monthly_hours', 'lid_monthly', $atts );
		// $o = '<div id=s_lc_mhw_973_' . $lid . ' ></div>';
		$html .= '<div id=libcal-monthly-hours></div>';
		return $html;
	}
	/*
	* Combined weekly and monthly hours widgets using JS
	*/
	public function libcal_hours_combined_func() {
		$html = $this->hours_nav_html;
		$lid = get_option( 'libcal_library_id' );
		$atts['libcal_library_id'] = $lid;
		wp_enqueue_script( 'libcal_weekly_hours', 'https://api3.libcal.com/js/hours_grid.js?002' );
		wp_enqueue_script( 'libcal_monthly_hours', 'https://api3.libcal.com/js/hours_month.js?002' );
		wp_enqueue_script( 'libcal_monthly_hours', 'https://api3.libcal.com/js/hours_month.js?002' );
		wp_enqueue_script( 'semantic_ui_transitions_js', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.js' );
		wp_enqueue_style( 'semantic_ui_transitions_css', 'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.min.css' );
		wp_localize_script( 'libcal_monthly_hours', 'lid_combined', $atts );
		$html .= '<div id=libcal-weekly-hours></div>';
		$html .= '<div id=libcal-monthly-hours></div>';
		return $html;
	}

 // Render custom prev next/week month button to navigate the weekly/monthly calendar
	public function render_custom_hours_nav_buttons() {
		// Start output buffering.
		ob_start();
		include_once 'partials/libcal_hours_nav_buttons.php';
		// End output buffer and return it.
		return $this->hours_nav_html = ob_get_clean();
	}

}
