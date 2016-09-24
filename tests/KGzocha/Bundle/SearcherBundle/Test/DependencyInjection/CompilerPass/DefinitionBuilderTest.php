<?php

namespace KGzocha\Bundle\SearcherBundle\Test\DependencyInjection\CompilerPass;

use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\DefinitionBuilder;
use KGzocha\Bundle\SearcherBundle\DependencyInjection\CompilerPass\ParametersValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @group di
 */
class DefinitionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingService()
    {
        $builder = new DefinitionBuilder(new ParametersValidator());
        $container = new ContainerBuilder();
        $oldServiceName = 'old-service-name';
        $container->setDefinition($oldServiceName, new Definition('\stdClass'));
        $config = ['service' => $oldServiceName];

        $builder->buildDefinition(
            $container,
            'someContextId',
            'new-service-name',
            $config
        );

        $this->assertTrue($container->has('new-service-name'));
        $this->assertInstanceOf('\stdClass', $container->get('new-service-name'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     * @expectedExceptionMessageRegExp /^Could not create \"new-service-name" service, because configured service \"non-existing-service\" does not exist\.$/
     */
    public function testCreatingNonExistingService()
    {
        $builder = new DefinitionBuilder(new ParametersValidator());
        $container = new ContainerBuilder();
        $config = ['service' => 'non-existing-service'];

        $builder->buildDefinition(
            $container,
            'someContextId',
            'new-service-name',
            $config
        );
    }

    public function testCreatingFromClass()
    {
        $builder = new DefinitionBuilder(new ParametersValidator());
        $container = new ContainerBuilder();
        $config = ['class' => '\stdClass'];

        $builder->buildDefinition(
            $container,
            'someContextId',
            'new-service-name',
            $config
        );

        $this->assertTrue($container->has('new-service-name'));
        $this->assertInstanceOf('\stdClass', $container->get('new-service-name'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     * @expectedExceptionMessageRegExp /^Could not create service "new-service-name", because class "\\NonExistingClass" does not exist\.$/
     */
    public function testCreatingFromMissingClass()
    {
        $builder = new DefinitionBuilder(new ParametersValidator());
        $container = new ContainerBuilder();
        $config = ['class' => '\NonExistingClass'];

        $builder->buildDefinition(
            $container,
            'someContextId',
            'new-service-name',
            $config
        );
    }
}
