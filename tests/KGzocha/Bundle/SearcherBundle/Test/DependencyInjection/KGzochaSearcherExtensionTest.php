<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ChainSearchCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\TransformerCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use KGzocha\Searcher\Chain\ChainSearch;
use KGzocha\Searcher\Criteria\Collection\CriteriaCollectionInterface;
use KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection
 * @group di
 * @group extension
 */
class KGzochaSearcherExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessPathWithoutChains()
    {
        $container = new ContainerBuilder();
        $container->setDefinition(
            'my_context',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearchingContextStub', [true])
        );

        $extension = new KGzochaSearcherExtension();
        $extension->load($this->getMinimalConfig(), $container);
        $this->addCompilerPasses($container);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.searcher'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.builder_collection'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.criteria_collection'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.context'));

        /** @var CriteriaBuilderCollectionInterface $builderCollection */
        $builderCollection = $container->get('k_gzocha_searcher.people.builder_collection');
        $this->assertCount(2, $builderCollection->getCriteriaBuilders());

        /** @var CriteriaCollectionInterface $criteriaCollection */
        $criteriaCollection = $container->get('k_gzocha_searcher.people.criteria_collection');
        $this->assertCount(3, $criteriaCollection->getCriteria());

        $this->assertInstanceOf(
            '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearchingContextStub',
            $container->get('k_gzocha_searcher.people.context')
        );
    }

    public function testSuccessPathWithChains()
    {
        $container = new ContainerBuilder();
        $container->setDefinition(
            'my_context',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearchingContextStub', [true])
        );
        $container->setDefinition(
            'my_transformer_service',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\TransformerStub')
        );

        $extension = new KGzochaSearcherExtension();
        $extension->load($this->getMinimalConfigWithChains(), $container);
        $this->addCompilerPasses($container);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.chains.people_log.searcher'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.chains.people_log.transformer.people_id_to_log_id'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.chains.people_log.cell.people_cell'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.chains.people_log.cell.log_cell'));

        /** @var ChainSearch $searcher */
        $searcher = $container->get('k_gzocha_searcher.chains.people_log.searcher');
        $this->assertInstanceOf(
            '\KGzocha\Searcher\Chain\ChainSearch',
            $searcher
        );
        $this->assertInstanceOf(
            '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\TransformerStub',
            $container->get('k_gzocha_searcher.chains.people_log.transformer.my_transformer')
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addCompilerPasses(ContainerBuilder $container)
    {
        $validator = new ParametersValidator();
        $builder = new DefinitionBuilder($validator);
        $servicePrefix = 'k_gzocha_searcher';

        $container->addCompilerPass(new CriteriaCollectionCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCollectionCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearchingContextCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearcherCompilerPass(
            $builder,
            $servicePrefix,
            $validator
        ));

        $container->addCompilerPass(new TransformerCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CellCollectionCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CellCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new ChainSearchCompilerPass(
            $builder,
            $servicePrefix
        ));
    }

    /**
     * @return array
     */
    private function getMinimalConfig()
    {
        return [
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
    }

    /**
     * @return array
     */
    private function getMinimalConfigWithChains()
    {
        $config = $this->getMinimalConfig();
        $config['k_gzocha_searcher']['chains'] = [
            'people_log' => [
                // Dummy transformer
                'transformers' => [
                    [
                        'name' => 'people_id_to_log_id',
                        'class' => '\KGzocha\Searcher\Chain\EndTransformer'
                    ],
                    [
                        'name' => 'my_transformer',
                        'service' => 'my_transformer_service'
                    ],
                ],
                'cells' => [
                    [
                        'name' => 'people_cell',
                        'searcher' => 'people',
                        'transformer' => 'people_id_to_log_id',
                    ],
                    [
                        'name' => 'log_cell',
                        'searcher' => 'people',
                        'transformer' => 'people_id_to_log_id',
                    ],
                ],
            ]
        ];

        return $config;
    }
}
