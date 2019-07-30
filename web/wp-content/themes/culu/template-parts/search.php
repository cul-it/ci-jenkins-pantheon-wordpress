<!-- search -->
<form class="user-tool-search" role="search" aria-label="Search catalog and site" method="get" action="/">

  <div class="search-field">

    <label for="search">Search</label>
    <input type="search" id="search" aria-label="Search" value="<?php the_search_query(); ?>"/>

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
