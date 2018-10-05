<?php

namespace ild78\tests\unit\Api;

use atoum;
use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ild78\Api;
use ild78\Api\Object as testedClass;
use ild78\Exceptions;

class Object extends atoum
{
    public function test__construct()
    {
        $this
            ->given($id = uniqid())
            ->and($timestamp = rand())
            ->and($mock = new MockHandler([
                new Response(200, [], '{"id":"' . $id . '","created":' . $timestamp . '}'),
                new Response(401, [], file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json')),
            ]))
            ->and($handler = HandlerStack::create($mock))
            ->and($client = new Client(['handler' => $handler]))
            ->and($api = Api\Config::init(uniqid()))
            ->and($api->setHttpClient($client))

            ->assert('Without id')
                ->given($this->newTestedInstance())
                    ->variable($this->testedInstance->getId())
                        ->isNull

            ->assert('With valid id')
                ->given($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo($id)

                    ->object($date = $this->testedInstance->getCreationDate())
                        ->isInstanceOf('DateTime')
                    ->variable($date->format('U'))
                        ->isEqualTo($timestamp)

            ->assert('With bad credential')
                ->given($key = uniqid())
                ->then
                    ->exception(function () use ($key) {
                        $this->newTestedInstance($key);
                    })
                        ->isInstanceOf(Exceptions\NotAuthorizedException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('You are not authorized to access that resource')
        ;

        $errors = [
            310 => [
                'expected' => Exceptions\TooManyRedirectsException::class,
                'thrown' => GuzzleHttp\Exception\TooManyRedirectsException::class,
                'message' => 'Too many redirect',
            ],
            400 => [
                'expected' => Exceptions\BadRequestException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Bad request',
            ],
            402 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Payment required',
            ],
            403 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Forbidden',
            ],
            404 => [
                'expected' => Exceptions\NotFoundException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not found',
            ],
            405 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Method Not Allowed',
            ],
            406 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not Acceptable',
            ],
            407 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Proxy Authentication Required',
            ],
            408 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Request Time-out',
            ],
            409 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Conflict',
            ],
            410 => [ // not handled
                'expected' => Exceptions\ClientException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Gone',
            ],
            500 => [
                'expected' => Exceptions\ServerException::class,
                'thrown' => GuzzleHttp\Exception\ServerException::class,
                'message' => 'Internal Server Error',
            ],
        ];

        foreach ($errors as $code => $infos) {
            $this
                ->assert(sprintf('%d - %s', $code, $infos['message']))
                    ->given($key = uniqid())
                    ->and($request = new Request('GET', $api->getUri()))
                    ->and($response = new Response($code))
                    ->and($exception = $infos['thrown'])
                    ->and($mock = new MockHandler([
                        new $exception($infos['message'], $request, $response),
                    ]))
                    ->and($handler = HandlerStack::create($mock))
                    ->and($client = new Client(['handler' => $handler]))
                    ->and($api->setHttpClient($client))
                    ->then
                        ->exception(function () use ($key) {
                            $this->newTestedInstance($key);
                        })
                            ->isInstanceOf($infos['expected'])
                            ->hasNestedException
            ;
        }
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isEmpty
        ;
    }

    public function testGetId()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull // No default value
        ;
    }
}
