<?php

namespace ild78\Http\tests\unit;

use atoum;

class Response extends atoum
{
    public function testGetBody()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($body = uniqid())
            ->and($this->newTestedInstance($code, $body))
            ->then
                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($body)
        ;
    }

    public function testGetStatusCode()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($this->newTestedInstance($code))
            ->then
                ->integer($this->testedInstance->getStatusCode())
                    ->isIdenticalTo($code)
        ;
    }
}
