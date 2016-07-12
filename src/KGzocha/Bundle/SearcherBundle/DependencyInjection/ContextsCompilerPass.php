<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Criteria;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaBuilderCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\CriteriaCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\Searcher;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\ServiceDefiner\SearchingContext;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class ContextsCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $contextParam = 'k_gzocha_searcher.contexts';
        if (!$container->hasParameter($contextParam)) {
            throw new \InvalidArgumentException('Contexts are missing from the configuration.');
        }

        $contexts = $container->getParameter($contextParam);

        foreach ($contexts as $contextId => &$context) {
            CriteriaCollection::defineServices(
                $contextId, $context, $container
            );
            CriteriaBuilderCollection::defineServices(
                $contextId, $context, $container
            );
            Criteria::defineServices(
                $contextId, $context, $container
            );
            CriteriaBuilder::defineServices(
                $contextId, $context, $container
            );
            SearchingContext::defineServices(
                $contextId, $context, $container
            );
            Searcher::defineServices(
                $contextId, $context, $container
            );
        }
    }
}
