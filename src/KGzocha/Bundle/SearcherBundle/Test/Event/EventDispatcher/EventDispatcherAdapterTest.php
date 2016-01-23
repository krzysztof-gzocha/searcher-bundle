<?php

namespace KGzocha\Bundle\SearcherBundle\Test\Event\EventDispatcher;

use KGzocha\Bundle\SearcherBundle\Event\EventDispatcher\EventDispatcherAdapter;
use KGzocha\Bundle\SearcherBundle\Event\SearcherEventAdapter;
use KGzocha\Searcher\Event\SearcherEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\Event\EventDispatcher
 */
class EventDispatcherAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testCallingAdaptingEventDispatcher()
    {
        $symfonyDispatcher = $this
            ->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->getMock();

        $eventName = 'testingName';
        $eventObject = $this
            ->getMockBuilder('\KGzocha\Searcher\Event\SearcherEventInterface')
            ->getMock();

        /** @var SearcherEventInterface $eventObject */
        $symfonyDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->withConsecutive([
                $eventName,
                new SearcherEventAdapter($eventName, $eventObject)
            ]);

        /** @var EventDispatcherInterface $symfonyDispatcher */
        $adapter = new EventDispatcherAdapter($symfonyDispatcher);

        $adapter->dispatch($eventName, $eventObject);
    }
}
