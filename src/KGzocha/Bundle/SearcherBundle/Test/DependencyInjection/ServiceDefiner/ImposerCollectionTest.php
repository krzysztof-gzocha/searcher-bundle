<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\ImposerCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 * @group di
 * @SuppressWarnings("static")
 */
class ImposerCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectionDefinedWithClass()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'imposer_collection' => [
                'class' => '\\stdClass',
            ],
        ];

        ImposerCollection::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.imposer_collection'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.imposer_collection')
        );
    }

    public function testCollectionDefinedWithService()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'imposer_collection' => [
                'service' => 'user_defined_collection_service',
            ],
        ];
        $container->setDefinition(
            'user_defined_collection_service',
            new Definition('\\stdClass')
        );
        ImposerCollection::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.imposer_collection'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.imposer_collection')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testDefinitionWithoutClassAndService()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'imposer_collection' => [
                'wrong_param' => 'wrong_value',
            ],
        ];
        ImposerCollection::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
    }
}
