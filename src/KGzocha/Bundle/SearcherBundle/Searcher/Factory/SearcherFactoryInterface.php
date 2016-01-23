<?php
namespace KGzocha\Bundle\SearcherBundle\Searcher\Factory;

use KGzocha\Searcher\FilterImposer\FilterImposerInterface;
use KGzocha\Searcher\Searcher\Searcher;
use KGzocha\Searcher\Searcher\SearcherInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Searcher\Factory
 */
interface SearcherFactoryInterface
{
    /**
     * @return SearcherInterface
     */
    public function build();

    /**
     * @param FilterImposerInterface $filterImposer
     */
    public function addFilterImposer(FilterImposerInterface $filterImposer);
}
