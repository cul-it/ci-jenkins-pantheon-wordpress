<?php

/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> aria-label="<?php the_title(); ?>">

    <header class="entry-header">

        <?php the_title('<h2 class="entry-title">', '</h2>'); ?>

    </header>

    <div class="entry-content">

        <?php
        the_content();

        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'culu'),
            'after'  => '</div>',
        ));
        ?>

    </div><!-- .entry-content -->

    <?php if (get_edit_post_link()) : ?>


        <footer class="entry-footer" aria-label="Footer content">

            <?php culu_edit_post(); ?>

        </footer><!-- .entry-footer -->

    <?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->