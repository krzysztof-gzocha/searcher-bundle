<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
abstract class AbstractChainsCompilerPass extends AbstractContextCompilerPass
{
    /**
     * @inheritDoc
     */
    protected function getParamToBeProcessed()
    {
        return 'chains';
    }

    /**
     * @param string $contextId
     * @param string $name
     *
     * @return string
     */
    protected function buildChainServiceName($contextId, $name)
    {
        return sprintf(
            '%s.chains.%s.%s',
            $this->servicePrefix,
            $contextId,
            $name
        );
    }
}
