<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection
 */
class SearchingContext implements ServiceDefinerInterface
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
        $collectionConfig = $contextConfig['context'];
        if (!isset($collectionConfig['class'])
            && !isset($collectionConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'You have to specify "class" or "service" for '.
                'context in searching context "%s"',
                $contextId
            ));
        }

        if (isset($collectionConfig['class'])) {
            return $container->setDefinition(
                sprintf('k_gzocha_searcher.%s.context', $contextId),
                new Definition($collectionConfig['class'])
            );
        }

        if (isset($collectionConfig['service'])
            && !$container->hasDefinition($collectionConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for context in'.
                'searching context "%s" does not exist',
                $collectionConfig['service'],
                $contextId
            ));
        }

        return $container->setDefinition(
            sprintf('k_gzocha_searcher.%s.context', $contextId),
            $container->getDefinition($collectionConfig['service'])
        );
    }
}
