<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaBuilderCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Criteria;
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
            CriteriaCollection::defineServices(
                $contextId, $context, $container
            );
            CriteriaBuilderCollection::defineServices(
                $contextId, $context, $container
            );
            Criteria::defineServices(
                $contextId, $context, $container
            );
            CriteriaBuilder::defineServices(
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
