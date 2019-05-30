<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
?>
<div align="center">
    <div class = "title">
        <h3>Shortcodes Data</h3>
    </div>
    <div>
        <table class="manager_table">
            <tr style="width:100px">
                <th width="10%"><input type="checkbox" id="selectAllid" name="selectAllid"></th>
                <th width="48%"><h3 id="row-title">File Info</h3></th>
                <th width="26%"><h3 id="row-title">Image Info</h3></th>
                <th><h3 id="row-title">Status</h3></th>
            </tr>
        </table>
        <hr />
    </div>
    <div style="height:450px;overflow-y:scroll;">
        <table class="manager_table">
            <?php for($i = 0;$i<10;$i++){?>
                <tbody onmouseover = "show_fileEvents(<?php echo $i;?>);" onmouseout="hide_fileEvents(<?php echo $i;?>);">
                <tr>
                    <td><input type="checkbox" name = "selectAllid" id="selectAllid" /></td>
                    <td class="schedule-name"><?php echo esc_html__('File Name');?></td>
                    <td>:</td>
                    <td class = "schedule-filename"><?php echo esc_html__('post_csv.csv');?>post_csv.csv</td>
                    <td width = "16%"><?php echo esc_html__('Shortcode Mode');?></td>
                    <td>:</td>
                    <td><?php echo esc_html__('Inline');?></td>
                    <td><?php echo esc_html__('Replaced');?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><?php echo esc_html__('Event Key');?></td>
                    <td>:</td>
                    <td><?php echo esc_html__('ddr4553g8992');?></td>
                    <td><?php echo esc_html__('Module');?></td>
                    <td>:</td>
                    <td colspan="2"><?php echo esc_html__('Post');?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><?php echo esc_html__('Revision');?></td>
                    <td>:</td>
                    <td><?php echo esc_html__('1');?></td>
                    <td><?php echo esc_html__('No.of.shortcodes');?></td>
                    <td>:</td>
                    <td colspan="2">5</td>
                </tr>
                <tr id = "file_events<?php echo $i;?>" class="row-links">
                    <td></td>
                    <td colspan="6">
                        <?php echo esc_html__('Populate|Update');?>
                    </td>
                </tr>
                <tr>
                    <td colspan="8"><hr /></td>
                </tr>
                </tbody>
            <?php }?>
        </table>
    </div>
    </div>
<script>
    jQuery(document).ready(function () {
        var i;
        jQuery('#4').addClass("selected");
        jQuery('#4').removeClass("bg-leftside");
        for(i=1;i<=5;i++) {
            if(i == 4)
                continue;
            jQuery('#'+i).addClass("bg-leftside");
            jQuery('#'+i).removeClass("selected");
        }
    });
</script>
