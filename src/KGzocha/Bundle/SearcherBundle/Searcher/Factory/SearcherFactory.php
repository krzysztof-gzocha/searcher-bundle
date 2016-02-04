<?php

namespace KGzocha\Bundle\SearcherBundle\Searcher\Factory;

use KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollectionInterface;
use KGzocha\Searcher\Searcher\Searcher;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Searcher\Factory
 */
class SearcherFactory implements SearcherFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function build(
        FilterImposerCollectionInterface $imposerCollection
    ) {
        return new Searcher($imposerCollection);
    }
}
