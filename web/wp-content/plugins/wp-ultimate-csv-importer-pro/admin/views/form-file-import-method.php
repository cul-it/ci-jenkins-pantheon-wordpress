<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

$ucisettings = get_option('sm_uci_pro_settings');
$main_mode = isset($ucisettings['enable_main_mode']) ? $ucisettings['enable_main_mode'] : '';
   ?>
<div class="whole_body wp_ultimate_csv_importer_pro">
   <form class="form-horizontal" id="form_import_file" method="post" action= "<?php echo esc_url(admin_url() . 'admin.php?page=sm-uci-import&step=suggested_template');?>" enctype="multipart/form-data">
   <!-- <div id='wp_warning_main' class = 'updated notice'>
   <p>Supported file types .csv .xml .txt .zip 
   </p></div> -->

 <?php if($main_mode == 'on') { ?>
      <div id='wp_warning_main' style = 'margin-top: 10px;font-size: 15px;color: red;' class = 'error' > Maintenance mode is enabled. <a style="cursor: pointer;" onclick="saveoptions('main_check_import_off', 'off')"> Disable </a> </div>
<?php } ?>


      <div id='wp_warning' style = 'display:none;' class = 'error'></div>
      <div id='wp_notice' style = 'display:none;' class = 'notice notice-warning'><p></p></div>
      <input type='hidden' id="siteurl" value="<?php echo site_url(); ?>" />
      <!-- Code Added For POP UP  Starts here -->
      <div class='modal fade model_for_xml col-md-8 col-md-offset-2' id = 'modal_zip' tabindex='-1' role='dialog' aria-labelledby='mymodallabel' aria-hidden='true'>
         <div class='modal-dialog'>
            <div class='modal-content'>
               <div class='modal-header'>
                  <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                  <h4 class='modal-title' id='mymodallabel'> <?php echo esc_html_e("Choose CSV/XML to import","wp-ultimate-csv-importer-pro");?> </h4>
               </div>
               <div class='modal-body' id = 'choose_file' style="padding-left: 50px">
                  ...
               </div>
               <div class='modal-footer'>
                  <!--<button type='button' class='btn btn-default' data-dismiss='modal'>close</button>  -->
                  <button type='button' class='smack-btn smack-btn-primary btn-radius' data-dismiss='modal'><?php echo esc_html_e("Close","wp-ultimate-csv-importer-pro");?></button>
               </div>
            </div>
         </div>
      </div>
      <!-- Code Added For POP UP Ends here -->
        <div class="">
         <div class="list-inline pull-right mb10">
            <div class="col-md-6"><a href="https://goo.gl/jdPMW8" target="_blank"><?php echo esc_html__('Documentation','wp-ultimate-csv-importer-pro');?></a></div>
            <div class="col-md-6"><a href="https://goo.gl/fKvDxH" target="_blank"><?php echo esc_html__('Sample CSV','wp-ultimate-csv-importer-pro');?></a></div>
         </div>
        </div>
	<div class="clearfix"></div>
      <div class="panel upload-view" style="width: 99%;">
         <!-- <div class="panel-heading">
            <h1 class="text-center"><?php //echo esc_html__('Hello, Choose CSV/XML to import','wp-ultimate-csv-importer-pro');?></h1>
            </div> -->
	      <div id="warningsec" style="color:red;width:100%; min-height: 110px;border: 1px solid #d1d1d1;background-color:#fff;display:none;">
		      <div id ="warning" class="display-warning" style="color:red;align:center;display:inline;font-weight:bold;font-size:15px; border: 1px solid red;margin:2% 2%;padding: 20px 0 20px;position: absolute;text-align: center;width:93%;display:none;"> </div>
	      </div>
         <div class="panel-body">
            <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bhoechie-tab-container">
                  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 no-padding bhoechie-tab-menu">
                     <div class="list-group">
                        <a id="1" href="#" class="list-group-item active text-left" onclick="show_upload(this.id);">
                           <h4 class="glyphicon glyphicon-upload icon-cloud-upload"></h4>
                           <?php echo esc_html__('Upload from Desktop','wp-ultimate-csv-importer-pro');?>
                        </a>
                        <a id="2" href="#" class="list-group-item text-left" onclick="show_upload(this.id);">
                           <h4 class="glyphicon glyphicon-upload icon-upload"></h4>
                           <?php echo esc_html__('Upload from FTP/SFTP','wp-ultimate-csv-importer-pro');?>
                        </a>
                        <a id="3" href="#" class="list-group-item text-left" onclick="show_upload(this.id);">
                           <h4 class="glyphicon glyphicon-upload icon-link2"></h4>
                           <?php echo esc_html__('Upload from URL','wp-ultimate-csv-importer-pro');?>
                        </a>
                        <a id="4" href="#" class="list-group-item text-left" onclick="show_upload(this.id);">
                           <h4 class="glyphicon glyphicon-upload icon-tree"></h4>
                           <?php echo esc_html__('Choose File in the Server','wp-ultimate-csv-importer-pro');?>
                        </a>
                     </div>
                  </div>
                  <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 no-padding bhoechie-tab">
                     <div id ='displaysection' class="col-md-12" style='display: none;'>
                        <div id="displayname">
                           <div id="filenamedisplay"></div>
                        </div>
                        <div class="">
                           <!-- <progress id ='progressdiv' value="100" max="100"> </progress> -->
                           <div id="progress-div">
                              <div id="progress-bar">
                                 <span class="progresslabel">
                                 <?php #echo esc_html__('Upload Completed','wp-ultimate-csv-importer-pro');?>
                                 </span>
                              </div>
                           </div>
                           <div id="targetLayer"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group mt10">
                           <label>
                           <input type="radio" name="import_mode" id="mode_insert" value="new_items" checked="checked"> <?php echo esc_html__('New Items','wp-ultimate-csv-importer-pro');?>
                           </label>
                           <label class="pl20">
                           <input type="radio" name="import_mode" id="mode_update" value="existing_items"> <?php echo esc_html__('Existing Items','wp-ultimate-csv-importer-pro');?>
                           </label>
                        </div>
                        <div id="select_module" class="select_module col-md-8 col-md-offset-3">
                           <span>
                           <label class="import-textnew"><?php echo esc_html__('Import each record as','wp-ultimate-csv-importer-pro');?></label>
                           </span>
                           <span class="select_box" style="width:200px;height:40px;">
                              <select class="search_dropdown selectpicker" id="search_dropdowns" data-size="5" name ='posttype' style="width:200px;height:39px;">
                                 <?php
                                    global $uci_admin;
                                    $all_post_types = $uci_admin->get_import_post_types(); ?>
                                 <optgroup label="PostType">
                                    <?php foreach ($all_post_types as $key => $type) { ?>
                                    <option value="<?php print($type);?>"><?php print($key); ?></option>
                                    <?php }?>
                                 </optgroup>
                                 <optgroup label="Taxonomy">
                                    <?php foreach (get_taxonomies() as $key => $taxo) { ?>
                                    <option value="<?php print($taxo);?>"><?php print($taxo); ?></option>
                                    <?php } ?>
                                 </optgroup>
                              </select>
                           </span>
                        </div>
                        <div class="col-md-1 col-md-offset-10 col-sm-1 col-sm-offset-8 mt20">
                           <input type ="submit" class="smack-btn smack-btn-primary btn-radius ripple-effect continue-btn" disabled value="<?php echo esc_attr__('Continue','wp-ultimate-csv-importer-pro');?>">
                        </div>
                     </div>
                     <div style="border: #999 3px dashed;height: 400px;" class="bhoechie-tab-content active" id="division1" ondrop="drag_drop(event)" ondragover="return false">
                     <h4 align="center">Drop your files here.</h4>
                            <h4 align="center">Supported file types .csv .xml .txt .zip</h4>
                        <div class="file_upload">
                           <input id="upload_file" type="file" name = "files[]" onchange ="upload_method()"/>
                           <div class="file-upload-icon">
                           <span id="fileupload" style="cursor: pointer;" class="import-icon"> <img src="<?php echo plugins_url().'/'.SM_UCI_SLUG ;?>/assets/images/upload-128.png" width="60" height="60" /> </span>
                           <span class="file-upload-text"><?php echo esc_html__('Click here to upload from desktop','wp-ultimate-csv-importer-pro');?><p style='color:#fff;'>(Max filesize is: <?php echo ini_get('upload_max_filesize').'B'; ?>)</p></span>
                           </div>
                        </div>
                     </div>
                     <div class="bhoechie-tab-content" id="division2">
                        <!-- Tab 2 Content -->
                        <div class="col-md-12" >
                           <div class="col-md-6 col-sm-6">
                              <div class="wp_csv_ftp form-group">
                                 <label><?php echo esc_html__('Hostname','wp-ultimate-csv-importer-pro');?></label>
                                 <input type="text" name="host_name" id="host_name" class="align form-control textbox_size" value="" >
                                 <p class="hint_fonts"><?php echo esc_html__('smackcoders.com or 54.213.74.129','wp-ultimate-csv-importer-pro');?></p>
                              </div>
                              <div class="wp_csv_ftp form-group">
                                 <label><?php echo esc_html__('Host Username','wp-ultimate-csv-importer-pro');?></label>
                                 <input type="text" name="host_username" id="host_username" class="align form-control textbox_size" value =""  >
                                 <p class="hint_fonts" ><?php echo __('ftp username','wp-ultimate-csv-importer-pro');?></p>
                              </div>
                           </div>
                           <div class="col-md-6 col-sm-6">
                              <div class="wp_csv_ftp form-group">
                                 <label><?php echo esc_html__('Host Port','wp-ultimate-csv-importer-pro');?></label>
                                 <input type="text" name="host_port" id="host_port" class="align form-control textbox_size" value=""  >
                                 <p class="hint_fonts"><?php echo esc_html__('Default Port : 21','wp-ultimate-csv-importer-pro');?></p>
                              </div>
                              <div class="wp_csv_ftp form-group">
                                 <label><?php echo esc_html__('Host Password','wp-ultimate-csv-importer-pro');?></label>
                                 <input type="text" name="host_password" id="host_password" class="align form-control textbox_size" value="" > 
                                 <p class="hint_fonts"><?php echo esc_html__('ftp password','wp-ultimate-csv-importer-pro');?></p>
                              </div>
                           </div>
                           <div class="col-md-12 col-sm-12">
                              <div class="wp_csv_ftp form-group" style="margin: 0 -8px 0 -10px;">
                                 <label><?php echo esc_html__('Host Path','wp-ultimate-csv-importer-pro');?></label>
                                 <input type="text" name="host_path" id="host_path" class="align form-control textbox_size" value="" placeholder = "">
                                 <p class="hint_fonts" ><?php echo esc_html__('/home/guest/sample.csv','wp-ultimate-csv-importer-pro');?></p>
                              </div>
                           </div>
                           <div class="col-md-12 col-sm-12">
                              <div class="wp_csv_ftp form-group" style="margin: 0 -8px 0 -10px;">
                                 <label><?php echo esc_html__('Connection Type
','wp-ultimate-csv-importer-pro');?></label>
                                <fieldset style="margin-left: 5% !important;" id="tempgroup">
                                     <input checked type="radio" value="ftp" id="template1" name="host_type"><span style="margin-right:5%;font-size: 1.3em;font-weight: bold;"> FTP</span>
                                     <input type="radio" value="ftps" id="template2" name="host_type"><span style="margin-right:5%;font-size: 1.3em;font-weight: bold;">FTPS (SSL)</span> 
                                     <input type="radio" value="ssh2" id="template3" name="host_type"><span style="font-size: 1.3em;font-weight: bold;" >SSH2 / SFTP</span>
                              </fieldset> 
                              </div>
                           </div>
                           
                           <div class="col-md-offset-10 mb10">
                              <input type="button" name="dwn_ftp_file" id="dwn_ftp_file" class="smack-btn smack-btn-primary btn-radius continue-btn" value="<?php echo esc_attr__('Continue','wp-ultimate-csv-importer-pro');?>">
                           </div>
                        </div>
                        <!-- Tab 2 Content End-->
                     </div>
                     <div class="bhoechie-tab-content" id="division3">
                        <!--url -->
                        <div class="col-md-12">
                        <div class="" style="margin-top: 100px;">
                           <label class="text-left"><?php echo __('File path','wp-ultimate-csv-importer-pro');?></label>
                           <input type="text" name="extrnfileurl" id="extrnfileurl" class="align form-control textbox_size"  placeholder="http://example.com/sample.csv (or) https://goo.gl/SX2tNf (or) http://bit.ly/2hXvlAQ" value=''/></div>
                           
                           <div class = "wp_csv_ftp download_btnexternal col-md-offset-10 col-sm-offset-7 col-xs-offset-3" style="">
                           
                           <input type="button" name="dwn_file" id="dwn_file" class="smack-btn smack-btn-primary btn-radius continue-btn" value="<?php echo esc_attr__('Continue','wp-ultimate-csv-importer-pro');?>" onclick="external_method()"></div></div>

                        
                        <!--url end-->
                    </div>

                     <div class="bhoechie-tab-content" id="division4">
                        <div class="col-md-12">
                        <div id="file_tree" class="file_tree mt40 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0" style="overflow-y: scroll;">
                        </div>
                        <script type = 'text/javascript'>
	                        jQuery(document).ready( function() {
		                        var siteurl = document.getElementById('siteurl').value;
		                        jQuery('#file_tree').fileTree({
			                        root: '/',
			                        script: siteurl + '/wp-admin/admin-ajax.php?action=file_treeupload',
			                        expandSpeed: 750,
			                        collapseSpeed: 750,
			                        multiFolder: false
		                        }, function(file) {
			                        var postdata = new Array({'external_file_url':file, 'import_method':'server_import'});
			                        jQuery.ajax({
				                        type: 'POST',
				                        url: ajaxurl,
				                        data: {
					                        'action': 'external_file_actions',
					                        'postdata': postdata
				                        },
				                        xhr: function(){
					                        //upload Progress
					                        var xhr = jQuery.ajaxSettings.xhr();
					                        if (xhr.upload) {
						                        xhr.upload.addEventListener('progress', function(event) {
							                        var percent = 0;
							                        var position = event.loaded || event.position;
							                        //var position = event.position;
							                        var total = event.total;
							                        if (event.lengthComputable) {
								                        percent = Math.ceil(position / total * 100);
							                        }
							                        //update progressbar
							                        jQuery("#progress-div" + " #progress-bar").css("width", + percent +"%");
						                        }, true);
					                        }
					                        return xhr;
				                        },
				                        success: function (data) {
					                        data = JSON.parse(data);
					                        if(data['Success'] == 'Success!') {
						                        document.getElementById('file_name').value = '';
						                        document.getElementById('file_name').value = data['filename'];
						                        var get_file_extension = data['filename'].split('/');
						                        var get_file_name = get_file_extension[get_file_extension.length-1];
						                        var get_file = get_file_name.split('.');
						                        var fileextn = get_file[get_file.length-1];
									if(data['isutf8'] == 'No'){
                        							document.getElementById('wp_notice').style.display = '';
                        							document.getElementById('wp_notice').innerHTML = '<p>Your csv file has invalid UTF-8 character. please check your csv</p>';
                							}
						                        if(fileextn == 'zip'){
							                        jQuery.ajax({
								                        type: 'POST',
								                        url: ajaxurl,
								                        data: {
									                        'action': 'upload_zipfile_handler',
									                        'eventkey': data['eventkey'],
									                        'import_method':'server'
								                        },
								                        success: function (data) {
									                        data = JSON.parse(data);
									                        document.getElementById('choose_file').innerHTML =data['data'];
									                        jQuery('#modal_zip').modal('show');
								                        }
							                        });
						                        } else {
							                        document.getElementById('file_version').value=data['version'];
							                        document.getElementById('uploaded_name').value=data['filename'];
							                        document.getElementById('file_extension').value = data['extension'];
							                        var get_current_action = jQuery( '#form_import_file' ).attr( 'action' );
							                        document.getElementById('displaysection').style.display = "";
							                        document.getElementById('division4').style.display = "none";
							                        jQuery("#filenamedisplay").empty();
							                        jQuery('<label/>').text((data['filename']) + ' - ' + data['filesize']).appendTo('#filenamedisplay');
						                        }
						                        if(fileextn != 'zip'){
							                        jQuery.ajax({
								                        type: 'POST',
								                        url: ajaxurl,
								                        data: {
									                        'action': 'set_post_types',
									                        'filekey': data['eventkey'],
									                        'uploadedname': data['filename']
								                        },
								                        success: function (result) {
									                        var result = JSON.parse(result);
									                        if(result != '') {
										                        if(result['is_template'] == 'yes'){
											                        var action = get_current_action + '&eventkey=' + data['eventkey'];
										                        } else {
											                        var splitaction = get_current_action.split("&");
											                        var action = splitaction[0] + '&step=mapping_config&istemplate=no&eventkey=' + data['eventkey'];
										                        }
										                        jQuery('.selectpicker').selectpicker('val', result['type']);
									                        } else {
										                        var splitaction = get_current_action.split("&");
										                        var action = splitaction[0] + '&step=mapping_config&istemplate=no&eventkey=' + data['eventkey'];
									                        }
									                        jQuery('#form_import_file').attr('action', action);
									                        jQuery('.continue-btn').attr('disabled', false);
								                        }
							                        });
						                        }
						                        document.getElementById('server_dwn_file').disabled = false;
					                        } else {
						                        var warning = data['Failure'];
						                        notice_warning(warning);
						                        document.getElementById('upload_file').value="";
						                        return false;
					                        }
				                        }
			                        });
		                        });
	                        });
                        </script>
                        <div class = "col-md-1 col-md-offset-11 col-sm-1 col-sm-offset-10 col-xs-1 col-xs-offset-7 mb15" style="margin-top:73px;"><input type="button" name="dwn_file" id="server_dwn_file" class="smack-btn smack-btn-primary btn-radius pull-right continue-btn" value="<?php echo esc_attr__('Continue','wp-ultimate-csv-importer-pro');?>" onclick="server_method()"></div></div>
                     </div>
                  </div>
               </div>
               <!-- Row -->
            </div>
            <!-- Panel Body -->
         </div>
      </div>
      <script type="text/javascript">
         jQuery(document).ready(function() {
         jQuery("div.bhoechie-tab-menu>div.list-group>a").click(function(e) {
         	e.preventDefault();
         	jQuery(this).siblings('a.active').removeClass("active");
         	jQuery(this).addClass("active");
         	var index = jQuery(this).index();
         	jQuery("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
         	jQuery("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
         });
         });
      </script>
      <input type='hidden' id='uploaded_name' name='uploaded_name' value =''>
      <input type='hidden' id='file_name' name='file_name' value =''>
      <input type="hidden" id="file_extension" name="file_extension" value="">
      <input type="hidden" id="import_method" name = "import_method" value="desktop">
      <input type='hidden' id='file_version' name='file_version' value=''>
      <input type='hidden' id='upload_max' name='upload_max' value='<?php echo ini_get('upload_max_filesize');?>'>
   </form>
</div>
<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com/?utm_source=wordpress&utm_medium=plugin&utm_campaign=pro_csv_importer" target="blank">Smackcoders</a>.</div>
