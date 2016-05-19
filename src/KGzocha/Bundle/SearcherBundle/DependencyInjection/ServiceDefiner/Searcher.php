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
    const WRAPPER_CLASS_KEY = 'wrapper_class';
    const CLASS_KEY = 'class';
    const SERVICE_KEY = 'service';

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

        if (isset($searcherConfig[self::SERVICE_KEY])) {
            return self::createFromService($container, $contextId, $searcherConfig);
        }

        $definition = new Definition($searcherConfig[self::CLASS_KEY]);
        if ($searcherConfig[self::CLASS_KEY] === Configuration::SEARCHER_CLASS) {
            $definition
                ->addArgument($container->getDefinition(
                    sprintf('k_gzocha_searcher.%s.builder_collection', $contextId)
                ))
                ->addArgument($container->getDefinition(
                    sprintf('k_gzocha_searcher.%s.context', $contextId)
                ));
        }

        if (self::shouldWrap($searcherConfig)) {
            $wrapperDefinition = new Definition($searcherConfig[self::WRAPPER_CLASS_KEY]);
            $wrapperDefinition->addArgument($definition);

            return $container->setDefinition(
                sprintf('k_gzocha_searcher.%s.searcher', $contextId),
                $wrapperDefinition
            );
        }

        return $container->setDefinition(
            sprintf('k_gzocha_searcher.%s.searcher', $contextId),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param                  $contextId
     * @param                  $searcherConfig
     *
     * @return Definition
     */
    private static function createFromService(
        ContainerBuilder $container,
        $contextId,
        $searcherConfig
    ) {
        self::checkServiceExists($container, $contextId, $searcherConfig);

        if (self::shouldWrap($searcherConfig)) {
            $definition = new Definition($searcherConfig[self::WRAPPER_CLASS_KEY]);
            $definition->addArgument($container->getDefinition($searcherConfig[self::SERVICE_KEY]));
            return $container->setDefinition(
                sprintf('k_gzocha_searcher.%s.searcher', $contextId),
                $definition
            );
        }

        return $container->setDefinition(
            sprintf('k_gzocha_searcher.%s.searcher', $contextId),
            $container->getDefinition($searcherConfig[self::SERVICE_KEY])
        );
    }

    /**
     * @param string $contextId
     * @param array $searcherConfig
     */
    private static function checkParameters($contextId, array &$searcherConfig)
    {
        if (!isset($searcherConfig[self::CLASS_KEY])
            && !isset($searcherConfig[self::SERVICE_KEY])) {
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
        if (!$container->hasDefinition($searcherConfig[self::SERVICE_KEY])) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured for searcher in'.
                'searching context "%s" does not exist',
                $searcherConfig[self::SERVICE_KEY],
                $contextId
            ));
        }
    }

    /**
     * @param array $config
     *
     * @return bool
     */
    private static function shouldWrap(array &$config)
    {
        return isset($config[self::WRAPPER_CLASS_KEY])
            && $config[self::WRAPPER_CLASS_KEY]
            && class_exists($config[self::WRAPPER_CLASS_KEY]);
    }
}
