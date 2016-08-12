<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use KGzocha\Searcher\Criteria\Collection\NamedCriteriaCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class CriteriaCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testCompilingWithClass()
    {
        $compiler = new CriteriaCompilerPass(
            new ParametersValidator(),
            'k_gzocha_searcher'
        );
        $container = $this->getContainer($this->getConfig());
        $container->addCompilerPass($compiler);
        $container->setDefinition(
            'criteria_service_3',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaStub')
        );
        $container->compile();

        /** @var NamedCriteriaCollection $criteriaCollection */
        $criteriaCollection = $container->get('k_gzocha_searcher.people.criteria_collection');
        $this->assertCount(3, $criteriaCollection);
        for ($i = 1; $i <= 3; ++$i) {
            $this->assertTrue(
                $container->hasDefinition(sprintf('k_gzocha_searcher.people.criteria.model%d', $i)),
                sprintf('k_gzocha_searcher.people.model%d.criteria', $i)
            );
            $criteria = $container->get(sprintf('k_gzocha_searcher.people.criteria.model%d', $i));
            $this->assertInstanceOf(
                '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaStub',
                $criteria
            );
            $this->assertTrue(
                (bool) $criteriaCollection->getNamedCriteria(sprintf('model%d', $i))
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
        $container->addCompilerPass(new CriteriaCollectionCompilerPass(
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
                            ]
                        ],
                    ],
                ],
            ],
        ];

        return $result;
    }
}
