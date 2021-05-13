<?php

/**
 * Function partial which creates custom fields created by ACF PRO.
 *
 * @package culu
 *
 *
 */

if (function_exists('acf_add_local_field_group')) :

    // ACF for Staff profiles.
    acf_add_local_field_group(array(
        'key' => 'group_5cbdf95e3f992',
        'title' => 'Staff Profile',
        'fields' => array(
            array(
                'key' => 'field_5f1756ef42ce3',
                'label' => 'Unit Name',
                'name' => 'unit_name',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfbs_allow_search' => 1,
                'choices' => array(),
                'default_value' => '',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 1,
                'ajax' => 0,
                'return_format' => 'value',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5f1780bd06835',
                'label' => 'Department',
                'name' => 'departments',
                'type' => 'taxonomy',
                'instructions' => 'You can select more than one department',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'department',
                'field_type' => 'multi_select',
                'allow_null' => 1,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'object',
                'multiple' => 0,
            ),
            array(
                'key' => 'field_5f1891513d00a',
                'label' => 'Discipline Support Team',
                'name' => 'discipline_support_team',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'teams',
                'field_type' => 'multi_select',
                'allow_null' => 1,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'object',
                'multiple' => 0,
            ),
            array(
                'key' => 'field_5cbdfc7db1044',
                'label' => 'Photo',
                'name' => 'photo',
                'type' => 'image_aspect_ratio_crop',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'crop_type' => 'pixel_size',
                'aspect_ratio_width' => 282,
                'aspect_ratio_height' => 282,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'min_width' => 0,
                'min_height' => 0,
            ),
            array(
                'key' => 'field_5cbdf967b103f',
                'label' => 'First Name',
                'name' => 'first_name',
                'type' => 'text',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfbs_allow_search' => 1,
                'default_value' => '',
                'placeholder' => 'First Name',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5cbdfc43b1040',
                'label' => 'Last Name',
                'name' => 'last_name',
                'type' => 'text',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfbs_allow_search' => 1,
                'default_value' => '',
                'placeholder' => 'Last name',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_6053c451b4dde',
                'label' => 'Librarian',
                'name' => 'librarian',
                'type' => 'button_group',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    true => 'Yes',
                    false => 'No',
                ),
                'allow_null' => 0,
                'default_value' => 0,
                'layout' => 'horizontal',
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_5cf003a7086c2',
                'label' => 'Degree',
                'name' => 'degree',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5cbdfc55b1041',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfbs_allow_search' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5cbdfc60b1042',
                'label' => 'Email',
                'name' => 'email',
                'type' => 'email',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfbs_allow_search' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_5cbdfc69b1043',
                'label' => 'Phone',
                'name' => 'phone',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5cbdfd5bcddd4',
                'label' => 'Consultation',
                'name' => 'consultation',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(),
                'default_value' => array(),
                'allow_null' => 1,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5d3b181a0ffe7',
                'label' => 'Office Location',
                'name' => 'office_location',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5f189a34c92cd',
                'label' => 'Areas of Expertise',
                'name' => 'areas_of_expertise',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'expertise',
                'field_type' => 'multi_select',
                'allow_null' => 1,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'object',
                'multiple' => 0,
            ),
            array(
                'key' => 'field_5d9cffe9e3a52',
                'label' => 'Faculty Bio',
                'name' => 'faculty_bio',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5d3b184f0ffe9',
                'label' => 'ORCID ID',
                'name' => 'orcid_id',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5d3b185a0ffea',
                'label' => 'Linkedin Profile',
                'name' => 'linkedin_profile',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5d3b18d90ffeb',
                'label' => 'Liaison Areas',
                'name' => 'liaison_areas',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfbs_allow_search' => 0,
                'taxonomy' => 'liaisons',
                'field_type' => 'multi_select',
                'allow_null' => 1,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'object',
                'multiple' => 0,
            ),
            array(
                'key' => 'field_5f80ca0cdac77',
                'label' => 'Language of Expertise',
                'name' => 'language_of_expertise',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfbs_allow_search' => 0,
                'taxonomy' => 'language_expertise',
                'field_type' => 'multi_select',
                'allow_null' => 1,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'object',
                'multiple' => 0,
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'staff',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(
            0 => 'the_content',
            1 => 'excerpt',
            2 => 'discussion',
            3 => 'comments',
            4 => 'format',
            5 => 'page_attributes',
            6 => 'featured_image',
            7 => 'send-trackbacks',
        ),
        'active' => true,
        'description' => '',
    ));

endif;

// Add a select consultation ID list of staff dynamically to consultation field in Staff ACF group.
function my_acf_load_field_consultation($field)
{

    $url_appointments = 'https://api2.libcal.com/1.1/appointments/users';
    $options_appointments = array(
        'http' => array(
            'method'  => 'GET',
            'header' => 'Authorization: Bearer ' . get_libcal_token()
        )
    );

    $context_appointments  = stream_context_create($options_appointments);
    $response = file_get_contents($url_appointments, false, $context_appointments);
    $get_response = json_decode($response);
    $users = array();
    $single_user = array();

    // Get first, last name, and appointment ID
    foreach ($get_response as $user) {
        $user_name = $user->first_name . ' ' . $user->last_name;
        $single_user[$user->id] = $user_name;
    }

    $field['choices'] = $single_user;
    // return field choices to ACF select list with all consultation staff members.
    return $field;
}
add_filter('acf/load_field/name=consultation', 'my_acf_load_field_consultation');

// Add a select list of library units dynamically to unit_name field in Staff ACF group.
function my_acf_load_field_unit_name($field)
{

    $all_units = array();
    $culConfig = json_decode(getenv('CUL_CONFIG'));
    $units = $culConfig->units;
    foreach ($units as $key => $value) {
        $all_units[$key] = ucfirst($key);
    }
    $field['choices'] = $all_units;

    // return field choices to ACF select list with all unit names.
    return $field;
}
add_filter('acf/load_field/name=unit_name', 'my_acf_load_field_unit_name');
