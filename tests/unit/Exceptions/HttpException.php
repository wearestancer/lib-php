<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use GuzzleHttp;
use ild78;
use mock;

class HttpException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Exceptions\Exception::class)
                ->implements(ild78\Interfaces\ExceptionInterface::class)
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
                ->and($previous = new mock\Exception)
                ->and($request = new mock\Psr\Http\Message\RequestInterface)
                ->and($response = new mock\Psr\Http\Message\ResponseInterface)
                ->and($params = compact('message', 'code', 'previous', 'request', 'response'))
                ->then
                    ->object($obj = $class::create($params))
                        ->isInstanceOf($class)

                    ->string($obj->getMessage())
                        ->isIdenticalTo($message)

                    ->integer($obj->getCode())
                        ->isIdenticalTo($code)

                    ->object($obj->getPrevious())
                        ->isIdenticalTo($previous)

                    ->object($obj->getRequest())
                        ->isIdenticalTo($request)

                    ->object($obj->getResponse())
                        ->isIdenticalTo($response)
        ;
    }

    public function testGetDefaultMessage()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getDefaultMessage())
                    ->isIdenticalTo('HTTP error')
        ;
    }

    public function testGetRequest()
    {
        $this
            ->assert('Return null when nothing particular is done')
                ->given($this->newTestedInstance())
                ->then
                    ->variable($this->testedInstance->getRequest())
                        ->isNull

            ->assert('Return object when a Guzzle RequestException is used')
                ->given($request = new mock\Psr\Http\Message\RequestInterface)

                ->and($previous = new GuzzleHttp\Exception\RequestException(uniqid(), $request))

                ->if($this->newTestedInstance(uniqid(), rand(0, 100), $previous))
                ->then
                    ->object($this->testedInstance->getRequest())
                        ->isInstanceOf($request)
        ;
    }

    public function testGetResponse()
    {
        $this
            ->assert('Return null when nothing particular is done')
                ->given($this->newTestedInstance())
                ->then
                    ->variable($this->testedInstance->getResponse())
                        ->isNull

            ->assert('Return object when a Guzzle RequestException is used')
                ->given($request = new mock\Psr\Http\Message\RequestInterface)
                ->and($response = new mock\Psr\Http\Message\ResponseInterface)

                ->and($previous = new GuzzleHttp\Exception\RequestException(uniqid(), $request, $response))

                ->if($this->newTestedInstance(uniqid(), rand(0, 100), $previous))
                ->then
                    ->object($this->testedInstance->getResponse())
                        ->isInstanceOf($response)
        ;
    }
}
