<?php

namespace KGzocha\Bundle\SearcherBundle\Form;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\NamedFilterModelCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Form
 */
abstract class SearchForm extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault(
            'data_class',
            NamedFilterModelCollection::class
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return '';
    }

    /**
     * Will return string that can be used to configure "property_path" parameter
     * of single field in a form.
     *
     * @param string $modelName is a name of filter model
     * @param string|null $propertyName is a name of parameter inside filter model.
     * If null $modelName will be used
     *
     * @return string
     */
    protected function getPath($modelName, $propertyName = null)
    {
        if (!$propertyName) {
            $propertyName = $modelName;
        }

        return sprintf('filterModels[%s].%s', $modelName, $propertyName);
    }
}
