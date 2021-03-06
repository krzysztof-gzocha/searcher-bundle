<?php

namespace KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
class ParametersValidator
{
    /**
     * @param string $contextId
     * @param array  $config
     *
     * @return bool
     *
     * @throws InvalidDefinitionException
     */
    public function validateParameters(
        $contextId,
        array &$config
    ) {
        if (isset($config[AbstractContextCompilerPass::CLASS_PARAMETER])) {
            return true;
        }

        if (isset($config[AbstractContextCompilerPass::SERVICE_PARAMETER])) {
            return true;
        }

        throw new InvalidDefinitionException(sprintf(
            'You have to specify "%s" or "%s" parameters for %s '.
            'in searching context "%s".',
            AbstractContextCompilerPass::CLASS_PARAMETER,
            AbstractContextCompilerPass::SERVICE_PARAMETER,
            AbstractContextCompilerPass::CRITERIA_COLLECTION_PARAMETER,
            $contextId
        ));
    }
}
