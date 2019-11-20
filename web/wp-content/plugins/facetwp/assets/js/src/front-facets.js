(function($) {

    FWP.logic = FWP.logic || {};

    /* ======== IE11 .val() fix ======== */

    $.fn.pVal = function() {
        var val = $(this).eq(0).val();
        return val === $(this).attr('placeholder') ? '' : val;
    }

    /* ======== Autocomplete ======== */

    var ac_selected = false;

    FWP.hooks.addAction('facetwp/refresh/autocomplete', function($this, facet_name) {
        var val = $this.find('.facetwp-autocomplete').val() || '';
        FWP.facets[facet_name] = val;
    });

    $(document).on('facetwp-loaded', function() {
        $('.facetwp-autocomplete:not(.ready)').each(function() {
            var $this = $(this);
            var $parent = $this.closest('.facetwp-facet');
            var facet_name = $parent.attr('data-name');

            // ignore the current facet's selections
            var post_data = FWP.build_post_data();
            var facet_values = JSON.parse(JSON.stringify(FWP.facets)); // clone
            facet_values[facet_name] = ''; // clear value
            post_data.facets = JSON.stringify(facet_values);

            // initialize
            var opts = FWP.hooks.applyFilters('facetwp/set_options/autocomplete', {
                serviceUrl: ('wp' === FWP.template) ? document.URL : FWP_JSON.ajaxurl,
                type: 'POST',
                minChars: 3,
                deferRequestBy: 200,
                showNoSuggestionNotice: true,
                triggerSelectOnValidInput: false,
                noSuggestionNotice: FWP_JSON['no_results'],
                onSelect: function() {
                    ac_selected = true;
                    FWP.autoload();
                },
                params: {
                    action: 'facetwp_autocomplete_load',
                    facet_name: facet_name,
                    data: post_data
                }
            }, { 'facet_name': facet_name });
            $this.autocomplete(opts);
            $this.addClass('ready');
        });
    });

    $(document).on('keyup', '.facetwp-autocomplete', function(e) {
        if (13 === e.which && ! ac_selected) {
            FWP.autoload();
        }
        if (ac_selected) {
            ac_selected = false;
        }
    });

    $(document).on('click', '.facetwp-autocomplete-update', function() {
        FWP.autoload();
    });

    /* ======== Checkboxes ======== */

    FWP.hooks.addAction('facetwp/refresh/checkboxes', function($this, facet_name) {
        var selected_values = [];
        $this.find('.facetwp-checkbox.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        FWP.facets[facet_name] = selected_values;
    });

    FWP.hooks.addFilter('facetwp/selections/checkboxes', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            var choice = params.el.find('.facetwp-checkbox[data-value="' + val + '"]').clone();
            choice.find('.facetwp-counter').remove();
            choice.find('.facetwp-expand').remove();
            choices.push({
                value: val,
                label: choice.text()
            });
        });
        return choices;
    });

    $(document).on('click', '.facetwp-type-checkboxes .facetwp-expand', function(e) {
        var $wrap = $(this).parent('.facetwp-checkbox').next('.facetwp-depth');
        $wrap.toggleClass('visible');
        var content = $wrap.hasClass('visible') ? FWP_JSON['collapse'] : FWP_JSON['expand'];
        $(this).html(content);
        e.stopPropagation();
    });

    $(document).on('click', '.facetwp-type-checkboxes .facetwp-checkbox:not(.disabled)', function() {
        $(this).toggleClass('checked');
        FWP.autoload();
    });

    $(document).on('click', '.facetwp-type-checkboxes .facetwp-toggle', function() {
        var $parent = $(this).closest('.facetwp-facet');
        $parent.find('.facetwp-toggle').toggleClass('facetwp-hidden');
        $parent.find('.facetwp-overflow').toggleClass('facetwp-hidden');
    });

    $(document).on('facetwp-loaded', function() {
        $('.facetwp-type-checkboxes .facetwp-overflow').each(function() {
            var num = $(this).find('.facetwp-checkbox').length;
            var $el = $(this).siblings('.facetwp-toggle:first');
            $el.text($el.text().replace('{num}', num));
        });

        // are children visible?
        $('.facetwp-type-checkboxes').each(function() {
            var $facet = $(this);
            var name = $facet.attr('data-name');

            // error handling
            if (Object.keys(FWP.settings).length < 1) {
                return;
            }

            // hierarchy toggles
            if ('yes' === FWP.settings[name]['show_expanded']) {
                $facet.find('.facetwp-depth').addClass('visible');
            }

            if (1 > $facet.find('.facetwp-expand').length) {
                $facet.find('.facetwp-depth').each(function() {
                    var which = $(this).hasClass('visible') ? 'collapse' : 'expand';
                    $(this).prev('.facetwp-checkbox').append(' <span class="facetwp-expand">' + FWP_JSON[which] + '</span>');
                });

                // un-hide groups with selected items
                $facet.find('.facetwp-checkbox.checked').each(function() {
                    $(this).parents('.facetwp-depth').each(function() {
                        $(this).prev('.facetwp-checkbox').find('.facetwp-expand').html(FWP_JSON['collapse']);
                        $(this).addClass('visible');
                    });

                    // show children of selected items
                    $(this).find('.facetwp-expand').trigger('click');
                });
            }
        });
    });

    /* ======== Radio ======== */

    FWP.hooks.addAction('facetwp/refresh/radio', function($this, facet_name) {
        var selected_values = [];
        $this.find('.facetwp-radio.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        FWP.facets[facet_name] = selected_values;
    });

    FWP.hooks.addFilter('facetwp/selections/radio', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            var choice = params.el.find('.facetwp-radio[data-value="' + val + '"]').clone();
            choice.find('.facetwp-counter').remove();
            choices.push({
                value: val,
                label: choice.text()
            });
        });
        return choices;
    });

    $(document).on('click', '.facetwp-type-radio .facetwp-radio:not(.disabled)', function() {
        var is_checked = $(this).hasClass('checked');
        $(this).closest('.facetwp-facet').find('.facetwp-radio').removeClass('checked');
        if (! is_checked) {
            $(this).addClass('checked');
        }
        FWP.autoload();
    });

    /* ======== Date Range ======== */

    FWP.hooks.addAction('facetwp/refresh/date_range', function($this, facet_name) {
        var min = $this.find('.facetwp-date-min').pVal() || '';
        var max = $this.find('.facetwp-date-max').pVal() || '';
        FWP.facets[facet_name] = ('' !== min || '' !== max) ? [min, max] : [];
    });

    FWP.hooks.addFilter('facetwp/selections/date_range', function(output, params) {
        var vals = params.selected_values;
        var $el = params.el;
        var out = '';

        if ('' !== vals[0]) {
            out += ' ' + FWP_JSON.datepicker.fromText + ' ' + $el.find('.facetwp-date-min').next().val();
        }
        if ('' !== vals[1]) {
            out += ' ' + FWP_JSON.datepicker.toText + ' ' + $el.find('.facetwp-date-max').next().val();
        }
        return out;
    });

    $(document).on('facetwp-loaded', function() {
        var $dates = $('.facetwp-type-date_range .facetwp-date:not(".ready, .flatpickr-alt")');

        if (0 === $dates.length) {
            return;
        }

        var flatpickr_opts = {
            altInput: true,
            altInputClass: 'flatpickr-alt',
            altFormat: 'Y-m-d',
            disableMobile: true,
            locale: FWP_JSON.datepicker.locale,
            onChange: function() {
                FWP.autoload();
            },
            onReady: function(dateObj, dateStr, instance) {
                var clearBtn = '<div class="flatpickr-clear">' + FWP_JSON.datepicker.clearText + '</div>';
                $(clearBtn).on('click', function() {
                        instance.clear();
                        instance.close();
                })
                .appendTo($(instance.calendarContainer));
            }
        };

        $dates.each(function() {
            var $this = $(this);
            var facet_name = $this.closest('.facetwp-facet').attr('data-name');
            flatpickr_opts.altFormat = FWP.settings[facet_name].format;

            var opts = FWP.hooks.applyFilters('facetwp/set_options/date_range', flatpickr_opts, {
                'facet_name': facet_name,
                'element': $this
            });
            new flatpickr(this, opts);
            $this.addClass('ready');
        });
    });

    /* ======== Dropdown ======== */

    FWP.hooks.addAction('facetwp/refresh/dropdown', function($this, facet_name) {
        var val = $this.find('.facetwp-dropdown').val();
        FWP.facets[facet_name] = val ? [val] : [];
    });

    FWP.hooks.addFilter('facetwp/selections/dropdown', function(output, params) {
        return params.el.find('.facetwp-dropdown option:selected').text();
    });

    $(document).on('change', '.facetwp-type-dropdown select', function() {
        var $facet = $(this).closest('.facetwp-facet');
        var facet_name = $facet.attr('data-name');

        if ('' !== $facet.find(':selected').val()) {
            FWP.frozen_facets[facet_name] = 'soft';
        }
        FWP.autoload();
    });

    /* ======== fSelect ======== */

    FWP.hooks.addAction('facetwp/refresh/fselect', function($this, facet_name) {
        var val = $this.find('select').val();
        if (null === val || '' === val) {
            val = [];
        }
        else if (false === $.isArray(val)) {
            val = [val];
        }
        FWP.facets[facet_name] = val;
    });

    FWP.hooks.addFilter('facetwp/selections/fselect', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            var choice = params.el.find('.facetwp-dropdown option[value="' + val + '"]').text();
            choices.push({
                value: val,
                label: choice.replace(/{{(.*?)}}/, '')
            });
        });
        return choices;
    });

    $(document).on('facetwp-loaded', function() {
        $('.facetwp-type-fselect select:not(.ready)').each(function() {
            var facet_name = $(this).closest('.facetwp-facet').attr('data-name');
            var settings = FWP.settings[facet_name];

            settings.optionFormatter = function(row) {
                row = row.replace(/{{/g, '<span class="facetwp-counter">');
                row = row.replace(/}}/g, '<span>');
                return row;
            };

            var opts = FWP.hooks.applyFilters('facetwp/set_options/fselect', settings, {
                'facet_name': facet_name
            });

            $(this).fSelect(opts);
            $(this).addClass('ready');
        });

        // unfreeze choices
        $('.fs-wrap.fs-disabled').removeClass('fs-disabled');
    });

    $(document).on('fs:changed', function(e, wrap) {
        var is_facet = $(wrap).closest('.facetwp-facet').length > 0;

        if (is_facet && wrap.classList.contains('multiple')) {
            var facet_name = $(wrap).closest('.facetwp-facet').attr('data-name');

            if ('or' === FWP.settings[facet_name]['operator']) {
                FWP.frozen_facets[facet_name] = 'soft';

                // freeze choices
                if (FWP.auto_refresh) {
                    $(wrap).addClass('fs-disabled');
                }
            }

            FWP.autoload();
        }
    });

    $(document).on('fs:closed', function(e, wrap) {
        var is_facet = $(wrap).closest('.facetwp-facet').length > 0;

        if (is_facet && ! wrap.classList.contains('multiple')) {
            FWP.autoload();
        }
    });

    /* ======== Hierarchy ======== */

    FWP.hooks.addAction('facetwp/refresh/hierarchy', function($this, facet_name) {
        var selected_values = [];
        $this.find('.facetwp-link.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        FWP.facets[facet_name] = selected_values;
    });

    FWP.hooks.addFilter('facetwp/selections/hierarchy', function(output, params) {
        return params.el.find('.facetwp-link.checked').text();
    });

    $(document).on('click', '.facetwp-type-hierarchy .facetwp-link', function() {
        $(this).closest('.facetwp-facet').find('.facetwp-link').removeClass('checked');
        if ('' !== $(this).attr('data-value')) {
            $(this).addClass('checked');
        }
        FWP.autoload();
    });

    $(document).on('click', '.facetwp-type-hierarchy .facetwp-toggle', function() {
        var $parent = $(this).closest('.facetwp-facet');
        $parent.find('.facetwp-toggle').toggleClass('facetwp-hidden');
        $parent.find('.facetwp-overflow').toggleClass('facetwp-hidden');
    });

    /* ======== Number Range ======== */

    FWP.hooks.addAction('facetwp/refresh/number_range', function($this, facet_name) {
        var min = $this.find('.facetwp-number-min').val() || '';
        var max = $this.find('.facetwp-number-max').val() || '';
        FWP.facets[facet_name] = ('' !== min || '' !== max) ? [min, max] : [];
    });

    FWP.hooks.addFilter('facetwp/selections/number_range', function(output, params) {
        return params.selected_values[0] + ' - ' + params.selected_values[1];
    });

    $(document).on('click', '.facetwp-type-number_range .facetwp-submit', function() {
        FWP.refresh();
    });

    /* ======== Proximity ======== */

    $(document).on('facetwp-loaded', function() {
        var $locations = $('.facetwp-location');

        if ($locations.length < 1) {
            return;
        }

        if (! FWP.loaded) {
            window.FWP_MAP = window.FWP_MAP || {};
            FWP_MAP.sessionToken = new google.maps.places.AutocompleteSessionToken();
            FWP_MAP.autocompleteService = new google.maps.places.AutocompleteService();
            FWP_MAP.placesService = new google.maps.places.PlacesService(
                document.createElement('div')
            );

            // We need FWP_JSON available to grab the queryDelay
            $(document).on('input', '.facetwp-location', FWP.helper.debounce(function() {
                var val = $(this).val();
                var $facet = $(this).closest('.facetwp-facet');

                if ('' == val || val.length < FWP_JSON['proximity']['minLength']) {
                    $facet.find('.location-results').addClass('facetwp-hidden');
                    return;
                }

                var options = FWP_JSON['proximity']['autocomplete_options'];
                options.sessionToken = FWP_MAP.sessionToken;
                options.input = val;

                FWP_MAP.autocompleteService.getPredictions(options, function(results, status) {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        var html = '';

                        results.forEach(function(result, index) {
                            var css = (0 === index) ? ' active' : '';
                            html += '<div class="location-result' + css + '" data-id="' + result.place_id + '" data-index="' + index + '">';
                            html += '<span class="result-main">' + result.structured_formatting.main_text + '</span> ';
                            html += '<span class="result-secondary">' + result.structured_formatting.secondary_text + '</span>';
                            html += '<span class="result-description facetwp-hidden">' + result.description + '</span>';
                            html += '</div>';
                        });

                        $facet.find('.location-results').html(html).removeClass('facetwp-hidden');
                    }
                });
            }, FWP_JSON['proximity']['queryDelay']));
        }

        $locations.each(function(idx, el) {
            var $input = $(this);

            if ($input.closest('.location-wrap').length < 1) {

                // Preserve CSS IDs
                if (0 === idx) {
                    $input.attr('id', 'facetwp-location');
                    $input.closest('.facetwp-facet').find('.facetwp-radius').attr('id', 'facetwp-radius');
                }

                // Add the "Locate me" icon
                $input.wrap('<span class="location-wrap"></span>');
                $input.before('<i class="locate-me"></i>');
                $input.after('<div class="location-results facetwp-hidden"></div>');
            }

            $input.trigger('keyup');
        });
    });

    $(document).on('click', '.location-result', function() {
        var $facet = $(this).closest('.facetwp-facet');
        var place_id = $(this).attr('data-id');
        var description = $(this).find('.result-description').text();

        FWP_MAP.placesService.getDetails({
            placeId: place_id,
            fields: ['geometry']
        }, function(place, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                $facet.find('.facetwp-lat').val(place.geometry.location.lat());
                $facet.find('.facetwp-lng').val(place.geometry.location.lng());
                FWP.autoload();
            }
        });

        $('.facetwp-location').val(description);
        $('.location-results').addClass('facetwp-hidden');
    });

    $(document).on('click', '.facetwp-type-proximity .locate-me', function(e) {
        var $this = $(this);
        var $facet = $this.closest('.facetwp-facet');
        var $input = $facet.find('.facetwp-location');
        var $lat = $facet.find('.facetwp-lat');
        var $lng = $facet.find('.facetwp-lng');

        // reset
        if ($this.hasClass('f-reset')) {
            $lat.val('');
            $lat.val('');
            $input.val('');
            FWP.autoload();
            return;
        }

        // loading icon
        $this.addClass('f-loading');

        // HTML5 geolocation
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            $lat.val(lat);
            $lng.val(lng);

            var geocoder = new google.maps.Geocoder();
            var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
            geocoder.geocode({'location': latlng}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    $input.val(results[0].formatted_address);
                }
                else {
                    $input.val('Your location');
                }
                $this.addClass('f-reset');
                FWP.autoload();
            });

            $this.removeClass('f-loading');

            FWP.hooks.doAction('facetwp/geolocation/success', {
                'facet': $facet,
                'position': position
            });
        },
        function(error) {
            $this.removeClass('f-loading');

            FWP.hooks.doAction('facetwp/geolocation/error', {
                'facet': $facet,
                'error': error
            });
        });
    });

    $(document).on('keyup', '.facetwp-location', function(e) {
        var $facet = $(this).closest('.facetwp-facet');
        $facet.find('.locate-me').toggleClass('f-reset', ('' !== $(this).val()));

        if (38 === e.which || 40 === e.which || 13 === e.which) {
            var curr_index = parseInt($facet.find('.location-result.active').attr('data-index'));
            var max_index = parseInt($facet.find('.location-result:last').attr('data-index'));
        }

        if (38 === e.which) { // up
            var new_index = (0 < curr_index) ? (curr_index - 1) : max_index;
            $facet.find('.location-result.active').removeClass('active');
            $facet.find('.location-result[data-index=' + new_index + ']').addClass('active');
        }
        else if (40 === e.which) { // down
            var new_index = (curr_index < max_index) ? (curr_index + 1) : 0;
            $facet.find('.location-result.active').removeClass('active');
            $facet.find('.location-result[data-index=' + new_index + ']').addClass('active');
        }
        else if (13 === e.which) { // enter
            $facet.find('.location-result.active').trigger('click');
        }
    });

    $(document).on('click focusout', function(e) {
        var $el = $(e.target);
        var $wrap = $el.closest('.location-wrap');

        if ($wrap.length < 1 || $el.hasClass('f-reset')) {
            $('.location-results').addClass('facetwp-hidden');
        }
    });

    $(document).on('focusin', '.facetwp-location', function() {
        var $facet = $(this).closest('.facetwp-facet');
        if ('' != $(this).val()) {
            $facet.find('.location-results').removeClass('facetwp-hidden');
        }
    });

    $(document).on('change', '.facetwp-radius', function() {
        var $facet = $(this).closest('.facetwp-facet');
        if ('' !== $facet.find('.facetwp-location').val()) {
            FWP.autoload();
        }
    });

    $(document).on('input', '.facetwp-radius-slider', function(e) {
        var $facet = $(this).closest('.facetwp-facet');
        $facet.find('.facetwp-radius-dist').text(e.target.value);
    });

    FWP.hooks.addAction('facetwp/refresh/proximity', function($this, facet_name) {
        var lat = $this.find('.facetwp-lat').val();
        var lng = $this.find('.facetwp-lng').val();
        var radius = $this.find('.facetwp-radius').val();
        var location = encodeURIComponent($this.find('.facetwp-location').val());
        FWP.frozen_facets[facet_name] = 'hard';
        FWP.facets[facet_name] = ('' !== lat && 'undefined' !== typeof lat) ?
            [lat, lng, radius, location] : [];
    });

    FWP.hooks.addFilter('facetwp/selections/proximity', function(label, params) {
        return FWP_JSON['proximity']['clearText'];
    });

    /* ======== Search ======== */

    FWP.logic.search = {
        delay_refresh: FWP.helper.debounce(function(facet_name) {
            FWP.frozen_facets[facet_name] = 'soft';
            FWP.autoload();
        }, 250)
    };

    FWP.hooks.addAction('facetwp/refresh/search', function($this, facet_name) {
        var val = $this.find('.facetwp-search').val() || '';
        FWP.facets[facet_name] = val;
    });

    $(document).on('keyup', '.facetwp-type-search .facetwp-search', function(e) {
        var $facet = $(this).closest('.facetwp-facet');
        var facet_name = $facet.attr('data-name');

        if ('undefined' !== typeof FWP.settings[facet_name]) {
            if ('yes' === FWP.settings[facet_name]['auto_refresh']) {
                FWP.logic.search['delay_refresh'](facet_name);
            }
            else if (13 === e.keyCode) {
                FWP.autoload();
            }
        }
    });

    $(document).on('click', '.facetwp-type-search .facetwp-btn', function() {
        FWP.autoload();
    });

    /* ======== Slider ======== */

    FWP.hooks.addAction('facetwp/refresh/slider', function($this, facet_name) {
        FWP.facets[facet_name] = [];

        // settings have already been loaded
        if ('undefined' !== typeof FWP.frozen_facets[facet_name]) {
            if ('undefined' !== typeof $this.find('.facetwp-slider')[0].noUiSlider) {
                FWP.facets[facet_name] = $this.find('.facetwp-slider')[0].noUiSlider.get();
            }
        }
    });

    FWP.hooks.addAction('facetwp/set_label/slider', function($this) {
        var facet_name = $this.attr('data-name');
        var min = FWP.settings[facet_name]['lower'];
        var max = FWP.settings[facet_name]['upper'];
        var format = FWP.settings[facet_name]['format'];
        var opts = {
            decimal_separator: FWP.settings[facet_name]['decimal_separator'],
            thousands_separator: FWP.settings[facet_name]['thousands_separator']
        };

        if ( min === max ) {
            var label = FWP.settings[facet_name]['prefix']
                + nummy(min).format(format, opts)
                + FWP.settings[facet_name]['suffix'];
        }
        else {
            var label = FWP.settings[facet_name]['prefix']
                + nummy(min).format(format, opts)
                + FWP.settings[facet_name]['suffix']
                + ' &mdash; '
                + FWP.settings[facet_name]['prefix']
                + nummy(max).format(format, opts)
                + FWP.settings[facet_name]['suffix'];
        }
        $this.find('.facetwp-slider-label').html(label);
    });

    FWP.hooks.addFilter('facetwp/selections/slider', function(output, params) {
        return params.el.find('.facetwp-slider-label').text();
    });

    $(document).on('facetwp-loaded', function() {
        $('.facetwp-type-slider .facetwp-slider:not(.ready)').each(function() {
            var $parent = $(this).closest('.facetwp-facet');
            var facet_name = $parent.attr('data-name');
            var opts = FWP.settings[facet_name];

            // on first load, check for slider URL variable
            if (false !== FWP.helper.get_url_var(facet_name)) {
                FWP.frozen_facets[facet_name] = 'hard';
            }

            // fail on slider already initialized
            if ('undefined' !== typeof $(this).data('options')) {
                return;
            }

            // fail if start values are null
            if (null === FWP.settings[facet_name].start[0]) {
                return;
            }

            // fail on invalid ranges
            if (parseFloat(opts.range.min) >= parseFloat(opts.range.max)) {
                FWP.settings[facet_name]['lower'] = opts.range.min;
                FWP.settings[facet_name]['upper'] = opts.range.max;
                FWP.hooks.doAction('facetwp/set_label/slider', $parent);
                return;
            }

            // custom slider options
            var slider_opts = FWP.hooks.applyFilters('facetwp/set_options/slider', {
                range: opts.range,
                start: opts.start,
                step: parseFloat(opts.step),
                connect: true
            }, { 'facet_name': facet_name });


            var slider = $(this)[0];
            noUiSlider.create(slider, slider_opts);
            slider.noUiSlider.on('update', function(values, handle) {
                FWP.settings[facet_name]['lower'] = values[0];
                FWP.settings[facet_name]['upper'] = values[1];
                FWP.hooks.doAction('facetwp/set_label/slider', $parent);
            });
            slider.noUiSlider.on('set', function() {
                FWP.frozen_facets[facet_name] = 'hard';
                FWP.autoload();
            });

            $(this).addClass('ready');
        });

        // hide reset buttons
        $('.facetwp-type-slider').each(function() {
            var name = $(this).attr('data-name');
            var $button = $(this).find('.facetwp-slider-reset');
            $.isEmptyObject(FWP.facets[name]) ? $button.hide() : $button.show();
        });
    });

    $(document).on('click', '.facetwp-type-slider .facetwp-slider-reset', function() {
        var facet_name = $(this).closest('.facetwp-facet').attr('data-name');
        FWP.reset(facet_name);
    });

    /* ======== Rating ======== */

    FWP.hooks.addAction('facetwp/refresh/rating', function($this, facet_name) {
        var selected_values = [];
        $this.find('.facetwp-star.selected').each(function() {
            var val = $(this).attr('data-value');
            if ('' != val) {
                selected_values.push(val);
            }
        });
        FWP.facets[facet_name] = selected_values;
    });

    $(document).on('mouseover', '.facetwp-star', function() {
        var $facet = $(this).closest('.facetwp-facet');

        if ($(this).hasClass('selected')) {
            $facet.find('.facetwp-star-label').text(FWP_JSON['rating']['Undo']);
        }
        else {
            var label = ('5' == $(this).attr('data-value')) ? '' : FWP_JSON['rating']['& up'];
            $facet.find('.facetwp-star-label').text(label);
            $facet.find('.facetwp-counter').text('(' + $(this).data('counter') + ')');
        }
    });

    $(document).on('mouseout', '.facetwp-star', function() {
        var $facet = $(this).closest('.facetwp-facet');
        $facet.find('.facetwp-star-label').text('');
        $facet.find('.facetwp-counter').text('');
    });

    $(document).on('click', '.facetwp-star', function() {
        var $facet = $(this).closest('.facetwp-facet');
        var is_selected = $(this).hasClass('selected');
        $facet.find('.facetwp-star').removeClass('selected');
        if (! is_selected) {
            $(this).addClass('selected');
        }
        FWP.autoload();
    });

})(jQuery);