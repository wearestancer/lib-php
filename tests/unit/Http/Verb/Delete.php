<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class Delete extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb Delete Http
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }

    /**
     * @tags AbstractVerb Delete Http
     */
    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('DELETE')
        ;
    }

    /**
     * @tags AbstractVerb Delete Http
     */
    public function testIsAllowed()
    {
        $this
            ->boolean($this->newTestedInstance->isAllowed())
                ->isTrue
        ;
    }
}
