<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\ImposerCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Imposers;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\ModelCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Models;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Searcher;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\SearchingContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection
 * @SuppressWarnings("static")
 */
class KGzochaSearcherExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');
        $this->defineContexts($config, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function defineContexts(array &$config, ContainerBuilder $container)
    {
        foreach ($config['contexts'] as $contextId => &$context) {
            ModelCollection::defineServices(
                $contextId, $context, $container
            );
            ImposerCollection::defineServices(
                $contextId, $context, $container
            );
            Models::defineServices(
                $contextId, $context, $container
            );
            Imposers::defineServices(
                $contextId, $context, $container
            );
            SearchingContext::defineServices(
                $contextId, $context, $container
            );
            Searcher::defineServices(
                $contextId, $context, $container
            );
        }
    }
}
