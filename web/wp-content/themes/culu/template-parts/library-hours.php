<?php

/**
 * Template part for displaying hours status content in header.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<div class="available-time">
    <?php $lid_1 = get_option('libcal_library_id_1');
    $lid_2 = get_option('libcal_library_id_2');
    if ($lid_1 != NULL) :
        $shortcode = '[libcal_header_hours lid="' . $lid_1 . '"]'; ?>
        <i class="fas fa-clock icon-time" aria-hidden="true"><span class="sr-only">Library hours
                status</span></i>
        <?php echo do_shortcode($shortcode); ?>
    <?php endif;
    if ($lid_2 != NULL) :
        $shortcode = '[libcal_header_hours lid="' . $lid_2 . '"]'; ?>
        <br />
        <i class="fas fa-clock icon-time" aria-hidden="true"><span class="sr-only">Library hours
                status</span></i>
        <?php echo do_shortcode($shortcode); ?>
        <br />
    <?php endif; ?>
    <?php if ($lid_2 == NULL) : ?> - <?php endif; ?><a class="full-hours" href="/full-hours">Full
        Hours</a> /

    <ul class="header-contact">
        <li>
            <a href="<?php if (filter_var($email_label, FILTER_VALIDATE_EMAIL)) : ?>mailto:<?php echo $email_label ?><?php else : ?><?php echo $email_label ?><?php endif; ?>" title="Contact us">
                <i class="fas fa-envelope" aria-hidden="true"><span class="sr-only">Contact
                        us</span></i>
            </a>
        </li>
        <?php if (!empty($google_map_label)) { ?>
            <li>
                <a href="<?php echo $google_map_label ?>" title="Library location"><i class="fas fa-map-marker-alt" aria-hidden="true"><span class="sr-only">Library
                            Location</span></i></a>
            </li>
        <?php } ?>
    </ul>
</div>