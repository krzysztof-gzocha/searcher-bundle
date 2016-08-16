<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Chain\TransformerInterface;
use KGzocha\Searcher\Criteria\Collection\CriteriaCollectionInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class TransformerStub implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($results, CriteriaCollectionInterface $criteria)
    {
        return $results;
    }

    /**
     * @inheritDoc
     */
    public function skip($results)
    {
        return false;
    }
}
