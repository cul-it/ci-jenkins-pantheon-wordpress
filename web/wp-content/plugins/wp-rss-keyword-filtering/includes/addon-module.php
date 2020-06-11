<?php

use Dhii\Output\TemplateInterface;
use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RebelCode\Wpra\Core\Modules\ModuleInterface;

// Do not continue if WPRA Core is not active or not at least v4.13
if (!defined('WPRSS_VERSION') || version_compare(WPRSS_VERSION, '4.13', '<')) {
    return;
}

/**
 * A module that extends the WP RSS Aggregator master template.
 *
 * @since 1.6.2
 */
class ShortcodeFilterModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 1.6.2
     */
    public function getFactories()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @since 1.6.2
     */
    public function getExtensions()
    {
        return [
            /*
             * Extends the core master template with a decorator that can handle keyword filtering context args.
             *
             * @since 1.6.2
             */
            'wpra/templates/feeds/master_template' => function (ContainerInterface $c, $prev) {
                return new FilteringMasterFeedTemplate($prev, $c->get('wpra/templates/feeds/feed_item_collection'));
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 1.6.2
     */
    public function run(ContainerInterface $c)
    {
    }
}

/**
 * A decorator for the master template that handlers the Keyword filtering "filter" argument.
 *
 * @since 1.6.2
 */
class FilteringMasterFeedTemplate implements TemplateInterface
{
    /**
     * The inner template.
     *
     * @since 1.6.2
     *
     * @var TemplateInterface
     */
    protected $inner;

    /**
     * The collection of feed items.
     *
     * @since 1.6.2
     *
     * @var CollectionInterface
     */
    protected $itemsCollection;

    /**
     * Constructor.
     *
     * @since 1.6.2
     *
     * @param TemplateInterface   $inner           The inner template.
     * @param CollectionInterface $itemsCollection The collection of feed items.
     */
    public function __construct($inner, $itemsCollection)
    {
        $this->inner           = $inner;
        $this->itemsCollection = $itemsCollection;
    }

    /**
     * @inheritdoc
     *
     * @since 1.6.2
     */
    public function render($ctx = null)
    {
        $arrCtx = (is_array($ctx) || is_object($ctx))
            ? (array)$ctx
            : $ctx;

        $filter = isset($arrCtx['filter'])
            ? $arrCtx['filter']
            : null;

        $items = (empty($ctx['items']) || !($ctx['items'] instanceof CollectionInterface))
            ? $this->itemsCollection
            : $ctx['items'];

        if ($filter !== null) {
            $ctx['items'] = $items->filter(['s' => $filter]);
        }

        return $this->inner->render($ctx);
    }
}

// Loads the module
add_filter('wpra_plugin_modules', function ($modules) {
    $modules['feeds_shortcode_filtering'] = new ShortcodeFilterModule();

    return $modules;
});
