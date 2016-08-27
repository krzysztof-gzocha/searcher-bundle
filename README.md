<img align="right" src="https://camo.githubusercontent.com/03659f3fcddeaec49aa2f494c1d4aff0ec9cbd36/687474703a2f2f7777772e636c6b65722e636f6d2f636c6970617274732f612f632f612f382f31313934393936353638313938333637303238396b63616368656772696e642e7376672e7468756d622e706e67"/>

# SearcherBundle [![Build Status](https://travis-ci.org/krzysztof-gzocha/searcher-bundle.svg?branch=master)](https://travis-ci.org/krzysztof-gzocha/searcher-bundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/krzysztof-gzocha/searcher-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/krzysztof-gzocha/searcher-bundle/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/krzysztof-gzocha/searcher-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/krzysztof-gzocha/searcher-bundle/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/f930d40b-07bb-4320-bfd3-039f92ad24a2/mini.png)](https://insight.sensiolabs.com/projects/f930d40b-07bb-4320-bfd3-039f92ad24a2)

This bundle is providing integration between Symfony and **[Searcher](https://github.com/krzysztof-gzocha/searcher)**

### What is Searcher?
*Searcher* is a library completely decoupled from any framework created in order to simplify construction of complex searching queries basing on passed criteria.
It's basic idea is to split each searching *filter* to separate class.
Regardless of what do you want to search: entities in MySQL, MongoDB or just files.
Supported PHP versions: >=5.5, 7 and HHVM.

### Full documentation
Full documentation can be found at [http://searcher.rtfd.io/](http://searcher.readthedocs.io/en/stable/introduction.html)

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
```yaml
k_gzocha_searcher:
  contexts:
    people:
      context:
        service: your_searching_context_service_id

      criteria:
        - { class: \AgeRangeCriteria, name: age_range}
        - { service: some_service_id, name: other_criteria }

      builders:
        - { class: \AgeRangeCriteriaBuilder, name: age_range }
        - { service: other_service_id, name: my_criteria_builder }
```
As you can see you can specify everything as a simple class or as your own service.
This configuration will create our `people` context and create all required services (builder collection, criteria collection, searcher and context), so you can access them and make use of them. For example to access Searcher instance from controller you can simply:
```php
$this->get('k_gzocha_searcher.people.searcher');
```
or to access `age_range` criteria:
```php
$this->get('k_gzocha_searcher.people.criteria.age_range');
```
or access `my_criteria_builder`:
```php
$this->get('k_gzocha_searcher.people.builder.my_criteria_builder');
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
For this example we will use simple **AgeRangeCriteria** (described in [here](https://github.com/krzysztof-gzocha/searcher)), but of course you can use your own class or service.
```php
class AgeRangeCriteria implements CriteriaInterface
{
    private $minimalAge;
    private $maximalAge;

    /**
    * Only required method.
    */
    public function shouldBeApplied()
    {
        return null !== $this->minimalAge && null !== $this->maximalAge;
    }

    // getters, setters, what ever
}
```
We will also use **AgeRangeCriteriaBuilder** (described in [here](https://github.com/krzysztof-gzocha/searcher)), but of course you can use your own class or service.
```php
class AgeRangeCriteriaBuilder implements FilterImposerInterface
{
    public function buildCriteria(
        CriteriaInterface $criteria,
        SearchingContextInterface $searchingContext
    ) {
        $searchingContext
            ->getQueryBuilder()
            ->andWhere('e.age >= :minimalAge')
            ->andWhere('e.age <= :maximalAge')
            ->setParameter('minimalAge', $criteria->getMinimalAge())
            ->setParameter('maximalAge', $criteria->getMaximalAge());
    }

    public function allowsCriteria(
        CriteriaInterface $criteria
    ) {
        // No need to check shouldBeApplied(). Searcher will check it
        return $criteria instanceof AgeRangeCriteria;
    }

    /**
    * You can skip this method if you will extend from QueryBuilderFilterImposer.
    */
    public function supportsSearchingContext(
        SearchingContextInterface $searchingContext
    ) {
        return $searchingContext instanceof \KGzocha\Searcher\Context\Doctrine\QueryBuilderSearchingContext;
    }
}
```
### Form (optional)
Now we can create example form. Form will allow Symfony to take care of population and validation our criteria from request.
This step is optional and you don't have to populate criteria from request. You can do this however you want to.
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
                    '<CRITERIA NAME FROM CONFIG>',
                    '<CRITERIA ATTRIBUTE NAME>'
                ),
            ]);
    }
}
```
### Controller
```php
public function searchAction(Request $request)
{
    $form = $this->createForm(
        new MySearchForm(),
        $this->get('k_gzocha_searcher.people.criteria_collection')
    );

    $form->handleRequest($request);
    // Now we can check if form is valid

    $searcher = $this->get('k_gzocha_searcher.people.searcher');
    $results = $searcher->search($form->getData());
    // Yay, we have our results!
    // $results is instance of ResultCollection by default. Read for 'wrapper_class'
}
```

### Wrapper class
By default SearcherBundle will wrap Searcher into `WrappedResultsSearcher`,
which will return `ResultCollection` which has method `getResults()` that will return collection of your results.
Of course `ResultCollection` itself is traversable, so you can use it inside `foreach` loop.
This feature is useful in rare situations where you are not sure if your `QueryBuilder` will return array or traversable object. Returning `null` and trying to iterate over it will lead to an error. ResultCollection will prevent this kind of situation. If you want to change wrapper class then you need to specify `wrapper_class` in searcher config.
Of course sometimes you want your Searcher to just return an integer or whatever, then you do not want to wrap your Searcher. In order to do that just specify `wrapper_class` as `null`

## Chain searching
Searcher library allows you to perform [chain searching](http://searcher.readthedocs.io/en/stable/chain-search.html) and 
you can use with this bundle as well. All what you need to do is to properly configure it in config file and fetch
`ChainSearch` service.

Example chain searching config:  
```yaml
k_gzocha_searcher:
    chains:
        people_log:
    
          # optional
          chain_searcher:
            class: \KGzocha\Searcher\Chain\ChainSearch
            service: chain_searcher_service
    
          transformers:
            - name: peopleIdToLogId 
              service: transfomer_service
              class: \TransformerClass
    
          # at least two are required
          cells:
            - name: peopleCell
              searcher: people
              transformer: peopleIdToLogId
              class: \KGzocha\Searcher\Chain\Cell   # optional
              service: cell_service_1               # optional
    
            - name: logCell
              searcher: logs
              transformer: ~                        # If empty EndTransformer will be used
              class: \KGzocha\Searcher\Chain\Cell   # optional
              service: cell_service_2               # optional
```

With above config you can easily fetch all services like this:  
```php
$this->get('k_gzocha_searcher.chains.people_log.searcher');         // ChainSearch service

$this->get('k_gzocha_searcher.chains.people_log.cell.peopleCell');  // #1 Cell service 
$this->get('k_gzocha_searcher.chains.people_log.cell.logCell');     // #2 Cell service

$this->get('k_gzocha_searcher.chains.people_log.transformer.peopleToLogId'); // Transformer service
```

### Contributing
All ideas and pull request are welcomed and appreciated.
Please, feel free to share your thought via issues.

Command to run tests: `composer test`.

#### License
License: MIT  
Author: Krzysztof Gzocha [![](https://img.shields.io/badge/Twitter-%40kgzocha-blue.svg)](https://twitter.com/kgzocha)
