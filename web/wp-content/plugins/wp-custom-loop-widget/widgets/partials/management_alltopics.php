<?php 
$tags = get_tags(array(
  'hide_empty' => true
));
foreach ($tags as $tag) {
  echo '<a href="' . home_url( '/?s=&search-type-home=site&fwp_categories=', 'https' ) . $tag->slug . '">' . $tag->name . '</a><br />';
}
?>