<?php
/**
 * The template for displaying all hours
 * Template Name: Hours
 * Template Post Type: post, page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-interior">

<?php

	while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/content', 'page' );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;

endwhile; // End of the loop.
?>
<?php $full_hours_label = get_theme_mod( 'full_hours_label', '' ); ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://api3.libcal.com/js/hours_grid.js?002"></script>
<div id="s-lc-whw<?php echo $full_hours_label ?>"></div>
<script>
$(function(){
var week<?php echo $full_hours_label ?> = new $.LibCalWeeklyGrid( $("#s-lc-whw<?php echo $full_hours_label ?>"), { iid: 973, lid: <?php echo $full_hours_label ?>,  weeks: 4, systemTime: false });
});
</script>

<!-- Please note: The following styles are optional, feel free to modify! //-->
<style>
.s-lc-whw thead { background-color: #F5F5F5; }
.s-lc-whw-head-date { color: #999; }
.s-lc-whw-today-h {  background-color: #ddd; }
.s-lc-whw-today { background-color: #F5F5F5; }
.s-lc-whw-bh { text-align: right; white-space: nowrap; }
.s-lc-whw-locname { font-weight: bold;}
.s-lc-whw-sublocname{ padding-left: 10px!important; }
.s-lc-whw-footnote { color: #555; font-size: 80%; }
.s-lc-whw-footnote td:hover { background-color:#fff!important;}
/* Below styles can be removed if you are already using Bootstap v3 in host page */
.s-lc-whw-cont {font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 12px;}
.s-lc-whw-pr, .s-lc-whw-ne{ padding: 5px 10px; font-size: 12px; line-height: 1.5; border-radius: 3px; color: #333; background-color: #fff; border-color: #ccc; display: inline-block; margin-bottom: 0; font-weight: 400; text-align: center; vertical-align: middle; cursor: pointer; background-image: none; border: 1px solid transparent; white-space: nowrap; }
.s-lc-whw-pr:disabled { background-color: #efefef; }
.s-lc-whw-ne:disabled { background-color: #efefef; }
.s-lc-whw { width: 100%; margin-bottom: 20px; max-width: 100%; background-color: transparent; border-bottom: none; border-left: none; border-collapse: collapse; border-spacing: 0; }
.s-lc-whw>tbody>tr>td { padding: 5px; }
.s-lc-whw>thead>tr>th { vertical-align: bottom; border-bottom: 2px solid #ddd; padding: 5px;}
.s-lc-whw th { border-top: none; border-bottom: none; border-right: none;}
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }
@media (max-width: 768px) {
   .s-lc-whw-cont .table-responsive { width: 100%; margin-bottom: 15px; overflow-y: hidden; overflow-x: scroll; -ms-overflow-style: -ms-autohiding-scrollbar; border: 1px solid #ddd; -webkit-overflow-scrolling: touch; }
   .s-lc-whw td { white-space: nowrap; }
}
</style>

</main><!-- #main -->

<?php

get_footer();
