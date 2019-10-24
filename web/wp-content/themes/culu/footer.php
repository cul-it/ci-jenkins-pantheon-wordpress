<?php
/**
 * The template for displaying the footer.
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
            <a href="https://www.library.cornell.edu/libraries"><span class="fa fa-arrow-left"
                    aria-hidden="true"></span> Libraries and Hours</a> | <a
                href="https://www.library.cornell.edu/ask/email">Ask a Librarian</a>
        </div>

        <nav aria-label="footer navigation">
            <?php
        	wp_nav_menu( array(
        		'theme_location' => 'footer',
        		'menu_id'        => 'footer-menu'
        	) );
      	?>
        </nav>
    </div>

    <!-- Declare variables for theme options-->
    <!-- Use Kirki to handle customizer -->
    <?php
      $college_label = get_theme_mod( 'college_label', '' );
      $college_link = get_theme_mod( 'college_link', '' );
      $college_logo = get_theme_mod( 'college_logo', '' );
      $address_label = get_theme_mod( 'address_label', '' );
      $city_label = get_theme_mod( 'city_label', '' );
      $state_label = get_theme_mod( 'state_label', '' );
      $zip_label = get_theme_mod( 'zip_label', '' );
      $google_map_label = get_theme_mod( 'google_map_label', '' );
      $circulation_number_label = get_theme_mod( 'circulation_number_label', '' );
      $reference_number_label = get_theme_mod( 'reference_number_label', '' );
      $email_label = get_theme_mod( 'email_label', '' );
    ?>

    <?php if ( !empty( $college_link ) ){ ?>

    <figure class="college">

        <a href="<?php echo $college_link ?>">
            <img src="<?php echo get_domain_path( $college_logo );?>"
                alt="<?php echo $college_label ?> website homepage" />
        </a>

    </figure>

    <?php } ?>

    <address class="library">

        <div class="container">

            <div class="c-1">
                <p>CONTACT</p>
                <p><strong><?php echo get_bloginfo( 'name' ); ?></strong><br>

                    <?php if ( !empty( $address_label ) ){ ?>

                    <?php echo $address_label ?><br>
                    <?php echo $city_label ?>, <?php echo $state_label ?> <?php echo $zip_label ?>
                    <a class="icon-map" href="<?php echo $google_map_label ?>" title="Library Location"> <i
                            class="fas fa-map-marker-alt" aria-hidden="true"><span class="sr-only">Library
                                Location</span></i></a></p>

                <?php } ?>

                <?php if( is_active_sidebar( 'widget-social-media' ) ) : ?>

                <div class="widget-social-media">

                    <?php dynamic_sidebar( 'widget-social-media' ); ?>

                </div>

                <?php endif; ?>

            </div>

            <div class="c-2">

                <?php
            // Grabbing Kirki Repeater Field and Assigning Variable
            $settings = get_theme_mod( 'section_contact_email_phones_setting' );
            ?>

                <?php foreach( $settings as $setting ) : ?>

                <p><?php echo $setting['contact_title'] . ': ';

              $email = $setting['contact_value'];

              if ( checkEmail($email) ) {
                  echo '<a href="mailto:' . $email . '">' . $email . '</a></p>';
              } else {
                  echo $setting['contact_value'] . '</p>';
              }
              endforeach;
              ?>

            </div>
      </div>
    </address>

    <address class="cornell">

        <p><?php echo date('Y'); ?> Cornell University Library, Ithaca, NY 14853 | (607) 255-0000 | <a
                href="https://www.library.cornell.edu/privacy">Privacy</a> | <a
                href="https://www.library.cornell.edu/web-accessibility">Web Accessibility Assistance</a> | <a
                href="/admin">Staff Login</a></p>

    </address>

    <p class="feedback">

        <a class="btn-graphic"
            href="https://www.library.cornell.edu/feedback?destination=web-accessibility">FEEDBACK</a>
        <a class="btn-graphic" href="https://alumni.library.cornell.edu/content/give-library">GIVE TO THE LIBRARY <i
                class="fas fa-arrow-right" aria-hidden="true"></i></a>

    </p>

</footer>

<?php wp_footer(); ?>

</body>

</html>