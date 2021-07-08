<?php


require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';


global $wpdb;

// current user
$current_user              = wp_get_current_user();
$current_user_name         = $current_user->display_name;
$current_user_email        = $current_user->user_email;
$current_user_email_hash   = md5( $current_user->user_email );
$free_templates_subscribed = get_option( 'seedprod_free_templates_subscribed' );
if ( $free_templates_subscribed ) {
	$free_templates_subscribed = '1';
}
$seedprod_nonce = wp_create_nonce( 'seedprod_nonce' );


$lpage_id = '';
if ( ! empty( $_GET['id'] ) ) {
	$lpage_id = absint( $_GET['id'] );
} else {
	wp_die();
}

// Template Vars
$timezones     = seedprod_lite_get_timezones();
$times         = seedprod_lite_get_times();
$block_options = seedprod_lite_block_options();


// get page
$tablename = $wpdb->prefix . 'posts';
$sql       = "SELECT * FROM $tablename WHERE id = %d";
$safe_sql  = $wpdb->prepare( $sql, $lpage_id );
$lpage     = $wpdb->get_row( $safe_sql );

// reset id
$lpage->id = $lpage->ID;

// Get page uuid
$lpage_uuid = get_post_meta( $lpage->id, '_seedprod_page_uuid', true );
if ( empty( $lpage_uuid ) ) {
	$this_uuid = wp_generate_uuid4();
	update_post_meta( $lpage->id, '_seedprod_page_uuid', $this_uuid );
	$lpage_uuid = $this_uuid;
}

// add default settings if they do not exisits
if ( empty( $lpage->post_content_filtered ) ) {
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
	$settings                            = json_decode( $seedprod_basic_lpage, true );
	$settings['page_type']               = 'lp';
	$settings['from_edit_with_seedprod'] = true;
} else {
	// get settings and maybe modify
	$settings = json_decode( $lpage->post_content_filtered, true );
}




// get preview link
//$preview_link = get_preview_post_link( $lpage_id );
if ( $settings['page_type'] == 'lp' ) {
	$preview_link = home_url() . "/?page_id=$lpage_id&preview_id=$lpage_id&preview_nonce=" . wp_create_nonce( 'post_preview_' . $lpage_id ) . '&preview=true';
} else {
	$preview_link = home_url() . "/?post_type=seedprod&page_id=$lpage_id&preview_id=$lpage_id&preview_nonce=" . wp_create_nonce( 'post_preview_' . $lpage_id ) . '&preview=true';

}

// keep track for changes
$settings['post_title']  = $lpage->post_title;
$settings['post_name']   = $lpage->post_name;
$settings['post_status'] = $lpage->post_status;

$show_bottombar_cta    = true;
$dismiss_bottombar_cta = get_option( 'seedprod_dismiss_upsell_2' );
if ( $dismiss_bottombar_cta ) {
	$show_bottombar_cta = false;
}


// Email integration logic
$seedprod_api_token    = get_option( 'seedprod_api_token' );
$seedprod_user_id      = get_option( 'seedprod_user_id' );
$seedprod_site_token   = get_option( 'seedprod_token' );
$license_key           = get_option( 'seedprod_api_key' );
$email_integration_url = '';


$seedprod_app_settings = get_option( 'seedprod_app_settings' );
if ( ! empty( $seedprod_app_settings ) ) {
	$seedprod_app_settings = json_decode( stripslashes( $seedprod_app_settings ) );
} else {
	// fail safe incase settings go missing
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
	update_option( 'seedprod_app_settings', $seedprod_app_default_settings );
	$seedprod_app_settings = json_decode( $seedprod_app_default_settings );
}


$template_preview_path = 'https://assets.seedprod.com/preview-';


// Pers
$per                        = array();
$active_license             = false;
$template_dev_mode          = false;
$template_dev_mode_url      = false;
$template_dev_mode_password = false;

?>


<style>
 .sp-mobile-view  .sp-w-full {
	width: 100% !important;
}

.sp-mobile-view .sp-el-section,.sp-mobile-view .sp-el-row,.sp-mobile-view .sp-el-col,.sp-mobile-view .sp-el-block{
		padding: 5px !important;
	}

.sp-mobile-view .sm\:sp-flex {
	display: block;
}

.mce-content-body {
	line-height: 1.5;
}

h1.mce-content-body,h2.mce-content-body,h3.mce-content-body,h4.mce-content-body,h5.mce-content-body,h6.mce-content-body {
	line-height: 1.1;
}
</style>

<div id="seedprod-vue-app-builder" class="sp-font-sans"></div>

<?php
	$fontawesome_file = SEEDPROD_PLUGIN_PATH . 'resources/data-templates/fontawesome.json';
	$fontawesome_json = json_decode( file_get_contents( $fontawesome_file ) );
	$icons            = array();
foreach ( $fontawesome_json as $v ) {
	$icons[] = array(
		'c' => 'fa',
		'n' => $v,
	);
}

	$googlefonts_file = SEEDPROD_PLUGIN_PATH . 'resources/data-templates/googlefonts.json';

	$fonts['Standard Fonts'] = array(
		"'Helvetica Neue', Arial, sans-serif"   => 'Helvetica Neue',
		'Garamond, serif'                       => 'Garamond',
		'Georgia, serif'                        => 'Georgia',
		'Impact, Charcoal, sans-serif'          => 'Impact',
		'Tahoma, Geneva, sans-serif'            => 'Tahoma',
		"'Times New Roman', Times,serif"        => 'Times New Roman',
		"'Trebuchet MS', Helvetica, sans-serif" => 'Trebuchet',
		'Verdana, Geneva, sans-serif'           => 'Verdana',
		'Courier, monospace'                    => 'Courier',
		"'Comic Sans MS', cursive"              => 'Comic Sans',
	);
	$fonts['Google Fonts']   = json_decode( file_get_contents( $googlefonts_file ) );
	//$googlefonts_json = json_decode(file_get_contents($googlefonts_file));

	//get list of fonts to load
	$google_fonts_str = seedprod_lite_construct_font_str( $settings['document'] );

	?>

<?php if ( ! empty( $google_fonts_str ) ) : ?>
<!-- Google Font -->
<link rel="stylesheet" href="<?php echo $google_fonts_str; ?>">
<?php endif; ?>

<script>
var seedprod_nonce = "<?php echo $seedprod_nonce; ?>";
var seedprod_page = "<?php echo sanitize_text_field($_GET['page']); ?>";
var seedprod_remote_api = "<?php echo SEEDPROD_API_URL; ?>";
<?php
$from = '';
if ( ! empty( $_GET['from'] ) ) {
	$form = sanitize_text_field($_GET['from']);
}
?>
var seedprod_from = "<?php echo $from; ?>";
<?php
// see if we need below
$ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_save_template', 'seedprod_lite_save_template' ) );
?>
var seedprod_template_save_url = "<?php echo $ajax_url; ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_template_subscribe', 'seedprod_lite_template_subscribe' ) ); ?>
var seedprod_template_subscribe_url = "<?php echo $ajax_url; ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_save_page', 'seedprod_lite_save_page' ) ); ?>
var seedprod_save_lpage_url = "<?php echo $ajax_url; ?>";

<?php $utc_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_get_utc_offset', 'seedprod_lite_get_utc_offset' ) ); ?>
var seedprod_utc_url = "<?php echo $utc_url; ?>";

<?php $get_namespaced_custom_css_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_get_namespaced_custom_css', 'seedprod_lite_get_namespaced_custom_css' ) ); ?>
var seedprod_get_namespaced_custom_css_url = "<?php echo $get_namespaced_custom_css_url; ?>";

<?php $stockimages_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_get_stockimages', 'seedprod_lite_get_stockimages' ) ); ?>
var seedprod_stockimages_url = "<?php echo $stockimages_url; ?>";

<?php $backgrounds_sideload_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_backgrounds_sideload', 'seedprod_lite_backgrounds_sideload' ) ); ?>
var seedprod_backgrounds_sideload_url = "<?php echo $backgrounds_sideload_url; ?>";

<?php $backgrounds_download_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_backgrounds_download', 'seedprod_lite_backgrounds_download' ) ); ?>
var seedprod_backgrounds_download_url = "<?php echo $backgrounds_download_url; ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_slug_exists', 'seedprod_lite_slug_exists' ) ); ?>
var seedprod_slug_exists_url = "<?php echo $ajax_url; ?>";

<?php $seedprod_upgrade_link = seedprod_lite_upgrade_link( '' ); ?>

<?php $url = seedprod_lite_get_plugins_install_url( 'all-in-one-seo-pack' ); ?>
var seedprod_seo_install_link = "<?php echo htmlspecialchars_decode( $url ); ?>";

<?php $url = seedprod_lite_get_plugins_install_url( 'wpforms-lite' ); ?>
var seedprod_form_install_link = "<?php echo htmlspecialchars_decode( $url ); ?>";

<?php $url = seedprod_lite_get_plugins_install_url( 'rafflepress' ); ?>
var seedprod_giveaway_install_link = "<?php echo htmlspecialchars_decode( $url ); ?>";

<?php $url = seedprod_lite_get_plugins_install_url( 'google-analytics-for-wordpress' ); ?>
var seedprod_analytics_install_link = "<?php echo htmlspecialchars_decode( $url ); ?>";

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'google-analytics-for-wordpress/googleanalytics.php' );
?>

var seedprod_analytics_activate_link = "<?php echo htmlspecialchars_decode( $url ); ?>";

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'wpforms-lite/wpforms.php' );
?>

var seedprod_form_activate_link = "<?php echo htmlspecialchars_decode( $url ); ?>";

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'all-in-one-seo-pack/all_in_one_seo_pack.php' );
?>
var seedprod_seo_activate_link = "<?php echo htmlspecialchars_decode( $url ); ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_install_addon', 'seedprod_lite_install_addon' ) ); ?>
var seedprod_get_install_addon_url = "<?php echo $ajax_url; ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_activate_addon', 'seedprod_lite_activate_addon' ) ); ?>
var seedprod_activate_addon_url = "<?php echo $ajax_url; ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_deactivate_addon', 'seedprod_lite_deactivate_addon' ) ); ?>
var seedprod_deactivate_addon_url = "<?php echo $ajax_url; ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_plugin_nonce', 'seedprod_lite_plugin_nonce' ) ); ?>
var seedprod_plugin_nonce_url = "<?php echo $ajax_url; ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_dismiss_upsell', 'seedprod_lite_dismiss_upsell' ) ); ?>
var seedprod_dismiss_upsell = "<?php echo $ajax_url; ?>";

<?php
$unfiltered_html= false;
if(current_user_can( 'unfiltered_html' )){
	$unfiltered_html = true;
}
?>

var seedprod_data = 
<?php
$seedprod_data = array(
	'unfiltered_html'			  => $unfiltered_html,
	'show_bottombar_cta'          => $show_bottombar_cta,
	'template_preview_path'       => $template_preview_path,
	'page_uuid'                   => $lpage_uuid,
	'placeholder_image'           => SEEDPROD_PLUGIN_URL . 'public/img/img-placeholder.png',
	'placeholder_sm_image'        => SEEDPROD_PLUGIN_URL . 'public/img/img-placeholder-sm.png',
	'block_templates'             => json_decode( $seedprod_lite_block_templates ),
	'expire_times'                => seedprod_lite_get_expire_times(),
	'roles'                       => seedprod_lite_get_roles(),
	'my_ip'                       => seedprod_lite_get_ip(),
	'plugins_installed'           => seedprod_lite_get_plugins_array(),
	'giveaway_plugins_installed'  => seedprod_lite_get_giveaway_plugins_list(),
	'form_plugins_installed'      => seedprod_lite_get_form_plugins_list(),
	'seo_plugins_installed'       => seedprod_lite_get_seo_plugins_list(),
	'analytics_plugins_installed' => seedprod_lite_get_analytics_plugins_list(),
	'page_type'                   => $settings['page_type'],
	'current_user_name'           => $current_user_name,
	'current_user_email_hash'     => $current_user_email_hash,
	'current_user_email'          => $current_user_email,
	'free_templates_subscribed'   => $free_templates_subscribed,
	'preview_link'                => $preview_link,
	'icons'                       => $icons,
	'googlefonts'                 => $fonts,
	'api_token'                   => $seedprod_api_token,
	'seedprod_user_id'            => $seedprod_user_id,
	'site_token'                  => $seedprod_site_token,
	'license_key'                 => $license_key,
	'page_path'                   => 'seedprod_lite',
	'plugin_path'                 => SEEDPROD_PLUGIN_URL,
	'web_path'                    => SEEDPROD_WEB_API_URL,
	'home_url'                    => home_url(),
	'upgrade_link'                => $seedprod_upgrade_link,
	'lpage'                       => $lpage,
	'settings'                    => $settings,
	'app_settings'                => $seedprod_app_settings,
	'block_options'               => $block_options,
	'timezones'                   => $timezones,
	'times'                       => $times,
	'template_dev_mode'           => $template_dev_mode,
	'template_dev_mode_url'       => $template_dev_mode_url,
	'template_dev_mode_password'  => $template_dev_mode_password,
	'email_integration_url'       => $email_integration_url,
	'per'                         => $per,
	'active_license'              => $active_license,
);

//if (function_exists('wpforms')) {
	$seedprod_data['wpforms'] = array(
		'edit_form_url' => admin_url( 'admin.php?page=wpforms-builder&view=fields&form_id=' ),
		'add_form_url'  => admin_url( 'admin.php?page=wpforms-builder&view=setup' ),
		'placeholder'   => sprintf( '<img src="%s" width="80px" alt="WPForms Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-wpforms.png' ) ),
	);

	$rp_version = 'lite';
	if ( function_exists( 'rafflepress_pro_load_textdomain' ) ) {
		$rp_version = 'pro';
	}

	$seedprod_data['rafflepress'] = array(
		'edit_form_url' => admin_url( 'admin.php?page=rafflepress_' . $rp_version . '_builder&id=$id$#/setup/$id$' ),
		'add_form_url'  => admin_url( 'admin.php?page=rafflepress_' . $rp_version . '_builder&id=0#/template' ),
		'placeholder'   => sprintf( '<img src="%s" width="80px" alt="RafflePress Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-rp.png' ) ),
	);
	//}

	// Check if WooCommerce is active
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$seedprod_data['wc_active'] = true;
	} else {
		$seedprod_data['wc_active'] = false;
	}

	echo wp_json_encode( $seedprod_data );
	?>
	;

		jQuery('link[href*="forms.css"]').remove();
		jQuery('link[href*="common.css"]').remove();

	//     var stop = true;
	// jQuery(".sp-drag-section").on("drag", function (e) {

	//     stop = true;

	//     if (e.originalEvent.clientY < 150) {
	//         stop = false;
	//         scroll(-1)
	//     }

	//     if (e.originalEvent.clientY > (jQuery(window).height() - 150)) {
	//         stop = false;
	//         scroll(1)
	//     }

	// });

	// jQuery(".draggable").on("dragend", function (e) {
	//      stop = true;
	// });

	// var scroll = function (step) {
	//     var scrollY = jQuery(window).scrollTop();
	//     jQuery(window).scrollTop(scrollY + step);
	//     if (!stop) {
	//         setTimeout(function () { scroll(step) }, 20);
	//     }
	// }
</script>

