<?php
/**
 * Show media category filter on top bar
 *
 * @link       https://ninjateam.org
 * @since      1.0.0
 *
 * @package    FileBird_Topbar
 * @subpackage FileBird_Topbar/includes
 */
/** If this file is called directly, abort. */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * class FileBird_Topbar
 * the main class
 */
class FileBird_Topbar
{
    public $plugin_version = NJT_FILEBIRD_VERSION;

    /**
     * Initialize the hooks and filters
     */
    public function __construct()
    {
        // load code that is only needed in the admin section
        if (is_admin()) {
            add_action('add_attachment', array($this, 'filebird_add_attachment_category'));
            add_action('edit_attachment', array($this, 'filebird_set_attachment_category'));
            add_filter('ajax_query_attachments_args', array($this, 'filebird_ajax_query_attachments_args'));
            add_action('wp_ajax_filebird_save_attachment', array($this, 'filebird_save_attachment'), 0);
            add_action('wp_ajax_nt_wcm_get_terms_by_attachment', array($this, 'nt_wcm_get_terms_by_attachment'), 0);
            add_action('wp_ajax_filebird_save_multi_attachments', array($this, 'filebird_save_multi_attachments'), 0);
        }
    }

    public function filebird_add_attachment_category($post_ID)
    {
        $filebird_Folder = isset($_REQUEST["ntWMCFolder"]) ? sanitize_text_field($_REQUEST["ntWMCFolder"]) : null;
        if (is_null($filebird_Folder)) {
            $filebird_Folder = isset($_REQUEST["njt_filebird_folder"]) ? sanitize_text_field($_REQUEST["njt_filebird_folder"]) : null;
        }
        if ($filebird_Folder !== null) {
            $filebird_Folder = (int) $filebird_Folder;
            if ($filebird_Folder > 0) {
                wp_set_object_terms($post_ID, $filebird_Folder, NJT_FILEBIRD_FOLDER, false);
            }
        }
    }

    /**
     * Handle default category of attachments without category
     * @action add_attachment
     * @param array $post_ID
     */
    public function filebird_set_attachment_category($post_ID)
    {

        // default taxonomy
        $taxonomy = NJT_FILEBIRD_FOLDER;
        // add filter to change the default taxonomy
        $taxonomy = apply_filters('filebird_taxonomy', $taxonomy);

        // if attachment already have categories, stop here
        if (wp_get_object_terms($post_ID, $taxonomy)) {
            return;
        }

        // no, then get the default one
        $post_category = array(get_option('default_category'));

        // then set category if default category is set on writting page
        if ($post_category) {
            wp_set_post_categories($post_ID, $post_category);
        }
    }

    public static function filebird_get_terms_values($keys = 'ids')
    {

        // Get media taxonomy
        $media_terms = FileBird_Helpers::njtGetTerms(NJT_FILEBIRD_FOLDER, array(
            'hide_empty' => 0,
            'fields' => 'id=>slug',
        ));

        $media_values = array();
        foreach ($media_terms as $key => $value) {
            $media_values[] = ($keys === 'ids')
            ? $key
            : $value;
        }

        return $media_values;
    }

    /**
     * Changing categories in the 'grid view'
     * @action ajax_query_attachments_args
     * @param array $query
     */
    public function filebird_ajax_query_attachments_args($query = array())
    {
        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);
        // grab original query, the given query has already been filtered by WordPress
        $taxquery = isset($_REQUEST['query']) ? (array) $_REQUEST['query'] : array();

        $taxonomies = get_object_taxonomies('attachment', 'names');
        $taxquery = array_intersect_key($taxquery, array_flip($taxonomies));

        // merge our query into the WordPress query
        $query = array_merge($query, $taxquery);

        $query['tax_query'] = array('relation' => 'AND');

        foreach ($taxonomies as $taxonomy) {
            if (isset($query[$taxonomy])) {
                if (is_numeric($query[$taxonomy])) {
                    if ($query[$taxonomy] > 0) {
                        // $query['post_status'] = 'inherit,private';
                        array_push($query['tax_query'], array(
                            'taxonomy' => $taxonomy,
                            'field' => 'id',
                            'terms' => $query[$taxonomy],
                            'include_children' => false,
                        ));
                        //$query['include_children'] = false;
                    } else {
                        $all_terms_ids = self::filebird_get_terms_values('ids');
                        array_push($query['tax_query'], array(
                            'taxonomy' => $taxonomy,
                            'field' => 'id',
                            'terms' => $all_terms_ids,
                            'operator' => 'NOT IN',
                        ));
                    }
                } elseif (is_array($query[$taxonomy]) && $taxonomy == 'nt_wmc_folder') {
                    $query['tax_query']['relation'] = 'OR';
                    foreach ($query[$taxonomy] as $k => $v) {
                        if (is_numeric($v)) {
                            array_push($query['tax_query'], array(
                                'taxonomy' => $taxonomy,
                                'field' => 'id',
                                'terms' => $v,
                                'include_children' => false,
                            ));
                        }
                    }
                }
            }
            unset($query[$taxonomy]);
        }
        //print_r($query);die;
        return $query;
    }

    public static function filebird_filters_enqueue_scripts()
    {
        $filebird_option = get_option('filebird_setting');
        $unloadFrontend = $filebird_option ? $filebird_option['unload-frontend'] : false;
        if (!$unloadFrontend) {
            add_action('wp_enqueue_scripts', array('FileBird_Topbar', 'filebird_enqueue_media_action'));
        }
        add_action('admin_enqueue_scripts', array('FileBird_Topbar', 'filebird_enqueue_media_action'));
    }

    /**
     * Enqueue admin scripts and styles
     * @action admin_enqueue_scripts
     */
    public static function filebird_enqueue_media_action()
    {
        global $pagenow;

        // Default taxonomy
        $taxonomy = NJT_FILEBIRD_FOLDER;
        // Add filter to change the default taxonomy
        $taxonomy = apply_filters('filebird_taxonomy', $taxonomy);

        $dropdown_options = array(
          'taxonomy' => $taxonomy,
          'hide_empty' => false,
          'hierarchical' => true,
          'orderby' => 'name',
          'show_count' => true,
          'walker' => new filebird_walker_category_mediagridfilter(),
          'value' => 'id',
          'echo' => false,
        );
        $attachment_terms = wp_dropdown_categories($dropdown_options);
        $attachment_terms = preg_replace(array("/<select([^>]*)>/", "/<\/select>/"), "", $attachment_terms);

        // echo '<script type="text/javascript">';
        // echo '/* <![CDATA[ */';
        // echo 'var filebird_folder = "' . NJT_FILEBIRD_FOLDER . '";';
        // echo 'var filebird_taxonomies = {"folder":{"list_title":"' . html_entity_decode(__('All categories', NJT_FILEBIRD_TEXT_DOMAIN), ENT_QUOTES, 'UTF-8') . '","term_list":[{"term_id":"-1","term_name":"' . __('Uncategorized', NJT_FILEBIRD_TEXT_DOMAIN) . '"},' . substr($attachment_terms, 2) . ']}};';
        // echo '/* ]]> */';
        // echo '</script>';

        $term_list = json_decode('[' . substr($attachment_terms, 2) . ']', true);
        array_unshift($term_list, array('term_id' => '-1', 'term_name' => __('Uncategorized', NJT_FILEBIRD_TEXT_DOMAIN)));

        wp_register_script('njt-filebird-upload-localize', plugins_url('admin/js/filebird-util.js', dirname(__FILE__)), array('jquery', 'jquery-ui-draggable', 'jquery-ui-droppable'), NJT_FILEBIRD_VERSION, false);
        wp_localize_script('njt-filebird-upload-localize', 'filebird_folder', NJT_FILEBIRD_FOLDER);
        wp_localize_script('njt-filebird-upload-localize', 'filebird_taxonomies', array('folder' => array('list_title' => __('All categories', NJT_FILEBIRD_TEXT_DOMAIN), 'term_list' => $term_list)));
        wp_localize_script('njt-filebird-upload-localize', 'filebird_translate', FileBird_JS_Translation::get_translation());
        wp_localize_script('njt-filebird-upload-localize', 'njt_fb_nonce', wp_create_nonce('ajax-nonce'));
        wp_localize_script('njt-filebird-upload-localize', 'njtFBV', NJT_FB_V);
        /**
         * --DIVI BUILDER
         * ET_CORE
         * */
        if (defined('ET_CORE')) {
            wp_localize_script('njt-filebird-upload-localize', 'ajaxurl', admin_url('admin-ajax.php'));
        }
        wp_enqueue_script('njt-filebird-upload-localize');
        wp_enqueue_style('njt-filebird-treeview', plugins_url('admin/css/filebird-treeview.css', dirname(__FILE__)), array(), NJT_FILEBIRD_VERSION);
        wp_style_add_data('njt-filebird-treeview', 'rtl', 'replace');
        if (!defined('ELEMENTOR_VERSION') || is_admin()) {
            wp_enqueue_script('filebird-admin-topbar', plugins_url('admin/js/filebird-admin-topbar.js', dirname(__FILE__)), array('media-views'), NJT_FILEBIRD_VERSION, true);
        } else {
            wp_enqueue_script('filebird-admin-topbar', plugins_url('admin/js/filebird-admin-topbar.js', dirname(__FILE__)), array(), NJT_FILEBIRD_VERSION, true);
        }
    }

    public function filebird_save_multi_attachments()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if (!wp_verify_nonce($nonce, 'ajax-nonce')) {
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        $ids = $_REQUEST['ids'];

        $result = array();

        foreach ($ids as $key => $id) {

            $term_list = wp_get_post_terms($id, NJT_FILEBIRD_FOLDER, array('fields' => 'ids'));

            $from = -1;

            if (count($term_list)) {

                $from = $term_list[0];

            }

            $obj = (object) array('id' => $id, 'from' => $from, 'to' => $_REQUEST['folder_id']);

            $result[] = $obj;

            FileBird_Helpers::njtMoveImage($id, $_REQUEST['folder_id']);

            if(has_action('njt_filebird_save_attachment')){
                do_action('njt_filebird_save_attachment', $id, intval($_REQUEST['folder_id']));
            }
        }

        wp_send_json_success($result);

    }

    public function filebird_save_attachment()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if (!wp_verify_nonce($nonce, 'ajax-nonce')) {
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        if (!isset($_REQUEST['id'])) {
            wp_send_json_error();
        }

        if (!$id = absint($_REQUEST['id'])) {
            wp_send_json_error();
        }

        if(!isset($_REQUEST['folder_id'])) {
          wp_send_json_error();
        }
        $folder_id = intval($_REQUEST['folder_id']);

        if(apply_filters('njt_fb_can_save_attachment', true, $folder_id) !== true) {
          wp_send_json_error();
        }


        if (empty($_REQUEST['attachments']) || empty($_REQUEST['attachments'][$id])) {
            wp_send_json_error();
        }
        $attachment_data = $_REQUEST['attachments'][$id];

        //check_ajax_referer( 'update-post_' . $id, 'nonce' );

        // if ( ! current_user_can( 'edit_post', $id ) ) {
        //     wp_send_json_error();
        // }

        $post = get_post($id, ARRAY_A);

        if ('attachment' != $post['post_type']) {
            wp_send_json_error();
        }

        /** This filter is documented in wp-admin/includes/media.php */
        $post = apply_filters('attachment_fields_to_save', $post, $attachment_data);

        if (isset($post['errors'])) {
            $errors = $post['errors']; // @todo return me and display me!
            unset($post['errors']);
        }

        wp_update_post($post);
        
        //add attachment into foder-term
        FileBird_Helpers::njtMoveImage($id, $folder_id);
        if (!$attachment = wp_prepare_attachment_for_js($id)) {
            //echo 1;die;
            wp_send_json_error();
        }

        if(has_action('njt_filebird_save_attachment')){
            do_action('njt_filebird_save_attachment', $id, $folder_id);
        }

        wp_send_json_success($attachment);
    }

    public function nt_wcm_get_terms_by_attachment()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if (!wp_verify_nonce($nonce, 'ajax-nonce')) {
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        if (!isset($_REQUEST['id'])) {
            wp_send_json_error();
        }
        if (!$id = absint($_REQUEST['id'])) {
            wp_send_json_error();
        }
        $terms = get_the_terms($id, NJT_FILEBIRD_FOLDER);
        wp_send_json_success($terms);
    }

    public static function count_all_categories_attachment()
    {
        if (has_filter('njt_filebird_all_categorized_counter')) {
            return apply_filters('njt_filebird_all_categorized_counter', '');
        }else{
            $count = wp_count_posts('attachment')->inherit;
            return $count ? "data-number={$count}" : '';
        }
    }

    public static function get_uncategories_attachment()
    {
        if (has_filter('njt_filebird_uncategorized_counter')) {
            return apply_filters('njt_filebird_uncategorized_counter', '');
        } else {
          $result = FileBird_Helpers::getUncategorizedAttachmentCount();
            return $result ? "data-number={$result}" : '';
        }

        // $args = array(
        //     'post_type' => 'attachment',
        //     'post_status' => 'inherit,private',
        //     'posts_per_page' => -1,
        //     'tax_query' => array
        //     (
        //         'relation' => 'AND',
        //         0 => array
        //         (
        //             'taxonomy' => NJT_FILEBIRD_FOLDER,
        //             'field' => 'id',
        //             'terms' => self::filebird_get_terms_values('ids'),
        //             'operator' => 'NOT IN',
        //         ),

        //     ),

        // );
        // $result = get_posts($args); //don't use WP_query in backend
        // return count($result);
    }
}
$filebird_topbar = new FileBird_Topbar();
