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
$importAs = $templateName = $istemplate = '';
if(isset($_REQUEST['istemplate']) && sanitize_text_field($_REQUEST['istemplate']) == 'no') {
   $istemplate = 'no';
}

if($_POST) {
   if($_REQUEST['istemplate'] == 'no'){
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      $records['import_file'] = $_POST;
      $uci_admin->SetPostValues(sanitize_key($_REQUEST['eventkey']), $records);
   }else{
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      $records['suggested_template'] = $_POST;
      $post_values = $uci_admin->GetPostValues(sanitize_key($_REQUEST['eventkey']));
      $result = array_merge($post_values[$_REQUEST['eventkey']], $records);
      $uci_admin->SetPostValues(sanitize_key($_REQUEST['eventkey']), $result);
   }
}
$eventKey = sanitize_key($_REQUEST['eventkey']);
$get_records = $uci_admin->GetPostValues($eventKey);

if($get_records[$eventKey]['import_file']['file_extension'] == 'xml'){
   ?>
   <script type="text/javascript">
      mapping_type('advanced');
   </script>
   <?php
}

$import_type = $get_records[$eventKey]['import_file']['posttype'];
if(!empty($get_records[$eventKey]['mapping_config']) && $get_records[$eventKey]['mapping_config']) {
   $mapping_screendata = $uci_admin->get_mapping_screendata( $import_type, $get_records[$eventKey]['mapping_config']);
}
$file = SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
$parserObj->parseCSV($file, 0, -1);
$Headers = $parserObj->get_CSVheaders();
$Headers = $Headers[0];
global $wpdb;
if($istemplate == 'no'){
   $backlink = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=sm-uci-import');
   $actionURL = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=media_config&istemplate=no&eventkey='.$_REQUEST['eventkey']);
}else{
   $backlink = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=suggested_template&eventkey='.$_REQUEST['eventkey']);
   $actionURL = esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=media_config&eventkey='.$_REQUEST['eventkey']);
}
$templateName = '';
if(isset($_REQUEST['templateid'])) {
   $templateInfo = $wpdb->get_results($wpdb->prepare("select templatename, mapping from wp_ultimate_csv_importer_mappingtemplate where id = %d", $_REQUEST['templateid']));
   $template_mapping = maybe_unserialize($templateInfo[0]->mapping);
   $templateName = $templateInfo[0]->templatename;
   $actionURL .= '&templateid=' . intval($_REQUEST['templateid']);
   $backlink .= '&templateid=' . intval($_REQUEST['templateid']);
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
   $templateInfo = $wpdb->get_results($wpdb->prepare("select templatename, mapping from wp_ultimate_csv_importer_mappingtemplate where eventKey = %s", $_REQUEST['eventkey']));
   $template_mapping = maybe_unserialize($templateInfo[0]->mapping);
   $templateName = $templateInfo[0]->templatename;
}
?>
<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
   <div class="col-md-6"><a href="https://goo.gl/jdPMW8" target="_blank"><?php echo esc_html__('Documentation','wp-ultimate-csv-importer-pro');?></a></div>
   <div class="col-md-6"><a href="https://goo.gl/fKvDxH" target="_blank"><?php echo esc_html__('Sample CSV','wp-ultimate-csv-importer-pro');?></a></div>
</div>
<?php
$normal = 'active'; $advanced = '';
if(isset($_REQUEST['mapping_type']) && $_REQUEST['mapping_type'] == 'normal') {
   $normal = 'active';
   $advanced = '';
} elseif(isset($_REQUEST['mapping_type']) && $_REQUEST['mapping_type'] == 'advanced') {
   $normal = '';
   $advanced = 'active';
}
?>
<div class="col-md-6 col-md-offset-3">
   <ul class="mapping-switcher">
      <li class="<?php echo $normal; ?>" onclick="mapping_type('normal');">Advanced Mode</li>
      <li class="<?php echo $advanced; ?>" onclick="mapping_type('advanced');">Drag & Drop Mode</li>
   </ul>
</div>
<div class="clearfix"></div>
<div class="template_body wp_ultimate_csv_importer_pro whole_body" style="margin-top: 40px;">
   <div>
      <h3 style="margin-left:2%;" class="csv-importer-heading"><?php echo esc_html__('Mapping Section','wp-ultimate-csv-importer-pro');?></h3>
   </div>
   <form id = "mapping_section" method="post" action="<?php echo $actionURL;?>">
      <div id='wp_warning' style = 'display:none;' class = 'error'></div>
      <?php $import_mode = $get_records[sanitize_key($_REQUEST['eventkey'])]['import_file']['import_mode']; ?>
      <div class="mapping_table">
         <?php
         $integrations = $uci_admin->available_widgets($import_type, $importAs);
         if(!empty($integrations)) :
            foreach($integrations as $widget_name => $plugin_file) {
               $widget_slug = strtolower(str_replace(' ', '_', $widget_name));
               $fields = $uci_admin->get_widget_fields($widget_name, $import_type, $importAs);
               ?>
               <div class="panel-group" id='accordion'>
                  <div class='panel panel-default' data-target="#<?php echo $widget_slug;?>" data-parent="#accordion">
                     <div id='<?php echo $widget_slug;?>' class='panel-heading' style='width:100%'  onclick="toggle_func('<?php echo $widget_slug;?>');">
                        <div id= "corehead" class="panel-title"> <b style=""> <?php if($widget_name == 'Core Fields'){ echo esc_html__('WordPress Fields'); } else { echo $widget_name; } ?> </b>
                           <span class = 'glyphicon icon-circle-down' id = '<?php echo 'icon'.$widget_slug ?>' style="float:right;"> </span>
                        </div>
                     </div>
                     <div id= '<?php echo $widget_slug;?>toggle'  style="height:auto;">
                        <div class="grouptitlecontent " id="corefields_content">
                           <table  class='table table-mapping custom_table' id='<?php echo $widget_slug;?>_table' style='font-size: 12px;margin-bottom:0px;' id='$tableid'>
                              <tbody>
                              <tr>
                                 <?php if($import_mode != 'new_items') { ?>
                                    <td style='width:10%;padding:15px;'>
                                       <input type='checkbox' name = 'name<?php  print($plugin_file); ?>' id = 'id<?php print($plugin_file); ?>' onClick="select_All(this.id,'<?php print($plugin_file);?>','<?php print($widget_slug);?>')">
                                       <?php #} ?>
                                    </td>
                                 <?php } ?>
                                 <td class='columnheader mappingtd_style'><label class='groupfield'><?php echo esc_html__('WP Fields','wp-ultimate-csv-importer-pro');?></label></td>
                                 <td class='columnheader mappingtd_style'><label class='groupfield'><?php echo esc_html__('CSV Header','wp-ultimate-csv-importer-pro');?></label></td>
                                 <!--<td style='width:20%;'></td>-->
				<?php if( $widget_slug == 'wordpress_custom_fields' ){ ?>
                                 <td style='width:20%;' class='columnheader mappingtd_style'><label class='groupfield'><?php echo esc_html__('Is Serialized','wp-ultimate-csv-importer');?></label></td> <?php }else{ ?>
                                 <td style='width:20%;'></td> <?php } ?>
                                 <td style='width:30%;'></td>
                              </tr>
                              <div>
                                 <?php
                                 $CORE_count = 0; $mappingcount = 0;
                                 if(!empty($fields)){
                                 foreach($fields as $key => $value) {
                                 $prefix = $key;
                                 foreach ($value as $key1 => $value1) {
                                 $label = $value1['label'];
                                 $name = $value1['name']; ?>
                                 <tr id='<?php print($prefix); ?>_tr_count<?php print($CORE_count); ?>'>
                                    <?php if($import_mode != 'new_items') { ?>
                                       <td id='<?php print($prefix); ?>_tdc_count<?php print($CORE_count); ?>' style='width:10%;padding:15px;'>
                                          <input type = "hidden" name = '<?php print($prefix);?>_check_<?php echo $name; ?>' value = 'off' >
                                          <?php # if(isset($_REQUEST['templateid'])){ ?>
                                          <input type='checkbox' name = '<?php print($prefix);?>_check_<?php echo $name; ?>' id = '<?php print($prefix);?>_num_<?php echo $mappingcount; ?>'>
                                          <?php #} ?>
                                       </td>
                                    <?php } ?>
                                    <td id='<?php print ($prefix); ?>_tdg_count<?php print($CORE_count); ?>' class='left_align' style='width:20%;'>
                                       <label class='wpfields'> <?php print('<b style="">'.$label.'</b></label><br><label class="samptxt">[Name: '.$name.']'); ?> </label>
                                       <input type='hidden' name='<?php echo $key . '__fieldname' . $mappingcount; ?>' id='<?php echo $key . '__fieldname' . $mappingcount; ?>' value='<?php echo $name; ?>' class='hiddenclass'/>
                                    </td>
                                    <td class="mappingtd_style">
                                       <div id="headerlist" class="" >
                                          <div class="mapping-select-div">
                                             <select class="selectpicker"  id="<?php print($prefix); ?>__mapping<?php print($mappingcount); ?>" style="height:25px;" name="<?php print($prefix); ?>__mapping<?php print($mappingcount); ?>" onchange="enable_mapping_fields('<?php echo $prefix; ?>', '<?php echo $mappingcount; ?>', this.id);">
                                                <option value="--select--"> <?php echo esc_html__('--select--');?></option>
                                                <?php foreach ($Headers as $csvkey => $csvheader) {
                                                   if(!empty($template_mapping[$key])) {
                                                      $mapping_selected = null;
                                                   if(array_key_exists($name,$template_mapping[$key]) && $csvheader == $template_mapping[$key][$name]) {
                                                      $mapping_selected = 'selected'; ?>
                                                      <?php if ( $import_mode != 'new_items' ) { ?>
                                                      <script>
                                                         document.getElementById("<?php echo esc_js( $prefix ); ?>_num_<?php echo esc_js( $mappingcount ); ?>").checked = true;
                                                      </script>
                                                   <?php }
                                                   }?>
                                                      <option value="<?php echo $csvheader; ?>" <?php echo $mapping_selected;?>> <?php echo $csvheader; ?> </option>
                                                   <?php } else {
                                                   if(!empty($mapping_screendata[$key])){
                                                      if(array_key_exists($name,$mapping_screendata[$key]) && $csvheader == $mapping_screendata[$key][$name]){
                                                      $mapping_selected = 'selected'; ?>

                                                      <option value="<?php echo $csvheader; ?>" <?php echo $mapping_selected;?>> <?php echo $csvheader; ?> </option>
                                                   <?php }
                                                   }
                                                   if($name == $csvheader) {?>
                                                      <option value="<?php print($csvheader); ?>" selected><?php print($csvheader); ?> </option>
                                                   <?php if($import_mode != 'new_items') { ?>
                                                      <script>
                                                         document.getElementById("<?php echo esc_js($prefix); ?>_num_<?php echo esc_js($mappingcount); ?>").checked = true;
                                                      </script>
                                                   <?php }
                                                   } else { ?>
                                                      <option value="<?php echo $csvheader; ?>"><?php echo $csvheader; ?> </option>
                                                   <?php }
                                                   }
                                                }?>
                                                <option value="header_manip"><?php echo esc_html__('Header Manipulation');?></option>
                                             </select>
                                          </div>
                                       </div>
                                    </td>
                                    <!--<td class="mappingtd_style"></td>-->
				    <?php if( $widget_slug == 'wordpress_custom_fields' ){ ?>
                                             <td id='<?php print ($prefix); ?>_tdSerialize_count<?php print($CORE_count); ?>' class="mappingtd_style" style='padding-left:5%'><input type='checkbox' name='<?php print ($prefix); ?>__SerializeVal<?php print($CORE_count); ?>'></td>
                                             <?php }else{ ?>
                                             <td class="mappingtd_style"></td> <?php } ?>
                                    <td>
                                       <div class="mapping-static-formula-group">
                                          <span title='Static'  style='margin-right:15px;' id='<?php echo $prefix; ?>_staticbutton_mapping<?php echo $mappingcount; ?>' onclick="static_method(this.id,'<?php echo $prefix; ?>','<?php echo $mappingcount; ?>',null)"><img style="margin-right:15px;" src="<?php echo plugins_url().'/'.SM_UCI_SLUG ;?>/assets/images/static.png" width="24" height="24" /></span>
                                          <span title='Formula' style='margin-right:15px;' id='<?php echo $prefix; ?>_formulabutton_mapping<?php echo $mappingcount; ?>' onclick="formula_method(this.id,'<?php echo $prefix; ?>','<?php echo $mappingcount; ?>',null)"><img src="<?php echo plugins_url().'/'.SM_UCI_SLUG ;?>/assets/images/formula.png" width="24" height="24" /></span>
                                       </div>
                                       <div id="<?php echo $prefix; ?>_customdispdiv_mapping<?php echo $mappingcount; ?>"
                                            class='mapping-select-close-div' style='height:246px;padding:8px;display:none;width:267px;border:3px solid #2ea2cc;margin-top:5px;position:absolute;background-color:#ffffff;z-index: 99;'></div>
                                    </td>
                              </div>
                              </tr>
                              <?php
                              if(!empty($template_mapping[$key][$name])) {
                                 if(preg_match_all("/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/", $template_mapping[$key][$name], $results, PREG_PATTERN_ORDER)) { ?>
                                    <script>
                                       var dropdownid = '<?php echo $prefix.'__mapping'.$mappingcount;?>';
                                       document.getElementById(dropdownid).value = 'header_manip';
                                       var buttonid = '<?php echo $prefix.'_staticbutton_mapping'.$mappingcount;?>';
                                       var staticheader = '<?php echo $template_mapping[$key][$name];?>';
                                       static_method(buttonid,'<?php echo $prefix;?>','<?php echo $mappingcount;?>',staticheader);
                                    </script>
                                 <?php } elseif(preg_match_all("/({([\w]+)(.*?)(}))/", $template_mapping[$key][$name], $results, PREG_PATTERN_ORDER)) { ?>
                                    <script>
                                       var dropdownid = '<?php echo $prefix.'__mapping'.$mappingcount;?>';
                                       document.getElementById(dropdownid).value = 'header_manip';
                                       var buttonid = '<?php echo $prefix.'_formulabutton_mapping'.$mappingcount;?>';
                                       var formulaheader = '<?php echo $template_mapping[$key][$name];?>';
                                       formula_method(buttonid,'<?php echo $prefix;?>','<?php echo $mappingcount;?>',formulaheader);
                                    </script>
                                 <?php }}
                              $CORE_count++;
                              $mappingcount++;
                              }
                              }}?>
                              </tbody>
                           </table>
                           <input type='hidden' id='<?php echo $widget_slug;?>_count' value= '<?php echo $CORE_count; ?>'>
                           <?php
                           if($widget_slug=='wordpress_custom_fields'|| $widget_slug=='acf_pro_fields'|| $widget_slug=='acf_fields' || $widget_slug=='types_custom_fields' || $widget_slug=='pods_custom_fields') { ?>
                              <div class="customfield_btndiv">
                                 <div class="col-md-offset-9 col-sm-offset-8">
                                    <input type="button" class="customfield_btn smack-btn smack-btn-primary btn-radius" value="Add Custom Field" onclick="addCustomfield('<?php echo $plugin_file;?>','<?php echo $widget_slug;?>','<?php echo sanitize_title($_REQUEST["eventkey"]);?>')" >
                                 </div>
                              </div>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
               </div>
               <script>
                  var id = '<?php echo $widget_slug;?>'+'toggle';
                  if(id != 'core_fieldstoggle'){
                     jQuery('#'+id).hide();
                  }else{
                     jQuery("#iconcore_fields").attr('class','glyphicon icon-circle-up')
                  }
               </script>
               <?php
            }
         endif;
         $save_templatename = '';
         if($save_templatename == '' && $templateName == '') {
            $filename = isset($get_records[$eventKey]['import_file']['uploaded_name']) ? $get_records[$eventKey]['import_file']['uploaded_name'] : '';
            $file_extension = pathinfo($filename,PATHINFO_EXTENSION);
            $file_extn = '.'.$file_extension;
            $filename = explode($file_extn, $filename);
            $templateName = $filename[0];
         }
         ?>
         <?php if(!isset($_REQUEST['action'])) { ?>
            <div align="center" style="font-size: 15px;" class="col-md-12">
               <?php if(isset($_REQUEST['templateid'])) { ?>
                  <label style="font-size: 15px;"><input type="checkbox" name="template" checked="checked" id="save_template_chkbox" value="auto_update" onclick="show_savetemplate();"> <?php echo esc_html__('Do you need to update the current mapping','wp-ultimate-csv-importer-pro'); ?></label>
                  <input type='text' id='templatename' name='templatename' style='margin-left:10px; width: 25% !important; display:inline;' placeholder='<?php echo $templateName; ?>' value = '<?php echo $templateName;?>'/>
               <?php } else { ?>
                  <label style="font-size: 15px;"><input type="checkbox" class="" name="template" checked="checked" id="save_template_chkbox" value="auto_save" onclick="show_savetemplate();">   <?php echo esc_html__('Save the current mapping as new template','wp-ultimate-csv-importer-pro'); ?></label>
                  <input type='text' class="form-control" id='templatename' name='templatename' style='margin-left:10px; width:25% !important; display:inline;' placeholder='<?php echo $templateName; ?>' value='<?php echo $templateName; ?>'/>
               <?php } ?>
            </div>
         <?php }?>
      </div>
      <?php if(isset($_REQUEST['action']) && sanitize_title($_REQUEST['action']) == 'edit') {?>
         <div align="center" id = 'newmappingtemplatename'><?php echo __("Save this mapping as Template"); ?>
            <input type='text' id='templatename' name = 'templatename' style='margin-left:10px; width: 25% !important;' placeholder='<?php echo $templateName; ?>' value = '<?php echo $templateName;?>'/>
         </div>
         <input type="button" class="smack-btn smack-btn-primary btn-radius mapping_continuebtn" value="<?php echo esc_attr__('Save','wp-ultimate-csv-importer-pro');?>" style="margin-bottom:5%;" onclick="update_template('<?php echo sanitize_key($_REQUEST['eventkey']);?>');">
      <?php } else {?>
         <div class="col-md-12 mt20">
            <!-- back btn -->
            <div class="pull-left">
               <a class="smack-btn btn-default btn-radius" href="<?php echo $backlink;?>">Back
               </a> <!-- back btn end -->
            </div>
            <!--continue btn -->
            <div class="pull-right">
               <input type="button" class="smack-btn smack-btn-primary btn-radius" value="<?php echo esc_attr__('Continue','wp-ultimate-csv-importer-pro');?>" onclick="save_template('<?php echo $import_type;?>');">
            </div>
         </div>
         <!-- continue btn end -->
         <div class="clearfix"></div>
         <div class="mb20"></div>
      <?php }?>
      <input type="hidden" name="smack_uci_mapping_method" value="<?php if(isset($_REQUEST['mapping_type'])) echo sanitize_title($_REQUEST['mapping_type']); ?>">
   </form>
</div>
<input type='hidden' id='h1' name='h1' value='<?php if (isset($mappingcount)) { echo $mappingcount; } ?>'/>
<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com/?utm_source=wordpress&utm_medium=plugin&utm_campaign=pro_csv_importer" target="blank">Smackcoders</a>.</div>
