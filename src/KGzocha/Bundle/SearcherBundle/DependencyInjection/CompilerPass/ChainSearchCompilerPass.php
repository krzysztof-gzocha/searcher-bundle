<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class ChainSearchCompilerPass extends AbstractChainsCompilerPass
{
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
                self::SEARCHER_PARAMETER
            ),
            $paramConfig['chain_searcher']
        );
    }
}
