<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\KGzochaSearcherExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class SearcherCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testCompilingAsDefaults()
    {
        $compiler = new SearcherCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher',
            new ParametersValidator()
        );
        $container = $this->getContainer($this->getConfig());
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.searcher'));
        $this->assertInstanceOf(
            '\KGzocha\Searcher\SearcherInterface',
            $container->get('k_gzocha_searcher.people.searcher')
        );
        $this->assertInstanceOf(
            Configuration::WRAPPER_CLASS,
            $container->get('k_gzocha_searcher.people.searcher')
        );
    }

    public function testCompilingAsService()
    {
        $compiler = new SearcherCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher',
            new ParametersValidator()
        );
        $container = $this->getContainer($this->getConfig([
            'service' => 'searcher_service',
        ]));
        $container->addCompilerPass($compiler);
        $container->setDefinition(
            'searcher_service',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearcherStub')
        );
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.searcher'));
        $this->assertInstanceOf(
            '\KGzocha\Searcher\SearcherInterface',
            $container->get('k_gzocha_searcher.people.searcher')
        );
        $this->assertInstanceOf(
            Configuration::WRAPPER_CLASS,
            $container->get('k_gzocha_searcher.people.searcher')
        );
    }

    /**
     * @param $class
     * @dataProvider searcherClassDataProvider
     */
    public function testCompilingAsClass($class)
    {
        $compiler = new SearcherCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher',
            new ParametersValidator()
        );
        $container = $this->getContainer($this->getConfig([
            'class' => $class,
        ]));
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.searcher'));
        $this->assertInstanceOf(
            '\KGzocha\Searcher\SearcherInterface',
            $container->get('k_gzocha_searcher.people.searcher')
        );
        $this->assertInstanceOf(
            Configuration::WRAPPER_CLASS,
            $container->get('k_gzocha_searcher.people.searcher')
        );
    }

    /**
     * @param $class
     * @dataProvider searcherClassDataProvider
     */
    public function testCompilingWithoutWrapperClass($class)
    {
        $compiler = new SearcherCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher',
            new ParametersValidator()
        );
        $container = $this->getContainer($this->getConfig([
            'class' => $class,
            'wrapper_class' => null,
        ]));
        $container->addCompilerPass($compiler);
        $container->compile();

        $this->assertTrue($container->hasDefinition('k_gzocha_searcher.people.searcher'));
        $this->assertInstanceOf(
            '\KGzocha\Searcher\SearcherInterface',
            $container->get('k_gzocha_searcher.people.searcher')
        );
        $this->assertNotInstanceOf(
            Configuration::WRAPPER_CLASS,
            $container->get('k_gzocha_searcher.people.searcher')
        );
    }


    public function searcherClassDataProvider()
    {
        return [
            [Configuration::SEARCHER_CLASS],
            ['\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearcherStub']
        ];
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
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        ));
        $container->addCompilerPass(new SearchingContextCompilerPass(
            new DefinitionBuilder(new ParametersValidator()),
            'k_gzocha_searcher'
        ));
        $container->setDefinition(
            'my_context',
            new Definition('\KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\SearchingContextStub')
        );

        return $container;
    }

    /**
     * @param array|null $searcher
     * @return array
     */
    private function getConfig($searcher = null)
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

        if ($searcher) {
            $result['k_gzocha_searcher']['contexts']['people']['searcher'] = $searcher;
        }

        return $result;
    }
}
