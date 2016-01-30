<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\NamedFilterModelCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass
 */
class NamedFilterModelCollectionTest extends \PHPUnit_Framework_TestCase
{
    const NAMED_COLLECTION = 'NamedCollection';
    const COLLECTION_TAG = 'collection_tag';
    const MODEL_TAG = 'model_tag';

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
                'contextId' => 'project_search',
                'modelName' => 'name_of_the_model',
            ]);

        $this->process($container);

        $this->assertTrue($container->hasDefinition(self::NAMED_COLLECTION));
        $this->assertTrue(
            $container
                ->getDefinition(self::NAMED_COLLECTION)
                ->hasMethodCall('addNamedFilterModel')
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function process(ContainerBuilder $container)
    {
        $pass = new NamedFilterModelCollection(self::COLLECTION_TAG, self::MODEL_TAG);
        $pass->process($container);
    }
}
