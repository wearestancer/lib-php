<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api;
use ild78\Card as testedClass;

class Card extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
        ;
    }

    public function testGetForbiddenProperties()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->getForbiddenProperties())
                    ->contains('created') // from parent
                    ->contains('endpoint') // from parent
                    ->contains('id') // from parent
                    ->contains('last4')
        ;
    }

    public function testSetNumber()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($number = rand(pow(10, 15), pow(10, 16) - 1))
            ->and($last = substr((string) $number, -4))
            ->then
                ->variable($this->testedInstance->getNumber())
                    ->isNull

                ->variable($this->testedInstance->getLast4())
                    ->isNull

                ->object($this->testedInstance->setNumber($number))
                    ->isTestedInstance

                ->integer($this->testedInstance->getNumber())
                    ->isIdenticalTo($number)

                ->string($this->testedInstance->getLast4())
                    ->isIdenticalTo($last)
        ;
    }
}
