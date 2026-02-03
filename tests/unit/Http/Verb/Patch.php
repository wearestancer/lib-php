<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class Patch extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb Patch Http
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }

    /**
     * @tags AbstractVerb Patch Http
     */
    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('PATCH')
        ;
    }

    /**
     * @tags AbstractVerb Patch Http
     */
    public function testIsAllowed()
    {
        $this
            ->boolean($this->newTestedInstance->isAllowed())
                ->isTrue
        ;
    }
}
