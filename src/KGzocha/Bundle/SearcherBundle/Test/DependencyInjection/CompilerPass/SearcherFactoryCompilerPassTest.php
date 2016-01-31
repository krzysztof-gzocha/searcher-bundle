<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherFactoryCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\SearcherBundleExtension;
use KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection;
use KGzocha\Searcher\Searcher\Searcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass
 */
class SearcherFactoryCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    const FACTORY_TAG = 'factoryTag';
    const IMPOSER_COLLECTION_TAG = 'imposerCollectionTag';
    const CONTEXT_ID_NAME = 'contextIdName';

    public function testProcess()
    {
        $container = $this->prebuildContainer();

        $searcherDefinition = new Definition();
        $searcherDefinition->addTag(self::FACTORY_TAG, [
            self::CONTEXT_ID_NAME => 'someSearch',
        ]);

        $imposerCollection = new Definition();
        $imposerCollection
            ->setClass('KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection')
            ->addTag(self::IMPOSER_COLLECTION_TAG, [
                self::CONTEXT_ID_NAME => 'someSearch',
            ]);

        $container
            ->addDefinitions([
                'searcher_service_name' => $searcherDefinition,
                'some_imposers' => $imposerCollection
            ]);

        $this->process($container);
        $this->assertInstanceOf(
            'KGzocha\Searcher\Searcher\Searcher',
            $container->get('searcher_service_name')
        );
    }

    /**
     * @return ContainerBuilder
     */
    private function prebuildContainer()
    {
        $container = new ContainerBuilder();

        $dispatcher = new Definition('Symfony\Component\EventDispatcher\EventDispatcher');
        $container->addDefinitions([
            'event_dispatcher' => $dispatcher,
        ]);

        $extension = new SearcherBundleExtension();
        $container->registerExtension($extension);
        $container->loadFromExtension('searcher_bundle');
        $extension->load([], $container);

        return $container;
    }

    /**
     * @param ContainerBuilder $container
     */
    private function process(ContainerBuilder $container)
    {
        $pass = new SearcherFactoryCompilerPass(
            self::FACTORY_TAG,
            self::IMPOSER_COLLECTION_TAG,
            self::CONTEXT_ID_NAME
        );

        $pass->process($container);
    }
}
