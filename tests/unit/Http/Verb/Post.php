<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class Post extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb Post Http
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Http\Verb\AbstractVerb::class)
        ;
    }

    /**
     * @tags AbstractVerb Post Http
     */
    public function testCastToString()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo('POST')
        ;
    }

    /**
     * @tags AbstractVerb Post Http
     */
    public function testIsAllowed()
    {
        $this
            ->boolean($this->newTestedInstance->isAllowed())
                ->isTrue
        ;
    }
}
