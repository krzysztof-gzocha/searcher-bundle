<?php

namespace KGzocha\Bundle\SearcherBundle\Test;

use KGzocha\Bundle\SearcherBundle\KGzochaSearcherBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 * @group bundle
 */
class KGzochaSearcherBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new KGzochaSearcherBundle();
        $container = new ContainerBuilder();

        $bundle->build($container);
        $compilerPasses = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        $this->assertCount(10, $compilerPasses);
        $requiredCompilerPasses = [
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\TransformerCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ChainSearchCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCompilerPass',
            '\KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCollectionCompilerPass',
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
