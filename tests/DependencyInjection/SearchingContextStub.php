<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Context\SearchingContextInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class SearchingContextStub implements SearchingContextInterface
{
    /**
     * @inheritDoc
     */
    public function getQueryBuilder()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getResults()
    {
        return true;
    }
}
