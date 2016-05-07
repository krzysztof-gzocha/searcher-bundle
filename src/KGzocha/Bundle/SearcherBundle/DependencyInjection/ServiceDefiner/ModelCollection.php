<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection
 */
class ModelCollection implements ServiceDefinerInterface
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
        $collectionConfig = $contextConfig['model_collection'];
        self::checkCollectionParameters($contextId, $collectionConfig);

        if (isset($collectionConfig['service'])) {
            self::checkServiceExists(
                $container,
                $contextId,
                $collectionConfig
            );

            return $container->setDefinition(
                sprintf('k_gzocha_searcher.%s.model_collection', $contextId),
                $container->getDefinition($collectionConfig['service'])
            );
        }

        return $container->setDefinition(
            sprintf('k_gzocha_searcher.%s.model_collection', $contextId),
            new Definition($collectionConfig['class'])
        );
    }

    /**
     * @param string $contextId
     * @param array $collectionConfig
     */
    private static function checkCollectionParameters(
        $contextId,
        array &$collectionConfig
    ) {
        if (!isset($collectionConfig['class'])
            && !isset($collectionConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'You have to specify "class" or "service" for '.
                'model_collection in searching context "%s"',
                $contextId
            ));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $contextId
     * @param array $collectionConfig
     */
    private static function checkServiceExists(
        ContainerBuilder $container,
        $contextId,
        array &$collectionConfig
    ) {
        if (!$container->hasDefinition($collectionConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for model_collection in'.
                'searching context "%s" does not exist',
                $collectionConfig['service'],
                $contextId
            ));
        }
    }
}
