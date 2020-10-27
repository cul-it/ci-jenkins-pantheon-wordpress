<?php
/**
 * Render Pages
 */


class seedprod_lite_Render
{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;
    private $path = null;

    public function __construct()
    {

        // exit if preview
        if (!empty($_GET['post_type']) && !empty($_GET['preview']) && $_GET['post_type'] == 'seedprod' && $_GET['preview'] =='true') {
            return false;
        }
      
        if (!seedprod_lite_cu('none')) {
            $ts = get_option('seedprod_settings');
            if (!empty($ts)) {
                $seedprod_settings = json_decode($ts, true);
                if (!empty($seedprod_settings)) {
                    extract($seedprod_settings);
                }
            } else {
                return false;
            }


            // Actions & Filters if the landing page is active or being previewed
            if (!empty($seedprod_settings['enable_coming_soon_mode']) || !empty($seedprod_settings['enable_maintenance_mode'])) {
                if (function_exists('bp_is_active')) {
                    add_action('template_redirect', array(&$this,'render_comingsoon_page'), 9);
                } else {
                    $priority = 10;
                    if (function_exists('tve_frontend_enqueue_scripts')) {
                        $priority = 8;
                    }
                    // FreshFramework
                    if (class_exists('ffFrameworkVersionManager')) {
                        $priority = 1;
                    }
                    // Seoframwork
                    if (function_exists('the_seo_framework_pre_load')) {
                        $priority = 1;
                    }
                    // jetpack subscribe
                    if (isset($_REQUEST['jetpack_subscriptions_widget'])) {
                        $priority = 11;
                    }
                    
                    // show legacy versions if we need to
                    #TODO Check if coming soon mode or mm mode and import settings
                    $seedprod_show_csp4 = get_option('seedprod_show_csp4');
                    $seedprod_show_cspv5 = get_option('seedprod_show_cspv5');
                    if($seedprod_show_cspv5){
                        require_once(SEEDPROD_PLUGIN_PATH.'app/backwards/cspv5-functions.php');
                        add_action('template_redirect', 'seedprod_lite_cspv5_render_comingsoon_page', $priority);
                    }elseif($seedprod_show_csp4){
                        require_once(SEEDPROD_PLUGIN_PATH.'app/backwards/csp4-functions.php');
                        add_action('template_redirect', 'seedprod_lite_csp4_render_comingsoon_page', $priority);
                    }else{
                        add_action('template_redirect', array(&$this,'render_comingsoon_page'), $priority);
                    }

                    add_action('admin_bar_menu', 'seedprod_lite_admin_bar_menu', 999);
                }
                add_action('init', array( &$this, 'remove_ngg_print_scripts' ));
            }
        }

        // enable /disable coming soon/maintenanace mode
        add_action('init', array(&$this,'csp_mm_api'));
    }

    /**
     * Return an instance of this class.
     */
    public static function get_instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function remove_ngg_print_scripts()
    {
        if (class_exists('C_Photocrati_Resource_Manager')) {
            remove_all_actions('wp_print_footer_scripts', 1);
        }
    }




    /**
     *  coming soon mode/maintence mode api
     *   mode 0 /disable 1/ coming soon mode 2/maintenance mode
     *  curl http://wordpress.dev/?seed_cspv5_token=4b51fd72-69b7-4796-8d24-f3499c2ec44b&seed_cspv5_mode=1
     */
    public function csp_mm_api()
    {
        $seedprod_api_key= '';
        if (defined('SEEDPROD_API_KEY')) {
            $seedprod_api_key = SEEDPROD_API_KEY;
        }
        if (empty($seedprod_api_key)) {
            $seedprod_api_key = get_option('seedprod_api_key');
        }
        if (!empty($seedprod_api_key)) {
            if (isset($_REQUEST['seedprod_token']) && $_REQUEST['seedprod_token'] == $seedprod_api_key) {
                if (isset($_REQUEST['seedprod_mode'])) {
                    $mode = $_REQUEST['seedprod_mode'];
                    $ts = get_option('seedprod_settings');
                    $seedprod_settings = json_decode($ts, true);


                    if (!empty($seedprod_settings)) {
                        if ($mode == 0) {
                      
                            echo '0';
                            $seedprod_settings['enable_coming_soon_mode'] = false;
                            $seedprod_settings['enable_maintenance_mode'] = false;
                       
                        } elseif ($mode == 1) {
                        
                            echo '1';
                            $seedprod_settings['enable_coming_soon_mode'] = true;
                            $seedprod_settings['enable_maintenance_mode'] = false;
                        
                        } elseif ($mode == 2) {
                          
                            echo '2';
                            $seedprod_settings['enable_coming_soon_mode'] = false;
                            $seedprod_settings['enable_maintenance_mode'] = true;
                      
                        }

                        update_option('seedprod_settings', json_encode($seedprod_settings));
                        exit();
                    }
                }
            }
        }
    }



      


    /**
     * Display the coming soon/ maintenance mode page
     */
    public function render_comingsoon_page()
    {

        // Top Level Settings
        $ts = get_option('seedprod_settings');
        $seedprod_settings = json_decode($ts);
        
        // Page Info
        $page_id = 0;

        //Get Coming Soon Page Id
        if (!empty($seedprod_settings->enable_coming_soon_mode)) {
            $page_id = get_option('seedprod_coming_soon_page_id');
        } elseif (!empty($seedprod_settings->enable_maintenance_mode)) {
            $page_id = get_option('seedprod_maintenance_mode_page_id');
        }

        if (empty($page_id)) {
            wp_die('Your Coming Soon or Maintenance page needs to be setup.');
        }
            

        // Get Page
        global $wpdb;
        $tablename = $wpdb->prefix . 'posts';
        $sql = "SELECT * FROM $tablename WHERE id= %d";
        $safe_sql = $wpdb->prepare($sql, absint($page_id));
        $page = $wpdb->get_row($safe_sql);

        $settings = json_decode($page->post_content_filtered);

        // redirect mode
        $enable_redirect_mode = false;
        $redirect_url = $settings->redirect_url;
        if(!empty($settings->redirect_mode)){
            $enable_redirect_mode = true;
        }
        if(empty($redirect_url)){
            $enable_redirect_mode = false;
        }

        // Countdown Launch
        // if(!empty($settings['countdown_date']) && !empty($settings['enable_countdown']) && !empty($settings['countdown_launch'])){

        //     $date = new DateTime($settings['countdown_date'], new DateTimeZone($settings['countdown_timezone']));
        //     $timestamp = $date->format('U');
        //         // var_dump($timestamp);
        //         // var_dump(time());

        //     // Launch this biatch
        //     if($timestamp <= time()){
        //         // Email the admin the site has been launched
        //         $message = __(sprintf('%s has been launched.',home_url()), 'seedprod');
        //         $result = wp_mail( get_option('admin_email'), __(sprintf('%s has been launched.',home_url()), 'seedprod'), $message);

        //         $o = get_option('seed_cspv5_settings_content');
        //         //var_dump($o);
        //         $o['status'] = 0;
        //         update_option('seed_cspv5_settings_content', $o);
        //         return false;

        //     }
        // }
        

        // Check for Bypass Code
        $bypass_param = false;
        if (!empty($_GET['bypass'])) {
            $bypass_param = $_GET['bypass'];
        }
        if (is_multisite() ||  $settings->bypass_cookie) {

            //Check for Client View
            if (isset($_COOKIE['wp-seedprod-bypass']) && ((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($settings->bypass_phrase))) || (strtolower($bypass_param) == trim(strtolower($settings->bypass_phrase)))) && !empty($settings->bypass_phrase)) {
                if (!empty($_REQUEST['return'])) {
                    nocache_headers();
                    header('Cache-Control: max-age=0, private');
                    header('Location: '.urldecode($_REQUEST['return'])) ;
                    exit;
                } else {
                    nocache_headers();
                    header('Cache-Control: max-age=0, private');
                    header('Location: '.home_url().'?'.rand()) ;
                    exit;
                }
            }

            // Don't show Coming Soon Page if client View is active
            $bypass_hash = md5($settings->bypass_phrase . get_current_blog_id());
            if (isset($_COOKIE['wp-seedprod-bypass']) && $_COOKIE['wp-seedprod-bypass'] == $bypass_hash  && !empty($settings->bypass_phrase)) {
                nocache_headers();
                header('Cache-Control: max-age=0, private');
                return false;
            } else {
                nocache_headers();
                header('Cache-Control: max-age=0, private');
                setcookie("wp-seedprod-bypass", "", time()-3600);
            }



            // If Client view is not empty and we are on the client view url set cookie.
            if (!empty($settings->bypass_phrase)) {
                if ((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($settings->bypass_phrase))) || (strtolower($bypass_param) == trim(strtolower($settings->bypass_phrase)))) {
                    if (!empty($settings->bypass_expires)) {
                        $exipres_in = time()+ (3600 * $settings->bypass_expires);
                    } else {
                        $exipres_in = time()+172800;
                    }


                    setcookie("wp-seedprod-bypass", $bypass_hash, $exipres_in, COOKIEPATH, COOKIE_DOMAIN, false);


                    if (!empty($_REQUEST['return'])) {
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
                        header('Location: '.urldecode($_REQUEST['return'])) ;
                        exit;
                    } else {
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
                        header('Location: '.home_url().'?'.rand()) ;
                        exit;
                    }
                }
            }
        } else {


        // ClientView
            if (!empty($settings->bypass_phrase)) {
                

                // If client view url is passed in log user in
                if ((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($settings->bypass_phrase))) || (strtolower($bypass_param) == trim(strtolower($settings->bypass_phrase)))) {
                    if (!username_exists('seedprod_bypass_user_'.$settings->bypass_phrase)) {
                        $user_id = wp_create_user('seedprod_bypass_user_'.$settings->bypass_phrase, wp_generate_password());
                        $user = new WP_User($user_id);
                        $user->set_role('none');
                    }


                    if (!empty($settings->bypass_expires)) {
                        global $seedprod_bypass_expires;
                        $seedprod_bypass_expires = (3600 * $settings->bypass_expires);
                    }

                    $bypass_hash = md5($settings->bypass_phrase . get_current_blog_id());
                    setcookie("wp-seedprod-bypass", $bypass_hash, 0, COOKIEPATH, COOKIE_DOMAIN, false);


                    add_filter('auth_cookie_expiration', 'seedprod_lite_change_wp_cookie_logout');

                    // Log user in auto
                    $username = 'seedprod_bypass_user_'.$settings->bypass_phrase;
                    if (!is_user_logged_in()) {
                        $user = get_user_by('login', $username);
                        $user_id = $user->ID;
                        wp_set_current_user($user_id, $username);
                        wp_set_auth_cookie($user_id);
                        do_action('wp_login', $username, $user);
                        update_user_meta($user_id, 'show_admin_bar_front', false);
                    }

                    if (!empty($_REQUEST['return'])) {
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
                        header('Location: '.urldecode($_REQUEST['return'])) ;
                        exit;
                    } else {
                        nocache_headers();
                        header('Cache-Control: max-age=0, private');
                        header('Location: '.home_url().'?'.rand()) ;
                        exit;
                    }
                }
            }
        }

        // Check for excluded IP's
        if (!empty($settings->access_by_ip)) {
            $ip = seedprod_lite_get_ip();
            $exclude_ips = explode("\n", $settings->access_by_ip);
            if (is_array($exclude_ips) && in_array($ip, $exclude_ips)) {
                return false;
            }
        }
        

       
        if (!empty($settings->include_exclude_type) && $settings->include_exclude_type == '2') {
            if (substr($settings->include_list, 0, 3) != '>>>') {

         // Check for included pages
                if (!empty($settings->include_list)) {
                    //$url = preg_replace('/\?ref=\d*/','',$_SERVER['REQUEST_URI']);
                    // TODO lok for when wordpress is in sub folder
                    $request_uri = explode('?', $_SERVER['REQUEST_URI']);
                    $url = rtrim(ltrim($request_uri[0], '/'), '/');

                    $r = array_intersect(explode('/', $url), explode('/', home_url()));

                    $url = str_replace($r, '', $url);

                    $url = str_replace('/', '', $url);
                    //var_dump($url);

                    $include_urls = explode("\n", $settings->include_list);
                    $include_urls = array_filter($include_urls);
                    $include_urls = str_replace(home_url(), "", $include_urls);
                    $include_urls = str_replace('/', '', $include_urls);
                    //$include_urls = array_filter($include_urls);
                    //var_dump($include_urls);
                    //var_dump($url);
                    $post_id = '';
                    global $post;
                    //var_dump($post->ID);
                    if (!empty($post->ID)) {
                        $post_id = $post->ID;
                    }

                    $show_coming_soon_page = false;

                    if (is_array($include_urls) && (in_array($url, $include_urls) || in_array($post_id, $include_urls))) {
                        $show_coming_soon_page = true;
                    }

                    // check wildcard urls
                    $urls_to_test = $include_urls;
                    $urls_to_test = str_replace(home_url(), "", $urls_to_test);
                    $url_uri = $_SERVER['REQUEST_URI'];
                    foreach ($urls_to_test as $url_to_test) {
                        if (strpos($url_to_test, '*') !== false) {
                            // Wildcard url
                            $url_to_test = str_replace("*", "", $url_to_test);
                            if (strpos($url_uri, untrailingslashit($url_to_test)) !== false) {
                                $show_coming_soon_page = true;
                            }
                        }
                    }

                    if ($show_coming_soon_page === false) {
                        return false;
                    }
                }
            } else {
                // Check for included pages regex
                $settings->include_list = substr($settings->include_list, 3);
                if (!empty($settings->include_list) && @preg_match("/{$settings->include_list}/", $_SERVER['REQUEST_URI']) == 0) {
                    return false;
                }
            }
        }

        // Check for excludes pages
        if (!empty($settings->include_exclude_type) && $settings->include_exclude_type == '3') {
            if (substr($settings->exclude_list, 0, 3) != '>>>') {
                if (!empty($settings->exclude_list)) {
                    //$url = preg_replace('/\?ref=\d*/','',$_SERVER['REQUEST_URI']);
                    $request_uri = explode('?', $_SERVER['REQUEST_URI']);
                    $url = rtrim(ltrim($request_uri[0], '/'), '/');

                    $r = array_intersect(explode('/', $url), explode('/', home_url()));

                    $url = str_replace($r, '', $url);

                    $url = str_replace('/', '', $url);
                    //var_dump($url);

                    $exclude_urls = explode("\n", $settings->exclude_list);
                    $exclude_urls = array_filter($exclude_urls);
                    $exclude_urls = str_replace(home_url(), "", $exclude_urls);
                    $exclude_urls = str_replace('/', '', $exclude_urls);
                    //$exclude_urls = array_filter($exclude_urls);
                    $post_id = '';
                    global $post;
                    //var_dump($post->ID);
                    if (!empty($post->ID)) {
                        $post_id = $post->ID;
                    }
                
                    // check exact urls
                    if (is_array($exclude_urls) && (in_array($url, $exclude_urls) || in_array($post_id, $exclude_urls))) {
                        return false;
                    }

                    // check wildcard urls
                    $urls_to_test = $exclude_urls;
                    $urls_to_test = str_replace(home_url(), "", $urls_to_test);
                    $url_uri = $_SERVER['REQUEST_URI'];
                    foreach ($urls_to_test as $url_to_test) {
                        if (strpos($url_to_test, '*') !== false) {
                            // Wildcard url
                            $url_to_test = str_replace("*", "", $url_to_test);
                            if (strpos($url_uri, untrailingslashit($url_to_test)) !== false) {
                                return false;
                            }
                        }
                    }

                    // Check for affiliateWP
                    if (class_exists('Affiliate_WP') && (strpos($url, 'ref') !== false)) {
                        return false;
                    }
                }
            } else {

            // Check for excluded pages
                $settings->exclude_list = substr($settings->exclude_list, 3);
                if (!empty($settings->exclude_list) && @preg_match("/{$settings->exclude_list}/", $_SERVER['REQUEST_URI']) > 0) {
                    return false;
                }
            }
        }
        

       
        // Only show the Coming Soon Page on the home page
        if (!empty($settings->include_exclude_type) && $settings->include_exclude_type == '1') {
            if ($_SERVER['REQUEST_URI'] == "/" || substr($_SERVER['REQUEST_URI'], 0, 2) == '/?') {
            } else {
                return false;
            }
        }



        // Check if redirect url and exclude
        if (!empty($enable_redirect_mode) && !empty($redirect_url)) {
            $r_url = parse_url($redirect_url);
            if ($r_url['host'] == $_SERVER['HTTP_HOST'] && $r_url['path'] == $_SERVER['REQUEST_URI']) {
                return false;
            }
        }

        // Exit if a custom login page
        if (!empty($settings->disable_default_excluded_urls)) {
            if (preg_match("/login|admin|dashboard|account/i", $_SERVER['REQUEST_URI']) > 0) {
                return false;
            }
        }

       
        //Exit if wysija double opt-in
        if (isset($emaillist) &&  $emaillist == 'wysija' && preg_match("/wysija/i", $_SERVER['REQUEST_URI']) > 0) {
            return false;
        }

        if (isset($emaillist) &&  $emaillist == 'mailpoet' && preg_match("/mailpoet/i", $_SERVER['REQUEST_URI']) > 0) {
            return false;
        }

        if (isset($emaillist) &&  $emaillist == 'mymail' && preg_match("/confirm/i", $_SERVER['REQUEST_URI']) > 0) {
            return false;
        }



        //Limit access by role
        if (!empty($settings->access_by_role) && !isset($_COOKIE['wp-seedprod-bypass'])) {
            foreach ($settings->access_by_role as $v) {
                $v = str_replace(' ', '', strtolower($v));
                if ($v == 'anyoneloggedin' && is_user_logged_in()) {
                    return false;
                }
                if (current_user_can($v)) {
                    return false;
                }
            }
        } elseif (is_user_logged_in()) {
            return false;
        }
        

         
        // Finally check if we should show the coming soon page.
        // do not cache this page
        if(!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        if(!defined('DONOTCDN')) {
            define('DONOTCDN', true);
        }
        if(!defined('DONOTCACHEDB')) {
            define('DONOTCACHEDB', true);
        }
        if(!defined('DONOTMINIFY')) {
            define('DONOTMINIFY', true);
        }
        if(!defined('DONOTCACHEOBJECT')) {
            define('DONOTCACHEOBJECT', true);
        }
        nocache_headers();

        // set headers
        if (!empty($seedprod_settings->enable_maintenance_mode)) {
            if (empty($settings)) {
                echo __("Please create your Maintenance Page in the plugin settings.", "seedprod-coming-soon-pro");
                exit();
            }
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Status: 503 Service Temporarily Unavailable');
            header('Retry-After: 86400'); // retry in a day
        } elseif (!empty($enable_redirect_mode)) {
            if (!empty($redirect_url)) {
                wp_redirect($redirect_url);
                exit;
            } else {
                echo __("Please create enter your redirect url in the plugin settings.", "seedprod-coming-soon-pro");
                exit();
            }
        } else {
            if (empty($settings)) {
                echo __("Please create your Coming Soon Page in the plugin settings.", "seedprod-coming-soon-pro");
                exit();
            }
            header("HTTP/1.1 200 OK");

        }

        if (is_feed()) {
            header('Content-Type: text/html; charset=UTF-8');
        }


       

        // keep for backwards compatability
        $upload_dir = wp_upload_dir();
        if (is_multisite()) {
            $path = $upload_dir['baseurl'].'/seedprod/'.get_current_blog_id().'/template-'.$page_id.'/index.php';
        } else {
            $path = $upload_dir['basedir'].'/seedprod/template-'.$page_id.'/index.php';
        }

        if (!empty($page->html) && 1 == 0) {
            echo $page->html;
        } else {
            if (file_exists($path)) {
                require_once($path);
            } else {
                require_once(SEEDPROD_PLUGIN_PATH.'resources/views/seedprod-preview.php');
            }
        }

        exit();
    }
}
