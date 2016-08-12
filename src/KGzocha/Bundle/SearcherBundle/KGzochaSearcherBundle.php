<?php

namespace KGzocha\Bundle\SearcherBundle;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaBuilderCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCollectionCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\CriteriaCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherCompilerPass;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearchingContextCompilerPass;
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
        $servicePrefix = $this->getContainerExtension()->getAlias();

        $container->addCompilerPass(new CriteriaCollectionCompilerPass(
            $parametersValidator,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCollectionCompilerPass(
            $parametersValidator,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaBuilderCompilerPass(
            $parametersValidator,
            $servicePrefix
        ));
        $container->addCompilerPass(new CriteriaCompilerPass(
            $parametersValidator,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearchingContextCompilerPass(
            $parametersValidator,
            $servicePrefix
        ));
        $container->addCompilerPass(new SearcherCompilerPass(
            $parametersValidator,
            $servicePrefix
        ));
    }
}
