<?php

namespace Stancer\tests\unit\Exceptions;

use Stancer;
use Psr;

class ServerException extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Exceptions\HttpException::class)
                ->implements(Stancer\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('HTTP 5xx - Server error')
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

    public function testGetStatus()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getStatus())
                    ->isIdenticalTo('5xx')
        ;
    }
}
