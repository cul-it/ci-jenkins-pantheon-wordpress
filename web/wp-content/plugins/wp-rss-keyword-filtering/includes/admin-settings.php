<?php

add_action('wprss_admin_init', 'wprss_kf_add_settings');
/**
 * Adds some more settings fields pertaining to keyword filtering
 *
 * @since 1.0
 */
function wprss_kf_add_settings()
{

    add_settings_section(
        'wprss_settings_kf_section',
        __('Filter by keywords', 'wprss'),
        'wprss_settings_kf_callback',
        'wprss_settings_kf'
    );

    add_settings_section(
        'wprss_settings_kf_tags_section',
        __('Filter by tags', 'wprss'),
        'wprss_settings_kf_tags_callback',
        'wprss_settings_kf'
    );

    register_setting(
        'wprss_settings_kf',
        'wprss_settings_kf',
        'wprss_settings_kf_validate'
    );

    add_settings_field(
        'wprss-settings-kf-keywords-any',
        __('Only import items that have <u>any</u> of these keywords', 'wprss'),
        'wprss_setting_kf_keywords_any_callback',
        'wprss_settings_kf',
        'wprss_settings_kf_section'
    );
    add_settings_field(
        'wprss-settings-kf-keywords',
        __('Only import items that have <u>all</u> of these keywords', 'wprss'),
        'wprss_setting_kf_keywords_callback',
        'wprss_settings_kf',
        'wprss_settings_kf_section'
    );
    add_settings_field(
        'wprss-settings-kf-keywords-not',
        __('Do <u>not</u> import items that have <u>any</u> of these keywords', 'wprss'),
        'wprss_setting_kf_keywords_not_callback',
        'wprss_settings_kf',
        'wprss_settings_kf_section'
    );
    add_settings_field(
        'wprss-settings-kf-keywords-tags',
        __('Only import items that have <u>any</u> of these tags', 'wprss'),
        'wprss_setting_kf_tags_callback',
        'wprss_settings_kf',
        'wprss_settings_kf_tags_section'
    );
    add_settings_field(
        'wprss-settings-kf-keywords-not-tags',
        __('Do <u>not</u> import items that have <u>any</u> of these tags', 'wprss'),
        'wprss_setting_kf_not_tags_callback',
        'wprss_settings_kf',
        'wprss_settings_kf_tags_section'
    );

    if (version_compare(WPRSS_VERSION, '4.5', '<')) {
        add_settings_section(
            'wprss_settings_kf_licenses_section',
            __('Keyword Filtering License', 'wprss'),
            'wprss_kf_settings_license_callback',
            'wprss_settings_license_keys'
        );

        add_settings_field(
            'wprss-settings-license',
            __('License Key', 'wprss'),
            'wprss_kf_setting_license_callback',
            'wprss_settings_license_keys',
            'wprss_settings_kf_licenses_section'
        );

        add_settings_field(
            'wprss-settings-license-activation',
            __('Activate License', 'wprss'),
            'wprss_kf_setting_license_activation_callback',
            'wprss_settings_license_keys',
            'wprss_settings_kf_licenses_section'
        );
    }
}

add_action('wprss_add_settings_fields_sections', 'wprss_kf_add_settings_fields_sections', 10, 1);
/**
 * Add settings fields and sections for Keyword Filtering
 *
 * @since 1.0
 */
function wprss_kf_add_settings_fields_sections($active_tab)
{
    if ($active_tab == 'kf_settings') {
        settings_fields('wprss_settings_kf');
        do_settings_sections('wprss_settings_kf');
    }
}

/**
 * Draw the licenses settings section header
 *
 * @since 1.0
 */
function wprss_kf_settings_license_callback()
{
}

/**
 * Set license
 *
 * @since 1.0
 */
function wprss_kf_setting_license_callback($args)
{
    $license_keys = get_option('wprss_settings_license_keys');
    $kf_license_key = (isset($license_keys['kf_license_key'])) ? $license_keys['kf_license_key'] : false;

    ?>
    <input id="wprss-kf-license-key"
           name="wprss_settings_license_keys[kf_license_key]"
           type="text"
           value="<?= esc_attr($kf_license_key) ?> "
    />
    <label class="description" for="wprss-kf-license- key">
        <?= __('Enter your license key', 'wprss') ?>
    </label>
    <?php
}

/**
 * License activation button and indicator
 *
 * @since 1.0
 */
function wprss_kf_setting_license_activation_callback($args)
{
    $license_keys = get_option('wprss_settings_license_keys');
    $license_statuses = get_option('wprss_settings_license_statuses');
    $kf_license_key = (isset($license_keys['kf_license_key'])) ? $license_keys['kf_license_key'] : false;
    $kf_license_status = (isset($license_statuses['kf_license_status'])) ? $license_statuses['kf_license_status']
        : false;

    if ($kf_license_status != false && $kf_license_status == 'valid') : ?>
        <span style="color:green;">
            <?php _e('active', 'wprss'); ?>
        </span>
        <?php wp_nonce_field('wprss_kf_license_nonce', 'wprss_kf_license_nonce'); ?>
        <input type="submit"
               class="button-secondary"
               name="wprss_kf_license_deactivate"
               value="<?php _e('Deactivate License', 'wprss'); ?>"
        />
    <?php else : ?>
        <?php wp_nonce_field('wprss_kf_license_nonce', 'wprss_kf_license_nonce'); ?>
        <input type="submit" class="button-secondary" name="wprss_kf_license_activate"
               value="<?php _e('Activate License', 'wprss'); ?>" />
    <?php endif;
}

/**
 * Callback function that validates the wprss_settings_kf page's options
 *
 * @since 1.0
 * @todo  settings The settings to be validated and sanitized before insertion into DB
 */
function wprss_settings_kf_validate($settings)
{
    $settings['keywords'] = preg_replace('/,(\s*)/', ', ', $settings['keywords']);

    return $settings;
}

/**
 * The callback that displays the description of the section
 *
 * @since 1.0
 */
function wprss_settings_kf_callback()
{
    wp_enqueue_style('wprss-kf-options-css');
    ?>
    <p class="wprss-kf-help">
        <span class="dashicons dashicons-editor-help"></span>
        <?php _e('Separate your keywords by commas. For example:', 'wprss'); ?>
        <span class="wprss-kf-help-example">sports, news, red velvet, cupcakes</span>
    </p>
    <?php

}

/**
 * The callback that displays the description of the section
 *
 * @since 1.0
 */
function wprss_settings_kf_tags_callback()
{
    ?>
    <p class="wprss-kf-help">
        <span class="dashicons dashicons-editor-help"></span>
        <?php _e('Separate your tags by commas. For example:', 'wprss'); ?>
        <span class="wprss-kf-help-example">tennis, breaking-news, chess, tips-and-tricks</span>
    </p>
    <?php

}

/**
 * Callback that prints the keywords option field
 *
 * @since 1.0
 */
function wprss_setting_kf_keywords_callback()
{
    $options = get_option('wprss_settings_kf', array());
    $keywords = (isset($options['keywords'])) ? $options['keywords'] : '';

    ?>
    <textarea id="keywords"
              name="wprss_settings_kf[keywords]"
              cols="50" rows="5"
              type="text"
              value="<?= $keywords ?>"
              class="small-text"
    ><?= $keywords ?></textarea>
    <?php

}

/**
 * Callback that prints the any keywords option field
 *
 * @since 1.0
 */
function wprss_setting_kf_keywords_any_callback()
{
    $options = get_option('wprss_settings_kf', array());
    $keywords_any = (isset($options['keywords_any'])) ? $options['keywords_any'] : '';

    ?>
    <textarea id="keywords_any"
              name="wprss_settings_kf[keywords_any]"
              cols="50" rows="5"
              type="text"
              value="<?= $keywords_any ?>"
              class="small-text"
    ><?= $keywords_any ?></textarea>
    <?php

}

/**
 * Callback that prints the no keywords option field
 *
 * @since 1.0
 */
function wprss_setting_kf_keywords_not_callback()
{
    $options = get_option('wprss_settings_kf', array());
    $keywords_not = (isset($options['keywords_not'])) ? $options['keywords_not'] : '';

    ?>
    <textarea id="keywords_not"
              name="wprss_settings_kf[keywords_not]"
              cols="50"
              rows="5"
              type="text"
              value="<?= $keywords_not ?>"
              class="small-text"
    ><?= $keywords_not ?></textarea>
    <?php

}

/**
 * Callback that prints the tags option field
 *
 * @since 1.0
 */
function wprss_setting_kf_tags_callback()
{
    $options = get_option('wprss_settings_kf', array());
    $keywords_tags = (isset($options['keywords_tags'])) ? $options['keywords_tags'] : '';

    ?>
    <textarea id="keywords_tags"
              name="wprss_settings_kf[keywords_tags]"
              cols="50"
              rows="5"
              type="text"
              value="<?= $keywords_tags ?>"
              class="small-text"
    ><?= $keywords_tags ?></textarea>
    <?php

}

/**
 * Callback that prints the not tags option field
 *
 * @since 1.5
 */
function wprss_setting_kf_not_tags_callback()
{
    $options = get_option('wprss_settings_kf', array());
    $keywords_not_tags = (isset($options['keywords_not_tags'])) ? $options['keywords_not_tags'] : '';

    ?>
    <textarea id="keywords_not_tags"
              name="wprss_settings_kf[keywords_not_tags]"
              cols="50"
              rows="5"
              type="text"
              value="<?= $keywords_not_tags ?>"
              class="small-text"
    ><?= $keywords_not_tags ?></textarea>
    <?php

}

add_action('wprss_options_tabs', 'wprss_kf_add_settings_tabs');
/**
 * Add KF-related settings on the RSS Aggregator > Settings page
 *
 * @since 1.0
 */
function wprss_kf_add_settings_tabs($args)
{
    $args['keyword_filtering'] = array(
        'label' => __('Filtering', 'wprss'),
        'slug' => 'kf_settings',
    );

    return $args;
}

/**
 * The default addon options
 *
 * @since 1.0
 */
function wprss_kf_default_options()
{
    return array(
        'filter_title' => 'true',
        'filter_content' => 'true',
        'keywords' => '',
        'keywords_any' => '',
        'keywords_not' => '',
        'keywords_tags' => '',
        'keywords_not_tags' => '',
    );
}
