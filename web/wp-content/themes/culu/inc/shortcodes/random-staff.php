<?php

/**
 *
 * Random staff progile
 * @package culu
 *
 *
 */

function culu_random_staff($atts)
{
    $unit = constant('CUL_UNIT');

    // Normalize attribute keys, lowercase
    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    // Combine user attributes with known attributes & fill in defaults when needed
    // -- https://developer.wordpress.org/reference/functions/shortcode_atts
    $staff_atts = shortcode_atts(
        array(
            'librarians_only' => false,
        ),
        $atts
    );

    // Convert attribute value to boolean
    $librarians_only = wp_validate_boolean($staff_atts['librarians_only']);

    // Prepare arguments for WP Query
    $args = array(
        'post_type' => 'staff',
        'orderby'   => 'rand',
        'posts_per_page' => 1,
    );

    // Limit to librarians only if shortcode attribute exists
    if ($librarians_only) {
        $args['meta_query'] = array(
            array(
                'key' => 'librarian',
                'value' => $librarians_only,
            )
        );
    }

    // Get Query
    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();

            $image = get_field('photo');
            $first_name = get_field('first_name');
            $last_name = get_field('last_name');
            $name = $first_name . " " . $last_name;
            $anchor_name = "/staff-profile/#" . strtolower($first_name) . "-" . strtolower($last_name);
            $title = get_field('title');
            $consultation = get_field('consultation');

            // Render random staff profile
            if (!empty($image)) {
                $img_src = $image['url'];
                $img_alt = $image['alt'];
            } else {
                $img_unit = ($unit === 'mann') ? $unit . '-' : '';
                $img_src = get_template_directory_uri() . '/images/staff/' . $img_unit . 'no-photo-profile.png';
                $img_alt = 'staff photo not available';
            }

            // Holds random profiles display
            $display_random_profile = "<a href='$anchor_name'><img class='staff-photo' src='$img_src' alt='$img_alt'></a>";
            $display_random_profile .= "<h3><a href='$anchor_name'>$name<span>$title</span></a></h3>";

            if (!empty($consultation)) {
                $uid = str_replace("mysched_", "", $consultation);
                $libcal_js = <<<EOT
                    <script>
                        jQuery.getScript("https://api3.libcal.com/js/myscheduler.min.js", function() {
                            jQuery("#$consultation").LibCalMySched({
                                iid: 973,
                                lid: 0,
                                gid: 0,
                                uid: $uid,
                                width: 560,
                                height: 680,
                                title: 'Make an Appointment',
                                domain: 'https://api3.libcal.com'
                            });
                        });
                    </script>
                EOT;

                $display_random_profile .= "<p><a href='#' id='$consultation' class='btn-graphic'>Book a Consultation</a></p>";
            } else {
                $libcal_js = '';
            }

            $display_random_profile .= "<p class='all-staff'><a href='/staff-profile/'>All staff »</a></p>";

            $random_staff = <<<EOT
                $libcal_js
                <section class="random-staff-profile" aria-label="$name staff profile">
                    $display_random_profile
                </section>
            EOT;

            return $random_staff;
        }

        // Restore original Post Data
        wp_reset_postdata();
    } else {
        return "No staff profiles found";
    }
}

add_shortcode('random-staff', 'culu_random_staff');
