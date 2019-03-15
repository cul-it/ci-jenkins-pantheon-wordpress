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
 * Class SmackUCIExporter
 *
 * This helper class which helps you to export the data as CSV
 */
class SmackUCIExporter {

	/**
	 * Headers
	 *
	 * @var array   - Headers
	 */
	var $headers = array();

	/**
	 * Module
	 *
	 * @var string  -Ex: post (or) page (or) product
	 */
	var $module;

	/**
	 * Export file type
	 *
	 * @var string  -   Ex: csv (or) xml
	 */
	var $exportType = 'csv';

	/**
	 * Can specify the Taxonomy (or) Custom Posts name to be export
	 *
	 * @var null|string Can specify the Taxonomy (or) Custom Posts name to be export
	 */
	var $optionalType = null;

	/**
	 * Conditions for export data
	 *
	 * @var array   - Conditions for export data
	 */
	var $conditions = array();

	/**
	 * Export with specific columns, Can exclude the unwanted headers.
	 *
	 * @var array - Export with specific columns, Can exclude the unwanted headers.
	 */
	var $eventExclusions = array();

	/**
	 * Export the data with the specific filename.
	 *
	 * @var string  - Export the data with the specific filename.
	 */
	var $fileName;

	/**
	 * Offset
	 *
	 * @var int|string
	 */
	var $offset = 0;

	/**
	 * Limit
	 *
	 * @var int|string
	 */
	var $limit = 1000;

	/**
	 * Total row count
	 *
	 * @var - Total row count
	 */
	var $totalRowCount;

	/**
	 * CSV data array
	 *
	 * @var array
	 */
	var $data = array();

	/**
	 * CSV Header
	 *
	 * @var bool    - use first line/entry as field names
	 */
	var $heading = true;

	/**
	 * Delimiter
	 *
	 * @var string  - delimiter (comma)
	 */
	var $delimiter = ',';

	/**
	 * Enclosure
	 *
	 * @var string  - enclosure (double quote)
	 */
	var $enclosure = '"';

	/**
	 * Preferred delimiter characters, only used when all filtering method
	 * returns multiple possible delimiters (happens very rarely)
	 * @var string
	 *
	 */
	var $auto_preferred = ",;\t.:|";

	/**
	 * Only used by output() function
	 *
	 * @var string  - only used by output() function
	 */
	var $output_delimiter = ',';

	/**
	 * Line Feed
	 *
	 * @var string  - Line Feed
	 */
	var $linefeed = "\r\n";

	var $export_mode;

	var $export_log = array();

	/**
	 * SmackUCIExporter constructor.
	 *
	 * Set values into global variables based on post value
	 */
	public function __construct() {
		if(!empty($_POST)) {
			$this->module          = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : '';
			$this->exportType      = isset( $_POST['exp_type'] ) ? sanitize_text_field( $_POST['exp_type'] ) : 'csv';
			$this->conditions      = isset( $_POST['conditions'] ) && ! empty( $_POST['conditions'] ) ? $_POST['conditions'] : array();
			$this->optionalType    = isset( $_POST['optionalType'] ) ? sanitize_text_field( $_POST['optionalType'] ) : '';
			$this->eventExclusions = isset( $_POST['eventExclusions'] ) && ! empty( $_POST['eventExclusions'] ) ? $_POST['eventExclusions'] : array();
			$this->fileName        = isset( $_POST['fileName'] ) ? sanitize_text_field( $_POST['fileName'] ) : ''; //'Post.csv';
			$this->offset          = isset( $_POST['offset'] ) ? sanitize_text_field( $_POST['offset'] ) : 0;
			$this->limit           = isset( $_POST['limit'] ) ? sanitize_text_field( $_POST['limit'] ) : 1000;
			$this->delimiter       = isset($_POST['conditions']['delimiter']) ? $this->setDelimiter( $_POST['conditions']['delimiter'] ) : ',';
			$this->export_mode = 'normal';
			$this->checkSplit = isset( $_POST['is_check_split'] ) ? sanitize_text_field( $_POST['is_check_split'] ) : 'no';
		}
		$this->exportData();
	}

	/**
	 * set the delimiter
	 */
	public function setDelimiter($conditions)
	{
		if(isset($conditions['delimiter']) && $conditions['delimiter'] != 'Select'){
			if($conditions['delimiter'] == '{Tab}')
				return "\t";
			elseif ($conditions['delimiter'] == '{Space}')
				return " ";
			else
				return $conditions['delimiter'];
		}
		elseif (isset($conditions['optional_delimiter']) && $conditions['optional_delimiter'] != '') {
			return $conditions['optional_delimiter'];
		}
		else{
			return ',';
		}
	}

	/**
	 * Export DB based on the requested module
	 */
	public function exportData( ) {
		global $uci_admin;
		switch ($this->module) {
			case 'Posts':
			case 'Pages':
			case 'CustomPosts':
			case 'WooCommerce':
			case 'MarketPress':
			case 'WooCommerceVariations':
			case 'WooCommerceOrders':
			case 'WooCommerceCoupons':
			case 'WooCommerceRefunds':
			case 'WPeCommerce':
			case 'eShop':
				$this->FetchDataByPostTypes();
				break;
			case 'Users':
				$this->FetchUsers();
				break;
			case 'Comments':
				$this->FetchComments();
				break;
			case 'Taxonomies':
				$this->FetchTaxonomies();
				break;
			case 'CustomerReviews':
				$this->FetchCustomerReviews();
				break;
			case 'Categories':
				$this->FetchCategories();
				break;
			case 'Tags':
				$this->FetchTags();
				break;
		}
	}

	/**
	 * Generate CSV headers
	 *
	 * @param $module       - Module to be export
	 * @param $optionalType - Exclusions
	 */
	public function generateHeaders ($module, $optionalType) {
		global $uci_admin;
		$integrations = $uci_admin->available_widgets($module, $optionalType);
		$headers = array();
		if(!empty($integrations)) :
			foreach($integrations as $widget_name => $group_name) {
				if($module == 'CustomPosts')
				$fields = $uci_admin->get_widget_fields($widget_name, $optionalType, 'export');
				else
				 $fields = $uci_admin->get_widget_fields($widget_name, $module, $optionalType, 'export');
				if(!empty($fields)) {
					foreach($fields as $groupKey => $fieldArray) {
						if(!empty($fieldArray)) {
							foreach ( $fieldArray as $fKey => $fVal ) {
								if(!in_array($fVal['name'], $headers))
									$headers[] = $fVal['name'];
							}
						}
					}
				}
			}
		endif;
		if(isset($this->eventExclusions['is_check']) && $this->eventExclusions['is_check'] == 'true') :
			$headers_with_exclusion = $this->applyEventExclusion($headers);
			$this->headers = $headers_with_exclusion;
		else:
			$this->headers = $headers;
		endif;
	}

	/**
	 * Fetch data by requested Post types
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchDataByPostTypes ($mode = null) {
		if(empty($this->headers))
			$this->generateHeaders($this->module, $this->optionalType);
		$recordsToBeExport = $this->get_records_based_on_post_types($this->module, $this->optionalType, $this->conditions);
		if(!empty($recordsToBeExport)) :
			foreach($recordsToBeExport as $postId) {
				$this->data[$postId] = $this->getPostsDataBasedOnRecordId($postId);
				$this->getPostsMetaDataBasedOnRecordId($postId, $this->module, $this->optionalType);
				$this->getTermsAndTaxonomies($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceVariations')
					$this->getProductData($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceOrders')
					$this->getWooComOrderData($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceRefunds')
					$this->getWooComCustomerUser($postId, $this->module, $this->optionalType);
				if($this->module == 'WPeCommerce')
					$this->getEcomData($postId, $this->module, $this->optionalType);
			}
		endif;
		
		$result = $this->finalDataToExport($this->data);
		if($mode == null)
			$this->proceedExport( $result );
		else
			return $result;
	}


	/**
	 * Get user role based on the capability
	 *
	 * @param null $capability  - User capability
	 *
	 * @return int|string       - Role of the user
	 */
	public function getUserRole ($capability = null) {
		if($capability != null) {
			$getRole = unserialize($capability);
			foreach($getRole as $roleName => $roleStatus) {
				$role = $roleName;
			}
			return $role;
		} else {
			return 'subscriber';
		}
	}

	/**
	 * Fetch users and their meta information
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchUsers($mode = null) {
		global $wpdb;
		$this->generateHeaders($this->module, $this->optionalType);
		$get_available_user_ids = "select DISTINCT ID from $wpdb->users u join $wpdb->usermeta um on um.user_id = u.ID";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			$get_available_user_ids .= " where u.user_registered >= '" . $this->conditions['specific_period']['from'] . "' and u.user_registered <= '" . $this->conditions['specific_period']['to'] . "'";
		}
		$get_available_user_ids .= " order by ID asc limit $this->offset, $this->limit";
		$availableUsers = $wpdb->get_col($get_available_user_ids);
		$this->totalRowCount = count($availableUsers);
		if(!empty($availableUsers)) {
			$whereCondition = '';
			foreach($availableUsers as $userId) {
				if($whereCondition != ''):
					$whereCondition = $whereCondition . ',' . $userId;
				else:
					$whereCondition = $userId;
				endif;
				// Prepare the user details to be export
				$query_to_fetch_users = "SELECT * FROM $wpdb->users where ID in ($whereCondition);";
				$users = $wpdb->get_results($query_to_fetch_users);
				if(!empty($users)) {
					foreach($users as $userInfo) {
						foreach($userInfo as $userKey => $userVal) {
							$this->data[$userId][$userKey] = $userVal;
						}
					}
				}
				// Prepare the user meta details to be export
				$query_to_fetch_users_meta = $wpdb->prepare("SELECT user_id, meta_key, meta_value FROM  $wpdb->users wp JOIN $wpdb->usermeta wpm  ON wpm.user_id = wp.ID where ID= %d", $userId);
				$userMeta = $wpdb->get_results($query_to_fetch_users_meta);

				$wptypesfields = get_option('wpcf-usermeta');
		
				if(!empty($wptypesfields)){
					$i = 1;
					foreach ($wptypesfields as $key => $value) {
						$typesf[$i] = 'wpcf-'.$key;
						$i++;
					}
				}

				if(!empty($userMeta)) {
					foreach($userMeta as $userMetaInfo) {
						if($userMetaInfo->meta_key == 'wp_capabilities') {
							$userRole = $this->getUserRole($userMetaInfo->meta_value);
							$this->data[ $userId ][ 'role' ] = $userRole;
						}
						elseif(in_array($userMetaInfo->meta_key, $typesf) && is_serialized($userMetaInfo->meta_value)){
									$typefileds = unserialize($userMetaInfo->meta_value);
									$typedata = "";
									foreach ($typefileds as $key2 => $value2) {
										if(is_array($value2)){
											foreach ($value2 as $key3 => $value3) {
												$typedata .= $value3.',';
											}
										}
										else
										 $typedata .= $value2.',';
									}
									$this->data[ $userId ][ $userMetaInfo->meta_key ] = substr($typedata, 0, -1);
						}
						else {
							$this->data[ $userId ][ $userMetaInfo->meta_key ] = $userMetaInfo->meta_value;
						}
					}
				}
			}
		}

		$result = $this->finalDataToExport($this->data);
		if($mode == null)
			$this->proceedExport($result);
		else
			return $result;
	}

	/**
	 * Fetch Terms & Taxonomies
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchTaxonomies($mode = null) {
		global $uci_admin;
		$this->generateHeaders($this->module, $this->optionalType);
		$get_all_taxonomies = get_terms( $this->optionalType, 'orderby=count&hide_empty=0' );
		$this->totalRowCount = count($get_all_taxonomies);
		if(!empty($get_all_taxonomies)) {
			foreach( $get_all_taxonomies as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termMeta = get_term_meta($termID);
				if(isset($termMeta['thumbnail_id'][0])){
					$thum_id = $termMeta['thumbnail_id'][0];
					$this->data[$termID]['image'] = $this->getAttachment($thum_id);
				}
				$termName = $termValue->name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->description;
				$termParent = $termValue->parent;
				if($termParent == 0) {
					$this->data[$termID]['name'] = $termName;
				} else {
					$termParentName = get_cat_name( $termParent );
					$this->data[$termID]['name'] = $termParentName . '|' . $termName;
				}
				$this->data[$termID]['slug'] = $termSlug;
				$this->data[$termID]['description'] = $termDesc;
				$this->data[$termID]['TERMID'] = $termID;
			}
		}
		if(in_array('wordpress-seo/wp-seo.php', $uci_admin->get_active_plugins())) {
			$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
			if ( isset( $seo_yoast_taxonomies[$this->optionalType] ) ) {
				foreach ( $seo_yoast_taxonomies[$this->optionalType] as $taxoKey => $taxoValue ) {
					$taxoID = $taxoKey;
					$this->data[ $taxoID ]['title'] = $taxoValue['wpseo_title'];
					$this->data[ $taxoID ]['meta_desc'] = $taxoValue['wpseo_desc'];
					$this->data[ $taxoID ]['canonical'] = $taxoValue['wpseo_canonical'];
					$this->data[ $taxoID ]['meta-robots-noindex'] = $taxoValue['wpseo_noindex'];
					$this->data[ $taxoID ]['sitemap-include'] = $taxoValue['wpseo_sitemap_include'];
					$this->data[ $taxoID ]['opengraph-title'] = $taxoValue['wpseo_opengraph-title'];
					$this->data[ $taxoID ]['opengraph-description'] = $taxoValue['wpseo_opengraph-description'];
					$this->data[ $taxoID ]['twitter-title'] = $taxoValue['wpseo_twitter-title'];
					$this->data[ $taxoID ]['twitter-description'] = $taxoValue['wpseo_twitter-description'];
					$this->data[ $taxoID ]['focus_keyword'] = $taxoValue['wpseo_focuskw'];
				}
			}
		}
		$result = $this->finalDataToExport($this->data);
		if($mode == null)
			$this->proceedExport($result);
		else
			return $result;
	}

	/**
	 * Fetch all Categories
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchCategories($mode = null) {
		global $uci_admin;
		$this->generateHeaders($this->module, $this->optionalType);
		$get_all_terms = get_categories('hide_empty=0');
		$this->totalRowCount = count($get_all_terms);
		if(!empty($get_all_terms)) {
			foreach( $get_all_terms as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termName = $termValue->cat_name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->category_description;
				$termParent = $termValue->parent;
				if($termParent == 0) {
					$this->data[$termID]['name'] = $termName;
				} else {
					$termParentName = get_cat_name( $termParent );
					$this->data[$termID]['name'] = $termParentName . '|' . $termName;
				}
				$this->data[$termID]['slug'] = $termSlug;
				$this->data[$termID]['description'] = $termDesc;
			}
		}
		if(in_array('wordpress-seo/wp-seo.php', $uci_admin->get_active_plugins())) {
			$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
			if ( isset( $seo_yoast_taxonomies['category'] ) ) {
				foreach ( $seo_yoast_taxonomies['category'] as $taxoKey => $taxoValue ) {
					$taxoID = $taxoKey;
					$this->data[ $taxoID ]['title'] = $taxoValue['wpseo_title'];
					$this->data[ $taxoID ]['meta_desc'] = $taxoValue['wpseo_desc'];
					$this->data[ $taxoID ]['canonical'] = $taxoValue['wpseo_canonical'];
					$this->data[ $taxoID ]['meta-robots-noindex'] = $taxoValue['wpseo_noindex'];
					$this->data[ $taxoID ]['sitemap-include'] = $taxoValue['wpseo_sitemap_include'];
					$this->data[ $taxoID ]['opengraph-title'] = $taxoValue['wpseo_opengraph-title'];
					$this->data[ $taxoID ]['opengraph-description'] = $taxoValue['wpseo_opengraph-description'];
					$this->data[ $taxoID ]['twitter-title'] = $taxoValue['wpseo_twitter-title'];
					$this->data[ $taxoID ]['twitter-description'] = $taxoValue['wpseo_twitter-description'];
					$this->data[ $taxoID ]['focus_keyword'] = $taxoValue['wpseo_focuskw'];
				}
			}
		}
		$result = $this->finalDataToExport($this->data);
		if($mode == null)
			$this->proceedExport($result);
		else
			return $result;
	}

	/**
	 * Fetch all Tags
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchTags($mode = null) {
		global $uci_admin;
		$this->generateHeaders($this->module, $this->optionalType);
		$get_all_terms = get_tags('hide_empty=0');
		$this->totalRowCount = count($get_all_terms);
		if(!empty($get_all_terms)) {
			foreach( $get_all_terms as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termName = $termValue->name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->description;
				$this->data[$termID]['name'] = $termName;
				$this->data[$termID]['slug'] = $termSlug;
				$this->data[$termID]['description'] = $termDesc;
			}
		}
		if(in_array('wordpress-seo/wp-seo.php', $uci_admin->get_active_plugins())) {
			$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
			if ( isset( $seo_yoast_taxonomies['post_tag'] ) ) {
				foreach ( $seo_yoast_taxonomies['post_tag'] as $taxoKey => $taxoValue ) {
					$taxoID = $taxoKey;
					$this->data[ $taxoID ]['title'] = $taxoValue['wpseo_title'];
					$this->data[ $taxoID ]['meta_desc'] = $taxoValue['wpseo_desc'];
					$this->data[ $taxoID ]['canonical'] = $taxoValue['wpseo_canonical'];
					$this->data[ $taxoID ]['meta-robots-noindex'] = $taxoValue['wpseo_noindex'];
					$this->data[ $taxoID ]['sitemap-include'] = $taxoValue['wpseo_sitemap_include'];
					$this->data[ $taxoID ]['opengraph-title'] = $taxoValue['wpseo_opengraph-title'];
					$this->data[ $taxoID ]['opengraph-description'] = $taxoValue['wpseo_opengraph-description'];
					$this->data[ $taxoID ]['twitter-title'] = $taxoValue['wpseo_twitter-title'];
					$this->data[ $taxoID ]['twitter-description'] = $taxoValue['wpseo_twitter-description'];
					$this->data[ $taxoID ]['focus_keyword'] = $taxoValue['wpseo_focuskw'];
				}
			}
		}
		$result = $this->finalDataToExport($this->data);
		if($mode == null)
			$this->proceedExport($result);
		else
			return $result;
	}

	/**
	 * Fetch all Customer Reviews
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchCustomerReviews($mode = null) {
		global $wpdb;
		$headers = array();
		$get_customer_reviews = "select DISTINCT ID from $wpdb->posts p join $wpdb->postmeta pm ";
		$get_customer_reviews .= " where p.post_type = '$this->optionalType'";
		// Check for specific status
		if($this->conditions['specific_status']['status'] == 'true') {
			if(isset($this->conditions['specific_status']['status']) && sanitize_text_field($this->conditions['specific_status']['status']) == 'All') {
				$get_customer_reviews .= " and p.post_status in ('publish','draft','future','private','pending')";
			} else if(isset($this->conditions['specific_status']['status']) && (sanitize_text_field($this->conditions['specific_status']['status']) == 'Publish' || sanitize_text_field($this->conditions['specific_status']['status']) == 'Sticky')) {
				$get_customer_reviews .= " and p.post_status in ('publish')";
			} else if(isset($this->conditions['specific_status']['status']) && sanitize_text_field($this->conditions['specific_status']['status']) == 'Draft') {
				$get_customer_reviews .= " and p.post_status in ('draft')";
			} else if(isset($this->conditions['specific_status']['status']) && sanitize_text_field($this->conditions['specific_status']['status']) == 'Scheduled') {
				$get_customer_reviews .= " and p.post_status in ('future')";
			} else if(isset($this->conditions['specific_status']['status']) && sanitize_text_field($this->conditions['specific_status']['status']) == 'Private') {
				$get_customer_reviews .= " and p.post_status in ('private')";
			} else if(isset($this->conditions['specific_status']['status']) && sanitize_text_field($this->conditions['specific_status']['status']) == 'Pending') {
				$get_customer_reviews .= " and p.post_status in ('pending')";
			} else if(isset($this->conditions['specific_status']['status']) && sanitize_text_field($this->conditions['specific_status']['status']) == 'Protected') {
				$get_customer_reviews .= " and p.post_status in ('publish') and post_password != ''";
			}
		} else {
			$get_customer_reviews .= " and p.post_status in ('publish','draft','future','private','pending')";
		}
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			$get_customer_reviews .= " and c.comment_date >= '" . $this->conditions['specific_period']['from'] . "' and c.comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == 'true') {
			if(isset($this->conditions['specific_authors']['author']) && $this->conditions['specific_authors']['author'] != 0) {
				$get_customer_reviews .= " and c.comment_author_email = {$this->conditions['specific_authors']['author']}";
			}
		}
		$get_total_row_count = $wpdb->get_col($get_customer_reviews);
		$this->totalRowCount = count($get_total_row_count);
		$offset_limit = " order by ID asc limit $this->offset, $this->limit";
		$query_with_offset_limit = $get_customer_reviews . $offset_limit;
		$result = $wpdb->get_col($query_with_offset_limit);

		if(!empty($result)) {
			foreach($result as $reviewId) {
				// Review Information
				$query_for_reviews = $wpdb->prepare("SELECT wp.* FROM $wpdb->posts wp where ID=%d", $reviewId);
				$reviewDetails = $wpdb->get_results($query_for_reviews);
				if (!empty($reviewDetails)) {
					foreach ($reviewDetails as $posts) {
						foreach ($posts as $post_key => $post_value) {
							if ($post_key == 'post_status') {
								if (is_sticky($reviewId)) {
									$headers[] = $post_key;
									$this->data[$reviewId][$post_key] = 'Sticky';
									$post_status = 'Sticky';
								} else {
									$headers[] = $post_key;
									$this->data[$reviewId][$post_key] = $post_value;
									$post_status = $post_value;
								}
							} else {
								$headers[] = $post_key;
								$this->data[$reviewId][$post_key] = $post_value;
							}
							if ($post_key == 'post_password') {
								if ($post_value) {
									$headers[] = $post_key;
									$this->data[$reviewId]['post_status'] = "{" . $post_value . "}";
								} else {
									$headers[] = $post_key;
									$this->data[$reviewId]['post_status'] = $post_status;
								}
							}
							if ($post_key == 'comment_status') {
								if ($post_value == 'closed') {
									$headers[] = $post_key;
									$this->data[$reviewId]['comment_status'] = 0;
								}
								if ($post_value == 'open') {
									$headers[] = $post_key;
									$this->data[$reviewId]['comment_status'] = 1;
								}
							}
						}
					}
				}
				// Review Meta Information
				$query_for_review_meta = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->posts wp JOIN $wpdb->postmeta wpm ON wpm.post_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d", '_edit_lock', '_edit_last', $reviewId);
				$reviewMetaDetails = $wpdb->get_results($query_for_review_meta);

				if(!empty($reviewMetaDetails)) :
					foreach($reviewMetaDetails as $key => $value) :
						if ($value->meta_key == '_thumbnail_id') {
							$attachment_file = null;
							$get_attachment = $wpdb->prepare("select guid from $wpdb->posts where ID = %d AND post_type = %s", $value->meta_value, 'attachment');
							$attachment = $wpdb->get_results($get_attachment);
							$attachment_file = $attachment[0]->guid;
							$this->data[$reviewId][$value->meta_key] = '';
							$value->meta_key = 'featured_image';
							$this->data[$reviewId][$value->meta_key] = $attachment_file;
						} else {
							$this->data[$reviewId][$value->meta_key] = $value->meta_value;
							$headers[] = $value->meta_key;
						}
					endforeach;
				endif;

				// Prepare the headers
				if(!empty($headers)) {
					foreach($headers as $hKey) {
						if(!in_array($hKey, $this->headers)) {
							$this->headers[] = $hKey;
						}
					}
				}
			}
		}
		
		$result = $this->finalDataToExport($this->data);
		if($mode == null)
			$this->proceedExport($result);
		else
			return $result;
	}

	/**
	 * Fetch all Comments
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchComments($mode = null) {
		global $wpdb;
		$this->generateHeaders($this->module, $this->optionalType);
		$get_comments = "select * from $wpdb->comments";
		// Check status
		if($this->conditions['specific_status']['is_check'] == 'true') {
			if($this->conditions['specific_status']['status'] == 'Pending')
				$get_comments .= " where comment_approved = '0'";
			elseif($this->conditions['specific_status']['status'] == 'Approved')
				$get_comments .= " where comment_approved = '1'";
			else
				$get_comments .= " where comment_approved in ('0','1')";
		}
		else
			$get_comments .= " where comment_approved in ('0','1')";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "' and comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == 'true') {
			if(isset($this->conditions['specific_authors']['author'])) {
				$get_comments .= " and comment_author_email = '".$this->conditions['specific_authors']['author']."'"; 
			}
		}
		$get_comments .= " order by comment_ID asc limit $this->offset, $this->limit";
		$comments = $wpdb->get_results( $get_comments );
		$this->totalRowCount = count($comments);
		if(!empty($comments)) {
			foreach($comments as $commentInfo) {
				foreach($commentInfo as $commentKey => $commentVal) {
					// if(!in_array($commentKey, $this->headers)) {
					// 	$this->headers[] = $commentKey;
					// }
					$this->data[$commentInfo->comment_ID][$commentKey] = $commentVal;
				}
			}
		}
		$result = $this->finalDataToExport($this->data);
		if($mode == null)
			$this->proceedExport($result);
		else
			return $result;
	}


	public function array_to_xml( $data, &$xml_data ) {
	    foreach( $data as $key => $value ) {
	        if( is_numeric($key) ){
	            $key = 'item'; //dealing with <0/>..<n/> issues
	        }
	        if( is_array($value) ) {
	            $subnode = $xml_data->addChild($key);
	            $this->array_to_xml($value, $subnode);
	        } else {
	            $xml_data->addChild("$key",htmlspecialchars("$value"));
	        }
	     }
	}

	/**
	 * Export Data
	 *
	 * @param $data     - Fetched data to be export
	 * @return array $responseTojQuery
	 */
	public function proceedExport ($data) {

		$loggerObj = new SmackCSVLogger();

		if(!is_dir(SM_UCI_EXPORT_DIR)) {
			wp_mkdir_p(SM_UCI_EXPORT_DIR);
		}
		chmod(SM_UCI_EXPORT_DIR, 0777);
		//$file = SM_UCI_EXPORT_DIR . $this->fileName . '.' . $this->exportType;
		if($this->checkSplit == 'yes'){
			$i = 1;
			while ( $i != 0) {
				$file = SM_UCI_EXPORT_DIR . $this->fileName .'_'.$i.'.' . $this->exportType;
				if(file_exists($file)){
					$allfiles[$i] = $file;
					$i++;
				}
				else
					break;
			}
			$fileURL = SM_UCI_EXPORT_URL . $this->fileName.'_'.$i.'.' .$this->exportType;
		}
		else{
			$file = SM_UCI_EXPORT_DIR . $this->fileName .'.' . $this->exportType;
			$fileURL = SM_UCI_EXPORT_URL . $this->fileName.'.' .$this->exportType;
		}
		

		$spsize = 100;
		if ($this->offset == 0) :
			if(file_exists($file))
				unlink($file);
		endif;

		$checkRun = "no";
		if($this->checkSplit == 'yes' && ($this->totalRowCount - $this->offset) >= 0){
			$checkRun = 'yes';
		}
		if($this->checkSplit != 'yes'){
			$checkRun = 'yes';
		}

		if($checkRun == 'yes'){
			if($this->exportType == 'xml'){
			$xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
			$this->array_to_xml($data,$xml_data);
			$result = $xml_data->asXML($file);
			}else{
				if($this->exportType == 'json')
					$csvData = json_encode($data);
				else
				  $csvData = $this->unParse($data, $this->headers);
				try {
					
					file_put_contents( $file, $csvData, FILE_APPEND | LOCK_EX );
					//$this->splitCSV($file, $ex, $spsize, $this->exportType);
				} catch (Exception $e) {
					$loggerObj->logW('', $e);
				}
			}
		}
		

		$this->offset = $this->offset + $this->limit;

		
		$filePath = SM_UCI_EXPORT_DIR . $this->fileName . '.' . $this->exportType;

		if(($this->offset) > ($this->totalRowCount) && $this->checkSplit == 'yes'){
			$allfiles[$i] = $file;
			$zipname = SM_UCI_EXPORT_DIR . $this->fileName .'.' . 'zip';
			$zip = new ZipArchive;
			$zip->open($zipname, ZipArchive::CREATE);
			foreach ($allfiles as $allfile) {
				$newname = str_replace(SM_UCI_EXPORT_DIR, '', $allfile);
			    $zip->addFile($allfile, $newname);
			}
			$zip->close();
			$fileURL = SM_UCI_EXPORT_URL . $this->fileName.'.'.'zip';
			foreach ($allfiles as $removefile) {
				unlink($removefile);
			}
		}

		
		$responseTojQuery = array('new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $fileURL, 'exported_path' => $filePath);
		if($this->export_mode == 'normal')
			echo json_encode($responseTojQuery);
		elseif($this->export_mode == 'FTP')
			$this->export_log = $responseTojQuery;
	}



	/**
	 * Fetch ACF field information to be export
	 * @param $recordId - Id of the Post (or) Page (or) Product (or) User
	 */
	public function FetchACFData ($recordId) {

	}

	/**
	 * Get records based on the post types
	 *
	 * @param $module           - Requested module
	 * @param $optionalType     - Exclusion list
	 * @param $conditions       - Conditions
	 *
	 * @return array            - Data based on the module & conditions
	 */
	public function get_records_based_on_post_types ($module, $optionalType, $conditions) {
		global $wpdb, $uci_admin;
		if($module == 'CustomPosts') {
			$module = $optionalType;
		} elseif ($module == 'WooCommerceOrders') {
			$module = 'shop_order';
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
		else {
			$module = $uci_admin->import_post_types($module);
		}

		$get_post_ids = "select DISTINCT ID from $wpdb->posts p join $wpdb->postmeta pm ";

		// if ($module == 'WooCommerceRefunds') {
		// 	$get_post_ids .= " where p.post_type = 'shop_order'";
		// 	$get_post_ids .= " and p.post_status in ('wc-refunded')";
		// }
		// else
		    $get_post_ids .= " where p.post_type = '$module'";

		// Check for specific status
		if($module == 'shop_order'){
			if(isset($conditions['specific_status']['status'])) {
				if(sanitize_text_field($conditions['specific_status']['status']) == 'All') {
					$get_post_ids .= " and p.post_status in ('wc-completed','wc-cancelled','wc-refunded','wc-on-hold','wc-processing','wc-pending')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Completed Orders') {
					$get_post_ids .= " and p.post_status in ('wc-completed')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Cancelled Orders') {
					$get_post_ids .= " and p.post_status in ('wc-cancelled')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'On Hold Orders') {
					$get_post_ids .= " and p.post_status in ('wc-on-hold')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Processing Orders') {
					$get_post_ids .= " and p.post_status in ('wc-processing')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Pending Orders') {
					$get_post_ids .= " and p.post_status in ('wc-pending')";
				} 
			} else {
				$get_post_ids .= " and p.post_status in ('wc-completed','wc-cancelled','wc-on-hold','wc-processing','wc-pending')";
			}
		}elseif ($module == 'shop_coupon') {
			if(isset($conditions['specific_status']['status'])) {
				if(sanitize_text_field($conditions['specific_status']['status']) == 'All') {
					$get_post_ids .= " and p.post_status in ('publish','draft','pending')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Publish') {
					$get_post_ids .= " and p.post_status in ('publish')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Draft') {
					$get_post_ids .= " and p.post_status in ('draft')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Pending') {
					$get_post_ids .= " and p.post_status in ('pending')";
				} 
			} else {
				$get_post_ids .= " and p.post_status in ('publish','draft','pending')";
			}
			
		}elseif ($module == 'shop_order_refund') {
			
		}
		else {
			if(isset($conditions['specific_status']['status'])) {
				if(sanitize_text_field($conditions['specific_status']['status']) == 'All') {
					$get_post_ids .= " and p.post_status in ('publish','draft','future','private','pending')";
				} elseif(sanitize_text_field($conditions['specific_status']['status'] == 'Publish' || sanitize_text_field($conditions['specific_status']['status']) == 'Sticky')) {
					$get_post_ids .= " and p.post_status in ('publish')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Draft') {
					$get_post_ids .= " and p.post_status in ('draft')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Scheduled') {
					$get_post_ids .= " and p.post_status in ('future')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Private') {
					$get_post_ids .= " and p.post_status in ('private')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Pending') {
					$get_post_ids .= " and p.post_status in ('pending')";
				} elseif(sanitize_text_field($conditions['specific_status']['status']) == 'Protected') {
					$get_post_ids .= " and p.post_status in ('publish') and post_password != ''";
				}
			} else {
				$get_post_ids .= " and p.post_status in ('publish','draft','future','private','pending')";
			}
		}
		// Check for specific period
		if(isset($conditions['specific_period']['is_check']) && $conditions['specific_period']['is_check'] == 'true') {
			$get_post_ids .= " and p.post_date >= '" . $conditions['specific_period']['from'] . "' and p.post_date <= '" . $conditions['specific_period']['to'] . "'";
		}
		if($module == 'eshop')
			$get_post_ids .= " and pm.meta_key = '_eshop_product'";
		if($module == 'woocommerce')
			$get_post_ids .= " and pm.meta_key = '_sku'";
		if($module == 'marketpress')
			$get_post_ids .= " and pm.meta_key = 'mp_sku'";
		if($module == 'wpcommerce')
			$get_post_ids .= " and pm.meta_key = '_wpsc_sku'";

		// Check for specific authors
		if(isset($conditions['specific_period']['is_check']) && $conditions['specific_authors']['is_check'] == 'true') {
			if(isset($conditions['specific_authors']['author']) && $conditions['specific_authors']['author'] != 0) {
				$get_post_ids .= " and p.post_author = {$conditions['specific_authors']['author']}";
			}
		}

		$get_total_row_count = $wpdb->get_col($get_post_ids);
		$this->totalRowCount = count($get_total_row_count);
		$offset_limit = " order by ID asc limit $this->offset, $this->limit";
		$query_with_offset_limit = $get_post_ids . $offset_limit;
		$result = $wpdb->get_col($query_with_offset_limit);

		// Get sticky post alone on the specific post status
		if(isset($conditions['specific_period']['is_check']) && $conditions['specific_status']['is_check'] == 'true') {
			if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'Sticky') {
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

	/**
	 * Get post data based on the record id
	 *
	 * @param $id       - Id of the records
	 *
	 * @return array    - Data based on the requested id.
	 */
	public function getPostsDataBasedOnRecordId ($id) {
		global $wpdb;
		$PostData = array();
		$query1 = $wpdb->prepare("SELECT wp.* FROM $wpdb->posts wp where ID=%d", $id);
		$result_query1 = $wpdb->get_results($query1);
		if (!empty($result_query1)) {
			foreach ($result_query1 as $posts) {
				foreach ($posts as $post_key => $post_value) {
					if ($post_key == 'post_status') {
						if (is_sticky($id)) {
							$PostData[$post_key] = 'Sticky';
							$post_status = 'Sticky';
						} else {
							$PostData[$post_key] = $post_value;
							$post_status = $post_value;
						}
					} else {
						$PostData[$post_key] = $post_value;
					}
					if ($post_key == 'post_password') {
						if ($post_value) {
							$PostData['post_status'] = "{" . $post_value . "}";
						} else {
							$PostData['post_status'] = $post_status;
						}
					}
					if ($post_key == 'comment_status') {
						if ($post_value == 'closed') {
							$PostData['comment_status'] = 0;
						}
						if ($post_value == 'open') {
							$PostData['comment_status'] = 1;
						}
					}
					if($post_key == 'post_author'){
						$user_info = get_userdata($post_value);
						$PostData['post_author'] = $user_info->user_login;
					}
				}
			}
		}
		return $PostData;
	}

	public function getAttachment($id)
	{
		global $wpdb;
		$get_attachment = $wpdb->prepare("select guid from $wpdb->posts where ID = %d AND post_type = %s", $id, 'attachment');
		$attachment = $wpdb->get_results($get_attachment);
		$attachment_file = $attachment[0]->guid;
		return $attachment_file;

	}

	public function getRepeater($parent)
	{
		global $wpdb;

		$get_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts where post_parent = %d", $parent), ARRAY_A);

		$i = 0;
		foreach ($get_fields as $key => $value) {
			$array[$i] = $value['post_excerpt'];
			$i++;
		}

		return $array;	
	}

	/**
	 * Function to export the meta information based on Fetch ACF field information to be expo
	 * @param $id   - Id of the requested Post type
	 */
	public function getPostsMetaDataBasedOnRecordId ($id) {
		global $wpdb;
		
		$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->posts wp JOIN $wpdb->postmeta wpm ON wpm.post_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d", '_edit_lock', '_edit_last', $id);

		$get_acf_fields = $wpdb->get_results($wpdb->prepare("SELECT ID, post_excerpt, post_content, post_name, post_parent FROM $wpdb->posts where post_type = %s", 'acf-field'), ARRAY_A);

		$group_unser = array('customer_email', 'product_categories', 'exclude_product_categories');
		
		if(!empty($get_acf_fields)){
			foreach ($get_acf_fields as $key => $value) {
				$allacf[$key] = $value['post_excerpt']; 
				$content = unserialize($value['post_content']);
				$alltype[$value['post_excerpt']] = $content['type'];
				$parent = $value['post_parent'];
				if($content['type'] == 'repeater')
				$checkRep[$value['post_excerpt']] = $this->getRepeater($value['ID']);
			    else
			    $checkRep[$value['post_excerpt']] = "";
			}
		}

		$wptypesfields = get_option('wpcf-fields');

		if(!empty($wptypesfields)){
			$i = 1;
			foreach ($wptypesfields as $key => $value) {
				$typesf[$i] = 'wpcf-'.$key;
				$typeOftypesField[$typesf[$i]] = $value['type']; 
				$i++;
			}
		}

		$result = $wpdb->get_results($query);

		if(!empty($result)) :
			foreach($result as $key => $value) :
				if ($value->meta_key == '_thumbnail_id') {
					$attachment_file = null;
					$get_attachment = $wpdb->prepare("select guid from $wpdb->posts where ID = %d AND post_type = %s", $value->meta_value, 'attachment');
					$attachment = $wpdb->get_results($get_attachment);
					$attachment_file = $attachment[0]->guid;
					$this->data[$id][$value->meta_key] = '';
					$value->meta_key = 'featured_image';
					$this->data[$id][$value->meta_key] = $attachment_file;
				} else {
					
					if(in_array($value->meta_key, $allacf)){
						$getType = $alltype[$value->meta_key];
						
						if ($getType == 'repeater') {
							$count = $value->meta_value;
						    $getRF = $checkRep[$value->meta_key];
						    foreach ($getRF as $rep => $rep1) {
						    	$repType = $alltype[$rep1];
						    	$reval = "";
						    	for($z=0;$z<$count;$z++){
						    		$var = $value->meta_key.'_'.$z.'_'.$rep1;
						    		$qry = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM $wpdb->posts wp JOIN $wpdb->postmeta wpm ON wpm.post_id = wp.ID where meta_key = %s AND ID=%d", $var, $id));
						    		$meta = $qry[0]->meta_value;
						    		if($repType == 'image')
						    			$meta = $this->getAttachment($meta);
						    		if(is_serialized($meta))
						    		{
						    			$unmeta = unserialize($meta);
						    			$meta = "";
						    			foreach ($unmeta as $unmeta1) {
						    				if($repType == 'image' || $repType == 'gallery')
						    					$meta .= $this->getAttachment($unmeta1).",";
						    				else
						    					$meta .= $unmeta1.",";
						    			}
						    			$meta = substr($meta, 0, -1);
						    		}
						    		if($meta != "")
						    		$reval .= $meta."|";
						    	}
						    	$this->data[$id][$rep1] = substr($reval, 0, -1);
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
									$acfdata .= $attach.'|';
								}
								else
									$acfdata .= $value1.'|';
							}
							$this->data[$id][ $value->meta_key ] = substr($acfdata, 0, -1);
						}
						elseif($getType == 'gallery' || $getType == 'image'){
							$attach1 = $this->getAttachment($value->meta_value);
							$this->data[$id][ $value->meta_key ] = $attach1;
						}
						else{
							$this->data[$id][ $value->meta_key ] = $value->meta_value;
						}
					}
					elseif (in_array($value->meta_key, $typesf)) {
						$typeoftype = $typeOftypesField[$value->meta_key];
						if(is_serialized($value->meta_value)){
							    $typefileds = unserialize($value->meta_value);
								$typedata = "";
								foreach ($typefileds as $key2 => $value2) {
									if(is_array($value2)){
										foreach ($value2 as $key3 => $value3) {
											$typedata .= $value3.',';
										}
									}
									else
									 $typedata .= $value2.',';
								}
								$this->data[$id][ $value->meta_key ] = substr($typedata, 0, -1);
						}
						elseif ($typeoftype == 'date') {
							$this->data[$id][ $value->meta_key ] = date('y-m-d', $value->meta_value);
						}
						else{
							$this->data[$id][ $value->meta_key ] = $value->meta_value;
						}

					}
					elseif(in_array($value->meta_key, $group_unser) && is_serialized($value->meta_value)) {
							$unser = unserialize($value->meta_value);
							$data = "";
							foreach ($unser as $key4 => $value4) 
							 $data .= $value4.',';
							$this->data[$id][ $value->meta_key ] = substr($data, 0, -1);
					}
					else
					 $this->data[$id][ $value->meta_key ] = $value->meta_value;
				}
			endforeach;
		endif;
		
	}

	public function getWooComOrderData($id, $type, $optional)
	{
		global $wpdb;
		$orderid = $this->data[$id]['ID'];
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
				if($value->order_item_type != 'fee' && $value->order_item_type != 'shipping'){
					$order_itemname .= $value->order_item_name.'|';
					$order_itemtype .= $value->order_item_type.'|';
				}
				if($value->order_item_type == 'fee'){
					$feename .= $value->order_item_name.'|';
					$feetype .= $value->order_item_type.'|';
				}
				if($value->order_item_type == 'shipping'){
					$shipname .= $value->order_item_name.'|';
				}
				$query2 = $wpdb->prepare("SELECT * FROM $table2 where order_item_id = %d", $orderitem);
				$result2 = $wpdb->get_results($query2);

				foreach ($result2 as $key2 => $value2) {
					if($value->order_item_type != 'fee' && $value->order_item_type != 'shipping'){
						if($value2->meta_key == '_product_id'){
							$product_id .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_variation_id'){
							$var_id .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_subtotal'){
							$line_subtotal .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_subtotal_tax'){
							$line_subtotal_tax .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_total'){
							$line_total .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_tax'){
							$line_total_tax .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_tax_data'){
							$line_tax_data .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_qty'){
							$qty .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_tax_class'){
							$tx_cls .= $value2->meta_value.'|';
						}
					}
					if($value->order_item_type == 'fee'){
						if($value2->meta_key == '_line_subtotal'){
							$fee_subtotal .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_subtotal_tax'){
							$fee_subtotal_tax .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_total'){
							$fee_total .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_tax'){
							$fee_total_tax .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_line_tax_data'){
							$fee_tax_data .= $value2->meta_value.'|';
						}
						if($value2->meta_key == '_tax_class'){
							$fee_tx_cls .= $value2->meta_value.'|';
						}
					}
					if($value->order_item_type == 'shipping'){
						if($value2->meta_key == 'method_id'){
							$method_id .= $value2->meta_value.'|';
						}
						if($value2->meta_key == 'cost'){
							$cost .= $value2->meta_value.'|';
						}
						if($value2->meta_key == 'taxes'){
							$taxes .= $value2->meta_value.'|';
						}
					}
				}
			}
		}
		//itemdata
		$this->data[$id]['item_name'] = substr($order_itemname, 0, -1);
		$this->data[$id]['item_type'] = substr($order_itemtype, 0, -1);
		$this->data[$id]['item_product_id'] = substr($product_id, 0, -1);
		$this->data[$id]['item_variation_id'] = substr($var_id, 0, -1);
		$this->data[$id]['item_line_subtotal'] = substr($line_subtotal, 0, -1);
		$this->data[$id]['item_line_subtotal_tax'] = substr($line_subtotal_tax, 0, -1);
		$this->data[$id]['item_line_total'] = substr($line_total, 0, -1);
		$this->data[$id]['item_line_tax'] = substr($line_total_tax, 0, -1);
		$this->data[$id]['item_line_tax_data'] = substr($line_tax_data, 0, -1);	
		$this->data[$id]['item_qty'] = substr($qty, 0, -1);	
		$this->data[$id]['item_tax_class'] = substr($tx_cls, 0, -1);
		//fee data
		$this->data[$id]['fee_name'] = substr($feename, 0, -1);
		$this->data[$id]['fee_type'] = substr($feetype, 0, -1);
		$this->data[$id]['fee_line_subtotal'] = substr($fee_subtotal, 0, -1);
		$this->data[$id]['fee_line_subtotal_tax'] = substr($fee_subtotal_tax, 0, -1);
		$this->data[$id]['fee_line_total'] = substr($fee_total, 0, -1);
		$this->data[$id]['fee_line_tax'] = substr($fee_total_tax, 0, -1);
		$this->data[$id]['fee_line_tax_data'] = substr($fee_tax_data, 0, -1);	
		$this->data[$id]['fee_tax_class'] = substr($fee_tx_cls, 0, -1);
		// shipment data
		$this->data[$id]['shipment_name'] = substr($shipname, 0, -1);
		$this->data[$id]['shipment_method_id'] = substr($method_id, 0, -1);
		$this->data[$id]['shipment_cost'] = substr($cost, 0, -1);	
		$this->data[$id]['shipment_taxes'] = substr($taxes, 0, -1);

	}

	public function getEcomData($id, $type, $optional)
	{
		
		$meta = unserialize($this->data[$id]['_wpsc_product_metadata']);
		foreach ($meta as $key => $value) {
			if(is_array($value))
			{
				foreach ($value as $key1 => $value1) {
					$this->data[$id][$key1] = $value1;
					if($key1 == 'quantity')
						$this->data[$id][$key1] = $value1[0];
					if($key1 == 'table_price')
						$this->data[$id][$key1] = $value1[0];	
				}
			}
			else{
				if($key == 'dimension_unit'){
					$this->data[$id]['height_unit'] = $value;
					$this->data[$id]['length_unit'] = $value;
					$this->data[$id]['width_unit'] = $value;
				}
				if($key == 'price'){
					$this->data[$id]['sale_price'] = $value;
				}
				$this->data[$id][$key] = $value;	

				
			}	
		}
		$this->data[$id]['PRODUCTSKU'] = isset($this->data[$id]['_wpsc_sku']) ? $this->data[$id]['_wpsc_sku'] : "";
		$this->data[$id]['sale_price'] = isset($this->data[$id]['_wpsc_price']) ? $this->data[$id]['_wpsc_price'] : "";
		$this->data[$id]['taxable_amount'] = isset($this->data[$id]['wpec_taxes_taxable_amount']) ? $this->data[$id]['wpec_taxes_taxable_amount'] : "";
		$this->data[$id]['is_taxable'] = isset($this->data[$id]['wpec_taxes_taxable']) ? $this->data[$id]['wpec_taxes_taxable'] : 0;
		$this->data[$id]['enable_comments'] = isset($this->data[$id]['comment_status']) ? $this->data[$id]['comment_status'] : 0;


		$img_id = unserialize($this->data[$id]['_wpsc_product_gallery']);
		$img_link = $this->getAttachment($img_id[0]);
		$this->data[$id]['image_gallery'] = $img_link;

		$currency = isset($this->data[$id]['_wpsc_currency']) ? unserialize($this->data[$id]['_wpsc_currency']) : '';

		foreach ($currency as $country => $amount) {
			$money = $country.'|'.$amount;
		}

		$this->data[$id]['alternative_currencies_and_price'] = $money;

	}


	public function getProductData($id, $type, $optional)
	{
		global $wpdb;
		$productid = $this->data[$id]['post_parent'];
		$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->postmeta where post_id = %d", $productid);
		$result = $wpdb->get_results($query);
		
		$attname = $attvalue = $attvisible = $attvar = $attpos = "";
		if(!empty($result)){
			foreach ($result as $key => $value) {
				if($value->meta_key == '_product_attributes'){
					$attArray = unserialize($value->meta_value);
					
					foreach ($attArray as $key1 => $value1) {
						$attname .= $value1['name'].'|';
						$attvalue .= $value1['value'].',';
						$attvisible .= $value1['is_visible'].'|';
						$attvar .= $value1['is_variation'].'|';
						$attpos .= $value1['position'].'|';
					}
				}
				if($value->meta_key == '_sku'){
					$this->data[$id]['PARENTSKU'] = $value->meta_value; 
				}
				if($value->meta_key == '_sale_price_dates_from'){
					$this->data[$id]['sale_price_dates_from'] = $value->meta_value; 
				}
				if($value->meta_key == '_sale_price_dates_to'){
					$this->data[$id]['sale_price_dates_to'] = $value->meta_value; 
				}
				if($value->meta_key == '_downloadable'){
					$this->data[$id]['downloadable_files'] = $value->meta_value; 
				}
				if($value->meta_key == '_purchase_note'){
					$this->data[$id]['purchase_note'] = $value->meta_value; 
				}
				if($value->meta_key == '_thumbnail_id'){
					$this->data[$id]['thumbnail_id'] = $value->meta_value; 
				}
				if($value->meta_key == '_stock'){
					$this->data[$id]['stock_qty'] = $value->meta_value; 
				}

			}
		}
		$this->data[$id]['product_attribute_name'] = substr($attname, 0, -1);
		$this->data[$id]['product_attribute_value'] = substr($attvalue, 0, -1);
		$this->data[$id]['product_attribute_visible'] = substr($attvisible, 0, -1);
		$this->data[$id]['product_attribute_variation'] = substr($attvar, 0, -1);
		$this->data[$id]['product_attribute_position'] = substr($attpos, 0, -1);
		
		$cus = explode('|', $this->data[$id]['product_attribute_name']);
		$cusAttr = "";
		foreach ($cus as $cus1) {
			$name = 'attribute_'.$cus1;
			$cusAttr .= $cus1.'|'.$this->data[$id][$name].','; 
		}
		$this->data[$id]['custom_attributes'] = substr($cusAttr, 0, -1);
		
	}

	public function getWooComCustomerUser($id, $type, $optionalType)
	{
		global $wpdb;
		$parent = $this->data[$id]['post_parent'];
		$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->postmeta where post_id = %d", $parent);
		$result = $wpdb->get_results($query);
		if(!empty($result)){
			foreach ($result as $key => $value) {
				if($value->meta_key == '_customer_user'){
					$cus_user = $value->meta_value;
				}
			}
		}
		$this->data[$id]['customer_user'] = $cus_user;
	}

	/**
	 * Function used to fetch the Terms & Taxonomies for the specific posts
	 *
	 * @param $id
	 * @param $type
	 * @param $optionalType
	 */
	public function getTermsAndTaxonomies ($id, $type, $optionalType) {
		$TermsData = array();
		if($type == 'WooCommerce' || $type == 'MarketPress') {
			$product = wc_get_product($id);
			$pro_type = $product->get_type();
			switch ($pro_type) {
				case 'simple':
					$product_type = 1;
				break;
				case 'grouped':
					$product_type = 2;
				break;
				case 'external':
					$product_type = 3;
				break;
				case 'variable':
					$product_type = 4;
				break;
				case 'subscription':
					$product_type = 5;
				break;
				case 'variable-subscription':
					$product_type = 6;
				break;
				default:
					$product_type = 1;
				break;
			}
			$this->data[$id]['product_type'] = $product_type;
			$type = 'product';
			$postTags = $postCategory = '';
			$taxonomies = get_object_taxonomies($type);
			$get_tags = get_the_terms( $id, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name . ',';
				}
			}
			$postTags = substr($postTags, 0, -1);
			$this->data[$id]['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				if($taxonomy == 'product_cat' || $taxonomy == 'product_category'){
					$get_categories = get_the_terms( $id, $taxonomy );
					if($get_categories){
						foreach($get_categories as $category){
							$postCategory .= $category->name . '|';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}
			}
		} else if($type == 'WPeCommerce') {
			$type = 'wpsc-product';
			$postTags = $postCategory = '';
			$taxonomies = get_object_taxonomies($type);
			$get_tags = get_the_terms( $id, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name.',';
				}
			}
			$postTags = substr($postTags,0,-1);
			$this->data[$id]['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				if($taxonomy == 'wpsc_product_category'){
					$get_categories = wp_get_post_terms( $id, $taxonomy );
					if($get_categories){
						foreach($get_categories as $category){
							$postCategory .= $category->name.'|';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}
			}
		} else {
			global $wpdb;
			$postTags = $postCategory = '';
			$taxonomyId = $wpdb->get_col($wpdb->prepare("select term_taxonomy_id from $wpdb->term_relationships where object_id = %d", $id));
			if(!empty($taxonomyId)) {
				foreach($taxonomyId as $taxonomy) {
					$taxonomyType = $wpdb->get_col($wpdb->prepare("select taxonomy from $wpdb->term_taxonomy where term_taxonomy_id = %d", $taxonomy));
					if(!empty($taxonomyType)) {
						foreach($taxonomyType as $termName) {
							if($termName == 'category')
								$termName = 'post_category';
							if(in_array($termName, $this->headers)) {
								if($termName != 'post_tag') {
									$taxonomyData = $wpdb->get_col($wpdb->prepare("select name from $wpdb->terms where term_id = %d",$taxonomy));
									if(!empty($taxonomyData)) {
										if(isset($TermsData[$termName]))
											$this->data[$id][$termName] = $TermsData[$termName] . ',' . $taxonomyData[0];
										else
											$get_exist_data = $this->data[$id][$termName];
										if( $get_exist_data == '' ){
											$this->data[$id][$termName] = $taxonomyData[0];
										}
										else {
											$this->data[$id][$termName] = $get_exist_data . '|' . $taxonomyData[0];
										}
									}
								} else {
									if(!isset($TermsData['post_tag'])) {
										$get_tags = wp_get_post_tags($id, array('fields' => 'names'));
										foreach ($get_tags as $tags) {
											$postTags .= $tags . ',';
										}
										$postTags = substr($postTags, 0, -1);
										if( $this->data[$id][$termName] == '' ) {
											$this->data[$id][$termName] = $postTags;
										}
									}
								}
								if(!isset($TermsData['category'])){
									$get_categories = wp_get_post_categories($id, array('fields' => 'names'));
									foreach ($get_categories as $category) {
										$postCategory .= $category . '|';
									}
									$postCategory = substr($postCategory, 0, -1);
									$this->data[$id]['category'] = $postCategory;
								}

							}
							else{
								$this->data[$id][$termName] = '';
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Get types fields
	 *
	 * @return array    - Types fields
	 */
	public function getTypesFields() {
		$getWPTypesFields = get_option('wpcf-fields');
		$typesFields = array();
		if(!empty($getWPTypesFields) && is_array($getWPTypesFields)) {
			foreach($getWPTypesFields as $fKey){
				$typesFields[$fKey['meta_key']] = $fKey['name'];
			}
		}
		return $typesFields;
	}

	/**
	 * Final data to be export
	 *
	 * @param $data     - Data to be export based on the requested information
	 *
	 * @return array    - Final data to be export
	 */
	public function finalDataToExport ($data) {
		$result = array();
		foreach ($this->headers as $key => $value) {
			if($value == "")
				unset($this->headers[$key]);
		}
		foreach ($this->headers as $hKey) {
			foreach ( $data as $recordId => $rowValue ) {
				foreach($rowValue as $key => $value){
					if(array_key_exists($hKey, $rowValue)):
						$result[$recordId][$hKey] = $rowValue[$hKey];
					else:
						if(preg_match('/_aioseop_/', $key)):
							$key = preg_replace('/_aioseop_/', '', $key);
						endif;
						if(preg_match('/_yoast_wpseo_/', $key)):
							$key = preg_replace('/_yoast_wpseo_/', '', $key);
							if($key == 'focuskw') {
								$key = 'focus_keyword';
							} elseif($key == 'bctitle') {
								$key = 'bread-crumbs-title';
							} elseif($key == 'metadesc') {
								$key = 'meta_desc';
							}
						endif;
						if(preg_match('/wpcf-/', $key)):
							$key = preg_replace('/wpcf-/', '', $key);
						endif;
						if(preg_match('/_wpsc_/', $key)):
							$key = preg_replace('/_wpsc_/', '', $key);
						endif;
						if(preg_match('/_/', $key)):
							$key = preg_replace('/^_/', '', $key);
						endif;
						if($rowValue['post_type'] == 'shop_order_refund'){
							if ($key == 'ID') 
								$key = 'REFUNDID';
						}
						elseif($rowValue['post_type'] == 'shop_order'){
							if ($key == 'ID') 
								$key = 'ORDERID';
							if($key == 'post_status')
								$key = 'order_status';
							if($key == 'post_excerpt')
								$key = 'customer_note';
							if($key == 'post_date')
								$key = 'order_date';
						}
						if($rowValue['post_type'] == 'shop_coupon'){
							if ($key == 'ID') 
								$key = 'COUPONID';
							if($key == 'post_status')
								$key = 'coupon_status';
							if($key == 'post_excerpt')
								$key = 'description';
							if($key == 'post_date')
								$key = 'coupon_date';
							if($key == 'post_title')
								$key = 'coupon_code';
						}
						if($rowValue['post_type'] == 'product_variation'){
							if ($key == 'ID') 
								$key = 'VARIATIONID';
							if($key == 'post_parent')
								$key = 'PRODUCTID';
							if($key == 'sku')
								$key = 'VARIATIONSKU';
						}
						
						$rowValue[$key] = $value;
						if(array_key_exists($hKey, $rowValue)):
							$result[$recordId][$hKey] = $rowValue[$hKey];
						else:
							$result[$recordId][$hKey] = '';
						endif;
					endif;
				}
			}
		}
		
		return $result;
	}

	/**
	 * Create CSV data from array
	 * @param array $data       2D array with data
	 * @param array $fields     field names
	 * @param bool $append      if true, field names will not be output
	 * @param bool $is_php      if a php die() call should be put on the first
	 *                          line of the file, this is later ignored when read.
	 * @param null $delimiter   field delimiter to use
	 *
	 * @return string           CSV data (text string)
	 */
	public function unParse ( $data = array(), $fields = array(), $append = false , $is_php = false, $delimiter = null) {
		if ( !is_array($data) || empty($data) ) $data = &$this->data;
		if ( !is_array($fields) || empty($fields) ) $fields = &$this->titles;
		if ( $delimiter === null ) $delimiter = $this->delimiter;

		$string = ( $is_php ) ? "<?php header('Status: 403'); die(' '); ?>".$this->linefeed : '' ;
		$entry = array();

		// create heading
		if ($this->offset == 0 || $this->checkSplit == 'yes') :
			if ( $this->heading && !$append && !empty($fields) ) {
				foreach( $fields as $key => $value ) {
					$entry[] = $this->_enclose_value($value);
				}
				$string .= implode($delimiter, $entry).$this->linefeed;
				$entry = array();
			}
		endif;

		// create data
		foreach( $data as $key => $row ) {
			foreach( $row as $field => $value ) {
				$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($delimiter, $entry).$this->linefeed;
			$entry = array();
		}
		return $string;
	}

	/**
	 * Enclose values if needed
	 *  - only used by unParse()
	 * @param null $value
	 *
	 * @return mixed|null|string
	 */
	public function _enclose_value ($value = null) {
		if ( $value !== null && $value != '' ) {
			$delimiter = preg_quote($this->delimiter, '/');
			$enclosure = preg_quote($this->enclosure, '/');
			if($value[0]=='=') $value="'".$value; # Fix for the Comma separated vulnerabilities.
			if ( preg_match("/".$delimiter."|".$enclosure."|\n|\r/i", $value) || ($value{0} == ' ' || substr($value, -1) == ' ') ) {
				$value = str_replace($this->enclosure, $this->enclosure.$this->enclosure, $value);
				$value = $this->enclosure.$value.$this->enclosure;
			}
			else
			$value = $this->enclosure.$value.$this->enclosure;
		}
		return $value;
	}

	/**
	 * Apply exclusion before export
	 *
	 * @param $headers  - Apply exclusion headers
	 *
	 * @return array    - Available headers after applying the exclusions
	 */
	public function applyEventExclusion ($headers) {
		$header_exclusion = array();
		foreach ($headers as $hVal) {
			if(array_key_exists($hVal, $this->eventExclusions['exclusion_headers'])) :
				$header_exclusion[] = $hVal;
			endif;
		}
		return $header_exclusion;
	}
}

return new SmackUCIExporter();