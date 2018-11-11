<?php

namespace ild78\tests\unit\Api;

use atoum;
use Exception;
use GuzzleHttp;
use ild78;
use ild78\Api\Request as testedClass;
use mock;

class Request extends atoum
{
    public function httpVerbDataProvider()
    {
        return [
            'GET',
            'POST',
            'PUT',
        ];
    }

    /**
     * @dataProvider httpVerbDataProvider
     */
    public function testGet_Post_Put($verb)
    {
        // testing a mock is not a good test but here we only want to test we call an other method

        $this
            ->given($request = new mock\ild78\Api\Request)
            ->and($result = uniqid())
            ->and($this->calling($request)->request = $result)
            ->if($object = new mock\ild78\Api\Object)
            ->and($location = uniqid())
            ->then
                ->assert('No location')
                    ->string($request->$verb($object))
                        ->isIdenticalTo($result)
                    ->mock($request)
                        ->call('request')
                            ->withIdenticalArguments($verb, $object)
                                ->once

                ->assert('with location')
                    ->string($request->$verb($object, $location))
                        ->isIdenticalTo($result)
                    ->mock($request)
                        ->call('request')
                            ->withIdenticalArguments($verb, $object, $location)
                                ->once
        ;
    }

    public function testRequest_workingWithDefaultClient()
    {
        $this
            ->if($config = ild78\Api\Config::init(uniqid()))

            ->assert('No location defined')
                ->given($client = new mock\ild78\Http\Client)
                ->and($response = new mock\ild78\Http\Response(200))
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($method = 'GET')
                ->and($object = new mock\ild78\Api\Object)

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = 'API call : ' . $method . ' ' . $object->getUri())
                ->then
                    ->string($this->testedInstance->request($method, $object))
                        ->isIdenticalTo($body)
                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments($method, $object->getUri())
                                ->once
                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->never

            ->assert('Location defined')
                ->given($client = new mock\ild78\Http\Client)
                ->and($response = new mock\ild78\Http\Response(200))
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($method = 'POST')
                ->and($object = new mock\ild78\Api\Object)
                ->and($location = uniqid())

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = vsprintf('API call : %s %s', [
                    $method,
                    $object->getUri() . '/' . $location,
                ]))
                ->then
                    ->string($this->testedInstance->request($method, $object, $location))
                        ->isIdenticalTo($body)
                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments($method, $object->getUri() . '/' . $location)
                                ->once
                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->never
        ;
    }

    public function testRequest_errorsWithDefaultClient()
    {
        $this
            ->given($this->function->setDefaultNamespace('ild78\\Http'))
            ->if($config = ild78\Api\Config::init(uniqid()))

            ->assert('With bad credential')
                ->given($content = file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json'))
                ->and($this->function->curl_exec = $content)
                ->and($this->function->curl_getinfo = 401)
                ->and($this->function->curl_errno = rand(100, 200))

                ->if($client = new ild78\Http\Client)
                ->and($config->setHttpClient($client))

                ->if($object = new mock\ild78\Api\Object)
                ->and($method = 'PUT')

                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () use ($object, $method) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
                        ->message
                            ->isIdenticalTo('You are not authorized to access that resource.')

                        ->variable($this->exception->getPrevious())
                            ->isNull

            ->assert('Unsupported method')
                ->if($this->newTestedInstance)
                ->and($this->function->curl_exec = uniqid())
                ->and($object = new mock\ild78\Api\Object)
                ->and($method = uniqid())

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->and($errorMessage = vsprintf('Unknown HTTP verb "%s"', [
                    $method,
                ]))
                ->then
                    ->exception(function () use ($method, $object) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($method)

                    ->mock($logger)
                        ->call('debug')->never
                        ->call('error')->withArguments($errorMessage)->once
                        ->call('notice')->never

                    ->function('curl_exec')
                        ->wasCalled->never
        ;
    }

    public function testRequest_withGuzzle()
    {
        $this
            ->if($config = ild78\Api\Config::init(uniqid()))

            ->assert('Use test of client and no more location')
                ->given($client = new mock\GuzzleHttp\Client)
                ->and($response = new mock\GuzzleHttp\Psr7\Response)
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($method = 'GET')
                ->and($object = new mock\ild78\Api\Object)

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = 'API call : ' . $method . ' ' . $object->getUri())
                ->then
                    ->string($this->testedInstance->request($method, $object))
                        ->isIdenticalTo($body)
                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments($method, $object->getUri())
                                ->once
                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->never

            ->assert('Use test of client and location')
                ->given($client = new mock\GuzzleHttp\Client)
                ->and($response = new mock\GuzzleHttp\Psr7\Response)
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($method = 'POST')
                ->and($object = new mock\ild78\Api\Object)
                ->and($location = uniqid())

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = vsprintf('API call : %s %s', [
                    $method,
                    $object->getUri() . '/' . $location,
                ]))
                ->then
                    ->string($this->testedInstance->request($method, $object, $location))
                        ->isIdenticalTo($body)
                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments($method, $object->getUri() . '/' . $location)
                                ->once
                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->never

            ->assert('With bad credential')
                ->given($content = file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json'))
                ->and($response = new GuzzleHttp\Psr7\Response(401, [], $content))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($config->setHttpClient($client))

                ->if($object = new mock\ild78\Api\Object)
                ->and($method = 'PUT')

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = vsprintf('API call : %s %s', [
                    $method,
                    $config->getUri() . $object->getEndpoint(),
                ]))
                ->and($noticeMessage = vsprintf('HTTP 401 - Invalid credential : %s', [
                    $config->getKey(),
                ]))
                ->then
                    ->exception(function () use ($object, $method) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('You are not authorized to access that resource')

                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->withArguments($noticeMessage, [])->once

            ->assert('Every Guzzle exceptions (except the ones below)')
                ->given($content = file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json'))
                ->and($exceptionMessage = uniqid())
                ->and($response = new Exception($exceptionMessage))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($config->setHttpClient($client))

                ->if($object = new mock\ild78\Api\Object)
                ->and($method = 'GET')

                ->if($logger = new mock\ild78\Api\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = vsprintf('API call : %s %s', [
                    $method,
                    $config->getUri() . $object->getEndpoint(),
                ]))
                ->and($errorMessage = sprintf('Unknown error : %s', $exceptionMessage))
                ->then
                    ->exception(function () use ($object, $method) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\Exception::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('Unknown error, may be a network error')

                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->withArguments($errorMessage)->once
                        ->call('notice')->never
        ;

        $errors = [
            310 => [
                'expected' => ild78\Exceptions\TooManyRedirectsException::class,
                'thrown' => GuzzleHttp\Exception\TooManyRedirectsException::class,
                'message' => 'Too Many Redirection',
                'logLevel' => 'critical',
            ],
            400 => [
                'expected' => ild78\Exceptions\BadRequestException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Bad Request',
                'logLevel' => 'critical',
            ],
            402 => [
                'expected' => ild78\Exceptions\PaymentRequiredException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Payment Required',
                'logLevel' => 'error',
            ],
            403 => [
                'expected' => ild78\Exceptions\ForbiddenException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Forbidden',
                'logLevel' => 'error',
            ],
            404 => [
                'expected' => ild78\Exceptions\NotFoundException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not found',
                'logLevel' => 'error',
            ],
            405 => [
                'expected' => ild78\Exceptions\MethodNotAllowedException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Method Not Allowed',
                'logLevel' => 'critical',
            ],
            406 => [
                'expected' => ild78\Exceptions\NotAcceptableException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not Acceptable',
                'logLevel' => 'error',
            ],
            407 => [
                'expected' => ild78\Exceptions\ProxyAuthenticationRequiredException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Proxy Authentication Required',
                'logLevel' => 'error',
            ],
            408 => [
                'expected' => ild78\Exceptions\RequestTimeoutException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Request Time-out',
                'logLevel' => 'error',
            ],
            409 => [
                'expected' => ild78\Exceptions\ConflictException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Conflict',
                'logLevel' => 'error',
            ],
            410 => [
                'expected' => ild78\Exceptions\GoneException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Gone',
                'logLevel' => 'error',
            ],
            500 => [
                'expected' => ild78\Exceptions\InternalServerErrorException::class,
                'thrown' => GuzzleHttp\Exception\ServerException::class,
                'message' => 'Internal Server Error',
                'logLevel' => 'critical',
            ],
        ];

        foreach ($errors as $code => $infos) {
            $this
                ->assert(sprintf('%d - %s', $code, $infos['message']))
                    ->given($request = new GuzzleHttp\Psr7\Request('GET', $config->getUri()))
                    ->and($response = new GuzzleHttp\Psr7\Response($code))
                    ->and($exception = $infos['thrown'])
                    ->and($mock = new GuzzleHttp\Handler\MockHandler([
                        new $exception($infos['message'], $request, $response),
                    ]))
                    ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                    ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                    ->and($config->setHttpClient($client))

                    ->if($object = new mock\ild78\Api\Object)
                    ->and($method = 'GET')
                    ->and($location = uniqid())

                    ->if($logger = new mock\ild78\Api\Logger)
                    ->and($config->setLogger($logger))
                    ->and($debugMessage = vsprintf('API call : %s %s', [
                        $method,
                        $config->getUri() . $object->getEndpoint() . '/' . $location,
                    ]))
                    ->and($logMessage = sprintf('HTTP %d - %s', $code, $infos['message']))
                    ->when(function () use ($object, $code, $location, &$logMessage, &$infos) {
                        if ($code === 404) {
                            $tmp = get_class($object);
                            $parts = explode('\\', $tmp);
                            $class = end($parts);

                            $infos['message'] = vsprintf('Ressource "%s" unknown for %s', [
                                $location,
                                $class,
                            ]);

                            $logMessage .= ' : ' . $infos['message'];
                        }

                        if ($code === 500) {
                            $infos['message'] = 'Servor error, please leave a minute to repair it and try again';
                        }
                    })
                    ->then
                        ->exception(function () use ($object, $method, $location) {
                            $this->testedInstance->request($method, $object, $location);
                        })
                            ->isInstanceOf($infos['expected'])
                            ->hasNestedException
                            ->message
                                ->contains($infos['message'])

                        ->object($this->exception->getRequest())
                            ->isInstanceOf($request)

                        ->object($this->exception->getResponse())
                            ->isInstanceOf($response)

                        ->mock($logger)
                            ->call('debug')->withArguments($debugMessage, [])->once
                            ->call($infos['logLevel'])->withArguments($logMessage)->once
            ;
        }
    }
}
