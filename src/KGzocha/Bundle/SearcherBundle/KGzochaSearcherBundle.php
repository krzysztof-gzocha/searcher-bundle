<?php

namespace KGzocha\Bundle\SearcherBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\NamedFilterModelCollection;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle
 */
class KGzochaSearcherBundle extends Bundle
{
    const NAMED_COLLECTION_TAG = 'searcher.named_filter_model_collection';
    const NAMED_MODEL_TAG = 'searcher.named_model';

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new NamedFilterModelCollection(
                self::NAMED_COLLECTION_TAG,
                self::NAMED_MODEL_TAG
            )
        );
    }
}
