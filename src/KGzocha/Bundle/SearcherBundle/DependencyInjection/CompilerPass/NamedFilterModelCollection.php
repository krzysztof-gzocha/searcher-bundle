<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
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
class NamedFilterModelCollection implements CompilerPassInterface
{
    const CONTEXT_ID = 'contextId';
    const MODEL_NAME = 'modelName';

    /**
     * @var string name of a tag for FilterModelCollection
     */
    private $filterModelCollectionTag;

    /**
     * @var string name of a tag for FilterModel
     */
    private $filterModelTag;

    /**
     * @param string $filterModelCollectionTag
     * @param string $filterModelTag
     */
    public function __construct(
        $filterModelCollectionTag,
        $filterModelTag
    ) {
        $this->filterModelCollectionTag = $filterModelCollectionTag;
        $this->filterModelTag = $filterModelTag;
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $filterModelCollections = $container
            ->findTaggedServiceIds($this->filterModelCollectionTag);

        foreach ($filterModelCollections as $definitionName => $filterModelCollection) {
            $contextId = $this->getValueFromLastKey(
                $filterModelCollection,
                self::CONTEXT_ID
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
                self::CONTEXT_ID
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

    /**
     * @param array $array
     * @param string $key
     *
     * @return string
     */
    private function getValueFromLastKey(array &$array, $key)
    {
        $lastElement = end($array);
        if (!is_array($lastElement) || !array_key_exists($key, $lastElement)) {
            throw new \RuntimeException(sprintf(
                'Parameter %s is missing from configuration. Array: "%s"',
                $key,
                print_r($array, true)
            ));
        }

        return $lastElement[$key];
    }
}
