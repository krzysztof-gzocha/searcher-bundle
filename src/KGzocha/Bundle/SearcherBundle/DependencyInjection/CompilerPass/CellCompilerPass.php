<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CellCompilerPass extends AbstractChainsCompilerPass
{
    /**
     * @inheritDoc
     */
    protected function processParam(
        $contextId,
        array &$paramConfig,
        ContainerBuilder $container
    ) {
        $cellDefinitions = [];
        foreach ($paramConfig['cells'] as &$cellConfig) {
            $cellDefinitions[] = $this->processCell($contextId, $cellConfig, $container);
        }

        $chainSearch = $container->getDefinition($this->buildChainServiceName(
            $contextId,
            self::SEARCHER_PARAMETER
        ));

        $chainSearch->addArgument($cellDefinitions);
    }

    /**
     * @param string           $contextId
     * @param array            $cellConfig
     * @param ContainerBuilder $container
     *
     * @return Definition
     */
    private function processCell($contextId, array &$cellConfig, ContainerBuilder $container)
    {
        $definitionName = $this->buildChainServiceName(
            $contextId,
            sprintf('cell.%s', $cellConfig[self::NAME_PARAMETER])
        );

        $definition = $this->buildDefinition(
            $container,
            $contextId,
            $definitionName,
            $cellConfig
        );

        if ($cellConfig[self::SERVICE_PARAMETER]) {
            return $definition;
        }

        return $this->configureDefinition($contextId, $cellConfig, $container, $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $contextId
     * @param array            $cellConfig
     *
     * @return Definition
     */
    private function getTransformerDefinition(
        ContainerBuilder $container,
        $contextId,
        array &$cellConfig
    ) {
        if (!$cellConfig[self::TRANSFORMER_PARAMETER]) {
            return new Definition(Configuration::END_TRANSFORMER_CLASS);
        }

        return $container->getDefinition($this->buildChainServiceName(
            $contextId,
            sprintf(
                '%s.%s',
                self::TRANSFORMER_PARAMETER,
                $cellConfig[self::TRANSFORMER_PARAMETER]
            )
        ));
    }

    /**
     * @param                  $contextId
     * @param array            $cellConfig
     * @param ContainerBuilder $container
     * @param Definition       $definition
     *
     * @return Definition
     */
    private function configureDefinition(
        $contextId,
        array &$cellConfig,
        ContainerBuilder $container,
        Definition $definition
    ) {
        // Add searcher as first argument
        $definition->addArgument($container->getDefinition(
            $this->buildServiceName($cellConfig[self::SEARCHER_PARAMETER], self::SEARCHER_PARAMETER)
        ));

        // Add transformer as second argument
        $definition->addArgument($this->getTransformerDefinition(
            $container, $contextId, $cellConfig
        ));

        // Add cell name as third argument
        $definition->addArgument($cellConfig[self::NAME_PARAMETER]);

        return $definition;
    }
}
