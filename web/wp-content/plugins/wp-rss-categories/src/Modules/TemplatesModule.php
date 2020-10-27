<?php

namespace RebelCode\Wpra\Categories\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Categories\Templates\CategoriesFeedTemplate;
use RebelCode\Wpra\Core\Modules\ModuleInterface;

/**
 * The module that extends WP RSS Aggregator's template system.
 *
 * @since 1.3.3
 */
class TemplatesModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 1.3.3
     */
    public function run(ContainerInterface $c)
    {
    }

    /**
     * @inheritdoc
     *
     * @since 1.3.3
     */
    public function getFactories()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @since 1.3.3
     */
    public function getExtensions()
    {
        return [
            /*
             * Extends the core template with a decorator that can process categories in the context arg.
             *
             * @since 1.3.3
             */
            'wpra/feeds/templates/master_template' => function (ContainerInterface $c, $prev) {
                return new CategoriesFeedTemplate($prev, $c->get('wpra/feeds/templates/feed_item_collection'));
            },
        ];
    }
}
