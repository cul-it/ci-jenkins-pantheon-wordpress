<?php $url = str_replace('/', '', $_SERVER['REQUEST_URI']); //Gets appropriate country by using url
if ($url == 'wason') :
  $country_url = array('china','japan','korea');
elseif ($url == 'echols') :
  $country_url = array('southeast-asia','general-southeast-asia','brunei','burma','cambodia','east-timor','indonesia','laos','malaysia','the-philippines','singapore','thailand','vietnam');
elseif ($url == 'south-asia') :
  $country_url = array('south-asia','bangladesh','butan','india','nepal','pakistan','sri-lanka');
else :
  $country_url = $url;
endif;

//Book Chapter Args
$book_chapters_args = array(
  'post_type' => 'book_chapters',
'category__in' => 115,
  'tax_query' => array(
    'relation' => 'AND',
    array(
      'taxonomy' => 'region',
      'field' => 'slug',
      'terms' => $country_url
    )
  ),
  'posts_per_page' => 3,
  'hide_empty' => true,
  //'meta_key' => 'subject',
  'orderby' => 'title',
  'order' => 'ASC',
  'facetwp' => true
);

$book_chapters = new WP_Query($book_chapters_args);

if ($book_chapters->have_posts()) : ?>
  <div class="database">
    <?php while( $book_chapters->have_posts() ) : $book_chapters->the_post(); ?>
      <?php include 'asia_bookchapters_single.php'; ?>
    <?php endwhile; ?>
    <a href="<?php echo home_url( '/book-chapters/?fwp_country=', 'https' ) . $country_url; ?>">See all <?php echo ucwords(str_replace("-", " ", $country_url)); ?> book chapters >></a>
  </div>
<?php else : ?>
  <p class="description">No <?php echo ucwords(str_replace("-", " ", $country_url)); ?> book chapters found - <a href="/book-chapters">see all book chapters</a>.
<?php endif; ?>