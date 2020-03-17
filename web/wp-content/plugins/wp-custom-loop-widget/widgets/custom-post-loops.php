<?php
namespace WpCustomPostLoops\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Wp_Custom_Post_Loops extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wp-custom-post-loops';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'WP Custom Post Loops', 'wp-custom-post-loops' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-sync';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'wp-custom-post-loops' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Custom Post Loop', 'wp-custom-post-loops' ),
			]
		);

		$this->add_control(
			'loop_select',
			[
				'label' => 'Select Custom Loop',
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Select Custom Loop', 'CUL' ),
					'staff_profiles' => __( 'All Units - Staff Profiles', 'CUL' ),
					'africana_thesissingle' => __( 'Africana - Thesis Single', 'CUL' ),
          'africana_thesislist' => __( 'Africana - Thesis List', 'CUL' ),
					'engineering_databases' => __( 'Engineering - Databases', 'CUL' ),
          'ilr_botm' => __( 'ILR - Book of the Month', 'CUL' ),
					'ilr_wit' => __( 'ILR - Workplace Issues Today', 'CUL' ),
					'law_fellows' => __( 'Law - Fellows', 'CUL' ),
					'management_dbsingle' => __( 'Management - Databases Single (Main)', 'CUL' ),
          'management_dbsinglesidebar' => __( 'Management - Databases Single (Sidebar)', 'CUL' ),
          'management_dblist' => __( 'Management - Databases List', 'CUL' ),
          'management_faqssingle' => __( 'Management - FAQs Single', 'CUL' ),
					'management_faqslist' => __( 'Management - FAQs List', 'CUL' ),
					'math_collectedworks' => __( 'Math - Collected Works', 'CUL' ),
					'psl_corebooks' => __( 'PSL - Core Books', 'CUL' ),
					'psl_databases' => __( 'PSL - Databases', 'CUL' ),
					'psl_journals' => __( 'PSL - Journals', 'CUL' ),
					'rare_onlineexhibitions' => __( 'Rare - Online Exhibitions', 'CUL' ),
				],
				'default' => 'default',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_type_selection = $settings['loop_select'];
    if ($post_type_selection == "staff_profiles") :
      include_once 'partials/staff_profiles.php';
    elseif ($post_type_selection == "africana_thesissingle") :
      include_once 'partials/africana_thesissingle.php';
    elseif ($post_type_selection == "africana_thesislist") :
      include_once 'partials/africana_thesislist.php';
    elseif ($post_type_selection == "engineering_databases") :
      include_once 'partials/engineering_databases.php';
    elseif ($post_type_selection == "ilr_botm") :
      include_once 'partials/ilr_botm.php';
    elseif ($post_type_selection == "ilr_wit") :
      include_once 'partials/ilr_wit.php';
		elseif ($post_type_selection == "law_fellows") :
			include_once 'partials/law_fellows.php';
    elseif ($post_type_selection == "management_dbsingle") :
      include_once 'partials/management_dbsingle.php';
    elseif ($post_type_selection == "management_dbsinglesidebar") :
      include_once 'partials/management_dbsinglesidebar.php';
    elseif ($post_type_selection == "management_dblist") :
      include_once 'partials/management_dblist.php';
    elseif ($post_type_selection == "management_faqssingle") :
      include_once 'partials/management_faqssingle.php';
    elseif ($post_type_selection == "management_faqslist") :
      include_once 'partials/management_faqslist.php';
    elseif ($post_type_selection == "math_collectedworks") :
      include_once 'partials/math_collectedworks.php';
    elseif ($post_type_selection == "psl_corebooks") :
      include_once 'partials/psl_corebooks.php';
    elseif ($post_type_selection == "psl_databases") :
      include_once 'partials/psl_databases.php';
    elseif ($post_type_selection == "psl_journals") :
      include_once 'partials/psl_journals.php';
    elseif ($post_type_selection == "rare_onlineexhibitions") :
      include_once 'partials/rare_onlineexhibitions.php';
		endif;
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<div class="title">
			{{{ settings.loop_select }}}
		</div>
		<?php
	}
}
