<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Criteria;
use KGzocha\Searcher\Criteria\Collection\NamedCriteriaCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 * @group di
 * @SuppressWarnings("static")
 */
class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    const CRITERIA_CLASS = '\KGzocha\Searcher\Criteria\TextCriteria';

    public function testCollectionDefinedWithClass()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'criteria' => [
                ['class' => self::CRITERIA_CLASS, 'name' => 'criteria1'],
                ['class' => self::CRITERIA_CLASS, 'name' => 'criteria2']
            ],
        ];

        Criteria::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.criteria.criteria1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.criteria.criteria2'
        ));
        $this->assertInstanceOf(
            self::CRITERIA_CLASS,
            $container->get('k_gzocha_searcher.test.criteria.criteria1')
        );
        $this->assertInstanceOf(
            self::CRITERIA_CLASS,
            $container->get('k_gzocha_searcher.test.criteria.criteria2')
        );

        /** @var NamedCriteriaCollectionInterface $collection */
        $collection = $container->get('k_gzocha_searcher.test.criteria_collection');
        $this->assertInstanceOf('\KGzocha\Searcher\Criteria\Collection\NamedCriteriaCollectionInterface', $collection);
        $this->assertCount(2, $collection->getCriteria());
        $this->assertInstanceOf(self::CRITERIA_CLASS, $collection->getNamedCriteria('criteria1'));
        $this->assertInstanceOf(self::CRITERIA_CLASS, $collection->getNamedCriteria('criteria2'));
    }

    public function testCollectionDefinedWithService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'criteria' => [
                ['service' => 'user_defined_criteria1', 'name' => 'criteria1'],
                ['service' => 'user_defined_criteria2', 'name' => 'criteria2']
            ],
        ];
        $container->setDefinition(
            'user_defined_criteria1',
            new Definition(self::CRITERIA_CLASS)
        );
        $container->setDefinition(
            'user_defined_criteria2',
            new Definition(self::CRITERIA_CLASS)
        );
        Criteria::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.criteria.criteria1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.criteria.criteria2'
        ));
        $this->assertInstanceOf(
            self::CRITERIA_CLASS,
            $container->get('k_gzocha_searcher.test.criteria.criteria1')
        );
        $this->assertInstanceOf(
            self::CRITERIA_CLASS,
            $container->get('k_gzocha_searcher.test.criteria.criteria2')
        );

        /** @var NamedCriteriaCollectionInterface $collection */
        $collection = $container->get('k_gzocha_searcher.test.criteria_collection');
        $this->assertInstanceOf('\KGzocha\Searcher\Criteria\Collection\NamedCriteriaCollectionInterface', $collection);
        $this->assertCount(2, $collection->getCriteria());
        $this->assertInstanceOf(self::CRITERIA_CLASS, $collection->getNamedCriteria('criteria1'));
        $this->assertInstanceOf(self::CRITERIA_CLASS, $collection->getNamedCriteria('criteria2'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testDefinitionWithoutClassAndService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'criteria' => [
                [
                    'no_name' => 'no_name',
                    'class' => 'bla',
                    'service' => 'bla',
                ]
            ],
        ];
        Criteria::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        $container = new ContainerBuilder();
        $container->setDefinition(
            'k_gzocha_searcher.test.criteria_collection',
            new Definition('\KGzocha\Searcher\Criteria\Collection\NamedCriteriaCollection')
        );

        return $container;
    }
}
