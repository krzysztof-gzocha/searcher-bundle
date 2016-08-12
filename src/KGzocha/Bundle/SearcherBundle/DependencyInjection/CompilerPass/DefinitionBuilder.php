<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class DefinitionBuilder
{
    const CLASS_PARAMETER = 'class';
    const SERVICE_PARAMETER = 'service';

    /**
     * @var ParametersValidator
     */
    private $parameterValidator;

    /**
     * @param ParametersValidator $parameterValidator
     */
    public function __construct(ParametersValidator $parameterValidator)
    {
        $this->parameterValidator = $parameterValidator;
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $contextId
     * @param string           $definitionName
     * @param array            $config
     *
     * @return Definition
     *
     * @throws InvalidDefinitionException
     */
    public function buildDefinition(
        ContainerBuilder $container,
        $contextId,
        $definitionName,
        array &$config
    ) {
        $this->parameterValidator->validateParameters($contextId, $config);

        if (isset($config[self::SERVICE_PARAMETER])) {
            return $this->buildFromService(
                $container,
                $definitionName,
                $config[self::SERVICE_PARAMETER]
            );
        }

        return $this->buildFromClass(
            $container,
            $definitionName,
            $config[self::CLASS_PARAMETER]
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $definitionName
     * @param string           $oldServiceName
     *
     * @return Definition
     */
    private function buildFromService(
        ContainerBuilder $container,
        $definitionName,
        $oldServiceName
    ) {
        if (!$container->hasDefinition($oldServiceName)) {
            throw new InvalidDefinitionException(sprintf(
                'Could not create "%s" service, because configured service "%s" does not exist.',
                $definitionName,
                $oldServiceName
            ));
        }

        return $container->setDefinition(
            $definitionName,
            $container->getDefinition($oldServiceName)
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $definitionName
     * @param string           $className
     *
     * @return Definition
     */
    private function buildFromClass(
        ContainerBuilder $container,
        $definitionName,
        $className
    ) {
        if (!class_exists($className)) {
            throw new InvalidDefinitionException(sprintf(
                'Could not create service "%s", because class "%s" does not exist.',
                $definitionName,
                $className
            ));
        }

        return $container->setDefinition(
            $definitionName,
            new Definition($className)
        );
    }
}
