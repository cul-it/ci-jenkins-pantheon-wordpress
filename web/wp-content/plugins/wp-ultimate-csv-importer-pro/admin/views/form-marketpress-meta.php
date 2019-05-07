<?php function marketpress_lite_meta_api($template_mapping) { ?>
<!-- MarketPress Lite -->
<div class="marketpress_lite_widget_content col-md-12 no-padding">
   <div class="clearfix"></div>
   <!--<hr class="border-bottom-hr"> -->
   <div class="col-md-12 mt10">
       <div class="form-group">
           <label class="col-md-3">Variation Name</label>
           <div class="col-md-6">
               <input type="text" class="form-control droppable" name="ECOMMETA__variation" id="ECOMMETA__variation" value="<?php if(isset($template_mapping['ECOMMETA']['variation'])) echo $template_mapping['ECOMMETA']['variation']; ?>">
           </div>
       </div>
   </div>
   <div class="col-md-12">
       <table class="table table-responsive mt20">
           <tr>
               <th>SKU</th>
               <th>Price</th>
               <th>
                   <label>
                       <input type="checkbox" name="ECOMMETA__is_sale">Sale Price
                   </label>
               </th>
               <th>
                   <label>
                       <input type="checkbox" name="ECOMMETA__track_inventory">Inventory</label>
               </th>
           </tr>
           <tr>
               <td>
                   <input type="text" class="form-control droppable" name="ECOMMETA__sku" id="ECOMMETA__sku" value="<?php if(isset($template_mapping['ECOMMETA']['sku'])) echo $template_mapping['ECOMMETA']['sku']; ?>">
               </td>
               <td>
                   <input type="text" class="form-control droppable" name="ECOMMETA__regular_price" id="ECOMMETA__regular_price" value="<?php if(isset($template_mapping['ECOMMETA']['regular_price'])) echo $template_mapping['ECOMMETA']['regular_price']; ?>">
               </td>
               <td>
                   <input type="text" class="form-control droppable" name="ECOMMETA__sale_price" id="ECOMMETA__sale_price" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price'])) echo $template_mapping['ECOMMETA']['sale_price']; ?>">
               </td>
               <td>
                   <input type="text" class="form-control droppable" name="ECOMMETA__inventory" id="ECOMMETA__inventory" value="<?php if(isset($template_mapping['ECOMMETA']['inventory'])) echo $template_mapping['ECOMMETA']['inventory']; ?>">
               </td>
           </tr>
       </table>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12">
       <div class="form-group">
           <label class="control-label col-md-12">External Link: <span style="width:100%;">When set this overrides the purchase button with a link to this URL.</span>
           </label>
           <div class="col-md-6">
               <input type="text" name="ECOMMETA__product_link" class="form-control droppable" value="<?php if(isset($template_mapping['ECOMMETA']['product_link'])) echo $template_mapping['ECOMMETA']['product_link']; ?>">
           </div>
       </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 no-padding wp_ultimate_toggle_container">
       <div class="form-group">
           <div class="col-md-12 p10">
               <label class="control-label col-md-12">
                   <input type="checkbox" class="wp_ultimate_toggle" name="ECOMMETA__is_special_tax" id="ECOMMETA__is_special_tax">Special Tax Rate?</label>
           </div>
           <div class="col-md-11 ml30 shaded-panel wp_ultimate_toggle_target" style="display: none;">
               <div class="col-md-8">
                   <label class="control-label">Rate: </label>
                   <input type="text" class="form-control droppable" name="ECOMMETA__special_tax" value="<?php if(isset($template_mapping['ECOMMETA']['special_tax'])) echo $template_mapping['ECOMMETA']['special_tax']; ?>">
               </div>
           </div>
       </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 no-padding wp_ultimate_toggle_container">
       <div class="form-group">
           <div class="col-md-12 p10">
               <label class="control-label col-md-12">
                   <input type="checkbox" class="wp_ultimate_toggle" name="ECOMMETA__track_limit" id="ECOMMETA__track_limit">Limit Per Order?</label>
           </div>
           <div class="col-md-11 ml30 shaded-panel wp_ultimate_toggle_target" style="display: none;">
               <div class="col-md-8">
                   <label class="control-label">Limit: </label>
                   <input type="text" name="ECOMMETA__limit_per_order" class="form-control droppable" value="<?php if(isset($template_mapping['ECOMMETA']['limit_per_order'])) echo $template_mapping['ECOMMETA']['limit_per_order']; ?>">
               </div>
           </div>
       </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12">
       <div class="form-group">
           <label class="control-label col-md-3">Extra Shipping Cost:</label>
           <div class="col-md-9">
               <input type="text" name="ECOMMETA__extra_shipping_cost" class="form-control droppable" value="<?php if(isset($template_mapping['ECOMMETA']['extra_shipping_cost'])) echo $template_mapping['ECOMMETA']['extra_shipping_cost']; ?>">
           </div>
       </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 ">
       <div class="form-group">
           <label class="control-label col-md-12"><span>Product Download</span></label>
           <label class="col-md-3 control-label">File URL:</label>
           <div class="col-md-9">
               <input type="text" name="ECOMMETA__file_url" class="form-control droppable" value="<?php if(isset($template_mapping['ECOMMETA']['file_url'])) echo $template_mapping['ECOMMETA']['file_url']; ?>">
           </div>
       </div>
   </div>
   </div>
<!-- MarketPress Lite END -->
<?php }
function marketpress_premium_meta_api($template_mapping) { ?>
<!-- MarketPress Premium -->
<div class="marketpress_premium_widget_content">
   <div class="col-md-12">
      <div class="table-responsive">
        <table class="table ">
           <tr>
              <th>Inventory</th>
              <th>Price</th>
              <th>Sale Price</th>
           </tr>
           <tr>
              <td><input type="text" class="form-control" name="ECOMMETA__inventory" id="ECOMMETA__inventory" value="<?php if(isset($template_mapping['ECOMMETA']['inventory'])) echo $template_mapping['ECOMMETA']['inventory']; ?>"></td>
              <td><input type="text" class="form-control" name="ECOMMETA__regular_price" id="ECOMMETA__regular_price" value="<?php if(isset($template_mapping['ECOMMETA']['regular_price'])) echo $template_mapping['ECOMMETA']['regular_price']; ?>"></td>
              <td><input type="text" class="form-control" name="ECOMMETA__sale_price" id="ECOMMETA__sale_price" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price'])) echo $template_mapping['ECOMMETA']['sale_price']; ?>"></td>
           </tr>
        </table>
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 mb20">
      <div class="form-group" id="marketpress_variation">
         <div class="col-md-12">
            <label class="control-label">Variation Name</label>
            <input type="text" class="form-control" name="ECOMMETA__variation_name" id="ECOMMETA__variation_name" value="<?php if(isset($template_mapping['ECOMMETA']['variation_name'])) echo $template_mapping['ECOMMETA']['variation_name']; ?>">
         </div>
         <div class="col-md-12">
            <label class="control-label">Variation Value</label>
            <input type="text" class="form-control" name="ECOMMETA__variation_value" id="ECOMMETA__variation_value" value="<?php if(isset($template_mapping['ECOMMETA']['variation_value'])) echo $template_mapping['ECOMMETA']['variation_value']; ?>">
         </div>
         <div class="col-md-12">
            <label class="control-label">Variation Image</label>
            <input type="text" class="form-control" name="ECOMMETA__variation_image" id="ECOMMETA__variation_image" value="<?php if(isset($template_mapping['ECOMMETA']['variation_image'])) echo $template_mapping['ECOMMETA']['variation_image']; ?>">
         </div>
         <div class="col-md-12 mb10 mt10">
            <label class="control-label"><input type="checkbox" name="ECOMMETA__has_variation_content">Additional Content / Information for this Variation</label>
            <select name="ECOMMETA__variation_content_type" class="selectpicker">
               <option value="html">html</option>
               <option value="plain">plain</option>
            </select>
         </div>
         <div class="col-md-12">
            <div class="mt10">
               <input type="text" class="form-control" name="ECOMMETA__variation_content_desc" value="<?php if(isset($template_mapping['ECOMMETA']['variation_content_desc'])) echo $template_mapping['ECOMMETA']['variation_content_desc']; ?>">
            </div>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12">
      <label class="control-label col-md-12">SKU <span>(Stock Keeping Unit)</span></label>
      <div class="col-md-8">
         <input type="text" name="ECOMMETA__sku" id="ECOMMETA__sku" value="<?php if(isset($template_mapping['ECOMMETA']['sku'])) echo $template_mapping['ECOMMETA']['sku']; ?>" class="form-control">
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12  wp_ultimate_toggle_container">
      <label class="col-md-12"><input type="checkbox" class="wp_ultimate_toggle" name="ECOMMETA__track_limit">Limit the Amount of Items per Order</label>
      <div class="col-md-8 wp_ultimate_toggle_target" style="display: none;">
         <label class="control-label">Limit Per Order:</label>
         <input type="text" name="ECOMMETA__limit_per_order" value="<?php if(isset($template_mapping['ECOMMETA']['limit_per_order'])) echo $template_mapping['ECOMMETA']['limit_per_order']; ?>" class="form-control">
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 no-padding wp_ultimate_toggle_container">
      <div class="form-group">
         <div class="col-md-12">
            <label class="control-label col-md-12">
            <input type="checkbox" class="wp_ultimate_toggle" name="ECOMMETA__has_sale"> Set up a Sale for this Product</label>
         </div>
         <div class="col-md-11 ml30 shaded-panel wp_ultimate_toggle_target" style="display: none;">
            <div class="col-md-4">
               <label class="control-label">Percentage Discount</label>
               <input type="text" name="ECOMMETA__sale_price_percentage" class="form-control" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price_percentage'])) echo $template_mapping['ECOMMETA']['sale_price_percentage']; ?>">
            </div>
            <div class="col-md-4">
               <label class="control-label">Start Date (if applicable)</label>
               <input type="text" name="ECOMMETA__sale_price_start_date" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price_start_date'])) echo $template_mapping['ECOMMETA']['sale_price_start_date']; ?>" class="form-control">
            </div>
            <div class="col-md-4">
               <label class="control-label">End Date (if applicable)</label>
               <input type="text" name="ECOMMETA__sale_price_end_date" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price_end_date'])) echo $template_mapping['ECOMMETA']['sale_price_end_date']; ?>" class="form-control">
            </div>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 no-padding wp_ultimate_toggle_container">
      <div class="form-group">
         <div class="col-md-12">
            <label class="control-label col-md-12">
               <input type="checkbox" class="wp_ultimate_toggle" name="ECOMMETA__charge_tax" id="ECOMMETA__charge_tax">Charge Taxes <span>(Special Rate)</span>
         </div>
         <div class="col-md-11 ml30 shaded-panel wp_ultimate_toggle_target" style="display: none;">
         <div class="col-md-3">
         <label class="control-label">Pounds:</label>
         <input type="text" name="ECOMMETA__weight_pounds" value="<?php if(isset($template_mapping['ECOMMETA']['weight_pounds'])) echo $template_mapping['ECOMMETA']['weight_pounds']; ?>" class="form-control">
         </div>
         <div class="col-md-3">
         <label class="control-label">Ounces:</label>
         <input type="text" name="ECOMMETA__weight_ounces" value="<?php if(isset($template_mapping['ECOMMETA']['weight_ounces'])) echo $template_mapping['ECOMMETA']['weight_ounces']; ?>" class="form-control">
         </div>
         <div class="col-md-6">
         <label class="control-label">Extra Shipping Cost (if applicable) </label>
         <input type="text" name="ECOMMETA__charge_shipping" value="<?php if(isset($template_mapping['ECOMMETA']['charge_shipping'])) echo $template_mapping['ECOMMETA']['charge_shipping']; ?>" class="form-control">
         </div>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12">
      <label class="control-label col-md-12">Related Products</label>
      <div class="col-md-8">
         <input type="text" name="ECOMMETA__related_products" value="<?php if(isset($template_mapping['ECOMMETA']['related_products'])) echo $template_mapping['ECOMMETA']['related_products']; ?>" class="form-control">
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 shaded">
      <label class="control-label ml10"><input type="checkbox" name="ECOMMETA__is_featured"> Is Featured? </label>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12 no-padding wp_ultimate_toggle_container">
      <div class="form-group">
         <div class="col-md-12 p10">
            <label class="control-label col-md-12">
               <input type="checkbox" class="wp_ultimate_toggle" name="ECOMMETA__inventory_tracking"> Track Product Inventory 
         </div>
         <div class="col-md-11 ml30 shaded-panel wp_ultimate_toggle_target" style="display: none;">
           <div class="col-md-3">
           <label>Quantity: </label>
           <input type="text" name="ECOMMETA__quantity" value="<?php if(isset($template_mapping['ECOMMETA']['quantity'])) echo $template_mapping['ECOMMETA']['quantity']; ?>" class="form-control">
           </div>
           <div class="col-md-9">
           <label class="pad-top-30-laptop"><input type="checkbox" name="ECOMMETA__inv_out_of_stock_purchase">Allow this product to be purchased even if it's out of stock</label>
           </div>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12">
      <div class="form-group">
        <label>External Link:</label>
          <label>When set this overrides the purchase button with a link to this URL.</label>
          <div class="col-md-8"></div>
          <input type="text" name="ECOMMETA__external_url" value="<?php if(isset($template_mapping['ECOMMETA']['external_url'])) echo $template_mapping['ECOMMETA']['external_url']; ?>" class="form-control">
      </div>
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <div class="col-md-12">
      <label>Product Download</label>
        <label>File URL:</label>
        <input type="text" name="ECOMMETA__file_url" value="<?php if(isset($template_mapping['ECOMMETA']['file_url'])) echo $template_mapping['ECOMMETA']['file_url']; ?>" class="form-control">
   </div>
</div>
<!-- MarketPress Premium END -->
<?php } ?>