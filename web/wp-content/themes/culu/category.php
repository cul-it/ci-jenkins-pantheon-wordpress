<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-interior">

	<h3><?php //_e( '', 'culu' ); single_cat_title(); ?>Staff</h3>

	<?php

		$category = get_the_category();
		$theCategory = $category[0]->cat_name;


		if ( $theCategory == 'Staff Profile') {
			get_template_part('loop-staff');
			//echo $theCategory;

		}	else {
			get_template_part('loop');
		}
	?>

	<?php get_template_part('pagination'); ?>

</main><!-- #main -->

<?php

get_footer();

?>
