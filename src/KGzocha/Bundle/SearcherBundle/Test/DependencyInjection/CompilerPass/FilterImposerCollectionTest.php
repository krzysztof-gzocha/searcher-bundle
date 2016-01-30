<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\FilterImposerCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass
 */
class FilterImposerCollectionTest extends \PHPUnit_Framework_TestCase
{
    const NAMED_COLLECTION = 'NamedCollection';
    const COLLECTION_TAG = 'collection_tag';
    const MODEL_TAG = 'model_tag';
    const CONTEXT_ID = 'contextId';

    public function testProcess()
    {
        $container = new ContainerBuilder();

        $container
            ->register(self::NAMED_COLLECTION)
            ->addTag(self::COLLECTION_TAG, [
                'contextId' => 'project_search',
            ]);

        $container
            ->register('NamedModel')
            ->addTag(self::MODEL_TAG, [
                self::CONTEXT_ID => 'project_search',
                'modelName' => 'name_of_the_model',
            ]);

        $this->process($container);

        $this->assertTrue($container->hasDefinition(self::NAMED_COLLECTION));
        $this->assertTrue(
            $container
                ->getDefinition(self::NAMED_COLLECTION)
                ->hasMethodCall('addFilterImposer')
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function process(ContainerBuilder $container)
    {
        $pass = new FilterImposerCollection(
            self::COLLECTION_TAG, self::MODEL_TAG, self::CONTEXT_ID
        );
        $pass->process($container);
    }
}
