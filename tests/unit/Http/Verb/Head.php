<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class Head extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb Head Http
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }

    /**
     * @tags AbstractVerb Head Http
     */
    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('HEAD')
        ;
    }
}
