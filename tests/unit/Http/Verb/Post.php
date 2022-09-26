<?php

namespace Stancer\Http\Verb\tests\unit;

use Stancer;

class Post extends Stancer\Tests\atoum
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
                    ->isIdenticalTo('POST')
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
