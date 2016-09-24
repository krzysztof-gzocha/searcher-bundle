<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Criteria\CriteriaInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection
 */
class CriteriaStub implements CriteriaInterface
{
    /**
     * @inheritDoc
     */
    public function shouldBeApplied()
    {
        return true;
    }
}
