/**
 * Search functionality for searching from search icon on user top nav and homepage search.
 */

(function ($, root, undefined) {

	$(function () {

		var $selectedFilter  = $('input[name=search-type]:checked ').val();

		// DOM ready, take it away
		$('.icon-search').click(function (e) {

			$(' form.user-tool-search ').css('display', 'grid');
			e.preventDefault();
			$(' form.user-tool-search ').slideDown();
		});

		$(' .btn-close-search  ').click(function (e) {
			e.preventDefault();
			$(' form.user-tool-search ').slideUp();
		});

		$('input[name=search-type]').click(function () {
			$selectedFilter = $(this).val();
		});

		$('.user-tool-search').submit(function (e) {
			switch ($selectedFilter) {
				case 'catalog':
					$('input[type=text]').attr("name", 'q');
					$(this).attr("action", 'https://newcatalog.library.cornell.edu/search?q=');
					break;
				case 'site':
					$('input[type=text]').attr("name", 's');
					$(this).attr("action", '/?s=');
			}
		});


		// Search on homepage 

		$selectedFilterHome = $(' .home-search input[name=search-type-home]:checked ').val();

		$(' .home-search input[name=search-type-home] ').click(function () {
			$selectedFilterHome = $(this).val();
		});

		$('.home-search ').submit(function (e) {
			switch ($selectedFilterHome) {
				case 'catalog':
					$('input[type=text]').attr("name", 'q');
					$(this).attr("action", 'https://newcatalog.library.cornell.edu/search?q=');
					break;
				case 'site':
					$('input[type=text]').attr("name", 's');
					//$(this).attr("action", 'http://hotel.library.cornell.edu/' + '?s=');
					$(this).attr("action", '/?s=');
			}
		});

	});

})(jQuery, this);