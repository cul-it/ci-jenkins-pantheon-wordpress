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
<div align="center" class="wp_ultimate_csv_importer_pro" style="font-size: 13px;">
	<div class = "title">
		<h3 class="csv-importer-heading"><?php echo esc_html__('Log Info','wp-ultimate-csv-importer-pro');?></h3>
		<div style="">
			<?php
			global $fileObj;
			global $wpdb;
			$records = $wpdb->get_results("SELECT * FROM smackuci_events");
			?>
			<table class="table table-mapping table-fixed table-manager mt30">
				<thead>
				<tr class="" style="">
					<th width="27%"><?php echo esc_html__('FileName','wp-ultimate-csv-importer-pro');?></th>
					<th width="28%"><?php echo esc_html__('Module','wp-ultimate-csv-importer-pro');?></th>
					<th width="10%" style="overflow-wrap:break-word;"><?php echo esc_html__('Inserted','wp-ultimate-csv-importer-pro');?></th>
					<th width="10%" style="overflow-wrap:break-word;"><?php echo esc_html__('Updated','wp-ultimate-csv-importer-pro');?></th>
					<th width="10%" style="overflow-wrap:break-word;"><?php echo esc_html__('Skipped','wp-ultimate-csv-importer-pro');?></th>
					<th width="15%" style="overflow-wrap:break-word; text-align: center;"><?php echo esc_html__('Download','wp-ultimate-csv-importer-pro');?></th>
				</tr>
				</thead>
				<tbody>
				<?php if(!empty($records)) {
					foreach($records as $record_data) { ?>
						<tr style="padding: 20px 0 10px 0;">
							<td width="27%" style="overflow-wrap: break-word;"><?php echo $record_data->original_file_name;?>
								<br>
								<b><?php echo esc_html__('Revision:','wp-ultimate-csv-importer-pro');?> </b> <?php echo $record_data->revision; ?>
							</td>
							<td width="28%"><?php echo $record_data->import_type; ?></td>
							<td align="center" width="10%" style="text-align: center;"><?php echo $record_data->created; ?></td>
							<td align="center" width="10%" style="text-align: center;"><?php echo $record_data->updated; ?></td>
							<td align="center" width="10%" style="text-align: center;"><?php echo $record_data->skipped; ?></td>
							<td align="center" width="15%" class="row-links" style="text-align: center;">
								<?php
								$get_upload_url = wp_upload_dir();
								$eventkey =  $record_data->eventKey;
								$uploadLogURL = $get_upload_url['baseurl'] . '/smack_uci_uploads/imports/' . $eventkey . '/' . $eventkey;
								$logfilename = $uploadLogURL.".log";
								?>
								<div class="download-icon">
								<a href="<?php echo $logfilename; ?>" download id="dwnldlog" style="margin-left:5px;font-size:15px;">
									<span class="icon-cloud-download3"></span>
								</a><span class="download-text"><?php echo esc_html__('Download','wp-ultimate-csv-importer-pro');?></span>
</div>
							</td>
						</tr>
					<?php }
				} else { ?>
					<tr>
						<td colspan="6" style="text-align: center; width: 100%;">
							<div align ="center" width="50%" class="warning-msg">
									<?php echo esc_html__("You haven’t imported any files",'wp-ultimate-csv-importer-pro');?>
							</div>
						</td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function () {
		var i;
		for(i=1; i<6; i++) {
			jQuery('#'+i).addClass("bg-leftside");
			jQuery('#'+i).removeClass("selected");
		}
		jQuery('#5').addClass("selected");
		jQuery('#5').removeClass("bg-leftside");
	});
</script>
