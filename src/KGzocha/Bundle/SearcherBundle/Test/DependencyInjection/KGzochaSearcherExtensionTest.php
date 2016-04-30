<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollectionInterface;
use KGzocha\Searcher\FilterModel\Collection\FilterModelCollectionInterface;
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
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.imposer_collection'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.model_collection'));
        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.context'));

        /** @var FilterImposerCollectionInterface $imposerCollection */
        $imposerCollection = $container->get('k_gzocha_searcher.people.imposer_collection');
        $this->assertCount(2, $imposerCollection->getFilterImposers());

        /** @var FilterModelCollectionInterface $modelCollection */
        $modelCollection = $container->get('k_gzocha_searcher.people.model_collection');
        $this->assertCount(3, $modelCollection->getFilterModels());

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
                        'models' => [
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\FilterModelStub',
                                'name' => 'model1',
                            ],
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\FilterModelStub',
                                'name' => 'model2',
                            ],
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\FilterModelStub',
                                'name' => 'model3',
                            ]
                        ],
                        'imposers' => [
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\FilterImposerStub',
                                'name' => 'imposer1',
                            ],
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\FilterImposerStub',
                                'name' => 'imposer2',
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }
}
