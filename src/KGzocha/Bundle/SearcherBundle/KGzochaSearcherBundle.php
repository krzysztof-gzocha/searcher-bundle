<?php

namespace KGzocha\Bundle\SearcherBundle;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ContextsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle
 */
class KGzochaSearcherBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ContextsCompilerPass());
    }
}
