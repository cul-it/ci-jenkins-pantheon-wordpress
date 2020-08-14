<?php
defined('ABSPATH') || exit;

$current_tab = ((isset($_GET['tab']) && in_array(sanitize_text_field($_GET['tab']), array('settings', 'update-db', 'update', 'api'))) ? sanitize_text_field($_GET['tab']) : 'settings');

$countEnhancedFolder = count($helpers::foldersFromEnhanced(0, true));
$countWpmlfFolder = count($helpers::foldersFromWpmlf(0, true));
$countWpmfFolder = count($helpers::foldersFromWpmf(0, true));
$countRealMediaFolder = count($helpers::foldersFromRealMedia(-1, true));

?>
<div class="">
  
  <form action="options.php" method="POST" id="post" autocomplete="off">
    <?php settings_fields('njt_fbv'); ?>
    <?php do_settings_sections('njt_fbv'); ?>
    <nav class="nav-tab-wrapper">
        <a href="<?php echo add_query_arg(array('page' => 'filebird-settings', 'tab' => 'settings'), admin_url('options-general.php')); ?>" class="nav-tab <?php echo $current_tab == 'settings' ? 'nav-tab-active' : '' ?>"><?php _e('Settings', 'filebird') ?></a>
        <a href="<?php echo add_query_arg(array('page' => 'filebird-settings', 'tab' => 'update-db'), admin_url('options-general.php')); ?>" class="nav-tab <?php echo $current_tab == 'update-db' ? 'nav-tab-active' : '' ?>"><?php _e('Update Database', 'filebird') ?></a>
        
        <?php if(($countEnhancedFolder + $countWpmlfFolder + $countWpmfFolder + $countRealMediaFolder) > 0) : ?>
          <a href="<?php echo add_query_arg(array('page' => 'filebird-settings', 'tab' => 'update'), admin_url('options-general.php')); ?>" class="nav-tab <?php echo $current_tab == 'update' ? 'nav-tab-active' : '' ?>"><?php _e('Import', 'filebird') ?></a>
        <?php endif; ?>

        <a href="<?php echo add_query_arg(array('page' => 'filebird-settings', 'tab' => 'api'), admin_url('options-general.php')); ?>" class="nav-tab <?php echo $current_tab == 'api' ? 'nav-tab-active' : '' ?>"><?php _e('API', 'filebird') ?></a>
    </nav>
    <?php if($current_tab == 'settings') : ?>
    <h1><?php _e('Settings', 'filebird'); ?></h1>
    <table class="form-table">
      <tr>
        <th scope="row" style="width: 30%;">
          <label for="njt_fbv_folder_per_user"><?php _e('Each user has his own folders?', 'filebird'); ?></label>
        </th>
        <td>
          <label class="njt-switch">
            <input type="checkbox" name="njt_fbv_folder_per_user" class="njt-submittable" id="njt_fbv_folder_per_user"  value="1" <?php checked(get_option('njt_fbv_folder_per_user'), '1'); ?> />
            <span class="slider round"></span>
          </label>
        </td>
      </tr>
      <?php
      /**
       <tr>
        <th scope="row">
          <label><?php _e('Wipe old data', 'filebird'); ?></label>
          <p class="description" style="font-weight: 400"><?php _e('This action will delete FileBird data in version 3.0 and earlier installs.', 'filebird'); ?></p>
        </th>
        <td>
        <button type="button" class="button button-primary njt_fbv_wipe_old_data"><?php _e('Wipe', 'filebird'); ?></button>
        </td>
      </tr>
       */
      ?>
      <tr>
        <th scope="row">
          <label><?php _e('Clear all data', 'filebird'); ?></label>
          <p class="description" style="font-weight: 400"><?php _e('This action will delete all FileBird data, FileBird settings and bring you back to WordPress default media library.', 'filebird'); ?></p>
        </th>
        <td>
        <button type="button" class="button button-primary njt_fbv_clear_all_data"><?php _e('Clear', 'filebird'); ?></button>
        </td>
      </tr>
    </table>
    <?php //submit_button(); ?>
    <?php elseif ($current_tab == 'update-db'): ?>
    <h1><?php _e('Update Database', 'filebird'); ?></h1>
    <table class="form-table njt-fb-import-tbl">
        <tbody>
            <tr>
                <th scope="row">
                <label><?php _e('Import from old version', 'filebird'); ?></label>
                <p class="description" style="font-weight: 400"><?php _e('By running this action, all folders created in version 3.9 & earlier installs will be imported.', 'filebird'); ?></p>
                </th>
                <td>
                <button type="button" class="button button-primary njt_fbv_import_from_old_now"><?php _e('Update now', 'filebird'); ?></button>
                </td>
            </tr>
        </tbody>
    </table>

    <?php elseif ($current_tab == 'update'): ?>
      <h1><?php _e('Import to FileBird', 'filebird'); ?></h1>
      <p style="margin-top:0;">
        <?php _e('Import categories/folders from other plugins. We import virtual folders, your website will be safe, don\'t worry ;)', 'filebird') ?>
      </p>
        <table class="form-table njt-fb-import-tbl">
            <tbody>
                <tr class="<?php echo $countEnhancedFolder <= 3 ? 'hidden' : ''; ?>">
                    <th scope="row">
                      <label for="">
                        <?php echo __('Enhanced Media Library plugin by wpUXsolutions', 'filebird') ?>
                      </label>
                      <p class="description" style="font-weight: 400">
                        <?php
                          $str = __('We found you have <strong>(%1$s)</strong> categories you created from <strong>Enhanced Media Library</strong> plugin.', 'filebird');
                          if($countEnhancedFolder > 0) {
                            $str .= __(' Would you like to import to <strong>FileBird</strong>?', 'filebird');
                          }
                          echo (sprintf($str, $countEnhancedFolder));
                        ?>
                      </p>
                    </th>
                    <td>
                      <div class="fbv-btn-wrapper-import">
                        <?php if($countEnhancedFolder > 0) : ?>
                          <button class="button button-primary button-large njt-fb-import" data-site="enhanced" type="button" data-count="<?php echo $countEnhancedFolder; ?>"><?php _e('Import Now', 'filebird') ?></button>
                        <?php endif; ?>
                      </div>
                    </td>
                </tr>
                <tr class="fbv-row-breakline <?php echo $countEnhancedFolder <= 3 ? 'hidden' : ''; ?>">
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                </tr>
                <tr class="<?php echo $countWpmlfFolder <= 3 ? 'hidden' : ''; ?>">
                    <th scope="row">
                      <label for="">
                        <?php echo __('WordPress Media Library Folders by Max Foundry', 'filebird') ?>
                      </label>
                      <p class="description" style="font-weight: 400">
                        <?php
                          $str = __('We found you have <strong>(%1$s)</strong> categories you created from <strong>WordPress Media Library Folders</strong> plugin.', 'filebird');
                          if($countWpmlfFolder > 0) {
                            $str .= __(' Would you like to import to <strong>FileBird</strong>?', 'filebird');
                          }
                          echo (sprintf($str, $countWpmlfFolder));
                        ?>
                      </p>
                    </th>
                    <td>
                      <div class="fbv-btn-wrapper-import">
                        <?php if($countWpmlfFolder > 0) : ?>
                          <button class="button button-primary button-large njt-fb-import" data-site="wpmlf" type="button" data-count="<?php echo $countWpmlfFolder; ?>"><?php _e('Import Now', 'filebird') ?></button>
                          <?php endif; ?>
                      </div>
                    </td>
                </tr>
                <tr class="fbv-row-breakline <?php echo $countWpmlfFolder <= 3 ? 'hidden' : ''; ?>">
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                </tr>
                <tr class="<?php echo $countWpmfFolder <= 3 ? 'hidden' : ''; ?>">
                    <th scope="row">
                      <label for="">
                        <?php echo __('WP Media folder by Joomunited', 'filebird') ?>
                      </label>
                      <p class="description" style="font-weight: 400">
                        <?php
                          $str = __('We found you have <strong>(%1$s)</strong> categories you created from <strong>WP Media folder</strong> plugin.', 'filebird');
                          if($countWpmfFolder > 0) {
                            $str .= __(' Would you like to import to <strong>FileBird</strong>?', 'filebird');
                          }
                          echo (sprintf($str, $countWpmfFolder));
                        ?>
                      </p>
                    </th>
                    <td>
                      <div class="fbv-btn-wrapper-import">
                        <?php if($countWpmfFolder > 0) : ?>
                          <button class="button button-primary button-large njt-fb-import" data-site="wpmf" type="button" data-count="<?php echo $countWpmfFolder; ?>"><?php _e('Import Now', 'filebird') ?></button>
                          <?php endif; ?>
                      </div>
                    </td>
                </tr>
                <tr class="fbv-row-breakline <?php echo $countWpmfFolder <= 3 ? 'hidden' : ''; ?>">
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                </tr>
                <tr class="<?php echo $countRealMediaFolder <= 3 ? 'hidden' : ''; ?>">
                    <th scope="row">
                      <label for="">
                        <?php echo __('WP Real Media Library by devowl.io GmbH', 'filebird') ?>
                      </label>
                      <p class="description" style="font-weight: 400">
                        <?php
                          $str = __('We found you have <strong>(%1$s)</strong> categories you created from <strong>WP Real Media Library</strong> plugin.', 'filebird');
                          if($countRealMediaFolder > 0) {
                            $str .= __(' Would you like to import to <strong>FileBird</strong>?', 'filebird');
                          }
                          echo (sprintf($str, $countRealMediaFolder));
                        ?>
                      </p>
                    </th>
                    <td>
                      <div class="fbv-btn-wrapper-import">
                        <?php if($countRealMediaFolder > 0) : ?>
                          <button class="button button-primary button-large njt-fb-import" data-site="realmedia" type="button" data-count="<?php echo $countRealMediaFolder; ?>"><?php _e('Import Now', 'filebird') ?></button>
                          <?php endif; ?>
                      </div>
                    </td>
                </tr>
                <tr class="fbv-row-breakline <?php echo $countRealMediaFolder <= 3 ? 'hidden' : ''; ?>">
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                  <td>
                    <span class="fbv-breakline"></span>
                  </td>
                </tr>
            </tbody>
        </table>
    <?php elseif ($current_tab == 'api'): ?>
    <h1><?php _e('REST API', 'filebird'); ?></h1>
    <?php _e("An API to run Get folders & Set attachments", 'filebird') ?><br/>
    <?php echo __('Please see FileBird API for developers <a target="_blank" href="https://ninjateam.gitbook.io/filebird/api">here</a>.', 'filebird') ?>
    <table class="form-table">
      <tbody>
        <tr>
            <th scope="row">
              <label for="">
                <?php echo __('API key', 'filebird') ?>
              </label>
            </th>
            <td>
              <?php
              $key = get_option('fbv_rest_api_key', '');
              $classes = array('regular-text');
              if(strlen($key) == 0) {
                $classes[] = 'hidden';
              }
              $classes = array_map('esc_attr', $classes);
              ?>
              <input type="text" id="fbv_rest_api_key" class="<?php echo implode(' ', $classes); ?>" value="<?php echo esc_attr($key); ?>" onclick="this.select()" />
              <button type="button" class="button button-primary fbv_generate_api_key_now"><?php _e('Generate'); ?></button>
            </td>
        </tr>
      </tbody>
    </table>
    <?php endif; ?>
  </form>
</div>
<?php
if(isset($_GET['autorun']) && ($_GET['autorun'] == 'true')) {
  ?>
  <script>
    var njt_auto_run_import = true;
    var njt_fb_settings_page = '<?php echo add_query_arg(array('page' => 'filebird-settings', 'tab' => 'update-db'), admin_url('options-general.php')); ?>';
    jQuery(document).ready(function($){
      jQuery('.njt_fbv_import_from_old_now').click();
    })
  </script>
  <?php
}
?>