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
<div class="wp_ultimate_csv_importer_pro" style="margin-top: -20px;">
	<a id="file_download_url" href="" target="_blank" style="align:right;visibility:hidden;" >
            <?php echo esc_html__('Click Here','wp-ultimate-csv-importer-pro');?>
        </a>
        <h3 class="csv-importer-heading"><?php echo esc_html__('Events Summary','wp-ultimate-csv-importer-pro');?></h3>
    <div class="table-responsive" >
        <table class="table table-mapping table-manager table-fixed mt30 " >
            <thead>
            <tr>
                <th width="45%"><?php echo esc_html__('Event Info','wp-ultimate-csv-importer-pro');?></th>
                <th style="vertical-align:middle;" width="20%"><?php echo esc_html__('Event Date','wp-ultimate-csv-importer-pro');?></th>
                <th style="vertical-align:middle;" class="text-center" width="35%"><?php echo esc_html__('Actions','wp-ultimate-csv-importer-pro');?></th>
            </tr>
            </thead>
            <tbody id="style-1">
            <?php
            global $fileObj;
            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $url = explode('&',$url);
            $url = $url[0];
            $distinctEvents = $fileObj->fetchDistinctEvents();
            $i = 1;
            if(empty($distinctEvents)) { ?>
                <tr>
                    <td colspan="6" style="text-align: center; width: 100%;">
                        <div align ="center" width="50%" class="warning-msg">
                            <?php echo esc_html__('No events found','wp-ultimate-csv-importer-pro');?>
                        </div>
                    </td>
                </tr>
            <?php } elseif(!empty($distinctEvents)) {
                foreach($distinctEvents as $key => $eventData) {
                    $eventInformation = $fileObj->fetchDataOfDistinctEventsRevision( $eventData->original_file_name );
                    $event_id = array();
                    foreach ( $eventInformation as $eventIndex => $eventInfo ){
                        $file_revisions[ $eventInfo->revision ] = $eventInfo->filepath;
                        $event_id[ $eventInfo->revision ] = $eventInfo->id;
                        $eventId = $eventInfo->id;
                        $eventPurpose  = $eventInfo->import_type;
                        $eventKey      = $eventInfo->eventKey;
                        $insertedCount = $eventInfo->created;
                        $updatedCount  = $eventInfo->updated;
                        $skippedCount  = $eventInfo->skipped;
                        $eventHappened = $eventInfo->event_started_at;
                        $isDeleted     = $eventInfo->deleted;
                    } ?>
                    <tr>
                        <td width="45%"><p><b><?php echo esc_html__('File Name','wp-ultimate-csv-importer-pro');?> : </b><?php echo $eventInfo->original_file_name;?></p><p> <b> <?php echo esc_html__('Date','wp-ultimate-csv-importer-pro');?></b><span class="pl30" id="eventdate_<?php echo $eventId;?>"><b> : </b><?php echo $eventInfo->event_started_at;?></span></p><p><b><?php echo esc_html__('Purpose','wp-ultimate-csv-importer-pro');?><span class="pl15"> : </b> <?php echo $eventPurpose;?></span></p><p><b><?php echo esc_html__('Revision','wp-ultimate-csv-importer-pro');?><span class="pl15">: </b> <select id="record_<?php echo $eventId;?>"  onchange="selectrevision('<?php echo $eventId;?>', '<?php echo $eventInfo->original_file_name;?>');">
                                    <option><?php echo esc_html__('--Select--','wp-ultimate-csv-importer-pro');?></option>
                                    <?php foreach($event_id as $revisionId => $eventId){
                                        ?>
                                        <option value="<?php echo $eventId; ?>" ><?php echo $revisionId;?></option>
                                    <?php }?>
                                </select>
                                </span></p>
                        </td>
                        <td width="20%" style="vertical-align:middle; text-align: center;"><p><b><?php echo esc_html__('Inserted','wp-ultimate-csv-importer-pro');?> : </b><span id="inserted_<?php echo $eventId;?>" value=""><?php echo $eventInfo->created;?></span></p><p><b><?php echo esc_html__('Updated','wp-ultimate-csv-importer-pro');?> : </b><span id="updated_<?php echo $eventId;?>"> <?php echo $eventInfo->updated;?></span></p><p class = "file_summary"><b><?php echo esc_html__('Skipped','wp-ultimate-csv-importer-pro');?> :</b> <span id = "skipped_<?php echo $eventId;?>"><?php echo $eventInfo->skipped;?></span></p></td>
                        <td width="35%" style="vertical-align:middle !important; text-align: center;">
                            <div class="pt40">
                            <div class="filemanager-download-icon">
                                <span class="icon-cloud-download3" onclick ="downloadFile('<?php echo $eventId;?>' , '<?php echo $eventInfo->original_file_name;?>');" ></span><span class="filemanager-download-text">Download a specific file with revision</span></div>
                                <div class="filemanager-download-icon"><span class="icon-file-zip" onclick="downloadAll_files('<?php echo $eventId;?>');" ></span>
                                <span class="filemanager-download-text">Download all files as a zip</span></div>
                                <div class="filemanager-download-icon">
                                <span class="icon-delete text-danger" onclick="delete_file_event('<?php echo $eventId;?>','<?php echo $eventInfo->original_file_name;?>');" ></span>
                                <span class="filemanager-download-text">Delete a specific file with revision</span></div>
                                 <div class="filemanager-delete-all-icon">
                                <span class="icon-data text-danger" onclick="delete_record_event('<?php echo $eventId;?>','<?php echo $eventPurpose;?>','<?php echo $eventData->original_file_name;?>','<?php echo $eventPurpose;?>');" ></span><span class="filemanager-delete-all-text">Delete all records</span></div><div class="filemanager-download-icon">
                                <span class="icon-circle-cross text-danger" onclick="delete_all_event('<?php echo $eventId;?>','<?php echo $eventInfo->original_file_name;?>','<?php echo $eventPurpose;?>');" ></span><span class="filemanager-download-text">Delete all files & records</span></div>
                                <div class="filemanager-delete-all-icon">
                                <span class="icon-trash-can3" id="trash_<?php echo $eventId;?>" value="Trash" onclick="trash_records('<?php echo $eventId;?>','<?php echo $eventPurpose;?>','<?php echo $eventData->original_file_name;?>','trash')" ></span><span class="filemanager-delete-all-text">Trash all records</span></div>
                                <div class="filemanager-delete-all-icon">
                                <span class="icon-refresh-2" id="restore_<?php echo $eventId;?>" visibility="hidden" style="display:none" value="Restore" onclick="trash_records('<?php echo $eventId;?>','<?php echo $eventPurpose;?>','<?php echo $eventData->original_file_name;?>','publish')"></span><span class="filemanager-delete-all-text">Restore all records</span></div></div>
                        </td>
                    </tr>
                    <?php $i++;
                }
            } ?>
            </tbody>
        </table>
          </div>
</div>
<div class="clearfix"></div>
<script>
    jQuery(document).ready(function () {
        var i;
        for(i=1; i<6; i++) {
            jQuery('#'+i).addClass("bg-leftside");
            jQuery('#'+i).removeClass("selected");
        }
        jQuery('#1').addClass("selected");
        jQuery('#1').removeClass("bg-leftside");
    });
</script>
