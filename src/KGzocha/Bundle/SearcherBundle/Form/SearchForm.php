<?php

namespace KGzocha\Bundle\SearcherBundle\Form;

use KGzocha\Searcher\Criteria\Collection\NamedCriteriaCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
abstract class SearchForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault(
            'data_class',
            NamedCriteriaCollection::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }

    /**
     * Will return string that can be used to configure "property_path" parameter
     * of single field in a form.
     *
     * @param string      $criteriaName is a name of criteria
     * @param string|null $propertyName is a name of parameter inside criteria.
     *                                  If null $modelName will be used
     *
     * @return string
     */
    protected function getPath($criteriaName, $propertyName = null)
    {
        if (!$propertyName) {
            $propertyName = $criteriaName;
        }

        return sprintf('criteria[%s].%s', $criteriaName, $propertyName);
    }
}
