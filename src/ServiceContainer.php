<?php


namespace Laravelday2021;


use ReflectionClass;

class ServiceContainer
{
    private array $bindings = [];

    private array $singletonBindings = [];

    public function resolve($class)
    {
        if ($this->hasSingleton($class)) {
            return $this->singletonBindings[$class];
        }

        if ($this->hasBinding($class)) {
            return $this->get($this->bindings[$class]);
        }

        return $this->get($class);
    }

    public function bind($abstract, $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton($abstract, $concrete)
    {
        $this->singletonBindings[$abstract] = $this->get($concrete);
    }

    private function get($class)
    {
        if ($this->isNotInstantiable($class)) {
            return $class;
        }

        if ($this->isCallable($class)) {
            return \Closure::fromCallable($class)->call($this, $this);
        }

        return $this->makeFromAbstractClass($class);
    }

    private function getDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependencies[] = $this->resolve($parameter->getType()->getName());
        }

        return $dependencies;
    }

    /**
     * @param $class
     * @return bool
     */
    private function isNotInstantiable($class): bool
    {
        return is_string($class) && !class_exists($class);
    }

    /**
     * @param $class
     * @return bool
     */
    private function isCallable($class): bool
    {
        return $class instanceof \Closure;
    }

    /**
     * @param $class
     * @return bool
     */
    private function hasSingleton($class): bool
    {
        return array_key_exists($class, $this->singletonBindings);
    }

    /**
     * @param $class
     * @return bool
     */
    private function hasBinding($class): bool
    {
        return array_key_exists($class, $this->bindings);
    }

    /**
     * @param $class
     * @return object
     * @throws \ReflectionException
     */
    private function makeFromAbstractClass($class): object
    {
        $dependencies = [];

        $r = new ReflectionClass($class);
        $constructor = $r->getConstructor();

        if ($constructor) {
            $dependencies = $this->getDependencies($constructor->getParameters());
        }

        $result = $r->newInstanceArgs($dependencies);

        return $result;
    }
}