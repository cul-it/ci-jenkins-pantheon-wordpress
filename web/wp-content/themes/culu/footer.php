<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package culu
 */

?>

<footer>

    <div class="footer__nav">

      <div class="all-libraries">
        <a href="https://www.library.cornell.edu/"><span class="fas fa-arrow-left" role="presentation"></span> ALL LIBRARIES</a> | <a href="#">Cornell Library Hours</a> | <a href="https://www.library.cornell.edu/ask/email">Ask a Librarian</a>
      </div>


      <!--<p class="unit-library"><a href="/"><?php echo get_bloginfo( 'name' ); ?></a></p>-->

    	<?php
      	wp_nav_menu( array(
      		'theme_location' => 'footer',
      		'menu_id'        => 'footer-menu'
      	) );
    	?>

    </div>

    <figure class="college">

      <?php $college_label = get_theme_mod( 'college_label', '' ); ?>
      <?php $college_link = get_theme_mod( 'college_link', '' );?>
    	<?php $college_logo = get_theme_mod( 'college_logo', '' ); ?>

      <a href="<?php echo $college_link ?>">
        <img src="<?php echo get_domain_path( $college_logo );?>" alt="<?php echo $college_label ?> logo"/>
      </a>
      <figcaption></figcaption>

    </figure>

    <address class="library">

        <div class="container">

          <div class="c-1">
          <p><strong>CONTACT</strong></p>
          <p><strong>Unit Library</strong><br>
            G80 Statler Hall,<br>
            Cornell University<br>
            Ithaca, NY 14853
          <span class="fas fa-map-marker-alt" role="presentation" labelledby="Location"></span></p>
          </div>

        <div class="c-2">
         <p><strong>Phone: </strong><a href="tel:#">(607) 255-0000</a></p>

         <p><strong>E-mail: </strong><a href="mailto:#">unit@cornell.edu</a></p>
         <p><strong>Reference Help: </strong><a href="mailto:#">unit@cornell.edu</a></p>
        </div>

      </div>

    </address>

    <address class="cornell">

      <p><?php echo date('Y'); ?> Cornell University Library, Ithaca, NY 14853 | <a href="tel:">(607) 255-0000</a> | <a href="https://www.library.cornell.edu/privacy">Privacy</a> | <a href="https://www.library.cornell.edu/web-accessibility">Web Accessibility Assistance</a> | <a href="/admin">Staff Login</a></p>

    </address>

    <p class="feedback">

      <a class="btn-graphic" href="https://www.library.cornell.edu/feedback?destination=web-accessibility">FEEDBACK</a>
      <a class="btn-graphic" href="https://alumni.library.cornell.edu/content/give-library">GIVE TO THE LIBRARY <span class="fas fa-arrow-right" role="presentation"></span></a>

    </p>

  </footer>

<?php wp_footer(); ?>

</body>
</html>
