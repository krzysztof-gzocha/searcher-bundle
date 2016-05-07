<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Imposers;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 * @group di
 * @SuppressWarnings("static")
 */
class ImposersTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectionDefinedWithClass()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'imposers' => [
                ['class' => '\\stdClass', 'name' => 'imposer1'],
                ['class' => '\\stdClass', 'name' => 'imposer2']
            ],
        ];

        Imposers::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.imposer.imposer1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.imposer.imposer2'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.imposer.imposer1')
        );
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.imposer.imposer2')
        );
    }

    public function testCollectionDefinedWithService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'imposers' => [
                ['service' => 'user_defined_imposer1', 'name' => 'imposer1'],
                ['service' => 'user_defined_imposer2', 'name' => 'imposer2']
            ],
        ];
        $container->setDefinition(
            'user_defined_imposer1',
            new Definition('\\stdClass')
        );
        $container->setDefinition(
            'user_defined_imposer2',
            new Definition('\\stdClass')
        );
        Imposers::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.imposer.imposer1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.imposer.imposer2'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.imposer.imposer1')
        );
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.imposer.imposer2')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testDefinitionWithoutClassAndService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'imposers' => [
                [
                    'no_name' => 'no_name',
                    'class' => 'bla',
                    'service' => 'bla',
                ]
            ],
        ];
        Imposers::defineServices(
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
            'k_gzocha_searcher.test.imposer_collection',
            new Definition('\KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection')
        );

        return $container;
    }
}
