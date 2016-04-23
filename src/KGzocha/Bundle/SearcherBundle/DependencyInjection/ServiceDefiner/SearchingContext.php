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
        $config = $contextConfig['context'];
        self::checkParameters($contextId, $config);

        if (isset($config['service'])) {
            self::checkServiceExists($container, $contextId, $config);

            return $container->setDefinition(
                sprintf('k_gzocha_searcher.%s.context', $contextId),
                $container->getDefinition($config['service'])
            );
        }

        return $container->setDefinition(
            sprintf('k_gzocha_searcher.%s.context', $contextId),
            new Definition($config['class'])
        );
    }

    /**
     * @param $contextId
     * @param array $config
     */
    private static function checkParameters($contextId, array &$config)
    {
        if (!isset($config['class'])
            && !isset($config['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'You have to specify "class" or "service" for '.
                'context in searching context "%s"',
                $contextId
            ));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $contextId
     * @param array $config
     */
    private static function checkServiceExists(
        ContainerBuilder $container,
        $contextId,
        array &$config
    ) {
        if (!$container->hasDefinition($config['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for context in'.
                'searching context "%s" does not exist',
                $config['service'],
                $contextId
            ));
        }
    }
}
