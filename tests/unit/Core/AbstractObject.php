<?php

namespace ild78\tests\unit\Core;

use DateTime;
use DateTimeZone;
use GuzzleHttp;
use ild78;
use ild78\Core\AbstractObject as testedClass;
use mock;

class AbstractObject extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Dates;
    use ild78\Tests\Provider\Strings;

    public function test__construct()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($api = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
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
            ->and(ild78\Config::init([]))

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
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the creation date.')

                ->exception(function () {
                    $this->testedInstance->setEndpoint(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the endpoint.')

                ->exception(function () {
                    $this->testedInstance->setId(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the id.')

                ->exception(function () {
                    $this->testedInstance->setDataModel(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify the data model.')

            ->assert('Unknown method')
                ->if($method = uniqid())
                ->then
                    ->exception(function () use ($method) {
                        $this->testedInstance->$method();
                    })
                        ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
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
            ->and(ild78\Config::init([]))
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
                    ->isIdenticalTo($this->testedInstance->get_id)

                ->variable($this->testedInstance->getCreationDate())
                    ->isIdenticalTo($this->testedInstance->getCreationDate)
                    ->isIdenticalTo($this->testedInstance->creationDate)
                    ->isIdenticalTo($this->testedInstance->GeTcReAtIoNdAtE)
                    ->isIdenticalTo($this->testedInstance->get_creation_date)
                    ->isIdenticalTo($this->testedInstance->creation_date)
        ;
    }

    /**
     * @dataProvider caseStringDataProvider
     */
    public function testCamelCaseToSnakeCase($camel, $snake)
    {
        $this
            ->string($this->newTestedInstance->camelCaseToSnakeCase($camel))
                ->isIdenticalTo($snake)
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
                    ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
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
                    ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
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
                    ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Unknown property "' . $property . '"')
        ;
    }

    public function testDelete()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))
            ->and($client = new mock\ild78\Http\Client)
            ->and($logger = new mock\ild78\Core\Logger)
            ->and($config->setHttpClient($client))
            ->and($config->setLogger($logger))

            ->if($response = new mock\ild78\Http\Response(204))
            ->and($this->calling($client)->request = $response)
            ->and($this->calling($response)->getBody = null)

            ->if($id = uniqid())

            ->if($this->newTestedInstance($id))

            ->if($options = [])
            ->and($options['headers'] = [
                'Authorization' => $config->getBasicAuthHeader(),
                'Content-Type' => 'application/json',
                'User-Agent' => $config->getDefaultUserAgent(),
            ])
            ->and($options['timeout'] = $config->getTimeout())
            ->and($location = $this->testedInstance->getUri())

            ->and($logMessage = sprintf('AbstractObject "%s" deleted', $id))
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

                ->mock($logger)
                    ->call('info')
                        ->withArguments($logMessage)
                            ->once
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testGetCreationDate($tz)
    {
        $this
            ->given(ild78\Config::init([]))

            ->if($timestamp = rand(946681200, 1893452400))
            ->and($data = [
                'created' => $timestamp,
            ])
            ->and($this->newTestedInstance)
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@' . $timestamp))

            ->if($config = ild78\Config::init([]))
            ->and($config->setDefaultTimeZone($tz))
            ->then
                ->dateTime($this->testedInstance->getCreationDate())
                    ->hasTimezone(new DateTimeZone($tz))
        ;
    }

    public function testGetEntityName()
    {
        $this
            ->given($class = uniqid())
            ->and($ns = implode('\\', array_fill(0, rand(0, 3), uniqid())))

            ->if($this->function->get_class = $ns . '\\' . $class)
            ->and($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEntityName())
                    ->isIdenticalTo($class)
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
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
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
            ->and(ild78\Config::init([]))
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
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

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

    // There are no test for `Object::send()` method here
    // Nothing can be sent in `Object`, real test are availaible in `Customer` test case (`Customer::testSend()`)

    /**
     * @dataProvider caseStringDataProvider
     */
    public function testSnakeCaseToCamelCase($camel, $snake)
    {
        $this
            ->string($this->newTestedInstance->snakeCaseToCamelCase($snake))
                ->isIdenticalTo($camel)
        ;
    }

    // testToArray, testToString and testToJson moved to stubs (more detailled)
}
