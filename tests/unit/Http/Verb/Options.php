<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class Options extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb Options Http
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }
    /**
     * @tags AbstractVerb Options Http
     */
    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('OPTIONS')
        ;
    }
}
