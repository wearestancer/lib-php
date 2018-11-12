<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use GuzzleHttp;
use ild78;
use mock;
use Psr;

class HttpException extends atoum
{
    public function statusDataProvider()
    {
        return [
            [310, ild78\Exceptions\TooManyRedirectsException::class],
            [400, ild78\Exceptions\BadRequestException::class],
            [401, ild78\Exceptions\NotAuthorizedException::class],
            [402, ild78\Exceptions\PaymentRequiredException::class],
            [403, ild78\Exceptions\ForbiddenException::class],
            [404, ild78\Exceptions\NotFoundException::class],
            [405, ild78\Exceptions\MethodNotAllowedException::class],
            [406, ild78\Exceptions\NotAcceptableException::class],
            [407, ild78\Exceptions\ProxyAuthenticationRequiredException::class],
            [408, ild78\Exceptions\RequestTimeoutException::class],
            [409, ild78\Exceptions\ConflictException::class],
            [410, ild78\Exceptions\GoneException::class],
            [500, ild78\Exceptions\InternalServerErrorException::class],
            // Levels
            [399, ild78\Exceptions\RedirectionException::class],
            [499, ild78\Exceptions\ClientException::class],
            [599, ild78\Exceptions\ServerException::class],
        ];
    }

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

    public function testGetStatus()
    {
        $this
            ->if($class = $this->testedClass->getClass())
            ->then
                ->variable($class::getStatus())
                    ->isNull
        ;
    }
}
