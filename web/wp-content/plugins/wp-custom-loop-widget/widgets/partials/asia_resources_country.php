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
$bookchpters_args = array(
    'post_type' => 'resources',
    'category__in' => 115,
    'tax_query' => array(
        array(
            'taxonomy' => 'region',
            'field' => 'slug',
            'terms' => $country_url
        )
    ),
    'posts_per_page' => 3,
    'hide_empty' => true,
    'meta_key' => 'title',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'facetwp' => true
);

$bookchpters = new WP_Query($bookchpters_args);
if ($bookchpters->have_posts()) : ?>
    <div class="intro">
        <?php while( $bookchpters->have_posts() ) : $bookchpters->the_post(); ?>
          <?php include 'asia_resources_single.php'; ?>
        <?php endwhile; ?>
        <?php if (is_array($country_url)) : ?>
            <a href="<?php echo home_url( '/resources', 'https' ); ?>">See all resources >></a>
        <?php else : ?>
            <a href="<?php echo home_url( '/resources/?fwp_country=', 'https' ) . $country_url; ?>">See all <?php echo ucwords(str_replace("-", " ", $country_url)); ?> resources >></a>
        <?php endif; ?>
    </div>
<?php else : ?>
    <p class="description">No <?php echo ucwords(str_replace("-", " ", $url)); ?> resources found - <a href="/resources">see all resources</a>.
<?php endif; ?>