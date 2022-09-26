<?php

namespace Stancer\tests\unit\Exceptions;

use Stancer;
use Psr;

class MissingPaymentIdException extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Exceptions\BadMethodCallException::class)
                ->implements(Stancer\Interfaces\ExceptionInterface::class)
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
