/**
 * Search functionality for searching from search icon on user top nav
 */

 (function ($, root, undefined) {

	$(function () {
    var $selectedFilter = "catalog";
		// DOM ready, take it away
		$('.icon-search').click(function(e){
      $(' form.user-tool-search ').css( 'display', 'grid' );
			//console.log('click');
			e.preventDefault();
			$(' form.user-tool-search ').slideDown();
		});

		$(' .btn-close-search  ').click(function(e){
			e.preventDefault();
      $(' form.user-tool-search ').slideUp();
		});

	 $('input[name=search-type]').click(function(){
     $selectedFilter = $(this).val();
     console.log( $selectedFilter );
   });

	 $( '.user-tool-search' ).submit(function(e) {
			switch ($selectedFilter) {
						case 'catalog':
								$('input[type=search]').attr("name",'q');
								$(this).attr("action", 'https://newcatalog.library.cornell.edu/search?q=');
								break;

						case 'site':
								$('input[type=search]').attr("name",'s');
								//$(this).attr("action", 'http://hotel.library.cornell.edu/' + '?s=');
								$(this).attr("action", 'http:' + '?s=');
				}
		});

	});

})(jQuery, this);
