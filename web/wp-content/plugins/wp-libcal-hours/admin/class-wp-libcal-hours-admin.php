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

    $options = [
      'libcal_library_id_1' => 'Required Library Name',
      'libcal_library_id_2' => 'Optional: Library Name (only used with multiple header display)'
    ];

    foreach ($options as $name=>$label) {
      add_settings_field(
        $name,
        $label,
        array( $this, $name . '_cb' ),
        $this->plugin_name,
        $section_name,
        array( 'label_for' => $name )
      );
      register_setting( $this->plugin_name, $name, 'sanitize_text_field' );
    };
}
/**
 * Render the LibCal API endpoint input for this plugin
 *
 * @since  1.0.0
 */

public function libcal_library_id_1_cb($selected=false) {
  $opt = 'libcal_library_id_1';
  $selected = get_option($opt);
  $select_options = array( 
    3319 => 'Africana Library',
    7862 => 'Engineering Library', 
    3321 => 'Fine Arts Library', 
    3322 => 'Hotel School Library', 
    3323 => 'Industrial and Labor Relations Library',
    3349 => ' - ILR Reference',
    3350 => ' - ILR Kheel Center',
    3324 => 'Kroch Library, Division of Asia Collections',
    3325 => 'Kroch Library, Division of Rare & Manuscript Collections',
    3423 => ' - RMC Exhibition Gallery',
    3476 => 'Law Circulation',
    3478 => '- Law Reference',
    3326 => 'Library Annex',
    3340 => 'Management Library w/Johnson ID',
    3363 => ' - Management Circulation',
    3361 => ' - Management Reference',
    1707 => 'Mann Library',
    3485 => ' - Mann Circulation',
    1710 => ' - Mann Reference',
    8658 => ' - Makerspace',
    3327 => 'Mathematics Library',
    3328 => 'Music Library',
    2818 => 'Olin Library',
    3119 => ' - Olin Reference',
    3329 => 'Ornithology Library',
    10937 => 'Physical Sciences Library',
    2830 => 'Uris Library',
    3331 => 'Veterinary Library'
  );
  echo '<select name = "'. $opt . '">';
  echo  $selected == false ? '<option value="" selected="selected">Select Library</option>' : '<option value="" >Select Library</option>';
  while (list($key, $val) = each($select_options)) {
    echo '<option value="' . $key . '" ';
    if ($key == $selected){
      echo  ' selected = "selected" ';
    };
    echo "> " . $val . "</option>";
  }
  echo "</select>";
}
public function libcal_library_id_2_cb($selected=false) {
  $opt = 'libcal_library_id_2';
  $selected = get_option($opt);
  $select_options = array( 
    3319 => 'Africana Library',
    7862 => 'Engineering Library', 
    3321 => 'Fine Arts Library', 
    3322 => 'Hotel School Library', 
    3323 => 'Industrial and Labor Relations Library',
    3349 => ' - ILR Reference',
    3350 => ' - ILR Kheel Center',
    3324 => 'Kroch Library, Division of Asia Collections',
    3325 => 'Kroch Library, Division of Rare & Manuscript Collections',
    3423 => ' - RMC Exhibition Gallery',
    3476 => 'Law Circulation',
    3478 => '- Law Reference',
    3326 => 'Library Annex',
    3340 => 'Management Library w/Johnson ID',
    3363 => ' - Management Circulation',
    3361 => ' - Management Reference',
    1707 => 'Mann Library',
    3485 => ' - Mann Circulation',
    1710 => ' - Mann Reference',
    8658 => ' - Makerspace',
    3327 => 'Mathematics Library',
    3328 => 'Music Library',
    2818 => 'Olin Library',
    3119 => ' - Olin Reference',
    3329 => 'Ornithology Library',
    10937 => 'Physical Sciences Library',
    2830 => 'Uris Library',
    3331 => 'Veterinary Library'
  );
  echo '<select name = "'. $opt . '">';
  echo  $selected == false ? '<option value="" selected="selected">Select Library</option>' : '<option value="" >Select Library</option>';
  while (list($key, $val) = each($select_options)) {
    echo '<option value="' . $key . '" ';
    if ($key == $selected){
      echo  ' selected = "selected" ';
    };
    echo "> " . $val . "</option>";
  }
  echo "</select>";
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
	delete_transient( 'libcal_hours_weekly_data' );
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
