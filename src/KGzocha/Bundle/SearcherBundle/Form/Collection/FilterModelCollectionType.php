<?php

namespace KGzocha\Bundle\SearcherBundle\Form\Collection;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterModelCollectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('filterModels', 'collection');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'KGzocha\Searcher\Model\FilterModel\Collection\FilterModelCollection',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}
