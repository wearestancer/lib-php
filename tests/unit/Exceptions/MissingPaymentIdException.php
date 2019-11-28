<?php

namespace ild78\tests\unit\Exceptions;

use ild78;
use Psr;

class MissingPaymentIdException extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
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
                    ->isIdenticalTo('A payment ID is mandatory. Maybe you forgot to send the payment.')
        ;
    }

    public function testGetLogLevel()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getLogLevel())
                    ->isIdenticalTo(Psr\Log\logLevel::CRITICAL)
        ;
    }
}
