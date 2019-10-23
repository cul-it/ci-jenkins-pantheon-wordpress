<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/tap87
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
 * @author     Tahir Poduska <tahir.poduska@cornell.edu>
 */
class Wp_Libcal_Hours_Admin {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	function libcal_hours_plugin_menu() {
	   add_submenu_page( "options-general.php",
	                  "LibCal Hours",            // Page title
	                  "LibCal Hours",            // Menu title
	                  "manage_options",       // Minimum capability (manage_options is an easy way to target administrators)
										$this->plugin_name,
										array( $this, 'libcal_hours_plugin_options' )
	               );
	}

	function libcal_hours_plugin_options() {
	 if ( !current_user_can( "manage_options" ) )  {
	    wp_die( __( "You do not have sufficient permissions to access this page." ) );
	 }
	 include_once 'partials/wp-libcal-hours-admin-display.php';
	}

	public function libcal_hours_plugin_settings() {
	$section_name =  'wp_libcal_hours_general';
		add_settings_section(
			$section_name,
			'LibCal Settings Section',
			null,
			$this->plugin_name
		);

		$option_name = 'libcal_library_id';
		add_settings_field(
			$option_name,
			'Library Name',
			array( $this, $option_name . '_cb' ),
			$this->plugin_name,
			$section_name,
			array( 'label_for' => $option_name )
		);
		register_setting( $this->plugin_name, $option_name, 'sanitize_text_field' );
}
/**
 * Render the LibCal API endpoint input for this plugin
 *
 * @since  1.0.0
 */
public function libcal_library_id_cb() {
	$option_name = 'libcal_library_id';
	$lid         = get_option( $option_name );
	// echo '<input type="number" class="regular-text" name="' . $option_name . '" id="' . $option_name . '" value="' . esc_attr( $lid ) . '"> ';
	echo '<select name = "'. $option_name . '">
	<option>Select Library</option>
  <option value="3319" ';  echo $lid == 3319? "selected" : ""; echo '>Africana Library</option>
  <option value="7862" ';  echo $lid == 7862? "selected" : ""; echo '>Engineering Library</option>
  <option value="3321" ';  echo $lid == 3321? "selected" : ""; echo '>Fine Arts Library	</option>
  <option value="3322" ';  echo $lid == 3322? "selected" : ""; echo '>Hotel School Library</option>
	<option value="3323" ';  echo $lid == 3323? "selected" : ""; echo '>Industrial and Labor Relations Library</option>
	<option value="3349" ';  echo $lid == 3349? "selected" : ""; echo '> - ILR Reference</option>
	<option value="3350" ';  echo $lid == 3350? "selected" : ""; echo '> - ILR Kheel Center</option>
	<option value="3324" ';  echo $lid == 3324? "selected" : ""; echo '>Kroch Library, Division of Asia Collections</option>
	<option value="3325" ';  echo $lid == 3325? "selected" : ""; echo '>Kroch Library, Division of Rare & Manuscript Collections</option>
	<option value="3423" ';  echo $lid == 3423? "selected" : ""; echo '> - RMC Exhibition Gallery</option>
	<option value="3476" ';  echo $lid == 3476? "selected" : ""; echo '>Law Circulation</option>
	<option value="3478" ';  echo $lid == 3478? "selected" : ""; echo '>- Law Reference</option>
	<option value="3326" ';  echo $lid == 3326? "selected" : ""; echo '>Library Annex</option>
	<option value="3340" ';  echo $lid == 3340? "selected" : ""; echo '>Management Library w/Johnson ID</option>
	<option value="3363" ';  echo $lid == 3363? "selected" : ""; echo '> - Management Circulation</option>
	<option value="3361" ';  echo $lid == 3361? "selected" : ""; echo '> - Management Reference</option>
	<option value="1707" ';  echo $lid == 1707? "selected" : ""; echo '>Mann Library</option>
	<option value="3485" ';  echo $lid == 3485? "selected" : ""; echo '> - Mann Circulation</option>
	<option value="1710" ';  echo $lid == 1710? "selected" : ""; echo '> - Mann Reference</option>
	<option value="8658" ';  echo $lid == 8658? "selected" : ""; echo '> - Makerspace</option>
	<option value="3327" ';  echo $lid == 3327? "selected" : ""; echo '>Mathematics Library</option>
	<option value="3328" ';  echo $lid == 3328? "selected" : ""; echo '>Music Library</option>
	<option value="2818" ';  echo $lid == 2818? "selected" : ""; echo '>Olin Library</option>
	<option value="3119" ';  echo $lid == 3119? "selected" : ""; echo '> - Olin Reference</option>
	<option value="3329" ';  echo $lid == 3329? "selected" : ""; echo '>Ornithology Library</option>
	<option value="10937" ';  echo $lid == 10937? "selected" : ""; echo '>Physical Sciences Library</option>
	<option value="2830" ';  echo $lid == 2830? "selected" : ""; echo '>Uris Library</option>
	<option value="3331" ';  echo $lid == 3331? "selected" : ""; echo '>Veterinary Library</option>
</select>';
}

/**
 * Filter-callback function that adds links to the list of links displayed on the plugins page.
 *
 * @param array  $actions array List of existing links.
 *
 * @return array The updated list of links.
 *
 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
 *
 * @since 1.0.0
 */
public function add_action_links( $actions ) {
	$settings = '<a href="' . esc_attr( get_admin_url( null,
			'options-general.php?page=wp_libcal_hours' ) ) . '">' . __( 'Settings', 'General' ) . '</a>';
	array_unshift( $actions, $settings );
	return $actions;
}
/**
 * Update option callback on the "Ignore Cache" setting.
 * Clears out any LibCal data that may be in the transient cache if this option's value changes.
 *
 * @link https://developer.wordpress.org/reference/hooks/update_option_option/
 *
 * @since 1.0.0
 */
public function update_option_ignore_cache() {
	delete_transient( 'libcal_hours_today_data' );
}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-libcal-hours-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-libcal-hours-admin.js', array( 'jquery' ), $this->version, false );

	}

}
