<?php

namespace KGzocha\Bundle\SearcherBundle\Searcher\Factory;

use KGzocha\Searcher\Context\SearchingContextInterface;
use KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollectionInterface;
use KGzocha\Searcher\Searcher\Searcher;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Searcher\Factory
 */
interface SearcherFactoryInterface
{
    /**
     * @param FilterImposerCollectionInterface $imposerCollection
     * @param SearchingContextInterface $searchingContext
     *
     * @return Searcher
     */
    public function build(
        FilterImposerCollectionInterface $imposerCollection,
        SearchingContextInterface $searchingContext
    );
}
