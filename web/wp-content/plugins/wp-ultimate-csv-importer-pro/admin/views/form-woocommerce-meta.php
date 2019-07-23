<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/**
 * @param $template_mapping
 */
function woocommerce_meta_api($template_mapping) { ?>

	<div class="woocommerce_widget_head col-md-12">
		<div class="main_choise form-group">
			<label for="multiple_product_type_yes" class="col-md-3 control-label">Product Type</label>
			<div class="col-md-6">
				<input type="text" class="form-control droppable" name="ECOMMETA__product_type" value="<?php if(isset($template_mapping['ECOMMETA']['product_type'])) echo $template_mapping['ECOMMETA']['product_type']; ?>">
			</div>
		<!--	<div class="col-md-1">
				<a href="#help" class="wpallimport-help" original-title="The value from CSV should be one of the following: ('simple', 'grouped', 'external', 'variable', 'simple-subscription', 'variable-subscription').">?</a>
			</div>//-->
		</div>
	</div>
	<div class="clearfix"></div>

	<div class="col-md-12 no-padding mt20" style="border-top:1px solid #eee">
		<div class="col-xs-3 no-padding" style=" min-height: 600px; border-right:1px solid #eee;">
			<!-- required for floating -->
			<!-- Nav tabs -->
			<ul class="nav nav-tabs tabs-left">
				<li ><a href="#general" data-toggle="tab"><span class="icon-tools"></span>General</a></li>
				<li ><a href="#inventory" data-toggle="tab"><span class="icon-price-tags"></span>Inventory</a></li>
				<li ><a href="#shipping" data-toggle="tab"><span class="icon-truck"></span>Shipping</a></li>
				<li ><a href="#linked_products" data-toggle="tab"><span class="icon-link2"></span>Linked Products</a></li>
				<li ><a href="#attributes" data-toggle="tab"><span class="icon-note-list2"></span>Attributes</a></li>
				<li ><a href="#variations" data-toggle="tab"><span class="icon-note-list2"></span>Variations</a></li>
				<li class="active"><a href="#advanced" data-toggle="tab"><span class="icon-settings2"></span>Advanced</a></li>
			</ul>
		</div>
		<div class="col-xs-9 no-padding mt20">
			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane woocommerce_uci_api" id="general">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-md-3 control-label">SKU</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__sku" id="ECOMMETA__sku" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['sku'])) echo $template_mapping['ECOMMETA']['sku']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Product URL</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__product_url" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['product_url'])) echo $template_mapping['ECOMMETA']['product_url']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Button Text</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__button_text" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['button_text'])) echo $template_mapping['ECOMMETA']['button_text']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="" style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20">
						<p>Prices should be presented as you would enter them manually in WooCommerce - with no currency symbol.</p>
						<div class="form-group">
							<label class="col-md-3 control-label smaller">Regular Price ($)</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__regular_price" id="ECOMMETA__regular_price" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['regular_price'])) echo $template_mapping['ECOMMETA']['regular_price']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label smaller">Sale Price ($)</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__sale_price" id="ECOMMETA__sale_price" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price'])) echo $template_mapping['ECOMMETA']['sale_price']; ?>">
							</div>
						</div>
						<div class="form-group ">
							<label class="col-md-12 mt5 control-label smaller">Sale Price Dates</label>
							<div class="col-md-12">
								<div class="col-md-6">
									From
									<input type="text" name="ECOMMETA__sale_price_dates_from" id="ECOMMETA__sale_price_dates_from" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price_dates_from'])) echo $template_mapping['ECOMMETA']['sale_price_dates_from']; ?>">
								</div>
								<div class="col-md-6">
									To
									<input type="text" name="ECOMMETA__sale_price_dates_to" id="ECOMMETA__sale_price_dates_to" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price_dates_to'])) echo $template_mapping['ECOMMETA']['sale_price_dates_to']; ?>">
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20 wp_ultimate_container" >
						<div class="form-group">
							<label class="mr10">
								<input type="radio" checked="checked" id="is_virtual_yes" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_virtual" value="yes">Virtual</label>
							<label class="mr10">
								<input type="radio" id="is_virtual_no" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_virtual" value="no">Not Virtual</label>
							<label>
								<input type="radio" id="is_virtual_csv" data-key="true" class="wp_ultimate_slide" name="ECOMMETA__is_virtual" value="csv_value">Set from CSV</label>
						</div>
						<div class="form-group set_from_csv source-is_virtual_csv" style="display: none;">
							<input type="text" name="ECOMMETA__virtual" id="ECOMMETA__virtual" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['virtual'])) echo $template_mapping['ECOMMETA']['virtual']; ?>">
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20 wp_ultimate_container">
						<div class="form-group">
							<label class="mr10">
								<input type="radio" checked id="is_downloadable_yes" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_downloadable" value="yes">Downloadable</label>
							<label class="mr10">
								<input type="radio" id="is_downloadable_no"  data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_downloadable" value="no">Not Downloadable</label>
							<label>
								<input type="radio" id="is_downloadable_csv" data-key="true" class="wp_ultimate_slide" name="ECOMMETA__is_downloadable" value="csv_value">Set from CSV</label>
						</div>
						<div class="form-group set_from_csv source-is_downloadable_csv" style="display: none;">
							<input type="text" name="ECOMMETA__downloadable" id="ECOMMETA__downloadable" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['downloadable'])) echo $template_mapping['ECOMMETA']['downloadable']; ?>">
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20">
						<div class="wp_csv_ftp form-group">
							<label class="col-md-3 control-label">File names</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__file_name" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['file_name'])) echo $template_mapping['ECOMMETA']['file_name']; ?>">
							</div>
						</div>
						<div class="wp_csv_ftp form-group">
							<label class="col-md-3 control-label">File Paths</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__file_paths" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['file_paths'])) echo $template_mapping['ECOMMETA']['file_paths']; ?>">
							</div>
						</div>
						<div class="wp_csv_ftp form-group">
							<label class="col-md-3 control-label">Download Limit</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__download_limit" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['download_limit'])) echo $template_mapping['ECOMMETA']['download_limit']; ?>">
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group">
							<label class="col-md-3 control-label">Download Expiry</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__download_expiry" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['download_expiry'])) echo $template_mapping['ECOMMETA']['download_expiry']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20 ">
						<div class="form-group">
							<label class="col-md-4 control-label"><input type="radio" id="is_download_type" data-select="true" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_download_type" value="yes" checked="checked">Download Type</label>
							<div class="col-md-8">
								<select name="ECOMMETA__download_type" class="selectpicker">
									<option value="">Standard Product</option>
									<option value="application">Application/Software</option>
									<option value="music">Music</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12 mt15">
						<div class="form-group">
							<label class="col-md-12 control-label">
								<input type="radio" data-select="false" data-key="true" id="is_download_type"  class="wp_ultimate_slide" name="ECOMMETA__is_download_type" value="csv_value">Set from CSV</label>
							<div class="col-md-8 set_from_csv source-is_download_type" style="display:none;" >
								<input type="text" name="ECOMMETA__download_type" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['download_type'])) echo $template_mapping['ECOMMETA']['download_type']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<hr class="border-bottom-hr">
					</hr>
					<div class="wp_ultimate_container">
						<div class="col-md-12 mt15">
							<div class="form-group">
								<label class="col-md-12 control-label">
									<input type="radio" id="is_tax_status_yes" data-select="true" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_tax_status" value="yes" checked="checked">Tax Status</label>
								<div class="col-md-12 select-is_tax_status_yes slide_select">
									<select name="ECOMMETA__tax_status" class="selectpicker">
										<option value="none">None</option>
										<option value="taxable">Taxable</option>
										<option value="shipping">Shipping only</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-12 mt15">
							<div class="form-group">
								<label class="col-md-12 control-label">
									<input type="radio" data-select="false" data-key="true" id="is_tax_status_csv"  class="wp_ultimate_slide" name="ECOMMETA__is_tax_status" value="csv_value">Set from CSV</label>
								<div class="col-md-8 set_from_csv source-is_tax_status_csv" style="display:none;" >
									<input type="text" name="ECOMMETA__tax_status" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['tax_status'])) echo $template_mapping['ECOMMETA']['tax_status']; ?>">
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<hr class="border-bottom-hr">
					</hr>
					<div class="wp_ultimate_container">
						<div class="col-md-12 mt15">
							<div class="form-group">
								<label class="col-md-12 control-label"><input type="radio" id="is_tax_class_yes" data-key="false" data-select="true" class="wp_ultimate_slide" name="ECOMMETA__is_tax_class" value="yes">Tax Class</label>
								<div class="col-md-12 select-is_tax_class_yes slide_select">
									<select name="ECOMMETA__tax_class" class="selectpicker">
										<option value="">Standard</option>
										<option value="reduced-rate">Reduced Rate</option>
										<option value="zero-rate">Zero Rate</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-12 mt15">
							<div class="form-group">
								<label class="col-md-12 control-label"><input type="radio" id="is_tax_class_csv" data-key="true" data-select="false" class="wp_ultimate_slide" name="ECOMMETA__is_tax_class" value="csv_value">Set from CSV</label>
								<div class="col-md-8 set_from_csv source-is_tax_class_csv" style="display: none;">
									<input type="text" name="ECOMMETA__tax_class" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['tax_class'])) echo $template_mapping['ECOMMETA']['tax_class']; ?>">
								</div>
							</div>
						</div>
						<div class="col-md-12 mt15">
							<div class="form-group">
								<input type="text" placeholder="Product Image Gallery" name="ECOMMETA__product_image_gallery" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['product_image_gallery'])) echo $template_mapping['ECOMMETA']['product_image_gallery']; ?>">
							</div>
						</div>
					</div>
				</div>
				<!-- General Tab Ends -->
				<div class="tab-pane " id="inventory">
					<div class="col-md-12 wp_ultimate_container">
						<div class="col-md-12">
							<label class="control-label mr10">Manage Stock?</label>
						</div>
						<div class="col-md-10">
							<label class="control-label pr10"><input type="radio" checked="checked" data-key="false" class="wp_ultimate_slide" id="is_manage_stock_yes" name="ECOMMETA__is_manage_stock" value="yes">Yes</label>
							<label class="control-label pr10"><input type="radio" data-key="false" class="wp_ultimate_slide" id="is_manage_stock_no" name="ECOMMETA__is_manage_stock" value="no">No</label>
							<label class="control-label ml10"><input type="radio" data-key="true" class="wp_ultimate_slide" id="is_manage_stock_csv" name="ECOMMETA__is_manage_stock" value="csv_value">Set from CSV</label>
						</div>
						<div class="col-md-8 mt10 source-is_manage_stock_csv set_from_csv" style="display: none;">
							<input type="text" name="ECOMMETA__manage_stock" id="manage_stock" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['manage_stock'])) echo $template_mapping['ECOMMETA']['manage_stock']; ?>">
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="" style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20" id="manage_stock_qty">
						<div class="form-group">
							<label class="col-md-3 control-label">Stock Qty</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__stock_qty" id="stock_qty" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['stock_qty'])) echo $template_mapping['ECOMMETA']['stock_qty']; ?>">
								<hr style="display: none;">
							</div>
						</div>
					</div>
					<div class="col-md-12 wp_ultimate_container">
						<div class="col-md-12">
							<label class="control-label mr20">Stock Status</label>
						</div>
						<div class="col-md-10">
							<label class="control-label pr10"><input type="radio" checked data-key="false" class="wp_ultimate_slide" id="is_in_stock"  name="ECOMMETA__is_stock_status" value="1"><span class="pt5">In stock</span></label>
							<label class="control-label pr10"><input type="radio" id="is_out_of_stock" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_stock_status" value="0">Out of stock</label>
							<label class="control-label"><input type="radio" id="is_stock_status_csv" data-key="true" class="wp_ultimate_slide" name="ECOMMETA__is_stock_status" value="csv_value">Set from CSV</label>
						</div>
						<div class="col-md-8 mt10 set_from_csv source-is_stock_status_csv" style="display: none;">
							<input type="text" name="ECOMMETA__stock_status" id="ECOMMETA__stock_status"  class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['stock_status'])) echo $template_mapping['ECOMMETA']['stock_status']; ?>">
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 wp_ultimate_container">
						<div class="col-md-12 mt20 mb10">
							<label class="pt5">Allow Backorders?</label>
						</div>
						<div class="mt20">
							<div class="col-md-6">
								<label><input type="radio" checked id="is_do_not_allow" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_allow_backoders" value="0">Do not allow</label>
								<label><input type="radio" id="is_allow_notify" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_allow_backoders" value="1">Allow, but notify customer</label>
							</div>
							<div class="col-md-6">
								<label><input type="radio" id="is_allow_backoders" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_allow_backoders" value="2">Allow</label><br>
								<label><input type="radio" id="is_allow_backoders_csv" data-key="true" class="wp_ultimate_slide" name="ECOMMETA__is_allow_backoders" value="csv_value">Set from CSV</label>
							</div>
						</div>
						<div class="col-md-8 mt10 set_from_csv source-is_allow_backoders_csv" style="display: none;">
							<input type="text" name="ECOMMETA__backorders" id="ECOMMETA__backorders" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['backorders'])) echo $template_mapping['ECOMMETA']['backorders']; ?>">
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 wp_ultimate_container">
						<div class="col-sm-12 mt20 mb10">
							<label class="control-label">Sold Individually?</label></div>
						<div class="col-sm-12">
							<label class="control-label pr10"><input type="radio"  id="is_sold_individually_yes" data-key="true" class="wp_ultimate_slide" name="ECOMMETA__is_sold_individually" value="1" checked>Yes</label>
							<label class="control-label pr10"><input type="radio"  id="is_sold_individually_no" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_sold_individually" value="0">No</label>
							<label class="control-label"><input type="radio" checked="none" id="is_sold_individually_csv" data-key="false" class="wp_ultimate_slide" name="ECOMMETA__is_sold_individually" value="csv_value">Set from CSV</label>
						</div>
						<div class="col-sm-12 mt10 set_from_csv source-is_sold_individually_csv" style="display: none;">
							<input type="text" name="ECOMMETA__sold_individually" id="ECOMMETA__sold_individually" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['sold_individually'])) echo $template_mapping['ECOMMETA']['sold_individually']; ?>">
						</div>
					</div>

				</div> <!-- Inventory Tab Ends -->
				<div class="tab-pane " id="shipping">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-md-3 control-label">Weight (lbs)</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__weight" id="ECOMMETA__weight" placeholder="0.00" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['weight'])) echo $template_mapping['ECOMMETA']['weight']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20">
						<div class="form-group">
							<label class="col-md-3 control-label">Dimensions (in)</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__length" id="ECOMMETA__length" placeholder="Length" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['length'])) echo $template_mapping['ECOMMETA']['length']; ?>">
								<input type="text" name="ECOMMETA__width" id="ECOMMETA__width" placeholder="Width" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['width'])) echo $template_mapping['ECOMMETA']['width']; ?>">
								<input type="text" name="ECOMMETA__height" id="ECOMMETA__height" placeholder="Height" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['height'])) echo $template_mapping['ECOMMETA']['height']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 wp_ultimate_container">
						<div class="form-group">
							<label class="col-md-12 mt10 control-label">Shipping Class</label>
							<div class="col-md-8 mt10 set_from_csv source-is_shipping_class_csv">
								<input type="text" name="ECOMMETA__product_shipping_class" id="ECOMMETA__product_shipping_class" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['product_shipping_class'])) echo $template_mapping['ECOMMETA']['product_shipping_class']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
				</div> <!-- Shipping Tab Ends -->
				<div class="tab-pane" id="linked_products">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-md-3 control-label">Up-Sells</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__upsell_ids" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['upsell_ids'])) echo $template_mapping['ECOMMETA']['upsell_ids']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Cross-Sells</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__crosssell_ids" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['crosssell_ids'])) echo $template_mapping['ECOMMETA']['crosssell_ids']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20">
						<div class="form-group">
							<label class="col-md-3 control-label">Grouping Product</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__grouping_product" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['grouping_product'])) echo $template_mapping['ECOMMETA']['grouping_product']; ?>">
							</div>
						</div>
					</div>
				</div> <!-- linked_products Tab Ends -->
				<div class="tab-pane" id="attributes">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-md-3 control-label">Name</label>
							<div class="col-md-9">
								<input type="text"  name="ECOMMETA__product_attribute_name" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['product_attribute_name'])) echo $template_mapping['ECOMMETA']['product_attribute_name']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Values</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__product_attribute_value" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['product_attribute_value'])) echo $template_mapping['ECOMMETA']['product_attribute_value']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20">
						<div class="form-group">
							<label class="col-md-3 control-label">Is Visible</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__product_attribute_visible" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['product_attribute_visible'])) echo $template_mapping['ECOMMETA']['product_attribute_visible']; ?>">
							</div>
						</div>
					</div>

					</div> <!-- attributes Tab Ends -->
					<div class="tab-pane" id="variations">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-md-3 control-label">Default Attributes</label>
							<div class="col-md-9">
								<input type="text"  name="ECOMMETA__default_attributes" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['default_attributes'])) echo $template_mapping['ECOMMETA']['default_attributes']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Custom Attributes</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__custom_attributes" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['custom_attributes'])) echo $template_mapping['ECOMMETA']['custom_attributes']; ?>">
							</div>
						</div>
					</div>
					</div> <!-- Variations Tab Ends -->

					<div class="tab-pane active" id="advanced">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-md-3 control-label">Purchase Note</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__purchase_note" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['purchase_note'])) echo $template_mapping['ECOMMETA']['purchase_note']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20">
						<div class="form-group">
							<label class="col-md-3 control-label">Menu Order</label>
							<div class="col-md-9">
								<input type="text" name="ECOMMETA__menu_order" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['menu_order'])) echo $template_mapping['ECOMMETA']['menu_order']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 mt20 wp_ultimate_container" >
						<div class="form-group">
							<label class="col-md-12 control-label">Enable Reviews</label>
							<div class="col-md-12">
								<label class="mr10"><input data-key="false" class="wp_ultimate_slide" type="radio" name="ECOMMETA__is_enable_review" id="is_enable_review_yes" value="yes" checked="checked">Yes</label>
								<label class="mr10"><input data-key="false" class="wp_ultimate_slide" id="is_enable_review_no"  type="radio" name="ECOMMETA__is_enable_review" value="no">No</label>
								<label><input class="wp_ultimate_slide" data-key="true" type="radio" name="ECOMMETA__is_enable_review" id="is_enable_review_csv" value="csv"> Set from CSV	</label>
							</div>
							<div class="col-md-8 source-is_enable_review_csv set_from_csv" style="display: none;">
								<input type="text" name="ECOMMETA__comment_status" id="ECOMMETA__enable_reviews" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['comment_status'])) echo $template_mapping['ECOMMETA']['comment_status']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 wp_ultimate_container">
						<div class="form-group">
							<label class="control-label col-md-12">Featured</label>
							<div class="col-md-12">
								<label><input type="radio" class="wp_ultimate_slide" id="is_featured_yes"  data-key="false" name="ECOMMETA__product_featured" value="yes" checked="checked">Yes</label>
								<label><input type="radio" class="wp_ultimate_slide" id="is_featured_no" data-key="false" name="ECOMMETA__product_featured" value="no">No</label>
								<label><input type="radio" class="wp_ultimate_slide" id="is_featured_csv" data-key="true" name="ECOMMETA__product_featured" value="csv_value">Set from CSV</label>
							</div>
							<div class="col-md-8 set_from_csv source-is_featured_csv" style="display: none;">
								<input type="text" name="ECOMMETA__featured" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['featured'])) echo $template_mapping['ECOMMETA']['featured']; ?>">
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div style="border-bottom: 1px solid #eee;"></div>
					<div class="col-md-12 wp_ultimate_container">
						<div class="form-group" >
							<label class="control-label col-md-12">Catalog visibility</label>
							<div class="mt20">
								<div class="col-md-6">
									<label><input type="radio" data-key="false" class="wp_ultimate_slide" id="is_catalog_search" name="ECOMMETA__catalog_visibility" value="visible" checked="checked"> Catalog/search</label><br>
									<label><input type="radio" data-key="false" class="wp_ultimate_slide" id="is_catalog" name="ECOMMETA__catalog_visibility" value="catalog"> Catalog</label><br>
									<label><input type="radio" data-key="false" class="wp_ultimate_slide" id="is_search" name="ECOMMETA__catalog_visibility" value="search"> Search</label>
								</div>
								<div class="col-md-6">
									<label><input type="radio" data-key="false" class="wp_ultimate_slide" id="is_hidden" name="ECOMMETA__catalog_visibility" value="hidden"> Hidden</label><br>
									<label><input type="radio" id="is_catalog_set_from_csv" data-key="true" class="wp_ultimate_slide" name="ECOMMETA__catalog_visibility" value="csv_value"> Set from CSV</label>
								</div>
							</div>
							<div class="col-md-8 mt10 set_from_csv source-is_catalog_set_from_csv" style="display: none;">
								<input type="text" name="ECOMMETA__visibility" class="droppable form-control" value="<?php if(isset($template_mapping['ECOMMETA']['visibility'])) echo $template_mapping['ECOMMETA']['visibility']; ?>">
							</div>
						</div>
					</div>
				</div> <!-- advanced Tab Ends -->
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
<?php }
