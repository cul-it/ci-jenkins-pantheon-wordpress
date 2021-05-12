<?php

/**
 * Template part for displaying search form on homepage
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<form class="home-search" role="search" method="get" action="/" aria-label="Home search">

    <div class="search-field">

        <label for="home-search">Search</label>
        <input type="text" id="home-search" aria-label="Home Search" value="<?php the_search_query(); ?>" name="s" />


        <fieldset class="search-filter">

            <legend class="sr-only">Filter Search</legend>

            <div class="search-library-resources"><input type="radio" name="search-type-home" value="catalog" id="home-catalog" aria-label="Search Homepage Catalog Filter" checked />
                <label for="home-catalog">Library Resources</label>
            </div>
            <div class="search-library-unit"><input class="site-search" type="radio" name="search-type-home" value="site" aria-label="Searh Homepage Site Filter" id="search-type-home" />
                <label for="search-type-home">This site</label>

            </div>
        </fieldset>

    </div>

    <button class="btn-submit" type="submit">Search</button>

    <div></div>

</form>