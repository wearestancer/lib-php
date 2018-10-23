<?php

namespace ild78\tests\unit;

use atoum;
use ild78;

class Refund extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf(ild78\Api\AbstractObject::class)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('refunds')
        ;
    }
}
