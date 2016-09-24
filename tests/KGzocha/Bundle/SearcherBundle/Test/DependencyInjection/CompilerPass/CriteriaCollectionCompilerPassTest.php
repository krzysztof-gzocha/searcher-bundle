<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class CriteriaCollectionCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testCompilingWithClass()
    {
        $compiler = new CriteriaCollectionCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\Criteria\Collection\CriteriaCollection';
        $container = $this->getContainer($this->getConfig(
            ['class' => $class]
        ));
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.criteria_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.people.criteria_collection');
        $this->assertInstanceOf(
            $class,
            $builderCollection
        );
    }

    public function testCompilingWithDefaults()
    {
        $compiler = new CriteriaCollectionCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\Criteria\Collection\CriteriaCollection';
        $container = $this->getContainer($this->getConfig());
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.criteria_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.people.criteria_collection');
        $this->assertInstanceOf(
            $class,
            $builderCollection
        );
    }

    public function testCompilingWithServiceName()
    {
        $compiler = new CriteriaCollectionCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\Criteria\Collection\CriteriaCollection';
        $container = $this->getContainer($this->getConfig(
            ['service' => 'criteria_collection_service', 'class' => 'does-not-matter']
        ));
        $container->setDefinition(
            'criteria_collection_service',
            new Definition($class)
        );
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.criteria_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.people.criteria_collection');
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
     * @param $criteriaCollection
     *
     * @return array
     */
    private function getConfig($criteriaCollection = null)
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

        if (!$criteriaCollection) {
            return $result;
        }

        $result['k_gzocha_searcher']['contexts']['people']['criteria_collection'] = $criteriaCollection;

        return $result;
    }
}
