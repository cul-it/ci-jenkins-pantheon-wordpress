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

<div class="wp_ultimate_csv_importer_pro bigform-content">
    <h3 class="csv-importer-heading"><?php echo esc_html__('Schedule Info','wp-ultimate-csv-importer-pro');?></h3>
    <table class="table table-mapping table-fixed table-manager mt30 ">
        <thead>
        <tr>
            <th width="35%"><?php echo esc_html__('Event Info','wp-ultimate-csv-importer-pro');?></th>
            <th width="35%"><?php echo esc_html__('Event Date','wp-ultimate-csv-importer-pro');?></th>
            <th width="15%" style="overflow-wrap:break-word;"><?php echo esc_html__('Event Status','wp-ultimate-csv-importer-pro');?></th>
            <th width="15%" style="text-align: center;"><?php echo esc_html__('Actions','wp-ultimate-csv-importer-pro');?></th>
        </tr>
        </thead>
        <tbody>
        <?php global $scheduleObj;
        $scheduleList = $scheduleObj->get_scheduleData();
        $rowcount = 0;
        if(!empty($scheduleList)) {
            foreach($scheduleList as $schedule_data) { ?>
                <tr style="padding: 20px 0 10px 0 !important;" id="schedule<?php echo $schedule_data->id;?>" onmouseover="show_fileEvents(<?php echo $schedule_data->id;?>);" onmouseout="hide_fileEvents(<?php echo $schedule_data->id;?>);">
                    <td width="35%" style="overflow-wrap: break-word; font-size: 13px;">
                        <b><?php echo esc_html__('File Name','wp-ultimate-csv-importer-pro'); ?> : </b> <?php echo $schedule_data->csvname; ?><br><br>
                        <b><?php echo esc_html__('Template Name','wp-ultimate-csv-importer-pro'); ?> : </b> <?php echo $schedule_data->module; ?>
                    </td>
                    <td width="35%" style="font-size: 13px;">
                        <b><?php echo esc_html__('Scheduled Date','wp-ultimate-csv-importer-pro'); ?> : </b><?php echo $schedule_data->scheduleddate;?><br><br>
                        <b><?php echo esc_html__('Scheduled Time','wp-ultimate-csv-importer-pro'); ?> : </b> <?php echo $schedule_data->scheduledtimetorun;?>
                    </td>
                    <td width="15%">
                        <p><?php echo $schedule_data->cron_status;?></p>
                    </td>
                    <td width="15%" colspan="6" style="text-align: center">
                        <p><div class="download-icon"><span id="edit<?php echo $schedule_data->id; ?>" class="submit-button" style="color: #337ab7" onclick="show_schedule(<?php echo $schedule_data->id; ?>);"><i class="icon-pencil2"></i></span><span class="download-text">Edit</span></div>
                            <div class="download-icon"><span id="delete" onclick="delete_schedule(<?php echo $schedule_data->id;?>, 'scheduled_import');"><i class="icon-trash2"></i></span><span class="download-text">Delete</span></div></p>
                    </td>
                </tr>
                </tr>
                <?php $rowcount++;
            }
        } else { ?>
            <tr>
                <td colspan="6" style="text-align: center; width: 100%;">
                    <div align ="center" width="50%" class="warning-msg">
                        <?php echo esc_html__("You haven’t scheduled any event",'wp-ultimate-csv-importer-pro');?>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <!-- Testing -->
    <div class="modal animated zoomIn col-md-4 col-md-offset-2" id="dialog_confirm_map" tabindex="-1" role="dialog" aria-labelledby="dialog_confirm_mapLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group" style="width: 55%;">
                            <label class="pull-left"><?php echo esc_html__('Date','wp-ultimate-csv-importer-pro');?></label>
                            <input type='text' name='datetoschedule' style="" id='datetoschedule' readonly="readonly" class="form-control hasDatepicker">
                        </div>
                        <div class="form-group">
                            <label><?php echo esc_html__('Time','wp-ultimate-csv-importer-pro');?></label><br>
                            <select name='timetoschedule' id='timetoschedule' class="select_box config_select search_dropdown_mapping selectpicker">
                                <?php for ($hours = 0; $hours < 24; $hours++) {
                                    for ($mins = 0; $mins < 60; $mins += 30) {
                                        $datetime = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);?>
                                        <option value='<?php echo $datetime;?>'> <?php echo $datetime;?> </option>";
                                    <?php }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo esc_html__('Frequency','wp-ultimate-csv-importer-pro');?></label><br>
                            <select name='schedule_frequency' id='schedule_frequency' class="select_box config_select search_dropdown_mapping selectpicker">
                                <option value="0"><?php echo esc_html__('OneTime','wp-ultimate-csv-importer-pro');?></option>
                                <option value="1"><?php echo esc_html__('Daily','wp-ultimate-csv-importer-pro');?></option>
                                <option value="2"><?php echo esc_html__('Weekly','wp-ultimate-csv-importer-pro');?></option>
                                <option value="3"><?php echo esc_html__('Monthly','wp-ultimate-csv-importer-pro');?></option>
                                <option value="4"><?php echo esc_html__('Hourly','wp-ultimate-csv-importer-pro');?></option>
                                <option value="5"><?php echo esc_html__('Every 30 mins','wp-ultimate-csv-importer-pro');?></option>
                                <option value="6"><?php echo esc_html__('Every 15 mins','wp-ultimate-csv-importer-pro');?></option>
                                <option value="7"><?php echo esc_html__('Every 10 mins','wp-ultimate-csv-importer-pro');?></option>
                                <option value="8"><?php echo esc_html__('Every 5 mins','wp-ultimate-csv-importer-pro');?></option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="event_id" id="event_id" value="">
                            <input type="hidden" name="type" id="type" value="scheduled_import">
                            <span class="pull-right"><button type="button" class="smack-btn smack-btn-primary btn-radius" data-dismiss="modal" onclick="edit_schedule();"><?php echo esc_html__('Yes, I am sure','wp-ultimate-csv-importer-pro');?></button></span>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Testing End -->
</div>
<script>
    jQuery(document).ready(function () {
        var i;
        for(i=1; i<6; i++) {
            jQuery('#'+i).addClass("bg-leftside");
            jQuery('#'+i).removeClass("selected");
        }
        jQuery('#2').addClass("selected");
        jQuery('#2').removeClass("bg-leftside");
        var rowCount = jQuery('#schedule_table tr').length;
        var i;
        for(i =0;i<rowCount;i++) {
            jQuery('#datetoschedule').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }
    });

    jQuery(function () {
        jQuery("body").delegate("#datetoschedule", "focusin", function(){
            jQuery(this).datepicker({
                format: 'yyyy-mm-dd',
            });
        });
        //getting click event to show modal
        jQuery('.submit-button').click(function (e) {
            console.log(e);
            var get_event_id = e.currentTarget['id'];
            var event_id = get_event_id.split('edit');
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                async: false,
                dataType: 'json',
                data: {
                    'action': 'get_schedule_event_info',
                    'id' : event_id[1],
                },
                success: function (data) {
                    jQuery("#datetoschedule").val(data.scheduledDate);
                    jQuery("#timetoschedule > [value='"+ data.scheduledTime +"']").attr("selected", "selected");
                    jQuery("#schedule_frequency  > [value='"+ data.frequency +"']").attr("selected", "selected");
                    jQuery('.selectpicker').selectpicker('refresh');
                    jQuery('#event_id').val(data.event_id);
                    jQuery('#dialog_confirm_map').modal();
                    //appending modal background inside the bigform-content
                    jQuery('.modal-backdrop').appendTo('.bigform-content');
                    //removing body classes to able click events
                    jQuery('body').removeClass();
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        });

        //just to prove actions outside modal
        jQuery('#help-button').click(function () {
            alert("Action with modal opened or closed");
        });
        //end just to prove actions outside modal
    });
</script>
