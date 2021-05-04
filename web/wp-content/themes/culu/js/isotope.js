/**
 * Search functionality for searching from search icon on user top nav and homepage search.
 */

(function ($, root, undefined) {
    $(function () {
        var $grid = $(".staff-page .elementor-widget-container").isotope({
            itemSelector: ".staff-profile",
            masonry: {
                columnWidth: 100,
                horizontalOrder: true,
            },
        });

        $grid.imagesLoaded().progress(function () {
            $grid.isotope("layout");
        });
    });
})(jQuery, this);
