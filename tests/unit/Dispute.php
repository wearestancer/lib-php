<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api;
use ild78\Dispute as testedClass;

class Dispute extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\AbstractObject::class)
                ->hasMethod('getAmount') // From AmountTrait
                ->hasMethod('getCurrency') // From AmountTrait
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('disputes')
        ;
    }
}
