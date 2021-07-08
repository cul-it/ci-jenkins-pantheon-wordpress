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

class DefaultExtension extends ExtensionHandler{
	private static $instance = null;

	public static function getInstance() {
		if (DefaultExtension::$instance == null) {
			DefaultExtension::$instance = new DefaultExtension;
		}
		return DefaultExtension::$instance;
	}

	/**
	* Provides default mapping fields for specific post type or taxonomies
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
	public function processExtension($data){
		$mode = $_POST['Mode'];
		$import_types = $data;
		$import_type = $this->import_name_as($import_types);
		$response = [];
		if ($import_type != 'Users' && $import_type != 'Taxonomies' && $import_type != 'CustomerReviews' && $import_type != 'Categories' && $import_type != 'Comments' && $import_type != 'MarketPressVariations' && $import_type != 'WooCommerceVariations' && $import_type != 'WooCommerceOrders' && $import_type != 'WooCommerceCoupons' && $import_type != 'WooCommerceRefunds' && $import_type != 'WooCommerceCategories' && $import_type != 'WooCommerceattribute' && $import_type != 'WooCommercetags' && $import_type != 'Images' && $import_type != 'ngg_pictures' && $import_types != 'lp_order' && $import_types != 'nav_menu_item' && $import_types != 'widgets') {
			$wordpressfields = array(
                    'Title' => 'post_title',
                    'ID' => 'ID',
                    'Content' => 'post_content',
                    'Short Description' => 'post_excerpt',
                    'Publish Date' => 'post_date',
                    'Slug' => 'post_name',
                    'Author' => 'post_author',
                	'Status' => 'post_status',
                    'Featured Image' => 'featured_image'    
					);
			if(is_plugin_active('multilanguage/multilanguage.php')) {
				$wordpressfields['Language Code'] = 'lang_code';
			}
			if(is_plugin_active('post-expirator/post-expirator.php')) {
				$wordpressfields['Post Expirator'] = 'post_expirator';
				$wordpressfields['Post Expirator Status'] = 'post_expirator_status';
			}		
			if ($import_type === 'Posts') { 
				$wordpressfields['Format'] = 'post_format';
				$wordpressfields['Comment Status'] = 'comment_status';
				$wordpressfields['Ping Status'] = 'ping_status';
				$wordpressfields['Track Options'] = 'pinged';
			}
			if ($import_type === 'CustomPosts') { 
				$wordpressfields['Format'] = 'post_format';
				$wordpressfields['Comment Status'] = 'comment_status';
				$wordpressfields['Ping Status'] = 'ping_status';
				$wordpressfields['Track Options'] = 'pinged';
				$wordpressfields['Parent'] = 'post_parent';
				$wordpressfields['Order'] = 'menu_order';
			}
			if ($import_type === 'Pages') {
				$wordpressfields['Parent'] = 'post_parent';
				$wordpressfields['Order'] = 'menu_order';
				$wordpressfields['Page Template'] = 'wp_page_template';
				$wordpressfields['Comment Status'] = 'comment_status';
				$wordpressfields['Ping Status'] = 'ping_status';
			}
			if($import_type === 'MarketPress' || $import_type == 'WooCommerce' || $import_type == 'WPeCommerce' || $import_type == 'eShop'){
				//Commented for removing the sku field in core fields mapping section
				$wordpressfields['PRODUCT SKU'] = 'PRODUCTSKU';
			}

			if($mode == 'Insert'){
				unset($wordpressfields['ID']);
				if($import_type != 'WooCommerce'){
					unset($wordpressfields['PRODUCT SKU']);
				}
			}

			if($import_types == 'lp_lesson'){
				unset($wordpressfields['Format']);
				unset($wordpressfields['Featured Image']);
				unset($wordpressfields['Short Description']);
				unset($wordpressfields['Author']);
				unset($wordpressfields['Parent']);
				unset($wordpressfields['Order']);
			}

			if($import_types == 'lp_quiz' || $import_types == 'lp_question'){
				unset($wordpressfields['Format']);
				unset($wordpressfields['Featured Image']);
				unset($wordpressfields['Short Description']);
				unset($wordpressfields['Author']);
				unset($wordpressfields['Comment Status']);
				unset($wordpressfields['Ping Status']);
				unset($wordpressfields['Track Options']);
				unset($wordpressfields['Parent']);
				unset($wordpressfields['Order']);
			}
		} 

		if($import_types == 'lp_order'){
			$wordpressfields = array(
				'Order Status' => 'order_status',
				'Order Date' => 'order_date',
				'Order ID' => 'ORDER_ID'
			);
			if($mode == 'Insert'){
				unset($wordpressfields['Order ID']);
			}
		}

		if($import_types == 'nav_menu_item'){
			$wordpressfields = array(
				'Menu Title' => 'menu_title',
				'Menu Type' => '_menu_item_type',
				'Menu Items' => '_menu_item_object',
				'Menu Item Ids' => '_menu_item_object_id',
				'Menu Custom Url' => '_menu_item_url',
				'Menu Auto Add' => 'menu_auto_add'
			); 

			$get_navigation_locations = get_nav_menu_locations();
			foreach($get_navigation_locations as $nav_key => $nav_values){
				$wordpressfields[$nav_key] = $nav_key;
			}
		}

		if($import_types == 'widgets') {
			$wordpressfields = array(
				'Recent Posts'   => 'widget_recent-posts',
				'Pages'          => 'widget_pages',
				'Recent Comments'=> 'widget_recent-comments',
				'Archieves' => 'widget_archives',
				'Categories'     => 'widget_categories'
			);
		}

		if($import_type == 'WPeCommerceCoupons' ) {
			$wordpressfields = array(
					'Coupon Code' => 'coupon_code',
					'Coupon Id' => 'COUPONID',
					'Description' => 'description',
					'Status' => 'coupon_status',
					'Discount' => 'discount',
					'Discount Type' => 'discount_type',
					'Start' => 'start',
					'Expiry' => 'expiry',
					'Use Once' => 'use_once',
					'Apply On All Products' => 'apply_on_all_products',
					'Conditions' => 'conditions'
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Coupon Id']);
			}
		}

		if($import_type == 'Users'){
			$wordpressfields = array(
					'User Login' => 'user_login',
					'User Pass' => 'user_pass',
					'First Name' => 'first_name',
					'Last Name' => 'last_name',
					'Nick Name' => 'nickname',
					'User Email' => 'user_email',
					'User URL' => 'user_url',
					'User Nicename' => 'user_nicename',
					'User Registered' => 'user_registered',
					'Display Name' => 'display_name',
					'User Role' => 'role',
					'Biographical Info' => 'biographical_info',
					'Disable Visual Editor' => 'disable_visual_editor',
					'Syntax Highlighting' => 'syntax_highlighting',
					'Admin Color Scheme' => 'admin_color',
					'Enable Keyboard Shortcuts' => 'enable_keyboard_shortcuts',
					'Show Toolbar' => 'show_toolbar',
					'Language' => 'language',
					'ID' => 'ID'
					);
			if($mode == 'Insert'){
				unset($wordpressfields['ID']);
			}
		}
		if($import_type === 'Comments') {
			$wordpressfields = array(
					'Comment Post Id' => 'comment_post_ID',
					'Comment Author' => 'comment_author',
					'Comment Author Email' => 'comment_author_email',
					'Comment Author URL' => 'comment_author_url',
					'Comment Content' => 'comment_content',
					'Comment Rating' => 'comment_rating',
					'Comment Author IP' => 'comment_author_IP',
					'Comment Date' => 'comment_date',
					'Comment Approved' => 'comment_approved',
					'Comment Parent' => 'comment_parent', 
					'user_id'=>'user_id',
					);
		}
		if($import_type === 'Images') {
			$wordpressfields = array(
					'Caption' => 'caption',
					'Alt text' => 'alt_text',
					'Description' => 'description',
					'ID'    => 'ID',
					'File Name' => 'file_name',
					'Title' => 'title',
					'Featured Image' => 'featured_image');
		}
		if($import_type === 'ngg_pictures') {
			$wordpressfields = array(
					 'ID' =>'id',
					 'Filename' => 'filename',
					'Alt text' => 'alt_text',
					'Description' => 'description',
					'Featured Image' => 'featured_image',
					'Nextgen Gallery' => 'nextgen_gallery',
					'Manage Tags' =>'manage_tags');
		}
		if($import_type === 'Taxonomies') {
			$wordpressfields = array(
					'Taxonomy Name' => 'name',
					'Taxonomy Slug' => 'slug',
					'Taxonomy Description' => 'description',
					'Term ID' => 'TERMID',
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Term ID']);
			}
		}
		if($import_type === 'Categories') {
			$wordpressfields = array(
					'Category Name' => 'name',
					'Category Slug' => 'slug',
					'Category Description' => 'description',                        
					'Parent' => 'parent',
					'Term ID' => 'TERMID'
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Term ID']);
			}	
			if($import_types == 'product_cat'){
				$wordpressfields['Category Image'] = 'image';
				$wordpressfields['Display type'] = 'display_type';
				$wordpressfields['Top Content'] = 'top_content';
				$wordpressfields['Bottom Content'] = 'bottom_content';
			}elseif($import_types == 'wpsc_product_category'){
				$wordpressfields['Category Image'] = 'image';
			}elseif($import_types == 'event-categories'){
				$wordpressfields['Category Image'] = 'image';
				$wordpressfields['Category Color'] = 'color';
			}
		}
		if($import_type === 'Tags') {
			$wordpressfields = array(
					'Tag Name' => 'name',
					'Tag Slug' => 'slug',
					'Tag Description' => 'description',
					'Term ID' => 'TERMID',
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Term ID']);
			}if($import_types == 'event-tags'){
				$wordpressfields['Tag Image'] = 'image';
				$wordpressfields['Tag Color'] = 'color';
			}	
		}

		if($import_type === 'WooCommerceOrders'){
			$wordpressfields = array(
					'Customer Note' => 'customer_note',
					'Order Status' => 'order_status',
					'Order Date' => 'order_date',
					'Order Id' => 'ORDERID'
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Order Id']);
			}
		}

		if($import_type === 'WooCommerceCoupons'){
			$wordpressfields = array(
					'Coupon Code' => 'coupon_code',
					'Description' => 'description',
					'Date' => 'coupon_date',
					'Status' => 'coupon_status',
					'Coupon Id' =>'COUPONID'
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Coupon Id']);
			}
		}

		if($import_type === 'WooCommerceRefunds' ){
			$wordpressfields = array(
					'Post Parent' => 'post_parent',
					'Post Excerpt' => 'post_excerpt',
					'Refund Id' => 'REFUNDID'
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Refund Id']);
			}
		}

		if($import_type === 'WooCommerceVariations'){
			$wordpressfields = array(
					'Product Id' => 'PRODUCTID',
					'Parent Sku' => 'PARENTSKU',
					'Variation Sku' => 'VARIATIONSKU',
					'Variation ID' => 'VARIATIONID',
					'Featured Image' => 'featured_image',
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Variation ID']);
			}
		}

		if($import_type == 'WooCommerceCategories') { 
			$wordpressfields=array( 
					'Category Name'=>'name',                                
					'Category slug'=>'slug',                                
					'Category Description'=>'description',                  
					'Term ID'=>'term_id',                                   
					'Display type'=>'display_type',                         
					'Featured image'=>'featured_image',                     
					'Parent' =>'post_parent' 
					); 
			if($mode == 'Insert'){
				unset($wordpressfields['Term ID']);
			}  
		}

		if($import_type == 'WooCommercetags') {
			$wordpressfields=array(
					'Tag Name'=>'name',
					'Tag Slug'=>'slug',
					'Tag Description' =>'description',
					'Term ID'=>'TERMID'
					);
			if($mode == 'Insert'){
				unset($wordpressfields['Term ID']);
			}

		}
		if($import_type == 'WooCommerceattribute') {
			$wordpressfields=array( 
					'Name'=>'name',
					'Slug'=>'slug',
					'Enable Archives'=>'enable_archive',
					'Default sort order'=>'default_sort_order',
					'Configure terms'=>'configure_terms'
					);
		}			

		if($import_type === 'CustomerReviews') {
			if(is_plugin_active('wp-customer-reviews/wp-customer-reviews-3.php') || is_plugin_active('wp-customer-reviews/wp-customer-reviews.php')){
				$wordpressfields = array(
					'Review Date Time' => 'date_time',
					'Reviewer Name' => 'review_name',
					'Reviewer Email' => 'review_email',
					'Reviewer IP' => 'review_ip',
					'Review Format' => 'review_format',
					'Review Title' => 'review_title',
					'Review Text' => 'review_text',
					'Review Response' => 'review_admin_response',
					'Review Status' => 'status',
					'Review Rating' => 'review_rating',
					'Review URL' => 'review_website',
					'Review to Post/Page Id' => 'review_post',
					'Review ID' => 'review_id',
					);
				if($mode == 'Insert'){
					unset($wordpressfields['Review ID']);
				}
			}
		}
		if($import_type === 'MarketPressVariations'){
			$wordpressfields['Product Id'] = 'PRODUCTID';
			$wordpressfields['Variation ID'] = 'VARIATIONID';
		}
		$wordpress_value = $this->convert_static_fields_to_array($wordpressfields);
		$response['core_fields'] = $wordpress_value ;
		return $response;
	} 

	/**
	* Core Fields extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
	public function extensionSupportedImportType($import_type){
		return true;
	}

}
