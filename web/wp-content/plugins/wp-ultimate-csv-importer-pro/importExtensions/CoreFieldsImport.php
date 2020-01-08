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

class CoreFieldsImport {
	private static $core_instance = null,$media_instance,$nextgen_instance;
	public $detailed_log;
	public static function getInstance() {

		if (CoreFieldsImport::$core_instance == null) {
			CoreFieldsImport::$core_instance = new CoreFieldsImport;
			CoreFieldsImport::$media_instance = new MediaHandling;
			CoreFieldsImport::$nextgen_instance = new NextGenGalleryImport;
			return CoreFieldsImport::$core_instance;
		}
		return CoreFieldsImport::$core_instance;
	}

	function set_core_values($header_array ,$value_array , $map , $type , $mode , $line_number , $check , $hash_key){
		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();
		CoreFieldsImport::$media_instance->header_array = $header_array;
		CoreFieldsImport::$media_instance->value_array = $value_array;
		$log_table_name = $wpdb->prefix ."import_detail_log";

		$taxonomies = get_taxonomies();
		if (in_array($type, $taxonomies)) {

			$import_type = $type;
			if($import_type == 'category' || $import_type == 'product_category' || $import_type == 'product_cat' || $import_type == 'wpsc_product_category' || $import_type == 'event-categories'):
				$type = 'Categories';
			elseif($import_type == 'product_tag' || $import_type == 'event-tags' || $import_type == 'post_tag'):
				$type = 'Tags';
		else:
			$type = 'Taxonomies';
			endif;
		}

		if(($type == 'WooCommerce Product Variations' ) || ($type == 'WooCommerce Orders') || ($type == 'WooCommerce Coupons') || ($type == 'WooCommerce Refunds') || ($type == 'WooCommerce Attributes') || ($type == 'WooCommerce Tags') || ($type == 'WooCommerce Product') || ($type == 'Categories') || ($type == 'Tags') || ($type == 'Taxonomies') || ($type == 'Comments') || ($type == 'Users') || ($type == 'Customer Reviews') || ($type == 'WPeCommerce Products') || ($type == 'WPeCommerce Coupons') || ($type == 'MarketPress Product') || ($type == 'MarketPress Product Variations') || ($type == 'eShop Products')){

			$woocommerce_core_instance = WooCommerceCoreImport::getInstance();
			$taxonomies_instance = TaxonomiesImport::getInstance();
			$users_instance = UsersImport::getInstance();
			$comments_instance = CommentsImport::getInstance();
			$wpecommerce_instance = WPeCommerceImport::getInstance();
			$marketpress_instance = MarketPressImport::getInstance();	
			$customer_reviews_instance = CustomerReviewsImport::getInstance();
			$eshop_instance = EshopImport::getInstance();

			$post_values = [];
			$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		

			if($type == 'WooCommerce Product'){
				$result = $woocommerce_core_instance->woocommerce_product_import($post_values , $mode , $check , $hash_key , $line_number);
			}
			if($type == 'WooCommerce Orders'){
				$result = $woocommerce_core_instance->woocommerce_orders_import($post_values , $mode , $check , $hash_key , $line_number);
			}
			if($type == 'WooCommerce Product Variations'){
				$result = $woocommerce_core_instance->woocommerce_variations_import($post_values , $mode , $check ,$hash_key , $line_number);
			}
			if($type == 'WooCommerce Coupons'){
				$result = $woocommerce_core_instance->woocommerce_coupons_import($post_values , $mode , $check , $hash_key , $line_number);
			}
			if($type == 'WooCommerce Refunds'){
				$result = $woocommerce_core_instance->woocommerce_refunds_import($post_values , $mode , $check , $hash_key , $line_number);
			}
			if($type == 'WooCommerce Attributes'){
				$result = $woocommerce_core_instance->woocommerce_attributes_import($post_values , $mode , $check ,$hash_key , $line_number);
			}
			if($type == 'WooCommerce Tags'){
				$result = $woocommerce_core_instance->woocommerce_tags_import($post_values , $mode , $check , $hash_key , $line_number);
			}

			if(($type == 'Categories') || ($type == 'Tags') || ($type == 'Taxonomies') ){
				$result = $taxonomies_instance->taxonomies_import_function($post_values , $mode , $import_type , $check , $hash_key ,$line_number ,$header_array ,$value_array);
			}
			if($type == 'Users'){
				$result = $users_instance->users_import_function($post_values , $mode ,$hash_key , $line_number);
			}
			if($type == 'Comments'){
				$result = $comments_instance->comments_import_function($post_values , $mode ,$hash_key , $line_number);
			}
			if($type == 'WPeCommerce Products'){
				$result = $wpecommerce_instance->wpecommerce_product_import($post_values , $mode , $check , $hash_key , $line_number);
			}
			if($type == 'WPeCommerce Coupons'){
				$result = $wpecommerce_instance->wpecommerce_coupons_import($post_values , $mode ,$hash_key , $line_number);
			}
			if($type == 'MarketPress Product'){
				$result = $marketpress_instance->marketpress_product_import($post_values , $mode , $check , $hash_key , $line_number);
			}
			if($type == 'MarketPress Product Variations'){
				$result = $marketpress_instance->marketpress_variation_import($post_values , $mode ,$hash_key  ,$line_number);
			}	
			if($type == 'Customer Reviews'){
				$result = $customer_reviews_instance->customer_reviews_import($post_values , $mode , $check ,$hash_key , $line_number);
			}
			if($type == 'eShop Products'){
				$result = $eshop_instance->eshop_product_import($post_values , $mode , $check ,$hash_key , $line_number);
			}

			$last_import_id = isset($result['ID']) ? $result['ID'] : '';
			$post_id = $result['ID'];
			$helpers_instance->get_post_ids($post_id ,$hash_key);

			if(isset($post_values['featured_image'])) {	
				if ( preg_match_all( '/\b[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $post_values['featured_image'], $matchedlist, PREG_PATTERN_ORDER ) ) {	
					$image_type = 'Featured';		
					$attach_id = CoreFieldsImport::$media_instance->media_handling( $post_values['featured_image'] , $post_id ,$post_values,$type,$image_type,$hash_key,$header_array,$value_array);	
				}
			}
			
			if(preg_match("(Can't|Skipped|Duplicate)", $this->detailed_log[$line_number]['Message']) === 0) {  
				if ( $type == 'WooCommerce Product' || $type == 'MarketPress Product' || $type == 'eShop Products' || $type == 'WPeCommerce Products') {
					if ( ! isset( $post_values['post_title'] ) ) {
						$post_values['post_title'] = '';
					}
					$this->detailed_log[$line_number]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_permalink( $post_id ) . "' target='_blank' title='" . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $post_values['post_title'] ) ) . "'rel='permalink'>Web View</a> | <a href='" . get_edit_post_link( $post_id, true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
				}
				elseif( $type == 'Users'){
					$this->detailed_log[$line_number]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_edit_user_link( $post_id , true ) . "' target='_blank' title='" . esc_attr( 'Edit this item' ) . "'> User Profile </a>";
				}
				elseif($type == 'WooCommerce Orders' || $type == 'WooCommerce Coupons'){
					$this->detailed_log[$line_number]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_edit_post_link( $post_id, true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
				}
				else{
					$this->detailed_log[$line_number]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_permalink( $post_id ) . "' target='_blank' title='" . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $post_values['post_title'] ) ) . "'rel='permalink'>Web View</a> | <a href='" . get_edit_post_link( $post_id, true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
				}
				if(isset($post_values['post_status'])){
					$this->detailed_log[$line_number]['  Status'] = $post_values['post_status'];
				}	
			}
			
			return $post_id;
		}
		elseif($type == 'Images'){
			$post_values = [];
			foreach($map as $key => $value){
				$csv_value= trim($map[$key]);
				if(!empty($csv_value)){
					$get_key= array_search($csv_value , $header_array);
					if(isset($value_array[$get_key])){
						$csv_element = $value_array[$get_key];	
						$wp_element= trim($key);
						if(!empty($csv_element) && !empty($wp_element)){
							$post_values[$wp_element] = $csv_element;
						}
					}
				}
			}
			$result = CoreFieldsImport::$media_instance->image_import($post_values);
			$this->detailed_log[$line_number]['Message'] = 'Inserted Images '  . ' ID: ' . $result ;
			$this->detailed_log[$line_number]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_edit_post_link( $result, true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
		
		}
		elseif($type == 'ngg_pictures'){
			$post_values = [];
			foreach($map as $key => $value){
				$csv_value= trim($map[$key]);
				if(!empty($csv_value)){
					$get_key= array_search($csv_value , $header_array);
					if(isset($value_array[$get_key])){
						$csv_element = $value_array[$get_key];	
						$wp_element= trim($key);
						if(!empty($csv_element) && !empty($wp_element)){
							$post_values[$wp_element] = $csv_element;
						}
					}
				}
			}
			$result = CoreFieldsImport::$nextgen_instance->nextgenGallery($post_values);
		}
		else{

			$post_values = [];
			foreach($map as $key => $value){
				$csv_value = trim($map[$key]);

				if(!empty($csv_value)){
					$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
					if(preg_match_all($pattern, $csv_value, $matches, PREG_PATTERN_ORDER)){	
						
						$csv_element = $csv_value;
						foreach($matches[2] as $value){
							$get_key = array_search($value , $header_array);
							if(isset($value_array[$get_key])){
								$csv_value_element = $value_array[$get_key];	
								
								$value = '{'.$value.'}';
								$csv_element = str_replace($value, $csv_value_element, $csv_element);	
							}
						}

						$math = 'MATH(';
						if (strpos($csv_element, $math) !== false) {	
							$equation = str_replace('MATH(', '', $csv_element);
							$equation = str_replace(')', '', $equation);
							$csv_element = $helpers_instance->evalmath($equation);
						}

						$wp_element= trim($key);
						$extension_object = new ExtensionHandler;
						$import_type = $extension_object->import_type_as($type);
						$import_as = $extension_object->import_post_types($import_type );

						if(!empty($csv_element) && !empty($wp_element)){
							$post_values[$wp_element] = $csv_element;	
							$post_values['post_type'] = $import_as;
							$post_values = $this->import_core_fields($post_values);
						}
					}

					elseif(!in_array($csv_value , $header_array)){
						$wp_element= trim($key);
						$post_values[$wp_element] = $csv_value;
					}

					else{
						$get_key= array_search($csv_value , $header_array);

						if(isset($value_array[$get_key])){
							$csv_element = $value_array[$get_key];	
							$wp_element= trim($key);
							$extension_object = new ExtensionHandler;
							$import_type = $extension_object->import_type_as($type);
							$import_as = $extension_object->import_post_types($import_type );

							if(!empty($csv_element) && !empty($wp_element)){
								$post_values[$wp_element] = $csv_element;
								$post_values['post_type'] = $import_as;
								$post_values = $this->import_core_fields($post_values);	

								if($import_as == 'page'){
									if(isset($post_values['post_parent'])){
										if(!is_numeric($post_values['post_parent'])){
											$post_parent_title = $post_values['post_parent'];
											$post_parent_id = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$post_parent_title' AND post_type = 'page'");
											$post_values['post_parent'] = $post_parent_id;
										}
									}
								}
							}
						}
					}
				}
			}
			
			if($check == 'ID'){	
				$ID = $post_values['ID'];	
				$get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE ID = '$ID' AND post_type = '$import_as' AND post_status != 'trash' order by ID DESC ");			
			}
			if($check == 'post_title'){
				$title = $post_values['post_title'];
				$title = $wpdb->_real_escape($title);
				$get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND post_type = '$import_as' AND post_status != 'trash' order by ID DESC ");		
			}
			if($check == 'post_name'){
				$name = $post_values['post_name'];
				$get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_name = '$name' AND post_type = '$import_as' AND post_status != 'trash' order by ID DESC ");	
			}
			if($check == 'post_content'){
				$content = $post_values['post_content'];
				$get_result =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_content = '$content' AND post_type = '$import_as' AND post_status != 'trash' order by ID DESC ");	
			}

			$updated_row_counts = $helpers_instance->update_count($hash_key);
			$created_count = $updated_row_counts['created'];
			$updated_count = $updated_row_counts['updated'];
			$skipped_count = $updated_row_counts['skipped'];

			if($mode == 'Insert'){

				if (is_array($get_result) && !empty($get_result)) {
					$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
					$this->detailed_log[$line_number]['Message'] =  "Skipped, Due to duplicate found!.";
				}else{
					$media_handle = get_option('smack_image_options');
					if($media_handle['media_settings']['media_handle_option'] == 'true'){
					if(preg_match("/<img/", $post_values['post_content'])) {

						$content = "<p>".$post_values['post_content']."</p>";
						$doc = new \DOMDocument();
						#$doc->preserveWhiteSpace = false;
						if(function_exists('mb_convert_encoding')) {
							@$doc->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
						}else{
							@$doc->loadHTML( $content);
						}
						$searchNode = $doc->getElementsByTagName( "img" );
						if ( ! empty( $searchNode ) ) {
							foreach ( $searchNode as $searchNode ) {
								$orig_img_src = $searchNode->getAttribute( 'src' ); 			
								$media_dir = wp_get_upload_dir();
								$names = $media_dir['url'];

								if (strpos($orig_img_src , $names) !== false) {
									$shortcode_img = $orig_img_src;
									$image_table = $wpdb->prefix . "ultimate_csv_importer_media";
									$medias_fields = $wpdb->get_results("INSERT INTO $image_table (`image_url` ,  `post_id`,`hash_key`,`module`,`image_type`,`status`) VALUES ( '{$shortcode_img}', $post_id  ,'{$hash_key}','{$type}','{$image_type}','Completed')");
								}
								else{
									$rand = mt_rand(1, 999);	
									$shortcode_table = $wpdb->prefix . "ultimate_csv_importer_shortcode_manager";
									$get_shortcode = $wpdb->get_results("SELECT `image_shortcode` FROM $shortcode_table WHERE original_image = '{$orig_img_src}' ",ARRAY_A);
									if(!empty($get_shortcode)) 
									{
										$shortcode_img = $get_shortcode[0]['image_shortcode'];
									}		
									else{
										$shortcode_img = 'inline_'.$rand.'_'.$orig_img_src;
									}
								}
								$temp_img = plugins_url("../assets/images/loading-image.jpg", __FILE__);
								$searchNode->setAttribute( 'src', $temp_img);
								$searchNode->setAttribute( 'alt', $shortcode_img );
							}
							$post_content              = $doc->saveHTML();
							$post_values['post_content'] = $post_content;
							$update_content['ID']           = $post_id;
							$update_content['post_content'] = $post_content;
							wp_update_post( $update_content );
						}
					}
					}
					$format=trim($post_values['post_format'],"post-format-");
					$post_id = wp_insert_post($post_values);
					set_post_format($post_id ,$format );
					$fields = $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");

					if(preg_match("/<img/", $post_values['post_content'])) {
						
						$shortcode_table = $wpdb->prefix . "ultimate_csv_importer_shortcode_manager";
						$medias_fields = $wpdb->get_results("INSERT INTO $shortcode_table (image_shortcode , original_image , post_id,hash_key) VALUES ( '{$shortcode_img}', '{$orig_img_src}', $post_id  ,'{$hash_key}')");
						$doc = new \DOMDocument();
						$searchNode = $doc->getElementsByTagName( "img" );
						if ( ! empty( $searchNode ) ) {
							foreach ( $searchNode as $searchNode ) {
						$orig_img_src = $searchNode->getAttribute( 'src' ); 
							}
						}			
						$media_dir = wp_get_upload_dir();
						$names = $media_dir['url'];
						if (strpos($orig_img_src , $names) !== false) {
							$image_table = $wpdb->prefix . "ultimate_csv_importer_media";
							$image_type = 'Inline' ;
							$medias_fields = $wpdb->get_results("INSERT INTO $image_table (`image_url`,`post_id`,`module`,`image_type`,`hash_key`,`status`) VALUES ( '{$orig_img_src}',  $post_id  ,'{$type}','{$image_type}','{$hash_key}','Completed')");
						}
						else{
							$image_table = $wpdb->prefix . "ultimate_csv_importer_media";
							$image_type = 'Inline' ;
							$medias_fields = $wpdb->get_results("INSERT INTO $image_table (`image_url` ,  `post_id`,`module`,`image_type`,`hash_key`) VALUES ( '{$orig_img_src}',  $post_id  ,'{$type}','{$image_type}','{$hash_key}')");
						}
					}
					if(is_wp_error($post_id) || $post_id == '') {	
						if(is_wp_error($post_id)) {
							$this->detailed_log[$line_number]['Message'] = "Can't insert this " . $post_values['post_type'] . ". " . $post_id->get_error_message();
						}
						else {
							$this->detailed_log[$line_number]['Message'] =  "Can't insert this " . $post_values['post_type'];
						}
						$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
					}	
					else{
						// WPML support on post types
						$this->detailed_log[$line_number]['Message'] = 'Inserted ' . $post_values['post_type'] . ' ID: ' . $post_id . ', ' . $post_values['specific_author'];
					}
					
					#TODO Events and Recurring events are importing records in draft, need to analyze code
					if($post_values['post_type'] == 'event' || $post_values['post_type'] == 'event-recurring'){
						$status = $post_values['post_status'];
						$wpdb->get_results("UPDATE {$wpdb->prefix}posts set post_status = '$status' where id = $post_id");
					}
				}
			}

			if($mode == 'Update'){
				if (is_array($get_result) && !empty($get_result)) {

					$post_id = $get_result[0]->ID;	
					$post_values['ID'] = $post_id;
					wp_update_post($post_values);
					$format=trim($post_values['post_format'],"post-format-");
					set_post_format($post_id , $format);	
					//$this->set_format($post_values , $post_id);
					$fields = $wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
					$this->detailed_log[$line_number]['Message'] = 'Updated' . $post_values['post_type'] . ' ID: ' . $post_id . ', ' . $post_values['specific_author'];

				}else{
					unset($post_values['ID']);
					$media_handle = get_option('smack_image_options');
					if($media_handle['media_settings']['media_handle_option'] == 'true'){
						if(preg_match("/<img/", $post_values['post_content'])) {

						$content = "<p>".$post_values['post_content']."</p>";
						$doc = new \DOMDocument();
						#$doc->preserveWhiteSpace = false;
						if(function_exists('mb_convert_encoding')) {
							@$doc->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
						}else{
							@$doc->loadHTML( $content);
						}
						$searchNode = $doc->getElementsByTagName( "img" );
						if ( ! empty( $searchNode ) ) {
							foreach ( $searchNode as $searchNode ) {
								$orig_img_src = $searchNode->getAttribute( 'src' ); 			
								$media_dir = wp_get_upload_dir();
								$names = $media_dir['url'];

								if (strpos($orig_img_src , $names) !== false) {
									$shortcode_img = $orig_img_src;
									$image_table = $wpdb->prefix . "ultimate_csv_importer_media";
									// $medias_fields = $wpdb->get_results("INSERT INTO $image_table (image_url ,  post_id,hash_key,module,image_type,status) VALUES ( '{$shortcode_img}', $post_id  ,'{$hash_key}','{$module}','{$image_type}','Completed')");
									$medias_fields = $wpdb->get_results("INSERT INTO $image_table (`image_url` ,  `post_id`,`hash_key`,`module`,`image_type`,`status`) VALUES ( '{$shortcode_img}', $post_id  ,'{$hash_key}','{$type}','{$image_type}','Completed')");
								}
								else{
									$rand = mt_rand(1, 999);	
									$shortcode_table = $wpdb->prefix . "ultimate_csv_importer_shortcode_manager";
									$get_shortcode = $wpdb->get_results("SELECT `image_shortcode` FROM $shortcode_table WHERE original_image = '{$orig_img_src}' ",ARRAY_A);
									if(!empty($get_shortcode)) 
									{
										$shortcode_img = $get_shortcode[0]['image_shortcode'];
									}		
									else{
										$shortcode_img = 'inline_'.$rand.'_'.$orig_img_src;
									}
								}
								$temp_img = plugins_url("../assets/images/loading-image.jpg", __FILE__);
								$searchNode->setAttribute( 'src', $temp_img );
								$searchNode->setAttribute( 'alt', $shortcode_img );
							}
							$post_content              = $doc->saveHTML();
							$post_values['post_content'] = $post_content;
							$update_content['ID']           = $post_id;
							$update_content['post_content'] = $post_content;
							wp_update_post( $update_content );
						}
					}
				}
					$format=trim($post_values['post_format'],"post-format-");
					$post_id = wp_insert_post($post_values);
					set_post_format($post_id , $format);
					$fields = $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
					
					if(preg_match("/<img/", $post_values['post_content'])) {
						$shortcode_table = $wpdb->prefix . "ultimate_csv_importer_shortcode_manager";
						$medias_fields = $wpdb->get_results("INSERT INTO $shortcode_table (image_shortcode , original_image , post_id,hash_key) VALUES ( '{$shortcode_img}', '{$orig_img_src}', $post_id  ,'{$hash_key}')");
					}
					if(is_wp_error($post_id) || $post_id == '') {
						if(is_wp_error($post_id)) {
							$this->detailed_log[$line_number]['Message'] = "Can't insert this " . $post_values['post_type'] . ". " . $post_id->get_error_message();
						}
						else {
							$this->detailed_log[$line_number]['Message'] =  "Can't insert this " . $post_values['post_type'];
						}
						$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
					}
					else{
						$this->detailed_log[$line_number]['Message'] = 'Inserted ' . $post_values['post_type'] . ' ID: ' . $post_id . ', ' . $post_values['specific_author'];	
					}

					#TODO Events and Recurring events are importing records in draft, need to analyze code
					if($post_values['post_type'] == 'event' || $post_values['post_type'] == 'event-recurring'){
						$status = $post_values['post_status'];
						$wpdb->get_results("UPDATE {$wpdb->prefix}posts set post_status = '$status' where id = $post_id");
					}
				}
			}

			if(preg_match("(Can't|Skipped|Duplicate)", $this->detailed_log[$line_number]['Message']) === 0) {  
				if ( $type == 'Posts' || $type == 'CustomPosts' || $type == 'Pages' || $type == 'Tickets') {
					if ( ! isset( $post_values['post_title'] ) ) {
						$post_values['post_title'] = '';
					}
					$this->detailed_log[$line_number]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_permalink( $post_id ) . "' target='_blank' title='" . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $post_values['post_title'] ) ) . "'rel='permalink'>Web View</a> | <a href='" . get_edit_post_link( $post_id, true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
				}
				else{
					$this->detailed_log[$line_number]['VERIFY'] = "<b> Click here to verify</b> - <a href='" . get_permalink( $post_id ) . "' target='_blank' title='" . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $post_values['post_title'] ) ) . "'rel='permalink'>Web View</a> | <a href='" . get_edit_post_link( $post_id, true ) . "'target='_blank' title='" . esc_attr( 'Edit this item' ) . "'>Admin View</a>";
				}
				$this->detailed_log[$line_number][' Status'] = $post_values['post_status'];
			}

			if(isset($post_values['featured_image'])) {	
				if ( preg_match_all( '/\b[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $post_values['featured_image'], $matchedlist, PREG_PATTERN_ORDER ) ) {	
					$image_type = 'Featured';		
					$attach_id = CoreFieldsImport::$media_instance->media_handling( $post_values['featured_image'] , $post_id ,$post_values,$type,$image_type,$hash_key,$header_array,$value_array);	
				}
			}	
					
			return $post_id;
		}
	}

function image_handling($id){

	global $wpdb;
	
	$post_values = [];
	$get_result =  $wpdb->get_results("SELECT post_content FROM {$wpdb->prefix}posts where ID = $id",ARRAY_A);   
	$post_values['post_content']=htmlspecialchars_decode($get_result[0]['post_content']);
	$get_result =  $wpdb->get_results("SELECT original_image FROM {$wpdb->prefix}ultimate_csv_importer_shortcode_manager where post_id = $id",ARRAY_A);   
	$orig_img_src = $get_result[0]['original_image'];
	$get_results =  $wpdb->get_results("SELECT image_shortcode FROM {$wpdb->prefix}ultimate_csv_importer_shortcode_manager where post_id = $id",ARRAY_A);   
	$origs_img_src = $get_results[0]['image_shortcode'];
	$image_type = 'Inline' ;
	//$attach_id = CoreFieldsImport::$media_instance->media_handling( $orig_img_src , $id ,$post_values,$type,$image_type,$hash_key);
	$attach_id = CoreFieldsImport::$media_instance->media_handling( $orig_img_src , $id ,$post_values,'',$image_type,'');
	$get_guid = $wpdb->get_results("SELECT `guid` FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' and ID =  $attach_id ");
	foreach($get_guid as $value){
		$replace_guid = $value->guid;
	}

	$result  = str_replace($origs_img_src , ' ' , $post_values['post_content']);
	$media_dir = wp_get_upload_dir();
	$temp_img = plugins_url("../assets/images/loading-image.jpg", __FILE__);
	$result = str_replace($temp_img , $replace_guid , $result);
	$media_dir = wp_get_upload_dir();
	$names = $media_dir['url'];					
	$sql = $wpdb->get_results(
			"UPDATE {$wpdb->prefix}posts SET post_content = '$result' WHERE ID = $id"	
			);
	$wpdb->query( $sql );
	return $id;
}


function import_core_fields($data_array){
	$helpers_instance = ImportHelpers::getInstance();

	if(!isset( $data_array['post_date'] )) {
		$data_array['post_date'] = current_time('Y-m-d H:i:s');
	} else {
		if(strtotime( $data_array['post_date'] )) {
			$data_array['post_date'] = date( 'Y-m-d H:i:s', strtotime( $data_array['post_date'] ) );
		} else {
			$data_array['post_date'] = current_time('Y-m-d H:i:s');
		}
	}

	if(!isset($data_array['post_author'])) {
		$data_array['post_author'] = 1;
	} else {
		if(isset( $data_array['post_author'] )) {
			$user_records = $helpers_instance->get_from_user_details( $data_array['post_author'] );
			$data_array['post_author'] = $user_records['user_id'];
			//$assigned_author = $user_records['message'];
			$data_array['specific_author'] = $user_records['message'];
		}
	}
	if ( !empty($data_array['post_status']) ) {
		$data_array = $helpers_instance->assign_post_status( $data_array );
	}else{
		$data_array['post_status'] = 'publish';
	}
	return $data_array;
}

}
