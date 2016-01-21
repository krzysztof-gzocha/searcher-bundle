<?php

namespace KGzocha\Bundle\SearcherBundle\Event\EventDispatcher;

use KGzocha\Searcher\Event\SearcherEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyDispatcherInterface;
use KGzocha\Searcher\Event\Dispatcher\EventDispatcherInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Event\EventDispatcher
 */
class EventDispatcher implements EventDispatcherInterface
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
        $this->symfonyDispatcher->dispatch($name, $event);
    }
}
