<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaBuilderCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Criteria;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Searcher;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\SearchingContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class KGzochaSearcherExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $container->setParameter('k_gzocha_searcher.contexts', $config['contexts']);
        $loader->load('services.yml');
    }
}
