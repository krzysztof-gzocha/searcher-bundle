<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class TransformerCompilerPass extends AbstractChainsCompilerPass
{
    /**
     * @inheritDoc
     */
    protected function processParam(
        $contextId,
        array &$paramConfig,
        ContainerBuilder $container
    ) {
        foreach ($paramConfig['transformers'] as &$transformer) {
            $this->processTransformer($contextId, $transformer, $container);
        }
    }

    /**
     * @param string           $contextId
     * @param array            $transformer
     * @param ContainerBuilder $container
     */
    private function processTransformer(
        $contextId,
        array &$transformer,
        ContainerBuilder $container
    ) {
        $this->buildDefinition(
            $container,
            $contextId,
            $this->buildChainServiceName(
                $contextId,
                sprintf('%s.%s', self::TRANSFORMER_PARAMETER, $transformer[self::NAME_PARAMETER])
            ),
            $transformer
        );
    }
}
