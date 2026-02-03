<?php

namespace Stancer\tests\unit\Core\Request;

use GuzzleHttp;
use Stancer;

class Call extends Stancer\Tests\atoum
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
                ->given($exception = new Stancer\Exceptions\Exception())

                ->if($this->newTestedInstance(['exception' => $exception]))
                ->then
                    ->object($this->testedInstance->getException())
                        ->isIdenticalTo($this->testedInstance->exception)
                        ->isIdenticalTo($exception)

            ->assert('Can not be changed')
                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () {
                        $this->testedInstance->setException(new Stancer\Exceptions\Exception());
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
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

            ->assert('Can have an instance of Stancer\\Http\\Request')
                ->given($request = new Stancer\Http\Request('GET', 'http://127.0.0.1'))

                ->if($this->newTestedInstance(['request' => $request]))
                ->then
                    ->object($this->testedInstance->getRequest())
                        ->isIdenticalTo($this->testedInstance->request)
                        ->isIdenticalTo($request)

            ->assert('Can have an instance of GuzzleHttp\\Psr7\\Request')
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
                        $this->testedInstance->setRequest(new Stancer\Http\Request('GET', 'http://127.0.0.1'));
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
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

            ->assert('Can have an instance of Stancer\\Http\\Response')
                ->given($response = new Stancer\Http\Response(200))

                ->if($this->newTestedInstance(['response' => $response]))
                ->then
                    ->object($this->testedInstance->getResponse())
                        ->isIdenticalTo($this->testedInstance->response)
                        ->isIdenticalTo($response)

            ->assert('Can have an instance of GuzzleHttp\\Psr7\\Response')
                ->given($response = new GuzzleHttp\Psr7\Response())

                ->if($this->newTestedInstance(['response' => $response]))
                ->then
                    ->object($this->testedInstance->getResponse())
                        ->isIdenticalTo($this->testedInstance->response)
                        ->isIdenticalTo($response)

            ->assert('Can not be changed')
                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () {
                        $this->testedInstance->setResponse(new Stancer\Http\Response(200));
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "response".')
        ;
    }
}
