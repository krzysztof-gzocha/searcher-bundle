<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will search for service configurations of NamedFilterModelCollection
 * in order to populate them with corresponding FilterModels
 *
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass
 */
class NamedFilterModelCollection extends AbstractCompilerPass
{
    const MODEL_NAME = 'modelName';

    /**
     * @var string name of a tag for FilterModelCollection
     */
    private $modelCollectionTag;

    /**
     * @var string name of a tag for FilterModel
     */
    private $filterModelTag;

    /**
     * @var string name of a context parameter
     */
    private $contextParameterName;

    /**
     * @param string $modelCollectionTag
     * @param string $filterModelTag
     * @param string $contextParameterName
     */
    public function __construct(
        $modelCollectionTag,
        $filterModelTag,
        $contextParameterName
    ) {
        $this->modelCollectionTag = $modelCollectionTag;
        $this->filterModelTag = $filterModelTag;
        $this->contextParameterName = $contextParameterName;
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $modelCollections = $container
            ->findTaggedServiceIds($this->modelCollectionTag);

        foreach ($modelCollections as $definitionName => $modelCollection) {
            $contextId = $this->getValueFromLastKey(
                $modelCollection,
                $this->contextParameterName
            );
            $collectionDefinition = $container
                ->findDefinition($definitionName);

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
        $models = $container->findTaggedServiceIds($this->filterModelTag);

        if (0 === count($models)) {
            throw new \RuntimeException(sprintf(
                'There is no FilterModels to be injected with contextId "%s"',
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
                'addNamedFilterModel',
                [
                    $this->getValueFromLastKey($model, self::MODEL_NAME),
                    new Reference($definitionName)
                ]
            );
        }
    }
}
