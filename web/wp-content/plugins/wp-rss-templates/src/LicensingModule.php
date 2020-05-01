<?php

namespace RebelCode\Wpra\Templates;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Licensing\Addon;
use RebelCode\Wpra\Core\Modules\ModuleInterface;

/**
 * The licensing module for the WP RSS Aggregator - Templates addon.
 *
 * @since 0.1
 */
class LicensingModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 0.1
     */
    public function run(ContainerInterface $c)
    {
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
             * The addon instance, used by the licensing system.
             *
             * @since 0.1
             */
            'wpra_tmp/licensing/addon' => function (ContainerInterface $c) {
                return new Addon(
                    $c->get('wpra_tmp/licensing/info/item_key'),
                    $c->get('wpra_tmp/licensing/info/item_name'),
                    $c->get('wpra_tmp/licensing/info/version'),
                    $c->get('wpra_tmp/licensing/info/file_path'),
                    $c->get('wpra_tmp/licensing/info/store_url')
                );
            },
            /*
             * The addon's licensing key.
             *
             * @since 0.1
             */
            'wpra_tmp/licensing/info/item_key' => function () {
                return 'tmp';
            },
            /*
             * The addon's licensing name.
             *
             * @since 0.1
             */
            'wpra_tmp/licensing/info/item_name' => function () {
                return 'Templates';
            },
            /*
             * The addon's version.
             *
             * @since 0.1
             */
            'wpra_tmp/licensing/info/version' => function (ContainerInterface $c) {
                if ($c->has('wpra_tmp/version')) {
                    return $c->get('wpra_tmp/version');
                }

                return '0.0';
            },
            /*
             * The path to the addon's plugin file.
             *
             * @since 0.1
             */
            'wpra_tmp/licensing/info/file_path' => function (ContainerInterface $c) {
                if ($c->has('wpra_tmp/plugin_file_path')) {
                    return $c->get('wpra_tmp/plugin_file_path');
                }

                return WPRSS_TEMPLATES;
            },
            /*
             * The URL to the licensing store to use for this addon.
             *
             * @since 0.1
             */
            'wpra_tmp/licensing/info/store_url' => function () {
                return 'https://www.wprssaggregator.com/edd-sl-api/';
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
             * Registers this addon with WP RSS Aggregator.
             *
             * @since 0.1
             */
            'wpra/addons' => function (ContainerInterface $c, $addons) {
                $addons[] = $c->get('wpra_tmp/licensing/addon');

                return $addons;
            },
        ];
    }
}
