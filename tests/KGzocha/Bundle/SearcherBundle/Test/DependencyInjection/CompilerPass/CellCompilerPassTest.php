<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ChainSearchCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\TransformerCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class CellCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessPath()
    {
        $compilerPass = new CellCompilerPass(
            $definitionBuilder = new DefinitionBuilder(new ParametersValidator()),
            $prefix = 'k_gzocha_searcher'
        );
        $container = $this->getContainer($this->getConfig());
        $container->addCompilerPass($compilerPass);
        $container->compile();

        $peopleNames = ['peopleCell', 'peopleCell2', 'logCell'];
        foreach ($peopleNames as $name) {
            $this->assertTrue($container->has(sprintf(
                'k_gzocha_searcher.chains.people_log.cell.peopleCell',
                $name
            )));

            $this->assertInstanceOf(
                '\KGzocha\Searcher\Chain\CellInterface',
                $lastCell = $container->get(sprintf('k_gzocha_searcher.chains.people_log.cell.%s', $name))
            );
        }

        $this->assertFalse($lastCell->hasTransformer());
        $this->assertInstanceOf(
            Configuration::END_TRANSFORMER_CLASS,
            $lastCell->getTransformer()
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
        $definitionBuilder = new DefinitionBuilder(new ParametersValidator());
        $prefix = 'k_gzocha_searcher';
        $container->addCompilerPass(new TransformerCompilerPass($definitionBuilder, $prefix));
        $container->addCompilerPass(new CellCollectionCompilerPass($definitionBuilder, $prefix));
        $container->addCompilerPass(new ChainSearchCompilerPass($definitionBuilder, $prefix));

        $container->setDefinition('k_gzocha_searcher.people.searcher', new Definition(
            '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearcherStub'
        ));
        $container->setDefinition('k_gzocha_searcher.log.searcher', new Definition(
            '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearcherStub'
        ));
        $container->setDefinition('transformer_service', new Definition(
            '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\TransformerStub'
        ));
        $container->setDefinition('cell_service', new Definition(
            '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CellStub'
        ));

        return $container;
    }

    /**
     * @return array
     */
    private function getConfig()
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
                                'service' => 'criteria_service_3',
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
                'chains' => [
                    'people_log' => [
                        'chain_searcher' => [
                            'class' => '\KGzocha\Searcher\Chain\ChainSearch',
                        ],
                        'transformers' => [
                            [
                                'name' => 'transformer',
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\TransformerStub',
                            ],
                            [
                                'name' => 'transformer2',
                                'service' => 'transformer_service',
                            ],
                        ],
                        'cells' => [
                            [
                                'name' => 'peopleCell',
                                'searcher' => 'people',
                                'transformer' => 'transformer',
                                'service' => 'cell_service',
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

        return $result;
    }
}
