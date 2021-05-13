<?php
namespace Elementor;

function wts_insert_elementor($atts){

	// Enable support for WPML & Polylang
	$language_support = apply_filters('ae_multilingual_support', false);

    if(!class_exists('Elementor\Plugin')){
        return '';
    }
    if(!isset($atts['id']) || empty($atts['id'])){
        return '';
    }

    $post_id = $atts['id'];

    if($language_support){
	    $post_id = apply_filters( 'wpml_object_id', $post_id, 'ae_global_templates' );
    }



    $response = Plugin::instance()->frontend->get_builder_content_for_display($post_id);
    return $response;
}
add_shortcode('INSERT_ELEMENTOR','Elementor\wts_insert_elementor');


function enable_elementor(){
    add_post_type_support( 'ae_global_templates', 'elementor' );
}
add_action('elementor/init','Elementor\enable_elementor');


function print_ae_data($sections){
    foreach ( $sections as $section_data ) {
        $section = new Element_Section( $section_data );

        $section->print_element();
    }
}
/**
 *  Enable the use of shortcodes in text widgets.
 */
add_filter( 'widget_text', 'do_shortcode' );


function ae_plugin_action_links($links){
    $links['go_pro'] = '<a style="color:green;" title="Upgrade to Pro" href="https://www.elementoraddons.com/anywhere-elementor-pro/?utm_source=gopro&utm_medium=web&utm_campaign=ae" target="_blank"><b>Go Pro</b></a>';
    return $links;
}
add_filter( 'plugin_action_links_' . WTS_AE_PLUGIN_BASE,  'Elementor\ae_plugin_action_links' );
