<?php

namespace RebelCode\Wpra\Templates\Templates\Types;

use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Templates\Feeds\Types\AbstractWpraFeedTemplateType;

/**
 * Class GridTemplateType
 *
 * Grid template type.
 *
 * @package RebelCode\Wpra\Templates\Templates\Types
 */
class GridTemplateType extends AbstractWpraFeedTemplateType
{
    /**
     * The list of twig paths to grid item template parts.
     * For example, title, excerpt are grid item parts.
     *
     * @since 0.1
     *
     * @var array
     */
    protected $parts;

    /**
     * The list of twig paths to information's template parts.
     * For example, date and source are information item's parts.
     *
     * @since 0.1
     *
     * @var array
     */
    protected $informationParts;

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     *
     * @param array $parts
     * @param array $informationParts
     */
    public function __construct(DataSetInterface $templates, $parts = [], $informationParts = [])
    {
        parent::__construct($templates);

        $this->parts = $parts;
        $this->informationParts = $informationParts;
    }

    /**
     * Enqueues the assets required by this template type.
     *
     * @since 4.13
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
            wp_enqueue_style('wpra-grid-template-styles', WPRSS_TEMPLATES_URL . 'build/css/grid.min.css', [], WPRSS_VERSION);
            wp_enqueue_style('wpra-pagination', WPRSS_APP_CSS . 'pagination.min.css', [], WPRSS_VERSION);
        }
    }

    /**
     * Retrieves the template type key.
     *
     * @return string
     *
     * @since 4.13
     */
    public function getKey()
    {
        return 'grid';
    }

    /**
     * Retrieves the template type name.
     *
     * @return string
     *
     * @since 4.13
     */
    public function getName()
    {
        return __('Grid', 'wprss');
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    protected function prepareContext($ctx)
    {
        $context = parent::prepareContext($ctx);

        $context = array_merge($context, [
            'parts' => $this->parts,
            'information_parts' => $this->informationParts,
        ]);

        return $context;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getOptions()
    {
        // Add the "limit" std option to save it in template models
        $stdOpts = $this->getStandardOptions();
        $limitOpt = $stdOpts['limit'];
        unset($limitOpt['key']);

        return [
            'limit' => $limitOpt,
            'rows_number' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1],
                'default' => 4,
            ],
            'columns_number' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1],
                'default' => 2,
            ],
            'show_borders' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'card_fields_order' => [
                'default' => [
                    'image' => 0,
                    'title' => 1,
                    'excerpt' => 2,
                    'information' => 3,
                ],
            ],
            'information_fields_order' => [
                'default' => [
                    'date' => 0,
                    'source' => 1,
                    'author' => 2,
                ],
            ],
            'item_is_link' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'excerpt_max_words' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
                'default' => 0,
            ],
            'excerpt_ending' => [
                'default' => '...',
            ],
            'show_image' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'thumbnail_is_link' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'thumbnail_height' => [
                'filter' => FILTER_VALIDATE_INT,
                'default' => 250,
            ],
            'show_title' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'show_excerpt' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'excerpt_more_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'excerpt_read_more' => [
                'default' => __('read more', 'wprss'),
            ],
            
            'show_information' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'info_item_block' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],

            'image_is_background' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'latest_to_bottom' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'title_max_length' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
                'default' => 0,
            ],
            'title_is_link' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'flags' => [],
                'default' => false,
            ],
            'pagination' => [
                'key' => 'pagination_enabled',
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'pagination_type' => [
                'filter' => 'enum',
                'options' => ['default', 'numbered'],
                'default' => 'default',
            ],
            'source_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'source_prefix' => [
                'filter' => FILTER_DEFAULT,
                'default' => __('Source:', 'wprss'),
            ],
            'source_is_link' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'author_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'author_prefix' => [
                'filter' => FILTER_DEFAULT,
                'default' => __('By', 'wprss'),
            ],
            'date_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'date_prefix' => [
                'filter' => FILTER_DEFAULT,
                'default' => __('Published on:', 'wprss'),
            ],
            'date_format' => [
                'filter' => FILTER_DEFAULT,
                'default' => 'Y-m-d',
            ],
            'date_use_time_ago' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'videos_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'fill_image' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
        ];
    }
}
