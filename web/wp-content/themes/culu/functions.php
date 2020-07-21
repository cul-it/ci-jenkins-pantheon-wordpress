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
		set_post_thumbnail_size( 600 );

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

	wp_enqueue_style( 'merriweather_google_fonts', 'https://fonts.googleapis.com/css?family=Merriweather:400,400i,900,900i' );
	wp_enqueue_style( 'raleway_google_fonts', 'https://fonts.googleapis.com/css?family=Raleway:400,400i,700,700i&display=swap' );

	//wp_enqueue_style( 'culu-style', get_stylesheet_uri() );

	wp_enqueue_script( 'culu-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script( 'culu-search', get_template_directory_uri() . '/js/search.js', array(), '20151215', true );
	wp_enqueue_script( 'culu-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	/**
 	* Swiper JS: https://github.com/nolimits4web/swiper
 	*
 	*/

	wp_enqueue_style( 'swiper-css', get_template_directory_uri() . '/js/swiper/swiper.css');

	wp_enqueue_style( 'swiper-css-init', get_template_directory_uri() . '/js/swiper/init.css');

	wp_enqueue_script('swiper-js', get_template_directory_uri() . '/js/swiper/swiper.min.js', null, null, true);

	wp_enqueue_script('swiper-js-init', get_template_directory_uri() . '/js/swiper/init.js', null, null, true);

	wp_enqueue_script('isotope-init', get_template_directory_uri() . '/js/isotope.js', null, null, true);
	
	wp_enqueue_style( 'theme-style', get_stylesheet_directory_uri() . '/style.css' );

	wp_enqueue_script( 'culu-notifications', get_template_directory_uri() . '/js/notifications.js', array(), '20200317', true );

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

/**
 * Function partial which creates custom fields created by ACF PRO.
 */
require get_template_directory() . '/inc/template-acf.php';

/**
 * Function partial which creates custom post types created with CPT UI
 */
require get_template_directory() . '/inc/template-custom-posts.php';

/**
 * Function partial which enhance the theme by theme customization via Kirki
 */
require get_template_directory() . '/inc/template-kirki.php';

/**
 * Function partial which enhance the theme by custom pagination
 */
require get_template_directory() . '/inc/template-pagination.php';

/**
 * Shortcodes
 */
require get_template_directory() . '/inc/shortcodes/highlights.php';
require get_template_directory() . '/inc/shortcodes/vue.php';


/**
 * Add social media widget
 */

function culu_register_widgets() {

	register_sidebar( array(
		'name' => __( 'Social Media', 'culu' ),
		'id' => 'widget-social-media',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '',
		'after_title' => ''
	));
	}

	add_action( 'widgets_init', 'culu_register_widgets' );

	add_filter( 'simple_social_disable_custom_css', '__return_true' );

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

/* Add print css to theme */
function culu_print_styles(){
wp_enqueue_style(
	'culu-print-style',
	get_stylesheet_directory_uri() . '/sass/print/print.css',
	array(),
	false,
	'print' // print styles only
);
}
add_action( 'wp_footer', 'culu_print_styles' );


/*
 * Remove type from script and style markup
 * Source: https://www.damiencarbery.com/2018/11/remove-type-from-script-and-style-markup/
 */

add_filter( 'script_loader_tag', 'culu_remove_type', 10, 3 );
add_filter( 'style_loader_tag', 'culu_remove_type', 10, 3 );  // Ignore the $media argument to allow for a common function.
function culu_remove_type( $markup, $handle, $href ) {
	//error_log( 'Markup: ' . $markup );
	//error_log( 'Handle: ' . $handle );
	//error_log( 'Href: ' . $href );
	// Remove the 'type' attribute.
	$markup = str_replace( " type='text/javascript'", '', $markup );
	$markup = str_replace( " type='text/css'", '', $markup );
	return $markup;
}
// Store and process wp_head output to operate on inline scripts and styles.
add_action( 'wp_head', 'culu_wp_head_ob_start', 0 );
function culu_wp_head_ob_start() {
	ob_start();
}
add_action( 'wp_head', 'culu_wp_head_ob_end', 10000 );
function culu_wp_head_ob_end() {
	$wp_head_markup = ob_get_contents();
	ob_end_clean();
	
	// Remove the 'type' attribute. Note the use of single and double quotes.
	$wp_head_markup = str_replace( " type='text/javascript'", '', $wp_head_markup );
	$wp_head_markup = str_replace( ' type="text/javascript"', '', $wp_head_markup );
	$wp_head_markup = str_replace( ' type="text/css"', '', $wp_head_markup );
	$wp_head_markup = str_replace( " type='text/css'", '', $wp_head_markup );
	echo $wp_head_markup;
}
