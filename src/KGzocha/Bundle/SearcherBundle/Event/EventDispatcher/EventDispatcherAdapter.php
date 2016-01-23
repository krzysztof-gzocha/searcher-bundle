<?php

namespace KGzocha\Bundle\SearcherBundle\Event\EventDispatcher;

use KGzocha\Bundle\SearcherBundle\Event\SearcherEventAdapter;
use KGzocha\Searcher\Event\SearcherEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyDispatcherInterface;
use KGzocha\Searcher\Event\Dispatcher\EventDispatcherInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Event\EventDispatcher
 */
class EventDispatcherAdapter implements EventDispatcherInterface
{
    /**
     * @var SymfonyDispatcherInterface
     */
    private $symfonyDispatcher;

    /**
     * @param SymfonyDispatcherInterface $dispatcher
     */
    public function __construct(SymfonyDispatcherInterface $dispatcher)
    {
        $this->symfonyDispatcher = $dispatcher;
    }

    /**
     * @param string $name
     * @param SearcherEventInterface $event
     */
    public function dispatch($name, SearcherEventInterface $event)
    {
        $this->symfonyDispatcher->dispatch(
            $name,
            $this->adaptSearcherEvent($name, $event)
        );
    }

    /**
     * @param string $name
     * @param SearcherEventInterface $event
     *
     * @return SearcherEventAdapter
     */
    private function adaptSearcherEvent($name, SearcherEventInterface $event)
    {
        return new SearcherEventAdapter($name, $event);
    }
}
