<?php

namespace KGzocha\Bundle\SearcherBundle\Test;

use KGzocha\Bundle\SearcherBundle\KGzochaSearcherBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class KGzochaSearcherBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new KGzochaSearcherBundle();
        $container = new ContainerBuilder();

        $bundle->build($container);
        $compilerPasses = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        $this->assertCount(6, $compilerPasses);
        $requiredCompilerPasses = [
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherCompilerPass',
        ];

        foreach ($requiredCompilerPasses as $requiredCompilerPass) {
            $matches = false;
            foreach ($compilerPasses as $compilerPass) {
                if ($compilerPass instanceof $requiredCompilerPass) {
                    $matches = true;
                }
            }
            $this->assertTrue($matches);
        }
    }
}
