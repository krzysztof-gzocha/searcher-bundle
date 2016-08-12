<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class ParametersValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @dataProvider successValidationDataProvider
     */
    public function testSuccessValidation(array $config)
    {
        $validator = new ParametersValidator();

        $this->assertTrue($validator->validateParameters('irrelevant', $config));
    }

    /**
     * @return array
     */
    public function successValidationDataProvider()
    {
        return [
            [['class' => 1, 'service' => 1]],
            [['service' => 1]],
            [['class' => 1]],
        ];
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     * @expectedExceptionMessageRegExp /contextId/
     */
    public function testWrongValidation()
    {
        $validator = new ParametersValidator();

        $config = [];
        $validator->validateParameters('contextId', $config);
    }
}
