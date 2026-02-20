<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class Get extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb Get Http
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }

    /**
     * @tags AbstractVerb Get Http
     */
    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('GET')
        ;
    }

    /**
     * @tags AbstractVerb Get Http
     */
    public function testIsAllowed()
    {
        $this
            ->boolean($this->newTestedInstance->isAllowed())
                ->isTrue
        ;
    }
}
