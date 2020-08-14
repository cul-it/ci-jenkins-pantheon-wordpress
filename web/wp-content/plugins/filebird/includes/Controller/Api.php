<?php
namespace FileBird\Controller;

use FileBird\Model\Folder as FolderModel;

use FileBird\Controller\Folder as FolderController;

use FileBird\Classes\Helpers as Helpers;
use FileBird\Classes\Tree;

defined('ABSPATH') || exit;


class Api {

    protected static $instance = null;

    public function __construct() {
        add_action('rest_api_init', array($this, 'registerRestFields'));
    }
    public function registerRestFields() {
        register_rest_route(NJFB_REST_URL,
          'fbv-api',
          array(
            'methods' => 'POST',
            'callback' => array($this, 'restApi'),
            'permission_callback' => array($this, 'resAdminPermissionsCheck'),
          )
        );

        //GET http://yoursite/wp-json/njt-fbv/public/v1/folders
        register_rest_route(NJFB_REST_PUBLIC_URL,
          'folders',
          array(
            'methods' => 'GET',
            'callback' => array($this, 'publicRestApiGetFolders'),
            'permission_callback' => array($this, 'resPublicPermissionsCheck'),
          )
        );

        //POST http://yoursite/wp-json/njt-fbv/public/v1/folder/set-attachment
        //ids=&folder=
        register_rest_route(NJFB_REST_PUBLIC_URL,
          'folder/set-attachment',
          array(
            'methods' => 'POST',
            'callback' => array($this, 'publicRestApiSetAttachment'),
            'permission_callback' => array($this, 'resPublicPermissionsCheck'),
          )
        );
    }
    public function restApi() {
        $act = isset($_POST['act']) ? sanitize_text_field($_POST['act']) : '';
        if($act == 'generate-key') {
            $key = $this->generateRandomString(40);
            update_option('fbv_rest_api_key', $key);
            wp_send_json_success(array('key' => $key));
        }
        wp_send_json_error(array(
            'mess' => __('Invalid action')
        ));
    }
    public function publicRestApiGetFolders() {
        $data = array();

        $order_by = null;
        $data['folders'] = Tree::getFolders($order_by);

        wp_send_json_success($data);
    }
    public function publicRestApiSetAttachment() {
        $ids = ((isset($_POST['ids'])) ? Helpers::sanitize_array($_POST['ids']) : '');
        $folder = ((isset($_POST['folder'])) ? sanitize_text_field($_POST['folder']) : '');

        if(\is_numeric($ids)) $ids = array($ids);

        if($ids != '' && is_numeric($folder)) {
            FolderModel::setFoldersForPosts($ids, $folder);
            wp_send_json_success();
        }
        wp_send_json_error(array(
            'mess' => __('Validation failed', 'filebird')
        ));
    }
    public function resAdminPermissionsCheck() {
        return current_user_can("upload_files");
    }
    public function resPublicPermissionsCheck() {
        $key = get_option('fbv_rest_api_key', '');
        if(\strlen($key) == 40) {
            return $key === $this->getBearerToken();
        }
        return false;
    }

    public static function getInstance() {
        if (null == self::$instance) {
          self::$instance = new self;
        }
        return self::$instance;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
