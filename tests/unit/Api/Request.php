<?php

namespace ild78\tests\unit\Api;

use atoum;
use GuzzleHttp;
use ild78;
use ild78\Api\Request as testedClass;
use mock;

class Request extends atoum
{
    public function testGet_Post_Put()
    {
        // testing a mock is not a good test but here we only want to test we call an other method

        $methods = [
            'GET',
            'POST',
            'PUT',
        ];

        foreach ($methods as $method) {
            $this
                ->given($request = new mock\ild78\Api\Request)
                ->and($result = uniqid())
                ->and($this->calling($request)->request = $result)
                ->if($object = new mock\ild78\Api\Object)
                ->and($location = uniqid())
                ->then
                    ->assert('No location')
                        ->string($request->$method($object))
                            ->isIdenticalTo($result)
                        ->mock($request)
                            ->call('request')
                                ->withIdenticalArguments($method, $object)
                                    ->once

                    ->assert('with location')
                        ->string($request->$method($object, $location))
                            ->isIdenticalTo($result)
                        ->mock($request)
                            ->call('request')
                                ->withIdenticalArguments($method, $object, $location)
                                    ->once
            ;
        }
    }

    public function testRequest()
    {
        $this
            ->if($config = ild78\Api\Config::init(uniqid()))

            ->assert('Use test of GuzzleHttp\Client and no more location')
                ->given($client = new mock\GuzzleHttp\Client)
                ->and($response = new mock\GuzzleHttp\Psr7\Response)
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($method = 'GET')
                ->and($object = new mock\ild78\Api\Object)
                ->then
                    ->string($this->testedInstance->request($method, $object))
                        ->isIdenticalTo($body)
                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments($method, $object->getEndpoint())
                                ->once

            ->assert('Use test of GuzzleHttp\Client and location')
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
                ->then
                    ->string($this->testedInstance->request($method, $object, $location))
                        ->isIdenticalTo($body)
                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments($method, $object->getEndpoint() . '/' . $location)
                                ->once

            ->assert('Unsupported method')
                ->if($this->newTestedInstance)
                ->and($object = new mock\ild78\Api\Object)
                ->and($method = uniqid())
                ->then
                    ->exception(function () use ($method, $object) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($method)

            ->assert('With bad credential')
                ->given($content = file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json'))
                ->and($response = new GuzzleHttp\Psr7\Response(401, [], $content))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($config->setHttpClient($client))

                ->if($object = new mock\ild78\Api\Object)
                ->then
                    ->exception(function () use ($object) {
                        $this->testedInstance->request('PUT', $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('You are not authorized to access that resource')
        ;

        $errors = [
            310 => [
                'expected' => ild78\Exceptions\TooManyRedirectsException::class,
                'thrown' => GuzzleHttp\Exception\TooManyRedirectsException::class,
                'message' => 'Too many redirect',
            ],
            400 => [
                'expected' => ild78\Exceptions\BadRequestException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Bad request',
            ],
            402 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Payment required',
            ],
            403 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Forbidden',
            ],
            404 => [
                'expected' => ild78\Exceptions\NotFoundException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not found',
            ],
            405 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Method Not Allowed',
            ],
            406 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not Acceptable',
            ],
            407 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Proxy Authentication Required',
            ],
            408 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Request Time-out',
            ],
            409 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Conflict',
            ],
            410 => [ // not handled
                'expected' => ild78\Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Gone',
            ],
            500 => [
                'expected' => ild78\Exceptions\ServerException::class,
                'thrown' => GuzzleHttp\Exception\ServerException::class,
                'message' => 'Internal Server Error',
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
                    ->then
                        ->exception(function () use ($object) {
                            $this->testedInstance->request('GET', $object);
                        })
                            ->isInstanceOf($infos['expected'])
                            ->hasNestedException
            ;
        }
    }
}
