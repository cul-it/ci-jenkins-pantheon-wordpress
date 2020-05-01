<?php

namespace RebelCode\Wpra\Templates;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Data\ChangelogDataSet;
use RebelCode\Wpra\Core\Data\Wp\WpPluginInfoDataSet;
use RebelCode\Wpra\Core\Modules\ModuleInterface;
use RebelCode\Wpra\Core\Wp\Asset\ScriptAsset;
use RebelCode\Wpra\Core\Wp\Asset\StyleAsset;
use RebelCode\Wpra\Templates\Templates\Types\ExcerptThumbnailsTemplateType;
use RebelCode\Wpra\Templates\Templates\Types\GridTemplateType;

/**
 * The module that represents the WP RSS Aggregator Templates addon plugin.
 *
 * @since 0.1
 */
class AddonModule implements ModuleInterface
{
    /**
     * The path to the main plugin file.
     *
     * @since 0.1
     *
     * @var string
     */
    protected $pluginFilePath;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string $pluginFilePath The path to the main plugin file.
     */
    public function __construct($pluginFilePath)
    {
        $this->pluginFilePath = $pluginFilePath;
    }

    /**
     * @inheritdoc
     *
     * @since 0.1
     */
    public function run(ContainerInterface $c)
    {
        do_action('wpra_tmp/templates/init');
    }

    /**
     * @inheritdoc
     *
     * @since 0.1
     */
    public function getFactories()
    {
        return [
            /*
             * The addon main plugin file.
             *
             * @since 0.1
             */
            'wpra_tmp/plugin_file_path' => function () {
                return $this->pluginFilePath;
            },
            /*
             * The addon info.
             *
             * @since 0.1
             */
            'wpra_tmp/info' => function (ContainerInterface $c) {
                return new WpPluginInfoDataSet($c->get('wpra_tmp/plugin_file_path'));
            },
            /*
             * The addon version.
             *
             * @since 0.1
             */
            'wpra_tmp/version' => function (ContainerInterface $c) {
                return $c->get('wpra_tmp/info')['version'];
            },
            /*
             * The current version of the addon database.
             *
             * @since 0.1
             */
            'wpra_tmp/db_version' => function () {
                return '1';
            },
            /*
             * The path to the addon plugin directory.
             *
             * @since 0.1
             */
            'wpra_tmp/plugin_dir_path' => function (ContainerInterface $c) {
                return plugin_dir_path($c->get('wpra_tmp/plugin_file_path'));
            },
            /*
             * The URL to the addon plugin directory.
             *
             * @since 0.1
             */
            'wpra_tmp/plugin_dir_url' => function (ContainerInterface $c) {
                return plugin_dir_url($c->get('wpra_tmp/plugin_file_path'));
            },
            /*
             * The path to the `templates` directory. Trailing slash is included.
             *
             * @since 0.1
             */
            'wpra_tmp/templates_dir_path' => function (ContainerInterface $c) {
                return $c->get('wpra_tmp/plugin_dir_path') . 'templates/';
            },
            /*
             * The URL to the directory where the addon's JS files can be found. Trailing slash is included.
             *
             * @since 0.1
             */
            'wpra_tmp/js_dir_url' => function (ContainerInterface $c) {
                return $c->get('wpra_tmp/plugin_dir_url') . 'build/js/';
            },
            /*
             * The URL to the directory where the addon's CSS files can be found. Trailing slash is included.
             *
             * @since 0.1
             */
            'wpra_tmp/css_dir_url' => function (ContainerInterface $c) {
                return $c->get('wpra_tmp/plugin_dir_url') . 'build/css/';
            },
            /*
             * The URL to the directory where addon's images can be found. Trailing slash is included.
             *
             * @since 0.1
             */
            'wpra_tmp/images_dir_url' => function (ContainerInterface $c) {
                return $c->get('wpra_tmp/plugin_dir_url') . 'images/';
            },
            /*
             * The addon's changelog.
             *
             * @since 0.1
             */
            'wpra_tmp/changelog/raw' => function (ContainerInterface $c) {
                $file = $c->get('wpra_tmp/changelog_file_path');
                $raw = file_get_contents($file);

                return $raw;
            },
            /*
             * The addon's changelog, in data set form.
             *
             * @since 0.1
             */
            'wpra_tmp/changelog/dataset' => function (ContainerInterface $c) {
                return new ChangelogDataSet($c->get('wpra_tmp/changelog_file_path'));
            },
            /*
             * The path to the addon's changelog file.
             *
             * @since 0.1
             */
            'wpra_tmp/changelog/file_path' => function (ContainerInterface $c) {
                return $c->get('wpra_tmp/plugin_dir_path') . 'CHANGELOG.md';
            },
            /*
             * The E&T template type.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/et_template_type' => function (ContainerInterface $c) {
                return new ExcerptThumbnailsTemplateType(
                    $c->get('wpra/feeds/templates/file_template_collection')
                );
            },
            /*
             * The grid template type.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/grid_template_type' => function (ContainerInterface $c) {
                return new GridTemplateType(
                    $c->get('wpra/feeds/templates/file_template_collection'),
                    $c->get('wpra_tmp/templates/feeds/grid_parts_paths'),
                    $c->get('wpra_tmp/templates/feeds/grid_information_parts_paths')
                );
            },
            /*
             * The list of twig templates that are responsible for rendering grid item.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/grid_parts_paths' => function (ContainerInterface $c) {
                $parts = implode(DIRECTORY_SEPARATOR, ['feeds', 'grid', 'parts']);

                return [
                    'title' => $parts . DIRECTORY_SEPARATOR . 'title.twig',
                    'image' => $parts . DIRECTORY_SEPARATOR . 'image.twig',
                    'excerpt' => $parts . DIRECTORY_SEPARATOR . 'excerpt.twig',
                    'information' => $parts . DIRECTORY_SEPARATOR . 'information.twig',
                ];
            },
            /*
             * The list of twig template for information grid item part.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/grid_information_parts_paths' => function (ContainerInterface $c) {
                $parts = implode(DIRECTORY_SEPARATOR, ['feeds', 'grid', 'parts', 'information']);

                return [
                    'author' => $parts . DIRECTORY_SEPARATOR . 'author.twig',
                    'date' => $parts . DIRECTORY_SEPARATOR . 'date.twig',
                    'source' => $parts . DIRECTORY_SEPARATOR . 'source.twig',
                ];
            },
            /*
             * The possible options values for new template selects.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/custom_template_options' => function (ContainerInterface $c) {
                return [
                    'link_behavior' => [
                        'card' => __('Whole card is clickable', 'wprss'),
                        'title' => __('Only the title is clickable', 'wprss'),
                    ],
                    'empty_thumbnail_behavior' => [
                        'true' => __("Use the feed source's default featured image", 'wprss'),
                        'false' => __('Show no thumbnail', 'wprss'),
                    ],
                    'thumbnail_placement' => [
                        'excerpt-side' => __('Excerpt on the right of the image, title above', 'wprss'),
                        'item-side' => __('Title and excerpt on the right of the image', 'wprss'),
                        'excerpt-text' => __('Wrap the excerpt around the image', 'wprss'),
                        'item-top' => __('Image above title and excerpt', 'wprss'),
                    ],
                ];
            },
            /*
             * The et template options.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/et_model_options' => function (ContainerInterface $c) {
                return [
                    'show_excerpt' => true,

                    'excerpt_max_words' => 50,
                    'excerpt_ending' => '...',
                    'excerpt_more_enabled' => true,
                    'excerpt_read_more' => __('Read more', 'wprss'),

                    'show_image' => true,
                    'thumbnail_placement' => 'excerpt-side',
                    'thumbnail_width' => 175,
                    'thumbnail_height' => 150,
                    'thumbnail_is_link' => false,
                    'empty_thumbnail_behavior' => 'true',
                ];
            },
            /*
             * The grid template options.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/grid_model_options' => function (ContainerInterface $c) {
                return [
                    'rows_number' => 4,
                    'columns_number' => 2,
                    'image_is_background' => false,
                    'latest_to_bottom' => true,

                    'show_image' => true,
                    'thumbnail_is_link' => false,
                    'thumbnail_height' => 250,
                    'fill_image' => false,
                    'videos_enabled' => false,

                    'show_title' => true,
                    'show_excerpt' => true,

                    'show_information' => true,
                    'info_item_block' => false,

                    'item_is_link' => false,
                    'card_fields_order' => [
                        'image' => 0,
                        'title' => 1,
                        'excerpt' => 2,
                        'information' => 3,
                    ],
                    'information_fields_order' => [
                        'date' => 0,
                        'source' => 1,
                        'author' => 2,
                    ],
                    'excerpt_max_length' => 0,
                    'excerpt_ending' => '...',
                ];
            },
            /*
             * The list of assets for the grid type.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/grid_template_assets' => function (ContainerInterface $c) {
                return [
                    'grid_template_admin_script' => $c->get('wpra_tmp/scripts/admin/grid_template'),
                    'grid_template_admin_style' => $c->get('wpra_tmp/styles/admin/grid_template'),
                    'grid_template_style' => $c->get('wpra_tmp/styles/grid_template'),
                ];
            },
            /*
             * The grid template type's script (admin).
             *
             * @since 0.1
             */
            'wpra_tmp/scripts/admin/grid_template' => function (ContainerInterface $c) {
                $jsUrl = $c->get('wpra_tmp/js_dir_url');

                return new ScriptAsset('wpra-templates-addon', $jsUrl . 'main.min.js', [
                    'wpra-manifest',
                    'wpra-vendor',
                    'wpra-templates',
                ]);
            },
            /*
             * The raw grid template type script state (admin).
             *
             * @since 0.1
             */
            'wpra_tmp/templates/states/raw' => function (ContainerInterface $c) {
                return [
                    'preview' => [
                        'image' => $c->get('wpra_tmp/images_dir_url') . 'preview-image.jpg',
                    ],
                ];
            },
            /*
             * The grid template type's style (admin).
             *
             * @since 0.1
             */
            'wpra_tmp/styles/admin/grid_template' => function (ContainerInterface $c) {
                $cssUrl = $c->get('wpra_tmp/css_dir_url');

                return new StyleAsset('wpra-templates-addon', $cssUrl . 'main.min.css', [
                    'wpra-templates',
                ]);
            },
            /*
             * The grid template type's style.
             *
             * @since 0.1
             */
            'wpra_tmp/styles/grid_template' => function (ContainerInterface $c) {
                $cssUrl = $c->get('wpra_tmp/css_dir_url');

                return new StyleAsset('wpra-templates-grid', $cssUrl . 'grid.min.css');
            },
            /*
             * Tooltips for feed template model fields.
             *
             * @since 0.1
             */
            'wpra_tmp/templates/feeds/templates_model_options_tooltips' => function (ContainerInterface $c) {
                return [
                    // From grid template
                    'image_is_background' => __('The image will be used as a background with the text appearing over it.', 'wprss'),
                    'info_item_block' => __('Date, author and source will be displayed on separate lines rather than next to each other on a single line.', 'wprss'),
                    'excerpt_max_length' => __('The number of words displayed for the excerpt.', 'wprss'),
                    'item_is_link' => __('Check this box to make whole grid item clickable.', 'wprss'),
                    'latest_to_bottom' => __('Always align the last element in the template to the bottom of the grid. This is useful when, for example, two items have different excerpt lengths or image sizes. It keeps the alignment of each element consistent throughout the grid.', 'wprss'),
                    'columns_number' => __('The number of columns for the grid template. Recommended value is 2 or 3. ', 'wprss'),
                    'fill_image' => __('Check this box to make the image fill the entire image area. Note that your image might be cropped depending on its size and aspect ratio.', 'wprss'),
                    'videos_enabled' => __('Check this box to replace the image with an embedded video, if a video embed is available in the feed item.', 'wprss'),

                    // Common
                    'excerpt_more_enabled' => __('Check this box to add a \'Read more\' link at the end of the excerpt that links to the original source.', 'wprss'),
                    'excerpt_read_more' => __('Set your own text to appear as a link to the original source.', 'wprss'),
                    'thumbnail_is_link' => __('Check this box to link the image to the feed item\'s permalink (original source).', 'wprss'),

                    // From E&T template
                    'show_excerpt' => __('Check this box to show excerpts in the template.', 'wprss'),
                    'excerpt_max_words' => __('The number of words displayed for the excerpt.', 'wprss'),
                    'excerpt_ending' => __('The characters that will appear at end of excerpt, before the "Read more" link (if enabled).', 'wprss'),

                    'show_image' => __('Check this box to show thumbnail images in the template.', 'wprss'),
                    'thumbnail_placement' => __('Choose how you want the title, thumbnail image, and excerpt to be displayed.', 'wprss'),
                    'thumbnail_width' => __('The thumbnail image\'s width in pixels.', 'wprss'),
                    'thumbnail_height' => __('The thumbnail image\'s height in pixels.', 'wprss'),
                    'empty_thumbnail_behavior' => __('How the template should adapt when a feed item has no thumbnail image available.', 'wprss'),
                ];
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 0.1
     */
    public function getExtensions()
    {
        return [
            /*
             * Enables the image options UI for controlling image importing.
             *
             * @since 0.1
             */
            'wpra/images/ui_enabled' => function () {
                return true;
            },
            /*
             * Enables logging during image importing.
             *
             * @since 0.1
             */
            'wpra/images/logging/enabled' => function () {
                return true;
            },
            /*
             * Enables the feature to import featured images.
             *
             * @since 0.1
             */
            'wpra/images/features/import_ft_images' => function () {
                return true;
            },
            /*
             * Enables the feature to restrict images by size.
             *
             * @since 0.1
             */
            'wpra/images/features/image_min_size' => function () {
                return true;
            },
            /*
             * Adds the tooltips for the new template types to the core tooltips array.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/model_tooltips' => function (ContainerInterface $c, $prev) {
                $prev['options'] = array_merge(
                    $c->get('wpra_tmp/templates/feeds/templates_model_options_tooltips'),
                    $prev['options']
                );

                return $prev;
            },
            /*
             * Adds the model options for the new template types to the core model schema.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/model_schema' => function (ContainerInterface $c, $prev) {
                $prev['options'] = array_merge(
                    $c->get('wpra_tmp/templates/feeds/grid_model_options'),
                    $c->get('wpra_tmp/templates/feeds/et_model_options'),
                    $prev['options']
                );

                return $prev;
            },
            /*
             * Feed template's fields options.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/template_options' => function (ContainerInterface $c, $prev) {
                return array_merge($prev, $c->get('wpra_tmp/templates/feeds/custom_template_options'));
            },
            /*
             * Extends the templates app state.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/admin/states/main' => function (ContainerInterface $c, $prev) {
                return array_merge($prev, $c->get('wpra_tmp/templates/states/raw'));
            },
            /*
             * Registers this addon's Twig templates path.
             *
             * @since 0.1
             */
            'wpra/twig/paths' => function (ContainerInterface $c, $prev) {
                $prev[] = $c->get('wpra_tmp/templates_dir_path');

                return $prev;
            },
            /*
             * Enables template type selection in the UI.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/template_type_enabled' => function (ContainerInterface $c, $prev) {
                return true;
            },
            /*
             * The list of template page assets.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/admin/assets' => function (ContainerInterface $c, $prev) {
                return array_merge($prev, $c->get('wpra_tmp/templates/feeds/grid_template_assets'));
            },
            /*
             * The list of Gutenberg block assets.
             *
             * @since 0.1
             */
            'wpra/gutenberg_block/assets' => function (ContainerInterface $c, $prev) {
                $prev['grid_template_style'] = $c->get('wpra_tmp/styles/grid_template');

                return $prev;
            },
            /*
             * The list of JS modules to load.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/admin/js_modules' => function (ContainerInterface $c, $prev) {
                $prev[] = 'templates-addon-app';

                return $prev;
            },
            /*
             * The available template types.
             *
             * @since 0.1
             */
            'wpra/feeds/templates/template_types' => function (ContainerInterface $c, $prev) {
                $prev['grid'] = $c->get('wpra_tmp/templates/feeds/grid_template_type');
                $prev['et'] = $c->get('wpra_tmp/templates/feeds/et_template_type');

                return $prev;
            },
        ];
    }
}
