<?php

namespace KGzocha\Bundle\SearcherBundle;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\FilterImposerCollection;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\SearcherFactoryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\NamedFilterModelCollection;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle
 */
class KGzochaSearcherBundle extends Bundle
{
    const IMPOSER_COLLECTION_TAG = 'searcher.filter_imposer_collection';
    const IMPOSER_TAG = 'searcher.filter_imposer';

    const NAMED_COLLECTION_TAG = 'searcher.named_filter_model_collection';
    const NAMED_MODEL_TAG = 'searcher.named_model';

    const SEARCHER_FACTORY_TAG = 'searcher.factory';

    const CONTEXT_ID = 'contextId';

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $this->addCompilerPasses($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addCompilerPasses(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new NamedFilterModelCollection(
                self::NAMED_COLLECTION_TAG,
                self::NAMED_MODEL_TAG,
                self::CONTEXT_ID
            ))
            ->addCompilerPass(new FilterImposerCollection(
                self::IMPOSER_COLLECTION_TAG,
                self::IMPOSER_TAG,
                self::CONTEXT_ID
            ))
            ->addCompilerPass(new SearcherFactoryCompilerPass(
                self::SEARCHER_FACTORY_TAG,
                self::IMPOSER_COLLECTION_TAG,
                self::CONTEXT_ID
            ));
    }
}
