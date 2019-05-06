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
if($_POST){
	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	$records['mapping_config'] = $_POST;
	$auto_save_template = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : '';
	$post_values = $uci_admin->GetPostValues(sanitize_key($_REQUEST['eventkey']));
	$result = array_merge($post_values[$_REQUEST['eventkey']], $records);
	$uci_admin->SetPostValues(sanitize_key($_REQUEST['eventkey']), $result);
	if($auto_save_template != '' && $auto_save_template == 'auto_save') {
		$templatename = isset( $_POST['templatename'] ) ? $_POST['templatename'] : '';
		if($templatename) {
			if(isset($_POST['smack_uci_mapping_method']) && $_POST['smack_uci_mapping_method'] == 'advanced') {
				$uci_admin->saveAdvancedTemplate( $uci_admin );
			} else {
				$uci_admin->saveTemplate( $uci_admin );
			}
		}
	} elseif($auto_save_template != '' && $auto_save_template == 'auto_update') {
		$templatename = isset( $_POST['templatename'] ) ? sanitize_text_field($_POST['templatename']) : '';
		if($templatename) {
			if(isset($_POST['smack_uci_mapping_method']) && $_POST['smack_uci_mapping_method'] == 'advanced') {
				$uci_admin->saveAdvancedTemplate( $uci_admin );
			} else {
				$uci_admin->saveTemplate( $uci_admin );
			}
		}
	}
}
if(isset($_REQUEST['istemplate']) && sanitize_title($_REQUEST['istemplate']) == 'no') {
	$actionURL = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=import_config&istemplate=no&eventkey='.$_REQUEST['eventkey']);
	$backlink = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=mapping_config&istemplate=no&eventkey='.$_REQUEST['eventkey']);
} else {
	$actionURL = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=import_config&eventkey='.$_REQUEST['eventkey']);
	$backlink = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=mapping_config&eventkey='.$_REQUEST['eventkey']);
}
if(isset($_REQUEST['templateid'])) {
	$actionURL .= '&templateid=' . intval($_REQUEST['templateid']);
	$backlink .= '&templateid=' . intval($_REQUEST['templateid']);
}
$records =  $uci_admin->GetPostValues(sanitize_key($_REQUEST['eventkey']));
$post_values = $uci_admin->GetPostValues(sanitize_key($_REQUEST['eventkey']));
if( isset($post_values[$_REQUEST['eventkey']]['mapping_config']['smack_uci_mapping_method']) && $post_values[$_REQUEST['eventkey']]['mapping_config']['smack_uci_mapping_method'] == 'advanced'){
	$backlink .= '&mapping_type=advanced';
}
if( isset($post_values[$_REQUEST['eventkey']]['import_file']['file_extension']) && $post_values[$_REQUEST['eventkey']]['import_file']['file_extension'] == 'xml'){
	$tag = $post_values[$_REQUEST['eventkey']]['mapping_config']['xml_tag_name'];
	$backlink .= '&tag_name=' .$tag;
}
if (isset($post_values[$_REQUEST['eventkey']]['mapping_config']['tree_type'])) {
	$tree_type = $post_values[$_REQUEST['eventkey']]['mapping_config']['tree_type'];
	$backlink .= '&tree_type=' .$tree_type;
}

// echo "<pre>";
// print_r($post_values);
// die('f');
?>
<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
            <div class="col-md-6"><a href="https://goo.gl/jdPMW8" target="_blank"><?php echo esc_html__('Documentation','wp-ultimate-csv-importer-pro');?></a></div>
            <div class="col-md-6"><a href="https://goo.gl/fKvDxH" target="_blank"><?php echo esc_html__('Sample CSV','wp-ultimate-csv-importer-pro');?></a></div>
         </div>
<div class="template_body whole_body wp_ultimate_csv_importer_pro" style="margin-top: 40px;">
	<form class="form-inline" method="post" action="<?php echo $actionURL;?>">
		<div id='wp_warning' style = 'display:none;' class = 'error'></div>
		<h3 class="media_head csv-importer-heading"><?php echo esc_html__('Media Handling','wp-ultimate-csv-importer-pro');?></h3>
		<input type="hidden" id="eventkey" name="eventkey" value="<?php echo sanitize_text_field($_REQUEST['eventkey']); ?>" />
	<!-- table div start -->	<div class="media_data">
			<!-- tr div start <div>-->

<div class="col-md-12">	
	<fieldset class="scheduler-border"> <legend class="scheduler-border" style="margin-top:20px;"><?php echo esc_html__('Image Handling','wp-ultimate-csv-importer-pro');?></legend>	
		<!--	</div> tr div end -->
			<!-- tr div start  <div>-->
<div class="col-md-12">
				<div  class="col-xs-0 col-xs-offset-0 col-sm-7 col-md-6 col-md-offset-1"><label class="external_img_label"><?php echo esc_html__('Download external images to your media','wp-ultimate-csv-importer-pro');?></label></div>
				<div id='divtd' class="col-xs-6  col-sm-3 col-md-3 col-md-offset-0 col-xs-offset-1">

				<!-- first button -->
<input id="image-handling-btn" type='checkbox' class="tgl tgl-skewed noicheck" name='download_img_tag_src' id='download_imgon' value='on' checked="checked"  style="display:none" onclick="saveoptions(this.id, this.name);" /> 
<label data-tg-off="OFF" data-tg-on="ON" for="image-handling-btn"  id="download_on" class="tgl-btn" >
 </label>
<!-- first btn -->
				</div></div> 
				<div  class="col-md-12 mb20" id="image-handling-btn-opt">

					<div class="col-md-offset-2 col-sm-offset-1"><label class="external_img_label"><input type="radio"  name="imageprocess" id="use_existing_images" value="use_existing_images" onclick="displayselect(this.id);"><?php echo esc_html__('Use media images if already available','wp-ultimate-csv-importer-pro');?></label></div>
					<div class="col-md-offset-2 col-sm-offset-1"><label class="external_img_label"><input type="radio"  name="imageprocess" id="overwrite_existing_images" value="overwrite_existing_images" onclick="displayselect(this.id);"><?php echo esc_html__('Do you want to overwrite the existing images','wp-ultimate-csv-importer-pro');?></label></div>
				</div>
		<?php if(in_array('nextgen-gallery/nggallery.php', $uci_admin->getActivePlugins())) { ?>
			<div class="col-md-12 mt20">
				<div  class="col-xs-0 col-xs-offset-0 col-sm-7 col-md-6 col-md-offset-1">
					<label class="external_img_label"><?php echo esc_html__('NextGEN Gallery support on featured image', 'wp-ultimate-csv-importer-pro')?></label>
				</div>
				<div id='divtd' class="col-xs-6 col-sm-3 col-xs-offset-1 col-md-3 col-md-offset-0">
<!-- second  button code -->
<input id="gallery-support-btn" type='checkbox' class="tgl tgl-skewed noicheck" name='nextgen_featured_image' value='on' style="display:none" onclick="saveoptions(this.id, this.name);" />
<label data-tg-off="OFF" data-tg-on="ON" for="gallery-support-btn"  id="download_on" class="tgl-btn" >
 </label>
			</div>
			</div>  <!-- container div close </div>-->
		<?php } ?>
</fieldset></div>
	<div class="col-md-12">	
	<fieldset class="scheduler-border"> <legend class="scheduler-border" style="margin-top:20px;"><?php echo esc_html__('Image Sizes','wp-ultimate-csv-importer-pro');?></legend>	
				<div class="row">
					<div class="col-xs-6 col-xs-offset-0 col-sm-3 col-sm-offset-1 col-md-2 col-md-offset-1"><label class="wp_img_size"><input type="checkbox" name="media_thumbnail_size" value="thumbnail" ><?php echo esc_html__('Thumbnail','wp-ultimate-csv-importer-pro');?></label></div>
					<div class="col-xs-6 col-xs-offset-0 col-sm-3 col-sm-offset-0 col-md-2 col-md-offset-0"><label class="wp_img_size"><input type="checkbox" name="media_medium_size" value="medium" checked="checked"><?php echo esc_html__('Medium','wp-ultimate-csv-importer-pro');?></label></div>
					<div class="col-xs-6 col-sm-3 col-sm-offset-0 col-md-3 col-md-offset-0"><label class="wp_img_size"><input type="checkbox" name="media_medium_large_size" value="mediumlarge"><?php echo esc_html__('Medium Large','wp-ultimate-csv-importer-pro');?></label></div>
					<div class="col-xs-6 col-sm-3 col-sm-offset-0 col-md-2"><label class="wp_img_size"><input type="checkbox" name="media_large_size" value="large"><?php echo esc_html__('Large','wp-ultimate-csv-importer-pro');?></label></div>
				<div class="col-xs-6 col-sm-3 col-sm-offset-0 col-md-2"><label class="wp_img_size"><input type="checkbox" name="media_custom_size" value ="custom" id="media_custom_size"><?php echo esc_html__('Custom','wp-ultimate-csv-importer-pro');?></label></div>
				</div>
			<!--<div> tr div end -->
<!-- add custom  -->
<div class="col-md-12">
<div class="col-md-offset-1 mt20">
<table class="table">
<thead>
</thead>
<tbody id="TextBoxContainer">
</tbody>
<tfoot>
  <tr>
    <th colspan="2">
    <p id="custom-size-add"  class="smack-btn smack-btn-info" style="cursor: pointer;color:#008080;font-size:15px;"><i class="icon-circle-plus"></i>&nbsp;<?php echo esc_html__('Add custom sizes','wp-ultimate-csv-importer-pro');?></p></th>
  </tr>
</tfoot>
</table>
</div></div>

				</fieldset></div>
		<!--	</div> tr div end -->
		<!-- image hole div end -->
			<!-- advanced media option start -->
			<!-- tr div start <div>-->
				<div class="col-md-12"><fieldset class="scheduler-border"> <legend class="scheduler-border" style="margin-top:20px;"><?php echo esc_html__('Media SEO & Advanced Options','wp-ultimate-csv-importer-pro');?></legend>
		<!--	</div> tr div end -->
			<!-- tr div start <div>-->
				<div class="col-md-12">
					<div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-1"><label class="external_img_label"><input type="checkbox" name="media_seo_title" id="media_seo_title" data-key="title" class="media_seo" onclick="enable_media_seo_headers('title');"/><?php echo esc_html__('Set image Title:','wp-ultimate-csv-importer-pro');?></label></div>
						<div class="col-xs-12 col-sm-6 col-md-3 mb10"><?php print( $uci_admin->getCSVHeader('title') ); ?></div>
				</div>
		<!--	</div> tr div end -->
			<!-- tr div start  <div>-->
				<div class="col-md-12">
				<div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-1">
					<label class="external_img_label"><input type="checkbox" name="media_seo_caption" id="media_seo_caption" data-key="caption" class="media_seo" onclick="enable_media_seo_headers('caption');"/><?php echo esc_html__('Set image Caption:','wp-ultimate-csv-importer-pro');?></label></div>
					<div class="col-xs-12 col-sm-6 col-md-3 mb10 "><?php print( $uci_admin->getCSVHeader('caption') ); ?></div>
				</div>
		<!--	</div> tr div end -->
		<!--	<div> tr div start -->
				<div class="col-md-12">
				<div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-1">
					<label class="external_img_label"><input type="checkbox" name="media_seo_alttext" id="media_seo_alttext" data-key="alttext" class="media_seo" onclick="enable_media_seo_headers('alttext');"/><?php echo esc_html__('Set image Alt Text:','wp-ultimate-csv-importer-pro');?></label></div>
					<div class="col-xs-12 col-sm-6 col-md-3 mb10"><?php print( $uci_admin->getCSVHeader('alttext') ); ?></div>
				</div>
		<!--	</div> tr div end -->
		<!-- tr div start <div>-->	
				<div class="col-md-12">
					<div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-1"><label class="external_img_label"><input type="checkbox" name="media_seo_description" data-key="description" class="media_seo" id="media_seo_description" onclick="enable_media_seo_headers('description');"/><?php echo esc_html__('Set image Description:','wp-ultimate-csv-importer-pro');?></label></div>
				<div class="col-xs-12 col-sm-6 col-md-3 mb10">	<?php print( $uci_admin->getCSVHeader('description') ); ?></div>
				</div>
		<!--	</div> tr div end -->
		<!--	<div> tr div start -->
				<div  class="col-md-12">
					<div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-1"><label class="external_img_label"><input type="checkbox" name="change_media_file_name" class="media_seo" data-key="imageName" id="change_media_file_name" onclick="enable_media_seo_headers('imageName');"><?php echo esc_html__('Change image file name to:','wp-ultimate-csv-importer-pro');?></label></div>
					<div class="col-xs-12 col-sm-6 col-md-3 mb10"><?php print( $uci_admin->getCSVHeader('imageName') ); ?></div>
				</div>
		<!--	</div> tr div end -->
</fieldset></div>
<!-- advanced media option end -->
	<!-- table div end -->	</div>
<div class="clearfix"></div>
		<div class="col-md-12 mt15 mb15">
			<div class="pull-left">
			<a class="smack-btn btn-default btn-radius" href="<?php echo $backlink;?>"><?php echo esc_html__('Back','wp-ultimate-csv-importer-pro');?>
			</a></div>
<div class="pull-right">
			<input type="submit" class="smack-btn smack-btn-primary btn-radius" value="<?php echo esc_attr__('Continue','wp-ultimate-csv-importer-pro');?>"></div></div><div class="clearfix"></div>
<div class="mb20"></div>
	</form>
</div>
<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com/?utm_source=wordpress&utm_medium=plugin&utm_campaign=pro_csv_importer" target="blank">Smackcoders</a>.</div>
