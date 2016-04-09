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
        $collectionConfig = $contextConfig['searcher'];
        if (!isset($collectionConfig['class'])
            && !isset($collectionConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'You have to specify "class" or "service" for '.
                'searcher in searching context "%s"',
                $contextId
            ));
        }

        if (isset($collectionConfig['class'])) {
            $definition = new Definition($collectionConfig['class']);
            if ($collectionConfig['class'] === Configuration::SEARCHER_CLASS) {
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

        if (isset($collectionConfig['service'])
            && !$container->hasDefinition($collectionConfig['service'])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for searcher in'.
                'searching context "%s" does not exist',
                $collectionConfig['service'],
                $contextId
            ));
        }

        return $container->setDefinition(
            sprintf('k_gzocha_searcher.%s.searcher', $contextId),
            $container->getDefinition($collectionConfig['service'])
        );
    }
}
