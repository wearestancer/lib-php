<?php

namespace Stancer\Http\Verb\tests\unit;

use Stancer;

class Put extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }

    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('PUT')
        ;
    }
}
