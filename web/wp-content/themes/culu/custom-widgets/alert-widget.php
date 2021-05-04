<?php

namespace Elementor;

class Elementor_Alert_Widget extends Widget_Base
{

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'alert-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return 'Notification Banners';
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'fa fa-exclamation-triangle';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return ['basic'];
		//return [ 'theme-elements' ];
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls()
	{

		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'plugin-name'),
			]
		);


		$this->add_control(
			'select_notification_alert_type',
			[
				'label' => __('Select Notification alert type', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::SELECT,
				//'default' => 'default',
				'options' => [
					'Success' => __('Success', 'plugin-name'),
					'Info' => __('Info', 'plugin-name'),
					'Warning' => __('Warning', 'plugin-name'),
					'Alert' => __('Alert', 'plugin-name'),
				],
			]
		);

		$this->add_control(
			'notification_text',
			[
				'label' => __('Add notification text', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				//'default' => __( 'Default description', 'elementor' ),
				//'placeholder' => __( 'Type your description here', 'elementor' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{

		$settings = $this->get_settings_for_display();

		$alert_type = $settings['select_notification_alert_type'];

		switch ($alert_type) {

			case "Success":
				echo
					"<div role='alert' class='alert__widget alert__success'>
					<i class='far fa-check-circle' aria-hidden='true'><span class='sr-only'>Success alert</span></i>" . $settings['notification_text'] .
						"</div>";
				break;

			case "Warning":
				echo
					"<div role='alert' class='alert__widget alert__warning'>
					<i class='fas fa-exclamation-triangle' aria-hidden='true'><span class='sr-only'>Warning alert</span></i>" . $settings['notification_text'] .
						"</div>";
				break;

			case "Info":
				echo
					"<div role='alert' class='alert__widget alert__info'>
					<i class='fas fa-info-circle' aria-hidden='true'><span class='sr-only'>Info alert</span></i>" . $settings['notification_text'] .
						"</div>";
				break;

			case "Alert":
				echo
					"<div role='alert' class='alert__widget alert__error'>
					<i class='fas fa-exclamation-circle' aria-hidden='true'><span class='sr-only'>Error alert</span></i>" . $settings['notification_text'] .
						"</div>";
				break;
		}
	}
}
