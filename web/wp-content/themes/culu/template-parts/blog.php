<section class="blog" aria-label="Blog" >
  <header class="entry-header">
	<?php
	if ( is_singular() ) :
		the_title( '<h1 class="entry-title">', '</h1>' );
	else :
		the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
	endif;

  the_author();

	if ( 'post' === get_post_type() ) :
		?>
		<div class="entry-meta">
			<?php
			culu_posted_on();
			culu_posted_by();
			?>
		</div><!-- .entry-meta -->
	<?php endif; ?>
</header><!-- .entry-header -->

<?php culu_post_thumbnail(); ?>

<div class="entry-content">
	<?php
	the_content( sprintf(
		wp_kses(
			/* translators: %s: Name of current post. Only visible to screen readers */
			__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'culu' ),
			array(
				'span' => array(
					'class' => array(),
				),
			)
		),
		get_the_title()
	) );

	wp_link_pages( array(
		'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'culu' ),
		'after'  => '</div>',
	) );
	?>
</div><!-- .entry-content -->
</section>
