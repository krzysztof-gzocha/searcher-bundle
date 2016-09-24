<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class CellCollectionCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testCompilingWithClass()
    {
        $compiler = new CellCollectionCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\Chain\Collection\CellCollection';
        $container = $this->getContainer($this->getConfig(
            ['class' => $class]
        ));
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.chains.people_log.cell_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.chains.people_log.cell_collection');
        $this->assertInstanceOf(
            $class,
            $builderCollection
        );
    }

    public function testCompilingWithDefaults()
    {
        $compiler = new CellCollectionCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\Chain\Collection\CellCollection';
        $container = $this->getContainer($this->getConfig());
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.chains.people_log.cell_collection'));
        $builderCollection = $container->get('k_gzocha_searcher.chains.people_log.cell_collection');
        $this->assertInstanceOf(
            $class,
            $builderCollection
        );
    }

    public function testCompilingWithServiceName()
    {
        $compiler = new CellCollectionCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $class = '\KGzocha\Searcher\Chain\Collection\CellCollection';
        $container = $this->getContainer($this->getConfig(
            ['service' => 'cell_collection_service', 'class' => 'does-not-matter']
        ));
        $container->setDefinition(
            'cell_collection_service',
            new Definition($class)
        );
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.chains.people_log.cell_collection'));
        $cellCollection = $container->get('k_gzocha_searcher.chains.people_log.cell_collection');
        $this->assertInstanceOf(
            $class,
            $cellCollection
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
     * @param $cellCollection
     *
     * @return array
     */
    private function getConfig($cellCollection = null)
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
                            ],
                        ],
                        'builders' => [
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaBuilderStub',
                                'name' => 'imposer1',
                            ],
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaBuilderStub',
                                'name' => 'imposer2',
                            ],
                        ],
                    ],
                ],
                'chains' => [
                    'people_log' => [
                        'transformers' => [
                            [
                                'name' => 'transformer',
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\TransformerStub',
                            ],
                        ],
                        'cells' => [
                            [
                                'name' => 'peopleCell',
                                'searcher' => 'people',
                                'transformer' => 'transformer',
                            ],
                            [
                                'name' => 'peopleCell2',
                                'searcher' => 'people',
                                'transformer' => 'transformer2',
                            ],
                            [
                                'name' => 'logCell',
                                'searcher' => 'log',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (!$cellCollection) {
            return $result;
        }

        $result['k_gzocha_searcher']['chains']['people_log']['cell_collection'] = $cellCollection;

        return $result;
    }
}
