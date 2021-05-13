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

class EventsManagerExtension extends ExtensionHandler{
    private static $instance = null;
	
    public static function getInstance() {		
		if (EventsManagerExtension::$instance == null) {
			EventsManagerExtension::$instance = new EventsManagerExtension;
		}
		return EventsManagerExtension::$instance;
    }

	/**
	* Provides Events Manager mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
		$response = [];
		$import_type = $data;
		if($import_type != 'Tickets'){
        	$events_manager_Fields = array(
			        'Event_start_date' => 'event_start_date',
					'Event_end_date' => 'event_end_date',
					'Event_start_time' => 'event_start_time',
					'Event_end_time' => 'event_end_time',
					'Event_all_day' => 'event_all_day',
					'Event_rsvp_date' => 'event_rsvp_date',
					'Event_rsvp_time' => 'event_rsvp_time',
					'Event_rsvp_spaces' => 'event_rsvp_spaces',
					'Event_spaces' => 'event_spaces',
					'Recurrence_interval' => 'recurrence_interval',
					'Recurrence_freq' => 'recurrence_freq',
					'Recurrence_byday' => 'recurrence_byday',
					'Recurrence_byweekno'=>'recurrence_byweekno',
					'Recurrence_days' => 'recurrence_days',
					'Recurrence_rsvp_days' => 'recurrence_rsvp_days',
					'Location_name' => 'location_name',
					'Location_address' => 'location_address',
					'Location_town' =>'location_town',
					'Location_state' => 'location_state',
					'Location_postcode' => 'location_postcode',
					'Location_region' => 'location_region',
					'Location_country' => 'location_country',
					'Ticket_name' => 'ticket_name',
					'Ticket_description' => 'ticket_description',
					'Ticket_price' => 'ticket_price',
					'Ticket_start_date' => 'ticket_start_date',
					'Ticket_end_date' => 'ticket_end_date',
					'Ticket_start_time' => 'ticket_start_time',
					'Ticket_end_time' => 'ticket_end_time',
					'Ticket_min' => 'ticket_min',
					'Ticket_max' => 'ticket_max',
					'Ticket_spaces' => 'ticket_spaces',
					'Ticket_members' => 'ticket_members',
					'Ticket_members_roles' =>'ticket_members_roles',
					'Ticket_guests' => 'ticket_guests',
					'Ticket_required' => 'ticket_required',
			);
		}
		
		if($import_type === 'Tickets'){
			$events_manager_Fields = array(
					'Ticket_name' => 'ticket_name',
					'Ticket_description' => 'ticket_description',
					'Ticket_price' => 'ticket_price',
					'Ticket_start_date' => 'ticket_start_date',
					'Ticket_end_date' => 'ticket_end_date',
					'Ticket_start_time' => 'ticket_start_time',
					'Ticket_end_time' => 'ticket_end_time',
					'Ticket_min' => 'ticket_min',
					'Ticket_max' => 'ticket_max',
					'Ticket_spaces' => 'ticket_spaces',
					'Ticket_members' => 'ticket_members',
					'Ticket_members_roles' =>'ticket_members_roles',
					'Ticket_guests' => 'ticket_guests',
					'Ticket_required' => 'ticket_required',
			);
		}
		$events_manager_values = $this->convert_static_fields_to_array($events_manager_Fields);
		$response['events_manager_fields'] = $events_manager_values;
		return $response;	
    }

	/**
	* Events Manager extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type){
		
		if(is_plugin_active('events-manager/events-manager.php')){
			$import_type = $this->import_name_as($import_type);
			if($import_type =='event' || $import_type =='location' || $import_type == 'event-recurring' || $import_type == 'ticket') {				
				return true;
			}
			else{
				return false;
			}
		}
	}
}