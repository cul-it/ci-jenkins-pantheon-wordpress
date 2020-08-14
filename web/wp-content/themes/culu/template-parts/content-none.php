<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<section class="no-results not-found">

	<header class="page-header" aria-labelledby="no-content">

		<h2 id="no-content" class="page-title"><?php esc_html_e( 'Nothing Found', 'culu' ); ?></h2>

	</header>

	<div class="page-content">

		<?php

		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'culu' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'culu' ); ?></p>
			<?php
			//get_search_form();
			get_template_part( 'template-parts/get-search-form' ); 

		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'culu' ); ?></p>
			<?php
			//get_search_form();
			get_template_part( 'template-parts/get-search-form' ); 
		endif;
		?>

	</div><!-- .page-content -->

</section><!-- .no-results -->
