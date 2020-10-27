<?php
//query custom db fake
$mapped = array(
    'domain'=> 'ddd.test',
    'path' => 'mypath',
    'mapped_page_id' => 5349,
);
$seedprod_page_mapped_id = null;
$seedprod_page_mapped_url = null;

// get requested url
$seedprod_page_mapped_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$url_parsed = parse_url($seedprod_page_mapped_url);

// see if we have a match, real plugin would query db for domain, then path
if($url_parsed['host'] == $mapped['domain'] && $url_parsed['path'] == '/'.$mapped['path']){
    // if we match show the mapped page
    $seedprod_page_mapped_id = $mapped['mapped_page_id'];
    if(function_exists('bp_is_active')){
        add_action( 'template_redirect', 'seedprod_lite_mapped_domain_render',9);
    }else{
        add_action( 'template_redirect', 'seedprod_lite_mapped_domain_render',10);
    }
}

function seedprod_lite_mapped_domain_render(){
    global $seedprod_page_mapped_id;
    if(!empty($seedprod_page_mapped_id)){
        $has_settings = get_post_meta( $seedprod_page_mapped_id, '_seedprod_page', true );
        if (!empty($has_settings)) {
            // Get Page
            global $wpdb;
            $tablename = $wpdb->prefix . 'posts';
            $sql = "SELECT * FROM $tablename WHERE id= %d";
            $safe_sql = $wpdb->prepare($sql, absint($seedprod_page_mapped_id));
            $page = $wpdb->get_row($safe_sql);

            $settings = json_decode($page->post_content_filtered);
            
            $template = SEEDPROD_PLUGIN_PATH.'resources/views/seedprod-preview.php';
            add_action('wp_enqueue_scripts', 'seedprod_lite_deregister_styles', PHP_INT_MAX);
            add_filter( 'option_siteurl', 'seedprod_lite_modify_url' );
            add_filter( 'option_home', 'seedprod_lite_modify_url' );
            add_filter( 'script_loader_src', 'seedprod_lite_modify_asset_url', 10, 2 );
            add_filter( 'style_loader_src', 'seedprod_lite_modify_asset_url', 10, 2 );
            add_filter( 'stylesheet_directory_uri', 'seedprod_lite_modify_url' );
            add_filter( 'template_directory_uri', 'seedprod_lite_modify_url' );
            add_filter( 'pre_get_document_title', 'seedprod_lite_replace_title', 10, 2 );
            //remove_action( 'wp_head', '_wp_render_title_tag', 1 );
            header("HTTP/1.1 200 OK");
            $is_mapped =true;
            require_once($template);
            
            exit();
        } 
    }
  }

function seedprod_lite_modify_url( $url ) {
    return seedprod_lite_replace_url( $url );
}

function seedprod_lite_modify_asset_url( $url, $handle ) {
    return seedprod_lite_replace_url( $url );
}

function seedprod_lite_replace_url( $url ) {
    global $seedprod_page_mapped_url;
    $url_parsed = parse_url($seedprod_page_mapped_url);
    $new_domain = $url_parsed['scheme'].'://'.$url_parsed['host'];
    if(strpos($url,'/wp-content/') != false){
        $domain = explode('/wp-content/',$url);
        $url = str_replace($domain[0],$new_domain,$url);
    }elseif(strpos($url,'/wp-includes/') != false){
        $domain = explode('/wp-includes/',$url);
        $url = str_replace($domain[0],$new_domain,$url);
    }else{
        $url = $new_domain;
    }
    return $url;
}

function seedprod_lite_replace_title($title){
    global $seedprod_page_mapped_url;
    $url_parsed = parse_url($seedprod_page_mapped_url);
    $new_domain = $url_parsed['host'];
    global $wp_query;
    //if (is_404()) {
        $title = $new_domain;
    //}

    return $title;
}
