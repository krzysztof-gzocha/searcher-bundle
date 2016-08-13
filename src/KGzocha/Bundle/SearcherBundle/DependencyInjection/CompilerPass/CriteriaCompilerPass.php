<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CriteriaCompilerPass extends AbstractContextCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function processContext(
        $contextId,
        array &$context,
        ContainerBuilder $container
    ) {
        foreach ($context[self::CRITERIA_PARAMETER] as &$criteria) {
            $definitionName = $this->processCriteria($contextId, $criteria, $container);

            $criteriaCollection = $this
                ->buildServiceName($contextId, self::CRITERIA_COLLECTION_PARAMETER);
            $container
                ->getDefinition($criteriaCollection)
                ->addMethodCall(
                    'addNamedCriteria',
                    [$criteria[self::NAME_PARAMETER], new Reference($definitionName)]
                );
        }
    }

    /**
     * @param string           $contextId
     * @param array            $criteria
     * @param ContainerBuilder $container
     *
     * @return string
     *
     * @throws InvalidDefinitionException
     */
    private function processCriteria(
        $contextId,
        array &$criteria,
        ContainerBuilder $container
    ) {
        $definitionName = $this->buildServiceName(
            $contextId,
            sprintf('%s.%s', self::CRITERIA_PARAMETER, $criteria[self::NAME_PARAMETER])
        );

        $this->buildDefinition($container, $contextId, $definitionName, $criteria);

        return $definitionName;
    }
}
