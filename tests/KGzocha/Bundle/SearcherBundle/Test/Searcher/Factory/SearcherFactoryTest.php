<?php

namespace KGzocha\Bundle\SearcherBundle\Test\Searcher\Factory;

use KGzocha\Bundle\SearcherBundle\Searcher\Factory\SearcherFactory;
use KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearchingContextStub;
use KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollection;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\Searcher\Factory
 */
class SearcherFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildMethod()
    {
        $factory = new SearcherFactory();

        $this->assertInstanceOf(
            'KGzocha\Searcher\SearcherInterface',
            $factory->build(new CriteriaBuilderCollection(), new SearchingContextStub(true))
        );
    }
}
