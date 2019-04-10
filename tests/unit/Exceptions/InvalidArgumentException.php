<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78;
use Psr;

class InvalidArgumentException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Exceptions\Exception::class)
                ->implements(ild78\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('Invalid argument')
        ;
    }

    public function testGetLogLevel()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getLogLevel())
                    ->isIdenticalTo(Psr\Log\logLevel::NOTICE)
        ;
    }
}
