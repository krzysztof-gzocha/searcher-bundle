<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Context\SearchingContextInterface;
use KGzocha\Searcher\Criteria\CriteriaInterface;
use KGzocha\Searcher\CriteriaBuilder\CriteriaBuilderInterface;
use KGzocha\Searcher\FilterImposer\FilterImposerInterface;
use KGzocha\Searcher\FilterModel\FilterModelInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection
 */
class CriteriaBuilderStub implements CriteriaBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function buildCriteria(
        CriteriaInterface $criteria,
        SearchingContextInterface $searchingContext
    ) {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function allowsCriteria(CriteriaInterface $criteria)
    {
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
