<?php

namespace KGzocha\Bundle\SearcherBundle;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CellCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ChainSearchCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\TransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class KGzochaSearcherBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $parametersValidator = new ParametersValidator();
        $builder = new DefinitionBuilder($parametersValidator);
        $servicePrefix = $this->getContainerExtension()->getAlias();

        $container->addCompilerPass(new CriteriaCollectionCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCollectionCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearchingContextCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearcherCompilerPass(
            $builder,
            $servicePrefix,
            $parametersValidator
        ));

        // Chain search compiler passes
        $container->addCompilerPass(new CellCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new TransformerCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new CellCollectionCompilerPass(
            $builder,
            $servicePrefix
        ));
        $container->addCompilerPass(new ChainSearchCompilerPass(
            $builder,
            $servicePrefix
        ));
    }
}
