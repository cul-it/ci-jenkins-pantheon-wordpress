<?PHP function wpecommerce_meta_api($template_mapping) { ?>
<!-- WPeCommerce Product -->
<div class="wpecommerce_widget_content ">
   <h4>Product Delivery</h4>
   <div class="col-md-12">
      <div class="wpecommerce_tab mt10">
         <ul class="nav nav-tabs" role="tablist" >
            <li class="active"><a href="#shipping" aria-controls="shipping" role="tab" data-toggle="tab">Shipping</a></li>
            <li ><a href="#download" aria-controls="download" role="tab" data-toggle="tab" >Download</a></li>
            <li ><a href="#external_link" aria-controls="external_link" role="tab" data-toggle="tab">External Link</a></li>
         </ul>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="shipping">
               <div class="col-md-12">
                  <div class="form-group">
                     <label><input type="checkbox" name="ECOMMETA__no_shipping"> Product will not be shipped to customer</label>
                  </div>
                  <h5>Calculate Shipping Costs based on measurements</h5>
                  <div class="form-group mt20">
                     <label class="control-label col-md-2">Weight</label>
                     <div class="col-md-2">
                        <input class="form-control" type="text" name="ECOMMETA__weight" value="<?php if(isset($template_mapping['ECOMMETA']['weight'])) echo $template_mapping['ECOMMETA']['weight']; ?>">
                     </div>
                     <div class="col-md-6">
                        <select name="ECOMMETA__weight_unit" class="selectpicker">
                           <option value="pounds">pounds</option>
                           <option value="ounces">ounces</option>
                           <option value="grams">grams</option>
                           <option value="kilograms">kilograms</option>
                        </select>
                     </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="form-group ">
                     <label class="control-label col-md-2">Dimensions</label>
                     <div class="col-md-5">
                        <ul class="list-dimensions">
                           <li><input class="form-control" type="text" name="length" value="<?php if(isset($template_mapping['ECOMMETA']['length'])) echo $template_mapping['ECOMMETA']['length']; ?>"></li>
                           <li>X</li>
                           <li><input class="form-control" type="text" name="width" value="<?php if(isset($template_mapping['ECOMMETA']['width'])) echo $template_mapping['ECOMMETA']['width']; ?>"></li>
                           <li>X</li>
                           <li><input class="form-control" type="text" name="height" value="<?php if(isset($template_mapping['ECOMMETA']['height'])) echo $template_mapping['ECOMMETA']['height']; ?>"></li>
                        </ul>
                     </div>
                     <div class="col-md-4">
                        <select name="ECOMMETA__dimension_unit" class="selectpicker">
                           <option value="in">inches</option>
                           <option value="cm">cm</option>
                           <option value="meter">meters</option>
                        </select>
                     </div>
                  </div>
                  <div class="clearfix"></div>
                  <h5>Flat Rate Settings</h5>
                  <div class="form-group mt20">
                     <label class="control-label col-md-5 pr0">Local Shipping Fee <span class="pull-right">$</span></label>
                     <div class="col-md-2">
                        <input class="form-control" type="text" name="ECOMMETA__local_shipping" value="<?php if(isset($template_mapping['ECOMMETA']['local_shipping'])) echo $template_mapping['ECOMMETA']['local_shipping']; ?>">
                     </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="form-group ">
                     <label class="control-label col-md-5 pr0">International Shipping Fee<span class="pull-right">$</span></label>
                     <div class="col-md-2">
                        <input class="form-control" type="text" name="ECOMMETA__international_shipping" value="<?php if(isset($template_mapping['ECOMMETA']['international_shipping'])) echo $template_mapping['ECOMMETA']['international_shipping']; ?>">
                     </div>
                  </div>
               </div>
               <div class="clearfix"></div>
            </div> <!-- tabpanel ENd shipping -->
            <div role="tabpanel" class="tab-pane" id="download">
               <div class="col-md-12">
                  <div class="form-group">
                     <div class="col-md-9">
                        <input class="form-control" type="text" name="download_file" value="<?php if(isset($template_mapping['ECOMMETA']['download_file'])) echo $template_mapping['ECOMMETA']['download_file']; ?>">
                     </div>
                     <div class="col-md-1 pl0">
                        <span class="vertical-middle">?</span>
                     </div>
                  </div>
               </div>
               <div class="clearfix"></div>
            </div> <!-- tabpanel ENd hOMe -->
            <div role="tabpanel " class="tab-pane" id="external_link">
               <div class="col-md-12 mt20">
                  <div class="form-group ">
                     <label class="control-label col-md-3">URL</label>
                     <div class="col-md-9">
                        <input class="form-control" type="text" name="ECOMMETA__external_link" value="<?php if(isset($template_mapping['ECOMMETA']['external_link'])) echo $template_mapping['ECOMMETA']['external_link']; ?>">
                     </div>
                  </div>
                  <div class="form-group ">
                     <label class="control-label col-md-3">Label</label>
                     <div class="col-md-9">
                        <input class="form-control" type="text" name="ECOMMETA__external_link_text" value="<?php if(isset($template_mapping['ECOMMETA']['external_link_text'])) echo $template_mapping['ECOMMETA']['external_link_text']; ?>">
                     </div>
                  </div>
                  <div class="form-group wp_ultimate_container">
                     <label class="control-label col-md-3">Target</label>
                     <div class="col-md-9">
                        <!-- <div class="col-md-6 pl0">
                           <label class="radio-text"><input id="target_default" class="wp_ultimate_slide" data-key="false" type="radio" checked="checked" name="ECOMMETA__target"> Default (set by theme)</label>
                           <label class="radio-text"><input id="target_same_window" class="wp_ultimate_slide" data-key="false" type="radio" name="ECOMMETA__target"> Force open in same window</label>
                        </div>
                        <div class="col-md-6 pr0">
                           <label class="radio-text"><input id="target_new_window" class="wp_ultimate_slide" data-key="false"  type="radio" name="ECOMMETA__target"> Force open in new window</label>
                           <label class="radio-text"><input id="target_csv" class="wp_ultimate_slide" data-key="true"  type="radio" name="ECOMMETA__target"> Set from CSV</label>
                        </div>
                     </div>
                                       </div>
                  <div class="clearfix"></div>
                  <div class="form-group mt20">
                     <label class="control-label"><span class="text-hint">This option overrides the "Buy Now" and "Add to Cart" buttons, replacing them with the link you describe here.</span></label>
                     <div class="col-md-8 pl0">
                        <input class="form-control" type="text" name="ECOMMETA__external_link_target">
                     </div> -->
                        <input class="form-control" type="text" name="ECOMMETA__external_link_target" value="<?php if(isset($template_mapping['ECOMMETA']['external_link_target'])) echo $template_mapping['ECOMMETA']['external_link_target']; ?>">
                     </div>
                  </div>
               </div>
               <div class="clearfix"></div>
            </div> <!-- tabpanel ENd external_link -->
         </div>
      </div>
      <!-- wpecommerce_tab ID End -->
   </div>
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <h4>Product Details</h4>
   <div class="col-md-12">
      <div class="wpecommerce_tab mt10">
         <ul class="nav nav-tabs" role="tablist" >
            <li class="active"><a href="#image_gallery" aria-controls="shipping" role="tab" data-toggle="tab">image_gallery</a></li>
            <li ><a href="#short_description" aria-controls="download" role="tab" data-toggle="tab" >short_description</a></li>
            <li ><a href="#personalisation" aria-controls="messages" role="tab" data-toggle="tab">personalisation</a></li>
            <li ><a href="#meta_data" aria-controls="messages" role="tab" data-toggle="tab">meta_data</a></li>
         </ul>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="image_gallery">
               <div class="col-md-12 mt20">
                  <div class="form-group">
                     <input class="form-control droppable" type="text" name="ECOMMETA__image_gallery" value="<?php if(isset($template_mapping['ECOMMETA']['image_gallery'])) echo $template_mapping['ECOMMETA']['image_gallery']; ?>">
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="short_description">
               <div class="col-md-12 mt20">
                  <div class="form-group">
                     <textarea class="form-control droppable" name="ECOMMETA__short_description" value="<?php if(isset($template_mapping['ECOMMETA']['short_description'])) echo $template_mapping['ECOMMETA']['short_description']; ?>"></textarea>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="personalisation">
               <div class="col-md-12 mt20">
                  <label class="control-label"><input class="form-control" type="checkbox" name="ECOMMETA__engraved"> Users can personalize this product by leaving a message on single product page.</label>
                  <label class="control-label"><input class="form-control" type="checkbox" name="ECOMMETA__can_have_uploaded_image"> Users can upload images on single product page to purchase logs.</label>
                  <label class="control-label mt20"><span class="text-hint">Form fields for the customer to personalize this product will be shown on it's single product page.</span></label>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="meta_data">
               <div class="col-md-12 mt20">
                  <div class="table-responsive">
                     <table class="table table-responsive ecommerce-meta-table">
                        <tbody id="ecommerce-meta-textbox">
                           <td><input name="custom_meta_name[]" type="text"  class="form-control" placeholder="Name"/></td>
                           <td><input name="custom_meta_value[]" type="text" class="form-control" placeholder="value"/></td>
                           <td><span class="remove ecommerce-meta-trash"><i class="icon-trash4"></i></span></td>
                        </tbody>
                        <tfoot>
                           <tr>
                              <th colspan="4">
                                 <p id="ecommerce-custom-field-add"  class="smack-btn smack-btn-info"><i class="glyphicon glyphicon-plus-sign"></i>Add Custom Fields</p>
                              </th>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
               </div>
            </div>
         </div>
         <!-- tab-content END -->
      </div>
      <!-- wpecommerce_tab END -->
   </div>
   <!-- col-md-12 END -->
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <h4>Product Pricing</h4>
   <div class="col-md-12">
      <div class="shaded-panel">
         <div class="col-md-12 mt10">
            <div class="form-group">
               <label class="control-label col-md-3 pr5 ">Price <span class="pull-right ">$ </span></label>
               <div class="col-md-3 pl0">
                  <input class="form-control droppable" type="text" name="ECOMMETA__regular_price" id="ECOMMETA__price" value="<?php if(isset($template_mapping['ECOMMETA']['price'])) echo $template_mapping['ECOMMETA']['price']; ?>">
               </div>
               <label class="control-label col-md-3 pr5 ">Sale Price <span class="pull-right ">$ </span></label>
               <div class="col-md-3 pl0">
                  <input type="text" name="ECOMMETA__sale_price" id="ECOMMETA__sale_price" class="form-control droppable" value="<?php if(isset($template_mapping['ECOMMETA']['sale_price'])) echo $template_mapping['ECOMMETA']['sale_price']; ?>">
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
   </div>
   <!-- Product Pricing END -->
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <h4>SKU</h4>
   <div class="col-md-12 wp_ultimate_toggle_container">
         <div class="col-md-12 mt10">
            <div class="form-group">
               <label class="control-label col-md-3">SKU</label>
               <div class="col-md-6">
                  <input class="form-control droppable" type="text" name="ECOMMETA__sku" value="<?php if(isset($template_mapping['ECOMMETA']['sku'])) echo $template_mapping['ECOMMETA']['sku']; ?>">
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="col-md-12">
            <div class="form-group">
               <label class="control-label col-md-12"><input class="wp_ultimate_toggle" class="form-control" type="checkbox" name="ECOMMETA__quantity_limited"> Product has limited stock</label>
            </div>
         </div> <!-- Product has limited stock END -->
         <div class="clearfix"></div>
      <div class="shaded-panel wp_ultimate_toggle_target" style="display: none;">   
         <div class="col-md-12 mt20">
            <div class="form-group">
               <label class="control-label col-md-3">Quantity in stock</label>
               <div class="col-md-6">
                  <input class="form-control" type="text" name="ECOMMETA__quantity" value="<?php if(isset($template_mapping['ECOMMETA']['quantity'])) echo $template_mapping['ECOMMETA']['quantity']; ?>">
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="col-md-12 mt20">
            <div class="form-group">
               
                  <label class="control-label col-md-7 pr0">Notify site owner via email when stock reduces to : </label>
                  <div class="col-md-2">
                  <input class="form-control" type="text" name="ECOMMETA__notify_when_none_left" value="<?php if(isset($template_mapping['ECOMMETA']['notify_when_none_left'])) echo $template_mapping['ECOMMETA']['notify_when_none_left']; ?>">
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="col-md-12 mt20">
            <div class="form-group">
               <label class="control-label col-md-12">When stock reduces to zero:</label>
               <div class="col-md-9 mt10">
                  <label class="control-label"><input type="checkbox" name="ECOMMETA__unpublish_when_none_left" value="<?php echo $template_mapping['ECOMMETA']['unpublish_when_none_left']; ?>">Unpublish product from website</label>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
   </div>
   <!-- SKU END -->
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <h4>Taxes</h4>
   <div class="col-md-12">
      <div class="shaded-panel">
         <div class="col-md-12">
            <div class="form-group">
               <label class="control-label col-md-12"><input class="form-control" type="checkbox" name="ECOMMETA__is_taxable">  Product is exempt from taxation.</label>
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="col-md-12 mt20">
            <div class="form-group">
               <label class="control-label col-md-3">Taxable Amount</label>
               <div class="col-md-6">
                  <input class="form-control droppable" type="text" name="ECOMMETA__taxable_amount" value="<?php if(isset($template_mapping['ECOMMETA']['taxable_amount'])) echo $template_mapping['ECOMMETA']['taxable_amount']; ?>">
               </div>
            </div>
            <label class="control-label col-md-12"><span class="text-hint">Taxable amount in your currency, not percentage of price.</span></label>
         </div>
         <div class="clearfix"></div>
      </div>
   </div>
   <!-- Taxes END -->
   <div class="clearfix"></div>
   <hr class="border-bottom-hr">
   <h4>Custom Meta Info</h4>
   <div class="col-md-12">
      <div class="shaded-panel">
         <div class="col-md-12 mt10">
            <div class="form-group">
               <label class="control-label col-md-3 ">Custom Attribute Name</label>
               <div class="col-md-6">
                  <input class="form-control droppable" type="text" name="ECOMMETA__custom_attr_name" value="<?php if(isset($template_mapping['ECOMMETA']['custom_name'])) echo $template_mapping['ECOMMETA']['custom_name']; ?>">
               </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group">
               <label class="control-label col-md-3 ">Custom Attribute Value</label>
               <div class="col-md-6">
                  <input class="form-control droppable" type="text" name="ECOMMETA__custom_attr_value" value="<?php if(isset($template_mapping['ECOMMETA']['custom_desc'])) echo $template_mapping['ECOMMETA']['custom_desc']; ?>">
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
   </div>
   <!-- Custom Meta Info END -->
   <div class="clearfix"></div>
   <div class="mb20"></div>
</div>
<!-- WPeCommerce Coupons -->
<?PHP } ?>