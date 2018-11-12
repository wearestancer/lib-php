<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78;

class MissingPaymentMethodException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Exceptions\BadMethodCallException::class)
                ->implements(ild78\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('You must provide a valid credit card or SEPA account to make a payment.')
        ;
    }
}
