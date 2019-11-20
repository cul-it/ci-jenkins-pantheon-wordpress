<?php

namespace RebelCode\Wpra\Categories\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Modules\ModuleInterface;

class AddonModule implements ModuleInterface
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
        return [];
    }
}
