<?php
		// Load WooCommerce default styles if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	wp_enqueue_style(
		'seedprod-woocommerce-layout',
		str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce-layout.css',
		'',
		defined( 'WC_VERSION' ) ? WC_VERSION : null,
		'all'
	);
	wp_enqueue_style(
		'seedprod-woocommerce-smallscreen',
		str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce-smallscreen.css',
		'',
		defined( 'WC_VERSION' ) ? WC_VERSION : null,
		'only screen and (max-width: 1088px)' // 768px default break + 320px for sidebar
	);
	wp_enqueue_style(
		'seedprod-woocommerce-general',
		str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce.css',
		'',
		defined( 'WC_VERSION' ) ? WC_VERSION : null,
		'all'
	);
}
// get settings
if ( empty( $settings ) ) {
	global $wpdb, $post;
	$settings         = json_decode( $post->post_content_filtered );
	$google_fonts_str = seedprod_lite_construct_font_str( json_decode( $post->post_content_filtered, true ) );
	$content          = $post->post_content;
	$lpage_uuid       = get_post_meta( $post->ID, '_seedprod_page_uuid', true );
} else {
	$google_fonts_str = seedprod_lite_construct_font_str( $settings );
	$content          = $page->post_content;
	$lpage_uuid       = get_post_meta( $page->ID, '_seedprod_page_uuid', true );
}

// remove vue comment bug
$content = str_replace( 'function(e,n,r,i){return fn(t,e,n,r,i,!0)}', '', $content );

// mapped domain settings
$plugin_url = SEEDPROD_PLUGIN_URL;
if ( ! empty( $is_mapped ) ) {
	global $seedprod_url_parsed_scheme, $seedprod_url_parsed_host;
	$new_domain = $seedprod_url_parsed_scheme . '://' . $seedprod_url_parsed_host;
	$domain     = explode( '/wp-content/', $plugin_url );
	$plugin_url = str_replace( $domain[0], $new_domain, $plugin_url );
}


//check to see if we have a shortcode, form or giveaway
$settings_str = serialize( $settings );
if ( strpos( $settings_str, 'contact-form' ) !== false ) {
	$settings->no_conflict_mode = false;
}
if ( strpos( $settings_str, 'giveaway' ) !== false ) {
	$settings->no_conflict_mode = false;
}

$include_seed_fb_sdk   = false;
$facebook_app_id       = '383341908396413';
$seedprod_app_settings = json_decode( get_option( 'seedprod_app_settings' ) );

if ( strpos( $settings_str, 'facebooklike' ) !== false || strpos( $settings_str, 'facebookpage' ) !== false ||
	strpos( $settings_str, 'facebookcomments' ) !== false || strpos( $settings_str, 'facebookembed' ) !== false ) {

	if ( isset( $seedprod_app_settings->facebook_g_app_id ) ) {
		if ( $seedprod_app_settings->facebook_g_app_id != '' ) {
			$facebook_app_id = $seedprod_app_settings->facebook_g_app_id;
		}
	}

	if ( ! empty( $settings->facebook_app_id ) ) {
		$facebook_app_id = $settings->facebook_app_id;
	}

	$include_seed_fb_sdk = true;
}


// get url
$scheme = 'http';
if ( $_SERVER['SERVER_PORT'] == '443' ) {
	$scheme = 'https';
}
if ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$scheme = 'https';
}
$ogurl = "$scheme://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// subscriber callback
$seedprod_subscribe_callback_ajax_url = html_entity_decode( wp_nonce_url( admin_url() . 'admin-ajax.php?action=seedprod_lite_subscribe_callback', 'seedprod_lite_subscribe_callback' ) );

// $email_integration_id = '';
// if (!empty($settings->email_integration_id)) {
//     $email_integration_id = $settings->email_integration_id;
// }

// If site uses WP Rocket, disable minify
seedprod_lite_wprocket_disable_minify();

// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	add_filter( 'woocommerce_enqueue_styles', 'seedprod_lite_wc_dequeue_styles' );

	function seedprod_lite_wc_dequeue_styles( $enqueue_styles ) {
		// Dequeue main syles as it may serve theme-specific styles for themes that may not match SeedProd page
		unset( $enqueue_styles['woocommerce-general'] );

		// Enqueue generic WooCommerce stylesheet for predictable defaults on SeedProd pages
		$enqueue_styles['woocommerce-general'] = array(
			'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce.css',
			'deps'    => '',
			'version' => defined( 'WC_VERSION' ) ? WC_VERSION : null,
			'media'   => 'all',
			'has_rtl' => true,
		);
		return $enqueue_styles;
	}
}

if ( ! empty( $settings ) ) {
	?>
<!DOCTYPE html>
<html class="sp-html 
	<?php
	if ( wp_is_mobile() ) {
		echo 'sp-is-mobile';
	}
	?>
	 <?php
		if ( is_user_logged_in() ) {
				echo 'sp-is-logged-in';
		}
		?>
	 sp-seedprod sp-h-full">
<head>
	<?php
	if ( ! empty( $settings->no_conflict_mode ) ) {
		?>
		<?php if ( ! empty( $settings->seo_title ) ) : ?>
<title><?php echo esc_html( $settings->seo_title ); ?></title>
<?php endif; ?>
		<?php if ( ! empty( $settings->seo_description ) ) : ?>
<meta name="description" content="<?php echo esc_attr( $settings->seo_description ); ?>">
<?php endif; ?>
		<?php if ( ! empty( $settings->favicon ) ) : ?>
<link href="<?php echo esc_attr( $settings->favicon ); ?>" rel="shortcut icon" type="image/x-icon" />
<?php endif; ?>


		<?php if ( ! empty( $settings->no_index ) ) : ?>
<meta name="robots" content="noindex">
<?php endif; ?>



<!-- Open Graph -->
<meta property="og:url" content="<?php echo $ogurl; ?>" />
<meta property="og:type" content="website" />
		<?php if ( ! empty( $settings->seo_title ) ) : ?>
<meta property="og:title" content="<?php echo esc_attr( $settings->seo_title ); ?>" />
<?php endif; ?>
		<?php if ( ! empty( $settings->seo_description ) ) : ?>
<meta property="og:description" content="<?php echo esc_attr( $settings->seo_description ); ?>" />
<?php endif; ?>
		<?php if ( ! empty( $settings->social_thumbnail ) ) : ?>
<meta property="og:image" content="<?php echo $settings->social_thumbnail; ?>" />
<?php elseif ( ! empty( $settings->logo ) ) : ?>
<meta property="og:image" content="<?php echo $settings->logo; ?>" />
<?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="summary" />
		<?php if ( ! empty( $settings->seo_title ) ) : ?>
<meta name="twitter:title" content="<?php echo esc_attr( $settings->seo_title ); ?>" />
<?php endif; ?>
		<?php if ( ! empty( $settings->seo_description ) ) : ?>
<meta name="twitter:description" content="<?php echo esc_attr( $settings->seo_description ); ?>" />
<?php endif; ?>
		<?php if ( ! empty( $settings->social_thumbnail ) ) : ?>
<meta property="twitter:image" content="<?php echo $settings->social_thumbnail; ?>" />
<?php endif; ?>

		<?php
	}
	?>
	<?php if ( empty( $settings->no_conflict_mode ) ) : ?>
<title><?php wp_title(); ?></title>
<?php endif; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Default CSS -->
<link rel='stylesheet' id='seedprod-css-css'  href='<?php echo $plugin_url; ?>public/css/tailwind.min.css?ver=1.2.7.1' type='text/css' media='all' />
<link rel='stylesheet' id='seedprod-fontawesome-css'  href='<?php echo $plugin_url; ?>public/fontawesome/css/all.min.css?ver=1.2.7.1' type='text/css' media='all' />
	<?php if ( ! empty( $google_fonts_str ) ) : ?>
<!-- Google Font -->
<link rel="stylesheet" href="<?php echo $google_fonts_str; ?>">
<?php endif; ?>


	<?php if ( ! empty( $settings->enable_recaptcha ) ) { ?>
<!-- Recaptcha -->
<script src="https://www.google.com/recaptcha/api.js?onload=sp_CaptchaCallback&render=explicit" async defer></script>
<?php } ?>

<!-- Global Styles -->
<style>
	<?php echo $settings->document->settings->headCss; ?>

	<?php if ( ! empty( $settings->document->settings->placeholderCss ) ) { ?>
		<?php echo $settings->document->settings->placeholderCss; ?>
<?php } ?>

	<?php if ( ! empty( $settings->document->settings->mobileCss ) ) { ?>
@media only screen and (max-width: 480px) {
		<?php echo str_replace( '.sp-mobile-view', '', $settings->document->settings->mobileCss ); ?>
}
<?php } ?>

	<?php
	// get mobile css
	preg_match_all( '/data-mobile-css="([^"]*)"/', $content, $matches );
	if ( ! empty( $matches ) ) {
		echo '@media only screen and (max-width: 480px) {';
		foreach ( $matches[1] as $v ) {
			$val    = explode( '|', $v );
			$target = $val[0];
			$css    = $val[1];
			echo $target . '{' . $css . '!important} ';
		}
		echo '}';
		// remove inline data attributes
		foreach ( $matches[0] as $v ) {
			$content = str_replace( $v, '', $content );
		}
	}
	?>


	<?php if ( ! empty( $settings->document->settings->customCss ) ) { ?>
/* Custom CSS */
		<?php
		echo $settings->document->settings->customCss;
		?>
	<?php } ?>
</style>

<!-- JS -->
<script>
var seedprod_api_url = "<?php echo SEEDPROD_API_URL; ?>";
	<?php if ( ! empty( $settings->enable_recaptcha ) ) { ?>
var seeprod_enable_recaptcha = <?php echo $settings->enable_recaptcha; ?>;
<?php } else { ?>
	var seeprod_enable_recaptcha = 0;
<?php } ?>
</script>
<script src="<?php echo $plugin_url; ?>public/js/sp-scripts.min.js" defer></script>
	<?php
	if ( isset( $settings->document->settings->useSlideshowBg ) &&
		   $settings->document->settings->useSlideshowBg ) {
		?>
  <script>
	// Need to defer until after sp-scripts.min.js & defer attribute only works when using src
	window.addEventListener('DOMContentLoaded', (event) => {
		var setDelay = 5000;
		var slides = <?php echo json_encode( $settings->document->settings->useSlideshowImgs ); ?>;
		seedprod_bg_slideshow("body", slides, setDelay);
	});
  </script>
	<?php } ?>

	<?php if ( ! empty( $settings->document->settings->useVideoBg ) ) { ?>
<script src="<?php echo $plugin_url; ?>public/js/tubular.js" defer></script>
	<?php } ?>
	<?php if ( 1 == 0 ) { ?>
<script src="<?php echo $plugin_url; ?>public/js/dynamic-text.js" defer></script>
	<?php } ?>

	<?php
	if ( empty( $settings->no_conflict_mode ) ) {
		wp_enqueue_script( 'jquery' );
		wp_head();
	} else {
		$include_url = trailingslashit( includes_url() );
		if ( empty( $settings->enable_wp_head_footer ) ) {
			echo '<script src="' . $include_url . 'js/jquery/jquery.js"></script>' . "\n";
		}
	}

	/*
	echo "<pre>";
	print_r($settings->document);
	echo "</pre>";
	*/
	?>
	<?php
	if ( ! empty( $settings->header_scripts ) ) {
		echo $settings->header_scripts;
	}
	?>
</head>
<body class="spBg<?php echo $settings->document->settings->bgPosition; ?> sp-h-full sp-antialiased sp-bg-slideshow">
	<?php
	if ( ! empty( $settings->body_scripts ) ) {
		echo $settings->body_scripts;
	}
	?>

	<?php if ( $include_seed_fb_sdk ) { ?>
		
		<div id="fb-root"></div>
		<script async defer crossorigin="anonymous" 
		src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v9.0&appId=<?php echo $facebook_app_id; ?>&autoLogAppEvents=1" 
		>
		</script>
		<?php
		/*


		<div id="fb-root"></div>
		<script>
			window.fbAsyncInit = function() {
				FB.init({
				appId            :'<?php echo $facebook_app_id ?>',
				autoLogAppEvents : true,
				xfbml            : true,
				version          : 'v8.0'
				});
			};
		</script>
		<script async defer crossorigin="anonymous" src="http://connect.facebook.net/en_US/sdk.js"></script>
		*/
		?>
	<?php } ?>



	<?php
	$actual_link = urlencode( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" );
	$content     = str_replace( 'the_link', $actual_link, $content );
	echo do_shortcode( $content );
	?>



<div class="tv">
	<div class="screen mute" id="tv"></div>
</div>

	<?php
	if ( ! empty( $settings->show_powered_by_link ) ) {
		$aff_link = 'https://www.seedprod.com/?utm_source=seedprod-plugin&utm_medium=seedprod-frontend&utm_campaign=powered-by-link';
		if ( ! empty( $settings->affiliate_url ) ) {
			$aff_link = $settings->affiliate_url;
		}

		?>
<div class="sp-credit" >
	<a target="_blank" href="<?php echo $aff_link; ?>" rel="nofollow"><span>made with</span><img src="<?php echo $plugin_url; ?>public/svg/powered-by-logo.svg"></a>
</div>
		<?php
	}
	?>

<script>
	var sp_subscriber_callback_url = '<?php echo $seedprod_subscribe_callback_ajax_url; ?>';
	var sp_is_mobile = 
	<?php
	if ( wp_is_mobile() ) {
		echo 'true';
	} else {
		echo 'false';
	}
	?>
	;
	<?php if ( ! empty( $settings->document->settings->useVideoBg ) ) { ?>
	jQuery( document ).ready(function($) {
	if(!sp_is_mobile){
	$('body').tubular({
						videoId: '<?php echo seedprod_lite_youtube_id_from_url( $settings->document->settings->useVideoBgUrl ); ?>',
						mute: true,
						repeat: true,
						});
					}
	});
	<?php } ?>

</script>

	<?php
	if ( empty( $settings->no_conflict_mode ) ) {
		wp_footer();
	}
	?>
	<?php
	if ( ! empty( $settings->footer_scripts ) ) {
		echo $settings->footer_scripts;
	}
	?>
</body>

</html>

	<?php
} ?>
