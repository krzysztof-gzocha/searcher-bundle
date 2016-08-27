<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CriteriaCollectionCompilerPass extends AbstractContextCompilerPass
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
            $this->buildServiceName($contextId, self::CRITERIA_COLLECTION_PARAMETER),
            $paramConfig[self::CRITERIA_COLLECTION_PARAMETER]
        );
    }
}
