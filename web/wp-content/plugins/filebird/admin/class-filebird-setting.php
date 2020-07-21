<?php
class FileBird_Setting
{
    private $plugin_name;
    private $version;
    private $option_name;
    private $option_group;
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->option_group = 'filebird_setting';
        $this->option_name = 'filebird_setting';
        add_action('admin_init', array($this, 'register_setting'));
    }

    public function create_admin_sub_menu()
    {
        add_submenu_page('options-general.php',
            __('FileBird Setting', NJT_FILEBIRD_TEXT_DOMAIN),
            __('FileBird', NJT_FILEBIRD_TEXT_DOMAIN),
            'manage_options',
            'filebird_form_option',
            array($this, 'form_option')
        );
    }

    public function form_option(){
        $filebird_option = get_option('filebird_setting');
        $unloadFrontend = $filebird_option ? $filebird_option['unload-frontend'] : false;
        $current_tab = ((isset($_GET['tab']) && in_array(sanitize_text_field($_GET['tab']), array('settings', 'update'))) ? sanitize_text_field($_GET['tab']) : 'settings');
        ?>
        <form name="post" method="post" action="options.php" id="post" autocomplete="off">
            <?php wp_nonce_field($this->option_group . '-options'); ?>
            <style>
                input[type="checkbox"] {
                    display: none;
                }
                input[type="checkbox"] + label {
                    display: inline-block;
                    width: 40px;
                    height: 20px;
                    position: relative;
                    transition: 0.3s;
                    margin: 0px 20px;
                    box-sizing: border-box;
                }
                input[type="checkbox"] + label:after, input[type="checkbox"] + label:before {
                    content: '';
                    display: block;
                    position: absolute;
                    left: 0px;
                    top: 0px;
                    width: 20px;
                    height: 20px;
                    transition: 0.3s;
                    cursor: pointer;
                }
                .fb-checkbox:checked + label.green {
                    background: #AEDCAE;
                }
                .fb-checkbox:checked + label.green:after {
                    background: #5CB85C;
                }
                .fb-checkbox:checked + label:after {
                    left: calc( 100% - 18px );
                }
                .fb-checkbox + label {
                    background: #ddd;
                    border-radius: 20px;
                }
                .fb-checkbox + label:after {
                    background: #fff;
                    border-radius: 50%;
                    width: 16px;
                    height: 16px;
                    top: 2px;
                    left: 2px;
                }
                table.form-table.njt-fb-import-tbl tr th p {
                    font-weight: normal;
                }
            </style>
            <input type="hidden" name="option_page" value="<?php echo esc_attr($this->option_group); ?>">
            <input type="hidden" name="action" value="update">
            <nav class="nav-tab-wrapper">
                <a href="<?php echo add_query_arg(array('page' => 'filebird_form_option', 'tab' => 'settings'), admin_url('options-general.php')); ?>" class="nav-tab <?php echo $current_tab == 'settings' ? 'nav-tab-active' : '' ?>"><?php _e('Settings', NJT_FILEBIRD_TEXT_DOMAIN) ?></a>
                <a href="<?php echo add_query_arg(array('page' => 'filebird_form_option', 'tab' => 'update'), admin_url('options-general.php')); ?>" class="nav-tab <?php echo $current_tab == 'update' ? 'nav-tab-active' : '' ?>"><?php _e('Import', NJT_FILEBIRD_TEXT_DOMAIN) ?></a>
            </nav>
            <?php if($current_tab == 'settings') : ?>
              <h1><?php _e('FileBird Setting', NJT_FILEBIRD_TEXT_DOMAIN); ?></h1>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th style="width: 300px; padding: 20px 10px 10px 0" scope="row">
                            <label for=""><?php echo __('Don\'t want FileBird loading on front end?', NJT_FILEBIRD_TEXT_DOMAIN) ?></label>
                            </th>
                            <td style="padding: 20px 10px 10px 0">
                            <div class="">
                                <input type="checkbox" class="fb-checkbox" id="fb-unload-frontend" name="unload-frontend"
                                    <?php echo ($unloadFrontend ? 'checked' : '') ?>>
                                <label for="fb-unload-frontend" class="green"></label>
                            </div>
                            </td>
                        </tr>
                        <tr>
                        <td colspan="2">
                            <p class="description" style="margin-bottom: 40px"><?php echo __('Notice: If you turn on this option, FileBird will not function on front-end builders such as Divi, WPBakery, Beaver...', NJT_FILEBIRD_TEXT_DOMAIN) ?></p>
                        </td>
                        </tr>
                        <?php if(is_super_admin()) : ?>
                        <?php $foldersForEachUser = (is_array($filebird_option) && isset($filebird_option['foldersForEachUser'])) ? ($filebird_option['foldersForEachUser'] === true) : false; ?>
                        <tr>
                            <th style="width: 300px; padding: 20px 10px 10px 0" scope="row">
                            <label for=""><?php echo __('Each user has his own folders?', NJT_FILEBIRD_TEXT_DOMAIN) ?></label>
                            </th>
                            <td style="padding: 20px 10px 10px 0">
                            <div class="">
                                <input type="checkbox" class="fb-checkbox" id="fb-foldersForEachUser" name="foldersForEachUser"
                                    <?php echo ($foldersForEachUser ? 'checked' : '') ?>>
                                <label for="fb-foldersForEachUser" class="green"></label>
                            </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button class="button button-primary button-large" id="btnSave" type="submit"><?php echo __('Save Setting', NJT_FILEBIRD_TEXT_DOMAIN) ?></button>
            <?php elseif ($current_tab == 'update'): ?>
              <?php
                $countEnhancedFolder = count(FileBird_Helpers::foldersFromEnhanced(0, true));  
                $countWpmlfFolder = count(FileBird_Helpers::foldersFromWpmlf(0, true));
              ?>
              <h1><?php _e('Import to FileBird', NJT_FILEBIRD_TEXT_DOMAIN); ?></h1>
              <p>
                <?php _e('Import categories/folders from other plugins. We import virtual folders, your website will be safe, don\'t worry ;)', NJT_FILEBIRD_TEXT_DOMAIN) ?>
              </p>
                <table class="form-table njt-fb-import-tbl">
                    <tbody>
                        <tr>
                            <th style="width: 70%;" scope="row">
                              <label for="">
                                <?php echo __('Import database from Enhanced Media Library plugin', NJT_FILEBIRD_TEXT_DOMAIN) ?>
                              </label>
                              <p>
                                <?php
                                  $str = 'We found you have %1$s categories you created from <strong>Enhanced Media Library</strong> plugin.';
                                  if($countEnhancedFolder > 0) {
                                    $str .= ' Would you like to import to <strong>FileBird</strong> ?';
                                  }
                                  _e(sprintf($str, $countEnhancedFolder), NJT_FILEBIRD_TEXT_DOMAIN);
                                ?>
                              </p>
                            </th>
                            <td style="padding: 20px 10px 10px 0">
                              <div class="">
                                <?php if($countEnhancedFolder > 0) : ?>
                                  <button class="button button-primary button-large njt-fb-import" data-site="enhanced" type="button" data-count="<?php echo $countEnhancedFolder; ?>"><?php _e('Import Now', NJT_FILEBIRD_TEXT_DOMAIN) ?></button>
                                <?php endif; ?>
                              </div>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 70%;" scope="row">
                              <label for="">
                                <?php echo __('WordPress Media Library Folders by Max Foundry', NJT_FILEBIRD_TEXT_DOMAIN) ?>
                              </label>
                              <p>
                                <?php
                                  $str = 'We found you have %1$s categories you created from <strong>WordPress Media Library Folders</strong> plugin.';
                                  if($countWpmlfFolder > 0) {
                                    $str .= ' Would you like to import to <strong>FileBird</strong> ?';
                                  }
                                  _e(sprintf($str, $countWpmlfFolder), NJT_FILEBIRD_TEXT_DOMAIN);
                                ?>
                              </p>
                            </th>
                            <td style="padding: 20px 10px 10px 0">
                              <div class="">
                                <?php if($countWpmlfFolder > 0) : ?>
                                  <button class="button button-primary button-large njt-fb-import" data-site="wpmlf" type="button" data-count="<?php echo $countWpmlfFolder; ?>"><?php _e('Import Now', NJT_FILEBIRD_TEXT_DOMAIN) ?></button>
                                  <?php endif; ?>
                              </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <script>
                    jQuery( document ).ready(function() {
                        jQuery('.njt-fb-import').click(function(){
                          var $this = jQuery(this);
                          $this.addClass('updating-message')
                          jQuery.ajax({
                              url: ajaxurl,
                              method: 'POST',
                              data: {
                                  action: 'njt_fb_import',
                                  site: $this.data('site'),
                                  count: $this.data('count'),
                                  nonce: njt_fb_nonce,
                              }
                          })
                          .done(function(res){
                              $this.removeClass('updating-message')
                              if(res.success) {
                                  var html_notice = '<div class="njt-success-notice notice notice-warning is-dismissible"><p>'+res.data.mess+'</p><button type="button" class="notice-dismiss" onClick="jQuery(\'.njt-success-notice\').remove()"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
                                  jQuery(html_notice).insertBefore('form#post');
                              }
                          })
                          .fail(function(res){
                              $this.removeClass('updating-message')
                              alert('Please try again later');
                          })
                        })
                    });
                </script>
            <?php endif; ?>
        </form>
        <?php
    }

    public function register_setting(){
        register_setting($this->option_group, $this->option_name, array($this, 'save_setting'));
    }

    public function save_setting()
    {
        $new_input = [];
        if (isset($_POST['unload-frontend'])) {
            $new_input['unload-frontend'] = true;
        }else{
            $new_input['unload-frontend'] = false;
        }
        if(is_super_admin()) {
          if (isset($_POST['foldersForEachUser'])) {
            $new_input['foldersForEachUser'] = true;
          } else {
            $new_input['foldersForEachUser'] = false;
          }
        }
        return $new_input;
    }
}
