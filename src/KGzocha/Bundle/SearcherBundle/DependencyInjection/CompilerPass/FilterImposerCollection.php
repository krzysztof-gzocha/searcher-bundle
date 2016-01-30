<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Will search for services tagged with appropriate tag and populate them
 * with corresponding FilterImposers.
 *
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass
 */
class FilterImposerCollection extends AbstractCompilerPass
{
    /**
     * @var string
     */
    private $collectionTag;

    /**
     * @var string
     */
    private $imposerTag;

    /**
     * @var string
     */
    private $contextParameterName;

    /**
     * @param string $collectionTag
     * @param string $imposerTag
     * @param string $contextParameterName
     */
    public function __construct(
        $collectionTag,
        $imposerTag,
        $contextParameterName
    ) {
        $this->collectionTag = $collectionTag;
        $this->imposerTag = $imposerTag;
        $this->contextParameterName = $contextParameterName;
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $collections = $container
            ->findTaggedServiceIds($this->collectionTag);

        foreach ($collections as $collectionName => $collection) {
            $contextId = $this->getValueFromLastKey(
                $collection,
                $this->contextParameterName
            );
            $collectionDefinition = $container
                ->findDefinition($collectionName);

            $this->addModelsToCollection(
                $container,
                $collectionDefinition,
                $contextId
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition $collectionDefinition
     * @param string $collectionContextId
     */
    private function addModelsToCollection(
        ContainerBuilder $container,
        Definition $collectionDefinition,
        $collectionContextId
    ) {
        $models = $container->findTaggedServiceIds($this->imposerTag);

        if (0 === count($models)) {
            throw new \RuntimeException(sprintf(
                'There is no FilterImposers to be injected with contextId "%s"',
                $collectionContextId
            ));
        }

        foreach ($models as $definitionName => $model) {
            $modelContextId = $this->getValueFromLastKey(
                $model,
                $this->contextParameterName
            );

            if ($modelContextId !== $collectionContextId) {
                continue;
            }

            $collectionDefinition->addMethodCall(
                'addFilterImposer',
                [new Reference($definitionName)]
            );
        }
    }
}
