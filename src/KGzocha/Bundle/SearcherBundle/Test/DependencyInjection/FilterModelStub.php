<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Model\FilterModel\FilterModelInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection
 */
class FilterModelStub implements FilterModelInterface
{
    /**
     * @inheritDoc
     */
    public function isImposed()
    {
        return true;
    }
}
