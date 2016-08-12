<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass;
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

    /**
     * @param ContainerBuilder $container
     */
    private function addCompilerPasses(ContainerBuilder $container)
    {
        $validator = new ParametersValidator();
        $servicePrefix = 'k_gzocha_searcher';

        $container->addCompilerPass(new CriteriaCollectionCompilerPass(
            $validator,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCollectionCompilerPass(
            $validator,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaCompilerPass(
            $validator,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCompilerPass(
            $validator,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearchingContextCompilerPass(
            $validator,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearcherCompilerPass(
            $validator,
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
}
