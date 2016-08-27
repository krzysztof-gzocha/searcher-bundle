<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class Configuration implements ConfigurationInterface
{
    const SEARCHER_CLASS = 'KGzocha\Searcher\Searcher';
    const WRAPPER_CLASS = 'KGzocha\Searcher\WrappedResultsSearcher';
    const CRITERIA_COLLECTION_CLASS = 'KGzocha\Searcher\Criteria\Collection\NamedCriteriaCollection';
    const BUILDER_COLLECTION_CLASS = 'KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollection';
    const END_TRANSFORMER_CLASS = 'KGzocha\Searcher\Chain\EndTransformer';
    const CHAIN_SEARCHER_CLASS = 'KGzocha\Searcher\Chain\ChainSearch';
    const CELL_CLASS = '\KGzocha\Searcher\Chain\Cell';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('k_gzocha_searcher');

        $rootNode
            ->canBeUnset(true)
            ->children()
                ->append($this->getContextsNode())
                ->append($this->getChainsNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getContextsNode()
    {
        $node = new ArrayNodeDefinition('contexts');

        $node
            ->canBeUnset(true)
            ->useAttributeAsKey('context_id')
            ->prototype('array')
            ->children()
                    ->append($this->getCriteriaCollectionNode())
                    ->append($this->getBuilderCollectionNode())
                    ->append($this->getCriteriaNode())
                    ->append($this->getBuildersNode())
                    ->append($this->getSearcher())
                    ->append($this->getSearchingContextNode())
                ->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getChainsNode()
    {
        $node = new ArrayNodeDefinition('chains');

        $node
            ->canBeUnset(true)
            ->useAttributeAsKey('chain_id')
            ->prototype('array')
            ->children()
                ->append($this->getChainSearcherNode())
                ->append($this->getTransformersNode())
                ->append($this->getCellsNode())
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getCriteriaCollectionNode()
    {
        $node = new ArrayNodeDefinition('criteria_collection');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(self::CRITERIA_COLLECTION_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getBuilderCollectionNode()
    {
        $node = new ArrayNodeDefinition('builder_collection');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(self::BUILDER_COLLECTION_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getSearcher()
    {
        $node = new ArrayNodeDefinition('searcher');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(self::SEARCHER_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
                ->scalarNode('wrapper_class')->defaultValue(self::WRAPPER_CLASS)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getSearchingContextNode()
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
    protected function getCriteriaNode()
    {
        $node = new ArrayNodeDefinition('criteria');

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
    protected function getBuildersNode()
    {
        $node = new ArrayNodeDefinition('builders');

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
    protected function getChainSearcherNode()
    {
        $node = new ArrayNodeDefinition('chain_searcher');

        $node
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('class')->defaultValue(self::CHAIN_SEARCHER_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getTransformersNode()
    {
        $node = new ArrayNodeDefinition('transformers');

        $node
            ->prototype('array')
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->isRequired()->end()
                ->scalarNode('service')->defaultValue(null)->end()
                ->scalarNode('class')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getCellsNode()
    {
        $node = new ArrayNodeDefinition('cells');

        $node
            ->prototype('array')
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->isRequired()->end()
                ->scalarNode('searcher')->cannotBeEmpty()->isRequired()->end()
                ->scalarNode('transformer')->defaultNull()->end()
                ->scalarNode('class')->defaultValue(self::CELL_CLASS)->end()
                ->scalarNode('service')->defaultValue(null)->end()
            ->end();

        return $node;
    }
}
