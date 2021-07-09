<?php

/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package culu
 */

get_header();
?>

<main id="main-content" class="page-interior">

    <?php if (have_posts()) : ?>

        <header class="page-header" aria-label="title-content">

            <h2 id="title-content" class="page-title">

                <?php
                /* translators: %s: search query. */
                printf(esc_html__('Search Results for: %s', 'culu'), '<span>' . get_search_query() . '</span>');
                ?>
            </h2>

        </header><!-- .page-header -->

        <?php
        /* Start the Loop */
        while (have_posts()) :
            the_post();

            /**
             * Run the loop for the search to output the results.
             * If you want to overload this in a child theme then include a file
             * called content-search.php and that will be used instead.
             */
            get_template_part('template-parts/content', 'search');

        endwhile; ?>

        <div class="pagination">

            <?php culu_pagination(); ?>

        </div>

    <?php else :

        get_template_part('template-parts/content', 'none');

    endif;
    ?>

</main><!-- #main -->

<?php

//get_sidebar();
get_footer();
