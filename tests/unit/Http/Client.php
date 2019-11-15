<?php

namespace ild78\Http\tests\unit;

use ild78;
use mock;

class Client extends ild78\Tests\atoum
{
    public function errorDataProvider()
    {
        $datas = [];

        $datas[] = [
            CURLE_TOO_MANY_REDIRECTS,
            310,
            ild78\Exceptions\TooManyRedirectsException::class,
            ild78\Exceptions\TooManyRedirectsException::getDefaultMessage(),
            'critical',
            'HTTP 310 - Too Many Redirection',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            400,
            ild78\Exceptions\BadRequestException::class,
            ild78\Exceptions\BadRequestException::getDefaultMessage(),
            'critical',
            'HTTP 400 - Bad Request',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            401,
            ild78\Exceptions\NotAuthorizedException::class,
            ild78\Exceptions\NotAuthorizedException::getDefaultMessage(),
            'notice',
            'HTTP 401 - Invalid credential : %s',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            402,
            ild78\Exceptions\PaymentRequiredException::class,
            ild78\Exceptions\PaymentRequiredException::getDefaultMessage(),
            'error',
            'HTTP 402 - Payment Required',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            403,
            ild78\Exceptions\ForbiddenException::class,
            ild78\Exceptions\ForbiddenException::getDefaultMessage(),
            'error',
            'HTTP 403 - Forbidden',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            404,
            ild78\Exceptions\NotFoundException::class,
            ild78\Exceptions\NotFoundException::getDefaultMessage(),
            'error',
            'HTTP 404 - Not Found',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            405,
            ild78\Exceptions\MethodNotAllowedException::class,
            ild78\Exceptions\MethodNotAllowedException::getDefaultMessage(),
            'critical',
            'HTTP 405 - Method Not Allowed',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            406,
            ild78\Exceptions\NotAcceptableException::class,
            ild78\Exceptions\NotAcceptableException::getDefaultMessage(),
            'error',
            'HTTP 406 - Not Acceptable',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            407,
            ild78\Exceptions\ProxyAuthenticationRequiredException::class,
            ild78\Exceptions\ProxyAuthenticationRequiredException::getDefaultMessage(),
            'error',
            'HTTP 407 - Proxy Authentication Required',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            408,
            ild78\Exceptions\RequestTimeoutException::class,
            ild78\Exceptions\RequestTimeoutException::getDefaultMessage(),
            'error',
            'HTTP 408 - Request Timeout',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            409,
            ild78\Exceptions\ConflictException::class,
            ild78\Exceptions\ConflictException::getDefaultMessage(),
            'error',
            'HTTP 409 - Conflict',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            410,
            ild78\Exceptions\GoneException::class,
            ild78\Exceptions\GoneException::getDefaultMessage(),
            'error',
            'HTTP 410 - Gone',
        ];

        $datas[] = [
            rand(1, 100) + CURLE_TOO_MANY_REDIRECTS, // Prevent from having a 310 error
            500,
            ild78\Exceptions\InternalServerErrorException::class,
            ild78\Exceptions\InternalServerErrorException::getDefaultMessage(),
            'critical',
            'HTTP 500 - Internal Server Error',
        ];

        return $datas;
    }

    public function headerLineDataProvider()
    {
        // ($line, $expectedReturn, $expectedName, $expectedValues)

        $lines = [];

        $lines[] = [
            'HTTP/1.1 401 Unauthorized',
            null,
            'Status-Line',
            ['HTTP/1.1 401 Unauthorized'],
        ];

        $lines[] = [
            'Content-Type: application/json ',
            null,
            'Content-Type',
            ['application/json'],
        ];

        $lines[] = [
            'Content-Length: 105' . "\n",
            null,
            'Content-Length',
            ['105'],
        ];

        $lines[] = [
            'Connection: keep-alive',
            null,
            'Connection',
            ['keep-alive'],
        ];

        $lines[] = [
            'Date: Mon, 12 Nov 2018 15:42:16 GMT',
            null,
            'Date',
            ['Mon, 12 Nov 2018 15:42:16 GMT'],
        ];

        $lines[] = [
            'Allow: GET, HEAD, OPTIONS, POST',
            null,
            'Allow',
            ['GET', 'HEAD', 'OPTIONS', 'POST'],
        ];

        $lines[] = [
            "\r\n", // Will be triggered in curl callback
            null,
            null,
            null,
        ];

        // Add return value
        array_walk($lines, function (&$value) {
            $value[1] = strlen($value[0]);
        });

        return $lines;
    }

    public function testClass()
    {
        $this
            ->currentlyTestedClass
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

    public function testGetLastRequest_LastResponse()
    {
        $this
            ->given($config = ild78\Config::init([]))

            ->if($this->newTestedInstance)
            ->and($curl = $this->testedInstance->getCurlResource())

            ->if($this->function->curl_exec = $body = uniqid())
            ->and($this->function->curl_getinfo = 200)
            ->and($this->function->curl_errno = 0)
            ->and($this->function->curl_error = '')
            ->and($method = 'POST')
            ->and($host = uniqid())
            ->and($query = '/' . uniqid())
            ->and($url = 'http://' . $host . $query)
            ->and($options = [
                'timeout' => rand(1, 1000),
                'headers' => [
                    uniqid() => uniqid(),
                    uniqid() => [uniqid(), uniqid()],
                ],
                'body' => uniqid(),
            ])
            ->and($headers = [])
            ->when(function () use (&$headers, $options, $host) {
                foreach ($options['headers'] as $key => $value) {
                    $headers[$key] = (array) $value;
                }

                $headers['Host'] = [$host];
            })

            ->then
                ->variable($this->testedInstance->getLastRequest())
                    ->isNull

                ->variable($this->testedInstance->getLastResponse())
                    ->isNull

                ->object($response = $this->testedInstance->request($method, $url, $options))

                ->object($this->testedInstance->getLastResponse())
                    ->isInstanceOf(ild78\Http\Response::class)
                    ->isIdenticalTo($response)

                ->object($request = $this->testedInstance->getLastRequest())
                    ->isInstanceOf(ild78\Http\Request::class)

                ->string($request->getMethod())
                    ->isIdenticalTo($method)

                ->string($request->getUri())
                    ->isIdenticalTo($query)

                ->string($request->getBody())
                    ->isIdenticalTo($options['body'])

                ->array($request->getHeaders())
                    ->isIdenticalTo($headers)
        ;
    }

    /**
     * @dataProvider headerLineDataProvider
     */
    public function testParseHeaderLine($line, $expectedReturn, $expectedName, $expectedValues)
    {
        $this
            ->assert($line)
                ->given($this->newTestedInstance)
                ->and($curl = $this->testedInstance->getCurlResource())
                ->then
                    ->integer($this->testedInstance->parseHeaderLine($curl, $line))
                        ->isIdenticalTo($expectedReturn)
        ;

        if ($expectedName) {
            $this
                ->array($this->testedInstance->getResponseHeaders())
                    ->hasKey($expectedName)
                    ->contains($expectedValues)
            ;
        } else {
            $this
                ->array($this->testedInstance->getResponseHeaders())
                    ->isEmpty
            ;
        }
    }

    public function testRequest()
    {
        $this
            ->given($config = ild78\Config::init([]))

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

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_USERAGENT, $config->getDefaultUserAgent())
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
                        uniqid() => [uniqid(), uniqid()],
                    ],
                    'body' => [
                        uniqid() => uniqid(),
                        uniqid() => uniqid(),
                    ],
                ])
                ->and($headers = [])
                ->when(function () use (&$headers, $options) {
                    foreach ($options['headers'] as $key => $value) {
                        $headers[] = sprintf('%s: %s', $key, implode(', ', (array) $value));
                    }
                })
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

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_HTTPHEADER, $headers)
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

            ->assert('Error in response')
                ->given($this->newTestedInstance)
                ->and($curl = $this->testedInstance->getCurlResource())
                ->if($message = uniqid())
                ->and($body = json_encode(['error' => ['message' => $message]]))
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = $code = rand(400, 599))
                ->and($this->function->curl_errno = 0)
                ->and($this->function->curl_error = '')
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

            ->assert('Error as an array in response')
                ->given($this->newTestedInstance)
                ->and($curl = $this->testedInstance->getCurlResource())
                ->if($message = uniqid())
                ->and($body = json_encode(['error' => ['message' => [uniqid() => $message]]]))
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = $code = rand(400, 599))
                ->and($this->function->curl_errno = 0)
                ->and($this->function->curl_error = '')
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
    public function testRequest_exceptions($error, $code, $class, $message, $logLevel, $logMessage)
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

            ->assert($code . ' should throw ' . $class)
                ->given($this->newTestedInstance)
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body = uniqid())
                ->and($this->function->curl_getinfo = $code)
                ->and($this->function->curl_errno = $error)
                ->and($this->function->curl_error = uniqid())

                ->when(function () use ($code, &$logMessage, $config) {
                    if ($code === 401) {
                        $logMessage = sprintf($logMessage, $config->getSecretKey());
                    }
                })

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->then
                    ->exception(function () {
                        $this->testedInstance->request(uniqid(), uniqid());
                    })
                        ->isInstanceOf($class)
                        ->hasCode($code)
                        ->message
                            ->isIdenticalTo($message)

                    ->mock($logger)
                        ->call($logLevel)
                            ->withArguments($logMessage, [])
                                ->once
        ;
    }
}
