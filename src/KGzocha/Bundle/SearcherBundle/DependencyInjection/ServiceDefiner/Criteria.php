<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class Criteria implements ServiceDefinerInterface
{
    /**
     * @param $contextId
     * @param array $contextConfig
     * @param ContainerBuilder $container
     *
     * @return Definition
     */
    public static function defineServices(
        $contextId,
        array &$contextConfig,
        ContainerBuilder $container
    ) {
        foreach ($contextConfig['criteria'] as &$model) {
            self::defineModel($contextId, $model, $container);
        }
    }

    /**
     * @param $contextId
     * @param array $model
     * @param ContainerBuilder $container
     *
     * @return Definition
     */
    private static function defineModel(
        $contextId,
        array &$model,
        ContainerBuilder $container
    ) {
        if (!isset($model['name'])) {
            throw new InvalidDefinitionException(sprintf(
                'At least one model is missing name parameter'.
                ' in searching context "%s"',
                $contextId
            ));
        }

        $definitionName = sprintf(
            'k_gzocha_searcher.%s.criteria.%s',
            $contextId,
            $model['name']
        );

        self::checkParameters($contextId, $model);

        if (isset($model['service'])) {
            self::checkServiceExists($container, $contextId, $model);
            $definition = $container->setDefinition(
                $definitionName,
                $container->getDefinition($model['service'])
            );
            self::addToCollection($container, $contextId, $model['name'], $definitionName);

            return $definition;
        }

        $definition = $container->setDefinition(
            $definitionName,
            new Definition($model['class'])
        );
        self::addToCollection($container, $contextId, $model['name'], $definitionName);

        return $definition;
    }

    /**
     * @param string $contextId
     * @param array $model
     */
    private static function checkParameters($contextId, array &$model)
    {
        if (!isset($model['class'])
            && !isset($model['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'You have to specify "class" or "service" for '.
                'all criteria in searching context "%s"',
                $contextId
            ));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string$contextId
     * @param array $model
     */
    private static function checkServiceExists(
        ContainerBuilder $container,
        $contextId,
        array &$model
    ) {
        if (!$container->hasDefinition($model['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for criteria in'.
                'searching context "%s" does not exist',
                $model['service'],
                $contextId
            ));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $contextId
     * @param string           $name
     * @param string           $definitionName
     */
    private static function addToCollection(
        ContainerBuilder $container,
        $contextId,
        $name,
        $definitionName
    ) {
        $container
            ->getDefinition(sprintf(
                'k_gzocha_searcher.%s.criteria_collection',
                $contextId
            ))
            ->addMethodCall(
                'addNamedCriteria',
                [$name, new Reference($definitionName)]
            );
    }
}
