<?php

while ( have_posts() ) :

	the_post();

	?>

	<section class="staff-profile" aria-label="Staff profile" >

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

			<p>
				<a href="#" id="<?php echo the_field('consultation'); ?>" class="btn-graphic">
					Book a Consultation
				</a>
			</p>

		<?php } ?>

		</div>

	</section>

	<?php

	edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Edit <span class="screen-reader-text">%s</span>', 'culu' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);

endwhile; // End of the loop.

//the_post_navigation();

?>
