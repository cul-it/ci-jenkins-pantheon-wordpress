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
 * Class CustomerReviewExport
 * @package Smackcoders\WCSV
 */

class CustomerReviewExport {

	protected static $instance = null,$mapping_instance,$export_handler,$export_instance;
	public $totalRowCount;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			CustomerReviewExport::$export_instance = ExportExtension::getInstance();
		}
		return self::$instance;
	}

	/**
	 * CustomerReviewExport constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	public function FetchCustomerReviews($module,$mode = null,$optionalType,$conditions,$offset,$limit) {
		global $wpdb;
		$headers = array();
		CustomerReviewExport::$export_instance->generateHeaders($module, $optionalType);
		$get_customer_reviews = "select DISTINCT ID from {$wpdb->prefix}posts";
		$get_customer_reviews .= " where post_type = '$optionalType'";

		/**
		 * Check for specific status
		 */

		if($conditions['specific_status']['status'] == 'true') {
			if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'All') {
				$get_customer_reviews .= " and post_status in ('publish','draft','future','private','pending')";
			} else if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'Publish' || $conditions['specific_status']['status'] == 'Sticky') {
				$get_customer_reviews .= " and post_status in ('publish')";
			} else if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'Draft') {
				$get_customer_reviews .= " and post_status in ('draft')";
			} else if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'Scheduled') {
				$get_customer_reviews .= " and post_status in ('future')";
			} else if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'Private') {
				$get_customer_reviews .= " and post_status in ('private')";
			} else if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'Pending') {
				$get_customer_reviews .= " and post_status in ('pending')";
			} else if(isset($conditions['specific_status']['status']) && $conditions['specific_status']['status'] == 'Protected') {
				$get_customer_reviews .= " and post_status in ('publish') and post_password != ''";
			}
		} else {
			$get_customer_reviews .= " and post_status in ('publish','draft','future','private','pending')";
		}

		/**
		 * Check for specific authors
		 */

		if($conditions['specific_authors']['is_check'] == 'true') {
			if(isset($conditions['specific_authors']['author']) && $conditions['specific_authors']['author'] != 0) {
				$get_customer_reviews .= " and c.comment_author_email = {$conditions['specific_authors']['author']}";
			}
		}
		$get_total_row_count = $wpdb->get_col($get_customer_reviews);
		CustomerReviewExport::$export_instance->totalRowCount = count($get_total_row_count);
		$offset_limit = " order by ID asc limit $offset, $limit";
		$query_with_offset_limit = $get_customer_reviews . $offset_limit;
		$result = $wpdb->get_col($query_with_offset_limit);

		if(!empty($result)) {
			foreach($result as $reviewId) {

				/**
				 * Review Core Information
				 */

				$query_for_reviews = $wpdb->prepare("SELECT wp.* FROM {$wpdb->prefix}posts wp where ID=%d", $reviewId);
				$reviewDetails = $wpdb->get_results($query_for_reviews);
				if (!empty($reviewDetails)) {
					foreach ($reviewDetails as $posts) {
						foreach ($posts as $post_key => $post_value) {
							$post_key = CustomerReviewExport::$export_instance->change_fieldname_depends_on_post_type('wpcr3_review', $post_key);
							if ($post_key == 'post_status') {
								if (is_sticky($reviewId)) {
									CustomerReviewExport::$export_instance->data[$reviewId][$post_key] = 'Sticky';
									$post_status = 'Sticky';
								} else {
									CustomerReviewExport::$export_instance->data[$reviewId][$post_key] = $post_value;
									$post_status = $post_value;
								}
							} else {
								CustomerReviewExport::$export_instance->data[$reviewId][$post_key] = $post_value;
							}
							if ($post_key == 'post_password') {
								if ($post_value) {
									CustomerReviewExport::$export_instance->data[$reviewId]['post_status'] = "{" . $post_value . "}";
								} else {
									CustomerReviewExport::$export_instance->data[$reviewId]['post_status'] = $post_status;
								}
							}
						}
					}
				}

				/**
				 * Review Meta Information
				 */

				$query_for_review_meta = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->prefix}posts wp JOIN {$wpdb->prefix}postmeta wpm ON wpm.post_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d", '_edit_lock', '_edit_last', $reviewId);
				$reviewMetaDetails = $wpdb->get_results($query_for_review_meta);

				if(!empty($reviewMetaDetails)) :
					foreach($reviewMetaDetails as $key => $value) :
						if ($value->meta_key == '_thumbnail_id') {
							$attachment_file = null;
							$get_attachment = $wpdb->prepare("select guid from {$wpdb->prefix}posts where ID = %d AND post_type = %s", $value->meta_value, 'attachment');
							$attachment = $wpdb->get_results($get_attachment);
							$attachment_file = $attachment[0]->guid;
							CustomerReviewExport::$export_instance->data[$reviewId][$value->meta_key] = '';
							$value->meta_key = 'featured_image';
							CustomerReviewExport::$export_instance->data[$reviewId][$value->meta_key] = $attachment_file;
						} else {
							CustomerReviewExport::$export_instance->data[$reviewId][$value->meta_key] = $value->meta_value;
						}
				endforeach;
				endif;

				/**
				 * Prepare the headers
				 */

				if(!empty($headers)) {
					foreach($headers as $hKey) {
						if(!in_array($hKey, CustomerReviewExport::$export_instance->headers)) {
							CustomerReviewExport::$export_instance->headers[] = $hKey;

						}
					}
				}
			}
		}
		$result = CustomerReviewExport::$export_instance->finalDataToExport(CustomerReviewExport::$export_instance->data);
		if($mode == null)
			CustomerReviewExport::$export_instance->proceedExport($result);
		else
			return $result;
	}
}
