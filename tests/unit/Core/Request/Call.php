<?php

namespace ild78\tests\unit\Core\Request;

use GuzzleHttp;
use ild78;

class Call extends ild78\Tests\atoum
{
    public function testException()
    {
        $this
            ->assert('Null by default')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getException())
                        ->isIdenticalTo($this->testedInstance->exception)
                        ->isNull

            ->assert('Can have an instance')
                ->given($exception = new ild78\Exceptions\Exception)

                ->if($this->newTestedInstance(['exception' => $exception]))
                ->then
                    ->object($this->testedInstance->getException())
                        ->isIdenticalTo($this->testedInstance->exception)
                        ->isIdenticalTo($exception)

            ->assert('Can not be changed')
                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () {
                        $this->testedInstance->setException(new ild78\Exceptions\Exception);
                    })
                        ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "exception".')
        ;
    }

    public function testRequest()
    {
        $this
            ->assert('Null by default')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getRequest())
                        ->isIdenticalTo($this->testedInstance->request)
                        ->isNull

            ->assert('Can have an instance of ild78\Http\Request')
                ->given($request = new ild78\Http\Request('GET', 'http://127.0.0.1'))

                ->if($this->newTestedInstance(['request' => $request]))
                ->then
                    ->object($this->testedInstance->getRequest())
                        ->isIdenticalTo($this->testedInstance->request)
                        ->isIdenticalTo($request)

            ->assert('Can have an instance of GuzzleHttp\Psr7\Request')
                ->given($request = new GuzzleHttp\Psr7\Request('GET', 'http://127.0.0.1'))

                ->if($this->newTestedInstance(['request' => $request]))
                ->then
                    ->object($this->testedInstance->getRequest())
                        ->isIdenticalTo($this->testedInstance->request)
                        ->isIdenticalTo($request)

            ->assert('Can not be changed')
                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () {
                        $this->testedInstance->setRequest(new ild78\Http\Request('GET', 'http://127.0.0.1'));
                    })
                        ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "request".')
        ;
    }

    public function testResponse()
    {
        $this
            ->assert('Null by default')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getResponse())
                        ->isIdenticalTo($this->testedInstance->response)
                        ->isNull

            ->assert('Can have an instance of ild78\Http\Response')
                ->given($response = new ild78\Http\Response(200))

                ->if($this->newTestedInstance(['response' => $response]))
                ->then
                    ->object($this->testedInstance->getResponse())
                        ->isIdenticalTo($this->testedInstance->response)
                        ->isIdenticalTo($response)

            ->assert('Can have an instance of GuzzleHttp\Psr7\Response')
                ->given($response = new GuzzleHttp\Psr7\Response)

                ->if($this->newTestedInstance(['response' => $response]))
                ->then
                    ->object($this->testedInstance->getResponse())
                        ->isIdenticalTo($this->testedInstance->response)
                        ->isIdenticalTo($response)

            ->assert('Can not be changed')
                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () {
                        $this->testedInstance->setResponse(new ild78\Http\Response(200));
                    })
                        ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "response".')
        ;
    }
}
