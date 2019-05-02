<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if(!defined('ABSPATH'))
	die('Your requested url were wrong! Please contact your admin.');
$group = sanitize_text_field($_REQUEST['group']);
$mapping = sanitize_text_field($_REQUEST['mappingcount']);
$buttonid = sanitize_text_field($_REQUEST['buttonid']);
if ($buttonid == $group.'_staticbutton_mapping'.$mapping) {
	$static = "<div  id='".$group."_staticdiv_mapping$mapping' style='height:auto;'><textarea rows='4' cols='15' id='".$group."_statictext_mapping$mapping' name='".$group."_statictext_mapping$mapping' style='width:100%' onblur=static_validator(this.id);></textarea>";
	$static .= "<p style='margin-top:10px;border-radius:4px;border:1px solid #ddd;padding:10px;'>". __('HINT: Specify the CSV header to be added in between the curley braces({ }).','wp-ultimate-csv-importer-pro')."<br /> ". __('Example: {post_title}','wp-ultimate-csv-importer-pro')."</p>";
	$static .= "<a class='smack-btn mapping-close-btn' onclick=static_formula_divclose('".$group."','".$mapping."') style='float:right;color:red;'> ". __('Close','wp-ultimate-csv-importer-pro'). " </a>";
	$static .="</div>";
	#       $static .="<div class='black_overlay' id='".$group."_blur_mapping$mapping'></div>";
	print_r($static);
	die;
}
else if ($buttonid == $group.'_formulabutton_mapping'.$mapping)  {
	$formula = "<div id='".$group."_formuladiv_mapping$mapping' ><textarea rows='4' cols='15' id='".$group."_formulatext_mapping$mapping' name='".$group."_formulatext_mapping$mapping' style='width:100%' onblur=formula_validator(this.id);></textarea>";
	$formula .= "<p style='margin-top:10px;border-radius:4px;border:1px solid #ddd;padding:7px;'>". __('HINT: Specify operator(+,-,*,/) and CSV header as operand in MATH() within {}.Example: MATH ({product_quantity}/{discount_price})','wp-ultimate-csv-importer-pro')."</p>";
	$formula .= "<a class='smack-btn mapping-close-btn' style='float:right;color:red' onclick=static_formula_divclose('".$group."','".$mapping."')>". __('Close','wp-ultimate-csv-importer-pro')."</a> </div>";
	print_r($formula);
	die;
}
else {

}
