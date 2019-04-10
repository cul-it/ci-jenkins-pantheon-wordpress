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
global $wpdb;
$filter_condition = '';
if($_POST && isset($_POST['from-date'])) {
 $filter_condition = $uci_admin->filter_template($_REQUEST);
}
else {
 $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
 $records['import_file'] = $_POST;
 if ($_POST) {
  $uci_admin->SetPostValues(sanitize_key($_REQUEST['eventkey']), $records);
 }
}
$uploaded_name = sanitize_text_field($_POST['uploaded_name']);
$post_values = $uci_admin->GetPostValues(sanitize_key($_REQUEST['eventkey']));
if (isset($post_values[$_REQUEST['eventkey']]['import_file']['file_extension']) && $post_values[$_REQUEST['eventkey']]['import_file']['file_extension'] == 'xml') {
 $newmap = '&mapping_type=advanced';
}
else{
  $newmap = '';
}
$uploadedname = isset($_POST['uploaded_name']) ? sanitize_text_field($_POST['uploaded_name']) : $post_values[sanitize_title($_REQUEST['eventkey'])]['import_file']['uploaded_name'];
?>
<div id="priority_template" class="panel wp_ultimate_csv_importer_pro" style="width: 99%; margin-top: 40px;">
 <form class="form-inline" id = "suggested_template" action="<?php echo esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=suggested_template&eventkey=' . $_REQUEST['eventkey']);?>&mapping_type=advanced" method="post">
  <div class="col-md-10 col-md-offset-1 mt20" >
   <table class="table">
    <tr>
	     <input type="hidden" id="filename" value="<?php echo $uploadedname; ?>">
     <td><input id="search" class="form-control" type="text" placeholder="<?php echo esc_html__('Search','wp-ultimate-csv-importer-pro');?>" name="search" style="width:92%;"></td>
     <td width="25%"><input type="text" id="from-date" name = "from-date" class="align form-control" readonly="readonly" placeholder="<?php echo esc_attr__('From Date','wp-ultimate-csv-importer-pro');?>"></td>
     <td width="25%"><input type="text" id="to-date" name = "to-date" class="align form-control" placeholder="<?php echo esc_attr__('To Date','wp-ultimate-csv-importer-pro');?>"></td><td><input type="button" readonly="readonly" class="smack-btn smack-btn-primary btn-radius btn-noradius" id = "filter" value="<?php echo esc_attr__('Go','wp-ultimate-csv-importer-pro');?>" onclick="filter_template();"> </td>
    </tr>
   </table>
  </div>
  <div class="clearfix"></div>

  <div class="panel-group paneldiv_suggestedtemplate">
   <div class='panel panel-info'>
    <div class='panel-heading'>
     <div class="panel-title font-nunito"><b> <?php echo esc_html__('Saved Templates','wp-ultimate-csv-importer-pro');?></b>
     </div>
    </div>
    <?php
    $template_order = $uci_admin->setPriority($post_values[sanitize_title($_REQUEST['eventkey'])]['import_file']['uploaded_name'],$post_values[sanitize_title($_REQUEST['eventkey'])]['import_file']['file_version'],$uci_admin);
    $count = 1;
    if(!empty($template_order)) {
     ?>
     <input type="hidden" id = "template_limit" value="6">
     <input type="hidden" id = "template_offset" value="0">
     <input type="hidden" id = "template_row_count" value="0">

     <table class="table table-mapping font_suggestedtemp" id = "templates">
      <tr>
       <th style="text-align:center;"> <?php echo esc_html__('Templates','wp-ultimate-csv-importer-pro');?></th>
       <th style="text-align:center;"> <?php echo esc_html__('Matched Columns Count','wp-ultimate-csv-importer-pro');?></th>
       <th style="text-align:center;"><?php echo esc_html__('Module','wp-ultimate-csv-importer-pro');?> </th>
       <th style="text-align:center;"><?php echo esc_html__('Created Time','wp-ultimate-csv-importer-pro');?></th>
       <th style="text-align:center;"></th>
       <!--<th width="22%" style="text-align:center;"> <?php echo esc_html__('Action','wp-ultimate-csv-importer-pro');?></th>-->
      </tr>
      <?php foreach($template_order as $templatename => $templateCount){
       $template_data = $wpdb->get_results($wpdb->prepare("select id, module, createdtime, mapping, mapping_type from wp_ultimate_csv_importer_mappingtemplate where templatename = %s", $templatename));

       $unser_data = unserialize($template_data[0]->mapping);
       if (isset($unser_data) && $unser_data['XMLTAGNAME']) {
         $tagname = '&tag_name='.$unser_data['XMLTAGNAME'];
       }
       else{
          $tagname = '';
       }
       
       ?>
       <tr>
        <!--<td align="right"><?php echo $count;?></td>-->
        <td align="center"><?php echo $templatename;?></td>
        <td align="center"><?php echo $templateCount;?></td>
        <td align="center"><?php echo $template_data[0]->module;?></td>
        <td align="center"><?php echo $template_data[0]->createdtime;?></td>
        <td align="center">
         <?php
         if($template_data[0]->mapping_type == '') {
          $mapping_type = 'normal';
         } else {
          $mapping_type = $template_data[0]->mapping_type;
         } ?>
         <a href="<?php echo esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=mapping_config&eventkey='.$_REQUEST['eventkey'].'&templateid='.$template_data[0]->id); ?>&mapping_type=<?php echo $mapping_type.$tagname; ?>" class="smack-btn smack-btn-info"><?php echo esc_html__('Use Template','wp-ultimate-csv-importer-pro');?></a>
         </td>
       </tr>
       <?php
       $count++;
      }?>
     </table>
    <?php } else { ?>
     <p class = "empty_template"><?php echo esc_html__("No Templates Found","wp-ultimate-csv-importer-pro");?></p>
    <?php }?>
   </div>
  </div>
  <!--Back Button -->
<div class="clearfix"></div>
  <div class="col-md-12 mt20">
  <div class="pull-left">
   <a href="<?php echo esc_url(admin_url() . 'admin.php?page=sm-uci-import'); ?>" class="smack-btn btn-default btn-radius">Back
   <!--<?php echo esc_html__('Back','wp-ultimate-csv-importer-pro');?>--></a></div>

   <div class="pull-right mb20"><a href="<?php echo esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=mapping_config&eventkey='.sanitize_title($_REQUEST['eventkey'])); ?><?php echo $newmap; ?>" class="smack-btn smack-btn-primary btn-radius"><?php echo esc_html__('Create new Mapping','wp-ultimate-csv-importer-pro');?></a></div>
   </div>

 <div class="clearfix"></div>
   </form>
</div>
<script>
 jQuery(document).ready(function () {
  //set_widgetheight();
 });
 jQuery('#from-date').datepicker({
  format: 'yyyy-mm-dd',
 });
 jQuery('#to-date').datepicker({
  format: 'yyyy-mm-dd',
 });
</script>
