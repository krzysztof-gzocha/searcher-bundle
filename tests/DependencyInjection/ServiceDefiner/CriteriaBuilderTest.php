<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 * @group di
 * @SuppressWarnings("static")
 */
class CriteriaBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectionDefinedWithClass()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'builders' => [
                ['class' => '\\stdClass', 'name' => 'builder1'],
                ['class' => '\\stdClass', 'name' => 'builder2']
            ],
        ];

        CriteriaBuilder::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.builder.builder1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.builder.builder2'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.builder.builder1')
        );
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.builder.builder2')
        );
    }

    public function testCollectionDefinedWithService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'builders' => [
                ['service' => 'user_defined_builder1', 'name' => 'builder1'],
                ['service' => 'user_defined_builder2', 'name' => 'builder2']
            ],
        ];
        $container->setDefinition(
            'user_defined_builder1',
            new Definition('\\stdClass')
        );
        $container->setDefinition(
            'user_defined_builder2',
            new Definition('\\stdClass')
        );
        CriteriaBuilder::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.builder.builder1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.builder.builder2'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.builder.builder1')
        );
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.builder.builder2')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testDefinitionWithoutClassAndService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'builders' => [
                [
                    'no_name' => 'no_name',
                    'class' => 'bla',
                    'service' => 'bla',
                ]
            ],
        ];
        CriteriaBuilder::defineServices(
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
            'k_gzocha_searcher.test.builder_collection',
            new Definition('\KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollection')
        );

        return $container;
    }
}
