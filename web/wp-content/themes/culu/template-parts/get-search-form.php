<?php

/**
 * Template part for displaying search form when there is no search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package culu
 */

?>

<form class="no-search-results" role="search" method="get" aria-label="Search again using a different term" class="search-form" action="<?php echo home_url('/'); ?>">
    <label for="search-again"><span class="screen-reader-text">Search for:</span>
        <input id="search-again" aria-label="input another term" type="search" class="search-field" value="" name="s">
    </label>
    <button class="btn-submit" type="submit">Search</button>
</form>