<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function filebird_gallery_fb_block_assets() {
	wp_register_script(
		'filebird_gallery-fb-block-js',
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
		null,
		true
	);

	wp_localize_script(
		'filebird_gallery-fb-block-js',
		'fbGlobal',
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
		]
	);

	register_block_type(
		'fb/block-filebird-gallery', array(
			'editor_script' => 'filebird_gallery-fb-block-js',
		)
	);
}

add_action( 'init', 'filebird_gallery_fb_block_assets' );
///register AJAX
add_action('wp_ajax_filebird-get-folders', 'filebird_get_folders');
function filebird_get_folders() {
	$admin = new FileBird_Admin('filebird', NJT_FILEBIRD_VERSION);
	$tree = $admin->filebird_term_tree_array(NJT_FILEBIRD_FOLDER, 0);
	$folders = $admin->convert_tree_to_flat_array($tree);
	$folders = $admin->build_folder($folders, true);
	$f = array(
		array(
			'value' => 0,
			'label' => 'Please choose folder',
			'disabled' => true
		)
	);
	foreach ($folders as $k => $v) {
		$f[] = array(
			'value' => $v->term_id,
			'label' => $v->name
		);
	}
	wp_send_json_success($f);
	exit;
}
