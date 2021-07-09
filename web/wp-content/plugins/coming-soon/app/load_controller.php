<?php
// must load first
require_once SEEDPROD_PLUGIN_PATH . 'app/functions-utils.php';

require_once SEEDPROD_PLUGIN_PATH . 'app/cpt.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/admin-bar-menu.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/notifications.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/render-lp.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/render-csp-mm.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/backwards/backwards_compatibility.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/subscriber.php';
add_action( 'plugins_loaded', array( 'seedprod_lite_Render', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'SeedProd_Notifications', 'get_instance' ) );

if ( is_admin() ) {
	// Admin Only
	require_once SEEDPROD_PLUGIN_PATH . 'app/settings.php';
	require_once SEEDPROD_PLUGIN_PATH . 'app/lpage.php';
	//require_once(SEEDPROD_PLUGIN_PATH.'app/subscriber.php');
	require_once SEEDPROD_PLUGIN_PATH . 'app/functions-addons.php';
	if ( SEEDPROD_BUILD == 'lite' ) {
		require_once SEEDPROD_PLUGIN_PATH . 'app/review.php';
	}
} else {
	// Public only
}


// Load on Public and Admin
require_once SEEDPROD_PLUGIN_PATH . 'app/license.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/includes/upgrade.php';





function seedprod_lite_admin_js() {
	// Make Admin upgrade submenu link target _blank
	if ( defined( 'SEEDPROD_TEMPLATE_DEV_MODE' ) && SEEDPROD_TEMPLATE_DEV_MODE === true ) {
		echo "
        <script>
            jQuery( document ).ready(function($) {
                $('.toplevel_page_seedprod_lite .wp-first-item').hide();
            });
        </script>
        ";
	}
	echo "
    <script>
        jQuery( document ).ready(function($) {
            $('#sp-lite-admin-menu__upgrade').parent().attr('target','_blank');
            $('#sp-feature-request').parent().attr('target','_blank');
        });
    </script>
    ";

	if ( ! empty( $_GET['post'] ) ) {
		$id          = absint($_GET['post']);
		$is_seedprod = 0;
		if ( ! empty( get_post_meta( $id, '_seedprod_page', true ) ) ) {
			$is_seedprod = get_post_meta( $id, '_seedprod_page', true );
		}
		$post_type = get_post_type( $id );
		// $edit_link = sprintf(
		//     '<a href="%1$s">%2$s</a>',
		//     admin_url().'admin.php?page=seedprod_lite_builder&id='.$id.'#/setup/'.$id,
		//     __( 'Edit with SeedProd', 'seedprod' );

		$setup_url = admin_url() . 'admin.php?page=seedprod_lite_builder&id=' . $id . '#/template/' . $id;
		$edit_url  = admin_url() . 'admin.php?page=seedprod_lite_builder&id=' . $id . '#/setup/' . $id;
		if ( $post_type == 'page' ) {
			echo "
    <script>
    jQuery( document ).ready(function($) {
        var checkExist = setInterval(function() {
            if ($('.edit-post-header-toolbar').length) {
                if(1 === " . $is_seedprod . "){
                    $('.block-editor-block-list__layout').hide().after('<div style=\"text-align:center; \" class=\"managed_by_seedprod\">This page is managed by SeedProd<br><a href=\"" . $edit_url . '" class="button button-primary" style="display:flex; align-items:center; justify-content:center; margin:auto; width:200px; font-size: 18px; margin-top:10px"><img src="' . SEEDPROD_PLUGIN_URL . "public/svg/admin-bar-icon.svg\" style=\"margin-right:7px; margin-top:5px\"> Edit with SeedProd</a></div>');

                }
               clearInterval(checkExist);
            }
            if ($('#postdivrich').length) {
                if(1 === " . $is_seedprod . "){
            $('#postdivrich').hide().after('<div style=\"text-align:center; \" class=\"managed_by_seedprod\">This page is managed by SeedProd<br><a href=\"" . $edit_url . '" class="button button-primary" style="display:flex; align-items:center; justify-content:center; margin:auto; width:220px; font-size: 16px; margin-top:10px"><img src="' . SEEDPROD_PLUGIN_URL . "public/svg/admin-bar-icon.svg\" style=\"margin-right:7px; margin-top:5px\"> Edit with SeedProd</a></div>');
            clearInterval(checkExist);
                }
            }
         }, 100);

    });
    </script>
    ";
		}
	}
}
add_action( 'admin_footer', 'seedprod_lite_admin_js' );
