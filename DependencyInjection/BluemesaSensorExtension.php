<?php

namespace Bluemesa\Bundle\SensorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class BluemesaSensorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $now = new \DateTime();
        $container->setParameter(
            'date_now', $now->format('Y-m-d H:i:s')
        );
        $container->setParameter(
            'date_24_hours_ago', $now->sub(new \DateInterval("PT24H"))->format('Y-m-d H:i:s')
        );

    }
}
