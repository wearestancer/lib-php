<?php

namespace ild78\tests\unit\Api;

use atoum;
use DateTime;
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

            ->assert('adder')
                // Impossible to test, `Object`'s properties are not allowed for changes
                // See `Stub\Object` test case for real adder test

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
                            ->isIdenticalTo(vsprintf('Method "%s::%s()" unknown', [
                                get_class($this->testedInstance),
                                $method,
                            ]))
        ;
    }

    public function test__get()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($data = [
                'id' => uniqid(),
                'created' => rand(946681200, 1893452400),
            ])
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->variable($this->testedInstance->getId())
                    ->isIdenticalTo($this->testedInstance->getId)
                    ->isIdenticalTo($this->testedInstance->id)
                    ->isIdenticalTo($this->testedInstance->GETID)

                ->variable($this->testedInstance->getCreationDate())
                    ->isIdenticalTo($this->testedInstance->getCreationDate)
                    ->isIdenticalTo($this->testedInstance->creationDate)
                    ->isIdenticalTo($this->testedInstance->GeTcReAtIoNdAtE)
        ;
    }

    public function testDataModelAdder()
    {
        // More test in stubs
        $this
            ->given($this->newTestedInstance)
            ->and($property = uniqid())
            ->and($value = uniqid())
            ->then
                ->exception(function () use ($property, $value) {
                    $this->testedInstance->dataModelAdder($property, $value);
                })
                    ->isInstanceOf(Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Unknown property "' . $property . '"')
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

    public function testDelete()
    {
        $this
            ->given($config = Api\Config::init(uniqid()))
            ->and($client = new mock\ild78\Http\Client)
            ->and($config->setHttpClient($client))

            ->if($response = new mock\ild78\Http\Response(204))
            ->and($this->calling($client)->request = $response)
            ->and($this->calling($response)->getBody = null)

            ->if($id = uniqid())

            ->if($this->newTestedInstance($id))

            ->if($options = [])
            ->and($options['headers'] = [
                'Authorization' => $config->getBasicAuthHeader(),
                'Content-Type' => 'application/json',
            ])
            ->and($options['timeout'] = $config->getTimeout())
            ->and($options['body'] = json_encode($id))
            ->and($location = $this->testedInstance->getUri())
            ->then
                ->object($this->testedInstance->delete())
                    ->isTestedInstance

                ->variable($this->testedInstance->getId())
                    ->isNotEqualTo($id)
                    ->isNull

                ->mock($client)
                    ->call('request')
                        ->withArguments('DELETE', $location, $options)
                            ->once
        ;
    }

    public function testGetCreationDate()
    {
        $this
            ->given($timestamp = rand(946681200, 1893452400))
            ->and($data = [
                'created' => $timestamp,
            ])
            ->and($this->newTestedInstance)
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@' . $timestamp))
        ;
    }

    public function testGetId()
    {
        $this
            ->if($id = uniqid())
            ->and($this->newTestedInstance($id))
            ->then
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo($id)
        ;
    }

    public function testGetUri()
    {
        $this
            ->given($config = Api\Config::init(uniqid()))
            ->assert('No id')
                ->if($this->newTestedInstance)
                ->then
                    ->string($this->testedInstance->getUri())
                        ->isIdenticalTo($config->getUri() . $this->testedInstance->getEndpoint())

            ->assert('With an id')
                ->if($id = uniqid())
                ->and($this->newTestedInstance($id))
                ->and($tmp = [
                    $config->getUri(),
                    $this->testedInstance->getEndpoint(),
                    $this->testedInstance->getId(),
                ])
                ->and($uri = implode('/', array_map(function ($v) {
                    return trim($v, '/');
                }, $tmp)))
                ->then
                    ->string($this->testedInstance->getUri())
                        ->isIdenticalTo($uri)
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

    // testJsonSerialize moved in stub tests (more detailled)

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

            ->assert('No request if no endpoint')
                ->if($client = new mock\GuzzleHttp\Client)
                ->and($config->setHttpClient($client))

                ->then
                    ->object($this->newTestedInstance()->populate())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')->never
        ;
    }

    // There are no test for `Object::save()` method here
    // Nothing can be saved in `Object`, real test are availaible in `Customer` test case (`Customer::testSave()`)

    // testToArray, testToString and testToJson moved to stubs (more detailled)
}
