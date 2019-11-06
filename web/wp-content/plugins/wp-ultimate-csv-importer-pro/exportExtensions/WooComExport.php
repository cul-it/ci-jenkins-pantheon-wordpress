<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
namespace Smackcoders\WCSV;

if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

/**
 * Class WooCommerceExport
 * @package Smackcoders\WCSV
 */
class WooCommerceExport {

	protected static $instance = null,$mapping_instance,$export_handler,$export_instance,$post_export;
	private $offset = 0;	
	private $limit = 1000;	
	public $totalRowCount;	
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			WooCommerceExport::$export_instance = ExportExtension::getInstance();
			WooCommerceExport::$post_export = PostExport::getInstance();
		}
		return self::$instance;
	}

	/**
	 * WooCommerceExport constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 * Export woocommerce orders
	 * @param $id
	 * @param $type
	 * @param $optional
	 * @return bool
	 */
	public function getWooComOrderData($id, $type, $optional)
	{
	//	return true;
		global $wpdb;
		$orderid = self::$export_instance->data[$id]['ID'];
		$table1 = $wpdb->prefix."woocommerce_order_items";
		$table2 = $wpdb->prefix."woocommerce_order_itemmeta";
		$query = $wpdb->prepare("SELECT * FROM $table1 where order_id = %d", $orderid);
		$result = $wpdb->get_results($query);
		$order_itemname = $order_itemtype = $product_id = $var_id = $line_subtotal = $line_subtotal_tax = $line_total_tax = $line_total = $line_tax_data = $qty = $tx_cls = "";
		$feename = $feetype = $fee_subtotal = $fee_subtotal_tax = $fee_total_tax = $fee_total = $fee_tax_data = $fee_tx_cls = "";
		$shipname = $method_id = $cost = $taxes = "";
		if(!empty($result)){
			foreach ($result as $key => $value) {
				$orderitem = $value->order_item_id;
				if($value->order_item_type != 'fee' && $value->order_item_type != 'shipping' && $value->order_item_type != ' fee'){
					$order_itemname .= $value->order_item_name.',';
					$order_itemtype .= $value->order_item_type.',';
				}
				if($value->order_item_type == 'fee'){
					$feename .= $value->order_item_name.',';
					$feetype .= $value->order_item_type.',';
				}
				if($value->order_item_type == 'shipping'){
					$shipname .= $value->order_item_name.',';
				}
				$query2 = $wpdb->prepare("SELECT * FROM $table2 where order_item_id = %d", $orderitem);
				$result2 = $wpdb->get_results($query2);
				foreach ($result2 as $key2 => $value2) {
					if($value->order_item_type != 'fee' && $value->order_item_type != 'shipping' && $value->order_item_type != ' fee' ){
						if($value2->meta_key == '_product_id'){
							$product_id .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_variation_id'){
							$var_id .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_subtotal'){
							$line_subtotal .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_subtotal_tax'){
							$line_subtotal_tax .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_total'){
							$line_total .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_tax'){
							$line_total_tax .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_tax_data'){
							$line_tax_data .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_qty'){
							$qty .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_tax_class'){
							$tx_cls .= $value2->meta_value.',';
						}
					}
					if($value->order_item_type == 'fee'){
						if($value2->meta_key == '_line_subtotal'){
							$fee_subtotal .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_subtotal_tax'){
							$fee_subtotal_tax .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_total'){
							$fee_total .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_tax'){
							$fee_total_tax .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_line_tax_data'){
							$fee_tax_data .= $value2->meta_value.',';
						}
						if($value2->meta_key == '_tax_class'){
							$fee_tx_cls .= $value2->meta_value.',';
						}
					}
					if($value->order_item_type == 'shipping'){
						if($value2->meta_key == 'method_id'){
							$method_id .= $value2->meta_value.',';
						}
						if($value2->meta_key == 'cost'){
							$cost .= $value2->meta_value.',';
						}
						if($value2->meta_key == 'taxes'){
							$taxes .= $value2->meta_value.',';
						}
					}
				}
			}
		}
		//itemdata
		self::$export_instance->data[$id]['item_name'] = substr($order_itemname, 0, -1);
		self::$export_instance->data[$id]['item_type'] = substr($order_itemtype, 0, -1);
		self::$export_instance->data[$id]['item_product_id'] = substr($product_id, 0, -1);
		self::$export_instance->data[$id]['item_variation_id'] = substr($var_id, 0, -1);
		self::$export_instance->data[$id]['item_line_subtotal'] = substr($line_subtotal, 0, -1);
		self::$export_instance->data[$id]['item_line_subtotal_tax'] = substr($line_subtotal_tax, 0, -1);
		self::$export_instance->data[$id]['item_line_total'] = substr($line_total, 0, -1);
		self::$export_instance->data[$id]['item_line_tax'] = substr($line_total_tax, 0, -1);
		self::$export_instance->data[$id]['item_line_tax_data'] = substr($line_tax_data, 0, -1);	
		self::$export_instance->data[$id]['item_qty'] = substr($qty, 0, -1);	
		self::$export_instance->data[$id]['item_tax_class'] = substr($tx_cls, 0, -1);
		//fee data
		self::$export_instance->data[$id]['fee_name'] = substr($feename, 0, -1);
		self::$export_instance->data[$id]['fee_type'] = substr($feetype, 0, -1);
		self::$export_instance->data[$id]['fee_line_subtotal'] = substr($fee_subtotal, 0, -1);
		self::$export_instance->data[$id]['fee_line_subtotal_tax'] = substr($fee_subtotal_tax, 0, -1);
		self::$export_instance->data[$id]['fee_line_total'] = substr($fee_total, 0, -1);
		self::$export_instance->data[$id]['fee_line_tax'] = substr($fee_total_tax, 0, -1);
		self::$export_instance->data[$id]['fee_line_tax_data'] = substr($fee_tax_data, 0, -1);	
		self::$export_instance->data[$id]['fee_tax_class'] = substr($fee_tx_cls, 0, -1);
		// shipment data
		self::$export_instance->data[$id]['shipment_name'] = substr($shipname, 0, -1);
		self::$export_instance->data[$id]['shipment_method_id'] = substr($method_id, 0, -1);
		self::$export_instance->data[$id]['shipment_cost'] = substr($cost, 0, -1);	
		self::$export_instance->data[$id]['shipment_taxes'] = substr($taxes, 0, -1);
	}

	/**
	 * Code for Woocommerce Refund export
	 * @param $id
	 * @param $type
	 * @param $optionalType
	 */
	public function getWooComCustomerUser($id, $type, $optionalType)
	{
		global $wpdb;
		$parent = WooCommerceExport::$export_instance->data[$id]['post_parent'];
		$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->prefix}postmeta where post_id = %d", $parent);
		$result = $wpdb->get_results($query);
		if(!empty($result)){
			foreach ($result as $key => $value) {
				if($value->meta_key == '_customer_user'){
					$cus_user = $value->meta_value;
				}
			}
		}
		WooCommerceExport::$export_instance->data[$id]['customer_user'] = $cus_user;
	}

	/**
	 * Export woocommerce product and variation
	 * @param $id
	 * @param $type
	 * @param $optionalType
	 */
	public function getProductData($id, $type, $optionalType)
	{
		global $wpdb;

		if($type == 'WooCommerce'){	
			$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->prefix}postmeta where post_id = %d", $id);	
			$result = $wpdb->get_results($query);

			$term_relationship_arr = [];
			$term_relationship = $wpdb->get_results("SELECT term_taxonomy_id FROM {$wpdb->prefix}term_relationships WHERE object_id = $id");
			foreach($term_relationship as $relationship_value){
				$term_relationship_arr[] = $relationship_value->term_taxonomy_id;
			}

		}else{
			$productid = WooCommerceExport::$export_instance->data[$id]['post_parent'];
			$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->prefix}postmeta where post_id = %d", $productid);
			$result = $wpdb->get_results($query);

			$term_relationship_arr = [];
			$term_relationship = $wpdb->get_results("SELECT term_taxonomy_id FROM {$wpdb->prefix}term_relationships WHERE object_id = $productid");
			foreach($term_relationship as $relationship_value){
				$term_relationship_arr[] = $relationship_value->term_taxonomy_id;
			}
		}

		$attname = $attvalue = $attvisible = $attvar = $attpos = "";
		$attlabel = $atttaxo = "";
		if(!empty($result)){
			foreach ($result as $key => $value) {
				
				if($value->meta_key == '_product_attributes'){
					$attArray = unserialize($value->meta_value);
					foreach ($attArray as $key1 => $value1) {
							$pa_value = get_term( $value1['name'] );   
							$result = $wpdb->get_results("SELECT term_id FROM {$wpdb->prefix}term_taxonomy where taxonomy = '{$value1['name']}'",ARRAY_A);
							$attvalues = '';
							foreach ($result as $key => $value) {
								$value = $value['term_id'];

								if(in_array($value , $term_relationship_arr)){
									$querys = $wpdb->get_results("SELECT name FROM {$wpdb->prefix}terms where  term_id =$value ",ARRAY_A);
									if(!empty($querys['0']['name'])){
										$attvalues .= $querys['0']['name'].'|';
									}
								}
							}
						$attvalue .= rtrim($attvalues , '|') . ',';				
						$get_all_taxonomies =  $wpdb->get_results($query);

						$attriname = substr($value1['name'] , 3);
						$attr_name = $wpdb->get_var("SELECT attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '$attriname' ");
						
						$attname .= $attr_name.'|';
						$attvisible .= $value1['is_visible'].'|';
						$attvar .= $value1['is_variation'].'|';
						$attpos .= $value1['position'].'|';
						$atttaxo .= $value1['is_taxonomy'].'|';

						$attlabel .= $value1['name'].'|';
					}
				}	
				if($value->meta_key == '_low_stock_amount'){
					WooCommerceExport::$export_instance->data[$id]['low_stock_threshold'] = $value->meta_value; 
				}
				if($value->meta_key == '_sku'){
					WooCommerceExport::$export_instance->data[$id]['PARENTSKU'] = $value->meta_value; 
				}
				if($value->meta_key == '_sku'){
					WooCommerceExport::$export_instance->data[$id]['PARENTSKU'] = $value->meta_value; 
				}
				if($value->meta_key == '_thumbnail_id'){
					WooCommerceExport::$export_instance->data[$id]['thumbnail_id'] = $value->meta_value; 
				}
				if($value->meta_key == '_stock'){
					WooCommerceExport::$export_instance->data[$id]['stock_qty'] = $value->meta_value; 
				}

				if($value->meta_key == '_default_attributes'){
					$defaultArr = unserialize($value->meta_value);
					$defAttr = "";
					foreach($defaultArr as $defKey => $defVal){
						$defkey = substr($defKey , 3);
						$def_key = $wpdb->get_var("SELECT attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '$defkey' ");
						$def_val = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}terms WHERE slug = '$defVal' ");
						$defAttr .= $def_key.'|'.$def_val.','; 
					}
					WooCommerceExport::$export_instance->data[$id]['default_attributes'] = substr($defAttr, 0, -1);
				}

			}
		}
		WooCommerceExport::$export_instance->data[$id]['product_attribute_name'] = substr($attname, 0, -1);
		WooCommerceExport::$export_instance->data[$id]['product_attribute_value'] = substr($attvalue, 0, -1);
		WooCommerceExport::$export_instance->data[$id]['product_attribute_visible'] = substr($attvisible, 0, -1);
		WooCommerceExport::$export_instance->data[$id]['product_attribute_variation'] = substr($attvar, 0, -1);
		WooCommerceExport::$export_instance->data[$id]['product_attribute_position'] = substr($attpos, 0, -1);
		WooCommerceExport::$export_instance->data[$id]['product_attribute_taxonomy'] = substr($atttaxo, 0, -1);

		$cus = explode('|', substr($attlabel, 0, -1));
		$cusAttr = "";
		foreach ($cus as $cus1) {
			$name = 'attribute_'.$cus1;
			$custname = substr($cus1 , 3);
			$cust_name = $wpdb->get_var("SELECT attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '$custname' ");
			//$cusAttr .= $cust_name.'|'.WooCommerceExport::$export_instance->data[$id][$name].','; 

			$slug = WooCommerceExport::$export_instance->data[$id][$name];
			$cust_val = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}terms WHERE slug = '$slug' ");
			$cusAttr .= $cust_name.'|'.$cust_val.','; 
		}
		WooCommerceExport::$export_instance->data[$id]['custom_attributes'] = substr($cusAttr, 0, -1);

		$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->postmeta where post_id = %d", $id);
		$result = $wpdb->get_results($query);
		if(!empty($result)){
			foreach ($result as $key => $value) {
				if($value->meta_key == '_sale_price_dates_from'){
					WooCommerceExport::$export_instance->data[$id]['sale_price_dates_from'] =  date('Y-m-d', $value->meta_value);
				}
				if($value->meta_key == '_sale_price_dates_to'){
					WooCommerceExport::$export_instance->data[$id]['sale_price_dates_to'] =  date('Y-m-d', $value->meta_value);
				}
				if($value->meta_key == '_downloadable_files'){
					$downfiles = unserialize($value->meta_value);
					$down_file = '';
					foreach($downfiles as $dk => $dv){
						$down_file .= $dv['name'].','.$dv['file'].'|';
					}
					WooCommerceExport::$export_instance->data[$id]['downloadable_files'] = rtrim($down_file,"|");
				}
				if($value->meta_key == '_variation_description'){
					WooCommerceExport::$export_instance->data[$id]['description'] = $value->meta_value;
				}

				if($value->meta_key == '_sku'){
					WooCommerceExport::$export_instance->data[$id]['sku'] = $value->meta_value;
				}
				
			}
		}
		
		if($type == 'WooCommerce'){
			$product = new \WC_Product($id);	
			$get_catalog_visibility = $product->get_catalog_visibility();
			if($get_catalog_visibility == 'visible'){
				WooCommerceExport::$export_instance->data[$id]['visibility'] = '1';
			}
			elseif($get_catalog_visibility == 'catalog'){
				WooCommerceExport::$export_instance->data[$id]['visibility'] = '2';
			}
			elseif($get_catalog_visibility == 'search'){
				WooCommerceExport::$export_instance->data[$id]['visibility'] = '3';
			}
			elseif($get_catalog_visibility == 'hidden'){
				WooCommerceExport::$export_instance->data[$id]['visibility'] = '4';
			}
			$result = $wpdb->get_results("SELECT *
					FROM {$wpdb->prefix}terms as t
					JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id
					JOIN {$wpdb->prefix}term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
					WHERE t.name = 'featured'
					AND tr.object_id = $id");
			if(!empty($result)){
				WooCommerceExport::$export_instance->data[$id]['featured_product'] = '1';
			}
		}else{
			$var_product = new \WC_Product_Variation($id);
			$var_class_id = $var_product->get_shipping_class_id();
			$var_class = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}terms WHERE term_id = $var_class_id ");
			WooCommerceExport::$export_instance->data[$id]['variation_shipping_class'] = $var_class;
		}

		$product_type = WooCommerceExport::$export_instance->data[$id]['product_type'];
		if($product_type == 'simple'){
			WooCommerceExport::$export_instance->data[$id]['product_type'] = '1';
		}elseif($product_type == 'grouped'){
			WooCommerceExport::$export_instance->data[$id]['product_type'] = '2';
		}elseif($product_type == 'external'){
			WooCommerceExport::$export_instance->data[$id]['product_type'] = '3';
		}elseif($product_type == 'variable'){
			WooCommerceExport::$export_instance->data[$id]['product_type'] = '4';
		}
	}

	/**
	 * Fetch Terms & Taxonomies
	 * @param $mode
	 * @param $module
	 * @param $optionalType
	 * @return array
	 */
	public function FetchTaxonomies($mode = null,$module,$optionalType) {
		
		global $wpdb;
		$terms_table = $wpdb->prefix."terms";
		$terms_taxo_table = $wpdb->prefix."term_taxonomy";
		$events_meta_table = $wpdb->prefix."em_meta";
		self::$export_instance->generateHeaders($module, $optionalType);
		$taxonomy = $optionalType;
		// $query = 'SELECT * FROM wp_terms t INNER JOIN wp_term_taxonomy tax 
		// 	ON  `tax`.term_id = `t`.term_id WHERE ( `tax`.taxonomy = \'' . $taxonomy . '\')'; 
		
		$query = $wpdb->prepare("SELECT * FROM $terms_table t INNER JOIN $terms_taxo_table tax ON `tax`.term_id = `t`.term_id WHERE `tax`.taxonomy = %s ", $taxonomy);
			                 
					$get_all_taxonomies =  $wpdb->get_results($query);
					
					self::$export_instance->totalRowCount = count($get_all_taxonomies);
					if(!empty($get_all_taxonomies)) {
						foreach( $get_all_taxonomies as $termKey => $termValue ) {
							$termID = $termValue->term_id;
							$termMeta = get_term_meta($termID);
							
							//wpsc_meta data starts
							if(in_array('wp-e-commerce/wp-shopping-cart.php', self::$export_instance->get_active_plugins())) {
								$wpsc_query = $wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}wpsc_meta where object_id = %d AND object_type = %s", $termID, 'wpsc_category');
								$wpsc_meta = $wpdb->get_results($wpsc_query,ARRAY_A);
								foreach($wpsc_meta as $mk => $mv){
									if($mv['meta_key'] == 'image'){
										if($mv['meta_value']){
											$udir = wp_upload_dir();
											$img_path = $udir['baseurl'] . "/wpsc/category_images/".$mv['meta_value'];
											self::$export_instance->data[$termID]['category_image'] = $img_path; 
										}else{
											self::$export_instance->data[$termID]['category_image'] = ''; 
										}
									}elseif($mv['meta_key'] == 'display_type'){
										self::$export_instance->data[$termID]['catelog_view'] = $mv['meta_value'];
									}elseif($mv['meta_key'] == 'uses_billing_address'){
										self::$export_instance->data[$termID]['address_calculate'] = $mv['meta_value'];                                           			       }elseif($mv['meta_key'] == 'image_width'){
										self::$export_instance->data[$termID]['category_image_width'] = $mv['meta_value'];                                                                      }elseif($mv['meta_key'] == 'image_height'){
										self::$export_instance->data[$termID]['category_image_height'] = $mv['meta_value'];                                                                     }else{
										self::$export_instance->data[$termID][$mv['meta_key']] = $mv['meta_value'];
									}
								}
							}
							//wpsc_meta data ends
							//woocommerce meta data starts
							if(in_array('woocommerce/woocommerce.php', self::$export_instance->get_active_plugins())){
								if(isset($termMeta['thumbnail_id'][0])){
									$thum_id = $termMeta['thumbnail_id'][0];
									self::$export_instance->data[$termID]['image'] = self::$export_instance->getAttachment($thum_id);
								}
								if(!empty($termMeta['display_type'][0])){
									self::$export_instance->data[$termID]['display_type'] = $termMeta['display_type'][0];
								}
								if(!empty($termMeta['cat_meta'][0])){
									$cat_meta = unserialize($termMeta['cat_meta'][0]);
									self::$export_instance->data[$termID]['top_content'] = $cat_meta['cat_header'];
									self::$export_instance->data[$termID]['bottom_content'] = $cat_meta['cat_footer'];
								}
							}
							//woocommerce meta data ends
							$termName = $termValue->name;
							$termSlug = $termValue->slug;
							$termDesc = $termValue->description;
							$termParent = $termValue->parent;
							if($termParent == 0) {
								self::$export_instance->data[$termID]['name'] = $termName;
							} else {
								$termParentName = get_cat_name( $termParent );
								self::$export_instance->data[$termID]['name'] = $termParentName . '|' . $termName;
							}
							self::$export_instance->data[$termID]['slug'] = $termSlug;
							self::$export_instance->data[$termID]['description'] = $termDesc;
							self::$export_instance->data[$termID]['TERMID'] = $termID;
							self::$export_instance->data[$termID]['parent'] = $termParent;

							WooCommerceExport::$post_export->getPostsMetaDataBasedOnRecordId($termID, $module, $optionalType);

							if($optionalType == 'event-categories'){
								$get_cat_color = $wpdb->get_var("SELECT meta_value FROM $events_meta_table WHERE meta_key = 'category-bgcolor' AND object_id = $termID ");
								$get_cat_image = $wpdb->get_var("SELECT meta_value FROM $events_meta_table WHERE meta_key = 'category-image' AND object_id = $termID ");
								self::$export_instance->data[$termID]['color'] = $get_cat_color;
								self::$export_instance->data[$termID]['image'] = $get_cat_image;
							}
							if($optionalType == 'event-tags'){
								$get_tag_color = $wpdb->get_var("SELECT meta_value FROM $events_meta_table WHERE meta_key = 'tag-bgcolor' AND object_id = $termID ");
								$get_tag_image = $wpdb->get_var("SELECT meta_value FROM $events_meta_table WHERE meta_key = 'tag-image' AND object_id = $termID ");
								self::$export_instance->data[$termID]['color'] = $get_tag_color;
								self::$export_instance->data[$termID]['image'] = $get_tag_image;
							}

							if(in_array('wordpress-seo/wp-seo.php', self::$export_instance->get_active_plugins())) {
								$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
								if ( isset( $seo_yoast_taxonomies[$optionalType] ) ) {
										self::$export_instance->data[ $termID ]['title'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_title'];
										self::$export_instance->data[ $termID ]['meta_desc'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_desc'];
										self::$export_instance->data[ $termID ]['canonical'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_canonical'];
										self::$export_instance->data[ $termID ]['bctitle'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_bctitle'];
										self::$export_instance->data[ $termID ]['meta-robots-noindex'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_noindex'];
										self::$export_instance->data[ $termID ]['sitemap-include'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_sitemap_include'];
										self::$export_instance->data[ $termID ]['opengraph-title'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_opengraph-title'];
										self::$export_instance->data[ $termID ]['opengraph-description'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_opengraph-description'];
										self::$export_instance->data[ $termID ]['opengraph-image'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_opengraph-image'];
										self::$export_instance->data[ $termID ]['twitter-title'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_twitter-title'];
										self::$export_instance->data[ $termID ]['twitter-description'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_twitter-description'];
										self::$export_instance->data[ $termID ]['twitter-image'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_twitter-image'];
										self::$export_instance->data[ $termID ]['focus_keyword'] = $seo_yoast_taxonomies[$optionalType][$termID]['wpseo_focuskw'];
									
								}
							}
							
						}
					}
					
					$result = self::$export_instance->finalDataToExport(self::$export_instance->data , $module);

					if($mode == null)
						self::$export_instance->proceedExport($result);
					else
						return $result;
	}

}
