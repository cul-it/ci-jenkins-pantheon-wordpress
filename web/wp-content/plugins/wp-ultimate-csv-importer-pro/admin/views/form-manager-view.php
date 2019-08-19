<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
include_once(SM_UCI_PRO_DIR . 'managers/class-uci-filemanager.php');
?>
<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
            <div class="col-md-6"><a href="https://goo.gl/jdPMW8" target="_blank"><?php echo esc_html__('Documentation','wp-ultimate-csv-importer-pro');?></a></div>
            <div class="col-md-6"><a href="https://goo.gl/fKvDxH" target="_blank"><?php echo esc_html__('Sample CSV','wp-ultimate-csv-importer-pro');?></a></div>
         </div>

<div class="whole_body wp_ultimate_csv_importer_pro" style="margin-top: 40px">
    <form>
        <div class="import_holder" id="import_holder" >
            <div class="panel " style="width: 99%;">
            <div class="panel-body no-padding">
            <div class="col-md-3 setting-manager-list file-manager-list no-padding" id="manager_left_sidebar">
                 <ul id="example">
                    <li id='1' class="bg-leftside selected" onclick="redirect_manager(this.id);">
                        <span class="icon-copy-file"></span>
                        <span><?php echo esc_html__('File Manager','wp-ultimate-csv-importer-pro');?></span>
                    </li>
                    <li id='2' class="bg-leftside" onclick="redirect_manager(this.id);">
                        <span class="icon-calendar3"></span>
                        <span><?php echo esc_html__('Smart Schedule','wp-ultimate-csv-importer-pro');?></span>
                    </li>
                     <li id='3' class="bg-leftside" onclick="redirect_manager(this.id);">
                         <span class="icon-calendar3"></span>
                         <span><?php echo esc_html__('Scheduled Export','wp-ultimate-csv-importer-pro');?></span>
                     </li>
                    <li id='4' class="bg-leftside" onclick="redirect_manager(this.id);">
                        <span class="icon-insert-template"></span>
                        <span><?php echo esc_html__('Templates','wp-ultimate-csv-importer-pro');?></span>
                    </li>
                    <li id="5" class="bg-leftside" onclick="redirect_manager(this.id);">
                        <span class="icon-document-diagrams"></span>
                        <span><?php echo esc_html__('Log Manager','wp-ultimate-csv-importer-pro');?></span>
                    </li>
                </ul>
            </div>
            <div class="col-md-9" id="manager_rightside_content">
                <?php $manager_type = isset($_REQUEST['step']) ? sanitize_title($_REQUEST['step']) : '';?>
               	<?php
                switch($manager_type){
                    case 'filemanager':
                        include (SM_UCI_PRO_DIR . 'admin/views/form-file-manager.php' );
                        break;
                    case 'schedulemanager':
                        include (SM_UCI_PRO_DIR . 'admin/views/form-schedule-manager.php');
                        break;
                    case 'scheduledexport':
                        include (SM_UCI_PRO_DIR . 'admin/views/form-scheduled-export.php');
                        break;
                    case 'templatemanager':
                        include (SM_UCI_PRO_DIR . 'admin/views/form-template-manager.php');
                        break;
                    case 'shortcodemanager':
                        include(SM_UCI_PRO_DIR . 'admin/views/form-shortcode-manager.php');
                        break;
                    case 'logmanager':
                        include(SM_UCI_PRO_DIR . 'admin/views/form-log-manager.php');
                        require_once(SM_UCI_PRO_DIR . 'managers/class-uci-logmanager.php');
                        break;
                    default:
                        include(SM_UCI_PRO_DIR . 'admin/views/form-file-manager.php');
                        break;
                }
                ?>
            </div>
            </div>
            </div>
        </div>
    </form>
</div>
<script>
    jQuery(function () {
        jQuery(".selected").addClass("right-arrow");
    });
</script>
<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com/?utm_source=wordpress&utm_medium=plugin&utm_campaign=pro_csv_importer" target="blank">Smackcoders</a>.</div>
