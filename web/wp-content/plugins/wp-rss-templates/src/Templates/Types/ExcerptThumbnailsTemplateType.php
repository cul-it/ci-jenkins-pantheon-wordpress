<?php

namespace RebelCode\Wpra\Templates\Templates\Types;

use RebelCode\Wpra\Core\Templates\Feeds\Types\ListTemplateType;

/**
 * Class ExcerptThumbnailsTemplateType
 *
 * Excerpt and thumbnails template type.
 *
 * @package RebelCode\Wpra\Templates\Templates\Types
 */
class ExcerptThumbnailsTemplateType extends ListTemplateType
{
    /**
     * Enqueues the assets required by this template type.
     *
     * @since 0.1
     */
    protected function enqueueAssets()
    {
        $general_settings = get_option('wprss_settings_general');

        // Enqueue scripts
        wp_enqueue_script('jquery.colorbox-min', WPRSS_JS . 'jquery.colorbox-min.js', ['jquery']);
        wp_enqueue_script('wprss_custom', WPRSS_JS . 'custom.js', ['jquery', 'jquery.colorbox-min']);

        wp_enqueue_script('wpra-manifest', WPRSS_APP_JS . 'wpra-manifest.min.js', ['jquery'], WPRSS_VERSION);
        wp_enqueue_script('wpra-pagination', WPRSS_APP_JS . 'pagination.min.js', ['wpra-manifest'], WPRSS_VERSION);

        wp_localize_script('wpra-pagination', 'WpraPagination', [
            'baseUri' => rest_url('/wpra/v1/templates/%s/render/'),
        ]);

        if (empty($general_settings['styles_disable'])) {
            wp_enqueue_style('colorbox', WPRSS_CSS . 'colorbox.css', [], '1.4.33');
            wp_enqueue_style('wpra-et-template-styles', WPRSS_TEMPLATES_URL . 'build/css/et.min.css', [], WPRSS_VERSION);
            wp_enqueue_style('wpra-pagination', WPRSS_APP_CSS . 'pagination.min.css', [], WPRSS_VERSION);
        }
    }

    /**
     * Retrieves the template type key.
     *
     * @return string
     *
     * @since 0.1
     */
    public function getKey()
    {
        return 'et';
    }

    /**
     * Retrieves the template type name.
     *
     * @return string
     *
     * @since 0.1
     */
    public function getName()
    {
        return __('Excerpt & Thumbnails', 'wprss');
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getOptions()
    {
        $options = parent::getOptions();

        return $options + [
            'excerpt_max_length' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
                'default' => 0,
            ],
            'show_image' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'show_excerpt' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'excerpt_max_words' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
                'default' => 0,
            ],
            'excerpt_ending' => [
                'default' => '...',
            ],
            'excerpt_more_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'excerpt_read_more' => [
                'default' => __('Read more', 'wprss'),
            ],

            'thumbnail_placement' => [
                'filter' => 'enum',
                'options' => ['excerpt-side', 'excerpt-text', 'item-side', 'item-top'],
                'default' => 'excerpt-side',
            ],

            'thumbnail_width' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
                'default' => 175,
            ],
            'thumbnail_height' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
                'default' => 150,
            ],
            'thumbnail_is_link' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'empty_thumbnail_behavior' => [
                'filter' => 'enum',
                'options' => ['true', 'false'],
                'default' => 'true',
            ],
        ];
    }
}
