<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class CriteriaBuilderCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testCompiling()
    {
        $compiler = new CriteriaBuilderCompilerPass(
            new ParametersValidator(),
            'k_gzocha_searcher'
        );
        $container = $this->getContainer($this->getConfig());
        $container->addCompilerPass($compiler);
        $container->setDefinition(
            'criteria_builder_service_3',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaBuilderStub')
        );
        $container->compile();

        $this->assertCount(3, $container->get('k_gzocha_searcher.people.builder_collection'));
        for ($i = 1; $i <= 3; ++$i) {
            $this->assertTrue(
                $container->hasDefinition(sprintf('k_gzocha_searcher.people.builder.imposer%d', $i)),
                sprintf('k_gzocha_searcher.people.model%d.criteria', $i)
            );
            $criteria = $container->get(sprintf('k_gzocha_searcher.people.builder.imposer%d', $i));
            $this->assertInstanceOf(
                '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaBuilderStub',
                $criteria
            );
        }
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
        $container->addCompilerPass(new CriteriaBuilderCollectionCompilerPass(
            new ParametersValidator(),
            'k_gzocha_searcher'
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
                            ],
                            [
                                'service' => 'criteria_builder_service_3',
                                'name' => 'imposer3',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $result;
    }
}
