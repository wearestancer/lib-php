<?php

namespace ild78\tests\unit\Api;

use atoum;
use GuzzleHttp;
use ild78\Api;
use ild78\Api\Object as testedClass;
use ild78\Exceptions;

class Object extends atoum
{
    public function test__construct()
    {
        $this
            ->assert('Without id')
                ->given($this->newTestedInstance())
                    ->variable($this->testedInstance->getId())
                        ->isNull

            ->assert('With valid id')
                ->given($id = uniqid())
                ->and($timestamp = rand())
                ->and($mock = new GuzzleHttp\Handler\MockHandler([
                    new GuzzleHttp\Psr7\Response(200, [], '{"id":"' . $id . '","created":' . $timestamp . '}'),
                ]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($api = Api\Config::init(uniqid()))
                ->and($api->setHttpClient($client))

                ->if($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo($id)

                    ->dateTime($date = $this->testedInstance->getCreationDate())
                        ->variable($date->format('U'))
                            ->isEqualTo($timestamp)
        ;
    }

    public function test__call()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($data = [
                'id' => uniqid(),
                'created' => rand(946681200, 1893452400),
            ])
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isEmpty
                ->variable($this->testedInstance->getId())
                    ->isNull // No default value

            ->if($this->testedInstance->hydrate($data))
            ->then
                ->variable($this->testedInstance->getId())
                    ->isIdenticalTo($data['id'])
                ->dateTime($date = $this->testedInstance->getCreationDate())
                ->variable($date->format('U'))
                    ->isEqualTo($data['created'])

            ->assert('Unknown method')
                ->if($method = uniqid())
                ->then
                    ->exception(function () use ($method) {
                        $this->testedInstance->$method();
                    })
                        ->isInstanceOf(Exceptions\BadMethodCallException::class)
                        ->message
                            ->contains($method)
        ;
    }

    public function testHydrate()
    {
        $this
            ->given($data = [
                'id' => uniqid(),
                'created' => rand(0, PHP_INT_MAX),
            ])
            ->and($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->hydrate($data))
                    ->isTestedInstance
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo($data['id'])
                ->dateTime($date = $this->testedInstance->getCreationDate())
                    ->variable($date->format('U'))
                        ->isEqualTo($data['created'])
        ;
    }

    public function testJsonSerialize()
    {
        $this
            ->given($data = [
                'id' => uniqid(),
                'created' => rand(946681200, 1893452400),
            ])
            ->and($this->newTestedInstance)
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->array($this->testedInstance->jsonSerialize())
                    ->string['id']->isIdenticalTo($data['id'])
                    ->integer['created']->isIdenticalTo($data['created'])
                    ->notHasKey('endpoint')
                ->json(json_encode($this->testedInstance))
        ;
    }
}
