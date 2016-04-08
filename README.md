# SearcherBundle [![Build Status](https://travis-ci.org/krzysztof-gzocha/searcher-bundle.svg?branch=master)](https://travis-ci.org/krzysztof-gzocha/searcher-bundle)

This bundle is providing integration between Symfony and [Searcher](https://github.com/krzysztof-gzocha/searcher)

### Installation
You can install this bundle via composer
```
composer require krzysztof-gzocha/searcher-bundle
```
and don't forget to register it in your AppKernel:
```php
public function registerBundles()
{
  $bundles = array(
    /** Your bundles **/
    new KGzocha\Bundle\SearcherBundle\KGzochaSearcherBundle(),
  );
  /** rest of the code  **/
}
```

## Example usage
### Config
In config file we will specify minimal configuration for `people` context.
You can full full example of config reference in **[here](https://github.com/krzysztof-gzocha/searcher-bundle/blob/master/src/KGzocha/Bundle/SearcherBundle/configReference.yml)**
```
k_gzocha_searcher:
  contexts:
    people:
      context:
        service: your_searching_context_service_id

      models:
        - { class: \AgeRangeModel, name: age_range}
        - { service: some_service_id, name: other_model }

      imposers:
        - { class: \AgeRangeImposer, name: age_range }
        - { service: other_service_id, name: my_imposer }
```
As you can see you can specify everything as a simple class or as your own service.
This configuration will create our `people` context and create all required services (imposer collection, model collection, searcher and context), so you can access them and make use of them. For example to access Searcher instance from controller you can simply:
```php
$this->get('k_gzocha_searcher.people.searcher');
```
or to access `age_range` model:
```php
$this->get('k_gzocha_searcher.people.model.age_range');
```
or access `my_imposer`:
```php
$this->get('k_gzocha_searcher.people.imposer.my_imposer');
```
I guess it's pretty easy to understand this naming convention.
In this example you need to define only 1 service on your own - SearchingContext service with id specified in the config (`your_searching_context_service_id`). You can do it like this:
```yaml
your_searching_context_service_id:
  class: KGzocha\Searcher\Context\QueryBuilderSearchingContext
  arguments:
    - @my_search.query_builder  # Or any QueryBuilder service
```
### Code
For this example we will use simple **AgeRangeModel** (described in [here](https://github.com/krzysztof-gzocha/searcher)), but ofcourse you can use your own class or service.
```php
class AgeRangeFilterModel implements FilterModelInterface
{
    private $minimalAge;
    private $maximalAge;

    /**
    * Only required method.
    * If will return true, then it will be passed to some of the FilterImposer(s)
    */
    public function isImposed()
    {
        return null !== $this->minimalAge && null !== $this->maximalAge;
    }

    // getters, setters, what ever
}
```
We will also use **AgeRangeImposer** (described in [here](https://github.com/krzysztof-gzocha/searcher)), but ofcourse you can use your own class or service.
```php
class AgeRangeImposer implements FilterImposerInterface
{
    public function imposeFilter(
        FilterModelInterface $filterModel,
        SearchingContextInterface $searchingContext
    ) {
        $searchingContext
            ->getQueryBuilder()
            ->andWhere('e.age >= :minimalAge')
            ->andWhere('e.age <= :maximalAge')
            ->setParameter('minimalAge', $filterModel->getMinimalAge())
            ->setParameter('maximalAge', $filterModel->getMaximalAge());
    }

    public function supportsModel(
        FilterModelInterface $filterModel
    ) {
        // No need to check isImposed(). Searcher will check it
        return $filterModel instanceof AgeRangeFilterModel;
    }

    /**
    * You can skip this method if you will extend from QueryBuilderFilterImposer.
    */
    public function supportsSearchingContext(
        SearchingContextInterface $searchingContext
    ) {
        return $searchingContext instanceof \Doctrine\ORM\QueryBuilder;
    }
}
```
### Form (optional)
Now we can create example form. Form will allow Symfony to take care of population and validation our models from request.
This step is optional and you don't have to populate models from request. You can do this however you want to.
```php
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
```php
public function searchAction(Request $request)
{
    $form = $this->createForm(
        new MySearchForm(),
        $this->get('my_search.imposer_collection')
    );

    $form->handleRequest($request);
    // Now we can check if form is valid

    $searcher = $this->get('k_gzocha_searcher.people.searcher');
    $results = $searcher->search($form->getData());
    // Yay, we have our results!
}
```

### Contributing
All ideas and pull request are welcomed and appreciated.
Please, feel free to share your thought via issues.

Command to run tests: `bin/phing run-tests`.
