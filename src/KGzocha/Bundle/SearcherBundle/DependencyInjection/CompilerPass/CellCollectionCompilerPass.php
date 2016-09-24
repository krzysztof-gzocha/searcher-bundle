<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CellCollectionCompilerPass extends AbstractChainsCompilerPass
{
    const CELL_COLLECTION = 'cell_collection';

    /**
     * @inheritDoc
     */
    protected function processParam(
        $contextId,
        array &$paramConfig,
        ContainerBuilder $container
    ) {
        $this->buildDefinition(
            $container,
            $contextId,
            $this->buildChainServiceName(
                $contextId,
                self::CELL_COLLECTION
            ),
            $paramConfig[self::CELL_COLLECTION]
        );
    }

}
