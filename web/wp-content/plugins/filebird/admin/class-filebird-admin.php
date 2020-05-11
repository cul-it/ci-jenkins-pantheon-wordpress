<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ninjateam.org
 * @since      1.0.0
 *
 * @package    FileBird
 * @subpackage FileBird/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    FileBird
 * @subpackage FileBird/admin
 * @author     Ninja Team <support@ninjateam.org>
 */
class FileBird_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
   
        add_filter('restrict_manage_posts', array($this, 'restrictManagePosts'));
        //add_action('pre_get_posts', array($this, 'preGetPosts'));
        add_filter('posts_clauses', array($this, 'postsClauses'), 10, 2);
        add_filter('plugin_action_links_' . NJT_FILEBIRD_FOLDER_BASE, array($this, 'go_pro_version'));
        add_action('init', function(){
            if(is_user_logged_in()){
                call_user_func(array($this, 'enqueue_PageBuilder'));
            }
        });
    }

    public function go_pro_version($links)
    {
        if (NJT_FB_V == '0') {
            $links[] = '<a target="_blank" href="https://1.envato.market/FileBirdPro" style="color: #43B854; font-weight: bold">' . __('Go Pro', NJT_FILEBIRD_TEXT_DOMAIN) . '</a>';
            return $links;
        }
        return $links;
    }

    public function enqueue_PageBuilder()
    {
            $filebird_option = get_option('filebird_setting');
            $unloadFrontend = $filebird_option ? $filebird_option['unload-frontend'] : false;
            if(!$unloadFrontend){
                add_action('wp_enqueue_scripts', array($this, 'nt_upload'));
                add_action('wp_enqueue_scripts', function(){
                    wp_enqueue_style('njt-filebird-admin', plugin_dir_url(__FILE__) . 'css/filebird-admin.css', array(), $this->version, 'all');
                    wp_style_add_data('njt-filebird-admin', 'rtl', 'replace');
                });
            }
            FileBird_Topbar::filebird_filters_enqueue_scripts();
    }

    public function postsClauses($clauses, $query)
    {
        global $wpdb;
        if (isset($_GET['njt_filebird_folder'])) {
            $folder = sanitize_text_field($_GET['njt_filebird_folder']);
            if (!empty($folder) != '') {
                $folder = (int) $folder;
                if ($folder > 0) {
                    $term_taxonomy_id = get_term_by('id', $folder, NJT_FILEBIRD_FOLDER, OBJECT)->term_taxonomy_id;
                    if (has_filter('njt_filebird_postsClauses')) {
                        $clauses = apply_filters('njt_filebird_postsClauses', $clauses, $term_taxonomy_id);
                    } else{
                        $clauses['where'] .= ' AND (' . $wpdb->prefix . 'term_relationships.term_taxonomy_id = ' . $term_taxonomy_id . ')';
                        $clauses['join'] .= ' LEFT JOIN ' . $wpdb->prefix . 'term_relationships ON (' . $wpdb->prefix . 'posts.ID = ' . $wpdb->prefix . 'term_relationships.object_id)';
                    }
                } else {
                    //to improve performance: set default folder for files when addnew
                    $folders = FileBird_Helpers::njtGetTerms(NJT_FILEBIRD_FOLDER, array(
                        'hide_empty' => false,
                    ));
                    $folder_ids = array();
                    foreach ($folders as $k => $v) {
                        $folder_ids[] = $v->term_id;
                    }
                    $files_have_folder_query = "SELECT `ID` FROM " . $wpdb->prefix . "posts LEFT JOIN " . $wpdb->prefix . "term_relationships ON (" . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "term_relationships.object_id) WHERE (" . $wpdb->prefix . "term_relationships.term_taxonomy_id IN (" . implode(', ', $folder_ids) . "))";
                    $clauses['where'] .= " AND (" . $wpdb->prefix . "posts.ID NOT IN (" . $files_have_folder_query . "))";
                }
            }
        }

        return $clauses;
    }
    /*public function preGetPosts($query)
    {
    $folder = null;
    if ($query !== null) {
    $folder = $query->get('filebird_folder');
    }
    if ($folder !== null) {
    $query->set('filebird_folder', $folder);
    }
    }*/
    public function restrictManagePosts()
    {
        $scr = get_current_screen();
        if ($scr->base !== 'upload') {
            return;
        }
        echo '<select id="media-attachment-filters" class="wpmediacategory-filter attachment-filters" name="njt_filebird_folder"></select>';
    }
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style('njt-filebird-admin', plugin_dir_url(__FILE__) . 'css/filebird-admin.css', array(), $this->version, 'all');
        wp_style_add_data('njt-filebird-admin', 'rtl', 'replace');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('njt-filebird-upload-event-scripts', plugin_dir_url(__FILE__) . 'js/hook-add-new-upload.js', array('jquery'), $this->version, false);
    }

    public function nt_upload()
    {
        $checkScreen = true;
        if (!function_exists('get_current_screen')) {
            $checkScreen = true;
        } else {
            $screen = get_current_screen();
            $checkScreen = is_null($screen) ? true : $screen->id != 'upload';
        }
        //Get mode
        $mode = get_user_option('media_library_mode', get_current_user_id()) ? get_user_option('media_library_mode', get_current_user_id()) : 'grid';
        $modes = array('grid', 'list');

        if (isset($_GET['mode']) && in_array($_GET['mode'], $modes)) {
            $mode = sanitize_text_field($_GET['mode']);
            update_user_option(get_current_user_id(), 'media_library_mode', $mode);
        }

        //Load Scripts And Styles for Media Upload
        wp_enqueue_style('njt-filebird-sweet-alert-styles', plugin_dir_url(__FILE__) . 'plugin/sweet-alert/sweetalert.css', array(), $this->version, 'all');
        wp_enqueue_style('njt-filebird-mcustomscrollbar-styles', plugin_dir_url(__FILE__) . 'plugin/mCustomScrollbar/jquery.mCustomScrollbar.min.css', array(), $this->version, 'all');
        wp_enqueue_style('njt-filebird-contextMenu' . $this->plugin_name, plugin_dir_url(__FILE__) . 'css/jquery.contextMenu.min.css', array(), $this->version, 'all');
        if (function_exists('get_current_screen') && !is_null(get_current_screen()) && get_current_screen()->id == 'upload'){
            wp_enqueue_style('njt-filebird-explorer' . $this->plugin_name, plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        }
        wp_enqueue_style('njt-filebird-main' . $this->plugin_name, plugin_dir_url(__FILE__) . 'css/main.css', array(), $this->version, 'all');
        wp_style_add_data('njt-filebird-main' . $this->plugin_name, 'rtl', 'replace');

        wp_enqueue_style('njt-filebird-upload-styles', plugin_dir_url(__FILE__) . 'css/filebird-upload.css', array(), $this->version, 'all');
        wp_style_add_data('njt-filebird-upload-styles', 'rtl', 'replace');

        wp_enqueue_style('njt-filebird-folder-container', plugin_dir_url(__FILE__) . 'css/folder-container.css', array(), $this->version, 'all');
        wp_enqueue_script('njt-filebird-jquery-resize', plugin_dir_url(__FILE__) . 'plugin/rick-strahl/jquery-resizable.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-mcustomscrollbar-scripts', plugin_dir_url(__FILE__) . 'plugin/mCustomScrollbar/jquery.mCustomScrollbar.min.js', array('jquery'), $this->version, false);
        
        wp_enqueue_script('njt-filebird-sweet-alert-scripts', plugin_dir_url(__FILE__) . 'plugin/sweet-alert/sweetalert2.all.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-popper', plugin_dir_url(__FILE__) . 'plugin/tippy/popper.js', array(), $this->version, false);
        wp_enqueue_script('njt-filebird-tippy', plugin_dir_url(__FILE__) . 'plugin/tippy/tippy.js', array(), $this->version, false);

        wp_enqueue_script('njt-filebird-contextMenu', plugin_dir_url(__FILE__) . 'js/jquery.contextMenu.min.js', array('jquery'), $this->version, false);

        wp_enqueue_script('njt-filebird-folder-in-content', plugin_dir_url(__FILE__) . 'js/folder-in-content.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-trigger', plugin_dir_url(__FILE__) . 'js/trigger-folder.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-folder', plugin_dir_url(__FILE__) . 'js/folder.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-search-sort', plugin_dir_url(__FILE__) . 'js/filebird-search-sort.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-upload-scripts', plugin_dir_url(__FILE__) . 'js/filebird-upload.js', array('jquery'), $this->version, false);

        wp_enqueue_script('njt-filebird-modal', plugin_dir_url(__FILE__) . 'js/filebird-modal.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-modal-init', plugin_dir_url(__FILE__) . 'js/filebird-modal-init.js', array('jquery'), $this->version, false);
        wp_enqueue_script('njt-filebird-modal-scripts', plugin_dir_url(__FILE__) . 'js/filebird-media.js', array('jquery'), $this->version, false);

        if ($mode === 'grid' || $checkScreen) {
            wp_enqueue_script('njt-filebird-upload-libray-scripts', plugin_dir_url(__FILE__) . 'js/hook-library-upload.js', array('jquery'), $this->version, false);
            wp_enqueue_script('njt-filebird-upload-grid-scripts', plugin_dir_url(__FILE__) . 'js/filebird-upload-grid.js', array('jquery'), $this->version, false);
        } else {
            wp_enqueue_script('njt-filebird-upload-list-scripts', plugin_dir_url(__FILE__) . 'js/filebird-upload-list.js', array('jquery'), $this->version, false);
            wp_localize_script(     
                'njt-filebird-upload-list-scripts',
                'njt_filebird_dh',
                array(
                    'upload_url' => admin_url('upload.php'),
                    'current_folder' => ((isset($_GET['njt_filebird_folder'])) ? sanitize_text_field($_GET['njt_filebird_folder']) : ''),
                    'no_item_html' => '<tr class="no-items"><td class="colspanchange" colspan="' . apply_filters('filebird_noitem_colspan', 6) . '">' . __('No media files found.', NJT_FILEBIRD_TEXT_DOMAIN) . '</td></tr>',
                    'item' => __('item', NJT_FILEBIRD_TEXT_DOMAIN),
                    'items' => __('items', NJT_FILEBIRD_TEXT_DOMAIN),
                )
            );
        }

        //$the_query = new WP_Query("post_type=attachment&posts_per_page=-1");
    }
    public function convert_tree_to_flat_array($array)
    {
        $result = array();
        foreach ($array as $key => $row) {

            $item = new stdClass();
            $item->term_id = $row->term_id;
            $item->name = $row->name;
            $item->parent = $row->parent;
            $item->count = $row->count;

            $result[] = $item;
            if (count($row->children) > 0) {

                $result = array_merge($result, $this->convert_tree_to_flat_array($row->children));

            }
        }

        return $result;
    }
    public function filebird_add_init_media_manager($hook)
    {
        $isCallModal = isset($_POST['action']) && sanitize_text_field($_POST['action']) == 'filebird_ajax_treeview_folder';
        if($isCallModal){
            $nonce = sanitize_text_field($_POST['nonce']);
            if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
                wp_send_json_error(array('status' => 'Nonce error'));
                die();
            }
        }
        $all_count = FileBird_Topbar::count_all_categories_attachment();
        $uncatetory_count = FileBird_Topbar::get_uncategories_attachment();
        $tree = $this->filebird_term_tree_array(NJT_FILEBIRD_FOLDER, 0);
        $folders = $this->convert_tree_to_flat_array($tree);
        $sidebar_splitter_width = get_option('njt-filebird_splitter_width');
        $style = $sidebar_splitter_width && !$isCallModal ? ' style="width: ' . esc_attr($sidebar_splitter_width) . 'px;"' : '';
        ?>
		<div id="filebird_sidebar" style="display: none;">
            <div class="filebird_sidebar">
				<div class="filebird_sidebar_container panel-left" <?php echo $style ?>>
					<input type="hidden" id="filebird_terms">
					<h1 class="nt_main_title"><?php _e('Folders', 'filebird');?></h1>
					<!-- .nt_main_title -->
					<div class="filebird_add_new_container">
						<button type="button" class="nt_main_add_new js__nt_tipped new-folder">

						<span><?php _e('New Folder', NJT_FILEBIRD_TEXT_DOMAIN);?></span></button>
					</div>
					<!-- .filebird_add_new_container -->
					<div class="filebird_toolbar">
						<button type="button" class="nt_main_button_icon js__nt_tipped js__nt_rename button media-button" data-title="<?php _e('Rename', NJT_FILEBIRD_TEXT_DOMAIN);?>">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                <path fill="#8f8f8f" d="M3,4C1.89,4 1,4.89 1,6V18A2,2 0 0,0 3,20H11V18.11L21,8.11V8C21,6.89 20.1,6 19,6H11L9,4H3M21.04,11.13C20.9,11.13 20.76,11.19 20.65,11.3L19.65,12.3L21.7,14.35L22.7,13.35C22.92,13.14 22.92,12.79 22.7,12.58L21.42,11.3C21.31,11.19 21.18,11.13 21.04,11.13M19.07,12.88L13,18.94V21H15.06L21.12,14.93L19.07,12.88Z" />
                            </svg>
                            <span><?php _e('Rename', NJT_FILEBIRD_TEXT_DOMAIN);?></span><span class="opacity0"><?php _e('Rename', NJT_FILEBIRD_TEXT_DOMAIN);?></span>
                        </button>
						<button type="button" class="nt_main_button_icon js__nt_tipped js__nt_delete button media-button">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                <path fill="#8f8f8f" d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                            </svg>
                            <span><?php _e('Delete', NJT_FILEBIRD_TEXT_DOMAIN);?></span><span class="opacity0"><?php _e('Delete', NJT_FILEBIRD_TEXT_DOMAIN);?></span>
                        </button>
                        <div class="njt-filebird-dropdown">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                <path fill="#8f8f8f" d="M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z" />
                            </svg>
                        </div>
					</div>
					<div class="njt-filebird-loader"></div>
					<!-- /.filebird_toolbar -->
					<div id="njt-filebird-defaultTree" class="filebird_tree jstree-default">
                        <ul class="jstree-container-ul">
							<li id="menu-item-all" id="menu-item-all" data-id="all" <?php echo $all_count ?> class="menu-item">
								<a class="jstree-anchor" href="#">
                                    <svg style="width:18px;height:18px" viewBox="0 0 24 24">
                                        <path fill="#8f8f8f" d="M15,7H20.5L15,1.5V7M8,0H16L22,6V18A2,2 0 0,1 20,20H8C6.89,20 6,19.1 6,18V2A2,2 0 0,1 8,0M4,4V22H20V24H4A2,2 0 0,1 2,22V4H4Z" />
                                    </svg>
                                    <span><?php _e('All files', NJT_FILEBIRD_TEXT_DOMAIN);?></span>
                                </a>
							</li>
							<li id="menu-item--1" id="menu-item--1" data-id="-1" <?php echo $uncatetory_count ?> class="menu-item uncategory">
								<a class="jstree-anchor" href="#">
                                    <svg style="width:20px;height:20px" viewBox="0 0 24 24">
                                        <path fill="#8f8f8f" d="M21 11.1V8C21 6.9 20.1 6 19 6H11L9 4H3C1.9 4 1 4.9 1 6V18C1 19.1 1.9 20 3 20H10.3C11.6 21.9 13.8 23 16 23C19.9 23 23 19.9 23 16C23 14.2 22.3 12.4 21 11.1M16 21C13.2 21 11 18.8 11 16S13.2 11 16 11 21 13.2 21 16 18.8 21 16 21M17 20H15V15H17V20M17 14H15V12H17V14Z" />
                                    </svg>
                                    <span><?php _e('Uncategorized', NJT_FILEBIRD_TEXT_DOMAIN);?></span>
                                </a>
							</li>
                        </ul>
                        <div class="njt-filebird-search">
                            <input type="text"/>
                            <svg style="width:20px;height:20px" viewBox="0 0 24 24">
                                <path fill="#8f8f8f" d="M16.5,12C19,12 21,14 21,16.5C21,17.38 20.75,18.21 20.31,18.9L23.39,22L22,23.39L18.88,20.32C18.19,20.75 17.37,21 16.5,21C14,21 12,19 12,16.5C12,14 14,12 16.5,12M16.5,14A2.5,2.5 0 0,0 14,16.5A2.5,2.5 0 0,0 16.5,19A2.5,2.5 0 0,0 19,16.5A2.5,2.5 0 0,0 16.5,14M9,4L11,6H19A2,2 0 0,1 21,8V11.81C19.83,10.69 18.25,10 16.5,10A6.5,6.5 0 0,0 10,16.5C10,17.79 10.37,19 11,20H3C1.89,20 1,19.1 1,18V6C1,4.89 1.89,4 3,4H9Z" />
                            </svg>
                        </div>
					</div>
					<!-- /#njt-filebird-defaultTree -->
					<div id="njt-filebird-folderTree" class="filebird_tree jstree-default">
						<?php $this->build_folder($folders);?>
					</div>
                </div>
			    <div class="njt-splitter"></div>
				<!-- #njt-filebird-folderTree -->
			</div>
			<!-- .filebird_sidebar -->
		</div>
	<?php
    if ($isCallModal) {
            wp_die();
        }
    }

/**
 * Save values of Photographer Name and URL in media uploader
 *
 * @param $post array, the post data for database
 * @param $attachment array, attachment fields from $_POST form
 * @return $post array, modified post data
 */

    // public function be_attachment_field_credit_save( $post, $attachment ) {
    //     if( isset( $attachment['be-photographer-name'] ) )
    //         update_post_meta( $post['ID'], 'be_photographer_name', $attachment['be-photographer-name'] );

    //     if( isset( $attachment['be-photographer-url'] ) )
    //         update_post_meta( $post['ID'], 'be_photographer_url', esc_url( $attachment['be-photographer-url'] ) );

    //     return $post;
    // }

    private function find_depth($folder, $folders, $depth = 0)
    {
        if ($folder->parent != 0) {
            $depth = $depth + 1;
            $parent = $folder->parent;
            $find = array_filter($folders, function ($arr) use ($parent) {
                if ($arr->term_id == $parent) {
                    return $arr;
                } else {
                    return null;
                }
            });
            if (is_null($find)) {
                return $depth;
            } else {
                foreach ($find as $k2 => $v2) {
                    return $this->find_depth($v2, $folders, $depth);
                }
            }
        } else {
            return $depth;
        }
    }

    public function build_folder($folders, $return_obj = false)
    {
        // print_r($folders);die;
        //sort
        $orders = array();

        foreach ($folders as $key => $row) {
            $orders[$key] = $key;
        }
        array_multisort($orders, SORT_ASC, $folders);
        //end sort
        if(!$return_obj) {
            echo '<form action="javascript:void(0);" id="update-folders" enctype="multipart/form-data" method="POST"><ul id="folders-to-edit" class="menu">';
            foreach ($folders as $k => $v) {$depth = $this->find_depth($v, $folders);?>
                <li id="menu-item-<?php echo esc_attr($v->term_id); ?>" data-id="<?php echo esc_attr($v->term_id); ?>" <?php echo $this->filebird_folder_counter($v->count, $v->term_id); ?> class="menu-item menu-item-depth-<?php echo esc_attr($depth); ?>">
                    <i class="dh-tree-icon"></i>
                    <div class="menu-item-bar jstree-anchor">
                        <div class="menu-item-handle">
                            <span class="item-title "><span class="menu-item-title"><?php echo esc_html($v->name); ?></span>
                        </div>
                    </div>
                    <ul class="menu-item-transport"></ul>
                    <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($v->term_id); ?>]" value="<?php echo esc_attr($v->term_id); ?>">
                    <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($v->term_id); ?>]" value="<?php echo esc_attr($v->parent); ?>" />
                </li>
                <?php
            }
            echo '</ul></form>';
        } else {
            foreach ($folders as $k => $v) {
                $depth = $this->find_depth($v, $folders);
                $folders[$k]->name = str_repeat('-', $depth) . $folders[$k]->name;
            }
            return $folders;
        }
    }

    public function filebird_folder_counter($term_count, $term_id = null)
    {
        global $wpdb;

        if (has_filter('njt_filebird_attachment_counter')) {
            return apply_filters( 'njt_filebird_attachment_counter', $term_count, $term_id);
        } else {
            $term_taxonomy_id = get_term_by('id', (int) $term_id, NJT_FILEBIRD_FOLDER, OBJECT)->term_taxonomy_id;

            $term_count = (int) $wpdb->get_var("SELECT COUNT(*) 
                FROM $wpdb->posts as posts
                JOIN $wpdb->term_relationships as trs
                ON posts.ID = trs.object_id
                WHERE posts.post_type = 'attachment' AND trs.term_taxonomy_id IN ($term_taxonomy_id)
                AND (posts.post_status = 'inherit' OR posts.post_status = 'private')
            ");
            return $term_count ? "data-number={$term_count}" : '';
        }
    }

    public function filebird_add_folder_to_attachments()
    {
        register_taxonomy(NJT_FILEBIRD_FOLDER,
            array("attachment"),
            array("hierarchical" => true,
                "labels" => array(
                    'name' => __('Folder', NJT_FILEBIRD_TEXT_DOMAIN),
                    'singular_name' => __('Folder', NJT_FILEBIRD_TEXT_DOMAIN),
                    'add_new_item' => __('Add New Folder', NJT_FILEBIRD_TEXT_DOMAIN),
                    'edit_item' => __('Edit Folder', NJT_FILEBIRD_TEXT_DOMAIN),
                    'new_item' => __('Add New Folder', NJT_FILEBIRD_TEXT_DOMAIN),
                    'search_items' => __('Search Folder', NJT_FILEBIRD_TEXT_DOMAIN),
                    'not_found' => __('Folder not found', NJT_FILEBIRD_TEXT_DOMAIN),
                    'not_found_in_trash' => __('Folder not found in trash', NJT_FILEBIRD_TEXT_DOMAIN),
                ),
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'show_in_nav_menus' => false,
                'show_in_quick_edit' => false,
                'update_count_callback' => '_update_generic_term_count',
                'show_admin_column' => false,
                "rewrite" => false)
        );
    }

    public function filebird_ajax_get_folder_list_callback()
    {
        $terms = FileBird_Helpers::njtGetTerms(NJT_FILEBIRD_FOLDER, array(
            'hide_empty' => false,
            'meta_key' => 'folder_position',
            'orderby' => 'meta_value',
        ));
        // print_r($terms);die;
        echo filebird_loop_term(0, $terms);
        die();
    }

    public function filebird_ajax_update_folder_position_callback()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        $result = sanitize_text_field($_POST["result"]);
        $result = explode("|", $result);
        foreach ($result as $key) {
            $key = explode(",", $key);
            update_term_meta($key[0], 'folder_position', $key[1]);
        }
        die();
    }

    public function nt_custom_upload_filter_callback($file)
    {
        $file['name'] = 'wordpress-is-awesome-' . $file['name'];
        return $file;
    }

    public function filebird_ajax_delete_folder_list_callback()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        $current = sanitize_text_field($_POST["current"]);
        $count_attachments = 0;

        if(apply_filters('njt_fb_can_delete_folder', true, $current) !== true) {
          echo "error_permission";
          exit;
        }

        $current_term = get_term($current, NJT_FILEBIRD_FOLDER);
        $count_attachments = $current_term->count;
        $term = wp_delete_term($current, NJT_FILEBIRD_FOLDER);
        if (is_wp_error($term)) {
            echo "error";
        }
        echo $count_attachments;
        die();
    }

    public static function nt_set_valid_term_name($name, $parent)
    {

        if (!$parent) {
            $parent = 0;

        }

        $terms = FileBird_Helpers::njtGetTerms(NJT_FILEBIRD_FOLDER, array('parent' => $parent, 'hide_empty' => false));

        $check = true;

        if (count($terms)) {

            foreach ($terms as $term) {
                if ($term->name === $name) {
                    $check = false;
                    break;
                }
            }
        } else {
            return $name;
        }

        //$term = get_term_by('name', $name, NJT_FILEBIRD_FOLDER);

        if ($check) {

            return $name;
        }

        $arr = explode('_', $name);

        if ($arr && count($arr) > 1) {

            $suffix = array_values(array_slice($arr, -1))[0];

            //remove end item (suffix) of array
            array_pop($arr);

            //get folder base name (no suffix)
            $origin_name = implode($arr);

            if (intval($suffix)) {

                $name = $origin_name . '_' . (intval($suffix) + 1);

            }

        } else {

            $name = $name . '_1';

        }

        $name = self::nt_set_valid_term_name($name, $parent);

        return $name;

    }

    public function filebird_ajax_update_folder_list_callback()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        $current = isset($_POST["current"]) ? sanitize_text_field($_POST["current"]) : '';
        $new_name = isset($_POST["new_name"]) ? sanitize_text_field($_POST["new_name"]) : '';
        $parent = isset($_POST["parent"]) ? sanitize_text_field($_POST["parent"]) : ''; 
        $type = isset($_POST["type"]) ? sanitize_text_field($_POST["type"]) : '';
        $term_id = isset($_POST["term_id"]) ? sanitize_text_field($_POST["term_id"]) : '';
        switch ($type) {
            case 'new':
                $name = self::nt_set_valid_term_name($new_name, $parent);
                $term_new = wp_insert_term($name, NJT_FILEBIRD_FOLDER, array(
                    'name' => $name,
                    'parent' => $parent,
                ));
                if (is_wp_error($term_new)) {
                  echo "error";
                } else {
                    add_term_meta($term_new["term_id"], 'folder_type', sanitize_text_field($_POST["folder_type"]));
                    add_term_meta($term_new["term_id"], 'folder_position', 10000);
                    do_action('njt_fb_after_inserting_folfer', $term_new["term_id"]);
                    wp_send_json_success(array('term_id' => $term_new["term_id"], 'term_name' => $name));
                }

                break;

            case 'rename':
              if(apply_filters('njt_fb_can_rename_folder', true, $current) !== true) {
                echo "error_permission";
                break;
              }
              $check_error = wp_update_term($current, NJT_FILEBIRD_FOLDER, array(
                'name' => $new_name,
              ));
              if (is_wp_error($check_error)) {
                  echo "error";
              }
              break;
            case 'move':
              if(apply_filters('njt_fb_can_move_folder', true, $current) !== true) {
                echo "error_permission";
                break;
              }
              $check_error = wp_update_term($current, NJT_FILEBIRD_FOLDER, array(
                  'parent' => $parent,
              ));
              if (is_wp_error($check_error)) {
                  echo "error";
              }
              break;
            case 'new_edit_attachment':
              if(apply_filters('njt_fb_can_new_edit_attachment_folder', true, $term_id) !== true) {
                echo "error_permission";
                break;
              }
                if (isset($term_id)) {
                    add_term_meta($term_id, 'folder_type', sanitize_text_field($_POST["folder_type"]));
                    add_term_meta($term_id, 'folder_position', 10000);
                    wp_send_json_success(array('term_id' => $term_id));
                }
                break;
        }
        die();
    }

    public function filebird_ajax_get_child_folders_callback()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        $term_id = sanitize_text_field($_POST['folder_id']);
        // if($term_id === 'all' || $term_id === -1 || $term_id === '-1'){
        // if($term_id === 'all'){
        //     $term_id = 0;
        // }
        $terms = FileBird_Helpers::njtGetTerms(NJT_FILEBIRD_FOLDER, array(
            'hide_empty' => false,
            'meta_key' => 'folder_position',
            'orderby' => 'meta_value',
            'parent' => $term_id,
        ));

        if (is_wp_error($terms)) {
            echo "error";
        }

        wp_send_json_success($terms);
    }

    public function filebird_ajax_save_splitter()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }
        $width = sanitize_text_field($_POST['splitter_width']);
        if (update_option('njt-filebird_splitter_width', $width)) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    public function filebird_ajax_refresh_folder()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
            wp_send_json_error(array('status' => 'Nonce error'));
            die();
        }

        $current_folder = sanitize_text_field($_POST['current_folder']);
        global $wpdb;
        $query = "DELETE FROM " . $wpdb->prefix . "term_relationships" . " WHERE object_id NOT IN (SELECT ID from " . $wpdb->prefix . "posts) AND term_taxonomy_id=" . $current_folder;
        $result = $wpdb->query($query);
        wp_update_term_count_now([$current_folder], 'nt_wmc_folder');
        wp_send_json_success(array('rowChanged' => $result));
    }

    public function filebird_term_tree_option($terms, $spaces = "-")
    {

        $html = '';

        if (!is_null($terms) && count($terms) > 0) {
            foreach ($terms as $item) {
                $html .= '<option value="' . $item->term_id . '" data-id="' . $item->term_id . '">' . $spaces . '&nbsp;' . $item->name . '</option>';

                if (is_array($item->children) && count($item->children) > 0) {
                    $html .= $this->filebird_term_tree_option($item->children, str_repeat($spaces, 2));
                }
            }
        }
        return $html;
    }

    public function filebird_term_tree_array($taxonomy, $parent)
    {

        $terms = FileBird_Helpers::njtGetTerms($taxonomy, array(
            'hide_empty' => false,
            'meta_key' => 'folder_position',
            'orderby' => 'meta_value_num',
            'parent' => $parent,
        ));
        //var_dump($terms);
        $children = array();
        // go through all the direct decendants of $parent, and gather their children
        foreach ($terms as $term) {
            // recurse to get the direct decendants of "this" term
            $term->children = $this->filebird_term_tree_array($taxonomy, $term->term_id);
            // add the term to our new array
            $children[] = $term;
        }
        // send the results back to the caller
        return $children;
    }

    // show in upload file when add Media on alll page
    public function filebird_pre_upload_ui()
    {
        global $pagenow;
        $terms = $this->filebird_term_tree_array(NJT_FILEBIRD_FOLDER, 0);

        // Get the options depending on the current page
        //if ($pagenow === 'media-new.php' || $pagenow === 'post.php' ||  $pagenow === 'post-new.php') {
        $options = $this->filebird_term_tree_option($terms);
        $label = __("Select a folder and upload files (Optional)", NJT_FILEBIRD_TEXT_DOMAIN);
        echo '<p class="attachments-category">' . esc_html($label) . '<br/></p>
	        <p>
	            <select name="ntWMCFolder" class="njt-filebird-editcategory-filter"><option value="-1">-' . __('Uncategorized', NJT_FILEBIRD_TEXT_DOMAIN) . '</option>' . $options . '</select>
	        </p>';
        //  }

    }
}
function filebird_loop_term($parent_id, $terms)
{
    $html = null;
    foreach ($terms as $term) {
        if ($term->parent == $parent_id) {
            if (empty($html)) {
                $html .= '<ul>';
            }

            $sub_html = filebird_loop_term($term->term_id, $terms);
            $folder_type = get_term_meta($term->term_id, 'folder_type', true);
            $html .= '<li';
            $html_jstree = null;
            switch ($folder_type) {
                case 'collection':
                    $html_jstree .= '"type":"collection"';
                    break;
                case 'gallery':
                    $html_jstree .= '"type":"gallery"';
                    break;
                default:
                    $html_jstree .= '"type":"default"';
                    break;
            }
            if ($sub_html) {
                $html_jstree .= ',"opened":true';
            }

            if ($html_jstree) {
                $html .= " data-jstree='{" . $html_jstree . "}'";
            }

            if ($term->count > 0) {
                $html .= " data-number='" . $term->count . "'";
            }

            $html .= ' data-id="' . $term->term_id . '">' . $term->name;
            $html .= $sub_html;
            $html .= '</li>';
        }
    }
    if ($html) {
        $html .= '</ul>';
    }

    return $html;
}

function add_admin_scripts($hook)
{

    global $post;

    //  if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
    if ($hook !== 'upload.php' && $hook !== 'media-new.php') {
        wp_enqueue_script('njt-filebird-upload-scripts', plugin_dir_url(__FILE__) . 'js/hook-post-add-media.js', array('jquery'), NJT_FILEBIRD_VERSION, false);
    }
    // }
}
// add_action('admin_enqueue_scripts', 'add_admin_scripts', 10, 1);