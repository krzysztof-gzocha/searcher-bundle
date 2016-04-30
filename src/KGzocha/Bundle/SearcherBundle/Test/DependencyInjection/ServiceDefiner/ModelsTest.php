<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Models;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\ServiceDefiner
 * @group di
 * @SuppressWarnings("static")
 */
class ModelsTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectionDefinedWithClass()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'models' => [
                ['class' => '\\stdClass', 'name' => 'model1'],
                ['class' => '\\stdClass', 'name' => 'model2']
            ],
        ];

        Models::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.model.model1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.model.model2'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.model.model1')
        );
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.model.model2')
        );
    }

    public function testCollectionDefinedWithService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'models' => [
                ['service' => 'user_defined_model1', 'name' => 'model1'],
                ['service' => 'user_defined_model2', 'name' => 'model2']
            ],
        ];
        $container->setDefinition(
            'user_defined_model1',
            new Definition('\\stdClass')
        );
        $container->setDefinition(
            'user_defined_model2',
            new Definition('\\stdClass')
        );
        Models::defineServices(
            'test',
            $contextConfig,
            $container
        );

        $container->compile();
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.model.model1'
        ));
        $this->assertTrue($container->hasDefinition(
            'k_gzocha_searcher.test.model.model2'
        ));
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.model.model1')
        );
        $this->assertInstanceOf(
            '\\stdClass',
            $container->get('k_gzocha_searcher.test.model.model2')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testDefinitionWithoutClassAndService()
    {
        $container = $this->getContainer();
        $contextConfig = [
            'models' => [
                [
                    'no_name' => 'no_name',
                    'class' => 'bla',
                    'service' => 'bla',
                ]
            ],
        ];
        Models::defineServices(
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
            'k_gzocha_searcher.test.model_collection',
            new Definition('\KGzocha\Searcher\FilterModel\Collection\NamedFilterModelCollection')
        );

        return $container;
    }
}
