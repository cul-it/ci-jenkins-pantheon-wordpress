<?php if ( have_posts() ) :
  while ( have_posts() ) : the_post(); 
    $current_post_id = get_the_ID();
    ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.elementor-tab-title[data-tab="2"]').addClass('active elementor-active')
				jQuery('.elementor-tab-content[data-tab="2"]').addClass('active').attr('style','display: block');
			});
		</script>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'card mt-3r row' ); ?>>
      <div class="card-body">
		<div class="entry-header">
			<div>
			  <h3><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h3>
			</div>
		  </div>
		  <div class="search-metadata">
			<?php if (!empty(get_field('database_description'))) : ?>
			  <div class="row">
				<p class="col-12 col-sm-3 col-md-4"><strong>Description:</strong><br />
				<?php echo wp_trim_words( get_field('database_description' ), $num_words = 25, $more = '...' ); ?></p>
			  </div>
			<?php elseif (!empty(get_field('faq_description'))) : ?>
			  <div class="row">
				<p class="col-12 col-sm-3 col-md-4"><strong>Description:</strong><br />
				<?php echo wp_trim_words( get_field('faq_description' ), $num_words = 25, $more = '...' ); ?></p>
			  </div>
			<?php elseif (!empty(get_the_content())) : ?>
			  <div class="row">
				<p class="col-12 col-sm-3 col-md-4"><strong>Description:</strong><br />
				<?php echo wp_trim_words( get_the_content(), $num_words = 25, $more = '...' ); ?></p>
			  </div>
			<?php endif; ?>
			<?php $terms = get_the_tags($post->ID); if ( $terms ) : ?>
				<div class="row">
					<p class="col-12 col-sm-3 col-md-4"><strong>Topics:</strong> 
					<?php 
						$total = count($terms);
						$count = 1;
						foreach( $terms as $term ):
							echo '<a class="topic-tag" href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $term->slug . '">' . $term->name . '</a>';
							if ($count < $total) {
								echo ', ';
							}
							$count++;
						endforeach;
					?></p>
				</div>
			<?php endif; ?>
		</div>
      </div>
    </article>
  <?php endwhile;
else :
  echo 'no results found';
endif; ?>