<?php

namespace ild78\tests\unit\Api;

use atoum;
use GuzzleHttp;
use ild78\Api;
use ild78\Api\Object as testedClass;
use ild78\Exceptions;
use mock;

class Object extends atoum
{
    public function test__construct()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($api = Api\Config::init(uniqid()))
            ->and($api->setHttpClient($client))

            ->assert('Without id')
                ->given($this->newTestedInstance())
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->mock($client)
                        ->wasNotCalled

            ->assert('With valid id')
                ->if($id = uniqid())
                ->and($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo($id)

                    ->mock($client)
                        ->wasNotCalled
        ;
    }

    public function test__call()
    {
        $this
            ->given($this->newTestedInstance)
            ->assert('getter')
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

            ->assert('setter')
                // Impossible to test, `Object`'s properties are not allowed for changes
                // See `Customer` test case for real setter test

            ->assert('Not allowed changes')
                ->exception(function () {
                    $this->testedInstance->setCreated(uniqid());
                })
                    ->isInstanceOf(Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the creation date.')

                ->exception(function () {
                    $this->testedInstance->setEndpoint(uniqid());
                })
                    ->isInstanceOf(Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the endpoint.')

                ->exception(function () {
                    $this->testedInstance->setId(uniqid());
                })
                    ->isInstanceOf(Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the id.')

                ->exception(function () {
                    $this->testedInstance->setDataModel(uniqid());
                })
                    ->isInstanceOf(Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the data model.')

            ->assert('Unknown method')
                ->if($method = uniqid())
                ->then
                    ->exception(function () use ($method) {
                        $this->testedInstance->$method();
                    })
                        ->isInstanceOf(Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('Method "' . $method . '" unknown')
        ;
    }

    public function testDataModelGetter()
    {
        // More test in stubs
        $this
            ->given($this->newTestedInstance)
            ->and($property = uniqid())
            ->then
                ->exception(function () use ($property) {
                    $this->testedInstance->dataModelGetter($property);
                })
                    ->isInstanceOf(Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Unknown property "' . $property . '"')
        ;
    }

    public function testDataModelSetter()
    {
        // More test in stubs
        $this
            ->given($this->newTestedInstance)
            ->and($property = uniqid())
            ->and($value = uniqid())
            ->then
                ->exception(function () use ($property, $value) {
                    $this->testedInstance->dataModelSetter($property, $value);
                })
                    ->isInstanceOf(Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Unknown property "' . $property . '"')
        ;
    }

    public function testGetUri()
    {
        $this
            ->given($config = Api\Config::init(uniqid()))
            ->and($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getUri())
                    ->isIdenticalTo($config->getUri() . $this->testedInstance->getEndpoint())
        ;
    }

    public function testHydrate()
    {
        $this
            ->given($data = [
                'id' => uniqid(),
                'created' => rand(946681200, 1893452400),
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

    public function testPopulate()
    {
        // More tests available in stubs

        $this
            ->given($config = Api\Config::init(uniqid()))

            ->assert('No request if no id')
                ->if($client = new mock\GuzzleHttp\Client)
                ->and($config->setHttpClient($client))

                ->then
                    ->object($this->newTestedInstance()->populate())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')->never

            ->assert('Work with an id')
                ->given($id = uniqid())
                ->and($timestamp = time())
                ->and($mock = new GuzzleHttp\Handler\MockHandler([
                    new GuzzleHttp\Psr7\Response(200, [], '{"id":"' . $id . '","created":' . $timestamp . '}'),
                ]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->populate())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo($id)

                    ->dateTime($date = $this->testedInstance->getCreationDate())
                        ->variable($date->format('U'))
                            ->isEqualTo($timestamp)

            ->assert('Only one request with two consecutive call')
                ->if($client = new mock\GuzzleHttp\Client)
                ->and($id = uniqid())
                ->and($timestamp = time())
                ->and($body = '{"id":"' . $id . '","created":' . $timestamp . '}')
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))

                ->and($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->populate())
                    ->object($this->testedInstance->populate())

                    ->mock($client)
                        ->call('request')
                            ->once
        ;
    }

    // There are no test for `Object::save()` method here
    // Nothing can be saved in `Object`, real test are availaible in `Customer` test case (`Customer::testSave()`)

    public function testToArray()
    {
        $this
            ->given($data = [
                'id' => uniqid(),
                'created' => rand(946681200, 1893452400),
            ])
            ->and($this->newTestedInstance)
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->array($this->testedInstance->toArray())
                    ->string['id']->isIdenticalTo($data['id'])
                    ->integer['created']->isIdenticalTo($data['created'])
                    ->notHasKey('endpoint')
        ;
    }

    public function testToJson()
    {
        $this
            ->given($data = [
                'id' => uniqid(),
                'created' => rand(946681200, 1893452400),
            ])
            ->and($this->newTestedInstance)
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->json($this->testedInstance->toJson())
                    ->isIdenticalTo(json_encode($this->testedInstance))
        ;
    }

    public function testToString()
    {
        $this
            ->given($data = [
                'id' => uniqid(),
                'created' => rand(946681200, 1893452400),
            ])
            ->and($this->newTestedInstance)
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->string($this->testedInstance->toString())
                    ->isIdenticalTo((string) $this->testedInstance)
                    ->isIdenticalTo(json_encode($this->testedInstance))
        ;
    }
}
