<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class SearchingContextCompilerPass extends AbstractContextCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function processContext(
        $contextId,
        array &$context,
        ContainerBuilder $container
    ) {
        return $this->buildDefinition(
            $container,
            $contextId,
            $this->buildServiceName($contextId, self::CONTEXT_PARAMETER),
            $context[self::CONTEXT_PARAMETER]
        );
    }
}
