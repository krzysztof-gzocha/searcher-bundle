<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Context\SearchingContextInterface;
use KGzocha\Searcher\FilterImposer\FilterImposerInterface;
use KGzocha\Searcher\Model\FilterModel\FilterModelInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection
 */
class FilterImposerStub implements FilterImposerInterface
{
    /**
     * @inheritDoc
     */
    public function imposeFilter(
        FilterModelInterface $filterModel,
        SearchingContextInterface $searchingContext
    ) {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function supportsModel(
        FilterModelInterface $filterModel
    ) {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function supportsSearchingContext(
        SearchingContextInterface $searchingContext
    ) {
        return true;
    }
}
