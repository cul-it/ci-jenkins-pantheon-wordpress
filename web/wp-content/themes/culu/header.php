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

	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-60x60.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-120x120.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-76x76.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-152x152.png" />
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-196x196.png" sizes="196x196" />
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-16x16.png" sizes="16x16" />
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-128.png" sizes="128x128" />
	<meta name="application-name" content="&nbsp;"/>
	<meta name="msapplication-TileColor" content="#FFFFFF" />
	<meta name="msapplication-TileImage" content="mstile-144x144.png" />
	<meta name="msapplication-square70x70logo" content="mstile-70x70.png" />
	<meta name="msapplication-square150x150logo" content="mstile-150x150.png" />
	<meta name="msapplication-wide310x150logo" content="mstile-310x150.png" />
	<meta name="msapplication-square310x310logo" content="mstile-310x310.png" />

	<?php wp_head(); ?>

	<!-- Emergency Banner -->
	<script src="//embanner.univcomm.cornell.edu/OWC-emergency-banner.js" type="text/javascript"></script>
</head>

<body <?php body_class(); ?>>

	<a class="skip-link screen-reader-text" href="#main-content" aria-label="Skip to content"><?php esc_html_e( 'Skip to content', 'culu' ); ?></a>

	<!-- header -->
	<header class="branding" aria-label="Branding header">
		<!-- logo -->
		<div class="banding">

			<a class="logo-cul" href="https://www.library.cornell.edu/" title="Cornell University Library website"><img src="<?php echo get_template_directory_uri(); ?>/images/branding/cul-logo.svg" alt="Cornell University Library logo"></a>

			<!-- /logo -->
		</div>

		<!-- nav -->
		<nav class="user-tools" aria-label="My account and Search navigation">

			<ul>
				<!--<li><a href="#"><i class="fas fa-bars"></i></a></li>-->
				<li><a href="https://www.library.cornell.edu/myacct" title="My account"><span class="fa fa-user-o" aria-hidden="true" aria-label="My account"></span></a></li>
				<li><button class="icon-search" title="Search"><span class="fa fa-search" aria-hidden="true" aria-label="Search"></span></button></li>
			</ul>

		</nav>

  </header>

	<!-- search -->
		<form class="user-tool-search" role="search" aria-label="Search catalog and site" method="get" action="/">

			<div class="search-field">

				<label for="search">Search</label>
				<input type="search" id="search" value="" name="s" aria-label="Search" />

				<div class="search-filter" role="radiogroup" aria-label="search-filter" arial-label="Filter search">

					<input type="radio" name="search-type" id="catalog" aria-label="Catalog search" value="catalog" checked />
					<label for="catalog">Catalog</label>
					<input class="site-search" type="radio" name="search-type" id="site" aria-label="Site search" value="site" />
					<label for="site">This site</label>

				</div>
			</div>

			<button class="btn-submit" type="submit">Search</button>
			<button class="btn-close-search">Close</button>

		</form>

	<!-- header -->

	<!-- Declare variables for theme options-->
	<!-- Use Kirki to handle customizer -->
	<?php
		$image_hero_large = get_theme_mod( 'image_setting_url_hero_large', '' );
		$image_hero_medium = get_theme_mod( 'image_setting_url_hero_medium', '' );
		$image_hero_small = get_theme_mod( 'image_setting_url_hero_small', '' );
		$full_hours_label = get_theme_mod( 'full_hours_label', '' );
		$google_map_label = get_theme_mod( 'google_map_label', '' );

		$hero_top_color = get_theme_mod( 'hero_top_color', '#FFFFFF' );
		$hero_bottom_color = get_theme_mod( 'hero_bottom_color', '#FFFFFF' );

		//.home-header
	 ?>

	<style>

	.hero__content .all-libraries {
		background-color: <?php echo $hero_top_color; ?>;
	}

	.hero__content .home-header  {
		background-color: <?php echo $hero_bottom_color; ?>;
	}

	.hero__content.interior-pages .college {

			background-color: <?php echo $hero_top_color; ?>;

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

	.bg-header{

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

	<section class="<?php if ( is_front_page() ) { echo 'hero__content'; } else { echo 'hero__content interior-pages'; } ?>" aria-label="Hero header">

		<div class="all-libraries" role="region" aria-label="Main library site, all library hours, and contact links">
			<a href="https://www.library.cornell.edu/"><span class="fa fa-arrow-left" aria-hidden="true"></span> ALL LIBRARIES</a> | <a href="https://www.library.cornell.edu/libraries"><span> Hours</a></span> | <a href="https://www.library.cornell.edu/ask/email"><span>Ask a Librarian</span></a>
		</div>

		<div class="college">

			<?php

				$college_label = get_theme_mod( 'college_label', '' );
				$college_link = get_theme_mod( 'college_link', '' );
				if ( !empty( $college_label  ) ) { ?>

				<a href="<?php echo $college_link ?>"><?php echo $college_label ?></a>

			<?php } ?>

		</div>

		<div class="home-header" role="region" aria-label="Unit library header">


			<div class="subheader">

				<h1><a href="/"><?php echo get_bloginfo( 'name' ); ?></a></h1>

				<?php get_template_part( 'template-parts/library-hours' ); ?>

					<ul class="header-contact">

						<li><a href="https://www.library.cornell.edu/ask/email" title="Contact us"><span class="fa fa-envelope" aria-hidden="true" aria-label="Contact us"></span></a></li>

						<!--<li><a href=""><i class="fas fa-phone-square" aria-hidden title=""></i></a></li>-->

						<?php if ( !empty( $google_map_label  ) ) { ?>

						<li><a href="<?php echo $google_map_label ?>" title="Library location"><span class="fa fa-map-marker" aria-hidden="true" aria-label="Library Location"></span></a></li>

						<?php } ?>

					</ul>

			</div>


			<!-- search -->
			<form class="home-search" role="search" method="get" action="/" aria-label="Home search">

				<div class="search-field">

					<label for="search">Search</label>
					<input type="search-home" id="home-search" value="" name="s" aria-label="Filter search">

					<div class="search-filter" role="radiogroup" aria-label="search-filter" >

						<input type="radio" name="search-type-home" id="home-catalog" aria-label="Catalog search"  value="catalog" checked />
						<label for="catalog">Catalog</label>
						<input class="site-search" type="radio" name="search-type-home" id="home-site" aria-label="Site search" value="site" />
						<label for="site">This site</label>

					</div>
				</div>

				<button class="btn-submit" type="submit">Search</button>

			</form>



		</div>



	</section>


	<div class="bg-header"></div>



<!-- #site-navigation -->
<nav id="site-navigation" class="main-navigation" aria-label="main navigation">

	<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" title="Main Navigation" <?php //esc_html_e( 'Menu', 'culu' ); ?>><span class="fa fa-bars" aria-hidden="true"><span class="screen-reader-text">Main Navigation</span></span></button>
	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'menu_id'        => 'primary-menu'
	) );
	?>

</nav><!-- #site-navigation -->
