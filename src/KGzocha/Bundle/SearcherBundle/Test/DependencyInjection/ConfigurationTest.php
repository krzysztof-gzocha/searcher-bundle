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
                        'model_collection' => [
                            'class' => null,
                            'service' => 'model_service',
                        ],
                        'imposer_collection' => [
                            'class' => 'ImposerClass',
                            'service' => null
                        ],
                        'searcher' => [
                            'class' => null,
                            'service' => 'searcher_service',
                        ],
                        'context' => [
                            'service' => 'context_service',
                            'class' => null,
                        ],
                        'models' => [
                            [
                                'class' => 'modelClass1',
                                'name' => 'model1',
                                'service' => null,
                            ],
                            [
                                'class' => null,
                                'name' => 'model2',
                                'service' => 'model2',
                            ]
                        ],
                        'imposers' => [
                            [
                                'class' => 'imposerClass1',
                                'name' => 'imposer1',
                                'service' => null,
                            ],
                            [
                                'class' => null,
                                'name' => 'imposer2',
                                'service' => 'imposer2',
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
