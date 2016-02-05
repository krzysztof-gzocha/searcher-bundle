# SearcherBundle [![Build Status](https://travis-ci.org/krzysztof-gzocha/searcher-bundle.svg?branch=master)](https://travis-ci.org/krzysztof-gzocha/searcher-bundle)

This bundle is providing integration between Symfony and [Searcher](https://github.com/krzysztof-gzocha/searcher)

### Installation
You can install this bundle via composer
```
composer require krzysztof-gzocha/searcher-bundle
```
and don't forget to register it in your AppKernel:
```
public function registerBundles()
{
  $bundles = array(
    /** Your bundles **/
    new KGzocha\Bundle\SearcherBundle\KGzochaSearcherBundle(),
  );
  /** rest of the code  **/
}
```

### Example usage
- I recommend to create parameter, which will holds the name of your searching. For example `people` if you want to search for people.
```
parameters:
    my_search.context_id: "people"
```
- Create your collections for FilterModels and FilterImposers. Of course you do not need to create your own classes - they are ready in searcher core library. We just need to specify, that we want to use them in this context. In this example we are using `NamedFilterModelCollection`, which later on will allow us to create form and make use of `property_path`.
```
my_search.model_colection:
    class: KGzocha\Searcher\Model\FilterModel\Collection\NamedFilterModelCollection
    tags:
      - { name: searcher.named_filter_model_collection, contextId: %my_search.context_id% }
```
```
my_search.imposer_collection:
  class: KGzocha\Searcher\FilterImposer\Collection\FilterImposerCollection
  tags:
    - { name: searcher.filter_imposer_collection, contextId: %my_search.context_id% }
```
- Tag your model's services
```
my_search.age_range_model:
    class: \AgeRangeModel     # Your model class
    tags:
      - { 
          name: searcher.named_model, 
          contextId: %my_search.context_id%, 
          modelName: ageRange   # We will use this name in Form
        }
```
- Tag your imposer's services
```
my_search.age_range_imposer:
    class: \AgeRangeImposer   # Your imposer class
    tags:
      - { 
          name: searcher.filter_imposer, 
          contextId: %my_search.context_id%
        }
```
- Create SearchingContext for your search. In the example we assume that you want to use `Doctrine\ORM\QueryBuilder`, so we can use existing class as a context: `KGzocha\Searcher\Context\QueryBuilderSearchingContext`, but if you want anything else please feel free to search for it in searcher library or create your own.
```
my_search.searching_context:
  class: KGzocha\Searcher\Context\QueryBuilderSearchingContext
  arguments:
    - @my_search.query_builder  # Or any QueryBuilder service
```
- Searcher
```
my_search.searcher:
  class: KGzocha\Searcher\Searcher\Searcher
  tags:
    - { 
        name: searcher.factory,
        contextId: %my_search.context_id% 
      }
```
- Now we can create example form. Form will allow Symfony to take care of population and validation our models from request.
This step is optional and you don't have to populate models from request. You can do this however you want to.
```
use KGzocha\Bundle\SearcherBundle\Form\SearchForm;

class MySearchForm extends SearchForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minimalAge', 'integer', [
                'property_path' => $this->getPath('ageRange', 'minimalAge'),
            ])
            ->add('maximalAge', 'integer', [
                'property_path' => $this->getPath('ageRange', 'maximalAge'),
            ])
            /** and any other fields.. **/
            ->add('<PARAM NAME IN REQUEST>', '<ANY FORM TYPE>', [
                'property_path' => $this->getPath(
                    '<MODEL NAME FROM CONFIG>', 
                    '<MODELS ATTRIBUTE NAME>'
                ),
            ]);
    }
}
```
- Now we can search for our results in controller
```
public function searchAction(Request $request)
{
    $form = $this->createForm(
        new MySearchForm(),
        $this->get('my_search.imposer_collection')
    );

    $form->handleRequest($request);
    // Now we can check if form is valid
    
    $searcher = $this->get('my_search.searcher');
    $results = $searcher->search(
        $form->getData(),
        $this->get('my_search.searching_context')
    );
}
```

### Contributing
All ideas and pull request are welcomed and appreciated.
Please, feel free to share your thought via issues.

Command to run tests: `bin/phing run-tests`.
