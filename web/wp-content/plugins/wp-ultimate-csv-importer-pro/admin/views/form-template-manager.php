<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
global $wpdb;
$templateList = $wpdb->get_results("select * from wp_ultimate_csv_importer_mappingtemplate");
?>
<div class="wp_ultimate_csv_importer_pro">
    <h3 class="csv-importer-heading"><?php echo esc_html__('Template Info','wp-ultimate-csv-importer-pro');?></h3>
    <table class="table table-mapping table-fixed table-manager mt30">
        <thead>
        <tr>
            <th width="30%"><?php echo esc_html__('Template name','wp-ultimate-csv-importer-pro');?></th>
            <th width="25%"><?php echo esc_html__('Module','wp-ultimate-csv-importer-pro');?></th>
            <th width="25%"><?php echo esc_html__('Created Time','wp-ultimate-csv-importer-pro');?></th>
            <th width="20%" style="text-align: center;"><?php echo esc_html__('Actions','wp-ultimate-csv-importer-pro');?></th>
        </tr>
        </thead>
        <tbody >
        <?php if(!empty($templateList)) {
            foreach($templateList as $templatedata) { ?>
                <tr id="template<?php echo $templatedata->id;?>" onmouseover="show_fileEvents(<?php echo $templatedata->id;?>);" onmouseout="hide_fileEvents(<?php echo $templatedata->id;?>);">
                    <td width="30%" style="overflow-wrap: break-word; padding: 20px 10px;"><span class=""><?php echo $templatedata->templatename;?></span></td>
                    <td width="25%" style="padding: 20px 10px;"><?php echo $templatedata->module;?></td>
                    <td width="25%" style="padding: 20px 10px;"><?php echo $templatedata->createdtime;?></td>
                    <td width="20%" style="vertical-align: middle; text-align: center; padding: 20px 10px;" id="file_events1<?php echo $templatedata->id;?>">
                    <div class="download-icon">
                        <span><a href="<?php echo esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=mapping_config&eventkey=' . $templatedata->eventKey . '&action=edit'); ?>"><i class="icon-pencil2"></i></a></span><span class="download-text">Edit</span></div>
                        <div class="download-icon">
                        <span id="delete" onclick="delete_template(<?php echo $templatedata->id;?>);"><i class="icon-trash2"></i></span>
                        <span class="download-text">Delete</span></div>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="6" style="text-align: center; width: 100%;">
                    <div align ="center" width="50%" class="warning-msg">
                        <?php echo esc_html__('No templates found','wp-ultimate-csv-importer-pro');?>
                    </div>
                </td>
            </tr>
            <?php
        } ?>
        </tbody>
    </table>
</div>
<script>
    jQuery(document).ready(function () {
        var i;
        for(i=1; i<6; i++) {
            jQuery('#'+i).addClass("bg-leftside");
            jQuery('#'+i).removeClass("selected");
        }
        jQuery('#4').addClass("selected");
        jQuery('#4').removeClass("bg-leftside");
    });
</script>
