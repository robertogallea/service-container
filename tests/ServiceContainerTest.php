<?php


namespace Tests;


use Laravelday2021\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Tests\Dummy\AClass;
use Tests\Dummy\AClassWithDependencies;
use Tests\Dummy\AnInterface;
use Tests\Dummy\ASingleTonClass;

class ServiceContainerTest extends TestCase
{
    protected ServiceContainer $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new ServiceContainer();
    }

    /** @test */
    public function if_nothing_is_bound_it_returns_the_abstract_class()
    {
        $implementation = $this->container->resolve(AClass::class);
        $this->assertInstanceOf(AClass::class, $implementation);
    }

    /** @test */
    public function if_abstract_class_is_bounded_the_concrete_implementation_is_built()
    {
        $this->container->bind(AnInterface::class, AClass::class);

        $implementation = $this->container->resolve(AnInterface::class);
        $this->assertInstanceOf(AClass::class, $implementation);
    }

    /** @test */
    public function if_dependency_has_dependencies_they_are_resolved()
    {
        $this->container->bind(AnInterface::class, AClassWithDependencies::class);

        $implementation = $this->container->resolve(AnInterface::class);
        $this->assertInstanceOf(AClassWithDependencies::class, $implementation);
        $this->assertInstanceOf(AClass::class, $implementation->dependency);

    }

    /** @test */
    public function a_closure_can_be_used_as_binding()
    {
        $this->container->bind(AnInterface::class, function ($container) {
            return "ok";
        });

        $implementation = $this->container->resolve(AnInterface::class);

        $this->assertEquals($implementation, "ok");
    }

    /** @test */
    public function a_string_can_be_used_as_binding()
    {
        $this->container->bind('a_string', 'a_value');
        $this->assertEquals($this->container->resolve('a_string'), 'a_value');
    }

    /** @test */
    public function it_can_bind_singleton_instances()
    {
        $this->container->singleton(AnInterface::class, ASingleTonClass::class);

        $implementation = $this->container->resolve(AnInterface::class);
        $this->assertEquals(0, $implementation->value);

        $implementation->value++;

        $implementation2 = $this->container->resolve(AnInterface::class);
        $this->assertEquals(1, $implementation2->value);

        $this->assertEquals($implementation, $implementation2);
    }
}