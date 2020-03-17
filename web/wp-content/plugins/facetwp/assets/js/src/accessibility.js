(function($) {
    var last_checked = null;

    $(document).on('facetwp-loaded', function() {
        $('.facetwp-checkbox, .facetwp-radio').each(function() {
            $(this).attr('role', 'checkbox');
            $(this).attr('aria-checked', $(this).hasClass('checked') ? 'true' : 'false');
            $(this).attr('tabindex', 0);
        });

        $('.facetwp-page, .facetwp-toggle, .facetwp-selection-value').each(function() {
            $(this).attr('role', 'link');
            $(this).attr('tabindex', 0);
        });

        if ( null != last_checked ) {
            $('.facetwp-facet [data-value="' + last_checked + '"]').focus();
            last_checked = null;
        }
    });

    $(document).on('keydown', '.facetwp-checkbox, .facetwp-radio', function(e) {
        var keyCode = e.originalEvent.keyCode;
        if ( 32 == keyCode ) {
            last_checked = $(this).attr('data-value');
            e.preventDefault();
            $(this).click();
        }
    });

    $(document).on('keydown', '.facetwp-page, .facetwp-toggle, .facetwp-selection-value', function(e) {
        var keyCode = e.originalEvent.keyCode;
        if ( 13 == keyCode ) {
            e.preventDefault();
            $(this).click();
        }
    });
})(jQuery);