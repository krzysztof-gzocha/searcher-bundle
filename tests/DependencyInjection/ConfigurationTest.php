<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package KGzocha\Bundle\SearcherBundle\Test\DependencyInjection
 * @group di
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testContextsBuildingProcess()
    {
        $config = $this->getConfig();

        $this->assertEquals(
            $config['k_gzocha_searcher'],
            $this->process($config)
        );
    }

    protected function getConfig()
    {
        return [
            'k_gzocha_searcher' => [
                'contexts' => [
                    'people' => [
                        'criteria_collection' => [
                            'class' => null,
                            'service' => 'model_service',
                        ],
                        'builder_collection' => [
                            'class' => 'ImposerClass',
                            'service' => null
                        ],
                        'searcher' => [
                            'class' => null,
                            'service' => 'searcher_service',
                            'wrapper_class' => null,
                        ],
                        'context' => [
                            'service' => 'context_service',
                            'class' => null,
                        ],
                        'criteria' => [
                            [
                                'class' => 'criteriaClass1',
                                'name' => 'criteria1',
                                'service' => null,
                            ],
                            [
                                'class' => null,
                                'name' => 'criteria1',
                                'service' => 'criteria2',
                            ]
                        ],
                        'builders' => [
                            [
                                'class' => 'builderClass1',
                                'name' => 'builder1',
                                'service' => null,
                            ],
                            [
                                'class' => null,
                                'name' => 'builder2',
                                'service' => 'builder2',
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function process(array $config)
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $config);
    }
}