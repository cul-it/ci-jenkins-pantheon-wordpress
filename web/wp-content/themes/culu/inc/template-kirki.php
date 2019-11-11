<?php
/**
 * Function partial which enhance the theme by theme customization via Kirki
 *
 * @package culu
 *
 * Setup
 */

 Kirki::add_config( 'theme_config_id', array(
 	'capability'    => 'edit_theme_options',
 	'option_type'   => 'theme_mod',
 ) );

 /*
  *  Customize hero image
  *  Add panel
  */

 Kirki::add_panel( 'panel_id', array(
     'priority'    => 10,
     'title'       => esc_attr__( 'Update hero content image background', 'textdomain' ),
     'description' => esc_attr__( 'Upload hero images for mobile, tablet, and desktop', 'textdomain' ),
 ) );

 // Add section
 Kirki::add_section( 'section_id_hero_large', array(
     'title'          => esc_attr__( 'Hero image desktop size', 'textdomain' ),
     'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id',
     'priority'       => 160,
 ) );

 // Add image control
 // Default behaviour (saves data as a URL).
 Kirki::add_field( 'theme_config_id_hero_large', array(
  'type'        => 'image',
  'settings'    => 'image_setting_url_hero_large',
  'label'       => esc_attr__( 'Image Control (URL)', 'textdomain' ),
  'description' => esc_attr__( 'Upload image.', 'textdomain' ),
  'section'     => 'section_id_hero_large',
  'default'     => '',
 ) );


 // Add section
 Kirki::add_section( 'section_id_hero_medium', array(
     'title'          => esc_attr__( 'Hero image tablet size', 'textdomain' ),
     'description'    => esc_attr__( 'Upload hero image for tablet devices.', 'textdomain' ),
     'panel'          => 'panel_id',
     'priority'       => 160,
 ) );

 // Add image control
 // Default behaviour (saves data as a URL).
 Kirki::add_field( 'theme_config_id_hero_medium', array(
  'type'        => 'image',
  'settings'    => 'image_setting_url_hero_medium',
  'label'       => esc_attr__( 'Image Control (URL)', 'textdomain' ),
  'description' => esc_attr__( 'Upload image.', 'textdomain' ),
  'section'     => 'section_id_hero_medium',
  'default'     => '',
 ) );

 // Add section
 Kirki::add_section( 'section_id_hero_small', array(
     'title'          => esc_attr__( 'Hero image phone size', 'textdomain' ),
     'description'    => esc_attr__( 'Upload hero image for phone devices.', 'textdomain' ),
     'panel'          => 'panel_id',
     'priority'       => 160,
 ) );

 // Add image control
 // Default behaviour (saves data as a URL).
 Kirki::add_field( 'theme_config_id_hero_small', array(
  'type'        => 'image',
  'settings'    => 'image_setting_url_hero_small',
  'label'       => esc_attr__( 'Image Control (URL)', 'textdomain' ),
  'description' => esc_attr__( 'Upload image.', 'textdomain' ),
  'section'     => 'section_id_hero_small',
  'default'     => '',
 ) );


 /*
  *  Customize hero top graphic color.
  *  Add panel
  */

 Kirki::add_panel( 'panel_id_top_graphic', array(
     'priority'    => 9,
 		'title'       => esc_attr__( 'Update hero top graphic', 'textdomain' ),
     'description' => esc_attr__( 'Change color of top graphic', 'textdomain' ),
 ) );

 // Add section
 Kirki::add_section( 'section_id_hero_top_bg', array(
     'title'          => esc_attr__( 'Hero top container', 'textdomain' ),
     'description'    => esc_attr__( 'Add color to top area background on hero header', 'textdomain' ),
     'panel'          => 'panel_id_top_graphic',
     'priority'       => 160,
 ) );

 // Add color control
 Kirki::add_field( 'theme_config_id_hero_top_bg', [
 	'type'        => 'color',
 	'settings'    => 'hero_top_color',
 	'label'       => __( 'Color Control (with alpha channel)', 'kirki' ),
 	'description' => esc_html__( 'This is a color control - with alpha channel.', 'kirki' ),
 	'section'     => 'section_id_hero_top_bg',
 	'default'     => '#AD1A1A',
 	'choices'     => [
 		'alpha' => false,
 	],
 ] );

 /*
  *  Customize hero bottom and bottom graphic colors.
  *  Add panel
  */

 Kirki::add_panel( 'panel_id_bottom_graphic', array(
     'priority'    => 10,
     'title'       => esc_attr__( 'Update hero bottom graphic', 'textdomain' ),
     'description' => esc_attr__( 'Change color of bottom graphic', 'textdomain' ),
 ) );

 // Add section
 Kirki::add_section( 'section_id_hero_bottom_bg', array(
     'title'          => esc_attr__( 'Hero bottom container', 'textdomain' ),
     'description'    => esc_attr__( 'Add color to bottom area background on hero header', 'textdomain' ),
     'panel'          => 'panel_id_bottom_graphic',
     'priority'       => 160,
 ) );

 // Add color control
 Kirki::add_field( 'theme_config_id_hero_bottom_bg', [
 	'type'        => 'color',
 	'settings'    => 'hero_bottom_color',
 	'label'       => __( 'Color Control (with alpha channel)', 'kirki' ),
 	'description' => esc_html__( 'This is a color control - with alpha channel.', 'kirki' ),
 	'section'     => 'section_id_hero_bottom_bg',
 	'default'     => '#0A394A',
 	'choices'     => [
 		'alpha' => true,
 	],
 ] );

 /*
  *  Customize College Unit
  *  Add panel
  */

 Kirki::add_panel( 'panel_id_college', array(
     'priority'    => 10,
     'title'       => esc_attr__( 'Add College Unit', 'textdomain' ),
     //description' => esc_attr__( 'Upload hero images for mobile, tablet, and desktop', 'textdomain' ),
 ) );

 // Add section college label
 Kirki::add_section( 'section_id_college_label', array(
     'title'          => esc_attr__( 'Add College name', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_college',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_college_label', array(
 	'type'     => 'text',
 	'settings' => 'college_label',
 	'label'    => __( 'Add label', 'textdomain' ),
 	'section'  => 'section_id_college_label',
 	'default'  => esc_attr__( '', 'textdomain' ),
 	'priority' => 10,
 ) );

 // Add section college link
 Kirki::add_section( 'section_id_college_link', array(
     'title'          => esc_attr__( 'Add College url', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_college',
     'priority'       => 160,
 ) );

 // Add link control
 Kirki::add_field( 'theme_config_id_college_link', array(
 	'type'     => 'link',
 	'settings' => 'college_link',
 	'label'    => __( 'Add link', 'textdomain' ),
 	'section'  => 'section_id_college_link',
 	'default'  => esc_attr__( 'http://', 'textdomain' ),
 	'priority' => 10,
 ) );

 // Add section college logo
 Kirki::add_section( 'section_id_college_logo', array(
     'title'          => esc_attr__( 'Add College logo', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for phone devices.', 'textdomain' ),
     'panel'          => 'panel_id_college',
     'priority'       => 160,
 ) );


 /**
  * Default behaviour (saves data as a URL).
  */
 Kirki::add_field( 'theme_config_id_college_logo', array(
 	'type'        => 'image',
 	'settings'    => 'college_logo',
 	'label'       => esc_attr__( 'Image Control (URL)', 'textdomain' ),
 	'description' => esc_attr__( 'Description Here.', 'textdomain' ),
 	'section'     => 'section_id_college_logo',
 	'default'     => '',
 ) );

 /*
  *  Customize footer info
  *  Add panel
  */

 Kirki::add_panel( 'panel_id_contact_1', array(
     'priority'    => 10,
     'title'       => esc_attr__( 'Add Contact Info 1', 'textdomain' ),
     //description' => esc_attr__( 'Upload hero images for mobile, tablet, and desktop', 'textdomain' ),
 ) );

 // Add section Address Label
 Kirki::add_section( 'section_id_address_label', array(
     'title'          => esc_attr__( 'Address', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_1',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_address_label', array(
 	'type'     => 'text',
 	'settings' => 'address_label',
 	'label'    => __( 'Add address', 'textdomain' ),
 	'section'  => 'section_id_address_label',
 	'default'  => esc_attr__( '', 'textdomain' ),
 	'priority' => 10,
 ) );

 // Add section City Label
 Kirki::add_section( 'section_id_city_label', array(
     'title'          => esc_attr__( 'City', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_1',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_city_label', array(
 	'type'     => 'text',
 	'settings' => 'city_label',
 	'label'    => __( 'Add city', 'textdomain' ),
 	'section'  => 'section_id_city_label',
 	'default'  => esc_attr__( '', 'textdomain' ),
 	'priority' => 10,
 ) );

 // Add section State Label
 Kirki::add_section( 'section_id_state_label', array(
     'title'          => esc_attr__( 'State', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_1',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_state_label', array(
 	'type'     => 'text',
 	'settings' => 'state_label',
 	'label'    => __( 'Add state', 'textdomain' ),
 	'section'  => 'section_id_state_label',
 	'default'  => esc_attr__( '', 'textdomain' ),
 	'priority' => 10,
 ) );

 // Add section Zip Label
 Kirki::add_section( 'section_id_zip_label', array(
     'title'          => esc_attr__( 'Zip', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_1',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_zip_label', array(
 	'type'     => 'text',
 	'settings' => 'zip_label',
 	'label'    => __( 'Add zip', 'textdomain' ),
 	'section'  => 'section_id_zip_label',
 	'default'  => esc_attr__( '', 'textdomain' ),
 	'priority' => 10,
 ) );

 // Add section Email Label
 Kirki::add_section( 'section_id_email_label', array(
     'title'          => esc_attr__( 'Email on header', 'textdomain' ),
     'description'    => esc_attr__( 'This is the email located on hero content after Full Hours link.', 'textdomain' ),
     'panel'          => 'panel_id_contact_1',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_email_label', array(
 	'type'     => 'text',
 	'settings' => 'email_label',
 	'label'    => __( 'Add email', 'textdomain' ),
 	'section'  => 'section_id_email_label',
 	'default'  => esc_attr__( '', 'textdomain' ),
 	'priority' => 10,
 ) );

 // Add section Google Map Link Label
 Kirki::add_section( 'section_id_google_map_label', array(
     'title'          => esc_attr__( 'Google Map Link', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_1',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_google_map_label', array(
 	'type'     => 'text',
 	'settings' => 'google_map_label',
 	'label'    => __( 'Add google map link', 'textdomain' ),
 	'section'  => 'section_id_google_map_label',
 	'default'  => esc_attr__( '', 'textdomain' ),
 	'priority' => 10,
 ) );

// Add section for adding email and phone numbers
Kirki::add_section( 'section_contact_email_phones', array(
       'priority'    => 160,
       'title'          => __( 'Contact (emails/phones)' ),
       'panel'          => 'panel_id_contact_1',
       'capability'     => 'edit_theme_options',
    ) );

// Add repeater control
Kirki::add_field( 'section_contact_email_phones_control', array(
    'type'        => 'repeater',
    'label'       => esc_attr__( 'Add emails and phone numbers ', 'pestex' ),
    'section'     => 'section_contact_email_phones',
    'priority'    => 10,
    'row_label' => array(
        'type'  => 'field',
        'value' => esc_attr__('Contact', 'pestex' ),
        'field' => 'link_text',
    ),
    'settings'    => 'section_contact_email_phones_setting',

    'fields' => array(
        'contact_title' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Contact type', 'pestex' ),
            'description' => esc_attr__( 'This will be the title. Ex: Circulation, Front Desk, Email, etc.', 'pestex' ),
            'default'     => '',
        ),
        'contact_value' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Add email or phone number', 'pestex' ),
            'description' => esc_attr__( '', 'pestex' ),
            'default'     => '',
        ),
    )
) );

// Add second contact info panel for units with 2 addresses
Kirki::add_panel( 'panel_id_contact_2', array(
     'priority'    => 10,
     'title'       => esc_attr__( 'Add Contact Info 2', 'textdomain' ),
     //description' => esc_attr__( 'Upload hero images for mobile, tablet, and desktop', 'textdomain' ),
 ) );

// Add section Contact Name Label
 Kirki::add_section( 'section_id_contact_name_label_2', array(
     'title'          => esc_attr__( 'Contact Name', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_2',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_contact_name_label_2', array(
  'type'     => 'text',
  'settings' => 'contact_name_label_2',
  'label'    => __( 'Contact name', 'textdomain' ),
  'section'  => 'section_id_contact_name_label_2',
  'default'  => esc_attr__( '', 'textdomain' ),
  'priority' => 10,
 ) );

 // Add section Address Label
 Kirki::add_section( 'section_id_address_label_2', array(
     'title'          => esc_attr__( 'Address', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_2',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_address_label_2', array(
  'type'     => 'text',
  'settings' => 'address_label_2',
  'label'    => __( 'Add address', 'textdomain' ),
  'section'  => 'section_id_address_label_2',
  'default'  => esc_attr__( '', 'textdomain' ),
  'priority' => 10,
 ) );

 // Add section City Label
 Kirki::add_section( 'section_id_city_label_2', array(
     'title'          => esc_attr__( 'City', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_2',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_city_label_2', array(
  'type'     => 'text',
  'settings' => 'city_label_2',
  'label'    => __( 'Add city', 'textdomain' ),
  'section'  => 'section_id_city_label_2',
  'default'  => esc_attr__( '', 'textdomain' ),
  'priority' => 10,
 ) );

 // Add section State Label
 Kirki::add_section( 'section_id_state_label_2', array(
     'title'          => esc_attr__( 'State', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_2',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_state_label_2', array(
  'type'     => 'text',
  'settings' => 'state_label_2',
  'label'    => __( 'Add state', 'textdomain' ),
  'section'  => 'section_id_state_label_2',
  'default'  => esc_attr__( '', 'textdomain' ),
  'priority' => 10,
 ) );

 // Add section Zip Label
 Kirki::add_section( 'section_id_zip_label_2', array(
     'title'          => esc_attr__( 'Zip', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_2',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_zip_label_2', array(
  'type'     => 'text',
  'settings' => 'zip_label_2',
  'label'    => __( 'Add zip', 'textdomain' ),
  'section'  => 'section_id_zip_label_2',
  'default'  => esc_attr__( '', 'textdomain' ),
  'priority' => 10,
 ) );

 // Add section Google Map Link Label
 Kirki::add_section( 'section_id_google_map_label_2', array(
     'title'          => esc_attr__( 'Google Map Link', 'textdomain' ),
     //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
     'panel'          => 'panel_id_contact_2',
     'priority'       => 160,
 ) );

 // Add input text control
 Kirki::add_field( 'theme_config_id_google_map_label_2', array(
  'type'     => 'text',
  'settings' => 'google_map_label_2',
  'label'    => __( 'Add google map link', 'textdomain' ),
  'section'  => 'section_id_google_map_label_2',
  'default'  => esc_attr__( '', 'textdomain' ),
  'priority' => 10,
 ) );

// Add section for adding email and phone numbers
Kirki::add_section( 'section_contact_email_phones_2', array(
       'priority'    => 160,
       'title'          => __( 'Contact (emails/phones)' ),
       'panel'          => 'panel_id_contact_2',
       'capability'     => 'edit_theme_options',
    ) );

// Add repeater control
Kirki::add_field( 'section_contact_email_phones_control_2', array(
    'type'        => 'repeater',
    'label'       => esc_attr__( 'Add emails and phone numbers ', 'pestex' ),
    'section'     => 'section_contact_email_phones_2',
    'priority'    => 10,
    'row_label' => array(
        'type'  => 'field',
        'value' => esc_attr__('Contact', 'pestex' ),
        'field' => 'link_text',
    ),
    'settings'    => 'section_contact_email_phones_setting_2',

    'fields' => array(
        'contact_title' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Contact type', 'pestex' ),
            'description' => esc_attr__( 'This will be the title. Ex: Circulation, Front Desk, Email, etc.', 'pestex' ),
            'default'     => '',
        ),
        'contact_value' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Add email or phone number', 'pestex' ),
            'description' => esc_attr__( '', 'pestex' ),
            'default'     => '',
        ),
    )
) );
