<?php

namespace KGzocha\Bundle\SearcherBundle\Event;

use KGzocha\Searcher\Event\SearcherEventInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Event
 */
class SearcherEventAdapter extends Event
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var SearcherEventInterface
     */
    private $searcherEvent;

    /**
     * @param string $name
     * @param SearcherEventInterface $event
     */
    public function __construct($name, SearcherEventInterface $event)
    {
        $this->name = $name;
        $this->searcherEvent = $event;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return SearcherEventInterface
     */
    public function getSearcherEvent()
    {
        return $this->searcherEvent;
    }
}
