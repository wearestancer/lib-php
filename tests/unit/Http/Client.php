<?php

namespace ild78\Http\tests\unit;

use atoum;
use ild78;

class Client extends atoum
{
    public function errorDataProvider()
    {
        $datas = [];

        $datas[] = [
            CURLE_TOO_MANY_REDIRECTS,
            310,
            ild78\Exceptions\TooManyRedirectsException::class,
            ild78\Exceptions\TooManyRedirectsException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            400,
            ild78\Exceptions\BadRequestException::class,
            ild78\Exceptions\BadRequestException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            401,
            ild78\Exceptions\NotAuthorizedException::class,
            ild78\Exceptions\NotAuthorizedException::getDefaultMessage(),
        ];

        $datas = [];
        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            402,
            ild78\Exceptions\PaymentRequiredException::class,
            ild78\Exceptions\PaymentRequiredException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            403,
            ild78\Exceptions\ForbiddenException::class,
            ild78\Exceptions\ForbiddenException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            404,
            ild78\Exceptions\NotFoundException::class,
            ild78\Exceptions\NotFoundException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            405,
            ild78\Exceptions\MethodNotAllowedException::class,
            ild78\Exceptions\MethodNotAllowedException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            406,
            ild78\Exceptions\NotAcceptableException::class,
            ild78\Exceptions\NotAcceptableException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            407,
            ild78\Exceptions\ProxyAuthenticationRequiredException::class,
            ild78\Exceptions\ProxyAuthenticationRequiredException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            408,
            ild78\Exceptions\RequestTimeoutException::class,
            ild78\Exceptions\RequestTimeoutException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            409,
            ild78\Exceptions\ConflictException::class,
            ild78\Exceptions\ConflictException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            410,
            ild78\Exceptions\GoneException::class,
            ild78\Exceptions\GoneException::getDefaultMessage(),
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            500,
            ild78\Exceptions\InternalServerErrorException::class,
            ild78\Exceptions\InternalServerErrorException::getDefaultMessage(),
        ];

        return $datas;
    }

    public function testClass()
    {
        $this
            ->testedClass
                ->implements(ild78\Interfaces\HttpClientInterface::class)
        ;
    }

    public function test__construct__destruct()
    {
        $this
            ->given($ressource = uniqid())
            ->and($this->function->curl_init = $ressource)
            ->and($this->function->curl_close = true)
            ->then
                ->object($this->newTestedInstance)
                ->function('curl_init')->wasCalled->once
                ->function('curl_close')->wasCalled->never

                ->variable($this->testedInstance->__destruct())
                ->function('curl_close')
                    ->wasCalledWithArguments($ressource)
                        ->once
        ;
    }

    public function testGetCurlResource()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->resource($this->testedInstance->getCurlResource())
                    ->isOfType('curl')
        ;
    }

    public function testRequest()
    {
        $this
            ->assert('Basic request')
                ->given($this->newTestedInstance)
                ->and($curl = $this->testedInstance->getCurlResource())
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body = uniqid())
                ->and($this->function->curl_getinfo = 200)
                ->and($this->function->curl_errno = 0)
                ->and($this->function->curl_error = '')
                ->and($method = 'GET')
                ->and($host = uniqid())
                ->then
                    ->object($response = $this->testedInstance->request($method, $host))
                        ->isInstanceOf(ild78\Http\Response::class)

                    ->string($response->getBody())
                        ->isIdenticalTo($body)

                    ->function('curl_setopt')
                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_URL, $host)
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_CUSTOMREQUEST, $method)
                            ->once

                        ->wasCalledWithArguments($curl, CURLOPT_CONNECTTIMEOUT)
                            ->never

                        ->wasCalledWithArguments($curl, CURLOPT_TIMEOUT)
                            ->never

                        ->wasCalledWithArguments($curl, CURLOPT_HTTPHEADER)
                            ->never

                    ->function('curl_exec')
                        ->wasCalled
                            ->once

            ->assert('POST with headers and data')
                ->given($this->newTestedInstance)
                ->and($curl = $this->testedInstance->getCurlResource())
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body = uniqid())
                ->and($this->function->curl_getinfo = 200)
                ->and($this->function->curl_errno = 0)
                ->and($this->function->curl_error = '')
                ->and($method = 'POST')
                ->and($host = uniqid())
                ->and($options = [
                    'timeout' => rand(1, 1000),
                    'headers' => [
                        uniqid() => uniqid(),
                        uniqid() => uniqid(),
                    ],
                    'body' => [
                        uniqid() => uniqid(),
                        uniqid() => uniqid(),
                    ],
                ])
                ->then
                    ->object($response = $this->testedInstance->request($method, $host, $options))
                        ->isInstanceOf(ild78\Http\Response::class)

                    ->string($response->getBody())
                        ->isIdenticalTo($body)

                    ->function('curl_setopt')
                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_URL, $host)
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_CUSTOMREQUEST, $method)
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_CONNECTTIMEOUT, $options['timeout'])
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_TIMEOUT, $options['timeout'])
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_HTTPHEADER, $options['headers'])
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_POSTFIELDS, $options['body'])
                            ->once

                    ->function('curl_exec')
                        ->wasCalled
                            ->once

            ->assert('cURL error')
                ->given($this->newTestedInstance)
                ->and($curl = $this->testedInstance->getCurlResource())
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body = uniqid())
                ->and($this->function->curl_getinfo = $code = rand(600, 999))
                ->and($this->function->curl_errno = $error = rand(1, 1000))
                ->and($this->function->curl_error = $message = uniqid())
                ->and($method = 'GET')
                ->and($host = uniqid())
                ->then
                    ->exception(function () use ($method, $host) {
                        $this->testedInstance->request($method, $host);
                    })
                        ->isInstanceOf(ild78\Exceptions\HttpException::class)
                        ->hasCode($code)
                        ->message
                            ->isIdenticalTo($message)

                    ->object($this->exception->getRequest())
                        ->isInstanceOf(ild78\Http\Request::class)

                    ->object($response = $this->exception->getResponse())
                        ->isInstanceOf(ild78\Http\Response::class)

                    ->string($response->getBody())
                        ->isIdenticalTo($body)

                    ->function('curl_setopt')
                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_URL, $host)
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_CUSTOMREQUEST, $method)
                            ->once

                    ->function('curl_exec')
                        ->wasCalled
                            ->once
        ;
    }

    /**
     * @dataProvider errorDataProvider
     */
    public function testRequest_exceptions($error, $code, $class, $message)
    {
        $this
            ->assert($code . ' should throw ' . $class)
                ->given($this->newTestedInstance)
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body = uniqid())
                ->and($this->function->curl_getinfo = $code)
                ->and($this->function->curl_errno = $error)
                ->and($this->function->curl_error = uniqid())
                ->then
                    ->exception(function () {
                        $this->testedInstance->request(uniqid(), uniqid());
                    })
                        ->isInstanceOf($class)
                        ->hasCode($code)
                        ->message
                            ->isIdenticalTo($message)
        ;
    }
}
