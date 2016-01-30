<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass
 */
abstract class AbstractCompilerPass implements CompilerPassInterface
{
    /**
     * @param array $array
     * @param string $key
     *
     * @return string
     */
    protected function getValueFromLastKey(array &$array, $key)
    {
        $lastElement = end($array);
        if (!is_array($lastElement) || !array_key_exists($key, $lastElement)) {
            throw new \RuntimeException(sprintf(
                'Parameter %s is missing from configuration. Array: "%s"',
                $key,
                print_r($array, true)
            ));
        }

        return $lastElement[$key];
    }
}
