<?php

namespace Stancer\tests\unit\Exceptions;

use Stancer;
use Psr;

class InvalidExternalIdException extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Exceptions\InvalidArgumentException::class)
                ->implements(Stancer\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('Invalid external ID.')
        ;
    }

    public function testGetLogLevel()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getLogLevel())
                    ->isIdenticalTo(Psr\Log\logLevel::DEBUG)
        ;
    }
}
