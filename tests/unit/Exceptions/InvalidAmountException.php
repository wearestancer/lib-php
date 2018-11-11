<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78;

class InvalidAmountException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Exceptions\InvalidArgumentException::class)
                ->implements(ild78\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('Amount must be greater than or equal to 50.')
        ;
    }
}
