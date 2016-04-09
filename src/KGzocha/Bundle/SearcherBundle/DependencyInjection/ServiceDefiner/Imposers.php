<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection
 */
class Imposers implements ServiceDefinerInterface
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
        foreach ($contextConfig['imposers'] as &$model) {
            static::defineModel($contextId, $model, $container);
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
                'At least one imposer is missing name parameter'.
                ' in searching context "%s"',
                $contextId
            ));
        }

        $definitionName = sprintf(
            'k_gzocha_searcher.%s.imposer.%s',
            $contextId,
            $model['name']
        );

        if (!isset($model['class'])
            && !isset($model['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'You have to specify "class" or "service" for '.
                'all imposers in searching context "%s"',
                $contextId
            ));
        }

        if (isset($model['class'])) {
            $definition = $container->setDefinition(
                $definitionName,
                new Definition($model['class'])
            );
            static::addToCollection($container, $contextId, $definitionName);

            return $definition;
        }

        if (isset($model['service'])
            && !$container->hasDefinition($model['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for imposer in'.
                'searching context "%s" does not exist',
                $model['service'],
                $contextId
            ));
        }

        $definition = $container->setDefinition(
            $definitionName,
            $container->getDefinition($model['service'])
        );
        static::addToCollection($container, $contextId, $definitionName);

        return $definition;
    }

    /**
     * @param ContainerBuilder $container
     * @param $contextId
     * @param $name
     */
    private static function addToCollection(
        ContainerBuilder $container,
        $contextId,
        $name
    ) {
        $container
            ->getDefinition(sprintf(
                'k_gzocha_searcher.%s.imposer_collection',
                $contextId
            ))
            ->addMethodCall(
                'addFilterImposer',
                [new Reference($name)]
            );
    }
}
