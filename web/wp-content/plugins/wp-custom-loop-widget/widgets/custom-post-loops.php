<?php

namespace WpCustomPostLoops\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Wp_Custom_Post_Loops extends Widget_Base
{

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
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
	public function get_title()
	{
		return __('WP Custom Post Loops', 'wp-custom-post-loops');
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
	public function get_icon()
	{
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
	public function get_categories()
	{
		return ['general'];
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
	public function get_script_depends()
	{
		return ['wp-custom-post-loops'];
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
				'label' => __('Custom Post Loop', 'wp-custom-post-loops'),
			]
		);
		
		$options = [
			'default' => __('Select Custom Loop', 'CUL'),
			'staff_profiles' => __('All Units - Staff Profiles', 'CUL'),
			'staff_profiles_single' => __('All Units - Staff Profiles Single', 'CUL'),
		];

		$africana = [
			'africana_audiolist' => __('Africana - Audio List', 'CUL'),
			'africana_audio_single' => __('Africana - Audio Single', 'CUL'),
			'africana_films_list' => __('Africana - Films List', 'CUL'),
			'africana_films_single' => __('Africana - Films Single', 'CUL'),
			'africana_thesis_single' => __('Africana - Thesis Single', 'CUL'),
			'africana_thesis_list' => __('Africana - Thesis List', 'CUL'),
		];

		$engineering = [
			'engineering_db_list' => __('Engineering - Databases List', 'CUL'),
			'engineering_db_single' => __('Engineering - Databases Single (Main)', 'CUL'),
			'engineering_db_sidebar' => __('Engineering - Databases Single (Sidebar)', 'CUL'),
		];

		$ilr = [
			'ilr_botm' => __('ILR - Book of the Month', 'CUL'),
			'ilr_botm_single' => __('ILR - Book of the Month Single', 'CUL'),
			'ilr_wit_list' => __('ILR - Workplace Issues Today (List)', 'CUL'),
			'ilr_wit_single' => __('ILR - Workplace Issues Today (Single)', 'CUL'),
		];

		$law = [
			'law_bitnerfellows' => __('Law - Bitner Fellows', 'CUL'),
			'law_bitnerfellows_single' => __('Law - Bitner Fellows Single', 'CUL'),
			'law_diversityfellows' => __('Law - Diversity Fellows', 'CUL'),
			'law_diversityfellows_single' => __('Law - Diversity Fellows Single', 'CUL'),
		];

		$management = [
			'management_alltopics' => __('Management - All Topics', 'CUL'),
			'management_db_single' => __('Management - Databases Single (Main)', 'CUL'),
			'management_db_single_sidebar' => __('Management - Databases Single (Sidebar)', 'CUL'),
			'management_db_list' => __('Management - Databases List', 'CUL'),
			'management_faqs_single' => __('Management - FAQs Single', 'CUL'),
			'management_faqs_list' => __('Management - FAQs List', 'CUL'),
			'management_search' => __('Management - Search', 'CUL'),
		];

		$math = [
			'math_collectedworks' => __('Math - Collected Works', 'CUL'),
			'math_collectedworks_single' => __('Math - Collected Works Single', 'CUL'),
			'math_recommendedbooks' => __('Math - Recommended Books', 'CUL'),
			'math_recommendedbooks_single' => __('Math - Recommended Books Single', 'CUL'),
		];

		$psl = [
			'psl_corebooks' => __('PSL - Core Books', 'CUL'),
			'psl_corebooks_single' => __('PSL - Core Books Single', 'CUL'),
			'psl_databases' => __('PSL - Databases', 'CUL'),
			'psl_databases_single' => __('PSL - Databases Single', 'CUL'),
			'psl_journals' => __('PSL - Journals', 'CUL'),
			'psl_journals_single' => __('PSL - Journals Single', 'CUL'),
		];

		$rare = [
			'rare_onlineexhibitions' => __('Rare - Online Exhibitions', 'CUL'),
			'rare_onlineexhibitions_single' => __('Rare - Online Exhibitions Single', 'CUL'),
		];


		$unit = constant('CUL_UNIT');
		if (isset($$unit)) {
		  $options = array_merge($options, $$unit);
		}

		$this->add_control(
			'loop_select',
			[
				'label' => 'Select Custom Loop',
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'Select Custom Loop',
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
	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$post_type_selection = $settings['loop_select'];
		if ($post_type_selection == "staff_profiles") :
			include_once 'partials/staff_profiles.php';
		elseif ($post_type_selection == "staff_profiles_single") :
			include_once 'partials/staff_profiles_single.php';
		elseif ($post_type_selection == "africana_audio_list") :
			include_once 'partials/africana_audio_list.php';
		elseif ($post_type_selection == "africana_audio_single") :
			include_once 'partials/africana_audio_single.php';
		elseif ($post_type_selection == "africana_films_single") :
			include_once 'partials/africana_films_single.php';
		elseif ($post_type_selection == "africana_thesis_single") :
			include_once 'partials/africana_thesis_single.php';
		elseif ($post_type_selection == "africana_thesis_list") :
			include_once 'partials/africana_thesis_list.php';
		elseif ($post_type_selection == "engineering_db_list") :
			include_once 'partials/engineering_db_list.php';
		elseif ($post_type_selection == "engineering_db_single") :
			include_once 'partials/engineering_db_single.php';
		elseif ($post_type_selection == "engineering_db_sidebar") :
			include_once 'partials/engineering_db_sidebar.php';
		elseif ($post_type_selection == "ilr_botm") :
			include_once 'partials/ilr_botm.php';
		elseif ($post_type_selection == "ilr_botm_single") :
			include_once 'partials/ilr_botm_single.php';
		elseif ($post_type_selection == "ilr_wi_tlist") :
			include_once 'partials/ilr_wit_list.php';
		elseif ($post_type_selection == "ilr_wit_single") :
			include_once 'partials/ilr_wit_single.php';
		elseif ($post_type_selection == "law_bitnerfellows") :
			include_once 'partials/law_bitnerfellows.php';
		elseif ($post_type_selection == "law_bitnerfellows_single") :
			include_once 'partials/law_bitnerfellows_single.php';
		elseif ($post_type_selection == "law_diversityellows") :
			include_once 'partials/law_diversityfellows.php';
		elseif ($post_type_selection == "law_diversityellows_single") :
			include_once 'partials/law_diversityfellows_single.php';
		elseif ($post_type_selection == "management_alltopics") :
			include_once 'partials/management_alltopics.php';
		elseif ($post_type_selection == "management_db_single") :
			include_once 'partials/management_db_single.php';
		elseif ($post_type_selection == "management_db_single_sidebar") :
			include_once 'partials/management_db_single_sidebar.php';
		elseif ($post_type_selection == "management_db_list") :
			include_once 'partials/management_db_list.php';
		elseif ($post_type_selection == "management_faqs_single") :
			include_once 'partials/management_faqs_single.php';
		elseif ($post_type_selection == "management_faqs_list") :
			include_once 'partials/management_faqs_list.php';
		elseif ($post_type_selection == "management_search") :
			include_once 'partials/management_search.php';
		elseif ($post_type_selection == "math_collectedworks") :
			include_once 'partials/math_collectedworks.php';
		elseif ($post_type_selection == "math_collectedworks_single") :
			include_once 'partials/math_collectedworks_single.php';
		elseif ($post_type_selection == "math_recommendedbooks") :
			include_once 'partials/math_recommendedbooks.php';
		elseif ($post_type_selection == "math_recommendedbooks_single") :
			include_once 'partials/math_recommendedbooks_single.php';
		elseif ($post_type_selection == "psl_corebooks") :
			include_once 'partials/psl_corebooks.php';
		elseif ($post_type_selection == "psl_corebooks_single") :
			include_once 'partials/psl_corebooks_single.php';
		elseif ($post_type_selection == "psl_databases") :
			include_once 'partials/psl_databases.php';
		elseif ($post_type_selection == "psl_databases_single") :
			include_once 'partials/psl_databases_single.php';
		elseif ($post_type_selection == "psl_journals") :
			include_once 'partials/psl_journals.php';
		elseif ($post_type_selection == "psl_journals_single") :
			include_once 'partials/psl_journals_single.php';
		elseif ($post_type_selection == "rare_onlineexhibitions") :
			include_once 'partials/rare_onlineexhibitions.php';
		elseif ($post_type_selection == "rare_onlineexhibitions_single") :
			include_once 'partials/rare_onlineexhibitions_single.php';
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
	protected function _content_template()
	{
?>
		<div class="title">
			{{{ settings.loop_select }}}
		</div>
<?php
	}
}
