<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Searcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 * @group di
 * @SuppressWarnings("static")
 */
class SearcherTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectionDefinedWithClass()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'searcher' => [
                'class' => '\\stdClass',
            ],
        ];

        Searcher::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.searcher'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.searcher')
        );
    }

    public function testCollectionDefinedWithService()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'searcher' => [
                'service' => 'user_defined_collection_service',
            ],
        ];
        $container->setDefinition(
            'user_defined_collection_service',
            new Definition('\\stdClass')
        );
        Searcher::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.searcher'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.searcher')
        );
    }
    
    public function testIfArgumentsArePassed()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'searcher' => [
                'class' => Configuration::SEARCHER_CLASS,
            ],
        ];
        $container->setDefinition(
            'k_gzocha_searcher.test.imposer_collection',
            new Definition('KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection')
        );
        $container->setDefinition(
            'k_gzocha_searcher.test.context',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\SearchingContextStub', [true])
        );
        Searcher::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.searcher'
        ));
        $this->assertInstanceOf(
            Configuration::SEARCHER_CLASS,
            $container->get('k_gzocha_searcher.test.searcher')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testDefinitionWithoutClassAndService()
    {
        $container = new ContainerBuilder();
        $contextConfig = [
            'searcher' => [
                'wrong_param' => 'wrong_value',
            ],
        ];
        Searcher::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
    }
}
