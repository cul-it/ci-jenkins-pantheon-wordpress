<?php

/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

namespace Smackcoders\WCSV;

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

class EventsManagerImport
{
	private static $events_instance = null;

	public static function getInstance()
	{

		if (EventsManagerImport::$events_instance == null) {
			EventsManagerImport::$events_instance = new EventsManagerImport;
			return EventsManagerImport::$events_instance;
		}
		return EventsManagerImport::$events_instance;
	}

	public function set_events_values($header_array, $value_array, $map, $post_id, $type, $mode, $term_map)
	{
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map, $header_array, $value_array);

		$this->events_manager_import($post_values, $post_id, $mode, $type, $header_array ,$value_array, $term_map);
	}

	public function events_manager_import($data_array, $pID, $mode, $type, $header_array, $value_array, $term_map)
	{
		global $wpdb;
		$taxonomy_instance = new TermsandTaxonomiesImport();
		$post_content = isset($data_array['post_content']) ? $data_array['post_content'] : '';

		if ($type == 'Events' || $type == 'Recurring Events') {	
			$event_name = isset($data_array['post_title']) ? $data_array['post_title'] : '';
			$event_slug = sanitize_title($event_name);
			// Convert start date from any format
			$event_start_date = current_time('Y-m-d');
			if (!empty($data_array['event_start_date'])) {
				$var_start_date = trim($data_array['event_start_date']);
				$var_start = str_replace('/', '-', "$var_start_date");
				if (strtotime($var_start)) {
					$convertedDate = date('Y-m-d', strtotime($var_start));
					$event_start_date = $convertedDate;
				}
			}
			// Convert end date from any format
			$event_end_date = current_time('Y-m-d');

			if (!empty($data_array['event_end_date'])) {
				$var_end_date = trim($data_array['event_end_date']);
				$var_end = str_replace('/', '-', ".$var_end_date.");
				if (strtotime($var_end)) {
					$converted_date = date('Y-m-d', strtotime($var_end));
					$event_end_date = $converted_date;
				}
			}

			$event_owner = isset($data_array['post_author']) ? $data_array['post_author'] : '';
			$eventday = isset($data_array['event_all_day']) ? $data_array['event_all_day'] : '';
			$event_start_time = isset($data_array['event_start_time']) ? $data_array['event_start_time'] : '';
			$event_end_time = isset($data_array['event_end_time']) ? $data_array['event_end_time'] : '';
			if ($event_start_time != '') {
				$time_start_ts_info =  $event_start_date . ' ' . $event_start_time;
			} else {
				$time_start_ts_info = $event_start_date;
			}
			$time_start_ts_info = strtotime($time_start_ts_info);
			if ($event_end_time != '') {
				$time_end_ts_info =  $event_end_date . ' ' . $event_end_time;
			} else {
				$time_end_ts_info = $event_end_date;
			}
			$time_end_ts_info = strtotime($time_end_ts_info);

			$event_rsvp = isset($data_array['_event_rsvp']) ? $data_array['_event_rsvp'] : 1;
			// Convert booking date from any format
			$event_rsvp_date = current_time('Y-m-d');;

			$event_rsvp_time = isset($data_array['event_rsvp_time']) ? date('h:i:s', strtotime($data_array['event_rsvp_time'])) : '00:00:00';
			$recurrence = !isset($data_array['event_recurrence']) && !empty($data_array['event_recurrence']) && $data_array['event_recurrence'] != '' ? $data_array['event_recurrence'] : 0;
			$event_rsvp_spaces = isset($data_array['event_rsvp_spaces']) ? $data_array['event_rsvp_spaces'] : '00:00:00';
			$event_spaces = isset($data_array['event_spaces']) ? $data_array['event_spaces'] : '';
			$locname = isset($data_array['location_name']) ? $data_array['location_name'] : '';
			$locslug = strtolower($locname);
			$locadd = isset($data_array['location_address']) ? $data_array['location_address'] : '';
			$loctown = isset($data_array['location_town']) ? $data_array['location_town'] : '';
			$locstate = isset($data_array['location_state']) ? $data_array['location_state'] : '';
			$loccode = isset($data_array['location_postcode']) ? $data_array['location_postcode'] : '';
			$locregion = isset($data_array['location_region']) ? $data_array['location_region'] : '';
			$loccountry = isset($data_array['location_country']) ? $data_array['location_country'] : '';
			$loc_owner = isset($data_array['post_author']) ? $data_array['post_author'] : '';

			//Tickets Import - By Anto
			$multiple_ticket_array = $new_tickets = array();
			$multiple_ticket_array['ticket_name'] = isset($data_array['ticket_name']) ? $data_array['ticket_name'] : '';
			$multiple_ticket_array['ticket_description'] = isset($data_array['ticket_description']) ? $data_array['ticket_description'] : '';
			$multiple_ticket_array['ticket_price'] = isset($data_array['ticket_price']) ? $data_array['ticket_price'] : '';
			$multiple_ticket_array['ticket_start_date'] = isset($data_array['ticket_start_date']) ? $data_array['ticket_start_date'] : '';
			$multiple_ticket_array['ticket_start_time'] = isset($data_array['ticket_start_time']) ? $data_array['ticket_start_time'] : '';
			$multiple_ticket_array['ticket_end_date'] = isset($data_array['ticket_end_date']) ? $data_array['ticket_end_date'] : '';
			$multiple_ticket_array['ticket_end_time'] = isset($data_array['ticket_end_time']) ? $data_array['ticket_end_time'] : '';
			$multiple_ticket_array['ticket_min'] = isset($data_array['ticket_min']) ? $data_array['ticket_min'] : '';
			$multiple_ticket_array['ticket_max'] = isset($data_array['ticket_max']) ? $data_array['ticket_max'] : '';
			$multiple_ticket_array['ticket_spaces'] = isset($data_array['ticket_spaces']) ? $data_array['ticket_spaces'] : '';
			$multiple_ticket_array['ticket_members'] = isset($data_array['ticket_members']) ? $data_array['ticket_members'] : '';
			$multiple_ticket_array['ticket_members_roles'] = isset($data_array['ticket_members_roles']) ? $data_array['ticket_members_roles'] : '';
			$multiple_ticket_array['ticket_guests'] = isset($data_array['ticket_guests']) ? $data_array['ticket_guests'] : '';
			$multiple_ticket_array['ticket_required'] = isset($data_array['ticket_required']) ? $data_array['ticket_required'] : '';
			//Explode the tickets
			foreach ($multiple_ticket_array as $mult_key => $mult_val) {
				/*if (strpos($mult_val, '|') !== false) {
					$get_tickets_list = explode('|', $mult_val);
				} */
				if (strpos($mult_val, ',') !== false) {
					$get_tickets_list = explode(',', $mult_val);
				} 
				elseif (strpos($mult_val, ', ') !== false) {
					$get_tickets_list = explode(', ', $mult_val);
				} 
				$ticket_count = count($get_tickets_list);
				$new_tickets[$mult_key] = $get_tickets_list;
			}
			$date_conversion_array = array('ticket_start_date', 'ticket_start_time', 'ticket_end_date', 'ticket_end_time');
			$final_ticket_arr = array();
			if ($ticket_count > 1) {
				for ($i = 0; $i < $ticket_count; $i++) {
					foreach ($new_tickets as $tkey => $tval) {
						//Processing dates
						if (in_array($tkey, $date_conversion_array)) {
							//Date Conversion for ticket start date
							if ($tkey == 'ticket_start_date') {
								$tickstart = current_time('Y-m-d H:i:s');
								if (!empty($new_tickets['ticket_start_time'])) {
									if (strpos($new_tickets['ticket_start_date'][$i], '/') !== false) {
								    //if(strpos('/', $new_tickets['ticket_start_date'][$i])){
										$var_start_date = trim($new_tickets['ticket_start_date'][$i]);
										$var_start = str_replace('/', '-', "$var_start_date");
				                           if (strtotime($var_start)) {
					                          $convertedDate = date('Y-m-d', strtotime($var_start));
					                          $event_start_date = $convertedDate;
											}
											$ticket_start_time_info =$event_start_date . ' ' . $new_tickets['ticket_start_time'][$i];
									}
									else{
										$ticket_start_time_info = $new_tickets['ticket_start_date'][$i] . ' ' . $new_tickets['ticket_start_time'][$i];
									}
									
								} else {
									$ticket_start_time_info = $new_tickets['ticket_start_date'][$i];
								}
								if (strtotime($ticket_start_time_info)) {
									$convertedDate = date('Y-m-d H:i:s', strtotime($ticket_start_time_info));
									$final_ticket_arr[$i]['ticket_start'] = $convertedDate;
								}
							}
							//Date Conversion for ticket end date
							if ($tkey == 'ticket_end_date') {
								$tickend = current_time('Y-m-d H:i:s');
								if (!empty($new_tickets['ticket_end_time'])) {
									if (strpos($new_tickets['ticket_end_date'][$i], '/') !== false) {
									//if(strpos('/', $new_tickets['ticket_end_date'][$i])){
										$var_end_date = trim($new_tickets['ticket_end_date'][$i]);
			                        	$var_end = str_replace('/', '-', "$var_end_date");
				                           if (strtotime($var_end)) {
					                          $convertedDate = date('Y-m-d', strtotime($var_end));
					                          $event_end_date = $convertedDate;
											}
											$ticket_start_time_info =$event_start_date . ' ' . $new_tickets['ticket_end_time'][$i];
									}
									else{
										$ticket_end_time_info = $new_tickets['ticket_end_date'][$i] . ' ' . $new_tickets['ticket_end_time'][$i];
									}
								} else {
									$ticket_end_time_info = $new_tickets['ticket_end_date'][$i];
								}
								if (strtotime($ticket_end_time_info)) {
									$convertedDate = date('Y-m-d H:i:s', strtotime($ticket_end_time_info));
									$final_ticket_arr[$i]['ticket_end'] = $convertedDate;
								}
							}
						} else {
							$final_ticket_arr[$i][$tkey] = $tval[$i];
						}
					}
				}
			}
		
			//Process if single ticket
			else {
				$final_ticket_arr[0]['ticket_name'] = $data_array['ticket_name'];
				$final_ticket_arr[0]['ticket_description'] = $data_array['ticket_description'];
				$final_ticket_arr[0]['ticket_price'] = $data_array['ticket_price'];

				$tickstart = current_time('Y-m-d H:i:s');
				if ($data_array['ticket_start_time'] != '') {
					if (strpos($data_array['ticket_start_date'], '/') !== false) {
						//if(strpos('/', $new_tickets['ticket_start_date'][$i])){
							$var_start_date = trim($data_array['ticket_start_date']);
							$var_start = str_replace('/', '-', "$var_start_date");
							   if (strtotime($var_start)) {
								  $convertedDate = date('Y-m-d', strtotime($var_start));
								  $event_start_date = $convertedDate;
								}
								$ticket_start_time_info =$event_start_date . ' ' . $data_array['ticket_start_time'];
						}
						else{
							$ticket_start_time_info = $data_array['ticket_start_date'] . ' ' . $data_array['ticket_start_time'];
						}
					//$ticket_start_time_info = $data_array['ticket_start_date'] . ' ' . $data_array['ticket_start_time'];
				} else {
					$ticket_start_time_info = $data_array['ticket_start_date'];
				}
				if (strtotime($ticket_start_time_info)) {
					$convertedDate = date('Y-m-d H:i:s', strtotime($ticket_start_time_info));
					$final_ticket_arr[0]['ticket_start'] = $convertedDate;
				}
				// Convert ticket end date from any format
				$tickend = current_time('Y-m-d H:i:s');
				if ($data_array['ticket_end_time'] != '') {
					if (strpos($data_array['ticket_end_date'], '/') !== false) {
						//if(strpos('/', $new_tickets['ticket_start_date'][$i])){
							$var_end_date = trim($data_array['ticket_end_date']);
							$var_end = str_replace('/', '-', "$var_end_date");
							   if (strtotime($var_start)) {
								  $convertedDate = date('Y-m-d', strtotime($var_end));
								  $event_end_date = $convertedDate;
								}
								$ticket_end_time_info =$event_end_date . ' ' . $data_array['ticket_end_time'];
						}
						else{
							$ticket_end_time_info = $data_array['ticket_end_date'] . ' ' . $data_array['ticket_end_time'];
						}
					//$ticket_end_time_info = $data_array['ticket_end_date'] . ' ' . $data_array['ticket_end_time'];
				} else {
					$ticket_end_time_info = $data_array['ticket_end_date'];
				}
				if (strtotime($ticket_end_time_info)) {
					$convertedDate = date('Y-m-d H:i:s', strtotime($ticket_end_time_info));
					$final_ticket_arr[0]['ticket_end'] = $convertedDate;
				}
				$final_ticket_arr[0]['ticket_min'] = $data_array['ticket_min'];
				$final_ticket_arr[0]['ticket_max'] = $data_array['ticket_max'];
				$final_ticket_arr[0]['ticket_spaces'] = $data_array['ticket_spaces'];
				$final_ticket_arr[0]['ticket_required'] = $data_array['ticket_required'];
			}
			//Ends Ticket process

			$loclat = $loclong = '';
			$address = $locadd . ',' . $loctown . ',' . $locregion . ',' . $locstate . ',' . $loccountry;
			if (!empty($locadd)) {
				$get_lot_long = $this->get_latitude_longitude($locadd);
				$lat_long = explode(',', $get_lot_long);
				$loclat = $lat_long[0];
				$loclong = $lat_long[1];
			}

			if (!empty($locname)) {
				$data_arr['post_type'] = 'location';
				$data_arr['post_status'] = 'publish';
				$data_arr['post_title'] = $locname;
				$data_arr['post_name'] = $locname;
				if ($mode == 'Insert') {
					$loca_post_id = wp_insert_post($data_arr);
					$wpdb->insert("{$wpdb->prefix}em_locations", array('post_id' => $loca_post_id, 'location_name' => $locname, 'location_slug' => $locslug, 'location_address' => $locadd, 'location_town' => $loctown, 'location_state' => $locstate, 'location_postcode' => $loccode, 'location_region' => $locregion, 'location_country' => $loccountry, 'location_latitude' => $loclat, 'location_longitude' => $loclong, 'post_content' => $post_content, 'location_status' => '1', 'location_owner' => $loc_owner));
				}
				if ($mode == 'Update') {
					$loca_post_id = $wpdb->get_var($wpdb->prepare("select post_id from {$wpdb->prefix}em_locations where location_name=%s order by post_id DESC limit 1", $locname));
					if (!empty($loca_post_id)) {
						$wpdb->update("{$wpdb->prefix}em_locations", array('location_name' => $locname, 'location_slug' => $locslug, 'location_address' => $locadd, 'location_town' => $loctown, 'location_state' => $locstate, 'location_postcode' => $loccode, 'location_region' => $locregion, 'location_country' => $loccountry, 'location_latitude' => $loclat, 'location_longitude' => $loclong, 'post_content' => $post_content, 'location_status' => '1', 'location_owner' => $loc_owner), array('post_id' => $loca_post_id, 'location_name' => $locname));
					} else {
						$loca_post_id = wp_insert_post($data_arr);
						$wpdb->insert("{$wpdb->prefix}em_locations", array('post_id' => $loca_post_id, 'location_name' => $locname, 'location_slug' => $locslug, 'location_address' => $locadd, 'location_town' => $loctown, 'location_state' => $locstate, 'location_postcode' => $loccode, 'location_region' => $locregion, 'location_country' => $loccountry, 'location_latitude' => $loclat, 'location_longitude' => $loclong, 'post_content' => $post_content, 'location_status' => '1', 'location_owner' => $loc_owner));
					}
				}

				$location_query = "select location_id from {$wpdb->prefix}em_locations where (post_id = '{$loca_post_id}') order by location_id DESC";
				$location_result = $wpdb->get_results($location_query);
				$location_ID = '';
				if (!empty($location_result)) {
					$location_ID = $location_result[0]->location_id;
				}
				$location_array = array('_location_address' => $locadd, '_location_town' => $loctown, '_location_state' => $locstate, '_location_postcode' => $loccode, '_location_region' => $locregion, '_location_country' => $loccountry, '_location_latitude' => $loclat, '_location_longitude' => $loclong, '_location_id' => $location_ID);
				foreach ($location_array as $key => $value) {
					update_post_meta($loca_post_id, $key, $value);
				}
			}
			if (!empty($location_ID)) {
				$location_id = $location_ID;
			} else {
				$location_id = null;
			}
			$recurr_int = isset($data_array['recurrence_interval']) ? $data_array['recurrence_interval'] : null;
			$recurr_freq = isset($data_array['recurrence_freq']) ? $data_array['recurrence_freq'] : null;
			$recurr_days = isset($data_array['recurrence_days']) ? $data_array['recurrence_days'] : 0;
			$recurr_byday = isset($data_array['recurrence_byday']) ? $data_array['recurrence_byday'] : null;
			$recurr_byweek = isset($data_array['recurrence_byweekno']) ? $data_array['recurrence_byweekno'] : null;
			$recurr_rsvp_days = isset($data_array['recurrence_rsvp_days']) ? $data_array['recurrence_rsvp_days'] : null;

			$event_start_time1 = $event_start_date . ' ' . $event_start_time;
			$event_end_time1 = $event_end_date . ' ' . $event_end_time;
			if ($type == 'Events' || $type == 'Recurring Events') {
				$date = date('Y-m-d H:i:s');
				if ($mode == 'Insert') { 
					$wpdb->insert("{$wpdb->prefix}em_events", array('post_id' => $pID, 'event_name' => $event_name, 'event_slug' => $event_slug, 'event_owner' => $event_owner, 'event_start_date' => $event_start_date, 'event_end_date' => $event_end_date, 'event_all_day' => $eventday, 'event_start_time' => $event_start_time, 'event_end_time' => $event_end_time, 'event_start' => $event_start_time1, 'event_end' => $event_end_time1, 'event_rsvp_date' => $event_rsvp_date, 'event_rsvp_time' => $event_rsvp_time, 'event_rsvp_spaces' => $event_rsvp_spaces, 'event_spaces' => $event_spaces, 'post_content' => $post_content, 'event_rsvp' => 1, 'location_id' => $location_id, 'event_date_created' => $data_array['post_date'], 'event_date_modified' => $date, 'recurrence' => $recurrence, 'recurrence_interval' => $recurr_int, 'recurrence_freq' => $recurr_freq, 'recurrence_byday' => $recurr_byday, 'recurrence_byweekno' => $recurr_byweek, 'recurrence_days' => $recurr_days, 'recurrence_rsvp_days' => $recurr_rsvp_days, 'event_status' => 1, 'blog_id' => null, 'group_id' => 0));
				}
				if ($mode == 'Update') {
					$wpdb->update("{$wpdb->prefix}em_events", array('event_name' => $event_name, 'event_slug' => $event_slug, 'event_owner' => $event_owner, 'event_start_date' => $event_start_date, 'event_end_date' => $event_end_date, 'event_all_day' => $eventday, 'event_start' => $event_start_time1, 'event_end' => $event_end_time1, 'event_start_time' => $event_start_time, 'event_end_time' => $event_end_time, 'event_rsvp_date' => $event_rsvp_date, 'event_rsvp_time' => $event_rsvp_time, 'event_rsvp_spaces' => $event_rsvp_spaces, 'event_spaces' => $event_spaces, 'post_content' => $post_content, 'event_rsvp' => 1, 'location_id' => $location_id, 'event_date_created' => $data_array['post_date'], 'recurrence' => $recurrence, 'recurrence_interval' => $recurr_int, 'recurrence_freq' => $recurr_freq, 'recurrence_byday' => $recurr_byday, 'recurrence_byweekno' => $recurr_byweek, 'recurrence_days' => $recurr_days, 'recurrence_rsvp_days' => $recurr_rsvp_days, 'event_status' => 1, 'blog_id' => null, 'group_id' => 0), array('post_id' => $pID));
				}
			}
			$event_query = "select event_id from {$wpdb->prefix}em_events where (post_id = '{$pID}')";
			$event_result = $wpdb->get_results($event_query);
			$event_id = '';
			if ($event_result) {
				$event_id = $event_result[0]->event_id;
			}
			$event_array = array('_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_date' => $event_rsvp_date, '_event_end_date' => $event_end_date, '_event_start_date' => $event_start_date, '_event_all_day' => $eventday, '_event_end_time' => $event_end_time, '_event_start_time' => $event_start_time, '_recurrence_byweekno' => $recurr_byweek, '_recurrence_byday' => $recurr_byday, '_event_start_local' => $event_start_time1, '_event_end_local' => $event_end_time1, '_event_start' => $event_start_time1, '_event_end' => $event_end_time1, '_recurrence_freq' => $recurr_freq, '_recurrence_freq' => $recurr_freq, '_recurrence_days' => $recurr_days, '_event_rsvp_date' => $event_rsvp_date, '_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_spaces' => $event_rsvp_spaces, '_event_spaces' => $event_spaces, '_event_id' => $event_id, '_location_id' => $location_id, '_event_rsvp' => $event_rsvp, '_event_status' => 1, '_recurrence' => $recurrence, '_recurrence_id' => null, '_event_private' => 0, '_blog_id' => null, '_group_id' => 0, '_start_ts' => $time_start_ts_info, '_end_ts' => $time_end_ts_info, '_recurrence_interval' => $recurr_int, '_recurrence_rsvp_days' => $recurr_rsvp_days);
			foreach ($event_array as $key => $value) {
				update_post_meta($pID, $key, $value);
			}

			if ($type == 'Recurring Events') {
				$date_from = strtotime($event_start_date); // Convert date to a UNIX timestamp              
				$date_to = strtotime($event_end_date); // Convert date to a UNIX timestamp  
				// Loop from the start date to end date and output all dates inbetween 
				if ($recurr_freq == 'dialy') {
					for ($i = $date_from; $i <= $date_to; $i += 86400) {
						$recurr_date = date("Y-m-d", $i);
						$recur_start_time1 = $recurr_date . ' ' . $event_start_time;
						$recur_end_time1 = $recurr_date . ' ' . $event_end_time;
						$recur_slug = $event_slug . '-' . $recurr_date;
						$recurr_byweek = 'NULL';
						$recurr_freq = 'NULL';
						$recurr_byday = 'NULL';
						$recurr_int = 'NULL';
						$recurr_days = 'NULL';
						$recurr_rsvp_days = 'NULL';
						$recurring_events_arr = [];
						$recurring_events_arr['post_title'] = $event_name;
						$recurring_events_arr['post_name'] = $recur_slug;
						//	$recurring_events_arr['post_date'] = $data_array['post_date'];
						$recurring_events_arr['post_type'] = 'event';
						$recurring_events_arr['post_status'] = 'publish';
						$recurring_id = wp_insert_post($recurring_events_arr);

						if ($mode == 'Insert') {
							$taxonomy_instance->set_terms_taxo_values($header_array ,$value_array , $term_map , $recurring_id , $type, $mode);
							$wpdb->insert("{$wpdb->prefix}em_events", array('post_id' => $recurring_id, 'event_name' => $event_name, 'event_slug' => $recur_slug, 'event_owner' => $event_owner, 'event_start_date' => $recurr_date, 'event_end_date' => $recurr_date, 'event_all_day' => $eventday, 'event_start_time' => $event_start_time, 'event_end_time' => $event_end_time, 'event_start' => $recur_start_time1, 'event_end' => $recur_end_time1, 'event_rsvp_date' => $recurr_date, 'event_rsvp_time' => $event_rsvp_time, 'event_rsvp_spaces' => $event_rsvp_spaces, 'event_spaces' => $event_spaces, 'post_content' => $post_content, 'event_rsvp' => 1, 'location_id' => $location_id, 'event_date_created' => $data_array['post_date'], 'event_date_modified' => $date, 'recurrence_id' => $event_id, 'recurrence' => $recurrence, 'recurrence_interval' => $recurr_int, 'recurrence_freq' => $recurr_freq, 'recurrence_byday' => $recurr_byday, 'recurrence_byweekno' => $recurr_byweek, 'recurrence_days' => $recurr_days, 'recurrence_rsvp_days' => $recurr_rsvp_days, 'event_status' => 1, 'blog_id' => null, 'group_id' => 0));
						}

						$recur_query = "select event_id from {$wpdb->prefix}em_events where (post_id = '{$recurring_id}')";
						$recur_result = $wpdb->get_results($recur_query);
						$recur_id = '';
						if ($recur_result) {
							$recur_id = $recur_result[0]->event_id;
						}
						$recur_array = array('_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_date' => $event_rsvp_date, '_event_end_date' => $recurr_date, '_event_start_date' => $recurr_date, '_event_all_day' => $eventday, '_event_end_time' => $event_end_time, '_event_start_time' => $event_start_time, '_recurrence_byweekno' => $recurr_byweek, '_recurrence_byday' => $recurr_byday, '_event_start_local' => $recur_start_time1, '_event_end_local' => $recur_end_time1, '_event_start' => $recur_start_time1, '_event_end' => $recur_end_time1, '_recurrence_freq' => $recurr_freq, '_recurrence_freq' => $recurr_freq, '_recurrence_days' => $recurr_days, '_event_rsvp_date' => $event_rsvp_date, '_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_spaces' => $event_rsvp_spaces, '_event_spaces' => $event_spaces, '_event_id' => $recur_id, '_location_id' => $location_id, '_event_rsvp' => $event_rsvp, '_event_status' => 1, '_recurrence' => $recurrence, '_recurrence_id' => $event_id, '_event_private' => 0, '_blog_id' => null, '_group_id' => 0, '_start_ts' => $time_start_ts_info, '_end_ts' => $time_end_ts_info, '_recurrence_interval' => $recurr_int, '_recurrence_rsvp_days' => $recurr_rsvp_days);
						foreach ($recur_array as $recur_key => $recur_value) {
							update_post_meta($recurring_id, $recur_key, $recur_value);
						}

						$recur_status = $data_array['post_status'];
						$wpdb->get_results("UPDATE {$wpdb->prefix}posts set post_status = '$recur_status' where id = $recurring_id");
					}
				} elseif ($recurr_freq == 'weekly') {
					switch ($recurr_byday) {
						case "1":
							$date = date('Y-m-d', strtotime('next monday', strtotime($event_start_date)));
							break;
						case "2":
							$date = date('Y-m-d', strtotime('next tuesday', strtotime($event_start_date)));
							break;
						case "3":
							$date = date('Y-m-d', strtotime('next wednesday', strtotime($event_start_date)));
							break;
						case "4":
							$date = date('Y-m-d', strtotime('next thursday', strtotime($event_start_date)));
							break;
						case "5":
							$date = date('Y-m-d', strtotime('next friday', strtotime($event_start_date)));
							break;
						case "6":
							$date = date('Y-m-d', strtotime('next saturday', strtotime($event_start_date)));
							break;
						default:
							$date = date('Y-m-d', strtotime('next sunday', strtotime($event_start_date)));
							break;
					}
					$date_from = strtotime($date);
					for ($i = $date_from; $i <= $date_to; $i += 604800) {
						$recurr_date = date("Y-m-d", $i);
						$recur_start_time1 = $recurr_date . ' ' . $event_start_time;
						$recur_end_time1 = $recurr_date . ' ' . $event_end_time;
						$recur_slug = $event_slug . '-' . $recurr_date;
						$recurr_byweek = 'NULL';
						$recurr_freq = 'NULL';
						$recurr_byday = 'NULL';
						$recurr_int = 'NULL';
						$recurr_days = 'NULL';
						$recurr_rsvp_days = 'NULL';
						$recurring_events_arr = [];
						$recurring_events_arr['post_title'] = $event_name;
						$recurring_events_arr['post_name'] = $recur_slug;
						$recurring_events_arr['post_type'] = 'event';
						$recurring_events_arr['post_status'] = 'publish';

						$recurring_id = wp_insert_post($recurring_events_arr);

						if ($mode == 'Insert') {
							$taxonomy_instance->set_terms_taxo_values($header_array ,$value_array , $term_map , $recurring_id , $type, $mode);
							$wpdb->insert("{$wpdb->prefix}em_events", array('post_id' => $recurring_id, 'event_name' => $event_name, 'event_slug' => $recur_slug, 'event_owner' => $event_owner, 'event_start_date' => $recurr_date, 'event_end_date' => $recurr_date, 'event_all_day' => $eventday, 'event_start_time' => $event_start_time, 'event_end_time' => $event_end_time, 'event_start' => $recur_start_time1, 'event_end' => $recur_end_time1, 'event_rsvp_date' => $recurr_date, 'event_rsvp_time' => $event_rsvp_time, 'event_rsvp_spaces' => $event_rsvp_spaces, 'event_spaces' => $event_spaces, 'post_content' => $post_content, 'event_rsvp' => 1, 'location_id' => $location_id, 'event_date_created' => $data_array['post_date'], 'event_date_modified' => $date, 'recurrence_id' => $event_id, 'recurrence' => $recurrence, 'recurrence_interval' => $recurr_int, 'recurrence_freq' => $recurr_freq, 'recurrence_byday' => $recurr_byday, 'recurrence_byweekno' => $recurr_byweek, 'recurrence_days' => $recurr_days, 'recurrence_rsvp_days' => $recurr_rsvp_days, 'event_status' => 1, 'blog_id' => null, 'group_id' => 0));
						}

						$recur_query = "select event_id from {$wpdb->prefix}em_events where (post_id = '{$recurring_id}')";
						$recur_result = $wpdb->get_results($recur_query);
						$recur_id = '';
						if ($recur_result) {
							$recur_id = $recur_result[0]->event_id;
						}
						//New Code by Anto
						$recur_array = array('_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_date' => $event_rsvp_date, '_event_end_date' => $recurr_date, '_event_start_date' => $recurr_date, '_event_all_day' => $eventday, '_event_end_time' => $event_end_time, '_event_start_time' => $event_start_time, '_recurrence_byweekno' => $recurr_byweek, '_recurrence_byday' => $recurr_byday, '_event_start_local' => $recur_start_time1, '_event_end_local' => $recur_end_time1, '_event_start' => $recur_start_time1, '_event_end' => $recur_end_time1, '_recurrence_freq' => $recurr_freq, '_recurrence_freq' => $recurr_freq, '_recurrence_days' => $recurr_days, '_event_rsvp_date' => $event_rsvp_date, '_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_spaces' => $event_rsvp_spaces, '_event_spaces' => $event_spaces, '_event_id' => $recur_id, '_location_id' => $location_id, '_event_rsvp' => $event_rsvp, '_event_status' => 1, '_recurrence' => $recurrence, '_recurrence_id' => $event_id, '_event_private' => 0, '_blog_id' => null, '_group_id' => 0, '_start_ts' => $time_start_ts_info, '_end_ts' => $time_end_ts_info, '_recurrence_interval' => $recurr_int, '_recurrence_rsvp_days' => $recurr_rsvp_days);
						foreach ($recur_array as $recur_key => $recur_value) {
							update_post_meta($recurring_id, $recur_key, $recur_value);
						}

						$recur_status = $data_array['post_status'];
						$wpdb->get_results("UPDATE {$wpdb->prefix}posts set post_status = '$recur_status' where id = $recurring_id");
					}
				}elseif($recurr_freq == 'monthly') {
					switch ($recurr_byday) {
						case "1":
							$day='monday';
							break;
						case "2":
							$day='tuesday';
							break;
						case "3":
							$day='wednesday';
							break;
						case "4":
							$day='thursday';
							break;
						case "5":
							$day='friday';
							break;
						case "6":
							$day='saturday';
							break;
						default:
							$day = 'sunday';
							break;
						
					}
					switch ($recurr_byweek) {
						case "1":
							$order='first';
							break;
						case "2":
							$order='second';
							break;
						case "3":
							$order='third';
							break;
						case "4":
							$order='fourth';
							break;
						case "5":
							$order='fifth';
							break;
						case "6":
							$order='last';
							break;
					}
					
					$date=date('Y-m', $date_from);
					$year=date('Y', $date_from);
					$dates_from=date("Y-m-d",$date_from);
					$dates_to=date("Y-m-d",$date_to);
					$month_start=date('m', $date_from);
					$month_end=date('m', $date_to);
					$ts1 = strtotime($dates_from);
					$ts2 = strtotime($dates_to);
					$year1 = date('Y', $ts1);
					$year2 = date('Y', $ts2);
					$month1 = date('m', $ts1);
					$month2 = date('m', $ts2);
					$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
				    for($in=$month_start; $in < $diff;$in +=$recurr_int){	
						$values[] = $in;
					}
					foreach($values as $monthNum){
						$monthName = date('F', mktime(0, 0, 0, $monthNum, 10)); 		
					$dates[] = date('Y-m-d', strtotime("$order $day of $monthName $year"));
					}
			   foreach($dates as $kdate => $date){
					$recurr_date = $date;
					$recur_start_time1 = $recurr_date . ' ' . $event_start_time;
					$recur_end_time1 = $recurr_date . ' ' . $event_end_time;
					$recur_slug = $event_slug . '-' . $recurr_date;
					$recurr_byweek = 'NULL';
					$recurr_freq = 'NULL';
					$recurr_byday = 'NULL';
					$recurr_int = 'NULL';
					$recurr_days = 'NULL';
					$recurr_rsvp_days = 'NULL';
					$recurring_events_arr = [];
					$recurring_events_arr['post_title'] = $event_name;
					$recurring_events_arr['post_name'] = $recur_slug;
					//	$recurring_events_arr['post_date'] = $data_array['post_date'];
					$recurring_events_arr['post_type'] = 'event';
					$recurring_events_arr['post_status'] = 'publish';
					$recurring_id = wp_insert_post($recurring_events_arr);
	
					if ($mode == 'Insert') {
						$taxonomy_instance->set_terms_taxo_values($header_array ,$value_array , $term_map , $recurring_id , $type, $mode);
						$wpdb->insert("{$wpdb->prefix}em_events", array('post_id' => $recurring_id, 'event_name' => $event_name, 'event_slug' => $recur_slug, 'event_owner' => $event_owner, 'event_start_date' => $recurr_date, 'event_end_date' => $recurr_date, 'event_all_day' => $eventday, 'event_start_time' => $event_start_time, 'event_end_time' => $event_end_time, 'event_start' => $recur_start_time1, 'event_end' => $recur_end_time1, 'event_rsvp_date' => $recurr_date, 'event_rsvp_time' => $event_rsvp_time, 'event_rsvp_spaces' => $event_rsvp_spaces, 'event_spaces' => $event_spaces, 'post_content' => $post_content, 'event_rsvp' => 1, 'location_id' => $location_id, 'event_date_created' => $data_array['post_date'], 'event_date_modified' => $date, 'recurrence_id' => $event_id, 'recurrence' => $recurrence, 'recurrence_interval' => $recurr_int, 'recurrence_freq' => $recurr_freq, 'recurrence_byday' => $recurr_byday, 'recurrence_byweekno' => $recurr_byweek, 'recurrence_days' => $recurr_days, 'recurrence_rsvp_days' => $recurr_rsvp_days, 'event_status' => 1, 'blog_id' => null, 'group_id' => 0));
					}
	
					$recur_query = "select event_id from {$wpdb->prefix}em_events where (post_id = '{$recurring_id}')";
					$recur_result = $wpdb->get_results($recur_query);
					$recur_id = '';
					if ($recur_result) {
						$recur_id = $recur_result[0]->event_id;
					}
					$recur_array = array('_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_date' => $event_rsvp_date, '_event_end_date' => $recurr_date, '_event_start_date' => $recurr_date, '_event_all_day' => $eventday, '_event_end_time' => $event_end_time, '_event_start_time' => $event_start_time, '_recurrence_byweekno' => $recurr_byweek, '_recurrence_byday' => $recurr_byday, '_event_start_local' => $recur_start_time1, '_event_end_local' => $recur_end_time1, '_event_start' => $recur_start_time1, '_event_end' => $recur_end_time1, '_recurrence_freq' => $recurr_freq, '_recurrence_freq' => $recurr_freq, '_recurrence_days' => $recurr_days, '_event_rsvp_date' => $event_rsvp_date, '_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_spaces' => $event_rsvp_spaces, '_event_spaces' => $event_spaces, '_event_id' => $recur_id, '_location_id' => $location_id, '_event_rsvp' => $event_rsvp, '_event_status' => 1, '_recurrence' => $recurrence, '_recurrence_id' => $event_id, '_event_private' => 0, '_blog_id' => null, '_group_id' => 0, '_start_ts' => $time_start_ts_info, '_end_ts' => $time_end_ts_info, '_recurrence_interval' => $recurr_int, '_recurrence_rsvp_days' => $recurr_rsvp_days);
					foreach ($recur_array as $recur_key => $recur_value) {
						update_post_meta($recurring_id, $recur_key, $recur_value);
					}
	
					$recur_status = $data_array['post_status'];
					$wpdb->get_results("UPDATE {$wpdb->prefix}posts set post_status = '$recur_status' where id = $recurring_id");
				   
				
			   }
			 
					for ($i = $date_from; $i <= $date_to; $i++) {
						$recurr_date = date("Y-m-d", $i);
						$recur_start_time1 = $recurr_date . ' ' . $event_start_time;
						$recur_end_time1 = $recurr_date . ' ' . $event_end_time;
						$recur_slug = $event_slug . '-' . $recurr_date;
						$recurr_byweek = 'NULL';
						$recurr_freq = 'NULL';
						$recurr_byday = 'NULL';
						$recurr_int = 'NULL';
						$recurr_days = 'NULL';
						$recurr_rsvp_days = 'NULL';
						$recurring_events_arr = [];
						$recurring_events_arr['post_title'] = $event_name;
						$recurring_events_arr['post_name'] = $recur_slug;
						//	$recurring_events_arr['post_date'] = $data_array['post_date'];
						$recurring_events_arr['post_type'] = 'event';
						$recurring_events_arr['post_status'] = 'publish';
						$recurring_id = wp_insert_post($recurring_events_arr);

						if ($mode == 'Insert') {
							$taxonomy_instance->set_terms_taxo_values($header_array ,$value_array , $term_map , $recurring_id , $type, $mode);
							$wpdb->insert("{$wpdb->prefix}em_events", array('post_id' => $recurring_id, 'event_name' => $event_name, 'event_slug' => $recur_slug, 'event_owner' => $event_owner, 'event_start_date' => $recurr_date, 'event_end_date' => $recurr_date, 'event_all_day' => $eventday, 'event_start_time' => $event_start_time, 'event_end_time' => $event_end_time, 'event_start' => $recur_start_time1, 'event_end' => $recur_end_time1, 'event_rsvp_date' => $recurr_date, 'event_rsvp_time' => $event_rsvp_time, 'event_rsvp_spaces' => $event_rsvp_spaces, 'event_spaces' => $event_spaces, 'post_content' => $post_content, 'event_rsvp' => 1, 'location_id' => $location_id, 'event_date_created' => $data_array['post_date'], 'event_date_modified' => $date, 'recurrence_id' => $event_id, 'recurrence' => $recurrence, 'recurrence_interval' => $recurr_int, 'recurrence_freq' => $recurr_freq, 'recurrence_byday' => $recurr_byday, 'recurrence_byweekno' => $recurr_byweek, 'recurrence_days' => $recurr_days, 'recurrence_rsvp_days' => $recurr_rsvp_days, 'event_status' => 1, 'blog_id' => null, 'group_id' => 0));
						}

						$recur_query = "select event_id from {$wpdb->prefix}em_events where (post_id = '{$recurring_id}')";
						$recur_result = $wpdb->get_results($recur_query);
						$recur_id = '';
						if ($recur_result) {
							$recur_id = $recur_result[0]->event_id;
						}
						$recur_array = array('_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_date' => $event_rsvp_date, '_event_end_date' => $recurr_date, '_event_start_date' => $recurr_date, '_event_all_day' => $eventday, '_event_end_time' => $event_end_time, '_event_start_time' => $event_start_time, '_recurrence_byweekno' => $recurr_byweek, '_recurrence_byday' => $recurr_byday, '_event_start_local' => $recur_start_time1, '_event_end_local' => $recur_end_time1, '_event_start' => $recur_start_time1, '_event_end' => $recur_end_time1, '_recurrence_freq' => $recurr_freq, '_recurrence_freq' => $recurr_freq, '_recurrence_days' => $recurr_days, '_event_rsvp_date' => $event_rsvp_date, '_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_spaces' => $event_rsvp_spaces, '_event_spaces' => $event_spaces, '_event_id' => $recur_id, '_location_id' => $location_id, '_event_rsvp' => $event_rsvp, '_event_status' => 1, '_recurrence' => $recurrence, '_recurrence_id' => $event_id, '_event_private' => 0, '_blog_id' => null, '_group_id' => 0, '_start_ts' => $time_start_ts_info, '_end_ts' => $time_end_ts_info, '_recurrence_interval' => $recurr_int, '_recurrence_rsvp_days' => $recurr_rsvp_days);
						foreach ($recur_array as $recur_key => $recur_value) {
							update_post_meta($recurring_id, $recur_key, $recur_value);
						}

						$recur_status = $data_array['post_status'];
						$wpdb->get_results("UPDATE {$wpdb->prefix}posts set post_status = '$recur_status' where id = $recurring_id");
					}
				}  elseif ($recurr_freq == 'yearly') {
					for ($i = $date_from; $i <= $date_to; $i += 3.154e+7) {
						$recurr_date = date("Y-m-d", $i);
						$recur_start_time1 = $recurr_date . ' ' . $event_start_time;
						$recur_end_time1 = $recurr_date . ' ' . $event_end_time;
						$recur_slug = $event_slug . '-' . $recurr_date;
						$recurr_byweek = 'NULL';
						$recurr_freq = 'NULL';
						$recurr_byday = 'NULL';
						$recurr_int = 'NULL';
						$recurr_days = 'NULL';
						$recurr_rsvp_days = 'NULL';
						$recurring_events_arr = [];
						$recurring_events_arr['post_title'] = $event_name;
						$recurring_events_arr['post_name'] = $recur_slug;
						$recurring_events_arr['post_type'] = 'event';
						$recurring_events_arr['post_status'] = 'publish';

						$recurring_id = wp_insert_post($recurring_events_arr);

						if ($mode == 'Insert') {
							$taxonomy_instance->set_terms_taxo_values($header_array ,$value_array , $term_map , $recurring_id , $type, $mode);
							$wpdb->insert("{$wpdb->prefix}em_events", array('post_id' => $recurring_id, 'event_name' => $event_name, 'event_slug' => $recur_slug, 'event_owner' => $event_owner, 'event_start_date' => $recurr_date, 'event_end_date' => $recurr_date, 'event_all_day' => $eventday, 'event_start_time' => $event_start_time, 'event_end_time' => $event_end_time, 'event_start' => $recur_start_time1, 'event_end' => $recur_end_time1, 'event_rsvp_date' => $recurr_date, 'event_rsvp_time' => $event_rsvp_time, 'event_rsvp_spaces' => $event_rsvp_spaces, 'event_spaces' => $event_spaces, 'post_content' => $post_content, 'event_rsvp' => 1, 'location_id' => $location_id, 'event_date_created' => $data_array['post_date'], 'event_date_modified' => $date, 'recurrence_id' => $event_id, 'recurrence' => $recurrence, 'recurrence_interval' => $recurr_int, 'recurrence_freq' => $recurr_freq, 'recurrence_byday' => $recurr_byday, 'recurrence_byweekno' => $recurr_byweek, 'recurrence_days' => $recurr_days, 'recurrence_rsvp_days' => $recurr_rsvp_days, 'event_status' => 1, 'blog_id' => null, 'group_id' => 0));
						}

						$recur_query = "select event_id from {$wpdb->prefix}em_events where (post_id = '{$recurring_id}')";
						$recur_result = $wpdb->get_results($recur_query);
						$recur_id = '';
						if ($recur_result) {
							$recur_id = $recur_result[0]->event_id;
						}
						$recur_array = array('_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_date' => $event_rsvp_date, '_event_end_date' => $recurr_date, '_event_start_date' => $recurr_date, '_event_all_day' => $eventday, '_event_end_time' => $event_end_time, '_event_start_time' => $event_start_time, '_recurrence_byweekno' => $recurr_byweek, '_recurrence_byday' => $recurr_byday, '_event_start_local' => $recur_start_time1, '_event_end_local' => $recur_end_time1, '_event_start' => $recur_start_time1, '_event_end' => $recur_end_time1, '_recurrence_freq' => $recurr_freq, '_recurrence_freq' => $recurr_freq, '_recurrence_days' => $recurr_days, '_event_rsvp_date' => $event_rsvp_date, '_event_rsvp_time' => $event_rsvp_time, '_event_rsvp_spaces' => $event_rsvp_spaces, '_event_spaces' => $event_spaces, '_event_id' => $recur_id, '_location_id' => $location_id, '_event_rsvp' => $event_rsvp, '_event_status' => 1, '_recurrence' => $recurrence, '_recurrence_id' => $event_id, '_event_private' => 0, '_blog_id' => null, '_group_id' => 0, '_start_ts' => $time_start_ts_info, '_end_ts' => $time_end_ts_info, '_recurrence_interval' => $recurr_int, '_recurrence_rsvp_days' => $recurr_rsvp_days);
						foreach ($recur_array as $recur_key => $recur_value) {
							update_post_meta($recurring_id, $recur_key, $recur_value);
						}

						$recur_status = $data_array['post_status'];
						$wpdb->get_results("UPDATE {$wpdb->prefix}posts set post_status = '$recur_status' where id = $recurring_id");
					}
				}
			}
			foreach ($final_ticket_arr as $tick_key => $tick_val) {
				
				$tickname = $tick_val['ticket_name'];
				$tickdesc = $tick_val['ticket_description'];
				$tickprice = $tick_val['ticket_price'];
				$tickstart = $tick_val['ticket_start'];
				$tickend = $tick_val['ticket_end'];
				$tickmin = $tick_val['ticket_min'];
				$tickmax = $tick_val['ticket_max'];
				$tickspaces = $tick_val['ticket_spaces'];
				$tickmembers=$tick_val['ticket_members'];
				
				$ticketmemroles=$tick_val['ticket_members_roles'];
				$tic=trim($ticketmemroles);
				$tickmemroles=explode('| ',$tic);
				
				$serialise=serialize($tickmemroles);
				$tickguest=$tick_val['ticket_guests'];
				$tickreq = $tick_val['ticket_required'];	
		//	$count=count($tickname);
				if ($mode == 'Insert') {
					if(!empty($tickname[$tick_key])){
					$wpdb->insert("{$wpdb->prefix}em_tickets", array('event_id' => $event_id, 'ticket_name' => $tickname, 'ticket_description' => $tickdesc, 'ticket_price' => $tickprice, 'ticket_start' => $tickstart, 'ticket_end' => $tickend, 'ticket_min' => $tickmin, 'ticket_max' => $tickmax, 'ticket_spaces' => $tickspaces, 'ticket_members' => $tickmembers, 'ticket_members_roles' => $serialise, 'ticket_guests' => $tickguest, 'ticket_required' => $tickreq));
					}
				}
				if ($mode == 'Update') {
					$wpdb->update("{$wpdb->prefix}em_tickets", array('ticket_name' => $tickname, 'ticket_description' => $tickdesc, 'ticket_price' => $tickprice, 'ticket_start' => $tickstart, 'ticket_end' => $tickend, 'ticket_min' => $tickmin, 'ticket_max' => $tickmax, 'ticket_spaces' => $tickspaces,'ticket_members' => $tickmembers, 'ticket_members_roles' => $serialise, 'ticket_guests' => $tickguest, 'ticket_required' => $tickreq), array('event_id' => $event_id, 'ticket_name' => $tickname));
				}
			}
		}
		//Tickets Bulk Import 
		if ($type == 'Tickets') {
			//get Event ID
			if (!empty($data_array['ID'])) {
				$ticket_post_id = $data_array['ID'];
			}
			if (!empty($ticket_post_id)) {
				$event_id = $wpdb->get_var($wpdb->prepare("select event_id from {$wpdb->prefix}em_events where post_id=%d", $ticket_post_id));
			}
			//Get ticket values
			$tickname = $data_array['ticket_name'];
			$tickdesc = $data_array['ticket_description'];
			$tickprice = $data_array['ticket_price'];
			//Ticket Start Date
			$tickstart = current_time('Y-m-d H:i:s');
			if ($data_array['ticket_start_time'] != '') {
				$ticket_start_time_info = $data_array['ticket_start_date'] . ' ' . $data_array['ticket_start_time'];
			} else {
				$ticket_start_time_info = $data_array['ticket_start_date'];
			}
			if (strtotime($ticket_start_time_info)) {
				$convertedDate = date('Y-m-d H:i:s', strtotime($ticket_start_time_info));
				$tickstart = $convertedDate;
			}
			// Convert ticket end date from any format - Ticket End Date
			$tickend = current_time('Y-m-d H:i:s');
			if ($data_array['ticket_end_time'] != '') {
				$ticket_end_time_info = $data_array['ticket_end_date'] . ' ' . $data_array['ticket_end_time'];
			} else {
				$ticket_end_time_info = $data_array['ticket_end_date'];
			}
			if (strtotime($ticket_end_time_info)) {
				$convertedDate = date('Y-m-d H:i:s', strtotime($ticket_end_time_info));
				$tickend = $convertedDate;
			}

			$tickmin = $data_array['ticket_min'];
			$tickmax = $data_array['ticket_max'];
			$tickspaces = $data_array['ticket_spaces'];
			$tickreq = $data_array['ticket_required'];

			if ($mode == 'Insert') {
				if(!empty($tickname)){
				$wpdb->insert("{$wpdb->prefix}em_tickets", array('event_id' => $event_id, 'ticket_name' => $tickname, 'ticket_description' => $tickdesc, 'ticket_price' => $tickprice, 'ticket_start' => $tickstart, 'ticket_end' => $tickend, 'ticket_min' => $tickmin, 'ticket_max' => $tickmax, 'ticket_spaces' => $tickspaces, 'ticket_required' => $tickreq));
				}
			}
			if ($mode == 'Update') {
				//Check ticket name already present
				$check_ticket_present = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}em_tickets where ticket_name=%s and event_id=%d", $tickname, $event_id));
				if (!empty($check_ticket_present)) { //Update if ticket name already present
					$wpdb->update("{$wpdb->prefix}em_tickets", array('ticket_name' => $tickname, 'ticket_description' => $tickdesc, 'ticket_price' => $tickprice, 'ticket_start' => $tickstart, 'ticket_end' => $tickend, 'ticket_min' => $tickmin, 'ticket_max' => $tickmax, 'ticket_spaces' => $tickspaces, 'ticket_required' => $tickreq), array('event_id' => $event_id, 'ticket_name' => $tickname));
				} else { //Create ticket if not exist
					$wpdb->insert("{$wpdb->prefix}em_tickets", array('event_id' => $event_id, 'ticket_name' => $tickname, 'ticket_description' => $tickdesc, 'ticket_price' => $tickprice, 'ticket_start' => $tickstart, 'ticket_end' => $tickend, 'ticket_min' => $tickmin, 'ticket_max' => $tickmax, 'ticket_spaces' => $tickspaces, 'ticket_required' => $tickreq));
				}
			}
		}

		if ($type == 'Event Locations') {
			$locname = isset($data_array['post_title']) ? $data_array['post_title'] : '';
			$locslug = strtolower($locname);
			$locadd = isset($data_array['location_address']) ? $data_array['location_address'] : '';
			$loctown = isset($data_array['location_town']) ? $data_array['location_town'] : '';
			$locstate = isset($data_array['location_state']) ? $data_array['location_state'] : '';
			$loccode = isset($data_array['location_postcode']) ? $data_array['location_postcode'] : '';
			$locregion = isset($data_array['location_region']) ? $data_array['location_region'] : '';
			$loccountry = isset($data_array['location_country']) ? $data_array['location_country'] : '';
			$loc_owner = isset($data_array['post_author']) ? $data_array['post_author'] : '';
			$loclat = $loclong = '';
			if (!empty($locadd)) {
				$get_lot_long = $this->get_latitude_longitude($locadd);
				$lat_long = explode(',', $get_lot_long);
				$loclat = $lat_long[0];
				$loclong = $lat_long[1];
			}
			$location_ID = '';
			if ($mode == 'Insert') {
				$wpdb->insert("{$wpdb->prefix}em_locations", array('post_id' => $pID, 'location_name' => $locname, 'location_slug' => $locslug, 'location_address' => $locadd, 'location_town' => $loctown, 'location_state' => $locstate, 'location_postcode' => $loccode, 'location_region' => $locregion, 'location_country' => $loccountry, 'location_latitude' => $loclat, 'location_longitude' => $loclong, 'post_content' => $post_content, 'location_status' => '1', 'location_owner' => $loc_owner));
			}
			if ($mode == 'Update') {
				$check_location_exist = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}em_locations where location_name=%s and post_id=%d", $locname, $pID));
				if (!empty($check_location_exist)) {
					$wpdb->update("{$wpdb->prefix}em_locations", array('location_name' => $locname, 'location_slug' => $locslug, 'location_address' => $locadd, 'location_town' => $loctown, 'location_state' => $locstate, 'location_postcode' => $loccode, 'location_region' => $locregion, 'location_country' => $loccountry, 'location_latitude' => $loclat, 'location_longitude' => $loclong, 'post_content' => $post_content, 'location_status' => '1', 'location_owner' => $loc_owner), array('post_id' => $pID, 'location_name' => $locname));
				} else {
					$wpdb->insert("{$wpdb->prefix}em_locations", array('post_id' => $pID, 'location_name' => $locname, 'location_slug' => $locslug, 'location_address' => $locadd, 'location_town' => $loctown, 'location_state' => $locstate, 'location_postcode' => $loccode, 'location_region' => $locregion, 'location_country' => $loccountry, 'location_latitude' => $loclat, 'location_longitude' => $loclong, 'post_content' => $post_content, 'location_status' => '1', 'location_owner' => $loc_owner));
				}
			}
			$location_query = "select location_id from {$wpdb->prefix}em_locations where (post_id = '{$pID}') order by location_id DESC";
			$location_result = $wpdb->get_results($location_query);
			if ($location_result) {
				$location_ID = $location_result[0]->location_id;
			}
			$location_array = array('_location_address' => $locadd, '_location_town' => $loctown, '_location_state' => $locstate, '_location_postcode' => $loccode, '_location_region' => $locregion, '_location_country' => $loccountry, '_location_latitude' => $loclat, '_location_longitude' => $loclong, '_location_id' => $location_ID);
			foreach ($location_array as $key => $value) {
				update_post_meta($pID, $key, $value);
			}
		}
	}

	public function get_latitude_longitude($address = null, $region = null)
	{
		$address = str_replace(" ", "+", $address);
		$json = @file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
		$json = json_decode($json);
		if (!empty($json->results)) {
			$lat  = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
			return $lat . ',' . $long;
		} else {
			return false;
		}
	}

}

