<?php

namespace KGzocha\Bundle\SearcherBundle\Test\Searcher\Factory;

use KGzocha\Bundle\SearcherBundle\Searcher\Factory\SearcherFactory;
use KGzocha\Searcher\Event\Dispatcher\EventDispatcherInterface;
use KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\Searcher\Factory
 */
class SearcherFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildMethod()
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->getEventDispatcher();
        $factory = new SearcherFactory($eventDispatcher);

        $this->assertInstanceOf(
            'KGzocha\Searcher\Searcher\SearcherInterface',
            $factory->build(new FilterImposerCollection())
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEventDispatcher()
    {
        return $this
            ->getMockBuilder('KGzocha\Searcher\Event\Dispatcher\EventDispatcherInterface')
            ->getMockForAbstractClass();
    }
}
