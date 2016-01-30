<?php

namespace KGzocha\Bundle\SearcherBundle\Searcher\Factory;

use KGzocha\Searcher\Event\Dispatcher\EventDispatcherInterface;
use KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection;
use KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollectionInterface;
use KGzocha\Searcher\FilterImposer\FilterImposerInterface;
use KGzocha\Searcher\Searcher\Searcher;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Searcher\Factory
 */
class SearcherFactory implements SearcherFactoryInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function build(
        FilterImposerCollectionInterface $filterImposerCollection
    ) {
        return new Searcher(
            $filterImposerCollection,
            $this->eventDispatcher
        );
    }
}
