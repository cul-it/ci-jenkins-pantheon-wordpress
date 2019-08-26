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
