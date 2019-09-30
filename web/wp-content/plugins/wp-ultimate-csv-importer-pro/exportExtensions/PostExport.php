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
 * Class PostExport
 * @package Smackcoders\WCSV
 */
class PostExport {

	protected static $instance = null,$mapping_instance,$export_handler,$export_instance;
	public $offset = 0;	
	public $limit;
	public $totalRowCount;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$export_instance = ExportExtension::getInstance();
		}
		return self::$instance;
	}

	/**
	 * PostExport constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 * Get records based on the post types
	 * @param $module
	 * @param $optionalType
	 * @param $conditions
	 * @return array
	 */
	public function getRecordsBasedOnPostTypes ($module, $optionalType, $conditions ,$offset , $limit) {
		global $wpdb;
		if($module == 'CustomPosts') {
			$module = $optionalType;
		} elseif ($module == 'WooCommerceOrders') {
			$module = 'shop_order';
		}
		elseif ($module == 'Marketpress') {
			$module = 'product';
		}
		elseif ($module == 'WooCommerceCoupons') {
			$module = 'shop_coupon';
		}
		elseif ($module == 'WooCommerceRefunds') {
			$module = 'shop_order_refund';
		}
		elseif ($module == 'WooCommerceVariations') {
			$module = 'product_variation';
		}
		elseif($module == 'WPeCommerceCoupons'){
			$module = 'wpsc-coupon';
		}
		else {
			$module = self::import_post_types($module);
		}

		$get_post_ids = "select DISTINCT ID from {$wpdb->prefix}posts";
		$get_post_ids .= " where post_type = '$module'";

		/**
		 * Check for specific status
		 */
		if($module == 'shop_order'){
			if(!empty($conditions['specific_status']['status'])) {
				if($conditions['specific_status']['status'] == 'All') {
					$get_post_ids .= " and post_status in ('wc-completed','wc-cancelled','wc-refunded','wc-on-hold','wc-processing','wc-pending')";
				} elseif($conditions['specific_status']['status'] == 'Completed Orders') {
					$get_post_ids .= " and post_status in ('wc-completed')";
				} elseif($conditions['specific_status']['status'] == 'Cancelled Orders') {
					$get_post_ids .= " and post_status in ('wc-cancelled')";
				} elseif($conditions['specific_status']['status'] == 'On Hold Orders') {
					$get_post_ids .= " and post_status in ('wc-on-hold')";
				} elseif($conditions['specific_status']['status'] == 'Processing Orders') {
					$get_post_ids .= " and post_status in ('wc-processing')";
				} elseif($conditions['specific_status']['status'] == 'Pending Orders') {
					$get_post_ids .= " and post_status in ('wc-pending')";
				} 
			} else {
				$get_post_ids .= " and post_status in ('wc-completed','wc-cancelled','wc-on-hold','wc-processing','wc-pending')";
			}
		}elseif ($module == 'shop_coupon') {
			if(!empty($conditions['specific_status']['status'])) {
				if($conditions['specific_status']['status'] == 'All') {
					$get_post_ids .= " and post_status in ('publish','draft','pending')";
				} elseif($conditions['specific_status']['status']== 'Publish') {
					$get_post_ids .= " and post_status in ('publish')";
				} elseif($conditions['specific_status']['status'] == 'Draft') {
					$get_post_ids .= " and post_status in ('draft')";
				} elseif($conditions['specific_status']['status'] == 'Pending') {
					$get_post_ids .= " and post_status in ('pending')";
				} 
			} else {
				$get_post_ids .= " and post_status in ('publish','draft','pending')";
			}

		}elseif ($module == 'shop_order_refund') {

		}
		else {
			if(!empty($conditions['specific_status']['status'])) {
				if($conditions['specific_status']['status'] == 'All') {
					$get_post_ids .= " and post_status in ('publish','draft','future','private','pending')";
				} elseif($conditions['specific_status']['status'] == 'Publish' || $conditions['specific_status']['status'] == 'Sticky') {
					$get_post_ids .= " and post_status in ('publish')";
				} elseif($conditions['specific_status']['status'] == 'Draft') {
					$get_post_ids .= " and post_status in ('draft')";
				} elseif($conditions['specific_status']['status'] == 'Scheduled') {
					$get_post_ids .= " and post_status in ('future')";
				} elseif($conditions['specific_status']['status'] == 'Private') {
					$get_post_ids .= " and post_status in ('private')";
				} elseif($conditions['specific_status']['status'] == 'Pending') {
					$get_post_ids .= " and post_status in ('pending')";
				} elseif($conditions['specific_status']['status'] == 'Protected') {
					$get_post_ids .= " and post_status in ('publish') and post_password != ''";
				}
			} else {
				$get_post_ids .= " and post_status in ('publish','draft','future','private','pending')";
			}
		}
		// Check for specific period
		if(!empty($conditions['specific_period']['is_check']) && $conditions['specific_period']['is_check'] == 'true') {
			if($conditions['specific_period']['from'] == $conditions['specific_period']['to']){
				$get_post_ids .= " and post_date >= '" . $conditions['specific_period']['from'] . "'";
			}else{
				$get_post_ids .= " and post_date >= '" . $conditions['specific_period']['from'] . "' and post_date <= '" . $conditions['specific_period']['to'] . "'";
			}
		}
		if($module == 'eshop')
			$get_post_ids .= " and pm.meta_key = '_eshop_product'";
		if($module == 'woocommerce')
			$get_post_ids .= " and pm.meta_key = '_sku'";
		// if($module == 'marketpress')
		// 	$get_post_ids .= " and pm.meta_key = 'mp_sku'";
		if($module == 'wpcommerce')
			$get_post_ids .= " and pm.meta_key = '_wpsc_sku'";

		// Check for specific authors
		if(!empty($conditions['specific_authors']['is_check'] == '1')) {
			if(isset($conditions['specific_authors']['author'])) {
				$get_post_ids .= " and post_author = {$conditions['specific_authors']['author']}";
			}
		}
		//WpeCommercecoupons
		if($module == 'wpsc-coupon'){
			$get_post_ids = "select DISTINCT ID from {$wpdb->prefix}wpsc_coupon_codes";
		}
		//WpeCommercecoupons
		$get_total_row_count = $wpdb->get_col($get_post_ids);
		self::$export_instance->totalRowCount = count($get_total_row_count);
		$offset = self::$export_instance->offset;
		$limit = self::$export_instance->limit;
		$offset_limit = " order by ID asc limit $offset, $limit";
		$query_with_offset_limit = $get_post_ids . $offset_limit;
		$result = $wpdb->get_col($query_with_offset_limit);
		// Get sticky post alone on the specific post status
		if(isset($conditions['specific_period']['is_check']) && $conditions['specific_status']['is_check'] == 'true') {
			if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'Sticky') {
				$get_sticky_posts = get_option('sticky_posts');
				foreach($get_sticky_posts as $sticky_post_id) {
					if(in_array($sticky_post_id, $result))
						$sticky_posts[] = $sticky_post_id;
				}
				return $sticky_posts;
			}
		}
		return $result;
	}

	public function import_post_types($import_type, $importAs = null) {	
		$import_type = trim($import_type);
		$module = array('Posts' => 'post', 'Pages' => 'page', 'Users' => 'user', 'Comments' => 'comments', 'Taxonomies' => $importAs, 'CustomerReviews' =>'wpcr3_review', 'Categories' => 'categories', 'Tags' => 'tags', 'eShop' => 'post', 'WooCommerce' => 'product', 'WPeCommerce' => 'wpsc-product','WPeCommerceCoupons' => 'wpsc-product', 'Marketpress' => 'product', 'MarketPressVariations' => 'mp_product_variation','WooCommerceVariations' => 'product', 'WooCommerceOrders' => 'product', 'WooCommerceCoupons' => 'product', 'WooCommerceRefunds' => 'product', 'CustomPosts' => $importAs);
		foreach (get_taxonomies() as $key => $taxonomy) {
			$module[$taxonomy] = $taxonomy;
		}
		if(array_key_exists($import_type, $module)) {
			return $module[$import_type];
		}
		else {
			return $import_type;
		}
	}

	/**
	 * Function to export the meta information based on Fetch ACF field information to be export
	 * @param $id
	 * @return mixed
	 */
	public function getPostsMetaDataBasedOnRecordId ($id, $module, $optionalType) {
		
		global $wpdb;
		if($module == 'Users'){
			$query = $wpdb->prepare("SELECT user_id,meta_key,meta_value FROM {$wpdb->prefix}users wp JOIN {$wpdb->prefix}usermeta wpm ON wpm.user_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d", '_edit_lock', '_edit_last', $id);
		}else if($module == 'Categories' || $module == 'Taxonomies' || $module == 'Tags'){
			//$query = $wpdb->prepare("SELECT term_id,meta_key,meta_value FROM {$wpdb->prefix}terms wp JOIN {$wpdb->prefix}termmeta wpm ON wpm.term_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d", '_edit_lock', '_edit_last', $id);
			$query = $wpdb->prepare("SELECT wp.term_id,meta_key,meta_value FROM {$wpdb->prefix}terms wp JOIN {$wpdb->prefix}termmeta wpm ON wpm.term_id = wp.term_id where meta_key NOT IN (%s,%s) AND wp.term_id = %d", '_edit_lock', '_edit_last', $id);
		}else{
			$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->prefix}posts wp JOIN {$wpdb->prefix}postmeta wpm ON wpm.post_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d", '_edit_lock', '_edit_last', $id);
		}

		$get_acf_fields = $wpdb->get_results("SELECT ID, post_excerpt, post_content, post_name, post_parent, post_type FROM {$wpdb->prefix}posts where post_type = 'acf-field'", ARRAY_A);

		$group_unset = array('customer_email', 'product_categories', 'exclude_product_categories');

		if(!empty($get_acf_fields)){
			foreach ($get_acf_fields as $key => $value) {
				
				if(!empty($value['post_parent'])){
					$parent = get_post($value['post_parent']);
					if(!empty($parent)){
						if($parent->post_type == 'acf-field'){
							$allacf[$value['post_excerpt']] = $parent->post_excerpt.'_'.$value['post_excerpt']; 
						}else{
							$allacf[$value['post_excerpt']] = $value['post_excerpt']; 	
						}
					}else{
						$allacf[$value['post_excerpt']] = $value['post_excerpt']; 
					}
				}else{
					$allacf[$value['post_excerpt']] = $value['post_excerpt']; 
				}
		
				self::$export_instance->allacf = $allacf;

				$content = unserialize($value['post_content']);
				$alltype[$value['post_excerpt']] = $content['type'];
				
				if($content['type'] == 'repeater' || $content['type'] == 'flexible_content'){
					$checkRep[$value['post_excerpt']] = $this->getRepeater($value['ID']);
				}else{
					$checkRep[$value['post_excerpt']] = "";
				}
			}
		}
	   
		self::$export_instance->allpodsfields = $this->getAllPodsFields();

		if($module == 'Categories' || $module == 'Tags' || $module == 'Taxonomies'){
			self::$export_instance->alltoolsetfields = get_option('wpcf-termmeta');
		}
		elseif($module == 'Users'){
			self::$export_instance->alltoolsetfields = get_option('wpcf-usermeta');
		}
		else{
			self::$export_instance->alltoolsetfields = get_option('wpcf-fields');
		}
		

		if(!empty(self::$export_instance->alltoolsetfields)){
			$i = 1;
			foreach (self::$export_instance->alltoolsetfields as $key => $value) {
				$typesf[$i] = 'wpcf-'.$key;
				$typeOftypesField[$typesf[$i]] = $value['type']; 
				$i++;
			}
		}
		self::$export_instance->typeOftypesField = $typeOftypesField;
		
		$result = $wpdb->get_results($query);
		
		if(!empty($result)) {

			foreach($result as $key => $value) {
				$this->getCustomFieldValue($id, $value, $checkRep, $allacf, $typeOftypesField, $alltype, $parent, $typesf, $group_unset , $optionalType , self::$export_instance->allpodsfields);
			}
		}
		return self::$export_instance->data;
	}

	public function getAllPodsFields(){
		
		$pods_fields = [];
		if(in_array('pods/init.php', self::$export_instance->get_active_plugins())) {
			global $wpdb;
			$pods_fields_query_result = $wpdb->get_results("SELECT post_name FROM ".$wpdb->prefix."posts WHERE post_type = '_pods_field'");	
			foreach($pods_fields_query_result as $single_result){
				$pods_fields[] = $single_result->post_name;
			}
		}
		return $pods_fields;
	}

	public function getCustomFieldValue($id, $value, $checkRep, $allacf, $typeOftypesField, $alltype, $parent, $typesf, $group_unset , $optionalType , $pods_type){
		
		global $wpdb;
		$taxonomies = get_taxonomies();
		$down_file = false;
		
		if ($value->meta_key == '_thumbnail_id') {
			$attachment_file = null;
			$get_attachment = $wpdb->prepare("select guid from {$wpdb->prefix}posts where ID = %d AND post_type = %s", $value->meta_value, 'attachment');
			$attachment_file = $wpdb->get_var($get_attachment);
			self::$export_instance->data[$id][$value->meta_key] = '';
			$value->meta_key = 'featured_image';
			self::$export_instance->data[$id][$value->meta_key] = $attachment_file;
		}else if($value->meta_key == '_downloadable_files'){ 
			$downfiles = unserialize($value->meta_value); 
			foreach($downfiles as $dk => $dv){
				$down_file .= $dv['name'].','.$dv['file'].'|';
			}
			self::$export_instance->data[$id]['downloadable_files'] = rtrim($down_file,"|");
		}
		elseif($value->meta_key == '_upsell_ids'){
			$upselldata = unserialize($value->meta_value);
			$upsellids = implode(',',$upselldata);
			self::$export_instance->data[$id]['upsell_ids'] =  $upsellids;
		}
		elseif($value->meta_key == '_crosssell_ids'){
			$cross_selldata = unserialize($value->meta_value);
			$cross_sellids = implode(',',$cross_selldata);
			self::$export_instance->data[$id]['crosssell_ids'] =  $cross_sellids;
		}
		elseif($value->meta_key == '_children'){
			$grpdata = unserialize($value->meta_value);
			$grpids = implode(',',$grpdata);
			self::$export_instance->data[$id]['grouping_product'] =  $grpids;
		}elseif($value->meta_key == '_product_image_gallery'){
			if(strpos($value->meta_value, ',') !== false) {
				$file_data = explode(',',$value->meta_value);
				foreach($file_data as $k => $v){
					$attachment = wp_get_attachment_image_src($v);
					$attach[$k] = $attachment[0];
				}
				foreach($attach as $values){
					$gallery_data .= $values.'|';
				}
				$gallery_data = rtrim($gallery_data , '|');

				self::$export_instance->data[$id]['product_image_gallery'] = $gallery_data;
			}else{
				$attachment = wp_get_attachment_image_src($value->meta_value);
				self::$export_instance->data[$id]['product_image_gallery'] = $attachment[0];
			}
		}elseif($value->meta_key == '_sale_price_dates_from'){
			self::$export_instance->data[$id]['sale_price_dates_from'] = date('Y-m-d',$value->meta_value);
		}
		elseif($value->meta_key == '_sale_price_dates_to'){
			self::$export_instance->data[$id]['sale_price_dates_to'] = date('Y-m-d',$value->meta_value);
		}else {

			// Mari commented this if statement
			// if(preg_match('/group_/',$value->meta_key)){
			// 	$value->meta_key = preg_replace('/group_/','', $value->meta_key );
			// }            
			
			if(is_array($allacf) && array_search($value->meta_key, $allacf)){         
				$repeaterOfrepeater = false;
				$getType = $alltype[$value->meta_key];
				if(empty($getType)){
					$temp_fieldname = array_search($value->meta_key, $allacf);
					$getType = $alltype[$temp_fieldname];
				}
				
				if ($getType == 'flexible_content' || $getType == 'repeater') { 
					if(is_serialized($value->meta_value)){
						$value->meta_value = unserialize($value->meta_value);
						$count = count($value->meta_value);
					}else{
						$count = $value->meta_value;
					}

					$getRF = $checkRep[$value->meta_key];
					$repeater_data = [];

					if($getType == 'flexible_content'){
						$flexible_value = '';
						foreach($value->meta_value as $values){
							$flexible_value .= $values.'|';
						}
						$flexible_value = rtrim($flexible_value , '|');	
						self::$export_instance->data[$id][$value->meta_key] = self::$export_instance->returnMetaValueAsCustomerInput($flexible_value);
					}

					foreach ($getRF as $rep => $rep1) {
						$repType = $alltype[$rep1];
						
						$reval = "";
						for($z=0;$z<$count;$z++){
							$var = $value->meta_key.'_'.$z.'_'.$rep1;

							if(in_array($optionalType , $taxonomies)){
								$qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}terms wp JOIN {$wpdb->prefix}termmeta wpm ON wpm.term_id = wp.term_id where meta_key = %s AND wp.term_id = %d", $var, $id));
							}else{
								$qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}posts wp JOIN {$wpdb->prefix}postmeta wpm ON wpm.post_id = wp.ID where meta_key = %s AND ID=%d", $var, $id));
							}

							$meta = $qry[0]->meta_value;
							if($repType == 'image')
								$meta = $this->getAttachment($meta);
							if($repType == 'file')
								$meta =$this->getAttachment($meta);
							if($repType == 'repeater' || $repType == 'flexible_content')
								//$meta = $this->getRepeaterofRepeater($value->meta_key);
								$repeaterOfrepeater = true;
								$rep_rep_fields = $this->getRepeaterofRepeater($rep1);

								foreach($rep_rep_fields as $repeat => $repeat1){
									$repeat_type = $alltype[$repeat1];
									$repeater_count = get_post_meta($id , $var , true);
									$repeat_val = "";
									for($r = 0; $r<$repeater_count; $r++){
										$var_rep = $value->meta_key.'_'.$z.'_'.$rep1.'_'.$r.'_'.$repeat1;
										
										if(in_array($optionalType , $taxonomies)){
											$rep_qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}terms wp JOIN {$wpdb->prefix}termmeta wpm ON wpm.term_id = wp.term_id where meta_key = %s AND wp.term_id = %d", $var_rep, $id));
										}else{
											$rep_qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}posts wp JOIN {$wpdb->prefix}postmeta wpm ON wpm.post_id = wp.ID where meta_key = %s AND ID=%d", $var_rep, $id));
										}
										$rep_meta = $rep_qry[0]->meta_value;
										
										if($repeat_type == 'image')
											$rep_meta = $this->getAttachment($rep_meta);
										if($repeat_type == 'file')
											$rep_meta =$this->getAttachment($rep_meta);

										if(is_serialized($rep_meta))
										{	
											$unmeta = unserialize($rep_meta);
									
											$rep_meta = "";
											$repeat_gal_val = '';
											foreach ($unmeta as $unmeta1) {
												if($repeat_type == 'image'){
													$repeat_val .= $this->getAttachment($unmeta1).'->';
												}elseif( $repeat_type == 'gallery'){	
													$repeat_gal_val .= $this->getAttachment($unmeta1).',';
												}
												else{
													$repeat_val .= $unmeta1.'->';
												}
											}
											
											if($repeat_type == 'gallery'){
												$repeat_val .= rtrim($repeat_gal_val , ',') . '->';
											}

										}elseif($rep_meta != ''){
											$repeat_val .= $rep_meta . '->';		
										}	
									}
									$repeater_data[$repeat1][] = rtrim($repeat_val,'->');
								
								}

							if(is_serialized($meta))
							{
								$unmeta = unserialize($meta);
								$meta = "";
								foreach ($unmeta as $unmeta1) {
									if($repType == 'image' || $repType == 'gallery')
										//$meta .= $this->getAttachment($unmeta1).",";
										$meta .= $this->getAttachment($unmeta1).',';
									elseif($repType == 'taxonomy') {
										$meta .=$unmeta1.',';
									}
									elseif($repType == 'user') {
										$meta .=$unmeta1.',';
									}
									elseif($repType == 'post_object') {
										$meta .=$unmeta1.',';
									}
									elseif($repType == 'relationship') {
										$meta .=$unmeta1.',';
									}
									elseif($repType == 'page_link') {
										$meta .=$unmeta1.',';
									}
									elseif($repType == 'link') {
										$meta .=$unmeta1 . ',';
									}

									else
										$meta .= $unmeta1.",";
								}

								if($repType == 'image' || $repType == 'gallery'){
									$meta = rtrim($meta,',');
								}else{
									$meta = rtrim($meta,',');
								}
								
							}
							if($meta != "")
								$reval .= $meta."|";
						}
						if($repeaterOfrepeater){
							if(!empty($repeater_data)){
								foreach($repeater_data as $repeater_key => $repeater_value){
									$repeaterOfvalue = '';
									foreach($repeater_value as $rep_rep_value){
										$repeaterOfvalue .= $rep_rep_value . '|';
									}
									self::$export_instance->data[$id][$repeater_key] = self::$export_instance->returnMetaValueAsCustomerInput(rtrim($repeaterOfvalue,'|'));
								}
							}
						}
						self::$export_instance->data[$id][$rep1] = self::$export_instance->returnMetaValueAsCustomerInput(rtrim($reval,'|'));
					}
				}
				elseif( is_serialized($value->meta_value)){
					
					$acfva = unserialize($value->meta_value);
					$acfdata = "";
					foreach ($acfva as $key1 => $value1) {
						if($getType == 'checkbox')
							$acfdata .= $value1.',';
						elseif($getType == 'gallery' || $getType == 'image'){
							$attach = $this->getAttachment($value1);
							// $acfdata .= $attach.',';
							$acfdata .= $attach.',';
						}
						elseif($getType == 'google_map')
						{
							$acfdata=$acfva['address'];
						}
						else{
							if(!empty($value1)) { 
								$acfdata .= $value1.',';
							}
						}
					}

					if($getType == 'gallery' || $getType == 'image'){
						$acfdata = rtrim($acfdata , ',');
					}else{
						$acfdata = rtrim($acfdata , ',');
					}

					//self::$export_instance->data[$id][ $value->meta_key ] = self::$export_instance->returnMetaValueAsCustomerInput(rtrim($acfdata,','));
					self::$export_instance->data[$id][ $value->meta_key ] = self::$export_instance->returnMetaValueAsCustomerInput($acfdata);
				}
				elseif($getType == 'gallery' || $getType == 'image'|| $getType == 'file'  ){
					$attach1 = $this->getAttachment($value->meta_value);
					self::$export_instance->data[$id][ $value->meta_key ] = $attach1;
				}
				else{
					self::$export_instance->data[$id][ $value->meta_key ] = self::$export_instance->returnMetaValueAsCustomerInput($value->meta_value);
				}
			}
			elseif (in_array($value->meta_key, $typesf)) {
				$type_value = '';	
				$typeoftype = $typeOftypesField[$value->meta_key];
				if(in_array($optionalType , $taxonomies)){
					$type_data =  get_term_meta($id,$value->meta_key);
				}
				elseif($optionalType == 'users'){
					$type_data =  get_user_meta($id,$value->meta_key);
				}
				else{
					$type_data =  get_post_meta($id,$value->meta_key);
				}
				
				if(is_array($type_data)){	
					
					foreach($type_data as $k => $mid){	
						if(is_array($mid) && !empty($mid)){
							if($typeoftype == 'skype'){	
								$type_value .= $mid['skypename'] . '|';
							}
							elseif($typeoftype == 'checkboxes'){
								$check_type_value = '';	
								foreach($mid as $mid_value){
										$check_type_value .= $mid_value[0] . ',';
								}
								$type_value .= rtrim($check_type_value , ',');
							}	
						}
						elseif($typeoftype == 'date'){
							$type_value .= date('m/d/Y', $mid) . '|';
						}
						else{
							$type_value .= $mid . '|';	
						}
					}
					if(preg_match('/wpcf-/',$value->meta_key)){		
						$value->meta_key = preg_replace('/wpcf-/','', $value->meta_key );	
						self::$export_instance->data[$id][ $value->meta_key ] = rtrim($type_value , '|');
					}
				}	
				
				if(preg_match('/group_/',$value->meta_key)){
					$getType = $alltype[$value->meta_key];
					if($value->meta_key == 'group_gallery' || $value->meta_key == 'group_image'|| $value->meta_key == 'file'  ){
						$groupattach = $this->getAttachment($value->meta_value);
						self::$export_instance->data[$id][ $value->meta_key ] = $groupattach;
					}
					else{
						$value->meta_key = preg_replace('/group_/','', $value->meta_key );
						self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
					}
				}
				
				//TYPES Allow multiple-instances of this field
			}elseif(in_array($value->meta_key, $group_unset) && is_serialized($value->meta_value)) {
				$unser = unserialize($value->meta_value);
				$data = "";
				
				foreach ($unser as $key4 => $value4) 
					$data .= $value4.',';
				self::$export_instance->data[$id][ $value->meta_key ] = substr($data, 0, -1);
			}
			
			elseif(in_array($value->meta_key , $pods_type)){	
				if(!isset(self::$export_instance->data[$id][$value->meta_key])){
					if(in_array($optionalType , $taxonomies)){
						$pods_file_data = get_term_meta($id,$value->meta_key);
					}else{
						$pods_file_data = get_post_meta($id,$value->meta_key);
					}
					
					$pods_value = '';
					foreach($pods_file_data as $pods_file_value){	
						if(!empty($pods_file_value)){
							if(is_array($pods_file_value)){
								$pods_value .= $pods_file_value['guid'] . ',';
							}else{
								$pods_value .= $pods_file_value . ',';
							}
						}	
					}
					self::$export_instance->data[$id][$value->meta_key] = rtrim($pods_value , ',');		
				}
			}
			
			else{
				self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
			}
		}
	}

	public function getRepeater($parent)
	{
		global $wpdb;

		$get_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts where post_parent = %d", $parent), ARRAY_A);
		//$test = $get_fields[0]->ID ;
		$i = 0;
		foreach ($get_fields as $key => $value) {
			$array[$i] = $value['post_excerpt'];
			$i++;
		}

		return $array;	
	}

	public function getRepeaterofRepeater($parent)
	{
		global $wpdb;	
		$get_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts where post_excerpt = %s", $parent), ARRAY_A);
		$test = $get_fields[0]['ID'] ;
		
		$get_fieldss = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts where post_parent = %d", $test), ARRAY_A);
		$i = 0;
		foreach ($get_fieldss as $key => $value) {
			$array[$i] = $value['post_excerpt'];			
			$i++;
		}
		return $array;	
	}



	/**
	 * Fetch all Categories
	 * @param $mode
	 * @param $module
	 * @param $optionalType
	 * @return array
	 */
	public function FetchCategories($mode = null,$module,$optionalType) {
		$headers = self::$export_instance->generateHeaders($module, $optionalType);
		$get_all_terms = get_categories('hide_empty=0');
		self::$export_instance->totalRowCount = count($get_all_terms);
		if(!empty($get_all_terms)) {
			foreach( $get_all_terms as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termName = $termValue->cat_name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->category_description;
				$termParent = $termValue->parent;
				if($termParent == 0) {
					self::$export_instance->data[$termID]['name'] = $termName;
				} else {
					$termParentName = get_cat_name( $termParent );
					self::$export_instance->data[$termID]['name'] = $termParentName . '|' . $termName;
				}
				self::$export_instance->data[$termID]['slug'] = $termSlug;
				self::$export_instance->data[$termID]['description'] = $termDesc;
				self::$export_instance->data[$termID]['parent'] = $termParent;
				self::$export_instance->data[$termID]['TERMID'] = $termID;

				$this->getPostsMetaDataBasedOnRecordId ($termID, $module, $optionalType);

				if(in_array('wordpress-seo/wp-seo.php', self::$export_instance->get_active_plugins())) {
					$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
					if ( isset( $seo_yoast_taxonomies['category'] ) ) {
		
							self::$export_instance->data[ $termID ]['title'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_title'];
							self::$export_instance->data[ $termID ]['meta_desc'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_desc'];
							self::$export_instance->data[ $termID ]['canonical'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_canonical'];
							self::$export_instance->data[ $termID ]['bctitle'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_bctitle'];
							self::$export_instance->data[ $termID ]['meta-robots-noindex'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_noindex'];
							self::$export_instance->data[ $termID ]['sitemap-include'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_sitemap_include'];
							self::$export_instance->data[ $termID ]['opengraph-title'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_opengraph-title'];
							self::$export_instance->data[ $termID ]['opengraph-description'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_opengraph-description'];
							self::$export_instance->data[ $termID ]['opengraph-image'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_opengraph-image'];
							self::$export_instance->data[ $termID ]['twitter-title'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_twitter-title'];
							self::$export_instance->data[ $termID ]['twitter-description'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_twitter-description'];
							self::$export_instance->data[ $termID ]['twitter-image'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_twitter-image'];
							self::$export_instance->data[ $termID ]['focus_keyword'] = $seo_yoast_taxonomies['category'][$termID]['wpseo_focuskw'];
		
					}
				}
			}
		}
		
		$result = self::$export_instance->finalDataToExport(self::$export_instance->data, $module);
 
		if($mode == null){
			self::$export_instance->proceedExport($result);
		}else{
			return $result;
		}
	}


	public function get_common_post_metadata($meta_id){
		global $wpdb;
		$mdata = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_id = %d", $meta_id) ,ARRAY_A);
		return $mdata[0];
	}

	public function getAttachment($id)
	{
		global $wpdb;
		$get_attachment = $wpdb->prepare("select guid from {$wpdb->prefix}posts where ID = %d AND post_type = %s", $id, 'attachment');
		$attachment = $wpdb->get_results($get_attachment);
		$attachment_file = $attachment[0]->guid;
		return $attachment_file;

	}

	/**
	 * Fetch all Tags
	 * @param $mode
	 * @param $module
	 * @param $optionalType
	 * @return array
	 */
	public function FetchTags($mode = null,$module,$optionalType) {
		
		self::$export_instance->generateHeaders($module, $optionalType);
		$get_all_terms = get_tags('hide_empty=0');
		self::$export_instance->totalRowCount = count($get_all_terms);
		if(!empty($get_all_terms)) {
			foreach( $get_all_terms as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termName = $termValue->name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->description;
				self::$export_instance->data[$termID]['name'] = $termName;
				self::$export_instance->data[$termID]['slug'] = $termSlug;
				self::$export_instance->data[$termID]['description'] = $termDesc;
				self::$export_instance->data[$termID]['TERMID'] = $termID;

				$this->getPostsMetaDataBasedOnRecordId ($termID, $module, $optionalType);

				if(in_array('wordpress-seo/wp-seo.php', self::$export_instance->get_active_plugins())) {
					$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
					if ( isset( $seo_yoast_taxonomies['post_tag'] ) ) {

							self::$export_instance->data[ $termID ]['title'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_title'];
							self::$export_instance->data[ $termID ]['meta_desc'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_desc'];
							self::$export_instance->data[ $termID ]['canonical'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_canonical'];
							self::$export_instance->data[ $termID ]['bctitle'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_bctitle'];
							self::$export_instance->data[ $termID ]['meta-robots-noindex'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_noindex'];
							self::$export_instance->data[ $termID ]['sitemap-include'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_sitemap_include'];
							self::$export_instance->data[ $termID ]['opengraph-title'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_opengraph-title'];
							self::$export_instance->data[ $termID ]['opengraph-description'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_opengraph-description'];
							self::$export_instance->data[ $termID ]['opengraph-image'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_opengraph-image'];
							self::$export_instance->data[ $termID ]['twitter-title'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_twitter-title'];
							self::$export_instance->data[ $termID ]['twitter-description'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_twitter-description'];
							self::$export_instance->data[ $termID ]['twitter-image'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_twitter-image'];
							self::$export_instance->data[ $termID ]['focus_keyword'] = $seo_yoast_taxonomies['post_tag'][$termID]['wpseo_focuskw'];
						
					}
				}
			}	
		}
		
		$result = self::$export_instance->finalDataToExport(self::$export_instance->data, $module);
		
		if($mode == null)
			self::$export_instance->proceedExport($result);
		else
			return $result;
	}
}
