<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
global $uci_admin;
global $scheduleObj;
if($_POST) {
	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	$records['media_handling'] =$_POST;
	$get_records = $uci_admin->GetPostValues(sanitize_key($_REQUEST['eventkey']));
	$result = array_merge($get_records[$_REQUEST['eventkey']], $records);
	$uci_admin->SetPostValues(sanitize_key($_REQUEST['eventkey']), $result);
}
$post_values = $uci_admin->GetPostValues(sanitize_key($_REQUEST['eventkey']));
$eventkey = sanitize_title($_REQUEST['eventkey']);
$import_mode = '';
if($post_values[$eventkey]['import_file']['import_mode'] == 'existing_items') {
	$import_mode = "checked = 'checked'";
}
$import_type = $post_values[$eventkey]['import_file']['posttype'];
$importAs = '';
$server_request = $uci_admin->serverReq_data();
$file = SM_UCI_IMPORT_DIR . '/' . $eventkey . '/' . $eventkey;
$parserObj->parseCSV($file, 0, -1);
$total_row_count = $parserObj->total_row_cont - 1;
$actionURL = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=confirm&eventkey='.$_REQUEST['eventkey']);
$backlink = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=media_config&eventkey='.$_REQUEST['eventkey']);
if($import_mode != '') {
	$duplicate_text = 'Do you update on existing records ?';
	$duplicate_subtext = 'Update records based on';
}else{

	$duplicate_text = 'Do you want to handle the duplicate on existing records ?';
	$duplicate_subtext = 'Mention the fields which you want to handle duplicates';
}
if(isset($_REQUEST['templateid'])) {
         $actionURL .= '&templateid=' . intval($_REQUEST['templateid']);
	 $backlink .= '&templateid=' . intval($_REQUEST['templateid']);
}
$ucisettings = get_option('sm_uci_pro_settings');
$main_mode = isset($ucisettings['enable_main_mode']) ? $ucisettings['enable_main_mode'] : '';
if($main_mode == 'on'){
	$config_checkbox = "checked = 'checked'";
}
else{
 $config_checkbox = "";
}
?>
<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
            <div class="col-md-6"><a href="https://goo.gl/jdPMW8" target="_blank"><?php echo esc_html__('Documentation','wp-ultimate-csv-importer-pro');?></a></div>
            <div class="col-md-6"><a href="https://goo.gl/fKvDxH" target="_blank"><?php echo esc_html__('Sample CSV','wp-ultimate-csv-importer-pro');?></a></div>
         </div>
<div class="template_body whole_body wp_ultimate_csv_importer_pro" style="font-size: 14px; margin-top: 40px;">
	<h3 style="margin-left:2%;" class="csv-importer-heading"><?php echo esc_html__('Import configuration Section','wp-ultimate-csv-importer-pro');?></h3>
	<form class="form-inline" method="post" action="<?php echo $actionURL;?>">
		<div id='wp_warning' style = 'display:none;' class = 'error'></div>
		<!-- table --><div class="config_table">
			<div class="col-md-12 mt20" id="rollback_ch">
				<div class="col-md-12 mb15">
					<label><input type = "checkbox"  class="import_config_checkbox" name = "rollback_mode_config" id = "rollback_mode_config"><?php echo esc_html__('Enable safe prestate Rollback(Backup before import)');?></label></div>
			</div>
			<div class="col-md-12 mt20" id="main_ch">
				<div class="col-md-12 mb15">
					<label><input type = "checkbox"  class="import_config_checkbox" name = "main_mode_config" id = "main_mode_config" <?php echo $config_checkbox; ?> ><?php echo esc_html__('Do you want to SWITCH ON Maintenance mode while import ?');?></label></div>
			</div>
			<?php
			$duplicate_option = array('Users','CustomerReviews','Tags','Categories','Comments');
			if(!in_array($import_type,$duplicate_option)) { ?>
			<div class="col-md-12 mt20">
				<div class="col-md-12 mb15">
					<label style="display:inline;">
						<input type = "checkbox" name="duplicate" id="duplicate" class="import_config_checkbox" onclick = "toggle_configdetails(this.id);" /><?php echo esc_html__($duplicate_text,'wp-ultimate-csv-importer-pro');?></label></div>
			</div>
			<?php } ?>
			<div id="duplicate_headers" class="mb40" style="display:none;">
				<div class="col-md-12 mb15">
					<div class = "col-md-6 col-md-offset-1 col-sm-7 col-sm-offset-1">
					    <label>
						    <?php echo esc_html__($duplicate_subtext,'wp-ultimate-csv-importer-pro');?>
					    </label></div>
					    <div class="col-xs-offset-3 col-xs-0">
					     <select class="dropdown-search-multiple selectpicker" name="duplicate_conditions[]" id="duplicate_conditions" disabled>
					     <?php
					     //$fields = $uci_admin->get_widget_fields('Core Fields', $post_values[$eventkey]['import_file']['posttype'],$importAs);
					     $fields = $uci_admin->get_update_fields($post_values[$eventkey]['import_file']['posttype'],$post_values[$eventkey]['import_file']['import_mode']);
					     foreach( $fields as $wp_fieldLabel => $wp_fieldarray){ ?>
						     <option value="<?php echo esc_html($wp_fieldarray);?>">
							     <?php echo esc_html($wp_fieldarray);?>
						     </option>
					     <?php } ?>
					     </select></div>
				</div>
			</div>
			<!-- Schedule Configuration -->
			<div class="col-md-12 mt20">
				<div class="col-md-12 mb15">
					<label><input type = "checkbox" class="import_config_checkbox" name = "schedule" id = "schedule" onclick = "toggle_configdetails(this.id);"><?php echo esc_html__('Do you want to Schedule this Import ?');?></label></div>
			</div>
			<div id="schedule_import"  style = "display:none;">
				<div class="col-md-12 ">
					<?php
					$scheduleObj->generatescheduleView();
					?>
				</div>
			</div>
		</div>
		<input type="hidden" id="eventkey" value="<?php echo sanitize_key($_REQUEST['eventkey']);?>">
		<input type="hidden" id="import_type" value="<?php echo $import_type;?>">
	<div class="clearfix"></div>	<div class="col-md-12 mt40">	

		<div class="pull-left">
		<a class="smack-btn btn-default btn-radius" href="<?php echo $backlink;?>"><?php echo esc_html__('Back','wp-ultimate-csv-importer-pro');?>
                        </a></div>
 	 	
 	 	<div class="pull-right mb20" style="margin-top: -10px;">
 	 	<input type="submit" class="smack-btn smack-btn-primary btn-radius" id="ignite_import" name="ignite_import" value="<?php echo esc_attr__('Import','wp-ultimate-csv-importer-pro');?>" onsubmit="schedule_rightnow();">
		<input style="display:none" type="button" class="smack-btn smack-btn-primary btn-radius" id="schedule_import_btn" name="schedule_import" value="<?php echo esc_attr__('Schedule','wp-ultimate-csv-importer-pro');?>" onclick="igniteSchedule();"></div>
</div><div class="clearfix"></div>


	</form>
</div>

<?php if($import_mode != '') { ?>
	<script type="application/javascript">
		jQuery('#duplicate').click();
	</script>
<?php } ?>
<script>
jQuery(function(){

	jQuery('#datetoschedule').datepicker({
		format: 'yyyy-mm-dd',
	});
	jQuery('#schedule')
	    .on('ifChecked', function(event) {
		jQuery('#main_ch').hide();   
		jQuery('#rollback_ch').hide();
	})
	.on('ifUnchecked', function() {
		jQuery('#main_ch').show();
		jQuery('#rollback_ch').show();
        });
});
</script>
<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com/?utm_source=wordpress&utm_medium=plugin&utm_campaign=pro_csv_importer" target="blank">Smackcoders</a>.</div>
