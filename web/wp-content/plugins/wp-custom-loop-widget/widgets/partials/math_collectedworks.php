<?php $collected_works_args = array(
    'post_type' => 'collected_works',
    'posts_per_page' => 20,
    'hide_empty' => true,
    'meta_key' => 'author',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true
  );
  $collected_works = new WP_Query($collected_works_args);

  if ($collected_works->have_posts()) : ?>
    <div>
      <table class="table table-striped features">
        <thead>
          <tr class="d-flex">
            <th class="title" scope="col">Title</th>
            <th class="location" scope="col">Publication Year(s)</th>
            <th class="holdings" scope="col">Cornell Catalog</th>
            <th class="oclc" scope="col">WorldCat</th>
          </tr>
        </thead>
        <tbody>
          <?php while ( $collected_works->have_posts() ) : $collected_works->the_post(); ?>
            <tr class="group d-flex" scope="row" aria-label="Collected Works: <?php echo the_field('title'); ?><?php if( !empty(get_field('author'))) : ?> by <?php echo the_field('author'); ?><?php endif; ?>">
              <td class="title">
                <?php if( !empty(get_field('title'))) : ?>
                  <strong><?php echo the_field('title'); ?></strong>
                <?php endif; ?>
                <?php if( !empty(get_field('author'))) : ?><br />
                 <?php echo the_field('author'); ?><?php if( !empty(get_field('birth_year'))) : ?> (<?php echo the_field('birth_year'); ?><?php if( !empty(get_field('death_year'))) : ?> - <?php echo the_field('death_year'); ?><?php endif; ?>)<?php endif; ?>
                <?php endif; ?>
              </td>
              <td class="publisher">
                <?php if( !empty(get_field('date_range'))) : ?><?php echo the_field('date_range'); ?><?php endif; ?>
              </td>
              <td class="holdings">
                <?php if( !empty(get_field('cornell_holdings')) && !empty(get_field('bibid'))) : ?>
                  <a href="https://newcatalog.library.cornell.edu/catalog/<?php echo the_field('bibid'); ?>"><?php echo the_field('cornell_holdings'); ?></a>
                <?php endif; ?>
              </td>
              <td class="oclc">
                <?php if( !empty(get_field('oclc'))) : ?>
                  <a href="https://cornell.on.worldcat.org/oclc/<?php echo the_field('oclc'); ?>"><?php echo the_field('oclc'); ?></a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>