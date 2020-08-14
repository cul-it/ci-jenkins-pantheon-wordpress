<?php

/**
 * Software Availability
 */
add_shortcode('software_avail', 'render_software_avail');

function render_software_avail( $args ) {
  $data = get_sassafras_report();

  if ( $data ) {
    // Pass the Sassafras data to Vue if request was successful
    $jsonData = json_encode($data);
    $sassafrasHandoff = <<<EOT
    <script type="text/javascript">
      const sassafrasDataWP = $jsonData
    </script>
EOT;

    wp_enqueue_script('vue-vendors-chunk');
    wp_enqueue_script('vue-software-list');

    // Render placeholder when editing in Elementor
    // -- since it provides no reasonable hook to enqueue assets after iframe is fully loaded
    // -- https://github.com/elementor/elementor/issues/3337#issuecomment-389544232
    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
      $placeholder = <<<EOT
        <div role="alert" class="alert__info">
          <h2 class="alert__header">Software Availability Placeholder</h2>
          <p>Use Elementor's <strong>Preview Changes</strong> to display the real-time content that will be rendered on the live site.</p>
        </div>
EOT;
    } else {
      $placeholder = '';
    }

    return $sassafrasHandoff . '<div id="cul-software">' . $placeholder . '</div>';
  } else {
    // Otherwise indicate that data is unavailable
    return '<div role="alert" class="alert__error"><h3 class="alert__header">Unable to retrieve latest software availability data</h3><p>If the issue persists, please take a moment to report this error.</p></div>';
  }
}

function get_sassafras_report() {
  // Use cached data if it exists
  if ( false === ( $sassafrasData = get_transient( 'sassafras_software' ) ) ) {
    // Transient cache wasn't available, so fetch the data from Sassafras API & save it to the cache
    $apiUrlBase = 'https://licensing.citlabs.cornell.edu/archive/';
    $apiUrlParams = '?untagged=lib-hide&format=transform-cornell-products';
    
    // Default to 'olinuris' if no unit is set
    $unit = (constant('CUL_UNIT') === 'None') ? 'olinuris' : constant('CUL_UNIT');
    
    $culConfig = json_decode(getenv('CUL_CONFIG'));
    
    // Currently using array for`sassafras_reports` becuase of olinuris (two units, one site)
    $apiReports = $culConfig->units->$unit->sassafras_reports;
    
    $sassafrasData = array();
    
    foreach ($apiReports as $key => $reportId) {
      $apiUrl = $apiUrlBase . $reportId . $apiUrlParams;
      
      $args = array(
        'headers' => array(
          'Authorization' => 'Basic ' . base64_encode(getenv("SASSAFRAS_USER") . ':' . getenv("SASSAFRAS_PW"))
        ),
        'timeout' => 10 // 10 seconds – Pantheon cURL hits default timeout (5s) otherwise
      );
      $response = wp_remote_get($apiUrl, $args);
      $result = json_decode(wp_remote_retrieve_body($response));
      if ( is_array($result) && !is_wp_error($result) ) {
        array_push($sassafrasData, $result);
      } else {
        return false;
      }
    }

    // Store the Sassafras data in the transient cache for 2 hrs max
    set_transient( 'sassafras_software', $sassafrasData, 2 * HOUR_IN_SECONDS );
  }

  return $sassafrasData;
}