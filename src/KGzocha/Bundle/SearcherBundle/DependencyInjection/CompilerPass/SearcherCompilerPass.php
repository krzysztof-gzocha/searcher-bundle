<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class SearcherCompilerPass extends AbstractCompilerPass
{
    /**
     * @var ParametersValidator
     */
    private $parametersValidator;

    /**
     * @param DefinitionBuilder   $definitionBuilder
     * @param string              $servicePrefix
     * @param ParametersValidator $parametersValidator
     */
    public function __construct(
        DefinitionBuilder $definitionBuilder,
        $servicePrefix,
        ParametersValidator $parametersValidator
    ) {
        parent::__construct($definitionBuilder, $servicePrefix);
        $this->parametersValidator = $parametersValidator;
    }

    /**
     * {@inheritdoc}
     */
    protected function processContext(
        $contextId,
        array &$context,
        ContainerBuilder $container
    ) {
        $config = $context[self::SEARCHER_PARAMETER];
        $this->parametersValidator->validateParameters($contextId, $config);

        if (isset($config[self::SERVICE_PARAMETER])) {
            return $this->createFromService($container, $contextId, $config);
        }

        $definition = new Definition($config[self::CLASS_PARAMETER]);
        if (Configuration::SEARCHER_CLASS == $config[self::CLASS_PARAMETER]) {
            $definition
                ->addArgument($container->getDefinition(
                    $this->buildServiceName($contextId, self::BUILDER_COLLECTION_PARAMETER)
                ))
                ->addArgument($container->getDefinition(
                    $this->buildServiceName($contextId, self::CONTEXT_PARAMETER)
                ));
        }

        if (isset($config[self::WRAPPER_CLASS_PARAMETER])) {
            $wrapperDefinition = new Definition($config[self::WRAPPER_CLASS_PARAMETER]);
            $wrapperDefinition->addArgument($definition);

            return $container->setDefinition(
                $this->buildServiceName($contextId, self::SEARCHER_PARAMETER),
                $wrapperDefinition
            );
        }

        return $container->setDefinition(
            $this->buildServiceName($contextId, self::SEARCHER_PARAMETER),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $contextId
     * @param array            $config
     *
     * @return Definition
     */
    private function createFromService(
        ContainerBuilder $container,
        $contextId,
        array &$config
    ) {
        $this->checkIfServiceExists(
            $container,
            $contextId,
            $config[self::SERVICE_PARAMETER]
        );

        if (isset($config[self::WRAPPER_CLASS_PARAMETER])) {
            $definition = new Definition($config[self::WRAPPER_CLASS_PARAMETER]);
            $definition->addArgument(
                $container->getDefinition($config[self::SERVICE_PARAMETER])
            );

            return $container->setDefinition(
                $this->buildServiceName($contextId, self::SEARCHER_PARAMETER),
                $definition
            );
        }

        return $container->setDefinition(
            $this->buildServiceName($contextId, self::SEARCHER_PARAMETER),
            $container->getDefinition($config[self::SERVICE_PARAMETER])
        );
    }
}
