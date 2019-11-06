var FWPCL = FWPCL || {
    is_loading: false,
    action_el: null
};


(function($) {


    $(function() {
        FWPCL.load();

        // Topnav
        $(document).on('click', '.facetwp-tab', function() {
            var tab = $(this).attr('rel');
            $('.facetwp-tab').removeClass('active');
            $(this).addClass('active');
            $('.facetwp-region').removeClass('active');
            $('.facetwp-region-' + tab).addClass('active');

            // Populate the export code
            if ('settings' == tab) {
                var code = JSON.stringify(FWPCL.parse_data());
                $('.export-code').val(code);
            }
        });

        $('.export-code').on('focus', function() {
            $(this).select();
        });

        $(document).on('click', '.ruleset-label', function(e) {
            e.preventDefault();
        });

        $(document).on('click', '.ruleset .title', function() {
            $(this).closest('.ruleset').toggleClass('collapsed');
        });

        // Prevent label newlines
        $(document).on('keypress', '.ruleset-label', function(e) {
            if (13 === e.which) {
                e.preventDefault();
            }
        });

        // Trigger click
        $('.facetwp-header-nav a:first').click();
    });

    FWPCL.__ = function(str) {
        return ('undefined' !== typeof FWPCL.i18n[str]) ? FWPCL.i18n[str] : str;
    };


    FWPCL.load = function() {
        FWPCL.is_loading = true;

        $.each(FWPCL.rulesets, function(index, ruleset) {
            $('.add-ruleset').click();

            // Set the ruleset props
            $('.facetwp-region-rulesets .ruleset:last .ruleset-label').text(ruleset.label);
            $('.facetwp-region-rulesets .ruleset:last .action-else').val(ruleset.else);

            // Set the ations
            $.each(ruleset.actions, function(index, action) {
                $('.facetwp-region-rulesets .action-and:last').click();

                var $last = $('.facetwp-region-rulesets .action:last');

                // Add <option> if needed
                if ($last.find('.action-object option[value="' + action.object + '"]').length < 1) {
                    $last.find('.action-object').append('<option value="' + action.object + '">' + action.object + '</option>');
                }

                $last.find('.action-toggle').val(action.toggle);
                $last.find('.action-object').val(action.object).trigger('change');
                $last.find('.action-selector').val(action.selector);
            });

            // Set the conditions
            $.each(ruleset.conditions, function(index, cond_group) {
                $('.facetwp-region-rulesets .condition-and:last').click();

                $.each(cond_group, function(index, cond) {

                    // Skip first item ("AND")
                    if (0 < index) {
                        $('.facetwp-region-rulesets .condition-or:last').click();
                    }

                    var $last = $('.facetwp-region-rulesets .condition:last');

                    // Add <option> if needed
                    if ($last.find('.condition-object option[value="' + cond.object + '"]').length < 1) {
                        $last.find('.condition-object').append('<option value="' + cond.object + '">' + cond.object + '</option>');
                    }

                    $last.find('.condition-object').val(cond.object).trigger('change');
                    $last.find('.condition-compare').val(cond.compare);
                    $last.find('.condition-value').val(cond.value);
                });
            });
        });

        FWPCL.is_loading = false;
    }


    FWPCL.parse_data = function() {
        var rules = [];

        $('.facetwp-region-rulesets .ruleset').each(function(rule_num) {
            rules[rule_num] = {
                'label': $(this).find('.ruleset-label').text(),
                'conditions': [],
                'actions': [],
                'else': $(this).find('.action-else').val()
            };

            // Get conditions (and preserve groups)
            $(this).find('.condition-group').each(function(group_num) {
                var conditions = [];

                $(this).find('.condition').each(function() {
                    var condition = {
                        'object': $(this).find('.condition-object').val(),
                        'compare': $(this).find('.condition-compare').val(),
                        'value': $(this).find('.condition-value').val()
                    };
                    conditions.push(condition);
                });

                rules[rule_num]['conditions'][group_num] = conditions;
            });

            // Get actions
            $(this).find('.action').each(function() {
                var action = {
                    'toggle': $(this).find('.action-toggle').val(),
                    'object': $(this).find('.action-object').val(),
                    'selector': $(this).find('.action-selector').val()
                };

                rules[rule_num]['actions'].push(action);
            });
        });

        return rules;
    }


    $(document).on('change', '.condition-object', function() {
        var $condition = $(this).closest('.condition');
        var val = $(this).val() || '';

        $condition.find('.condition-value').show();
        $condition.find('.condition-compare').show();
        var is_template = ( 'template-' == val.substr(0, 9));
        if ('facets-empty' == val || 'facets-not-empty' == val || is_template) {
            $condition.find('.condition-compare').hide();
            $condition.find('.condition-value').hide();
        }
    });


    $(document).on('change', '.action-object', function() {
        var $this = $(this);
        var hidden = ('custom' == $this.val());
        $this.closest('.action').find('.action-selector').toggleClass('hidden', !hidden);
    });


    $(document).on('click', '.facetwp-save', function() {
        $('.fwpcl-response').html(FWPCL.__('Saving') + '...');
        $('.fwpcl-response').css({ display: 'inline-block' });

        var data = FWPCL.parse_data();

        $.post(ajaxurl, {
            'action': 'fwpcl_save',
            'data': JSON.stringify(data)
        }, function(response) {
            $('.fwpcl-response').html(FWPCL.__('Changes saved'));
            setTimeout(function() {
                $('.fwpcl-response').stop().fadeOut();
            }, 4000);
        });
    });


    $(document).on('click', '.add-ruleset', function() {
        var $clone = $('.clone').clone();
        var $rule = $clone.find('.clone-ruleset');

        $('.facetwp-region-rulesets .facetwp-content-wrap').append($rule.html());
        $('.facetwp-region-rulesets .facetwp-content-wrap').sortable({
            axis: 'y',
            items: '.ruleset',
            placeholder: 'sortable-placeholder',
            handle: '.toggle'
        });
    });


    $(document).on('click', '.condition-or', function() {
        var $clone = $('.clone-condition').clone();
        $clone.find('.condition').addClass('type-or');
        $clone.find('.condition .type').text(FWPCL.__('OR'));
        $clone.find('.condition .btn').html('');
        $(this).closest('.condition-group').append($clone.html());
        $(this).closest('.condition-group').find('.condition:last .condition-object').trigger('change');
    });


    $(document).on('click', '.condition-and', function() {
        var $clone = $('.clone-condition').clone();
        var $ruleset = $(this).closest('.conditions-col');

        // Set the type label
        $clone.find('.condition .type').text(FWPCL.__('AND'));

        // Create rule group
        $ruleset.find('.condition-wrap').append('<div class="condition-group" />');
        var $group = $ruleset.find('.condition-group:last');
        $group.append($clone.html());
        $group.find('.condition-object').trigger('change');

        // The first label should be "IF"
        $(this).closest('.conditions-col').find('.condition:first .type').text(FWPCL.__('IF'));
    });


    $(document).on('click', '.condition-drop', function() {
        var $wrap = $(this).closest('.condition-wrap');
        var $cond = $(this).closest('.condition');
        var index = $(this).closest('.condition-group').find('.condition').index($cond);
        var siblings = $cond.siblings().length;

        // Remove group if it's the first or only item
        if (0 === siblings || 0 === index) {
            $(this).closest('.condition-group').remove(); // remove group
        }
        else {
            $(this).closest('.condition').remove(); // remove condition
        }

        // The first label should be "IF"
        $wrap.find('.condition:first .type').text(FWPCL.__('IF'));
    });


    $(document).on('click', '.header-bar td.delete', function() {
        if (confirm(FWPCL.__('Delete this ruleset?'))) {
            $(this).closest('.ruleset').remove();
        }
    });


    $(document).on('click', '.action-and', function() {
        var html = $('.clone-action').html();
        var $wrap = $(this).siblings('.action-wrap');

        $wrap.append(html);
        $wrap.find('.action:first .type').text(FWPCL.__('THEN'));
        $wrap.find('.action:last .action-object').trigger('change');
    });


    $(document).on('click', '.action-drop', function() {
        var $wrap = $(this).closest('.action-wrap');
        $(this).closest('.action').remove();
        $wrap.find('.action:first .type').text(FWPCL.__('THEN'));
    });


    $(document).on('click', '.fwpcl-import', function() {
        $('.fwpcl-import-response').html(FWPCL.__('Importing') + '...');
        $.post(ajaxurl, {
            action: 'fwpcl_import',
            import_code: $('.import-code').val(),
        },
        function(response) {
            $('.fwpcl-import-response').html(response);
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        });
    });

})(jQuery);
