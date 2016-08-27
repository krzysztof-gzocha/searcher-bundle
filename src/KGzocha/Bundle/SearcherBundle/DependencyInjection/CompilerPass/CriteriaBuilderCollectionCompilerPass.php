<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CriteriaBuilderCollectionCompilerPass extends AbstractContextCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function processParam(
        $contextId,
        array &$paramConfig,
        ContainerBuilder $container
    ) {
        return $this->buildDefinition(
            $container,
            $contextId,
            $this->buildServiceName($contextId, self::BUILDER_COLLECTION_PARAMETER),
            $paramConfig[self::BUILDER_COLLECTION_PARAMETER]
        );
    }
}
