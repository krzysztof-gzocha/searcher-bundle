<?php

namespace KGzocha\Bundle\SearcherBundle\Searcher\Factory;

use KGzocha\Searcher\Context\SearchingContextInterface;
use KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollectionInterface;
use KGzocha\Searcher\Searcher;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
interface SearcherFactoryInterface
{
    /**
     * @param CriteriaBuilderCollectionInterface $builderCollection
     * @param SearchingContextInterface          $searchingContext
     *
     * @return Searcher
     */
    public function build(
        CriteriaBuilderCollectionInterface $builderCollection,
        SearchingContextInterface $searchingContext
    );
}
