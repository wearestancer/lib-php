<?php

namespace Stancer\tests\unit\Exceptions;

use Stancer;
use mock;
use Psr;

class Exception extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(\Exception::class)
                ->implements(Stancer\Interfaces\ExceptionInterface::class)
        ;
    }

    public function testCreate()
    {
        $this
            ->given($class = $this->testedClass->getClass())

            ->assert('No params')
                ->object($class::create())
                    ->isInstanceOf($class)

            ->assert('Complete params')
                ->given($message = uniqid())
                ->and($code = rand(0, 100))
                ->and($previous = new mock\Exception())
                ->and($params = compact('message', 'code', 'previous'))
                ->then
                    ->object($obj = $class::create($params))
                        ->isInstanceOf($class)

                    ->string($obj->getMessage())
                        ->isIdenticalTo($message)

                    ->integer($obj->getCode())
                        ->isIdenticalTo($code)

                    ->object($obj->getPrevious())
                        ->isIdenticalTo($previous)
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
