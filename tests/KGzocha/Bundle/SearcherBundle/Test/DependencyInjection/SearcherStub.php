<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Criteria\Collection\CriteriaCollectionInterface;
use KGzocha\Searcher\SearcherInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class SearcherStub implements SearcherInterface
{
    /**
     * @inheritDoc
     */
    public function search(
        CriteriaCollectionInterface $criteriaCollection
    ) {
        return true;
    }
}
