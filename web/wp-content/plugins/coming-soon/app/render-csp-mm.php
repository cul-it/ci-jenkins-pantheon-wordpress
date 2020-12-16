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
