<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Searcher\Chain\CellInterface;
use KGzocha\Searcher\Chain\EndTransformer;
use KGzocha\Searcher\Chain\TransformerInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CellStub implements CellInterface
{
    /**
     * @inheritDoc
     */
    public function getSearcher()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getTransformer()
    {
        return new EndTransformer();
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'someName';
    }

    /**
     * @inheritDoc
     */
    public function hasTransformer()
    {
        return true;
    }
}
