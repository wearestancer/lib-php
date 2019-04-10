<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78;
use Psr;

class InternalServerErrorException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Exceptions\ServerException::class)
                ->implements(ild78\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('Servor error, please leave a minute to repair it and try again')
        ;
    }

    public function testGetStatus()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getStatus())
                    ->isIdenticalTo('500')
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
