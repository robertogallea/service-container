<?php


namespace Tests\Dummy;


class AClassWithDependencies
{
    public function __construct(public AClass $dependency)
    {

    }
}