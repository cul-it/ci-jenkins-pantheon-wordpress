<?php

/**
 *
 * Display libcal equipment.
 * Shortcode example: [equipment]
 * @package culu
 *
 *
 */

function culu_get_equipment($atts)
{
    // Get unit name
    $unit = constant('CUL_UNIT');
    $cul_config = json_decode(getenv('CUL_CONFIG'));
    $get_equipment_lid = $cul_config->units->$unit->libcal->spaces_equip_lid;

    if (!empty($get_equipment_lid)) {
        $equipment_lid = implode(",", $get_equipment_lid);

        $url_equipment = 'https://api2.libcal.com/1.1/equipment/categories/' . $equipment_lid;
        $options_equipment = array(
            'http' => array(
                'method'  => 'GET',
                'header' => 'Authorization: Bearer ' . get_libcal_token()
            )
        );

        $context_equipment  = stream_context_create($options_equipment);
        // Read JSON file
        $json_data = file_get_contents($url_equipment, false, $context_equipment);
        // Decode JSON data into PHP array
        $response_data = json_decode($json_data);

        // Array to hold all the equipment categories of unit(s)
        $categoryTypes = array();

        // Loop throught all units
        foreach ($response_data as $key => $value) {

            // Loop throught all categories within a unit
            foreach ($response_data[$key]->categories as $category) {
                $categoryTypes[$category->cid] = $category->name;
            }
        }

        // Alpha order array
        asort($categoryTypes);

        // Holds category display  
        $display_categories = "";

        $display_categories .= "<ul class='equipment'>";

        // Loop throught all category types
        foreach ($categoryTypes as $key => $value) {

            $url_equipment = 'https://api2.libcal.com/1.1/equipment/category/' . $key;

            $options_equipment = array(
                'http' => array(
                    'method'  => 'GET',
                    'header' => 'Authorization: Bearer ' . get_libcal_token()
                )
            );

            $context_equipment  = stream_context_create($options_equipment);

            // Read JSON file
            $json_data = file_get_contents($url_equipment, false, $context_equipment);
            $response_data = json_decode($json_data);
            $itemNumber = $response_data[0]->items[0];

            if (!empty($itemNumber)) {
                $display_categories .=
                    "<li class='equipment-item'>
                    <div class='equipment-type'><p>$value</p></div>
                
                    <div class='equipment-btn'>
                      <a class='btn-graphic' href='https://spaces.library.cornell.edu/equipment/item/{$itemNumber}'>Reserve</a></p>
                    </div>
                </li>";
            }
        }
        $display_categories .= "</ul>";
        return $display_categories;
    }
}

add_shortcode('equipment', 'culu_get_equipment');
