<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    const FACTORY_SERVICE = 'kgzocha.searcher_bundle.searcher_factory';

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('k_gzocha_searcher');

        $rootNode
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('factory_service')
                    ->defaultValue(self::FACTORY_SERVICE)
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
