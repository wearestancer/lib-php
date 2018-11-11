<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78;

class Exception extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(\Exception::class)
                ->implements(ild78\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('Unexpected error')
        ;
    }
}
