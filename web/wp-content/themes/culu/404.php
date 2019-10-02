<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-interior">

	<section class="four04">

		<!--<figure class="four04_photo">

			<img src="<?php echo get_template_directory_uri(); ?>/images/404/404.png" alt=" ">

		</figure>-->

		<div class="four04_message">

			<h1>This page doesn't exist</h1>

			<p>Here are some helpful links to lead
			you in the right direction:</p>

			<ul>
				<li><a href="<?php echo home_url(); ?>"><?php _e( 'Home', 'html5blank' ); ?></a></li>
				<li><a href="https://newcatalog.library.cornell.edu/search?q="><?php _e( 'Search Catalog', 'html5blank' ); ?></a></li>
				<li><a href="https://www.library.cornell.edu/ask/email"><?php _e( 'Contact Us', 'html5blank' ); ?></a></li>
			</ul>

		</div>

	</section>

</main>

<?php
get_footer();
?>
