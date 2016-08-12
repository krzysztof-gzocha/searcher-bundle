<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class CriteriaBuilderCollectionCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     * @expectedExceptionMessageRegExp /^Service "non-existing-service" configured in searching context "people" does not exist/
     */
    public function testWithWrongService()
    {
        $compiler = new CriteriaBuilderCollectionCompilerPass(
            new ParametersValidator(),
            'k_gzocha_searcher'
        );
        $container = $this->getContainer($this->getConfig(
            ['service' => 'non-existing-service']
        ));
        $container->addCompilerPass($compiler);
        $container->compile();
    }

    public function testCompilingWithClass()
    {
        $compiler = new CriteriaBuilderCollectionCompilerPass(
            new ParametersValidator(),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollection';
        $container = $this->getContainer($this->getConfig(
            ['class' => $class]
        ));
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.builder_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.people.builder_collection');
        $this->assertInstanceOf(
            $class,
            $builderCollection
        );
    }

    public function testCompilingWithDefaults()
    {
        $compiler = new CriteriaBuilderCollectionCompilerPass(
            new ParametersValidator(),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollection';
        $container = $this->getContainer($this->getConfig());
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.builder_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.people.builder_collection');
        $this->assertInstanceOf(
            $class,
            $builderCollection
        );
    }

    public function testCompilingWithServiceName()
    {
        $compiler = new CriteriaBuilderCollectionCompilerPass(
            new ParametersValidator(),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollection';
        $container = $this->getContainer($this->getConfig(
            ['service' => 'builder_service', 'class' => 'does-not-matter']
        ));
        $container->setDefinition(
            'builder_service',
            new Definition($class)
        );
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.builder_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.people.builder_collection');
        $this->assertInstanceOf(
            $class,
            $builderCollection
        );
    }

    /**
     * @param array $config
     *
     * @return ContainerBuilder
     */
    private function getContainer(array $config)
    {
        $container = new ContainerBuilder();
        $extension = new KGzochaSearcherExtension();
        $extension->load($config, $container);

        return $container;
    }

    /**
     * @param $builderCollection
     * @return array
     */
    private function getConfig($builderCollection = null)
    {
        $result = [
            'k_gzocha_searcher' => [
                'contexts' => [
                    'people' => [
                        'context' => [
                            'service' => 'my_context',
                        ],
                        'criteria' => [
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaStub',
                                'name' => 'model1',
                            ],
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaStub',
                                'name' => 'model2',
                            ],
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaStub',
                                'name' => 'model3',
                            ]
                        ],
                        'builders' => [
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaBuilderStub',
                                'name' => 'imposer1',
                            ],
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaBuilderStub',
                                'name' => 'imposer2',
                            ]
                        ],
                    ],
                ],
            ],
        ];

        if (!$builderCollection) {
            return $result;
        }

        $result['k_gzocha_searcher']['contexts']['people']['builder_collection'] = $builderCollection;

        return $result;
    }
}
