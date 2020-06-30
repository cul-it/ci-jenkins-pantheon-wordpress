<?php
if (!defined('ABSPATH')) {
    exit;
}
  class FileBird_Convert {

    public function __construct() {
        add_action('wp_ajax_njt_fb_import', array($this, 'ajaxImport'));
        add_action('wp_ajax_njt_fb_no_thanks', array($this, 'ajaxNoThanks'));
        
        add_action( 'admin_notices', array($this, 'adminNotice') );
    }
    public function adminNotice() {
      global $pagenow;
      $oldEnhancedFolders = $this->getOldFolders('enhanced', true);
      $oldWpmlfFolders = $this->getOldFolders('wpmlf', true);
      $newFolders = FileBird_Helpers::njtGetTerms(NJT_FILEBIRD_FOLDER, array(
        'hide_empty' => false,
      ));
      
      $sites = array();
      if($pagenow !== 'upload.php' || count($newFolders) > 10) {
        return;
      }
      if(!$this->isUpdated('enhanced') && !$this->isNoThanks('enhanced') && count($oldEnhancedFolders) > 3) {
        $sites[] = array('site' => 'enhanced', 'title' => 'Enhanced Media Library');
      }
      if(!$this->isUpdated('wpmlf') && !$this->isNoThanks('wpmlf') && count($oldWpmlfFolders) > 3) {
        $sites[] = array('site' => 'wpmlf', 'title' => 'Media Library Folders');
      }
      foreach($sites as $k => $site) :
        if($site['site'] == 'enhanced') {
          $c = count($oldEnhancedFolders);
        } else if($site['site'] == 'wpmlf') {
          $c = count($oldWpmlfFolders);
        }
        ?>
        <div class="njt notice notice-warning <?php echo $site['site']; ?> is-dismissible">
          <p>
            <strong><?php _e('Import categories to FileBird', NJT_FILEBIRD_TEXT_DOMAIN); ?></strong>
          </p>
          <p>
            <?php _e(sprintf('We found you have %1$s categories you created from <strong>%2$s</strong> plugin. Would you like to import it to <strong>FileBird</strong>?', $c, $site['title']), NJT_FILEBIRD_TEXT_DOMAIN); ?>
          </p>
          <p>
            <a target="_blank" href="<?php echo esc_url(add_query_arg(array('page' => 'filebird_form_option', 'tab' => 'update'), admin_url('options-general.php'))); ?>" class="button button-primary"><?php _e('Import Now', NJT_FILEBIRD_TEXT_DOMAIN); ?></a> 
            <button class="button njt_fb_no_thanks_btn" data-site="<?php echo $site['site']; ?>"><?php _e('No, thanks', NJT_FILEBIRD_TEXT_DOMAIN) ?></button> 
          </p>
        </div>
      <?php endforeach; ?>
        <script>
        jQuery(document).ready(function(){
          jQuery('.njt_fb_no_thanks_btn').click(function(){
            var $this = jQuery(this);
            $this.addClass('updating-message')
            jQuery.ajax({
              type: "post",
              url: ajaxurl,
              data: {
                action: 'njt_fb_no_thanks',
                nonce: njt_fb_nonce,
                site: $this.data('site')
              },
              success: function (res) {
                $this.removeClass('updating-message');
                jQuery('.njt.notice.notice-warning.' + $this.data('site')).hide()
              }
            })
            .fail(function(res){
                $this.removeClass('updating-message');
                alert('Please try again later')
              });
          })
        })
        </script>
        <?php
    }
    public function isUpdated($site) {
        global $wpdb;
        $is = false;
        if($site == 'enhanced') {
          $is = get_option('njt_fb_updated_from_enhanced', '0') === '1';
        } else if($site == 'wpmlf') {
          $is = get_option('njt_fb_updated_from_wpmlf', '0') === '1';
        }
        return $is;
    }
    public function isNoThanks($site) {
      global $wpdb;
      if($site == 'enhanced') {
        return get_option('njt_fb_enhanced_no_thanks', '0') === '1';
      } else if($site == 'wpmlf') {
        return get_option('njt_fb_wpmlf_no_thanks', '0') === '1';
      }
    }
    public function ajaxImport() {
        global $wpdb;
        //ini_set('display_errors', 1);

        $nonce = isset($_POST['nonce'])? sanitize_text_field($_POST['nonce']) : '';
        $site = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : '';
        $count = isset($_POST['count']) ? sanitize_text_field($_POST['count']) : '';
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
            wp_send_json_error(array('mess' => __('Nonce error', NJT_FILEBIRD_TEXT_DOMAIN)));
            exit();
        }
        $this->beforeGettingNewFolders($site);
        $folders = $this->getOldFolders($site);
        $this->insertFolderAndItsAtt($site, $folders);
        $this->afterInsertingNewFolders($site);
        $this->updateUpdated($site);

        $mess = sprintf(__('Congratulations! We imported successfully %1$s folders into <strong>FileBird.</strong>'), $count);
        wp_send_json_success(array(
            'mess' => $mess
        ));
        exit();
    }
    public function ajaxNoThanks() {
      $nonce = isset($_POST['nonce'])? sanitize_text_field($_POST['nonce']) : '';
      $site = isset($_POST['site'])? sanitize_text_field($_POST['site']) : '';
      if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ){
        wp_send_json_error(array('mess' => __('Nonce error', NJT_FILEBIRD_TEXT_DOMAIN)));
        exit();
      }
      if($site == 'enhanced') {
        update_option('njt_fb_enhanced_no_thanks', '1');
      } else if($site == 'wpmlf') {
        update_option('njt_fb_wpmlf_no_thanks', '1');
      }
      
      wp_send_json_success(array(
        'mess' => __('Success', NJT_FILEBIRD_TEXT_DOMAIN)
      ));
    }
    private function afterInsertingNewFolders($site) {
      global $wpdb;
      $wpdb->delete($wpdb->termmeta, array('meta_key' => 'njt_old_term_id'));
    }
    private function updateUpdated($site) {
      if($site == 'enhanced') {
        update_option('njt_fb_updated_from_enhanced', '1');
      } else if($site == 'wpmlf') {
        update_option('njt_fb_updated_from_wpmlf', '1');
      }
    }
    private function beforeGettingNewFolders($site) {
      if($site == 'enhanced') {
        if(get_option('njt_fb_updated_from_enhanced', '0') == '1') {
          wp_send_json_success(array(
              'mess' => __('Already Updated', NJT_FILEBIRD_TEXT_DOMAIN)
          ));
          exit();
        }
      } else if($site == 'wpmlf') {
        if(get_option('njt_fb_updated_from_wpmlf', '0') == '1') {
          wp_send_json_success(array(
              'mess' => __('Already Updated', NJT_FILEBIRD_TEXT_DOMAIN)
          ));
          exit();
        }
      }
    }
    public function insertFolderAndItsAtt($site, $folders) {
        global $wpdb;

        foreach ($folders as $k => $folder) {
          //insert folder first
          $inserted = wp_insert_term(
              $folder->title,
              NJT_FILEBIRD_FOLDER
          );
          if(is_wp_error($inserted)) {
              $tmp_term_id = $inserted->error_data['term_exists'];
          } else {
              $tmp_term_id = $inserted['term_id'];
              update_term_meta($tmp_term_id, 'njt_old_term_id', $folder->id);
          }
          if($folder->parent > 0) {
            $new_parent = $wpdb->get_var("SELECT term_id FROM $wpdb->termmeta WHERE meta_key = 'njt_old_term_id' AND meta_value = $folder->parent");
            wp_update_term($tmp_term_id, NJT_FILEBIRD_FOLDER, array('parent' => $new_parent));
          }

          update_term_meta($tmp_term_id, 'folder_position', 0);
          if(FileBird_Helpers::foldersForEachUserEnabled() === true) {
              add_term_meta($tmp_term_id, 'fb_created_by', get_current_user_id());
          }
          $atts = $this->getAttOfFolder($site, $folder);
          foreach ($atts as $k2 => $att) {
              wp_set_object_terms(
                  (int)$att,
                  (int)$tmp_term_id,
                  NJT_FILEBIRD_FOLDER,
                  false
              );
          }
          $this->insertFolderAndItsAtt($site, $folder->children);
      }
    }

    public function getOldFolders($site, $flat = false) {
        global $wpdb;
        $folders = array();
        if($site == 'enhanced') {
          $folders = FileBird_Helpers::foldersFromEnhanced(0, $flat);
        } else if($site == 'wpmlf') {
          $folders = FileBird_Helpers::foldersFromWpmlf(0, $flat);
        }
        return $folders;
    }

    public function getAttOfFolder($site, $folder) {
      global $wpdb;
      $att = array();
      if($site == 'enhanced') {
        $att = $wpdb->get_col($wpdb->prepare('SELECT object_id FROM %1$s WHERE term_taxonomy_id = %2$d', $wpdb->term_relationships, $folder->term_taxonomy_id));
      } else if($site == 'wpmlf') {
        $folder_table = $wpdb->prefix . 'mgmlp_folders';
        $sql = "select ID from {$wpdb->prefix}posts 
        LEFT JOIN $folder_table ON({$wpdb->prefix}posts.ID = $folder_table.post_id)
        LEFT JOIN {$wpdb->prefix}postmeta AS pm ON (pm.post_id = {$wpdb->prefix}posts.ID) 
        where post_type = 'attachment' 
        and folder_id = '$folder->id'
        AND pm.meta_key = '_wp_attached_file' 
        order by post_date desc";
        $att = $wpdb->get_col($sql);
      }
      return $att;
    }
  }
