<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api as testedClass;

class Api extends atoum
{
    public function testGetHost_SetHost()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($defaultHost = 'api.iliad78.net')
            ->and($randomHost = uniqid())
            ->then
                ->string($this->testedInstance->getHost())
                    ->isIdenticalTo($defaultHost)
                ->object($this->testedInstance->setHost($randomHost))
                    ->isTestedInstance
                ->string($this->testedInstance->getHost())
                    ->isIdenticalTo($randomHost)
        ;
    }
}
