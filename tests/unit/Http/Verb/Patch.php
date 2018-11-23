<?php

namespace ild78\Http\Verb\tests\unit;

use atoum;
use ild78;

class Patch extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Http\Verb\AbstractVerb::class)
        ;
    }

    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('PATCH')
        ;
    }

    public function testIsAllowed()
    {
        $this
            ->boolean($this->newTestedInstance->isAllowed())
                ->isTrue
        ;
    }
}
