<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class Put extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb Put Http
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }

    /**
     * @tags AbstractVerb Put Http
     */
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
