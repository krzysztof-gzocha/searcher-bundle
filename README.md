# SearcherBundle [![Build Status](https://travis-ci.org/krzysztof-gzocha/searcher-bundle.svg?branch=master)](https://travis-ci.org/krzysztof-gzocha/searcher-bundle)

This bundle is providing integration between Symfony and [Searcher](https://github.com/krzysztof-gzocha/searcher)

### Installation
You can install this bundle via composer
```
composer require krzysztof-gzocha/searcher-bundle
```
and don't forget to register it in your AppKernel:
```
class AppKernel extends Kernel
{
  public function registerBundles()
  {
    $bundles = array(
      /** Your bundles **/
      
      new KGzocha\Bundle\SearcherBundle\KGzochaSearcherBundle(),
    );
  }
}
```

### Contributing
All ideas and pull request are welcomed and appreciated.
Please, feel free to share your thought via issues.

Command to run tests: `bin/phing run-tests`.
