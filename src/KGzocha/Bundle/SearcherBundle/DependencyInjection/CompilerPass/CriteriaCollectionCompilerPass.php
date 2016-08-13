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
    protected function processContext(
        $contextId,
        array &$context,
        ContainerBuilder $container
    ) {
        return $this->buildDefinition(
            $container,
            $contextId,
            $this->buildServiceName($contextId, self::CRITERIA_COLLECTION_PARAMETER),
            $context[self::CRITERIA_COLLECTION_PARAMETER]
        );
    }
}
