<?php

namespace ild78\tests\unit\Stub\Api;

use atoum;
use GuzzleHttp;
use ild78;
use mock;

class Object extends atoum
{
    public function invalidDataProvider()
    {
        $datas = [];

        // We will make 3 data of each
        for ($idx = 0; $idx < 3; $idx++) {
            // string 1, between 10 and 20
            $datas[] = [
                'string1',
                $this->makeStringAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string1 must be between 10 and 20 characters.',
            ];

            // string 1, between 10 and 20
            $datas[] = [
                'string1',
                $this->makeStringLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string1 must be between 10 and 20 characters.',
            ];

            // string 2, at least 10
            $datas[] = [
                'string2',
                $this->makeStringLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string2 must be at least 10 characters.',
            ];

            // string 3, max 20
            $datas[] = [
                'string3',
                $this->makeStringAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string3 must have less than 20 characters.',
            ];

            // string 4, exactly 5
            $datas[] = [
                'string4',
                $this->makeStringAtLeast(6),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string4 must have 5 characters.',
            ];

            // string 4, exactly 5
            $datas[] = [
                'string4',
                $this->makeStringLessThan(4),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string4 must have 5 characters.',
            ];

            // integer 1, between 10 and 20
            $datas[] = [
                'integer1',
                $this->makeIntegerLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer1 must be greater than or equal to 10 and be less than or equal to 20.',
            ];

            // integer 1, between 10 and 20
            $datas[] = [
                'integer1',
                $this->makeIntegerAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer1 must be greater than or equal to 10 and be less than or equal to 20.',
            ];

            // integer 2, at least 10
            $datas[] = [
                'integer2',
                $this->makeIntegerLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer2 must be greater than or equal to 10.',
            ];

            // integer 3, max 20
            $datas[] = [
                'integer3',
                $this->makeIntegerAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer3 must be less than or equal to 20.',
            ];
        }

        // string 1 with integer
        $datas[] = [
            'string1',
            $this->makeIntegerBetween(1, 10),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "integer" expected "string".',
        ];

        // integer 1 with string
        $datas[] = [
            'integer1',
            $this->makeStringBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "string" expected "integer".',
        ];

        // object 1 with string
        $datas[] = [
            'object1',
            $this->makeStringBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "string" expected "ild78\Card".',
        ];

        // restricted
        $datas[] = [
            'restricted1',
            $this->makeStringBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'You are not allowed to modify "restricted1".',
        ];

        // array1, array of string
        $datas[] = [
            'array1',
            $this->makeStringBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "string" expected "array".',
        ];

        // array1, array of string
        $datas[] = [
            'array1',
            $this->makeIntegerBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "integer" expected "array".',
        ];

        // array1, array of string
        $datas[] = [
            'array1',
            [$this->makeIntegerBetween(10, 20)],
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "integer" expected "string".',
        ];

        // array2, array of intger
        $datas[] = [
            'array2',
            [$this->makeStringBetween(10, 20)],
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "string" expected "integer".',
        ];

        // array3, array of object
        $datas[] = [
            'array3',
            [$this->makeStringBetween(10, 20)],
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "string" expected "ild78\Card".',
        ];

        // array3, array of object
        $datas[] = [
            'array3',
            [$this->makeIntegerBetween(10, 20)],
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "integer" expected "ild78\Card".',
        ];

        return $datas;
    }

    public function makeIntegerAtLeast($min)
    {
        return $this->makeIntegerBetween($min, 1000);
    }

    public function makeIntegerBetween($min, $max)
    {
        return rand($min, $max);
    }

    public function makeIntegerLessThan($max)
    {
        return $this->makeIntegerBetween(0, $max);
    }

    public function makeStringAtLeast($min)
    {
        return $this->makeStringWithFixedSize($this->makeIntegerAtLeast($min));
    }

    public function makeStringBetween($min, $max)
    {
        return $this->makeStringWithFixedSize($this->makeIntegerBetween($min, $max));
    }

    public function makeStringLessThan($max)
    {
        return $this->makeStringWithFixedSize($this->makeIntegerLessThan($max));
    }

    public function makeStringWithFixedSize($length)
    {
        return substr(md5(uniqid()), 0, $length);
    }

    public function test__call()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert('get / set / add with array1 (array of string)')
                    ->array($this->testedInstance->getArray1())
                        ->isEmpty

                    ->object($this->testedInstance->setArray1([$one = uniqid()]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getArray1())
                        ->string[0]
                            ->isIdenticalTo($one)
                        ->size->isEqualTo(1)

                    ->object($this->testedInstance->addArray1($two = uniqid()))
                        ->isTestedInstance

                    ->array($this->testedInstance->getArray1())
                        ->string[0]
                            ->isIdenticalTo($one)
                        ->string[1]
                            ->isIdenticalTo($two)
                        ->size->isEqualTo(2)
        ;
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testDataModelAdderThrowsNotAList($property, $value)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function () use ($property, $value) {
                    $this->testedInstance->dataModelAdder($property, $value);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('"' . $property . '" is not a list, you can not add elements in it.')
        ;
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testCreate($property, $value)
    {
        $this
            ->if($class = (string) $this->testedClass)
            ->and($this->newTestedInstance->dataModelSetter($property, $value))
            ->then
                ->object($obj = $class::create([$property => $value]))
                    ->isInstanceOf($class)

                ->variable($obj->dataModelGetter($property))
                    ->isIdenticalTo($value)
        ;
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testDataModelGetterAndSetter($property, $value)
    {
        $assertMessage = vsprintf('Test with %s with value "%s" (%d chars)', [
            $property,
            $value,
            strlen((string) $value),
        ]);

        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert($assertMessage)
                    ->variable($this->testedInstance->dataModelGetter($property))
                        ->isNull

                    ->object($this->testedInstance->dataModelSetter($property, $value))
                        ->isTestedInstance

                    ->variable($this->testedInstance->dataModelGetter($property))
                        ->isIdenticalTo($value)
        ;
    }

    /**
     * @dataProvider validArrayDataProvider
     */
    public function testDataModelGetterSetterAdderOnArray($property, $value, $extra)
    {
        $assertMessage = vsprintf('Test with %s with %s : ', [
            $property,
            json_encode($value),
        ]);

        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert($assertMessage . 'If nothing, we got an empty array')
                    ->array($this->testedInstance->dataModelGetter($property))
                        ->isEmpty

                ->assert($assertMessage . '"set" will insert value like other')
                    ->object($this->testedInstance->dataModelSetter($property, $value))
                        ->isTestedInstance

                    ->array($this->testedInstance->dataModelGetter($property))
                        ->isIdenticalTo($value)
                        ->size
                            ->isEqualTo(count($value))

                ->assert($assertMessage . '"add" will add value without touching previous ones')
                    ->object($this->testedInstance->dataModelAdder($property, $extra))
                        ->isTestedInstance

                    ->array($this->testedInstance->dataModelGetter($property))
                        ->containsValues($value)
                        ->size
                            ->isEqualTo(count($value) + 1)

                ->assert($assertMessage . '"set" will truncate previous')
                    ->array($this->testedInstance->dataModelGetter($property))
                        ->size
                            ->isGreaterThan(count($value))

                    ->object($this->testedInstance->dataModelSetter($property, $value))
                        ->isTestedInstance

                    ->array($this->testedInstance->dataModelGetter($property))
                        ->isIdenticalTo($value)
                        ->size
                            ->isEqualTo(count($value))
        ;
    }

    public function testDataModelGetterWillCallPopulate()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($id = uniqid())
            ->and($timestamp = time())
            ->and($string1 = $this->makeStringBetween(10, 20))
            ->and($body = '{"id":"' . $id . '","created":' . $timestamp . ',"string1":"' . $string1 . '"}')
            ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
            ->and($this->calling($client)->request = $response)
            ->and(ild78\Api\Config::init(uniqid())->setHttpClient($client))

            ->assert('No call without id')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->dataModelGetter('string1'))
                        ->isNull

                    ->mock($client)
                        ->call('request')
                            ->never

            ->assert('Will automatically call populate')
                ->if($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->dataModelGetter('string1'))
                        ->isIdenticalTo($string1)

                    ->mock($client)
                        ->call('request')
                            ->once
        ;
    }

    public function testDataModelThrowsUnknownProperty()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($property = uniqid())
            ->then
                ->assert('On getter')
                    ->exception(function () use ($property) {
                        $this->testedInstance->dataModelGetter($property);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('Unknown property "' . $property . '"')

                ->assert('On setter')
                    ->exception(function () use ($property) {
                        $this->testedInstance->dataModelSetter($property, uniqid());
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('Unknown property "' . $property . '"')
        ;
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testDataModelSetterThrowsInvalidData($property, $value, $class, $message)
    {

        if (is_array($value)) {
            $assertMessage = vsprintf('$%s = %s => %s', [
                $property,
                json_encode($value),
                $message,
            ]);
        } else {
            $assertMessage = vsprintf('$%s = "%s" (%d chars) => %s', [
                $property,
                $value,
                strlen((string) $value),
                $message,
            ]);
        }

        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert($assertMessage)
                    ->exception(function () use ($property, $value) {
                        $this->testedInstance->dataModelSetter($property, $value);
                    })
                        ->isInstanceOf($class)
                        ->message
                            ->isIdenticalTo($message)
        ;
    }

    public function testGetCreationDate()
    {
        $this
            ->given($config = ild78\Api\Config::init(uniqid()))

            ->if($client = new mock\ild78\Http\Client)
            ->and($config->setHttpClient($client))

            ->if($response = new mock\ild78\Http\Response(200))
            ->and($this->calling($client)->request = $response)

            ->assert('Can be null')
                ->given($this->calling($response)->getBody = '{}')

                ->if($this->newTestedInstance(uniqid()))
                ->then
                    ->variable($this->testedInstance->getCreationDate())
                        ->isNull

            ->assert('No date but an ID, it will populate data')
                ->given($created = rand(946681200, 1893452400))
                ->and($this->calling($response)->getBody = json_encode(compact('created')))

                ->if($this->newTestedInstance(uniqid()))
                ->then
                    ->dateTime($this->testedInstance->getCreationDate())
                        ->isEqualTo(new \DateTime('@' . $created))
        ;
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetModel($property, $value, $min, $max, $fixed)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->getModel($property))
                    ->child['size'](function ($size) use ($min, $max, $fixed) {
                        $size
                            ->variable['min']
                                ->isIdenticalTo($min)
                            ->variable['max']
                                ->isIdenticalTo($max)
                            ->variable['fixed']
                                ->isIdenticalTo($fixed)
                        ;
                    })
                    ->notHasKey('value')
                    ->hasKeys(['restricted', 'required'])

                ->array($this->testedInstance->getModel())
                    ->hasKey($property)
                    ->child[$property](function ($child) use ($min, $max, $fixed) {
                        $child
                            ->child['size'](function ($size) use ($min, $max, $fixed) {
                                $size
                                    ->variable['min']
                                        ->isIdenticalTo($min)
                                    ->variable['max']
                                        ->isIdenticalTo($max)
                                    ->variable['fixed']
                                        ->isIdenticalTo($fixed)
                                ;
                            })
                            ->notHasKey('value')
                            ->hasKeys(['restricted', 'required'])
                        ;
                    })
        ;
    }

    public function testHydrate()
    {
        $this
            ->given($data = [
                'created' => rand(946681200, 1893452400),
            ])
            ->and($withEmpty = array_merge($data, [
                'object1' => null,
                'array1' => [],
                'array3' => [],
            ]))
            ->and($object = $this->newTestedInstance)
            ->and($object->setUpdated(false))
            ->and($updatedPropagation = [
                'object2' => $object,
            ])

            ->if($this->newTestedInstance)
            ->then
                ->assert('Normal hydratation')
                    ->object($this->testedInstance->hydrate($data))
                        ->isTestedInstance

                    ->dateTime($date = $this->testedInstance->getCreationDate())
                        ->variable($date->format('U'))
                            ->isEqualTo($data['created'])

                    ->variable($this->testedInstance->getObject1())
                        ->isNull

                    ->array($this->testedInstance->getArray1())
                        ->isEmpty

                    ->array($this->testedInstance->getArray3())
                        ->isEmpty

                ->assert('Hydratation with empty value')
                    ->object($this->testedInstance->hydrate($withEmpty))
                        ->isTestedInstance

                    ->variable($this->testedInstance->getObject1())
                        ->isNull

                    ->array($this->testedInstance->getArray1())
                        ->isEmpty

                    ->array($this->testedInstance->getArray3())
                        ->isEmpty

                ->assert('Hydratation will pass updated flag')
                    ->boolean($object->getUpdated())
                        ->isIdenticalTo($this->testedInstance->getUpdated())
                        ->isFalse

                    ->object($this->testedInstance->setUpdated(true)->hydrate($updatedPropagation))
                        ->isTestedInstance

                    ->object($this->testedInstance->getObject2())
                        ->isInstanceOfTestedClass
                        ->isIdenticalTo($object)

                    ->boolean($object->getUpdated())
                        ->isIdenticalTo($this->testedInstance->getUpdated())
                        ->isTrue
        ;
    }

    public function testPopulate()
    {
        $this
            ->assert('Work with an id')
                ->given($config = ild78\Api\Config::init(uniqid()))
                ->and($id = uniqid())
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
                ->given($config = ild78\Api\Config::init(uniqid()))

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

            ->assert('Save blocks populate')
                ->given($config = ild78\Api\Config::init(uniqid()))
                ->and($id = uniqid())
                ->and($created = time())
                ->and($string1 = $this->makeStringBetween(10, 20))
                ->and($integer1 = $this->makeIntegerBetween(10, 20))

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($body = json_encode(compact('id', 'created', 'string1', 'integer1')))
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))

                ->and($this->newTestedInstance($id))
                ->and($this->testedInstance->setString1($string1))
                ->and($this->testedInstance->setInteger1($integer1))
                ->then
                    ->object($this->testedInstance->save())
                    ->object($this->testedInstance->populate())

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST')
                                ->once

                            ->withArguments('GET')
                                ->never

            ->assert('Populate working normally')
                ->given($config = ild78\Api\Config::init(uniqid()))
                ->and($id = uniqid())
                ->and($created = time())

                ->and($string1 = $this->makeStringBetween(10, 20))
                ->and($string2 = $this->makeStringAtLeast(10))
                ->and($string3 = $this->makeStringLessThan(20))
                ->and($string4 = $this->makeStringWithFixedSize(5))

                ->and($integer1 = $this->makeIntegerBetween(10, 20))
                ->and($integer2 = $this->makeIntegerAtLeast(10))
                ->and($integer3 = $this->makeIntegerLessThan(10))

                ->and($restricted1 = $this->makeStringAtLeast(10))

                ->and($body = json_encode(compact('id', 'created', 'string1', 'string2', 'string3', 'string4', 'integer1', 'integer2', 'integer3', 'restricted1')))

                ->and($mock = new GuzzleHttp\Handler\MockHandler([
                    new GuzzleHttp\Psr7\Response(200, [], $body),
                ]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))

                ->if($mock = new mock\GuzzleHttp\Client)
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($mock)->request = $response)

                ->then
                    ->if($config->setHttpClient($client))
                    ->and($this->newTestedInstance($id))
                    ->then
                        ->object($this->testedInstance->populate())
                            ->isTestedInstance

                        ->string($this->testedInstance->getId())
                            ->isIdenticalTo($id)

                        ->dateTime($date = $this->testedInstance->getCreationDate())
                            ->variable($date->format('U'))
                                ->isEqualTo($created)

                        ->string($this->testedInstance->getString1())
                            ->isIdenticalTo($string1)
                        ->string($this->testedInstance->getString2())
                            ->isIdenticalTo($string2)
                        ->string($this->testedInstance->getString3())
                            ->isIdenticalTo($string3)
                        ->string($this->testedInstance->getString4())
                            ->isIdenticalTo($string4)

                        ->integer($this->testedInstance->getInteger1())
                            ->isIdenticalTo($integer1)
                        ->integer($this->testedInstance->getInteger2())
                            ->isIdenticalTo($integer2)
                        ->integer($this->testedInstance->getInteger3())
                            ->isIdenticalTo($integer3)

                        ->string($this->testedInstance->getRestricted1())
                            ->isIdenticalTo($restricted1)
        ;
    }

    public function testRetrieve()
    {
        $this
            ->if($id = uniqid())
            ->and($class = (string) $this->testedClass)
            ->then
                ->object($obj = $class::retrieve($id))
                    ->isInstanceOf($class)
                    ->isEqualTo($this->newTestedInstance($id))

                ->string($obj->getId())
                    ->isIdenticalTo($id)
        ;
    }

    public function testSave()
    {
        $this
            ->assert('Throw exception if requirement are not provided')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setString2($this->makeStringAtLeast(10)))
                ->then
                    ->exception(function () {
                        $this->testedInstance->save();
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('You need to provide a value for : integer1, string1')

            ->assert('Save data if all requirement are complete')
                ->if($string1 = $this->makeStringBetween(10, 20))
                ->and($integer1 = $this->makeIntegerBetween(10, 20))
                ->and($id = uniqid())
                ->and($created = time())

                ->given($config = ild78\Api\Config::init(uniqid()))
                ->and($body = json_encode(compact('id', 'created', 'string1', 'integer1')))
                ->and($client = new mock\GuzzleHttp\Client)
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setString1($string1))
                ->and($this->testedInstance->setInteger1($integer1))

                ->if($options = [])
                ->and($options['headers'] = [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                ])
                ->and($options['timeout'] = $config->getTimeout())
                ->and($options['body'] = json_encode($this->testedInstance))
                ->then
                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $this->testedInstance->getUri(), $options)
                                ->once
        ;
    }

    public function testToArray()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->setCamelCaseProperty($camelCase = uniqid()))
            ->and($this->testedInstance->forceRestricted1($restricted = uniqid()))
            ->then
                ->array($this->testedInstance->toArray())
                    ->notHasKey('restricted1')

                    ->notHasKey('camelCaseProperty')
                    ->hasKey('camel_case_property')
                    ->string['camel_case_property']
                        ->isIdenticalTo($camelCase)
        ;
    }

    public function testToString()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($card1 = new ild78\Card(uniqid()))
            ->and($card2 = new ild78\Card())
            ->and($card2->setNumber($number = '4111 1111 1111 1111'))
            ->and($this->testedInstance->setArray3([$card1, $card2]))
            ->then
                ->json($json = $this->testedInstance->toString())

                ->array(json_decode($json, true))
                    ->child['array3'](function ($child) use ($card1, $card2) {
                        $child
                            ->string[0]
                                ->isIdenticalTo($card1->getId())

                            ->child[1](function ($crd) use ($card2) {
                                $crd
                                    ->string['number']
                                        ->isIdenticalTo($card2->getNumber())
                                ;
                            })
                        ;
                    })
        ;
    }

    public function validDataProvider()
    {
        $datas = [];

        // We will make 3 data of each
        for ($idx = 0; $idx < 3; $idx++) {
            // string 1, between 10 and 20
            $datas[] = [
                'string1',
                $this->makeStringBetween(10, 20),
                10,
                20,
                null,
            ];

            // string 2, at least 10
            $datas[] = [
                'string2',
                $this->makeStringAtLeast(10),
                10,
                null,
                null,
            ];

            // string 3, max 20
            $datas[] = [
                'string3',
                $this->makeStringLessThan(20),
                null,
                20,
                null,
            ];

            // integer 1, between 10 and 20
            $datas[] = [
                'integer1',
                $this->makeIntegerBetween(10, 20),
                10,
                20,
                null,
            ];

            // integer 2, at least 10
            $datas[] = [
                'integer2',
                $this->makeIntegerAtLeast(10),
                10,
                null,
                null,
            ];

            // integer 3, max 20
            $datas[] = [
                'integer3',
                $this->makeIntegerLessThan(10),
                null,
                20,
                null,
            ];
        }

        // Fixed sizes

        // string 4, exactly 5
        $datas[] = [
            'string4',
            $this->makeStringWithFixedSize(5),
            null,
            null,
            5,
        ];

        // string 1, between 10 and 20
        $datas[] = [
            'string1',
            $this->makeStringWithFixedSize(10),
            10,
            20,
            null,
        ];

        // string 1, between 10 and 20
        $datas[] = [
            'string1',
            $this->makeStringWithFixedSize(20),
            10,
            20,
            null,
        ];

        // integer 1, between 10 and 20
        $datas[] = [
            'integer1',
            10,
            10,
            20,
            null,
        ];

        // integer 1, between 10 and 20
        $datas[] = [
            'integer1',
            20,
            10,
            20,
            null,
        ];

        // object 1
        $datas[] = [
            'object1',
            new ild78\Card,
            null,
            null,
            null,
        ];

        return $datas;
    }

    public function validArrayDataProvider()
    {
        $datas = [];
        $array1 = [];
        $array2 = [];
        $array3 = [];

        for ($idx = 0; $idx <= 3; $idx ++) {
            $array1[] = $this->makeStringBetween(10, 20);
            $array2[] = $this->makeIntegerBetween(10, 20);
            $array3[] = new ild78\Card;

            // array 1, array of string
            $datas[] = [
                'array1',
                $array1,
                $this->makeStringBetween(10, 20),
            ];

            // array 2, array of integer
            $datas[] = [
                'array2',
                $array2,
                $this->makeIntegerBetween(10, 20),
            ];

            // array 3, array of object
            $datas[] = [
                'array3',
                $array3,
                new ild78\Card,
            ];
        }

        return $datas;
    }
}
