<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and header content
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package culu
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <link rel="apple-touch-icon-precomposed" sizes="57x57"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-196x196.png" sizes="196x196" />
    <link rel="icon" type="image/png"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png"
        href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-128.png"
        sizes="128x128" />
    <meta name="application-name" content="&nbsp;" />
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="mstile-144x144.png" />
    <meta name="msapplication-square70x70logo" content="mstile-70x70.png" />
    <meta name="msapplication-square150x150logo" content="mstile-150x150.png" />
    <meta name="msapplication-wide310x150logo" content="mstile-310x150.png" />
    <meta name="msapplication-square310x310logo" content="mstile-310x310.png" />

    <?php wp_head(); ?>

    <script src="https://kit.fontawesome.com/8ec932d54d.js"></script>

    <!-- Emergency Banner -->
    <script src="//embanner.univcomm.cornell.edu/OWC-emergency-banner.js" type="text/javascript"></script>

</head>

<body <?php body_class(); ?>>

    <nav aria-label="Skip to content"><a class="skip-link screen-reader-text"
            href="#main-content"><?php esc_html_e( 'Skip to content', 'culu' ); ?></a></nav>

    <!-- header -->
    <header class="branding">
        <!-- logo -->
        <div class="banding">

            <a class="logo-cul" href="https://www.library.cornell.edu/"
                title="Cornell University Library website homepage"><img
                    src="<?php echo get_template_directory_uri(); ?>/images/branding/cul-logo.svg"
                    alt="Cornell University Library site"></a>

            <!-- /logo -->
        </div>

        <!-- nav -->
        <nav class="user-tools" aria-label="My account and Search menu">

            <ul>
                <!--<li><a href="#"><i class="fas fa-bars"></i></a></li>-->
                <li><a href="https://www.library.cornell.edu/myacct" title="My account"><i class="fas fa-user"
                            aria-hidden="true"><span class="sr-only">My account</span></i></a></li>
                <li><button class="icon-search" title="Search"><i class="fas fa-search" aria-hidden="true"><span
                                class="sr-only">Search site and Catalog</span></i></button></li>
            </ul>

        </nav>

    </header>

    <?php get_template_part('template-parts/search'); ?>

    <!-- header -->

    <!-- Declare variables for theme options-->
    <!-- Use Kirki to handle customizer -->
    <?php
		$image_hero_large = get_theme_mod( 'image_setting_url_hero_large', '' );
		$image_hero_medium = get_theme_mod( 'image_setting_url_hero_medium', '' );
		$image_hero_small = get_theme_mod( 'image_setting_url_hero_small', '' );
		$full_hours_label = get_theme_mod( 'full_hours_label', '' );
		$google_map_label = get_theme_mod( 'google_map_label', '' );
		$email_label = get_theme_mod( 'email_label', '' );

		$hero_top_color = get_theme_mod( 'hero_top_color', '#FFFFFF' );
		$hero_bottom_color = get_theme_mod( 'hero_bottom_color', '#FFFFFF' );

		//.home-header
	 ?>

    <style>
    .hero__content .all-libraries {

        background-color: <?php echo $hero_top_color;
        ?>;

    }

    .hero__content .home-header {

        background-color: <?php echo $hero_bottom_color;
        ?>;

    }

    .hero__content.interior-pages .college {

        background-color: <?php echo $hero_top_color;
        ?>;

    }

    .bg-header {

        background: url('<?php echo get_domain_path( $image_hero_small );?>') no-repeat center -50px;

    }

    @media only screen and (min-width: 640px) {

        .bg-header {

            background: url('<?php echo get_domain_path( $image_hero_medium );?>') no-repeat -600px -100px;

        }

    }

    @media only screen and (min-width: 768px) {

        .bg-header {

            background: url('<?php echo get_domain_path( $image_hero_medium );?>') no-repeat center center;

        }

    }

    @media only screen and (min-width: 1440px) {

        .bg-header {

            background: url('<?php echo get_domain_path( $image_hero_large );?>') no-repeat center center;
        }

    }

    @media only screen and (min-width: 1600px) {

        .bg-header {

            background: url('<?php echo get_domain_path( $image_hero_large );?>') no-repeat center center;
            background-size: cover;

        }

    }
    </style>

    <section
        class="<?php if ( is_front_page() ) { echo 'hero__content'; } else { echo 'hero__content interior-pages'; } ?>"
        aria-label="Hero header">

        <div class="all-libraries" role="region" aria-label="All library websites and hours, and ask a librarian links">

            <a href="https://www.library.cornell.edu/libraries"><span><i class="fas fa-arrow-left"
                        aria-hidden="true"></i> Libraries and Hours</span></a><a
                href="https://www.library.cornell.edu/ask/email"><span><i class="far fa-comment-alt"
                        aria-hidden="true"></i> Ask a Librarian</span></a>

        </div>

        <div class="college" role="region" aria-label="College site">

            <?php
				/*
				$college_label = get_theme_mod( 'college_label', '' );
				$college_link = get_theme_mod( 'college_link', '' );
				if ( !empty( $college_label  ) ) { ?>

            <a href="<?php echo $college_link ?>"><?php echo $college_label ?></a>

            <?php } */?>

        </div>

        <div class="home-header" role="region" aria-label="Unit library header">

            <div class="subheader">

                <h1><a href="/"><?php echo ( get_bloginfo( 'name' ) ); ?></a><?php get_template_part('template-parts/college-logo-header'); ?></h1>

                <?php get_template_part( 'template-parts/library-hours' ); ?>

                <ul class="header-contact">

                    <li>
                        <a href="<?php if (filter_var($email_label, FILTER_VALIDATE_EMAIL)) : ?>mailto:<?php echo $email_label ?><?php else : ?><?php echo $email_label ?><?php endif; ?>"
                            title="Contact us"><i class="fas fa-envelope" aria-hidden="true"><span
                                    class="sr-only">Contact us</span></i></a>
                    </li>

                    <!--<li><a href=""><i class="fas fa-phone-square" aria-hidden title=""></i></a></li>-->

                    <?php if ( !empty( $google_map_label  ) ) { ?>

                    <li><a href="<?php echo $google_map_label ?>" title="Library location"><i
                                class="fas fa-map-marker-alt" aria-hidden="true"><span class="sr-only">Library
                                    Location</span></i></a></li>

                    <?php } ?>

                </ul>

            </div>

            <?php get_template_part('template-parts/search-home-hero'); ?>

        </div>

    </section>

    <div class="bg-header"></div>

    <!-- #site-navigation -->

    <nav id="site-navigation" class="main-navigation" aria-label="Main navigation">

        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" title="Main Navigation"
            <?php //esc_html_e( 'Menu', 'culu' ); ?>><i class="fas fa-bars" aria-hidden="true"><span
                    class="screen-reader-text">Main Navigation</span></i></button>
        <?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_id'        => 'primary-menu'
		) );
		?>

    </nav><!-- #site-navigation -->