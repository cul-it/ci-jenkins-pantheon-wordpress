<?php
/**
 * Template part for displaying staff profile posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header" aria-label="Title content">

		<?php

		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>

		<section class="staff-profile" aria-label="Staff profile">

			<?php

			$image = get_field('photo');

			if( !empty($image) ) { ?>

				<img class="staff-photo" src="<?php echo $image['url'];?>" alt="<?php echo $image['alt']; ?>">

			<?php } else { ?>

				<img class="staff-photo" src="<?php echo get_template_directory_uri(); ?>/images/staff/no-photo-profile.png">

			<?php } ?>

			<h2><?php echo the_field('first_name');?>
					<?php echo the_field('last_name');

					if ( !empty(get_field('degree')) ) {
						echo ', ';
						echo the_field('degree');
					}
					?>
			</h2>

			<h3><?php echo the_field('title'); ?></h3>
			<p><a href="mailto:<?php echo the_field('email');?>"><?php echo the_field('email');?></a></p>
			<p class="staff-phone">Phone: <?php the_field('phone');?></p>

			<?php if ( !empty(get_field('consultation')) ) { ?>
				<script>
					jQuery.getScript("https://api3.libcal.com/js/myscheduler.min.js", function() {
							jQuery("#<?php echo the_field('consultation'); ?>").LibCalMySched({iid: 973, lid: 0, gid: 0, uid: 18275, width: 560, height: 680, title: 'Make an Appointment', domain: 'https://api3.libcal.com'});
					});
				</script>
				<?php } ?>

				<?php
						if ( !empty(get_field('consultation')) ) {
				?>

				<p>
					<a href="#" id="<?php echo the_field('consultation');?>" class="btn-graphic">Book a Consultation</a>
				</p>

			<?php } ?>

			</div>

		</section>

		<?php if ( 'post' === get_post_type() ) : ?>

			<div class="entry-meta">

				<?php
				//culu_posted_on();
				//culu_posted_by();
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

	<footer class="entry-footer" aria-label="Footer content">

		<?php culu_edit_post(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
