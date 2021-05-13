<?php

/**
 *
 * Featured news slider shortcode
 * @package culu
 *
 *
 */

function get_featured_news()
{
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => '5',
        'category_name' => 'news+featured-news',
        'order' => 'desc',
        'suppress_filters' => 0,
    );

    $featured_news = "";

    $query = new WP_Query($args);

    if ($query->have_posts()) :

        while ($query->have_posts()) : $query->the_post();

            $the_permalink = get_the_permalink();
            $the_title = get_the_title();
            $the_date =  get_the_date('F j, Y');
            $the_excerpt = get_the_excerpt();

            $post_thumbnail_id = get_post_thumbnail_id($post->ID);

            if (!empty($post_thumbnail_id)) {

                $img_ar =  wp_get_attachment_image_src($post_thumbnail_id, 'full');
                $img_alt = get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', TRUE);

                $featured_news .= <<<EOT
                <article class="slide-featured-news" aria-label="Featured news about $the_title">
                    <a href="$the_permalink" title="$the_title"><img class='fearuted-news-photo' src="$img_ar[0]" alt="$img_alt"/></a>
                
                    <header>
                        <h1><a href="$the_permalink">$the_title</a></h1>
                        <time datetime="$the_date">$the_date</time>
                        </header>
                        <p>$the_excerpt</p>
                    <footer>
                        <p class="full-story"><a href="$the_permalink">Read full story »</a></p>
                        <p class="all-news"><a href="/category/news/">All news »</a></p>
                    </footer>
                </article>
                EOT;
            }

        endwhile;

        wp_reset_postdata();

    endif;

    $featured_news_wrapper = <<<EOT
            <h2>Featured News</h2>
            <div class="hero-slider">
                $featured_news
            </div>
    EOT;

    return $featured_news_wrapper;
}

add_shortcode('featured_news', 'get_featured_news');


// An attachment/image ID is all that's needed to retrieve its alt and title attributes.
$image_id = get_post_thumbnail_id();

$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
