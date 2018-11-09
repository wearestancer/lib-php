<?php

namespace ild78\Http\tests\unit;

use atoum;
use Psr;

class Request extends atoum
{
    public function testGetMethod()
    {
        $this
            ->given($method = uniqid())
            ->and($uri = uniqid())
            ->if($this->newTestedInstance($method, $uri))
            ->then
                ->string($this->testedInstance->getMethod())
                    ->isIdenticalTo(strtoupper($method))
        ;
    }

    public function testGetUri()
    {
        $this
            ->given($method = uniqid())
            ->and($uri = uniqid())
            ->if($this->newTestedInstance($method, $uri))
            ->then
                ->string($this->testedInstance->getUri())
                    ->isIdenticalTo($uri)
        ;
    }
}
