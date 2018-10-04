<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Core as testedClass;

class Core extends atoum
{
    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isEmpty
        ;
    }

    public function testGetId()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull // No default value
        ;
    }
}
