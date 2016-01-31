<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use KGzocha\Searcher\Searcher\Searcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will simplify creation of searcher service.
 * It will look for services tagged with appropriate tag and call method
 * build() on them with corresponding FilterImposerCollection, so end user
 * will not need to even remember class name, services names, and so on.
 * Services compiled by this class should return Searcher instances.
 *
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass
 */
class SearcherFactoryCompilerPass extends AbstractCompilerPass
{
    const SEARCHER_FACTORY_SERVICE = 'kgzocha.searcher_bundle.searcher_factory';

    /**
     * @var string
     */
    private $factoryTag;

    /**
     * @var string
     */
    private $imposerCollectionTag;

    /**
     * @var string
     */
    private $contextIdParamName;

    /**
     * @param string $factoryTag
     * @param string $imposerCollectionTag
     * @param string $contextIdParamName
     */
    public function __construct(
        $factoryTag,
        $imposerCollectionTag,
        $contextIdParamName
    ) {
        $this->factoryTag = $factoryTag;
        $this->imposerCollectionTag = $imposerCollectionTag;
        $this->contextIdParamName = $contextIdParamName;
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $factories = $container
            ->findTaggedServiceIds($this->factoryTag);
        $imposerCollections = $container
            ->findTaggedServiceIds($this->imposerCollectionTag);

        if (!empty($factories) && empty($imposerCollections)) {
            throw new \RuntimeException(sprintf(
                'There are %d factories registered by tag "%s",' .
                'but no FilterImposerCollections with tag "%s"',
                count($factories),
                $this->factoryTag,
                $this->imposerCollectionTag
            ));
        }

        foreach ($factories as $factoryName => $factory) {
            $factoryContext = $this
                ->getValueFromLastKey($factory, $this->contextIdParamName);

            $imposerCollection = $this->findImposerCollection(
                $container,
                $factoryContext
            );

            $container
                ->getDefinition($factoryName)
                ->setClass(Searcher::class)
                ->setFactory([
                    new Reference(self::SEARCHER_FACTORY_SERVICE),
                    'build'
                ])
                ->setArguments([new Reference($imposerCollection)]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $contextId
     *
     * @return int|string
     */
    private function findImposerCollection(
        ContainerBuilder $container,
        $contextId
    ) {
        $imposerCollections = $container
            ->findTaggedServiceIds($this->imposerCollectionTag);

        foreach ($imposerCollections as $collectionName => $imposerCollection) {
            $collectionContextId = $this->getValueFromLastKey(
                $imposerCollection,
                $this->contextIdParamName
            );

            if ($contextId === $collectionContextId) {
                return $collectionName;
            }
        }

        throw new \RunTimeException(sprintf(
            'There is no FilterImposerCollection tagged with tag "%s" and %s=%s',
            $this->imposerCollectionTag,
            $this->contextIdParamName,
            $contextId
        ));
    }
}
