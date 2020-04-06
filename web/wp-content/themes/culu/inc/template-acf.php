<?php
/**
  * Function partial which creates custom fields created by ACF PRO.
  *
  * @package culu
  *
  *
  */

if( function_exists('acf_add_local_field_group') ):

// ACF for Sataf profiles.
acf_add_local_field_group(array(
	'key' => 'group_5cbdf95e3f992',
	'title' => 'Staff Profile',
	'fields' => array(
		array(
			'key' => 'field_5cbdfc7db1044',
			'label' => 'Photo',
			'name' => 'photo',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
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
			'key' => 'field_5d40e008124ed',
			'label' => 'Staff Type',
			'name' => 'staff_type',
			'type' => 'text',
			'instructions' => 'Please add staff type: Administration, Staff, etc',
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
			'required' => 1,
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
			'choices' => array(
			),
			'default_value' => array(
			),
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
			'key' => 'field_5d3b182c0ffe8',
			'label' => 'Areas of Expertise',
			'name' => 'areas_of_expertise',
			'type' => 'textarea',
			'instructions' => 'Add expertise separated by a comma.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => '',
			'new_lines' => '',
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
			'type' => 'textarea',
			'instructions' => 'Add liaison areas separated by a comma.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => '',
			'new_lines' => '',
		),
		array(
			'key' => 'field_5d40e02f124ee',
			'label' => '',
			'name' => '',
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

// ACF for Highlights
acf_add_local_field_group(array(
	'key' => 'group_5d7fd5bc34ed1',
	'title' => 'Highlights',
	'fields' => array(
		array(
			'key' => 'field_5d7fd5cf33e4a',
			'label' => 'Highlights Photo',
			'name' => 'highlights_photo',
			'type' => 'image',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'preview_size' => 'medium',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array(
			'key' => 'field_5d7fd5e733e4b',
			'label' => 'Highlights Link',
			'name' => 'highlights_link',
			'type' => 'url',
			'instructions' => '',
			'required' => 1,
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
			'key' => 'field_5d7fd5f433e4c',
			'label' => 'Highlights Description',
			'name' => 'highlights_description',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => '',
			'new_lines' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'highlights',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

endif;

// Add consultation ID for staff member to select if they want to offer consultations.

function my_acf_load_field( $field ) {
	
	// Request token
	$url = 'https://spaces.library.cornell.edu/1.1/oauth/token';
	$data = array(
		'client_id' => '399', 
		'client_secret' => '3f0198b5211c299fbf4fb339a858163a',
		'grant_type' => 'client_credentials'
		);

	$options = array(
		'http' => array( // use key 'http' even if you send the request to https://...
		'header'  => "Content-type: application/x-www-form-urlencoded",
		'method'  => 'POST',
		'content' => http_build_query($data)
		),
		'ssl'=>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		)
	
	);
	
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { /* Handle error*/  }
	
	// Use token to get list of users with consultations 
	$get_token = json_decode($result);
	$token = $get_token->access_token;
	
	$url_appointments = 'https://api2.libcal.com/1.1/appointments/users';
	$options_appointments = array(
		'http' => array(
			'method'  => 'GET',
			'header' => 'Authorization: Bearer '.$token
		)
	);
	
	$context_appointments  = stream_context_create($options_appointments);
	$response = file_get_contents($url_appointments, false, $context_appointments );
	$get_response = json_decode($response);
	
	$users = array();
	$single_user = array();

	// Get first, last name, and ppointment ID
	foreach($get_response as $user){
		$user_name = $user->first_name . ' ' . $user->last_name;
		$single_user[$user->id] = $user_name;
	}
		
	$field['choices'] = $single_user;
	// return field chices to ACF select list for add ing a consultation
	return $field;
		
}
	
add_filter('acf/load_field/key=field_5cbdfd5bcddd4', 'my_acf_load_field');
	
	