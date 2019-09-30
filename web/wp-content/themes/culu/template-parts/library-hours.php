<?php
/**
 * emplate part for displaying hours status content in header.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<div class="available-time">

  <i class="fas fa-clock icon-time" aria-hidden="true"></i>
  <span class="libcal-status-now"><?php echo do_shortcode('[libcal_status_now]') ?></span>
  <span class="libcal-hours-today"> <?php echo do_shortcode('[libcal_hours_today]') ?> </span>
  - <a class="full-hours" href="/full-hours">Full Hours</a> /

</div>
