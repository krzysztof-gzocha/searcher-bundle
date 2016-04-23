<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection
 */
class Searcher implements ServiceDefinerInterface
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
        $searcherConfig = $contextConfig['searcher'];
        self::checkParameters($contextId, $searcherConfig);

        if (isset($searcherConfig['service'])) {
            self::checkServiceExists($container, $contextId, $searcherConfig);

            return $container->setDefinition(
                sprintf('k_gzocha_searcher.%s.searcher', $contextId),
                $container->getDefinition($searcherConfig['service'])
            );
        }

        $definition = new Definition($searcherConfig['class']);
        if ($searcherConfig['class'] === Configuration::SEARCHER_CLASS) {
            $definition
                ->addArgument($container->getDefinition(
                    sprintf('k_gzocha_searcher.%s.imposer_collection', $contextId)
                ))
                ->addArgument($container->getDefinition(
                    sprintf('k_gzocha_searcher.%s.context', $contextId)
                ));
        }

        return $container->setDefinition(
            sprintf('k_gzocha_searcher.%s.searcher', $contextId),
            $definition
        );
    }

    /**
     * @param string $contextId
     * @param array $searcherConfig
     */
    private static function checkParameters($contextId, array &$searcherConfig)
    {
        if (!isset($searcherConfig['class'])
            && !isset($searcherConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'You have to specify "class" or "service" for '.
                'searcher in searching context "%s"',
                $contextId
            ));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $contextId
     * @param array $searcherConfig
     */
    private static function checkServiceExists(
        ContainerBuilder $container,
        $contextId,
        array &$searcherConfig
    ) {
        if (!$container->hasDefinition($searcherConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for searcher in'.
                'searching context "%s" does not exist',
                $searcherConfig['service'],
                $contextId
            ));
        }
    }
}
