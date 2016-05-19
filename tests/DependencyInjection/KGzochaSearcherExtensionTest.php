<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use KGzocha\Searcher\Criteria\Collection\CriteriaCollectionInterface;
use KGzocha\Searcher\CriteriaBuilder\Collection\CriteriaBuilderCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection
 * @group di
 */
class KGzochaSearcherExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessPath()
    {
        $container = new ContainerBuilder();
        $container->setDefinition(
            'my_context',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\SearchingContextStub', [true])
        );

        $extension = new KGzochaSearcherExtension();
        $extension->load($this->getMinimalConfig(), $container);

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
            '\KGzocha\Bundle\SearcherBundle\Test\SearchingContextStub',
            $container->get('k_gzocha_searcher.people.context')
        );
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
}
