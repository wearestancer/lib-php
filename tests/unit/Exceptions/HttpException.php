<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use GuzzleHttp;
use ild78\Exceptions;
use mock;

class HttpException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf(Exceptions\Exception::class)
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
