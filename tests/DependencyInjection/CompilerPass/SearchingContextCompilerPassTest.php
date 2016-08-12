<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class SearchingContextCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testCompilingAsService()
    {
        $compiler = new SearchingContextCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $container = $this->getContainer($this->getConfig([
            'service' => 'my_context',
        ]));
        $container->setDefinition(
            'my_context',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearchingContextStub')
        );
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.context'));
        $this->assertInstanceOf(
            '\KGzocha\Searcher\Context\SearchingContextInterface',
            $container->get('k_gzocha_searcher.people.context')
        );
    }

    public function testCompilingAsClass()
    {
        $compiler = new SearchingContextCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        );
        $container = $this->getContainer($this->getConfig([
            'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearchingContextStub',
        ]));
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.context'));
        $this->assertInstanceOf(
            '\KGzocha\Searcher\Context\SearchingContextInterface',
            $container->get('k_gzocha_searcher.people.context')
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
        $container->addCompilerPass(new SearchingContextCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        ));

        return $container;
    }

    /**
     * @param array|null $searchingContext
     *
     * @return array
     */
    private function getConfig($searchingContext = null)
    {
        $result = [
            'k_gzocha_searcher' => [
                'contexts' => [
                    'people' => [
                        'context' => $searchingContext,
                        'criteria' => [
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaStub',
                                'name' => 'model1',
                            ],
                        ],
                        'builders' => [
                            [
                                'class' => '\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CriteriaBuilderStub',
                                'name' => 'imposer1',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $result;
    }
}
