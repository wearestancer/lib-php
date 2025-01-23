<?php

namespace Stancer\tests\unit\Exceptions;

use GuzzleHttp;
use Stancer;
use mock;
use Psr;

class HttpException extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Http;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Exceptions\Exception::class)
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
                ->and($request = new mock\Psr\Http\Message\RequestInterface())
                ->and($response = new mock\Psr\Http\Message\ResponseInterface())
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

    /**
     * @dataProvider statusDataProvider
     */
    public function testCreate_withStatus($status, $expected)
    {
        $this
            ->given($class = $this->testedClass->getClass())

            ->assert('HTTP ' . $status)
                ->object($class::create(['status' => $status]))
                    ->isInstanceOf($expected)
        ;
    }

    /**
     * @dataProvider statusDataProvider
     */
    public function testGetClassFromStatus($status, $expected)
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getClassFromStatus($status))
                    ->isIdenticalTo($expected)
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

    public function testGetLogLevel()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getLogLevel())
                    ->isIdenticalTo(Psr\Log\logLevel::WARNING)
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
                ->given($request = new mock\Psr\Http\Message\RequestInterface())

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
                ->given($request = new mock\Psr\Http\Message\RequestInterface())
                ->and($response = new mock\Psr\Http\Message\ResponseInterface())
                ->and($this->calling($response)->getStatusCode = random_int(400, 500))

                ->and($previous = new GuzzleHttp\Exception\RequestException(uniqid(), $request, $response))

                ->if($this->newTestedInstance(uniqid(), rand(0, 100), $previous))
                ->then
                    ->object($this->testedInstance->getResponse())
                        ->isInstanceOf($response)
        ;
    }

    public function testGetStatus()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->string($class::getStatus())
                    ->isEmpty
        ;
    }
}
