<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78;
use Psr;

class PaymentRequiredException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Exceptions\ClientException::class)
                ->implements(ild78\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('HTTP 402 - Payment Required')
        ;
    }

    public function testGetLogLevel()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getLogLevel())
                    ->isIdenticalTo(Psr\Log\logLevel::ERROR)
        ;
    }

    public function testGetStatus()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getStatus())
                    ->isIdenticalTo('402')
        ;
    }
}
