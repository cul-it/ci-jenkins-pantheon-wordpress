<!-- search -->
<form class="user-tool-search" role="search" aria-label="Search catalog and site" method="get" action="/">

    <div class="search-field">

        <label for="search">Search</label>
        <input type="text" id="search" aria-label="Search Overlay" value="<?php the_search_query(); ?>" />

        <fieldset class="search-filter">

            <legend class="sr-only">Filter Search</legend>

            <div class="search-library-resources"><input type="radio" name="search-type" value="catalog" id="catalog"
                    aria-label="Search Overlay Catalog Filter" checked />
                <label for="catalog">Library Resources</label></div>

            <div class="search-library-unit"><input class="site-search" type="radio" name="search-type" value="site"
                    id="site" aria-label="Search Overlay Site Filter" />
                <label for="site">This site</label></div>

        </fieldset>
    </div>

    <button class="btn-submit" type="submit">Search</button>
    <button class="btn-close-search">Close</button>

</form>