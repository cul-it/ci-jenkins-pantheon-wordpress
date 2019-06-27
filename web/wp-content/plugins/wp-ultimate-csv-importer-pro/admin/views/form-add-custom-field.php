<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
$prefix = sanitize_text_field($_REQUEST['prefix']);
$core_count = $mapping_count = intval($_REQUEST['count']);
$eventKey = sanitize_key($_REQUEST['eventkey']);
global $uci_admin;
$parserObj = new SmackCSVParser();
$get_records = $uci_admin->GetPostValues($eventKey);
$import_type = $get_records[$eventKey]['import_file']['posttype'];
$mode = $get_records[$eventKey]['import_file']['import_mode'];
$file = SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
$parserObj->parseCSV($file, 0, -1);
$Headers = $parserObj->get_CSVheaders();
$Headers = $Headers[0];
add_customrow_for_register_field($mode,$mapping_count,$prefix,$core_count,$Headers,$import_type);


function add_customrow_for_register_field($mode,$mapping_count,$prefix,$core_count,$Headers,$import_type){
	global $uci_admin;
	$slug = sanitize_text_field($_REQUEST['slug']);
	if($prefix == 'CORECUSTFIELDS'){
		add_corecustomfields($mode,$mapping_count,$prefix,$core_count,$Headers,$slug,$import_type);
	}
	else{
		$result = "<tr class='addrow_row'><td colspan = '5' align ='center' id='".$prefix."newrow$core_count' style=''>";
		$result .= "<div class = 'addrow_div' style='width:63%;'><div style='align:center;'><table class='table'><tr>
        <th colspan = '2' class='addrow_head'>".esc_html__('Basic Information for Field Registration','wp-ultimate-csv-importer-pro')."<label style = 'color:red;float:right;width:2%;' > <p class= 'glyphicon glyphicon-remove' id = '".$prefix."close_fd$mapping_count' onclick = Close_RegisterUI('".$prefix."','".$core_count."')></p></label> </th>
        <tr><td class='addrow_column'><label class='wpfields' name='".$prefix."ui_CustomFieldLabel' id='".$mapping_count."ui_CustomFieldLabel'>".esc_html__('Label','wp-ultimate-csv-importer-pro')."</label></td>
        <td class='addrow_column'><input class = 'addrow_fds' id='".$prefix."ui__CustomFieldLabel$mapping_count' type='text' name='".$prefix."ui__CustomFieldLabel$mapping_count' value='' size=60  onblur = is_emptyCF(this.id,'".$mapping_count."','".$prefix."')>
	<input type ='hidden' name = '".$prefix."__fieldname".$mapping_count."' id = '".$prefix."__fieldname".$mapping_count."' value = '' class = 'hiddenclass'/>
	</td></tr>
        <tr class = 'tboddcolor'>
        <td class='addrow_column'><label class='wpfields' name='".$prefix."ui_CustomFieldName' id='".$mapping_count."ui_CustomFieldName'>".esc_html__('Name','wp-ultimate-csv-importer-pro')."</label></td>
        <td class='addrow_column'><input class = 'addrow_fds' id = '".$prefix."ui__CustomFieldName$mapping_count' type='text' name='".$prefix."ui__CustomFieldName$mapping_count' value='' size=60 onblur = is_emptyCF(this.id,'".$mapping_count."','".$prefix."') /></td></tr>
        <tr>
        <td class='addrow_column'><label class='wpfields'>".esc_html__('Description','wp-ultimate-csv-importer-pro')."</label></td>
        <td class='addrow_column'><input class = 'addrow_fds' id = '".$prefix."ui__CustomFieldDesc$mapping_count' type='text' name='".$prefix."ui__CustomFieldDesc$mapping_count' value='' size=60/></td></tr>";
		$result .= "<tr class = 'tboddcolor'>
        <td class='addrow_column'><label class='wpfields'>".__('Field Type')."</label></td>
        <td class='addrow_column'>";
		$result .="<span id='".$prefix."_type".$core_count."' style='padding-bottom:10px;' >";
		/** ACF Free fields does not support all fields in ACF Pro fields **/
		/** ACF Pro fields are disabled that are not supported by ACF Free **/
		/** For disable "disable_acf_fields" function was used **/
		if($prefix != 'ACF')
			$result .= "<div id='".$prefix."_selectdiv'><select class = 'addrow_select' name='".$prefix."_datatype_$mapping_count' id='".$prefix."_datatype_$mapping_count' onchange = Validate_CF_types(this.id,'".$prefix."','".$mapping_count."')>";
		else
			$result .= "<div id='".$prefix."_selectdiv'><select class = 'addrow_select' name='".$prefix."_datatype_$mapping_count' id='".$prefix."_datatype_$mapping_count' onchange = Validate_CF_types(this.id,'".$prefix."','".$mapping_count."') onclick = disable_acf_fields(this.id,'".$slug."')>";
		$result .= "<option class = 'addrow_option' >".esc_html__('--Select--','wp-ultimate-csv-importer-pro')."</option>";
		$result .= add_cf_controls($prefix,$core_count,$mapping_count);
		$result .= "</select></div></span>";
		$result .= "</td></tr>";
		/** Choice for Custom Fields UI */
		if($prefix == 'ACF') {
			$result .= "<tr style='display:none' id='".$prefix."_trchoice".$mapping_count."'>
		<td class='addrow_column'><label class='wpfields'>Choices</label></td>
		<td class='addrow_column'>";
			$result .= "<textarea rows='3' cols='48' name='type_options' id='".$prefix."_type_options".$mapping_count."' onblur=validate_options('".$prefix."','".$mapping_count."') ></textarea>";
			$result .= "<p style='margin-top:10px;width:90%;border-radius:4px;border:1px solid #ddd;padding:10px;'>HINT: Specify the CHOICES with COMMA operator.<br /> Example: Red,Green,Blue</p>";
			$result .="</td></tr>";
			/** For get the user role for user field of ACF UI **/
			$result .= "<tr style='display:none' id='".$prefix."_truser".$mapping_count."'>
		<td class='addrow_column'><label class='wpfields'>".esc_html__('User Role','wp-ultimate-csv-importer-pro')."</label></td>
		<td class='addrow_column'>";
			$result .= "<select class = 'addrow_select' name = '".$prefix."_role".$mapping_count."' id = '".$prefix."_role".$mapping_count."' onchange= Validate_CF_types(this.id,'".$prefix."','".$mapping_count."') >
		<option class = 'addrow_option' style='line-height:5px;' value='--select--'>".esc_html__('--Select--','wp-ultimate-csv-importer-pro')."</option>
		<option class = 'addrow_option' style='line-height:5px;' value='administrator'>".esc_html__('Administrator','wp-ultimate-csv-importer-pro')."</option>
		<option class = 'addrow_option' style='line-height:5px;' value='editor'>".esc_html__('Editor','wp-ultimate-csv-importer-pro')."</option>
		<option class = 'addrow_option' style='line-height:5px;' value='author'>".esc_html__('Author','wp-ultimate-csv-importer-pro')."</option>
		<option class = 'addrow_option' style='line-height:5px;' value='subscriber'>".esc_html__('Subscriber','wp-ultimate-csv-importer-pro')."</option>
		<option class = 'addrow_option' style='line-height:5px;' value='contributor'>".esc_html__('Contributor','wp-ultimate-csv-importer-pro')."</option>
		</select></td></tr>";
			/** End User UI **/
		}
		if($prefix == 'PODS') {
			$relational_fields = array('Custom'=> array('custom'=>'Simple (custom defined list)'),
			                           'Post Types'=> array('page'=>'Pages (page)','post'=>'Posts (post)'),
			                           'Taxonomies' => array('category'=>'Categories (category)','linkcategory'=>'Link Categories (link_category)','tags'=>'Tags (post_tag)'),
			                           'Other WP Objects' => array('user'=>'Users','user_role'=>'User Roles','user_capability'=>'User Capabilities','media'=>'Media','comment'=>'Comments','image_size'=>'Image Sizes','navigation'=>'Navigation Menus','post_format'=>'Post Formats','post_status'=>'Post Status'),
			                           'Predefined Lists' => array('country'=>'Countries','us_status'=>'US States','calender_week'=>'Calendar - Days of Week','calender_year'=>'Calendar - Months of Year'));
			$result .= "<tr style='display:none' id = '".$prefix."_reltofd".$mapping_count."'>
		<td class='addrow_column'><label class='wpfields'>Related To</label></td>
		<td class='addrow_column'>";
			$result .= "<select class = 'addrow_select' name='".$prefix."_relatedtype_$mapping_count' id='".$prefix."_relatedtype_$mapping_count' onchange = show_PODS_relational_options(this.id,'".$prefix."','".$mapping_count."','".$import_type."') >";
			/** Add the relational fields **/
			$result .= "<option class = 'addrow_option' style='line-height:5px;' value='--select--'>--Select--</option>";
			foreach($relational_fields as $field_group => $field_list) {
				$result .= "<b><optgroup class = 'addrow_optgrp' style='font-size:14px;line-height:15px;' label='".$field_group."'></optgroup></b>";
				foreach($field_list as $field_key => $field_value) {
					$result .= "<option class = 'addrow_option' style='line-height:5px;' value='".$field_key."'>$field_value</option>";
				}
			}
			/** End relational field options **/
			$result .= "<b><optgroup class = 'addrow_optgrp' style='font-size:14px;line-height:15px;' label='Others ..'></optgroup></b>";
			/** Add the Custom Posts in relational options **/
			$custom_postList = $uci_admin->get_import_custom_post_types();
			foreach($custom_postList as $postList) {
				$result .= "<option class = 'addrow_option' style='line-height:5px;' value='".$postList."'>$postList</option>";
			}
			/** End Custom Post options **/
			/** Add the Taxonomy in relational options **/
			$taxonomy_data = $uci_admin->terms_and_taxonomies($import_type);
			if(!$taxonomy_data) {
				if(isset($taxonomy_data)) {
					foreach($taxonomy_data as $taxonomy_group => $taxonomyList) {
						foreach($taxonomyList as $taxonomy_key => $taxonomy_value) {
							$result .= "<option class = 'addrow_option' style='line-height:5px;' value='".$taxonomy_key."'>$taxonomy_key</option>";
						}
					}
				}
			}
			/** End Taxonomy options **/
			$result .= "</select>";
			$result .= "</tr>";
			/** Add the Custom Defined Options for Relational field **/
			$result .= "<tr style='display:none;' id = '".$prefix."_CDOfd".$mapping_count."'>
			<td><label class='wpfields'>Custom Defined Options</label></td>
			<td><textarea rows='3' cols='55'></textarea></tr>";
			$result .="<tr style='display:none' id = '".$prefix."_bidirecfd".$mapping_count."'>
			<td><label class='wpfields'>Bi-directional Field</label></td>
			<td><select class='addrow_select' name='".$prefix."_bidirec".$mapping_count."' id='".$prefix."_bidirec".$mapping_count."'>
			<option id = '".$prefix."_bidirec_op".$mapping_count."'>No Related Fields Found</option>
			</select></td></tr>";
			/** End Custom Defined Options **/
		}
		if($prefix == 'TYPES') {
			$result .= "<tr style='display:none' id='".$prefix."_trchoice".$mapping_count."'>
			<td class='addrow_column'><label class='wpfields'>Choices</label></td>
			<td class='addrow_column'>";
			$result .="<textarea rows='3' cols='48' name='type_options' id='".$prefix."_type_options".$mapping_count."'></textarea>";
			$result .= "<p style='margin-top:10px;width:90%;border-radius:4px;border:1px solid #ddd;padding:10px;'>HINT: Specify the CHOICES with COMMA operator.<br /> Example: Red,Green,Blue</p>";
			$result .="</td></tr>";
			$result .= "<tr style='display:none;' id = '".$prefix."_ckbox".$mapping_count."'>
			<td><label class='wpfields'>Choices</label></td>
			<td><input class = 'addrow_fds' id='".$prefix."chck_op$mapping_count' type='text' name='".$prefix."chck_op$mapping_count' value='' size=50></td></tr>";
		}
		/** End Choice UI */
		$result .= "<tr class = 'tboddcolor'>
        <td class='addrow_column'><label class='wpfields'>Options</label></td>
        <td class='addrow_column'><input type='checkbox' id = '".$prefix."ui__CustomFieldOption$mapping_count' name='".$prefix."ui__CustomFieldOption$mapping_count' />Required</td></tr>";

		$result .= "<tr>
        <td colspan=2><input type='button' disabled value=".esc_attr__('Register','wp-ultimate-csv-importer-pro')." id='$prefix".'Register'."$mapping_count' style='float:right;margin-top:25px;margin-bottom:10px;' class='btn btn-success' onclick='RegisterCF(\"".$prefix."\",\"".$slug."\",\"".$import_type."\",\"".$mapping_count."\");'>";
		$result .="</td></tr></table>";

		if($mode == "existing_items") {
			$result .= "<td id='".$prefix."_tdc_count".$core_count."' class='' style='width:10%; padding:15px; display:none;'>";
			$result .= "<input type='checkbox' class='' name='".$prefix."_num_".$core_count."' id='".$prefix."_check_".$core_count."'>";
			$result .= "</td>";
		}
		$result .= "<td id='".$prefix."_tdg_count".$core_count."' class='left_align' style='width:20%;padding-top:1.3%;display:none;'>";
		$result .= "<label class='wpfields' name='".$prefix."_CustomField' id='".$prefix.$mapping_count."_CustomField'><input type='textbox' id='".$prefix."__CustomField".$mapping_count."' name='".$prefix."__CustomField".$mapping_count."' value=''></label><br><label id='".$prefix."CustomField$mapping_count' class='samptxt' for = 'bbb'>[Name: CustomField$mapping_count]</label></td>";
		$result .= "<td id='".$prefix."_tdh_count".$core_count."' class='left_align' style='width:20%;display:none;'>";
		$result .= "<span id='".$prefix."_mapping$mapping_count' >";
		$result .= "<div class='select_box' style='width:135px;height:27px;'><div class='mapping-select-div'><select style='width:135px;height:26px;margin-bottom: 10px;' name='".$prefix."__mapping$mapping_count'  id='".$prefix."__mapping$mapping_count' class='selectpicker'>";
		$result .= "<option id='select' value = '--select--'>-- Select --</option>";
		/** Mapping Headers **/
		foreach($Headers as $header_key => $header_value) {
			$result .= "<option value=" . $header_value . ">$header_value</option>";
		}
		$result .= "<option value = 'header_manip'>Header Manipulation</option>";
		$result .= "</select></div></div></span></td>";
		/** End Mapping Headers **/
		$result .= "<td id='".$prefix."_tdd_count".$core_count."' class='left_align' style='width:20%;display:none;'>";
		$result .= "</td>";
		$result .= "<td id='".$prefix."_tdi_count".$core_count."' class='left_align' style='width:30%;display:none;'>";
		$result .= "<div class='mapping-static-formula-group col-md-5'><span title='Static' style='margin-right:33px;' id='".$prefix."_staticbutton_mapping$mapping_count' onclick=static_method(this.id,'".$prefix."','".$mapping_count."')><img src='".plugins_url()."/".SM_UCI_SLUG."/assets/images/static.png' width='24' height='24' /></span>";
		$result .= "<span title='Formula' style='margin-right:15px;' id='".$prefix."_formulabutton_mapping$mapping_count' onclick=formula_method(this.id,'".$prefix."','".$mapping_count."')><img src='".plugins_url()."/".SM_UCI_SLUG."/assets/images/formula.png' width='24' height='24' /></span></div>";
		$result .= "<div  id='".$prefix."_customdispdiv_mapping$mapping_count' class='mapping-select-close-div' style='height:246px;padding:8px;display:none;width:267px;border:3px solid #2ea2cc;margin-top:5px;position:absolute;background-color:#ffffff;z-index: 99;'></div>";
		$result .= "<input type='button' value='Delete' id='".$prefix."Delete".$mapping_count."' style='float:right; display:none;' class='btn btn-danger' onclick=DeleteCF('".$import_type."','".$prefix."',".$core_count.",\"".$slug."\")> ";
		$result .= "</td></tr>";

		print_r($result);
	}
}

function add_corecustomfields($mode, $mapping_count, $prefix, $core_count, $Headers, $slug, $import_type) {
	$result = "<tr id='".$prefix."_tr_count".$core_count."'>";
	if($mode == "existing_items") {
		$result .= "<td id='".$prefix."_tdc_count".$core_count."' class='' style='width:10%; padding:15px;'>";
		$result .= "<input type='checkbox' class='' name='".$prefix."_num_".$core_count."' id='".$prefix."_check_".$core_count."'>";
		$result .= "</td>";
	}
	$result .= "<td id='".$prefix."_tdg_count".$core_count."' class='left_align' style='width:20%'>";
	$result .= "<label class='wpfields' name='".$prefix."_CustomField' id='".$mapping_count."_CustomField'><input type='textbox' class='form-control new_custom_field' style='margin-top:20px;' id='".$prefix."__CustomField$mapping_count' data-key='".$prefix."__mapping$mapping_count' value='' onblur= SetWPRegisterData(this.id,'".$mapping_count."','".$prefix."')></label><br><label id = '".$prefix."CustomField$mapping_count' class='samptxt'>[Name: CustomField$mapping_count]</label>";
	$result .= " <input type='hidden' name='".$prefix."__fieldname". $mapping_count."' id='".$prefix."__fieldname".$mapping_count."' value = '' class='req_hiddenclass'/>";
	$result .= "</td>";
	$result .= "<td class='mappingtd_style' style='width:20%'>";
	$result .= "<span id='".$prefix."_mapping$mapping_count' >";
	$result .= "<div style='width:135px;margin-bottom: 10px;height:27px;'><div class='mapping-select-div'><select class='selectpicker' style='height: 30px;width: 180px;margin-bottom:100px;' disabled name='".$prefix."__mapping$mapping_count'  id='".$prefix."__mapping$mapping_count'>";
	$result .= "<option id='select' value = '--select--'>-- Select --</option>";
	/** Mapping Headers **/
	foreach($Headers as $header_key => $header_value) {
		$result .= "<option value=".$header_value.">$header_value</option>";
	}
	$result .= "<option value = 'header_manip'>Header Manipulation</option>";
	$result .= "</select></div></div></span></td>";
	/** End Mapping Headers **/
	//$result .= "<td class='mappingtd_style'>";
	$result .= "<td class='mappingtd_style' style='padding-left:5%;'><input type='checkbox' class='RegField_iCheck' name='".$prefix."__SerializeVal$mapping_count'>";
	$result .= "</td>";
	$result .= "<td>";
	$result .= "<div class='mapping-static-formula-group col-md-5' style='margin-left: -15px;'><span title='Static' id='".$prefix."_staticbutton_mapping$mapping_count' style='margin-right:15px;' onclick=static_method(this.id,'".$prefix."','".$mapping_count."')><img src='".plugins_url()."/".SM_UCI_SLUG."/assets/images/static.png' width='24' height='24' style='margin-right:15px;' /></span>";
	$result .= "<span title='Formula' style='margin-right:15px;' id='".$prefix."_formulabutton_mapping$mapping_count' onclick=formula_method(this.id,'".$prefix."','".$mapping_count."')><img src='".plugins_url()."/".SM_UCI_SLUG."/assets/images/formula.png' width='24' height='24' /></span></div>";
	$result .= "<div  id='".$prefix."_customdispdiv_mapping$mapping_count' class='mapping-select-close-div' style='height:246px;padding:8px;display:none;width:267px;border:3px solid #2ea2cc;margin-top:5px;position:absolute;background-color:#ffffff;z-index: 99;'></div>";
	$result .= "<input type='button' value='Delete' id='".$prefix."Delete".$mapping_count."' style='float:right; display:none;' class='btn btn-danger' onclick=DeleteCF('".$import_type."','".$prefix."',".$core_count.",\"".$slug."\")> ";
	$result .= "</td></tr>";
	print_r($result);
	die;
}

function add_cf_controls($prefix, $core_count, $mapping_count) {
	global $wpdb;
	$result = '';
	$head = $type = $option = array();
	$k=0;
	$prefix  = strtolower($prefix);
	$field_data = $wpdb->get_results($wpdb->prepare("select fieldType,choices from smack_field_types where groupType = %s",$prefix.'-field-type'));
	foreach($field_data as $field_type){
		$head[$field_type->fieldType] = $field_type->fieldType;
		$type[$k] = $field_type->choices;
		$k++;
		$result .="<b><optgroup class = 'addrow_optgrp' style='font-size:14px;line-height:15px;' label='".$head[$field_type->fieldType]."' ></optgroup></b>";
		foreach($type as $field_control){
			$field_control = unserialize($field_control);
		}
		foreach ($field_control as $cf_control){
			$result .= "<option class = 'addrow_option' style='line-height:5px;' value='".strtolower($cf_control)."'>$cf_control</option>";
		}
	}
	return $result;
}
