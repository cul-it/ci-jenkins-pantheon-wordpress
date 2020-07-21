<?php

/**
 * Software Availability
 */
add_shortcode('software_avail', 'render_software_avail');

function render_software_avail( $args ) {
  wp_enqueue_script('vue-vendors-chunk');
  wp_enqueue_script('vue-software-list');

  // Render placeholder when editing in Elementor
  // -- since it provides no reasonable hook to enqueue assets after iframe is fully loaded
  // -- https://github.com/elementor/elementor/issues/3337#issuecomment-389544232
  if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
    $placeholder = <<<EOT
      <div class="elementor-placeholder">
        <h2>Software Availability Placeholder</h2>
        <p>Use Elementor's <strong>Preview Changes</strong> to display the real-time content that will be rendered on the live site.</p>
      </div>
EOT;
  } else {
    $placeholder = '';
  }

  return '<div id="cul-software">' . $placeholder . '</div>';
}
