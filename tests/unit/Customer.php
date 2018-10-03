<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Core;
use ild78\Customer as testedClass;

class Customer extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Core::class)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('customers')
        ;
    }
}
