<?php

namespace KGzocha\Bundle\SearcherBundle\Test;

use KGzocha\Searcher\Context\AbstractSearchingContext;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 */
class SearchingContextStub extends AbstractSearchingContext
{
    /**
     * @inheritDoc
     */
    public function getResults()
    {
        return true;
    }
}
