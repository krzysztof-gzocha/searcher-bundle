<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class SearchingContextCompilerPass extends AbstractCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function processContext(
        $contextId,
        array &$context,
        ContainerBuilder $container
    ) {
        $config = $context[self::CONTEXT_PARAMETER];
        $this->validateParameters($contextId, $config);

        if (isset($config[self::SERVICE_PARAMETER])) {
            $this->checkIfServiceExists(
                $container,
                $contextId,
                $config[self::SERVICE_PARAMETER]
            );

            return $container->setDefinition(
                $this->buildServiceName($contextId, self::CONTEXT_PARAMETER),
                $container->getDefinition($config[self::SERVICE_PARAMETER])
            );
        }

        return $container->setDefinition(
            $this->buildServiceName($contextId, self::CONTEXT_PARAMETER),
            new Definition($config[self::CLASS_PARAMETER])
        );
    }
}
