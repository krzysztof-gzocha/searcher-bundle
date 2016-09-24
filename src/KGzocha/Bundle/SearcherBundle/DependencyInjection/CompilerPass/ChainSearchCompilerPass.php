<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class ChainSearchCompilerPass extends AbstractChainsCompilerPass
{
    const CHAIN_SEARCHER = 'chain_searcher';

    /**
     * @inheritDoc
     */
    protected function processParam(
        $contextId,
        array &$paramConfig,
        ContainerBuilder $container
    ) {
        $definition = $this->buildDefinition(
            $container,
            $contextId,
            $this->buildChainServiceName(
                $contextId,
                self::SEARCHER_PARAMETER
            ),
            $paramConfig[self::CHAIN_SEARCHER]
        );

        $this->addCellCollection($contextId, $paramConfig, $container, $definition);
    }

    /**
     * @param string           $contextId
     * @param array            $paramConfig
     * @param ContainerBuilder $container
     * @param Definition       $chainSearcher
     */
    private function addCellCollection(
        $contextId,
        array &$paramConfig,
        ContainerBuilder $container,
        Definition $chainSearcher
    ) {
        if ($paramConfig[self::CHAIN_SEARCHER]['service']) {
            return;
        }

        if (Configuration::CHAIN_SEARCHER_CLASS !== $paramConfig[self::CHAIN_SEARCHER]['class']) {
            return;
        }

        $cellCollection = $container->getDefinition($this->buildChainServiceName(
            $contextId,
            CellCollectionCompilerPass::CELL_COLLECTION
        ));

        $chainSearcher->addArgument($cellCollection);
    }
}
