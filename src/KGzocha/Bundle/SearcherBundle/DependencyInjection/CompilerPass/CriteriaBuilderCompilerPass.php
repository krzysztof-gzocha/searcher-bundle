<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CriteriaBuilderCompilerPass extends AbstractCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function processContext(
        $contextId,
        array &$context,
        ContainerBuilder $container
    ) {
        foreach ($context['builders'] as &$builder) {
            $definitionName = $this->processCriteriaBuilder($contextId, $builder, $container);

            $criteriaCollection = $this
                ->buildServiceName($contextId, self::BUILDER_COLLECTION_PARAMETER);
            $container
                ->getDefinition($criteriaCollection)
                ->addMethodCall(
                    'addCriteriaBuilder',
                    [new Reference($definitionName)]
                );
        }
    }

    /**
     * @param string           $contextId
     * @param array            $builder
     * @param ContainerBuilder $container
     *
     * @return string
     *
     * @throws InvalidDefinitionException
     */
    private function processCriteriaBuilder(
        $contextId,
        array &$builder,
        ContainerBuilder $container
    ) {
        $this->validateParameters($contextId, $builder);
        $definitionName = $this->buildServiceName(
            $contextId,
            sprintf('builder.%s', $builder[self::NAME_PARAMETER])
        );

        $this->buildDefinition($container, $contextId, $definitionName, $builder);

        return $definitionName;
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $contextId
     * @param string           $definitionName
     * @param array            $builder
     *
     * @return Definition
     */
    private function buildDefinition(
        ContainerBuilder $container,
        $contextId,
        $definitionName,
        array &$builder
    ) {
        if (isset($builder[self::SERVICE_PARAMETER])) {
            $this->checkIfServiceExists(
                $container,
                $contextId,
                $builder[self::SERVICE_PARAMETER]
            );

            return $container->setDefinition(
                $definitionName,
                $container->getDefinition($builder[self::SERVICE_PARAMETER])
            );
        }

        // Build from class
        return $container->setDefinition(
            $definitionName,
            new Definition($builder[self::CLASS_PARAMETER])
        );
    }
}
