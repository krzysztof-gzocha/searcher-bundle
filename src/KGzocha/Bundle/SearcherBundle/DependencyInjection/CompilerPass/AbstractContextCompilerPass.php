<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
abstract class AbstractContextCompilerPass implements CompilerPassInterface
{
    const CLASS_PARAMETER = 'class';
    const SERVICE_PARAMETER = 'service';
    const NAME_PARAMETER = 'name';
    const TRANSFORMER_PARAMETER = 'transformer';

    const CRITERIA_COLLECTION_PARAMETER = 'criteria_collection';
    const BUILDER_COLLECTION_PARAMETER = 'builder_collection';
    const CRITERIA_PARAMETER = 'criteria';
    const CONTEXT_PARAMETER = 'context';
    const SEARCHER_PARAMETER = 'searcher';
    const WRAPPER_CLASS_PARAMETER = 'wrapper_class';

    /**
     * @var DefinitionBuilder
     */
    private $definitionBuilder;

    /**
     * @var string
     */
    protected $servicePrefix;

    /**
     * @param DefinitionBuilder $definitionBuilder
     * @param string            $servicePrefix
     */
    public function __construct(DefinitionBuilder $definitionBuilder, $servicePrefix)
    {
        $this->definitionBuilder = $definitionBuilder;
        $this->servicePrefix = $servicePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $param = sprintf('%s.%s', $this->servicePrefix, $this->getParamToBeProcessed());
        $contexts = $container->getParameter($param);

        foreach ($contexts as $contextId => &$context) {
            $this->processParam($contextId, $context, $container);
        }
    }

    /**
     * @param string           $contextId
     * @param array            $paramConfig
     * @param ContainerBuilder $container
     */
    abstract protected function processParam(
        $contextId,
        array &$paramConfig,
        ContainerBuilder $container
    );

    /**
     * @return string
     */
    protected function getParamToBeProcessed()
    {
        return 'contexts';
    }

    /**
     * @param string $contextId
     * @param string $name
     *
     * @return string
     */
    protected function buildServiceName($contextId, $name)
    {
        return sprintf(
            '%s.%s.%s',
            $this->servicePrefix,
            $contextId,
            $name
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $contextId
     * @param string           $definitionName
     * @param array            $config
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    protected function buildDefinition(
        ContainerBuilder $container,
        $contextId,
        $definitionName,
        array &$config
    ) {
        return $this
            ->definitionBuilder
            ->buildDefinition(
                $container,
                $contextId,
                $definitionName,
                $config
            );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $contextId
     * @param string           $serviceName
     *
     * @return bool
     *
     * @throws InvalidDefinitionException
     */
    protected function checkIfServiceExists(
        ContainerBuilder $container,
        $contextId,
        $serviceName
    ) {
        if (!$container->hasDefinition($serviceName)) {
            throw new InvalidDefinitionException(sprintf(
                'Service "%s" configured in searching context "%s" does not exist',
                $serviceName,
                $contextId
            ));
        }
    }
}
