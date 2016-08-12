<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
abstract class AbstractCompilerPass implements CompilerPassInterface
{
    const CLASS_PARAMETER = 'class';
    const SERVICE_PARAMETER = 'service';
    const NAME_PARAMETER = 'name';

    const CRITERIA_COLLECTION_PARAMETER = 'criteria_collection';
    const BUILDER_COLLECTION_PARAMETER = 'builder_collection';
    const CRITERIA_PARAMETER = 'criteria';
    const CONTEXT_PARAMETER = 'context';
    const SEARCHER_PARAMETER = 'searcher';
    const WRAPPER_CLASS_PARAMETER = 'wrapper_class';

    /**
     * @var ParametersValidator
     */
    private $parametersValidator;

    /**
     * @var string
     */
    private $servicePrefix;

    /**
     * @param ParametersValidator $parametersValidator
     * @param string              $servicePrefix
     */
    public function __construct(
        ParametersValidator $parametersValidator,
        $servicePrefix
    ) {
        $this->parametersValidator = $parametersValidator;
        $this->servicePrefix = $servicePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $contextParam = 'k_gzocha_searcher.contexts';
        $contexts = $container->getParameter($contextParam);

        foreach ($contexts as $contextId => &$context) {
            $this->processContext($contextId, $context, $container);
        }
    }

    /**
     * @param string           $contextId
     * @param array            $context
     * @param ContainerBuilder $container
     */
    abstract protected function processContext(
        $contextId,
        array &$context,
        ContainerBuilder $container
    );

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
     * @param string $contextId
     * @param array  $config
     *
     * @return bool
     *
     * @throws InvalidDefinitionException
     */
    protected function validateParameters(
        $contextId,
        array &$config
    ) {
        return $this
            ->parametersValidator
            ->validateParameters($contextId, $config);
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
