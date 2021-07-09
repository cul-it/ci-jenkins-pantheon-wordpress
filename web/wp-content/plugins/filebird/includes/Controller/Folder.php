<?php

namespace FileBird\Controller;

use FileBird\Controller\Convert as ConvertController;

use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Helpers as Helpers;
use FileBird\Classes\Tree;

use FileBird\I18n as I18n;

defined('ABSPATH') || exit;

/**
 * Folder Controller
 */

class Folder extends Controller
{

  protected static $instance = null;

  public static function getInstance()
  {
    if (null == self::$instance) {
      self::$instance = new self;
      self::$instance->doHooks();
    }
    return self::$instance;
  }

  public function __construct()
  {
  }

  private function doHooks()
  {
    add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));

    add_action('rest_api_init', array($this, 'registerRestFields'));

    add_action('add_attachment', array($this, 'addAttachment'));
    // add_action('edit_attachment', array($this, 'filebird_set_attachment_category'));
    add_action('delete_attachment', array($this, 'deleteAttachment'));

    add_filter('ajax_query_attachments_args', array($this, 'ajaxQueryAttachmentsArgs'), 20);
    add_filter('mla_media_modal_query_final_terms', array($this, 'ajaxQueryAttachmentsArgs'), 20);
    add_filter('restrict_manage_posts', array($this, 'restrictManagePosts'));
    //add_action('pre_get_posts', array($this, 'preGetPosts'));
    add_filter('posts_clauses', array($this, 'postsClauses'), 10, 2);
    add_action('pre-upload-ui', array($this, 'actionPluploadUi'));
    add_action('wp_ajax_fbv_first_folder_notice', array($this, 'ajax_first_folder_notice'));
    // add_action( 'wp_ajax_fbv_close_buy_pro_dialog', array($this, 'ajaxCloseBuyProDialog'));
    add_action('admin_notices', array($this, 'adminNotices'));
  }

  public function adminNotices()
  {
    global $pagenow;
    //welcome to new filebird message
    $notShownInPages = array('upload.php');
    $optionFirstFolder = get_option('fbv_first_folder_notice');
    if (
      (int)FolderModel::countFolder() == 0
      && !in_array($pagenow, $notShownInPages)
      && ($optionFirstFolder === false || time() >= (int)$optionFirstFolder)
    ) {
?>
      <div class="notice notice-info is-dismissible" id="filebird-empty-folder-notice">
        <p>
          <?php _e('Create your first folder for media library now.', 'filebird') ?>
          <a href="<?php echo esc_url(admin_url('/upload.php')) ?>">
            <strong><?php _e('Get Started', 'filebird') ?></strong>
          </a>
        </p>
      </div>
      <?php
    }
    //import from old folders message
    $is_converted = get_option('fbv_old_data_updated_to_v4', '0');
    if ($is_converted !== '1') {
      $old_folder_count = count(ConvertController::getOldFolers());
      $style = '';
      if ($pagenow === 'upload.php') $style = 'display: none';
      if ($pagenow === 'options-general.php') {
        if (
          isset($_GET['page']) && isset($_GET['tab']) &&
          sanitize_text_field($_GET['page']) === 'filebird-settings' &&
          sanitize_text_field($_GET['tab']) === 'update-db'
        ) $style = 'display: none';
      }
      if ($old_folder_count > 0 && !isset($_GET['autorun'])) {
      ?>
        <div style="<?php echo esc_attr($style) ?>" class="notice notice-warning is-dismissible njt-fb-update-db-noti" id="njt-fb-update-db-noti">
          <div class="njt-fb-update-db-noti-item">
            <h3><?php _e('FileBird 4 Update Required', 'filebird'); ?></h3>
          </div>
          <div class="njt-fb-update-db-noti-item">
            <p>
              <?php _e('You\'re using the new FileBird 4. Please update database to view your folders correctly.', 'filebird'); ?>
            </p>
          </div>
          <div class="njt-fb-update-db-noti-item">
            <p>
              <a class="button button-primary" href="<?php echo esc_url(add_query_arg(array('page' => 'filebird-settings', 'tab' => 'update-db', 'autorun' => 'true'), admin_url('/options-general.php'))); ?>">
                <strong><?php _e('Update now', 'filebird') ?></strong>
              </a>
            </p>
          </div>
        </div>
<?php
      }
    }
  }

  public function ajax_first_folder_notice()
  {
    check_ajax_referer('fbv_nonce', 'nonce', true);
    update_option('fbv_first_folder_notice', time() + 30 * 60 * 60 * 24); //After 3 months show
    wp_send_json_success();
  }

  public function registerRestFields()
  {
    register_rest_route(
      NJFB_REST_URL,
      'get-folders',
      array(
        'methods' => 'GET',
        'callback' => array($this, 'ajaxGetFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'gutenberg-get-folders',
      array(
        'methods' => 'GET',
        'callback' => array($this, 'ajaxGutenbergGetFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );

    register_rest_route(
      NJFB_REST_URL,
      'new-folder',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxNewFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'update-folder',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxUpdateFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'update-folder-ord',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxUpdateFolderOrd'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'delete-folder',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxDeleteFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'set-folder-attachments',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxSetFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'update-tree',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxUpdateTree'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'get-relations',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxGetRelations'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'set-settings',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxSetSettings'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
  }
  public function resPermissionsCheck()
  {
    return current_user_can('upload_files');
  }

  public function enqueueAdminScripts($screenId)
  {
    if (function_exists('get_current_screen')) {
      if ($screenId == "upload.php") {
        wp_register_script('jquery-resizable', NJFB_PLUGIN_URL . 'assets/js/jquery-resizable.min.js');
        wp_enqueue_script('jquery-resizable');
      }
    }

    if ($screenId !== 'pagebuilders') {
      wp_enqueue_script('fbv-import', NJFB_PLUGIN_URL . 'assets/js/import.js', array('jquery'), NJFB_VERSION, false);
    }

    if ($screenId === 'settings_page_filebird-settings') {
      wp_enqueue_script('toastr', NJFB_PLUGIN_URL . 'assets/js/toastr/toastr.min.js', array(), '2.1.3', false);
      wp_enqueue_style('toastr', NJFB_PLUGIN_URL . 'assets/js/toastr/toastr.min.css', array(), '2.1.3');
    }

    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');

    wp_enqueue_script('fbv-folder', NJFB_PLUGIN_URL . 'assets/dist/js/app.js', array(), NJFB_VERSION, false);
    wp_enqueue_script('fbv-lib', NJFB_PLUGIN_URL . 'assets/js/jstree/jstree.min.js', array(), NJFB_VERSION, false);

    wp_enqueue_style('fbv-folder', NJFB_PLUGIN_URL . 'assets/dist/css/app.css', array(), NJFB_VERSION);
    wp_style_add_data('fbv-folder', 'rtl', 'replace');

    wp_localize_script('fbv-folder', 'fbv_data', apply_filters('fbv_data', array(
      'nonce' => wp_create_nonce('fbv_nonce'),
      'rest_nonce' => wp_create_nonce('wp_rest'),
      'nonce_error' => __('Your request can\'t be processed.', 'filebird'),
      'current_folder' => ((isset($_GET['fbv'])) ? (int)sanitize_text_field($_GET['fbv']) : -1), //-1: all files. 0: uncategorized
      'default_folder' => Helpers::getDefaultSelectedFolder(),
      'folders' => FolderModel::allFolders('id as term_id, name as term_name', array('term_id', 'term_name')),
      'relations' => FolderModel::getRelations(),
      // 'is_upload' => $current_screen != null && $current_screen->id === 'upload' ? 1 : 0,
      'i18n' => i18n::getTranslation(),
      'media_mode' => get_user_option('media_library_mode', get_current_user_id()),
      'json_url' => apply_filters('filebird_json_url', rtrim(rest_url(NJFB_REST_URL), "/")),
      'media_url' => admin_url('upload.php'),
      'auto_import_url' => esc_url(add_query_arg(array('page' => 'filebird-settings', 'tab' => 'update-db', 'autorun' => 'true'), admin_url('/options-general.php'))),
      'is_new_user' => get_option('fbv_is_new_user', false),
      // 'close_buy_pro_dialog' => time() < get_option('fbv_close_buy_pro_dialog', time())
    )));
  }

  public function restrictManagePosts()
  {
    $screen = get_current_screen();
    if ($screen->id == "upload") {
      $fbv = ((isset($_GET['fbv'])) ? (int)sanitize_text_field($_GET['fbv']) : -1);
      $folders = FolderModel::allFolders();

      $all = new \stdClass();
      $all->id = -1;
      $all->name = __('All Folders', 'filebird');

      $uncategorized = new \stdClass();
      $uncategorized->id = 0;
      $uncategorized->name = __('Uncategorized', 'filebird');

      array_unshift($folders, $all, $uncategorized);
      echo '<select name="fbv" id="filter-by-fbv" class="fbv-filter attachment-filters fbv">';
      foreach ($folders as $k => $folder) {
        echo sprintf('<option value="%1$d" %3$s>%2$s</option>', $folder->id, $folder->name, selected($folder->id, $fbv, false));
      }
      echo '</select>';
    }
  }
  // public function preGetPosts($query) {
  //   if ( is_admin() && $query->is_main_query() ) {
  //     if (isset($_GET['fbv'])) {
  //       $query->set('fbv', (int)$_GET['fbv']);
  //     }
  //   }
  // }
  public function postsClauses($clauses, $query)
  {
    global $wpdb;
    if ($query->get("post_type") !== "attachment") {
      return $clauses;
    }

    if (Helpers::isListMode() && !isset($_GET['fbv'])) {
      return $clauses;
    }

    // $isFolderUserEnabled = has_filter('fbv_in_not_in_created_by');
    $fbvPropery = $query->get('fbv');
    if ( isset($_GET['fbv']) || $fbvPropery !== '') {
      $fbv = isset($_GET['fbv']) ? (int)sanitize_text_field($_GET['fbv']) : (int)$fbvPropery;
      $table_name = $wpdb->prefix . 'fbv_attachment_folder';
      
      if ($fbv === -1) {
        return $clauses;
      } else if ($fbv === 0) {
        // if ($isFolderUserEnabled) {
        $clauses = FolderModel::getRelationsWithFolderUser($clauses);
        // } else {
        //   $clauses['join'] .= " LEFT JOIN {$table_name} AS fbva ON fbva.attachment_id = {$wpdb->posts}.ID ";
        //   $clauses['where'] .= " AND fbva.folder_id IS NULL";
        // }
      } else {
        $clauses['join'] .= $wpdb->prepare(" LEFT JOIN {$table_name} AS fbva ON fbva.attachment_id = {$wpdb->posts}.ID AND fbva.folder_id = %d ", $fbv);
        $clauses['where'] .= " AND fbva.folder_id IS NOT NULL";
      }
    }
    return $clauses;
  }
  public function addAttachment($post_id)
  {
    $fbv = ((isset($_REQUEST['fbv'])) ? sanitize_text_field($_REQUEST['fbv']) : '');
    if ($fbv != '') {
      if (is_numeric($fbv)) {
        $parent = $fbv;
      } else {
        $fbv = explode('/', ltrim(rtrim($fbv, '/'), '/'));
        $parent = (int)$fbv[0];
        if ($parent < 0) $parent = 0; //important
        unset($fbv[0]);
        foreach ($fbv as $k => $v) {
          $parent = FolderModel::newOrGet($v, $parent);
        }
      }
      FolderModel::setFoldersForPosts($post_id, $parent);
    }
  }
  public function deleteAttachment($post_id)
  {
    FolderModel::deleteFoldersOfPost($post_id);
  }

  public function ajaxQueryAttachmentsArgs($query)
  {
    if (isset($_REQUEST['query']['fbv'])) {
      $fbv = $_REQUEST['query']['fbv'];
      if (is_array($fbv)) {
        $fbv = array_map('intval', $fbv);
      } else {
        $fbv = intval($fbv);
      }
      $query['fbv'] = $fbv;
    }
    return $query;
  }
  public function ajaxGetFolder()
  {

    // if(get_option('fbv_old_data_updated_to_v4', '0') !== '1') {
    //   Convert::insertToNewTable();
    //   update_option('fbv_old_data_updated_to_v4', '1');
    // }

    $order_by = null;
    $sort_option = 'reset';

    $icl_lang = isset($_GET['icl_lang']) ? sanitize_text_field($_GET['icl_lang']) : null;
    if (isset($_GET['sort']) && \in_array(sanitize_text_field($_GET['sort']), array('name_asc', 'name_desc', 'reset'))) {
      if (sanitize_text_field($_GET['sort']) == 'name_asc') {
        $order_by = 'name asc';
        $sort_option = sanitize_text_field($_GET['sort']);
      } elseif (sanitize_text_field($_GET['sort']) == 'name_desc') {
        $order_by = 'name desc';
        $sort_option = sanitize_text_field($_GET['sort']);
      }
      update_option('njt_fb_sort_folder', $sort_option);
    } else {
      $njt_fb_sort_folder = get_option('njt_fb_sort_folder', 'reset');
      if ($njt_fb_sort_folder == 'reset') {
        $order_by = null;
      } elseif ($njt_fb_sort_folder == 'name_asc') {
        $order_by = 'name asc';
      } elseif ($njt_fb_sort_folder == 'name_desc') {
        $order_by = 'name desc';
      }
    }

    $tree = Tree::getFolders($order_by, false);

    wp_send_json_success(array(
      'tree' => $tree,
      'folder_count' => array(
        'total' => is_null($icl_lang) ? Tree::getCount(-1) : Tree::getCount(-1, $icl_lang),
        'folders' => is_null($icl_lang) ? Tree::getAllFoldersAndCount() : Tree::getAllFoldersAndCount($icl_lang)
      )
    ));
  }
  public function ajaxGutenbergGetFolder()
  {
    $_folders = Tree::getFolders(null, true, 0, true);
    $folders = array(
      array(
        'value' => 0,
        'label' => __('Please choose folder', 'filebird'),
        'disabled' => true
      )
    );
    foreach ($_folders as $k => $v) {
      $folders[] = array(
        'value' => $v['id'],
        'label' => $v['text']
      );
    }

    wp_send_json_success($folders);
  }
  public function ajaxNewFolder($request)
  {
    //check_ajax_referer('fbv_nonce', 'nonce', true);
    $name = $request->get_param('name');
    $parent = $request->get_param('parent');
    $name = isset($name) ? sanitize_text_field(wp_unslash($name)) : '';
    $parent = isset($parent) ? sanitize_text_field($parent) : '';
    $id = null;
    if ($name != '' && $parent != '') {
      // if(!is_array($name)) {
      //   $name = array($name);
      // }
      // if($parent < 0) $parent = 0;

      // $paths = array();
      // foreach($name as $k => $v) {
      //   $parent2 = $parent;
      //   $sub_folders = explode('/', ltrim(rtrim($v, '/'), '/'));
      //   foreach($sub_folders as $k2 => $v2) {
      //     $parent2 = FolderModel::newOrGet($v2, $parent2);
      //   }
      //   $paths[$v] = $parent2;
      //   $id = $parent2; //ID Added
      // }
      // wp_send_json_success(array('id' => $id));
      $insert = FolderModel::newOrGet($name, $parent, false);
      if ($insert !== false) {
        wp_send_json_success(array('id' => $insert));
      } else {
        wp_send_json_error(array('mess' => __('A folder with this name already exists. Please choose another one.', 'filebird')));
      }
    } else {
      wp_send_json_error(array(
        'mess' => __('Validation failed', 'filebird')
      ));
    }
  }
  public function ajaxUpdateFolder($request)
  {
    //check_ajax_referer('fbv_nonce', 'nonce', true);
    $id = $request->get_param('id');
    $parent = $request->get_param('parent');
    $name = $request->get_param('name');

    $id = isset($id) ? sanitize_text_field($id) : '';
    $parent = isset($parent) ? intval(sanitize_text_field($parent)) : '';
    $name = isset($name) ? sanitize_text_field(wp_unslash($name)) : '';
    if (is_numeric($id) && is_numeric($parent) && $name != '') {
      $update = FolderModel::updateFolderName($name, $parent, $id);
      if ($update === true) {
        wp_send_json_success();
      } else {
        wp_send_json_error(array('mess' => __('A folder with this name already exists. Please choose another one.', 'filebird')));
      }
    }
    wp_send_json_error();
  }
  public function ajaxUpdateFolderOrd($request)
  {
    $id = $request->get_param('id');
    $parent = $request->get_param('parent');
    $ord = $request->get_param('ord');

    $id = isset($id) ? sanitize_text_field($id) : '';
    $parent = isset($parent) ? sanitize_text_field(wp_unslash($parent)) : '';
    $ord = isset($ord) ? sanitize_text_field(wp_unslash($ord)) : '';
    if (is_numeric($id) && is_numeric($parent) && is_numeric($ord)) {
      FolderModel::updateOrdAndParent($id, $ord, $parent);
      wp_send_json_success();
    }
    wp_send_json_error();
  }
  public function ajaxDeleteFolder($request)
  {
    //check_ajax_referer('fbv_nonce', 'nonce', true);
    $ids = $request->get_param('ids');
    $ids = isset($ids) ? Helpers::sanitize_array($ids) : '';
    if ($ids != '') {
      if (!is_array($ids)) $ids = array($ids);
      $ids = array_map('intval', $ids);

      foreach ($ids as $k => $v) {
        if ($v > 0) FolderModel::deleteFolderAndItsChildren($v);
      }
      wp_send_json_success();
    }
    wp_send_json_error(array(
      'mess' => __('Can\'t delete folder, please try again later', 'filebird')
    ));
  }
  public function ajaxSetFolder($request)
  {
    $ids = $request->get_param('ids');
    $folder = $request->get_param('folder');

    $ids = isset($ids) ? Helpers::sanitize_array($ids) : '';
    $folder = isset($folder) ? sanitize_text_field($folder) : '';
    if ($ids != '' && is_array($ids) && is_numeric($folder)) {
      FolderModel::setFoldersForPosts($ids, $folder);
      wp_send_json_success();
    }
    wp_send_json_error(array(
      'mess' => __('Validation failed', 'filebird')
    ));
  }
  public function ajaxUpdateTree($request)
  {
    //check_ajax_referer('fbv_nonce', 'nonce', true);
    $tree = $request->get_param('tree');

    $tree = isset($tree) ? sanitize_text_field($tree) : '';
    if ($tree != '') {
      $tree = preg_replace('#[^0-9,()]#', '', $tree);
      FolderModel::rawInsert('(id, ord, parent) VALUES ' . $tree . ' ON DUPLICATE KEY UPDATE ord=VALUES(ord),parent=VALUES(parent)');
      wp_send_json_success(array(
        'mess' => __('Folder tree has been updated.', 'filebird')
      ));
    }
    wp_send_json_error(array(
      'mess' => __('Validation failed', 'filebird')
    ));
  }
  public function ajaxGetRelations()
  {
    //check_ajax_referer('fbv_nonce', 'nonce', true);
    wp_send_json_success(array(
      'relations' => FolderModel::getRelations()
    ));
  }
  public function ajaxSetSettings($request)
  {
    $folder_id = $request->get_param('folder_id');

    $folder_id = isset($folder_id) ? intval($folder_id) : -1;
    Helpers::setDefaultSelectedFolder($folder_id);
    wp_send_json_success();
  }
  // public function ajaxCloseBuyProDialog() {
  //   check_ajax_referer('fbv_nonce', 'nonce', true);
  //   update_option('fbv_close_buy_pro_dialog', time() + 7*24*3600); //After 7 days show
  //   wp_send_json_success();
  // }
  private static function addFolderToZip(&$zip, $children, $parent_dir = '')
  {
    foreach ($children as $k => $v) {
      $folder_name = $v->name;
      $folder_id = $v->id;

      $folder_name = sanitize_title($folder_name);

      $attachment_ids = Helpers::getAttachmentIdsByFolderId($folder_id);
      $empty_dir = $parent_dir != '' ? $parent_dir . '/' . $folder_name : $folder_name;
      $zip->addEmptyDir($empty_dir);

      foreach ($attachment_ids as $k => $id) {
        $file = get_attached_file($id);
        if ($file) {
          $zip->addFile($file, $empty_dir . '/' . \basename($file));
        }
      }
      if (\is_array($v->children)) {
        self::addFolderToZip($zip, $v->children, $empty_dir);
      }
    }
  }
  public function actionPluploadUi()
  {
    global $pagenow;
    $folders = FolderModel::allFolders();
    $default = array(
      array(
        'title' => __('Uncategorized', 'filebird'),
        'value' => '0'
      )
    );
    $data = array(
      'tree' => $this->getFlatTree($folders, 0, $default)
    );
    $this->loadView('particle/folder_dropdown', $data);
  }
  private function _buildQuery($tree, $parent)
  {
    $results = array();
    $ord = 0;
    foreach ($tree as $k => $v) {
      // if($v['key'] < 1) continue;
      $results[] = sprintf('(%1$d, %2$d, %3$d)', $v['id'], $ord, $parent);
      if (isset($v['children']) && is_array($v['children']) && count($v['children']) > 0) {
        $children = $this->_buildQuery($v['children'], $v['id']);
        foreach ($children as $k2 => $v2) {
          $results[] = $v2;
        }
      }
      $ord++;
    }
    return $results;
  }

  public function getFlatTree($data, $parent = 0, $default = null, $level = 0)
  {
    $tree = is_null($default) ? array() : $default;
    foreach ($data as $k => $v) {
      if ($v->parent == $parent) {
        $node = array(
          'title' => str_repeat('-', $level) .  $v->name,
          'value' => $v->id,
        );
        $tree[] = $node;
        $children = $this->getFlatTree($data, $v->id, null, $level + 1);
        foreach ($children as $k2 => $child) {
          $tree[] = $child;
        }
      }
    }
    return $tree;
  }
}
