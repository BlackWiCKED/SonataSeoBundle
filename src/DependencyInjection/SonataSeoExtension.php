<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\SeoBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @final since sonata-project/seo-bundle 2.14
 *
 * This is the class that loads and manages your bundle configuration.
 */
class SonataSeoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $config = $this->fixConfiguration($config);

        $bundles = $container->getParameter('kernel.bundles');

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($bundles['SonataBlockBundle'], $bundles['KnpMenuBundle'])) {
            $loader->load('blocks.xml');
        }

        $loader->load('event.xml');
        $loader->load('services.xml');
        $loader->load('commands.xml');

        $this->configureSeoPage($config['page'], $container);
        $this->configureSitemap($config['sitemap'], $container);
        $this->configureHttpClient($container, $config['http']);

        $container->getDefinition('sonata.seo.twig.extension')
            ->replaceArgument(1, $config['encoding']);
    }

    /**
     * Configure the default seo page.
     */
    protected function configureSeoPage(array $config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.seo.config', $config);
    }

    /**
     * Configure the sitemap source manager.
     */
    protected function configureSitemap(array $config, ContainerBuilder $container)
    {
        $source = $container->getDefinition('sonata.seo.sitemap.manager');

        $source->setShared(false);

        foreach ($config['doctrine_orm'] as $pos => $sitemap) {
            // define the connectionIterator
            $connectionIteratorId = 'sonata.seo.source.doctrine_connection_iterator_'.$pos;

            $connectionIterator = new Definition('%sonata.seo.exporter.database_source_iterator.class%', [
                new Reference($sitemap['connection']),
                $sitemap['query'],
            ]);

            $connectionIterator->setPublic(false);
            $container->setDefinition($connectionIteratorId, $connectionIterator);

            // define the sitemap proxy iterator
            $sitemapIteratorId = 'sonata.seo.source.doctrine_sitemap_iterator_'.$pos;

            $sitemapIterator = new Definition('%sonata.seo.exporter.sitemap_source_iterator.class%', [
                new Reference($connectionIteratorId),
                new Reference('router'),
                $sitemap['route'],
                $sitemap['parameters'],
            ]);

            $sitemapIterator->setPublic(false);

            $container->setDefinition($sitemapIteratorId, $sitemapIterator);

            $source->addMethodCall('addSource', [$sitemap['group'], new Reference($sitemapIteratorId), $sitemap['types']]);
        }

        foreach ($config['services'] as $service) {
            $source->addMethodCall('addSource', [$service['group'], new Reference($service['id']), $service['types']]);
        }
    }

    /**
     * Fix the sitemap configuration.
     *
     * @return array
     */
    protected function fixConfiguration(array $config)
    {
        foreach ($config['sitemap']['doctrine_orm'] as $pos => $sitemap) {
            $sitemap['group'] = $sitemap['group'] ?? false;
            $sitemap['types'] = $sitemap['types'] ?? [];
            $sitemap['connection'] = $sitemap['connection'] ?? 'doctrine.dbal.default_connection';
            $sitemap['route'] = $sitemap['route'] ?? false;
            $sitemap['parameters'] = $sitemap['parameters'] ?? false;
            $sitemap['query'] = $sitemap['query'] ?? false;

            if (false === $sitemap['route']) {
                throw new \RuntimeException('Route cannot be empty, please review the sonata_seo.sitemap configuration');
            }

            if (false === $sitemap['query']) {
                throw new \RuntimeException('Query cannot be empty, please review the sonata_seo.sitemap configuration');
            }

            if (false === $sitemap['parameters']) {
                throw new \RuntimeException('Route\'s parameters cannot be empty, please review the sonata_seo.sitemap configuration');
            }

            $config['sitemap']['doctrine_orm'][$pos] = $sitemap;
        }

        foreach ($config['sitemap']['services'] as $pos => $sitemap) {
            if (!\is_array($sitemap)) {
                $sitemap = [
                    'group' => false,
                    'types' => [],
                    'id' => $sitemap,
                ];
            } else {
                $sitemap['group'] = $sitemap['group'] ?? false;
                $sitemap['types'] = $sitemap['types'] ?? [];

                if (!isset($sitemap['id'])) {
                    throw new \RuntimeException('Service id must to be defined, please review the sonata_seo.sitemap configuration');
                }
            }

            $config['sitemap']['services'][$pos] = $sitemap;
        }

        return $config;
    }

    private function configureHttpClient(ContainerBuilder $container, array $config): void
    {
        if (null === $config['client'] || null === $config['message_factory']) {
            return;
        }

        $container->setAlias('sonata.seo.http.client', $config['client']);
        $container->setAlias('sonata.seo.http.message_factory', $config['message_factory']);
    }
}
