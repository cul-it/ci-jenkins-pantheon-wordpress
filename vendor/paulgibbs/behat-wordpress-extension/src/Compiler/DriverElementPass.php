<?php
namespace PaulGibbs\WordpressBehatExtension\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * WordpressBehatExtension container compilation pass.
 */
class DriverElementPass implements CompilerPassInterface
{
    /**
     * Modify the container before Symfony compiles it.
     *
     * @param ContainerBuilder $container
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function process(ContainerBuilder $container)
    {
        $wordpress = $container->getDefinition('wordpress.wordpress');
        $driver    = $container->getParameter('wordpress.wordpress.default_driver');

        if (! $wordpress || ! $driver) {
            return;
        }

        foreach ($container->findTaggedServiceIds('wordpress.element') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (! isset($attribute['alias'], $attribute['driver'])) {
                    continue;
                }

                if ($attribute['driver'] !== $driver) {
                    continue;
                }

                $wordpress->addMethodCall('registerDriverElement', [$attribute['alias'], new Reference($id)]);
            }
        }
    }
}
