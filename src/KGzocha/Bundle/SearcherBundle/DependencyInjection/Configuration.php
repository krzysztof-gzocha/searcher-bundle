<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    const SEARCHER_CLASS = 'KGzocha\Searcher\Searcher\Searcher';
    const MODEL_COLLECTION_CLASS = 'KGzocha\Searcher\FilterModel\Collection\NamedFilterModelCollection';
    const IMPOSER_COLLECTION_CLASS = 'KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection';

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('k_gzocha_searcher');

        $rootNode
            ->canBeUnset(true)
            ->children()
                ->arrayNode('contexts')
                ->canBeUnset(true)
                ->useAttributeAsKey('context_id')
                ->prototype('array')
                ->children()
                    ->append($this->getModelCollectionConfiguration())
                    ->append($this->getImposerCollectionConfiguration())
                    ->append($this->getModelsConfiguration())
                    ->append($this->getImposersConfiguration())
                    ->append($this->getSearcherConfiguration())
                    ->append($this->getContextConfiguration())
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getModelCollectionConfiguration()
    {
        $node = new ArrayNodeDefinition('model_collection');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(self::MODEL_COLLECTION_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getImposerCollectionConfiguration()
    {
        $node = new ArrayNodeDefinition('imposer_collection');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(self::IMPOSER_COLLECTION_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getSearcherConfiguration()
    {
        $node = new ArrayNodeDefinition('searcher');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(self::SEARCHER_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getContextConfiguration()
    {
        $node = new ArrayNodeDefinition('context');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(null)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getModelsConfiguration()
    {
        $node = new ArrayNodeDefinition('models');

        $node
            ->prototype('array')
            ->children()
                ->scalarNode('class')->defaultValue(null)->end()
                ->scalarNode('service')->defaultValue(null)->end()
                ->scalarNode('name')->cannotBeEmpty()->isRequired()->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getImposersConfiguration()
    {
        $node = new ArrayNodeDefinition('imposers');

        $node
            ->prototype('array')
            ->children()
                ->scalarNode('class')->defaultValue(null)->end()
                ->scalarNode('service')->defaultValue(null)->end()
                ->scalarNode('name')->cannotBeEmpty()->isRequired()->end()
            ->end();

        return $node;
    }
}
