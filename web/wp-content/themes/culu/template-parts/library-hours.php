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
    <?php
    $email_label = get_theme_mod('email_label', '');
    $lid_1 = get_option('libcal_library_id_1');
    $lid_2 = get_option('libcal_library_id_2'); ?>

    <div class="primary-hours">
        <?php if ($lid_1 != NULL) :
            $shortcode = '[libcal_header_hours lid="' . $lid_1 . '"]'; ?>

            <?php echo do_shortcode($shortcode); ?>
            - <a class="full-hours" href="/full-hours">Full
                Hours</a> / <a href="<?php if (filter_var($email_label, FILTER_VALIDATE_EMAIL)) : ?>mailto:<?php echo $email_label ?><?php else : ?><?php echo $email_label ?><?php endif; ?>" title="Contact us">
                <i class="fas fa-envelope" aria-hidden="true"><span class="sr-only">Contact us</span></i>
            </a>
    </div>

<?php endif; ?>

<div class="secondary-hours">
    <?php if ($lid_2 != NULL) :
        $shortcode = '[libcal_header_hours lid="' . $lid_2 . '"]'; ?>

        <?php echo do_shortcode($shortcode); ?>
    <?php endif; ?>
</div>

</div>