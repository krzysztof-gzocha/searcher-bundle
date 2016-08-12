<?php

namespace KGzocha\Bundle\SearcherBundle\Context;

use KGzocha\Searcher\Context\Doctrine\QueryBuilderSearchingContext;
use KGzocha\Searcher\Context\SearchingContextInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class KnpPaginatorAdapter implements SearchingContextInterface
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var QueryBuilderSearchingContext
     */
    private $searchingContext;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array
     */
    private $options;

    /**
     * @param PaginatorInterface           $paginator
     * @param QueryBuilderSearchingContext $searchingContext
     */
    public function __construct(
        PaginatorInterface $paginator,
        QueryBuilderSearchingContext $searchingContext
    ) {
        $this->paginator = $paginator;
        $this->searchingContext = $searchingContext;
        $this->page = 1;
        $this->limit = 50;
        $this->options = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder()
    {
        return $this->searchingContext->getQueryBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->paginator->paginate(
            $this->getQueryBuilder(),
            $this->page,
            $this->limit,
            $this->options
        );
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }
}
