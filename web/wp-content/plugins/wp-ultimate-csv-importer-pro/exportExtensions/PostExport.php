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
	public function getRecordsBasedOnPostTypes ($module, $optionalType, $conditions ,$offset , $limit,$category_module,$category_export) {
		global $wpdb;
		
		if(!empty($category_export)){
			trim($category_export);
			if($optionalType =='posts'){
				$optionalType='post';
			}
			$pos = strpos($category_export,'&');
			if ($pos === false) {
				$terms_id =  $wpdb->get_results("SELECT term_id FROM {$wpdb->prefix}terms where name='$category_export'");
			}else{
				$amp=$category_export.';amp';
				$terms_id =  $wpdb->get_results("SELECT term_id FROM {$wpdb->prefix}terms where name='$amp'");
			}
		
		
		foreach($terms_id as $termid){
				$taxo_id=$termid->term_id;
				$rel_id =  $wpdb->get_results("SELECT object_id FROM {$wpdb->prefix}term_relationships where term_taxonomy_id='$taxo_id'",ARRAY_A);
				foreach($rel_id as $rel_key => $rel_val){
					foreach($rel_val as $object_key => $object_val){
					$taxonomyexp[] =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts where ID = $object_val AND post_type = '$optionalType' And post_status !='trash'",ARRAY_A);
				}
				}
			}
		
					foreach($taxonomyexp as $tax_key => $tax_val){
						foreach($tax_val as $tax_exp){
                        $result[]=$tax_exp['ID'];
						}
					 }
					 self::$export_instance->totalRowCount = count($result);
		}else{
		
		if($module == 'CustomPosts' && $optionalType == 'nav_menu_item'){
			$get_menu_id = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms AS t LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'nav_menu' ", ARRAY_A);
			$get_menu_arr = array_column($get_menu_id, 'term_id');
			self::$export_instance->totalRowCount = count($get_menu_arr);
			return $get_menu_arr;			
		}

		if($module == 'CustomPosts' && $optionalType == 'widgets'){
			$get_widget_id = $wpdb->get_row("SELECT option_id FROM {$wpdb->prefix}options where option_name = 'widget_recent-posts' ", ARRAY_A);
			self::$export_instance->totalRowCount = 1;
			return $get_widget_id;			
		}

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
		elseif($module == 'Images'){
			$module='attachment';
			
		}
		else {
			$module = self::import_post_types($module);
		}
		$get_post_ids = "select DISTINCT ID from {$wpdb->prefix}posts";
		$get_post_ids .= " where post_type = '$module' and post_status in ('publish','future','private','pending')";

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
		elseif( $module == 'lp_order'){
			$get_post_ids .= " and post_status in ('lp-pending', 'lp-processing', 'lp-completed', 'lp-cancelled', 'lp-failed')";
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
			} 
			else {
				if(!$module=='attachment'){
					$get_post_ids .= " and post_status in ('publish','draft','future','private','pending')";
				}
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


// jeteng fields

		//if(is_plugin_active('jet-engine/jet-engine.php')){
			$jet_enginefields=$wpdb->get_results( $wpdb->prepare("SELECT id, meta_fields FROM {$wpdb->prefix}jet_post_types WHERE status != 'trash' AND slug = '$optionalType'"),ARRAY_A);
			$unserialized_meta = maybe_unserialize($jet_enginefields[0]['meta_fields']);
			foreach($unserialized_meta as $jet_key => $jet_value){
				$jet_field_label = $jet_value['title'];
				$jet_field_type = $jet_value['type'];
				if($jet_field_type != 'repeater'){
					$jet_field_namearr[] = $jet_value['name'];
				}
				else{
					$jet_field_namearr[] = $jet_value['name'];
					$fields=$jet_value['repeater-fields'];
				
					foreach($fields as $rep_fieldkey => $rep_fieldvalue){
						$jet_field_namearr1[] = $rep_fieldvalue['name'];
					}
				}
			}
			
			$jet_cpt_fields_name=array_merge($jet_field_namearr,$jet_field_namearr1);
		


		    //jeteng metabox fields


			global $wpdb;	
			$get_meta_fields = $wpdb->get_results( $wpdb->prepare("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name='jet_engine_meta_boxes'"),ARRAY_A);
			$unserialized_meta = maybe_unserialize($get_meta_fields[0]['option_value']);
			$count =count($unserialized_meta);
			for($i=1 ; $i<=$count ; $i++){
			$fields = $unserialized_meta['meta-'.$count];
			foreach($fields['meta_fields'] as $jet_key => $jet_value){
			
					if($jet_value['type'] != 'repeater'){
						$jet_field_name1[] = $jet_value['name'];
					}
					else{
						$jet_field_name1[] = $jet_value['name'];
						$jet_rep_fields = $jet_value['repeater-fields'];
						foreach($jet_rep_fields as $jet_rep_fkey => $jet_rep_fvalue){
							$jet_field_name2[] = $jet_rep_fvalue['name'];
						}
					}
				
				}
			}

			$jet_field_name = array_merge($jet_field_name1,$jet_field_name2);


			///jetengine custom taxonomy fields
			$jet_taxfields=$wpdb->get_results( $wpdb->prepare("SELECT id, meta_fields FROM {$wpdb->prefix}jet_taxonomies WHERE status != 'trash' AND slug = '$optionalType'"),ARRAY_A);
			$unserialized_taxmeta = maybe_unserialize($jet_taxfields[0]['meta_fields']);
			foreach($unserialized_taxmeta as $jet_taxkey => $jet_taxvalue){
				$jet_field_tax_label = $jet_taxvalue['title'];
				$jet_field_tax_type = $jet_taxvalue['type'];
				if($jet_field_tax_type != 'repeater'){
					$jet_field_tax_namearr[] = $jet_taxvalue['name'];
				}
				else{
					$jet_field_tax_namearr[] = $jet_taxvalue['name'];
					$taxfields=$jet_taxvalue['repeater-fields'];
				
					foreach($taxfields as $rep_taxfieldkey => $rep_taxfieldvalue){
						$jet_field_tax_namearr1[] = $rep_taxfieldvalue['name'];
					}
				}
			}

			$jet_tax_fields_name=array_merge($jet_field_tax_namearr,$jet_field_tax_namearr1);
		//}
       




		if(!empty($result)) {
			
			foreach($result as $key => $value) {
			
				if(in_array($value->meta_key,$jet_cpt_fields_name)){
					$jet_enginefields=$wpdb->get_results( $wpdb->prepare("SELECT id, meta_fields FROM {$wpdb->prefix}jet_post_types WHERE status != 'trash' AND slug = '$optionalType'"),ARRAY_A);
					$unserialized_meta = maybe_unserialize($jet_enginefields[0]['meta_fields']);
					foreach($unserialized_meta as $jet_key => $jet_value){
						$jet_field_label = $jet_value['title'];
						$jet_cptfield_names = $jet_value['name'];
						$jet_field_type = $jet_value['type'];
						if($jet_field_type != 'repeater'){
							$jet_cptfields[$jet_cptfield_names]=$jet_cptfield_names;
						    $jet_types[$jet_cptfield_names] = $jet_field_type;
						}
						else{
							$jet_cptfields[$jet_cptfield_names]=$jet_cptfield_names;
							$jet_types[$jet_cptfield_names] = $jet_field_type;
							$fields=$jet_value['repeater-fields'];
							foreach($fields as $rep_fieldkey => $rep_fieldvalue){
								$jet_rep_cptfields_label = $rep_fieldvalue['name'];
								$jet_rep_cptfields_type  = $rep_fieldvalue['type'];
								$jet_rep_cptfields[$jet_rep_cptfields_label] = $jet_rep_cptfields_label;
								$jet_rep_cpttypes[$jet_rep_cptfields_label]  = $jet_rep_cptfields_type;
							}
						}
			
					}
					self::$export_instance->jet_cptfields = $jet_cptfields;
					self::$export_instance->jet_types = $jet_types;
					if($jet_rep_cptfields){
						self::$export_instance->$jet_rep_cptfields = $jet_rep_cptfields;
						self::$export_instance->$jet_rep_cpttypes  = $jet_rep_cpttypes;
					}
					else{
						$jet_rep_cptfields = '';
						$jet_rep_cpttypes = '';
					}
					$this->getCustomFieldValue($id, $value, $checkRep, $allacf, $typeOftypesField, $alltype, $jet_cptfields, $jet_types, $jet_rep_cptfields, $jet_rep_cpttypes,  $parent, $typesf, $group_unset , $optionalType , self::$export_instance->allpodsfields, $module);
				}
				if(in_array($value->meta_key,$jet_field_name)){
					$get_meta_fields = $wpdb->get_results( $wpdb->prepare("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name='jet_engine_meta_boxes'"),ARRAY_A);
					$unserialized_meta = maybe_unserialize($get_meta_fields[0]['option_value']);
					$count =count($unserialized_meta);
					//$jet_rep_fields=[];
					for($i=1 ; $i<=$count ; $i++){
					    $fields = $unserialized_meta['meta-'.$count];
						foreach($fields['meta_fields'] as $jet_key => $jet_value){
							$jet_field_label = $jet_value['title'];
							$jet_field_names = $jet_value['name'];
							$jet_field_type = $jet_value['type'];
							if($jet_field_type != 'repeater'){

								$jet_metafields[$jet_field_names]=$jet_field_names;
							
								$jet_metatypes[$jet_field_names] = $jet_field_type;
								
							}
							
							else{
								$jet_metafields[$jet_field_names]=$jet_field_names;
							    $jet_metatypes[$jet_field_names] = $jet_field_type;
								$repfields=$jet_value['repeater-fields'];
								foreach($repfields as $rep_fieldkey => $rep_fieldvalue){
									$jet_rep_fields_label = $rep_fieldvalue['name'];
									$jet_rep_fields_type  = $rep_fieldvalue['type'];
								
									$jet_repfield[$jet_rep_fields_label] = $jet_rep_fields_label;
									$jet_reptype[$jet_rep_fields_label]  = $jet_rep_fields_type;
								}
							}
						
							
						}
						self::$export_instance->jet_metafields = $jet_metafields;
						self::$export_instance->jet_metatypes = $jet_metatypes;
						if($jet_repfield){
							self::$export_instance->$jet_repfield = $jet_repfield;
							self::$export_instance->$jet_reptype  = $jet_reptype;
						}
						else{
							$jet_repfield = '';
							$jet_reptype = '';
						}
						$this->getCustomFieldValue($id, $value, $checkRep, $allacf, $typeOftypesField, $alltype, $jet_metafields, $jet_metatypes, $jet_repfield, $jet_reptype,  $parent, $typesf, $group_unset , $optionalType , self::$export_instance->allpodsfields, $module);
					}
					
				}
				if(in_array($value->meta_key,$jet_tax_fields_name)){
					$jety_taxfields=$wpdb->get_results( $wpdb->prepare("SELECT id, meta_fields FROM {$wpdb->prefix}jet_taxonomies WHERE status != 'trash' AND slug = '$optionalType'"),ARRAY_A);
					$taxunserialized_meta = maybe_unserialize($jety_taxfields[0]['meta_fields']);
					foreach($taxunserialized_meta as $tax_key => $tax_value){
						$jet_taxfield_label = $tax_value['title'];
						$jet_taxfield_names = $tax_value['name'];
						$jet_taxfield_type = $tax_value['type'];
						if($jet_taxfield_type != 'repeater'){
							$jet_ctax_fields[$jet_taxfield_names]=$jet_taxfield_names;
						    $jet_tax_types[$jet_taxfield_names] = $jet_taxfield_type;
						}
						else{
							$jet_ctax_fields[$jet_taxfield_names]=$jet_taxfield_names;
							$jet_tax_types[$jet_taxfield_names] = $jet_taxfield_type;
							$taxfields=$tax_value['repeater-fields'];
							foreach($taxfields as $rep_taxfieldkey => $rep_taxfieldvalue){
								$jet_rep_taxfields_label = $rep_taxfieldvalue['name'];
								$jet_rep_taxfields_type  = $rep_taxfieldvalue['type'];
								$jet_rep_taxfields[$jet_rep_taxfields_label] = $jet_rep_taxfields_label;
								$jet_rep_taxtypes[$jet_rep_taxfields_label]  = $jet_rep_taxfields_type;
							}
						}
			
					}
					self::$export_instance->jet_taxfields = $jet_taxfields;
					self::$export_instance->jet_taxtypes = $jet_taxtypes;
					if($jet_rep_taxfields){
						self::$export_instance->$jet_rep_taxfields = $jet_rep_taxfields;
						self::$export_instance->$jet_rep_taxtypes  = $jet_rep_taxtypes;
					}
					else{
						$jet_rep_taxfields = '';
						$jet_rep_taxtypes = '';
					}
					$this->getCustomFieldValue($id, $value, $checkRep, $allacf, $typeOftypesField, $alltype, $jet_ctax_fields, $jet_tax_types, $jet_rep_taxfields, $jet_rep_taxtypes,  $parent, $typesf, $group_unset , $optionalType , self::$export_instance->allpodsfields, $module);

				}
				else{
					$jet_fields = $jet_field_type = $jet_rep_fields = $jet_rep_types = '';
					$this->getCustomFieldValue($id, $value, $checkRep, $allacf, $typeOftypesField, $alltype, $jet_fields, $jet_types, $jet_rep_fields, $jet_rep_types,  $parent, $typesf, $group_unset , $optionalType , self::$export_instance->allpodsfields, $module);
				}
				
				
			}
		}
		return self::$export_instance->data;





	}

	public function getAllPodsFields(){		
		$pods_fields = [];
		if(is_plugin_active('pods/init.php')){
			global $wpdb;
			$pods_fields_query_result = $wpdb->get_results("SELECT post_name FROM ".$wpdb->prefix."posts WHERE post_type = '_pods_field'");	
			foreach($pods_fields_query_result as $single_result){
				$pods_fields[] = $single_result->post_name;
				
			}
		}
		return $pods_fields;
	}

	public function getCustomFieldValue($id, $value, $checkRep, $allacf, $typeOftypesField, $alltype, $jet_fields, $jet_types, $jet_rep_fields, $jet_rep_types, $parent, $typesf, $group_unset , $optionalType , $pods_type, $module){
		
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
			if(!empty($downfiles) && is_array($downfiles)){
				foreach($downfiles as $dk => $dv){
					$down_file .= $dv['name'].','.$dv['file'].'|';
				}
			}	
			self::$export_instance->data[$id]['downloadable_files'] = rtrim($down_file,"|");
		}
		elseif($value->meta_key == '_upsell_ids'){
			$upselldata = unserialize($value->meta_value);
			if(!empty($upselldata) && is_array($upselldata)){
				$upsellids = implode(',',$upselldata);
			}
			else{
				$upsellids = $upselldata;
			}
			self::$export_instance->data[$id]['upsell_ids'] =  $upsellids;
		}
		elseif($value->meta_key == '_crosssell_ids'){
			$cross_selldata = unserialize($value->meta_value);
			if(!empty($cross_selldata) && is_array($cross_selldata)){
				$cross_sellids = implode(',',$cross_selldata);
			}
			else{
				$cross_sellids = $cross_selldata;
			}
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
					$ids=$v;
						$types_caption=$wpdb->get_results("select post_excerpt from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_description=$wpdb->get_results("select post_content from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_title=$wpdb->get_results("select post_title from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_alt_text=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attachment_image_alt' AND post_id='$ids'" ,ARRAY_A);
						$types_filename=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attached_file' AND post_id='$ids'" ,ARRAY_A);
						$filename=$types_filename[0]['meta_value'];
						$file_names=explode('/', $filename);
						$file_name= $file_names[2];
						self::$export_instance->data[$id]['product_caption'] = $types_caption;
						self::$export_instance->data[$id]['product_description'] = $types_description;
						self::$export_instance->data[$id]['product_title'] = $types_title;
						self::$export_instance->data[$id]['product_alt_text'] = $types_alt_text;
						self::$export_instance->data[$id]['product_file_name'] = $file_name;
					$attachment = wp_get_attachment_image_src($v);
					$attach[$k] = $attachment[0];
				}
				foreach($attach as $values){
					$getid=$wpdb->get_results("select ID from {$wpdb->prefix}posts where guid= '$values'" ,ARRAY_A);
						global $wpdb;
						
					$gallery_data .= $values.'|';
				}
				$gallery_data = rtrim($gallery_data , '|');
				self::$export_instance->data[$id]['product_image_gallery'] = $gallery_data;
			}else{
				$attachment = wp_get_attachment_image_src($value->meta_value);
				self::$export_instance->data[$id]['product_image_gallery'] = $attachment[0];
			}
		}elseif($value->meta_key == '_sale_price_dates_from'){
			if(!empty($value->meta_value)){
				self::$export_instance->data[$id]['sale_price_dates_from'] = date('Y-m-d',$value->meta_value);
			}
		}
		elseif($value->meta_key == '_sale_price_dates_to'){
			if(!empty($value->meta_value)){
				self::$export_instance->data[$id]['sale_price_dates_to'] = date('Y-m-d',$value->meta_value);
			}
		}else {          
			if(is_array($allacf) && array_search($value->meta_key, $allacf)){         
				$repeaterOfrepeater = false;
				$getType = $alltype[$value->meta_key];
				if(empty($getType)){
					$temp_fieldname = array_search($value->meta_key, $allacf);
					$getType = $alltype[$temp_fieldname];
				}
				if($getType == 'taxonomy'){
					if(is_serialized($value->meta_value)){
						$value->meta_value = unserialize($value->meta_value);
						foreach($value->meta_value as $meta){
							$termname = $wpdb->get_row($wpdb->prepare("select name from {$wpdb->prefix}terms where term_id= %d",$meta));
							$terms[]=$termname->name;	
						}
						$value->meta_value = implode(',',$terms );	
						self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
						
					}
				}
				if($getType =='user'){
					if(is_serialized($value->meta_value)){
						$meta_value = unserialize($value->meta_value);
						foreach($meta_value as $val){
							$user = $wpdb->get_row($wpdb->prepare("select user_login from {$wpdb->prefix}users where ID= %d",$val));
						$username[]=$user->user_login;
						}
						$value->meta_value = implode(',',$username);	
						self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
					}
				}
				if($getType =='relationship'){
					if(is_serialized($value->meta_value)){
						$rel_value = unserialize($value->meta_value);
						foreach($rel_value as $rel){
							$relname = $wpdb->get_row($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID= %d",$rel));
							$relation[]=$relname->post_title;
						}
						$value->meta_value = implode(',',$relation);	
						self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
					}
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
							$flexible_value .= $values.',';
						}
						$flexible_value = rtrim($flexible_value , ',');	
						self::$export_instance->data[$id][$value->meta_key] = self::$export_instance->returnMetaValueAsCustomerInput($flexible_value);
					}

					foreach ($getRF as $rep => $rep1) {
					

						$repType = $alltype[$rep1];
						$reval = "";
						for($z=0;$z<$count;$z++){
							$var = $value->meta_key.'_'.$z.'_'.$rep1;
							if(in_array($optionalType , $taxonomies)){
								$qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}terms wp JOIN {$wpdb->prefix}termmeta wpm ON wpm.term_id = wp.term_id where meta_key = %s AND wp.term_id = %d", $var, $id));
							}
							elseif($optionalType == 'users'){
								$qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}users wp JOIN {$wpdb->prefix}usermeta wpm ON wpm.user_id = wp.ID where meta_key = %s AND ID = %d", $var, $id));
							}
							else{
								$qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}posts wp JOIN {$wpdb->prefix}postmeta wpm ON wpm.post_id = wp.ID where meta_key = %s AND ID=%d", $var, $id));
							}
							$meta = $qry[0]->meta_value;
							if(is_numeric($meta) && $repType != 'image' && $repType != 'file' && $repType !='number' && $repType != 'range'){
								$meta_title = $wpdb->get_col($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d",$meta));
								foreach($meta_title as $meta_tit){
									$meta=$meta_tit;
									
								}	
							}
							if($repType == 'image')
								$meta = $this->getAttachment($meta);
							if($repType == 'file')
								$meta =$this->getAttachment($meta);
							if($repType == 'repeater' || $repType == 'flexible_content')
							
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
								
								// if(!is_serialized($meta)){
								// 	if(is_numeric($meta)){
								// 		$meta_title = $wpdb->get_col($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d",$meta1));
								// 		foreach($meta_title as $meta_tit){
								// 			$meta .=$meta_tit.',';
											
								// 		}	
								// 	}
								// }
							if(is_serialized($meta))
							{
								$unmeta = unserialize($meta);
								
								$meta = "";
								foreach ($unmeta as $unmeta1) {
									if($repType == 'image' || $repType == 'gallery')
										$meta .= $this->getAttachment($unmeta1).',';
									elseif($repType == 'taxonomy') {
										$meta .=$unmeta1.',';
									
									}
									elseif($repType == 'user') {
										$meta .=$unmeta1.',';
									}
									elseif($repType == 'post_object') {
										
										if(is_numeric($unmeta1)){
											$meta_title = $wpdb->get_col($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d",$unmeta1));
											foreach($meta_title as $meta_tit){
												$meta .=$meta_tit.',';
												
											}	
										}
										
										
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
				elseif($getType == 'post_object'){
					$check = false;
						if(is_serialized($value->meta_value)){
							$value->meta_value = @unserialize($value->meta_value);
							
							foreach($value->meta_value as $meta){
								$data[]=$meta;
								$check = true;
							}
						}
						if($check){
							foreach($data as $metas){
								if(is_numeric($metas)){

										$title = $wpdb->get_row($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d",$metas));
										$test[] = $title->post_title;
								}
							}
							
							$value->meta_value = implode(',',$test );			
							self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
						}else{
							if(is_numeric($value->meta_value)){	
								foreach($value->meta_value as $meta){
									$title = $wpdb->get_col($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d",$meta));	
								}
								foreach($title as $value->meta_value){
									self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
								}
								}
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
							$getid=$wpdb->get_results("select ID from {$wpdb->prefix}posts where guid= '$attach'" ,ARRAY_A);
						global $wpdb;
						$ids=$getid;
						$types_caption=$wpdb->get_results("select post_excerpt from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_description=$wpdb->get_results("select post_content from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_title=$wpdb->get_results("select post_title from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_alt_text=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attachment_image_alt' AND post_id='$ids'" ,ARRAY_A);
						$types_filename=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attached_file' AND post_id='$ids'" ,ARRAY_A);
						$filename=$types_filename[0]['meta_value'];
						$file_names=explode('/', $filename);
						$file_name= $file_names[2];
						$typecap=$types_caption.',';
						self::$export_instance->data[$id]['acf_caption'] = $typecap;
						self::$export_instance->data[$id]['acf_description'] = $types_description;
						self::$export_instance->data[$id]['acf_title'] = $types_title;
						self::$export_instance->data[$id]['acf_alt_text'] = $types_alt_text;
						self::$export_instance->data[$id]['acf_file_name'] = $file_name;
						$acfdata .= $attach.',';
						}
						elseif($getType == 'google_map')
						{
							$acfdata=$acfva['address'].'|'.$acfva['lat'].'|'.$acfva['lng'];
						}
						else{
							if(!empty($value1)) { 
								$acfdata .= $value1.',';
							}
						}
					}

					if($getType == 'gallery' || $getType == 'image'){
						$getid=$wpdb->get_results("select ID from {$wpdb->prefix}posts where guid= '$acfdata'" ,ARRAY_A);
						global $wpdb;
						foreach($getid as $getkey => $getval){
							$ids=$getval['ID'];
						$types_caption=$wpdb->get_results("select post_excerpt from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_description=$wpdb->get_results("select post_content from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_title=$wpdb->get_results("select post_title from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
						$types_alt_text=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attachment_image_alt' AND post_id='$ids'" ,ARRAY_A);
						$types_filename=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attached_file' AND post_id='$ids'" ,ARRAY_A);
						$filename=$types_filename[0]['meta_value'];
						$file_names=explode('/', $filename);
						$file_name= $file_names[2];
						$typecap=$types_caption[0]['post_excerpt'].',';
						self::$export_instance->data[$id]['acf_caption'] = $typecap;
						self::$export_instance->data[$id]['acf_description'] = $types_description[0]['post_content'];
						self::$export_instance->data[$id]['acf_title'] = $types_title[0]['post_title'];
						self::$export_instance->data[$id]['acf_alt_text'] = $types_alt_text[0]['meta_value'];
						self::$export_instance->data[$id]['acf_file_name'] = $file_name;	
						
						}
						$acfdata = rtrim($acfdata , ',');
					}else{
						$acfdata = rtrim($acfdata , ',');
					}

					self::$export_instance->data[$id][ $value->meta_key ] = self::$export_instance->returnMetaValueAsCustomerInput($acfdata);
				}
				elseif($getType == 'gallery' || $getType == 'image'|| $getType == 'file'  ){
					$attach1 = $this->getAttachment($value->meta_value);
					global $wpdb;
					$getid=$wpdb->get_results("select ID from {$wpdb->prefix}posts where guid= '$attach1'" ,ARRAY_A);
						foreach($getid as $getkey => $getval){
							$ids=$getval['ID'];
							$types_caption=$wpdb->get_results("select post_excerpt from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
							$types_description=$wpdb->get_results("select post_content from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
							$types_title=$wpdb->get_results("select post_title from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
							$types_alt_text=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attachment_image_alt' AND post_id='$ids'" ,ARRAY_A);
							$types_filename=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attached_file' AND post_id='$ids'" ,ARRAY_A);
							$filename=$types_filename[0]['meta_value'];
							$file_names=explode('/', $filename);
							$file_name= $file_names[2];
							
							self::$export_instance->data[$id]['acf_caption'] = $types_caption[0]['post_excerpt'];
							self::$export_instance->data[$id]['acf_description'] = $types_description[0]['post_content'];
							self::$export_instance->data[$id]['acf_title'] = $types_title[0]['post_title'];
							self::$export_instance->data[$id]['acf_alt_text'] = $types_alt_text[0]['meta_value'];
							self::$export_instance->data[$id]['acf_file_name'] = $file_name;
						}
						
						
					self::$export_instance->data[$id][ $value->meta_key ] = $attach1;
				}
				else{
					self::$export_instance->data[$id][ $value->meta_key ] = self::$export_instance->returnMetaValueAsCustomerInput($value->meta_value);
				}
			}

			elseif(is_array($jet_fields) && in_array($value->meta_key, $jet_fields)){
				$getjetType = $jet_types[$value->meta_key];
				if(empty($getjetType)){
					$temp_fieldname = array_search($value->meta_key, $jet_fields);
					$getjetType = $jet_types[$temp_fieldname];
				}
			
				if($getjetType == 'checkbox'){
					$value->meta_value = unserialize($value->meta_value);
				    $check = '';
					foreach($value->meta_value as $key => $metvalue){
						if(is_numeric($key)){
							$check .= $metvalue.',';	
							$rcheck = substr($check,0,-1);
						    self::$export_instance->data[$id][ $value->meta_key ] = $rcheck;
						}
						else{
							if($metvalue == 'true'){
								$exp_value[] = $key;
							}
							$value->meta_value = implode(',',$exp_value );	
							self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
						}
                       
					}

				}
				elseif($getjetType == 'gallery' || $getjetType == 'media'){
					$gallery= explode(',',$value->meta_value);
					foreach($gallery as $gallerykey => $galleryval){
						$galleries[] = $this->getAttachment($galleryval);
						$value->meta_value = implode(',',$galleries );	
						self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
					}
				}
				elseif($getjetType == 'posts'){
					if(is_serialized($value->meta_value)){
						$value->meta_value = unserialize($value->meta_value);
						foreach($value->meta_value as $postkey => $metpostvalue){
							if(is_numeric($metpostvalue)){

								$title = $wpdb->get_row($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d",$metpostvalue));
								$test[] = $title->post_title;
						    }
					    }
					
						$value->meta_value = implode(',',$test );			
						self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
						
					}
				}
				elseif($getjetType == 'select'){
					if(is_serialized($value->meta_value)){
						$value->meta_value = unserialize($value->meta_value);
						foreach($value->meta_value as $metkey => $metselectvalue){
							$select[] = $metselectvalue;
							$value->meta_value = implode(',',$select );	
							self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
						}
					}
					else{
						self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
					}
				}
				elseif($getjetType == 'repeater'){

					global $wpdb;
					foreach($jet_types as $jettypename => $jettypeval){
                        if($jettypeval == 'repeater'){
							$jet_fields_name =$jettypename;
							if($module == 'Users'){
                                $fieldarr = $wpdb->get_results( $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = '$jet_fields_name' AND user_id = $id "),ARRAY_A);
							}
						    elseif($module == 'Categories' || $module == 'Taxonomies' || $module == 'Tags'){
							    $fieldarr = $wpdb->get_results( $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}termmeta WHERE meta_key = '$jet_fields_name' AND term_id = $id "),ARRAY_A);
							}
							else{
								$fieldarr = $wpdb->get_results( $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '$jet_fields_name' AND post_id = $id "),ARRAY_A);
							}
							$arr =json_decode(json_encode($fieldarr),true);
							$unser = unserialize($arr[0]['meta_value']);
							
							$count = count($unser);
							$array_valuenum = $array_valuetext = $array_checkval = $array_wysval = $array_timval = $array_datval = $array_dattimval = $array_radval = $array_colorval = $array_switval = $array_iconval = $array_valuetextarea = $array_selval = $array_postval= $array_mediaval = $array_galval = '';
							for($i=0 ; $i<$count; $i++){
								$j =0;
								$idkey = 'item-'.$i;
								$array=$unser[$idkey];
								$array_keys =array_keys($array);
                                foreach($array_keys as $arrkey){
									$arrcol[$arrkey] = array_column($unser,$arrkey);
								}
								foreach($arrcol as $array_key => $array_val){
									
									$array_valuenum = $array_valuetext = $array_checkval = $array_wysval = $array_timval = $array_datval = $array_dattimval = $array_radval = $array_colorval = $array_switval = $array_iconval = $array_valuetextarea = $array_selval = $array_postval= $array_mediaval = $array_galval = '';
									
									if($jet_rep_types[$array_key] == 'text'){
										
										foreach($array_val as $arrval){
											$array_valuetext .= $arrval.'|';
										}
										
										self::$export_instance->data[$id][ $array_key ] = $array_valuetext;
									}
									elseif($jet_rep_types[$array_key] == 'checkbox'){
										foreach($array_val as $arrval){
											$exp_value = [];
										
											foreach($arrval as $key => $metvalue){
												if($metvalue == 'true'){
													$exp_value[] = $key;
													
												}
												
											}
											$checkval = implode(',',$exp_value );	
										
											$array_checkval .=$checkval.'|';
											
											self::$export_instance->data[$id][$array_key] = $array_checkval;

										} 
									}
									
									elseif($jet_rep_types[$array_key] == 'media'){
										foreach($array_val as $arrval){
											$medias = [];
											$media= explode(',',$arrval);
											foreach($media as $mediakey => $mediaval){
												$medias[] = $this->getAttachment($mediaval);
											}
											$mediaval = implode(',',$medias );	
											
											$array_mediaval .=$mediaval.'|';
											
											self::$export_instance->data[$id][$array_key] = $array_mediaval;
											
									    }			
									}
									elseif($jet_rep_types[$array_key] == 'gallery'){
										foreach($array_val as $arrval){
											$galleries =[];
											$gallery= explode(',',$arrval);
											foreach($gallery as $gallerykey => $galleryval){
												$galleries[] = $this->getAttachment($galleryval);
												
											}
											$gal_val = implode(',',$galleries );
											$array_galval .=$gal_val.'|';	
											self::$export_instance->data[$id][$array_key] = $array_galval;
										}
										

									}
									elseif($jet_rep_types[$array_key] == 'posts'){
										$test =[];
										$posts_val ='';
										foreach($array_val as $arrval){
											$test =[];
											if(is_array($arrval)){
												
												foreach($arrval as $postkey => $metpostvalue){
													if(is_numeric($metpostvalue)){
														$title = $wpdb->get_row($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d ORDER BY ID DESC",$metpostvalue));
														$test[] = $title->post_title;

													}	
														
												}
												$postval = implode(',',$test );
												
												$posts_val .=$postval.'|';	
												self::$export_instance->data[$id][$array_key] = $posts_val;
												
											}
											else{
											
												if(is_numeric($arrval)){
													$title = $wpdb->get_row($wpdb->prepare("select post_title from {$wpdb->prefix}posts where ID = %d ORDER BY ID DESC",$arrval));
													$testing = $title->post_title;
												}
												$posts_val .=$testing.'|';
												self::$export_instance->data[$id][$array_key] = $posts_val;
											}
										
										}
									
									}
									elseif($jet_rep_types[$array_key] == 'select'){
										$array_selval ='';
										foreach($array_val as $arrval){
											if(is_array($arrval)){
												$select =[];
												foreach($arrval as $metselectvalue){
													//foreach($metvalue as $metselectvalue){
														$select[] = $metselectvalue;
														$array_vals = implode(',',$select );
													//}
													
												}
												
												$array_selval .=$array_vals.'|';
												self::$export_instance->data[$id][$array_key] = $array_selval;
											}
											else{
												$array_selval .=$arrval.'|';
												self::$export_instance->data[$id][$array_key] = $array_selval;
											}

										}
									
									}
									elseif($jet_rep_types[$array_key] == 'date'){
										foreach($array_val as $arrval){
											$array_datval .= $arrval.'|';
										}
									
										self::$export_instance->data[$id][ $array_key ] = $array_datval;
									}
									elseif($jet_rep_types[$array_key] == 'time'){
										foreach($array_val as $arrval){
											$array_timval .= $arrval.'|';
										}
										//$array_timval = substr($array_timval,0,-1);
										self::$export_instance->data[$id][ $array_key ] = $array_timval;
									}
									elseif($jet_rep_types[$array_key] == 'wysiwyg'){
										foreach($array_val as $arrval){
											$array_wysval .= $arrval.'|';
										}
										//$array_wysval = substr($array_wysval,0,-1);
										self::$export_instance->data[$id][ $array_key ] = $array_wysval;
									}
									elseif($jet_rep_types[$array_key] == 'datetime-local'){
										foreach($array_val as $arrval){
										
											$array_dattimval .= $arrval.'|';
										}
										//$array_dattimval = substr($array_dattimval,0,-1);
										self::$export_instance->data[$id][ $array_key ] = $array_dattimval;
									}
									elseif($jet_rep_types[$array_key] == 'iconpicker'){
										foreach($array_val as $arrval){
											$array_iconval .= $arrval.'|';
										}
										//$array_iconval = substr($array_iconval,0,-1);
										self::$export_instance->data[$id][ $array_key ] = $array_iconval;
									}
									elseif($jet_rep_types[$array_key] == 'switcher'){
										foreach($array_val as $arrval){
											$array_switval .= $arrval.'|';
										}
										//$array_switval = substr($array_switval,0,-1);
										self::$export_instance->data[$id][ $array_key ] = $array_switval;
									}
									elseif($jet_rep_types[$array_key] == 'colorpicker'){
										foreach($array_val as $arrval){
											$array_colorval .= $arrval.'|';
										}
										//$array_colorval = substr($array_colorval,0,-1);
										self::$export_instance->data[$id][ $array_key ] = $array_colorval;
									}
									




									elseif($jet_rep_types[$array_key] == 'number'){
										foreach($array_val as $arrval){
											$array_valuenum .= $arrval.'|';
											$array_valuenum = rtrim($array_valuenum);
										}
										//$array_valuenum = substr($array_valuenum,0,-1);
										self::$export_instance->data[$id][$array_key] = $array_valuenum;

									}
									elseif($jet_rep_types[$array_key] == 'textarea'){
										foreach($array_val as $arrval){
											$array_valuetextarea .= $arrval.'|';
										}
										//$array_valuetextarea = substr($array_valuetextarea,0,-1);
										self::$export_instance->data[$id][$array_key] = $array_valuetextarea;

									}
									else{
										if(array_search("radio",$jet_rep_types)){
											
												$array_radval .= '|';
											
												if($jet_rep_types[$array_key] == 'radio'){
													foreach($array_val as $arrval){
														$array_radval .= $arrval.'|';
													}
												}
											
											self::$export_instance->data[$id][ $array_key ] = $array_radval;
										}

									}
									

								}
							}
						}
					}
				}
			
				else{	
					self::$export_instance->data[$id][ $value->meta_key ] = $value->meta_value;
				}
				
			}


			elseif (!empty($typesf) && in_array($value->meta_key, $typesf)) {
			global $wpdb;
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
					$typcap = "";
					foreach($type_data as $type_key =>$type_value){
						$getid=$wpdb->get_results("select ID from {$wpdb->prefix}posts where guid= '$type_value'" ,ARRAY_A);
						foreach($getid as $getkey => $getval){
							global $wpdb;
							$ids=$getval['ID'];
							$types_caption=$wpdb->get_results("select post_excerpt from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
							$types_description=$wpdb->get_results("select post_content from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
							$types_title=$wpdb->get_results("select post_title from {$wpdb->prefix}posts where ID= '$ids'" ,ARRAY_A);
							$types_alt_text=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attachment_image_alt' AND post_id='$ids'" ,ARRAY_A);
							$types_filename=$wpdb->get_results("select meta_value from {$wpdb->prefix}postmeta where meta_key= '_wp_attached_file' AND post_id='$ids'" ,ARRAY_A);
							$filename=$types_filename[0]['meta_value'];
							$file_names=explode('/', $filename);
							$file_name= $file_names[2];
							self::$export_instance->data[$id]['types_caption'] = $types_caption[0]['post_excerpt'];
							self::$export_instance->data[$id]['types_description'] = $types_description;
							self::$export_instance->data[$id]['types_title'] = $types_title;
							self::$export_instance->data[$id]['types_alt_text'] = $types_alt_text;
							self::$export_instance->data[$id]['types_file_name'] = $file_name;
						    
					
						}
						
							$type_value = rtrim($type_value , '|');
						
						
					}
				
					self::$export_instance->data[$id][ $value->meta_key ] = $type_value;
					
				}
				
				if(is_array($type_data)){	
					$type_value="";
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
				foreach($pods_type as $pods){
					$pods_id =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts where post_title='$pods'");	
					
					foreach($pods_id as $pod_id){
						$pods_id_value=$pod_id->ID;
						$pods_types =  $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta where post_id='$pods_id_value' and meta_key='type'");	
						foreach($pods_types as $pod_type){
							$ptype[]=$pod_type->meta_value;	
						}	
					}
				}
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
								$posts_type=$pods_file_value['post_type'];
								if($posts_type=='attachment'){
									$pods_value .= $pods_file_value['guid'] . ',';
								}
								elseif($posts_type!=='attachment'){
									$p_guid=$pods_file_value['guid'];
									$pod_tit =  $wpdb->get_results("SELECT post_title FROM {$wpdb->prefix}posts where guid='$p_guid'");	
									foreach($pod_tit as $pods_title){
										$pods_title_value=$pods_title->post_title;
										$pods_value .= $pods_title_value . ',';
									}
								}
								
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
		self::$export_instance->generateHeaders($module, $optionalType);
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
				if(is_plugin_active('wordpress-seo/wp-seo.php')){
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
				if(is_plugin_active('wordpress-seo/wp-seo.php')){
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