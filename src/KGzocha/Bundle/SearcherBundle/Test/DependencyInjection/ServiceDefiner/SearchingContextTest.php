<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\SearchingContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 * @group di
 * @SuppressWarnings("static")
 */
class SearchingContextTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectionDefinedWithClass()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'context' => [
                'class' => '\\stdClass',
            ],
        ];

        SearchingContext::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.context'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.context')
        );
    }

    public function testCollectionDefinedWithService()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'context' => [
                'service' => 'user_defined_collection_service',
            ],
        ];
        $container->setDefinition(
            'user_defined_collection_service',
            new Definition('\\stdClass')
        );
        SearchingContext::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.context'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.context')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testDefinitionWithoutClassAndService()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'context' => [
                'wrong_param' => 'wrong_value',
            ],
        ];
        SearchingContext::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
    }
}
