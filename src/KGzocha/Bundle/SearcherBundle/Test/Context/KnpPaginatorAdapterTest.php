<?php

namespace KGzocha\Bundle\SearcherBundle\Test\Context;

use KGzocha\Bundle\SearcherBundle\Context\KnpPaginatorAdapter;
use KGzocha\Searcher\Context\QueryBuilderSearchingContext;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\Context
 */
class KnpPaginatorAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testGetResultMethod()
    {
        $queryBuilder = $this->getQueryBuilder();
        $page = 5;
        $limit =26;
        $options = ['some' => 'options'];

        $searchingContext = new QueryBuilderSearchingContext(
            $queryBuilder
        );

        $adapter = new KnpPaginatorAdapter(
            $this->getPaginator($queryBuilder, $page, $limit, $options),
            $searchingContext
        );
        $adapter->setLimit($limit);
        $adapter->setPage($page);
        $adapter->setOptions($options);
        $this->assertEquals($queryBuilder, $adapter->getQueryBuilder());
        $this->assertTrue($adapter->getResults());
    }

    private function getPaginator(
        $queryBuider, $page, $limit, array $options
    ) {
        $paginator = $this
            ->getMockBuilder('Knp\Component\Pager\PaginatorInterface')
            ->getMockForAbstractClass();

        $paginator
            ->expects($this->once())
            ->method('paginate')
            ->withConsecutive([$queryBuider, $page, $limit, $options])
            ->willReturn(true);

        return $paginator;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getQueryBuilder()
    {
        return $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
