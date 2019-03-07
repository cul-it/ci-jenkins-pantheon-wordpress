<?php
/**
 * culu functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package culu
 */

if ( ! function_exists( 'culu_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function culu_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on culu, use a find and replace
		 * to change 'culu' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'culu', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );


		// Add Menu Support
		add_theme_support('menus');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location for main nav.
		register_nav_menus( array(
			'primary' => esc_html__( 'Main Menu', 'culu' )
		) );

		// This theme uses wp_nav_menu() in one location for footer nav.
		register_nav_menus( array(
			'footer' => esc_html__( 'Footer Menu', 'culu' )
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'culu_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'culu_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function culu_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'culu_content_width', 640 );
}
add_action( 'after_setup_theme', 'culu_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function culu_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'culu' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'culu' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'culu_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function culu_scripts() {

	//wp_enqueue_style( 'culu_fontawesome', 'https://use.fontawesome.com/releases/v5.7.0/css/all.css' );

	wp_enqueue_style( 'culu_google_fonts', 'https://fonts.googleapis.com/css?family=Merriweather:400,400i,900,900i' );
	wp_enqueue_style( 'culu_google_fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i' );

	wp_enqueue_style( 'culu-style', get_stylesheet_uri() );

	wp_enqueue_script( 'culu-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script( 'culu-search', get_template_directory_uri() . '/js/search.js', array(), '20151215', true );

	wp_enqueue_script( 'culu-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'culu_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


// Theme customization via Kirki
// Setup

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
    'title'       => esc_attr__( 'Upload hero images', 'textdomain' ),
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

Kirki::add_panel( 'panel_id_contact', array(
    'priority'    => 10,
    'title'       => esc_attr__( 'Add Contact Info', 'textdomain' ),
    //description' => esc_attr__( 'Upload hero images for mobile, tablet, and desktop', 'textdomain' ),
) );

// Add section Address Label
Kirki::add_section( 'section_id_address_label', array(
    'title'          => esc_attr__( 'Address', 'textdomain' ),
    //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
    'panel'          => 'panel_id_contact',
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
    'panel'          => 'panel_id_contact',
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
    'panel'          => 'panel_id_contact',
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
    'panel'          => 'panel_id_contact',
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


// Add section Reference Number Label
Kirki::add_section( 'section_id_reference_number_label', array(
    'title'          => esc_attr__( 'Reference Number', 'textdomain' ),
    //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
    'panel'          => 'panel_id_contact',
    'priority'       => 160,
) );

// Add input text control
Kirki::add_field( 'theme_config_id_reference_number_label', array(
	'type'     => 'text',
	'settings' => 'reference_number_label',
	'label'    => __( 'Add reference number', 'textdomain' ),
	'section'  => 'section_id_reference_number_label',
	'default'  => esc_attr__( '', 'textdomain' ),
	'priority' => 10,
) );

// Add section Circulation Number Label
Kirki::add_section( 'section_id_circulation_number_label', array(
    'title'          => esc_attr__( 'Circulation Number', 'textdomain' ),
    //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
    'panel'          => 'panel_id_contact',
    'priority'       => 160,
) );

// Add input text control
Kirki::add_field( 'theme_config_id_circulation_number_label', array(
	'type'     => 'text',
	'settings' => 'circulation_number_label',
	'label'    => __( 'Add circulation number', 'textdomain' ),
	'section'  => 'section_id_circulation_number_label',
	'default'  => esc_attr__( '', 'textdomain' ),
	'priority' => 10,
) );


// Add section Email Label
Kirki::add_section( 'section_id_email_label', array(
    'title'          => esc_attr__( 'Email', 'textdomain' ),
    //'description'    => esc_attr__( 'Upload hero image for desktop devices.', 'textdomain' ),
    'panel'          => 'panel_id_contact',
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
    'panel'          => 'panel_id_contact',
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










/**
 * Strip out domain path.
 */

  function get_domain_path($url) {
    $domain = parse_url($url);
    // parse_url return and array containing [scheme], [host], [path], and [query].
    return $domain["path"];
  }


	//if( !defined(THEME_IMG_PATH)){
     define( 'THEME_IMG_PATH', get_stylesheet_directory_uri() . '/images' );
    //}
