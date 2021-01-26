<?php

namespace ild78\tests\unit\Stub\Core;

use DateTime;
use DateTimeZone;
use GuzzleHttp;
use ild78;
use mock;

class StubObject extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Dates;

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
            ild78\Exceptions\BadMethodCallException::class,
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

    public function test__construct()
    {
        $this
            ->given($this->newTestedInstance)

            ->if($id = uniqid())
            ->and($string1 = $this->makeStringBetween(10, 20))
            ->and($integer1 = $this->makeIntegerBetween(10, 20))
            ->then
                ->object($this->newTestedInstance())
                    ->isInstanceOfTestedClass

                ->object($this->newTestedInstance($id))
                    ->isInstanceOfTestedClass

                ->string($this->testedInstance->getId())
                    ->isIdenticalTo($id)

                ->object($this->newTestedInstance(['id' => $id, 'string1' => $string1, 'integer1' => $integer1]))
                    ->isInstanceOfTestedClass

                ->string($this->testedInstance->getId())
                    ->isIdenticalTo($id)

                ->string($this->testedInstance->getString1())
                    ->isIdenticalTo($string1)

                ->integer($this->testedInstance->getInteger1())
                    ->isIdenticalTo($integer1)
        ;
    }

    public function test__call()
    {
        $this
            ->if($string1 = $this->makeStringBetween(10, 20))
            ->then
                ->assert('get / set / add with array1 (array of string)')
                    ->array($this->newTestedInstance->testOnlyGetModified())
                        ->isEmpty

                    ->array($this->testedInstance->getArray1())
                        ->isEmpty

                    ->object($this->testedInstance->setArray1([$one = uniqid()]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getArray1())
                        ->string[0]
                            ->isIdenticalTo($one)
                        ->size->isEqualTo(1)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('array1')

                    ->object($this->testedInstance->addArray1($two = uniqid()))
                        ->isTestedInstance

                    ->array($this->testedInstance->getArray1())
                        ->string[0]
                            ->isIdenticalTo($one)
                        ->string[1]
                            ->isIdenticalTo($two)
                        ->size->isEqualTo(2)

                ->assert('get / set with snake_case property')
                    ->variable($this->newTestedInstance->get_camel_case_property())
                        ->isNull

                    ->variable($this->testedInstance->set_camel_case_property($string1))

                    ->string($this->testedInstance->get_camel_case_property())
                        ->isIdenticalTo($string1)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('camel_case_property')

                ->assert('get / set with snake_case property in different case')
                    ->variable($this->newTestedInstance->getCamelCaseProperty())
                        ->isNull

                    ->variable($this->testedInstance->setCamelCaseProperty($string1))

                    ->string($this->testedInstance->getCamelCaseProperty())
                        ->isIdenticalTo($string1)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('camel_case_property')
        ;
    }

    public function test__get__set()
    {
        $this
            ->given($string1 = $this->makeStringBetween(10, 20))
            ->and($one = uniqid())
            ->and($two = uniqid())
            ->then
                ->assert('get / set with a string')
                    ->variable($this->newTestedInstance->string1)
                        ->isNull

                    ->variable($this->testedInstance->string1 = $string1)

                    ->string($this->testedInstance->string1)
                        ->isIdenticalTo($string1)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('string1')

                ->assert('get / set with a snake_case string')
                    ->variable($this->newTestedInstance->camel_case_property)
                        ->isNull

                    ->variable($this->testedInstance->camel_case_property = $string1)

                    ->string($this->testedInstance->camel_case_property)
                        ->isIdenticalTo($string1)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('camel_case_property')

                ->assert('get / set with a snake_case string in different case')
                    ->variable($this->newTestedInstance->camelCaseProperty)
                        ->isNull

                    ->variable($this->testedInstance->camelCaseProperty = $string1)

                    ->string($this->testedInstance->camelCaseProperty)
                        ->isIdenticalTo($string1)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('camel_case_property')

                ->assert('get / set with an array')
                    ->array($this->newTestedInstance->array1)
                        ->isEmpty

                    ->variable($this->testedInstance->array1 = [$one, $two])

                    ->array($this->testedInstance->array1)
                        ->string[0]
                            ->isIdenticalTo($one)
                        ->string[1]
                            ->isIdenticalTo($two)
                        ->size->isEqualTo(2)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('array1')

                    // Be careful, no array access, you must modify all the array.
        ;
    }

    public function testAllowedValues()
    {
        $this
            ->assert('Test with strings')
                ->given($this->newTestedInstance)
                ->if($value = 'foo')
                ->then
                    ->object($this->testedInstance->setString5($value))
                        ->isTestedInstance

                    ->string($this->testedInstance->getString5())
                        ->isIdenticalTo($value)

                ->if($value = 'bar')
                ->then
                    ->object($this->testedInstance->setString5($value))
                        ->isTestedInstance

                    ->string($this->testedInstance->getString5())
                        ->isIdenticalTo($value)

                ->if($value = uniqid())
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setString5($value);
                    })
                        ->isInstanceOf(Ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo(vsprintf('"%s" is not a valid %s, please use one of the following : %s', [
                                $value,
                                'string5',
                                'foo, bar',
                            ]))

            ->assert('Test with strings and lists')
                ->given($this->newTestedInstance)
                ->if($value = 'foo')
                ->then
                    ->object($this->testedInstance->setString6([$value]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getString6())
                        ->hasSize(1)
                        ->string[0]
                            ->isIdenticalTo($value)

                ->if($value = 'bar')
                ->then
                    ->object($this->testedInstance->setString6([$value]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getString6())
                        ->hasSize(1)
                        ->string[0]
                            ->isIdenticalTo($value)

                ->if($value = uniqid())
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setString6([$value]);
                    })
                        ->isInstanceOf(Ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo(vsprintf('"%s" is not a valid %s, please use one of the following : %s', [
                                $value,
                                'string6',
                                'foo, bar',
                            ]))

            ->assert('Test with objects\' constants (as string)')
                ->given($this->newTestedInstance)
                ->if($value = ild78\Stub\FakeStatus::DONE)
                ->then
                    ->object($this->testedInstance->setString7($value))
                        ->isTestedInstance

                    ->string($this->testedInstance->getString7())
                        ->isIdenticalTo($value)

                ->if($value = uniqid())
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setString7($value);
                    })
                        ->isInstanceOf(Ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo(vsprintf('"%s" is not a valid %s, please use one of the following : %s', [
                                $value,
                                'string7',
                                'ild78\Stub\FakeStatus::ACTIVE, ild78\Stub\FakeStatus::DONE, ild78\Stub\FakeStatus::PENDING',
                            ]))

            ->assert('Test with integers')
                ->given($this->newTestedInstance)
                ->if($value = 1)
                ->then
                    ->object($this->testedInstance->setInteger4($value))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getInteger4())
                        ->isIdenticalTo($value)

                ->if($value = 2)
                ->then
                    ->object($this->testedInstance->setInteger4($value))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getInteger4())
                        ->isIdenticalTo($value)

                ->if($value = rand(4, PHP_INT_MAX))
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setInteger4($value);
                    })
                        ->isInstanceOf(Ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo(vsprintf('"%s" is not a valid %s, please use one of the following : %s', [
                                $value,
                                'integer4',
                                '1, 2, 3',
                            ]))

            ->assert('Test with strings and lists')
                ->given($this->newTestedInstance)
                ->if($value = 1)
                ->then
                    ->object($this->testedInstance->setInteger5([$value]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getInteger5())
                        ->hasSize(1)
                        ->integer[0]
                            ->isIdenticalTo($value)

                ->if($value = 2)
                ->then
                    ->object($this->testedInstance->setInteger5([$value]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getInteger5())
                        ->hasSize(1)
                        ->integer[0]
                            ->isIdenticalTo($value)

                ->if($value = rand(10, PHP_INT_MAX))
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setInteger5([$value]);
                    })
                        ->isInstanceOf(Ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo(vsprintf('"%s" is not a valid %s, please use one of the following : %s', [
                                $value,
                                'integer5',
                                '1, 2, 3',
                            ]))

            ->assert('Test with objects\' constants (as integer)')
                ->given($this->newTestedInstance)
                ->if($value = ild78\Stub\FakeOptions::READ)
                ->then
                    ->object($this->testedInstance->setInteger6($value))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getInteger6())
                        ->isIdenticalTo($value)

                ->if($value = rand(10, PHP_INT_MAX))
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setInteger6($value);
                    })
                        ->isInstanceOf(Ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo(vsprintf('"%s" is not a valid %s, please use one of the following : %s', [
                                $value,
                                'integer6',
                                'ild78\Stub\FakeOptions::READ, ild78\Stub\FakeOptions::WRITE',
                            ]))
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

                ->array($this->testedInstance->testOnlyGetModified())
                    ->contains($property)
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

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains($property)
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

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->isEmpty

                ->assert($assertMessage . '"set" will insert value like other')
                    ->object($this->testedInstance->dataModelSetter($property, $value))
                        ->isTestedInstance

                    ->array($this->testedInstance->dataModelGetter($property))
                        ->isIdenticalTo($value)
                        ->size
                            ->isEqualTo(count($value))

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains($property)

                ->assert($assertMessage . '"add" will add value without touching previous ones')
                    ->object($this->testedInstance->dataModelAdder($property, $extra))
                        ->isTestedInstance

                    ->array($this->testedInstance->dataModelGetter($property))
                        ->containsValues($value)
                        ->size
                            ->isEqualTo(count($value) + 1)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains($property)

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

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains($property)
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
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

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

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->isEmpty
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

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->isEmpty
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testDateProperty_hydratation($zone)
    {
        $this
            ->given($config = ild78\Config::getGlobal())

            ->if($timestamp = rand(946681200, 1893452400))
            ->and($datetime = new DateTime('@' . $timestamp))
            ->and($timezone = new DateTimeZone($zone))
            ->and($defaultTimezone = new DateTimeZone('+00:00'))

            ->if($data = [
                'date1' => $timestamp,
            ])
            ->and($this->newTestedInstance->hydrate($data))
            ->then
                ->assert('Without default timezone')
                    ->if($config->resetDefaultTimeZone())
                    ->then
                        ->dateTime($this->testedInstance->getDate1())
                            ->isEqualTo($datetime)
                            ->hasTimeZone($defaultTimezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)

                            ->hasKey('date1')
                            ->integer['date1']
                                ->isEqualTo($timestamp)

                ->assert('With default timezone')
                    ->if($config->setDefaultTimezone($timezone))
                    ->then
                        ->dateTime($this->testedInstance->getDate1())
                            ->isEqualTo($datetime)
                            ->hasTimeZone($timezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)

                            ->hasKey('date1')
                            ->integer['date1']
                                ->isEqualTo($timestamp)
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testDateProperty_withAnInteger($zone)
    {
        $this
            ->given($config = ild78\Config::getGlobal())

            ->if($timestamp = rand(946681200, 1893452400))
            ->and($datetime = new DateTime('@' . $timestamp))
            ->and($timezone = new DateTimeZone($zone))
            ->and($defaultTimezone = new DateTimeZone('+00:00'))
            ->then
                ->variable($this->newTestedInstance->getDate1())
                    ->isNull

                ->object($this->testedInstance->setDate1($timestamp))
                    ->isTestedInstance

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->assert('Without default timezone')
                    ->if($config->resetDefaultTimeZone())
                    ->then
                        ->dateTime($this->testedInstance->getDate1())
                            ->isEqualTo($datetime)
                            ->hasTimezone($defaultTimezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date1')
                            ->integer['date1']
                                ->isEqualTo($timestamp)

                ->assert('With default timezone')
                    ->if($config->setDefaultTimezone($timezone))
                    ->then
                        ->dateTime($this->testedInstance->getDate1())
                            ->isEqualTo($datetime)
                            ->hasTimezone($timezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date1')
                            ->integer['date1']
                                ->isEqualTo($timestamp)
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testDateProperty_withAnObject($zone)
    {
        $this
            ->given($config = ild78\Config::getGlobal())

            ->if($timestamp = rand(946681200, 1893452400))
            ->and($datetime = new DateTime('@' . $timestamp))
            ->and($timezone = new DateTimeZone($zone))
            ->and($defaultTimezone = new DateTimeZone('+00:00'))
            ->then
                ->variable($this->newTestedInstance->getDate1())
                    ->isNull

                ->object($this->testedInstance->setDate1($datetime))
                    ->isTestedInstance

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->assert('Without default timezone')
                    ->if($config->resetDefaultTimeZone())
                    ->then
                        ->dateTime($this->testedInstance->getDate1())
                            ->isEqualTo($datetime)
                            ->hasTimezone($defaultTimezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date1')
                            ->integer['date1']
                                ->isEqualTo($timestamp)

                ->assert('With default timezone')
                    ->if($config->setDefaultTimezone($timezone))
                    ->then
                        ->dateTime($this->testedInstance->getDate1())
                            ->isEqualTo($datetime)
                            ->hasTimezone($timezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date1')
                            ->integer['date1']
                                ->isEqualTo($timestamp)
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testDatePropertyList_hydratation($zone)
    {
        $this
            ->given($config = ild78\Config::getGlobal())

            ->if($timestamp = rand(946681200, 1893452400))
            ->and($datetime = new DateTime('@' . $timestamp))
            ->and($timezone = new DateTimeZone($zone))
            ->and($defaultTimezone = new DateTimeZone('+00:00'))

            ->if($data = [
                'date2' => [
                    $datetime,
                    $timestamp,
                ],
            ])
            ->and($this->newTestedInstance->hydrate($data))
            ->then
                ->assert('Without default timezone')
                    ->if($config->resetDefaultTimeZone())
                    ->then
                        ->array($this->testedInstance->getDate2())
                            ->hasSize(2)
                            ->dateTime[0]
                                ->isEqualTo($datetime)
                                ->hasTimeZone($defaultTimezone)
                            ->dateTime[1]
                                ->isEqualTo($datetime)
                                ->hasTimeZone($defaultTimezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date2')
                            ->child['date2'](function ($date2) use ($timestamp) {
                                $date2
                                    ->hasSize(2)
                                    ->integer[0]
                                        ->isEqualTo($timestamp)
                                    ->integer[1]
                                        ->isEqualTo($timestamp)
                                ;
                            })

                ->assert('With default timezone')
                    ->if($config->setDefaultTimezone($timezone))
                    ->then
                        ->array($this->testedInstance->getDate2())
                            ->hasSize(2)
                            ->dateTime[0]
                                ->isEqualTo($datetime)
                                ->hasTimeZone($timezone)
                            ->dateTime[1]
                                ->isEqualTo($datetime)
                                ->hasTimeZone($timezone)

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date2')
                            ->child['date2'](function ($date2) use ($timestamp) {
                                $date2
                                    ->hasSize(2)
                                    ->integer[0]
                                        ->isEqualTo($timestamp)
                                    ->integer[1]
                                        ->isEqualTo($timestamp)
                                ;
                            })
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testDatePropertyList_withAnInteger($zone)
    {
        $this
            ->given($config = ild78\Config::getGlobal())

            ->if($timestamp = rand(946681200, 1893452400))
            ->and($datetime = new DateTime('@' . $timestamp))
            ->and($timezone = new DateTimeZone($zone))
            ->and($defaultTimezone = new DateTimeZone('+00:00'))
            ->then
                ->array($this->newTestedInstance->getDate2())
                    ->isEmpty

                ->object($this->testedInstance->addDate2($timestamp))
                    ->isTestedInstance

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->assert('Without default timezone')
                    ->if($config->resetDefaultTimeZone())
                    ->then
                        ->array($this->testedInstance->getDate2())
                            ->hasSize(1)
                            ->dateTime[0]
                                ->isEqualTo($datetime)
                                ->hasTimezone($defaultTimezone)

                        ->array($exported = $this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date2')
                            ->child['date2'](function ($date2) use ($timestamp) {
                                $date2
                                    ->hasSize(1)
                                    ->integer[0]
                                        ->isEqualTo($timestamp)
                                ;
                            })

                ->assert('With default timezone')
                    ->if($config->setDefaultTimezone($timezone))
                    ->then
                        ->array($this->testedInstance->getDate2())
                            ->hasSize(1)
                            ->dateTime[0]
                                ->isEqualTo($datetime)
                                ->hasTimezone($timezone)

                        ->array($exported = $this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date2')
                            ->child['date2'](function ($date2) use ($timestamp) {
                                $date2
                                    ->hasSize(1)
                                    ->integer[0]
                                        ->isEqualTo($timestamp)
                                ;
                            })
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testDatePropertyList_withAnObject($zone)
    {
        $this
            ->given($config = ild78\Config::getGlobal())

            ->if($timestamp = rand(946681200, 1893452400))
            ->and($datetime = new DateTime('@' . $timestamp))
            ->and($timezone = new DateTimeZone($zone))
            ->and($defaultTimezone = new DateTimeZone('+00:00'))
            ->then
                ->array($this->newTestedInstance->getDate2())
                    ->isEmpty

                ->object($this->testedInstance->addDate2($datetime))
                    ->isTestedInstance

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->assert('Without default timezone')
                    ->if($config->resetDefaultTimeZone())
                    ->then
                        ->array($this->testedInstance->getDate2())
                            ->hasSize(1)
                            ->dateTime[0]
                                ->isEqualTo($datetime)
                                ->hasTimezone($defaultTimezone)

                        ->array($exported = $this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date2')
                            ->child['date2'](function ($date2) use ($timestamp) {
                                $date2
                                    ->hasSize(1)
                                    ->integer[0]
                                        ->isEqualTo($timestamp)
                                ;
                            })

                ->assert('With default timezone')
                    ->if($config->setDefaultTimezone($timezone))
                    ->then
                        ->array($this->testedInstance->getDate2())
                            ->hasSize(1)
                            ->dateTime[0]
                                ->isEqualTo($datetime)
                                ->hasTimezone($timezone)

                        ->array($exported = $this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('date2')
                            ->child['date2'](function ($date2) use ($timestamp) {
                                $date2
                                    ->hasSize(1)
                                    ->integer[0]
                                        ->isEqualTo($timestamp)
                                ;
                            })
        ;
    }

    public function testGetCreationDate()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

            ->if($client = new mock\ild78\Http\Client)
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

            ->if($response = new mock\ild78\Http\Response(200))
            ->and($this->calling($client)->request = $response)

            ->assert('Can be null')
                ->given($this->calling($response)->getBody = new ild78\Http\Stream('{}'))

                ->if($this->newTestedInstance(uniqid()))
                ->then
                    ->variable($this->testedInstance->getCreationDate())
                        ->isNull

            ->assert('No date but an ID, it will populate data')
                ->given($created = rand(946681200, 1893452400))
                ->and($this->calling($response)->getBody = new ild78\Http\Stream(json_encode(compact('created'))))

                ->if($this->newTestedInstance(uniqid()))
                ->then
                    ->dateTime($this->testedInstance->getCreationDate())
                        ->isEqualTo(new DateTime('@' . $created))
        ;
    }

    public function testGet()
    {
        $this
            ->given($timestamp = rand(946681200, 1893452400))
            ->and($string1 = $this->makeStringBetween(10, 20))
            ->and($integer1 = $this->makeIntegerBetween(10, 20))
            ->and($integer2 = $this->makeIntegerBetween(10, 20))
            ->and($camelCaseProperty = $this->makeStringBetween(10, 20))
            ->and($objectData = ['integer2' => $integer2])
            ->and($object2 = $this->newTestedInstance($objectData))
            ->and($unknownKey = uniqid())
            ->and($unknownValue = uniqid())
            ->and($data = [
                'string1' => $string1,
                'integer1' => $integer1,
                'object2' => $objectData,
                'camel_case_property' => $camelCaseProperty,
                $unknownKey => $unknownValue,
            ])

            ->if($response = new mock\ild78\Http\Response(200))
            ->and($this->calling($response)->getBody[] = new ild78\Http\Stream(json_encode($data)))

            ->if($client = new mock\ild78\Http\Client)
            ->and($this->calling($client)->request = $response)

            ->if($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))
            ->and($config->setHttpClient($client))

            ->if($id = uniqid())
            ->and($this->newTestedInstance($id))
            ->then
                ->assert('Default values')
                    ->variable($this->testedInstance->get())
                        ->isNull

                    ->variable($this->testedInstance->get(uniqid()))
                        ->isNull

                    ->variable($this->testedInstance->get('string1'))
                        ->isNull

                    ->variable($this->testedInstance->get('integer1'))
                        ->isNull

                    ->variable($this->testedInstance->get('object2'))
                        ->isNull

                    ->variable($this->testedInstance->get('camel_case_property'))
                        ->isNull

                    ->variable($this->testedInstance->get('camelCaseProperty'))
                        ->isNull

                    ->variable($this->testedInstance->get($unknownKey))
                        ->isNull

                ->assert('After API call')
                    ->if($this->testedInstance->populate())
                    ->then
                        ->array($this->testedInstance->get())
                            ->isEqualTo($data)

                        ->variable($this->testedInstance->get(uniqid()))
                            ->isNull

                        ->string($this->testedInstance->get('string1'))
                            ->isIdenticalTo($string1)

                        ->integer($this->testedInstance->get('integer1'))
                            ->isIdenticalTo($integer1)

                        ->array($this->testedInstance->get('object2'))
                            ->isEqualTo($objectData)

                        ->string($this->testedInstance->get('camel_case_property'))
                            ->isIdenticalTo($camelCaseProperty)

                        ->string($this->testedInstance->get('camelCaseProperty'))
                            ->isIdenticalTo($camelCaseProperty)

                        ->string($this->testedInstance->get($unknownKey))
                            ->isIdenticalTo($unknownValue)

                        ->array($this->testedInstance->getObject2()->get())
                            ->isEqualTo($objectData)

                        ->integer($this->testedInstance->getObject2()->get('integer2'))
                            ->isIdenticalTo($integer2)
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
                    ->hasKeys(['restricted', 'required', 'value'])

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
                            ->hasKeys(['restricted', 'required', 'value'])
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
            ->and($object->testOnlySetPopulated(false))
            ->and($populatedPropagation = [
                'object2' => $object,
            ])

            ->and($id = uniqid())
            ->and($objectWithId = $this->newTestedInstance)
            ->and($withIds = [
                'object2' => $id,
            ])

            ->and($id1 = uniqid())
            ->and($id2 = uniqid())
            ->and($withArray = [
                'array4' => [
                    $id1,
                    $id2,
                ],
            ])
            ->and(ild78\Config::init([]))

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

                    ->array($this->testedInstance->testOnlyGetModified())
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

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->isEmpty

                ->assert('Hydratation will pass populated flag')
                    ->boolean($object->testOnlyGetPopulated())
                        ->isIdenticalTo($this->testedInstance->testOnlyGetPopulated())
                        ->isFalse

                    ->object($this->testedInstance->testOnlySetPopulated(true)->hydrate($populatedPropagation))
                        ->isTestedInstance

                    ->object($this->testedInstance->getObject2())
                        ->isInstanceOfTestedClass
                        ->isIdenticalTo($object)

                    ->boolean($object->testOnlyGetPopulated())
                        ->isIdenticalTo($this->testedInstance->testOnlyGetPopulated())
                        ->isTrue

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('object2')

                ->assert('Hydratation will keep instances')
                    ->object($this->testedInstance->setObject2($objectWithId)->hydrate($withIds))
                        ->isTestedInstance

                    ->object($this->testedInstance->getObject2())
                        ->isIdenticalTo($objectWithId)

                    ->string($objectWithId->getId())
                        ->isIdenticalTo($id)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('object2')

                ->assert('Work with lists')
                    ->object($this->newTestedInstance->hydrate($withArray))
                        ->isTestedInstance

                    ->array($array = $this->testedInstance->getArray4())
                        ->hasSize(2)

                    ->object($array[0])
                        ->isInstanceOfTestedClass

                    ->string($array[0]->getId())
                        ->isIdenticalTo($id1)

                    ->object($array[1])
                        ->isInstanceOfTestedClass

                    ->string($array[1]->getId())
                        ->isIdenticalTo($id2)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('array4')

                ->assert('Work with lists, and keep previous instance too')
                    ->object($object = $this->newTestedInstance($id2))
                    ->object($this->newTestedInstance->setArray4([$object])->hydrate($withArray))
                        ->isTestedInstance

                    ->array($array = $this->testedInstance->getArray4())
                        ->hasSize(2)

                    ->object($array[0])
                        ->isInstanceOfTestedClass

                    ->string($array[0]->getId())
                        ->isIdenticalTo($id1)

                    ->object($array[1])
                        ->isInstanceOfTestedClass
                        ->isIdenticalTo($object)

                    ->string($array[1]->getId())
                        ->isIdenticalTo($id2)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->contains('array4')
        ;
    }

    public function testIsModified_isNotModified()
    {
        $this
            ->given($object1 = $this->newTestedInstance)
            ->and($object2 = $this->newTestedInstance)
            ->and($object3 = $this->newTestedInstance)
            ->and($this->newTestedInstance)
            ->then
                ->assert('Should return internal state')
                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

                    ->boolean($this->testedInstance->isNotModified())
                        ->isTrue

                    ->boolean($this->testedInstance->testOnlyAddModified('string1')->isModified())
                        ->isTrue

                    ->boolean($this->testedInstance->isNotModified())
                        ->isFalse

                ->assert('Should return false if an object in one property is modified')
                    ->if($this->testedInstance->setObject2($object1))
                    ->and($object1->testOnlyAddModified('number'))
                    ->and($this->testedInstance->testOnlyResetModified())

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->boolean($this->testedInstance->isNotModified())
                        ->isFalse

                ->assert('Should return false if an object in a list is modified')
                    ->if($this->testedInstance->addArray4($object2))
                    ->and($this->testedInstance->addArray4($object3))
                    ->and($this->testedInstance->testOnlyResetModified())

                    ->and($object1->testOnlyResetModified()) // Be sure last test won't interfere

                    // randomise which one is modified
                    ->and($first = (bool) rand(1, 10) % 2)
                    ->when(function () use ($first, $object2, $object3) {
                        if ($first) {
                            $object2->testOnlyAddModified('string1');
                            $object3->testOnlyResetModified();
                        } else {
                            $object2->testOnlyResetModified();
                            $object3->testOnlyAddModified('string1');
                        }
                    })

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->boolean($this->testedInstance->isNotModified())
                        ->isFalse

                ->assert('Should not use not exported object state')
                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->setObject3($object3))

                    ->and($object3->testOnlyAddModified('string1'))
                    ->and($this->testedInstance->testOnlyResetModified())

                    ->then
                        ->boolean($this->testedInstance->isModified())
                            ->isFalse

                        ->boolean($this->testedInstance->isNotModified())
                            ->isTrue
        ;
    }

    public function testJsonSerialize()
    {
        $this
            ->given($object2 = $this->newTestedInstance(uniqid()))
            ->and($object2->setString1($this->makeStringBetween(10, 20)))

            ->if($this->newTestedInstance($id = uniqid()))
            ->and($this->testedInstance->setCamelCaseProperty($camelCase = uniqid()))
            ->and($this->testedInstance->forceRestricted1(uniqid()))
            ->and($this->testedInstance->setObject2($object2))
            ->then
                ->assert('An unmodified object with an ID should return only the ID')
                    ->if($this->testedInstance->testOnlyResetModified())
                    ->and($object2->testOnlyResetModified())
                    ->then
                        ->string($this->testedInstance->jsonSerialize())
                            ->isIdenticalTo($id)

                ->assert('A modified object with an ID return a body (without id)')
                    ->if($this->testedInstance->testOnlyAddModified('camel_case_property'))
                    ->and($this->testedInstance->testOnlyAddModified('object2'))
                    ->and($object2->testOnlyResetModified())
                    ->then
                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('id')
                            ->notHasKey('restricted1') // no output for restricted property

                            ->notHasKey('camelCaseProperty') // camelCase properties has converted to snake_case
                            ->hasKey('camel_case_property')
                            ->string['camel_case_property']
                                ->isIdenticalTo($camelCase)

                            ->hasKey('object2')
                            ->string['object2']
                                ->isIdenticalTo($object2->getId()) // object2 is not modified

                ->assert('A modified object with another modified object in it should return both body (without ids)')
                    ->if($this->testedInstance->testOnlyAddModified('camel_case_property'))
                    ->and($object2->testOnlyAddModified('string1'))
                    ->then
                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('id')

                            ->notHasKey('camelCaseProperty') // camelCase properties has converted to snake_case
                            ->hasKey('camel_case_property')
                            ->string['camel_case_property']
                                ->isIdenticalTo($camelCase)

                            ->hasKey('object2')
                            ->child['object2'](function ($child) use ($object2) {
                                $child
                                    ->notHasKey('id')

                                    ->string['string1']
                                        ->isIdenticalTo($object2->getString1())
                                ;
                            })

                ->assert('An unmodified object with another modified object in it should return both body too (without ids)')
                    ->if($this->testedInstance->testOnlyResetModified())
                    ->and($object2->testOnlyAddModified('string1'))
                    ->then
                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('id')

                            ->notHasKey('camelCaseProperty')   // camelCase properties has converted to snake_case
                            ->notHasKey('camel_case_property') // was not modified

                            ->hasKey('object2')
                            ->child['object2'](function ($child) use ($object2) {
                                $child
                                    ->notHasKey('id')

                                    ->string['string1']
                                        ->isIdenticalTo($object2->getString1())
                                ;
                            })

                ->assert('Same test (unmodified object with modified in it) but with a list of objects')
                    ->if($object2 = $this->newTestedInstance)
                    ->and($object3 = $this->newTestedInstance)

                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->addArray4($object2))
                    ->and($this->testedInstance->addArray4($object3))

                    ->and($this->testedInstance->testOnlySetId(uniqid()))

                    ->and($object2->testOnlySetId(uniqid()))
                    ->and($object2->setString1($this->makeStringBetween(10, 20)))

                    ->and($object3->testOnlySetId(uniqid()))
                    ->and($object3->setString1($this->makeStringBetween(10, 20)))

                    ->and($this->testedInstance->testOnlyResetModified())
                    ->and($object2->testOnlyResetModified())
                    ->and($object3->testOnlyAddModified('string1'))
                    ->then

                        // Recap
                        //  Tested instance is not modified and contains only data in array4
                        //  Object 2 is not modified and will return only an id
                        //  Object 3 is modified, so it will return a body
                        //
                        //  Object 2 and 3 have data on string1
                        //
                        //  All have ids

                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('id')

                            ->hasKey('array4')
                            ->child['array4'](function ($array4) use ($object2, $object3) {
                                $array4
                                    ->string[0]
                                        ->isIdenticalTo($object2->getId())

                                    ->array[1]
                                        ->notHasKey('id')

                                        ->string['string1']
                                            ->isIdenticalTo($object3->getString1())
                                ;
                            })

                ->assert('An unmodified object should appear if not modified')
                    ->given($object = $this->newTestedInstance)
                    ->and($object->testOnlySetId(uniqid()))
                    ->and($object->setString1($this->makeStringBetween(10, 20)))
                    ->and($object->testOnlyResetModified())

                    ->and($list1 = clone $object)
                    ->and($list2 = clone $object)

                    ->if($string = $this->makeStringBetween(10, 20))

                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->setObject2($object))
                    ->and($this->testedInstance->addArray4($list1))
                    ->and($this->testedInstance->addArray4($list2))
                    ->and($this->testedInstance->testOnlyResetModified())
                    ->and($this->testedInstance->setString1($string))

                    ->then
                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->notHasKey('object2')
                            ->notHasKey('array4')

                            ->hasKey('string1')
                            ->string['string1']
                                ->isIdenticalTo($string)

                ->assert('If one object in a list is modified, all list is exported')
                    ->given($object1 = $this->newTestedInstance)
                    ->and($object1->testOnlySetId(uniqid()))
                    ->and($object1->setString1($this->makeStringBetween(10, 20)))
                    ->and($object1->testOnlyResetModified())

                    ->if($object2 = $this->newTestedInstance)
                    ->and($object2->testOnlySetId(uniqid()))
                    ->and($object2->setString1($this->makeStringBetween(10, 20)))

                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->addArray4($object1))
                    ->and($this->testedInstance->addArray4($object2))

                    ->then
                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)

                            ->hasKey('array4')
                            ->child['array4'](function ($array4) use ($object1, $object2) {
                                $array4
                                    ->string[0]
                                        ->isIdenticalTo($object1->getId())

                                    ->array[1]
                                        ->notHasKey('id')

                                        ->string['string1']
                                            ->isIdenticalTo($object2->getString1())
                                ;
                            })

                ->assert('An unmodified list should not be exported except if a modified object is in it')
                    ->given($object1 = $this->newTestedInstance)

                    ->and($string1 = $this->makeStringBetween(10, 20))
                    ->and($string2 = $this->makeStringBetween(10, 20))

                    ->and($object1->testOnlySetId(uniqid()))
                    ->and($object1->setString1($this->makeStringBetween(10, 20)))
                    ->and($object1->testOnlyResetModified())

                    ->if($object2 = $this->newTestedInstance)
                    ->and($object2->testOnlySetId(uniqid()))
                    ->and($object2->setString1($this->makeStringBetween(10, 20)))

                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->addArray1($string1))
                    ->and($this->testedInstance->addArray1($string2))
                    ->and($this->testedInstance->addArray4($object1))
                    ->and($this->testedInstance->addArray4($object2))

                    ->then
                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(2)

                            ->hasKey('array1')
                            ->child['array1'](function ($array1) use ($string1, $string2) {
                                $array1
                                    ->string[0]
                                        ->isIdenticalTo($string1)

                                    ->string[1]
                                        ->isIdenticalTo($string2)
                                ;
                            })

                            ->hasKey('array4')
                            ->child['array4'](function ($array4) use ($object1, $object2) {
                                $array4
                                    ->string[0]
                                        ->isIdenticalTo($object1->getId())

                                    ->array[1]
                                        ->notHasKey('id')

                                        ->string['string1']
                                            ->isIdenticalTo($object2->getString1())
                                ;
                            })

                    ->if($this->testedInstance->testOnlyResetModified())

                    ->then
                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)

                            ->hasKey('array4')
                            ->child['array4'](function ($array4) use ($object1, $object2) {
                                $array4
                                    ->string[0]
                                        ->isIdenticalTo($object1->getId())

                                    ->array[1]
                                        ->notHasKey('id')

                                        ->string['string1']
                                            ->isIdenticalTo($object2->getString1())
                                ;
                            })
        ;
    }

    public function testPopulate()
    {
        $this
            ->assert('Work with an id')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($id = uniqid())
                ->and($timestamp = time())
                ->and($mock = new GuzzleHttp\Handler\MockHandler([
                    new GuzzleHttp\Psr7\Response(200, [], '{"id":"' . $id . '","created":' . $timestamp . '}'),
                ]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($config->setHttpClient($client))
                ->and($config->setDebug(false))

                ->if($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->populate())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo($id)

                    ->dateTime($date = $this->testedInstance->getCreationDate())
                        ->variable($date->format('U'))
                            ->isEqualTo($timestamp)

                    ->array($this->testedInstance->testOnlyGetModified())
                        ->isEmpty

            ->assert('Only one request with two consecutive call')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))

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

            ->assert('Send blocks populate')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))
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
                    ->object($this->testedInstance->send())
                    ->object($this->testedInstance->populate())

                    ->mock($client)
                        ->call('request')
                            ->withArguments('PATCH')
                                ->once

                            ->withArguments('GET')
                                ->never

            ->assert('Populate blocks send')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))
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
                    ->object($this->testedInstance->populate())
                    ->object($this->testedInstance->send())

                    ->mock($client)
                        ->call('request')
                            ->withArguments('PATCH')
                                ->never

                            ->withArguments('GET')
                                ->once

            ->assert('Inner object are marked as populated too')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($id = uniqid())
                ->and($timestamp = time())
                ->and($body = '{"id":"' . $id . '","created":' . $timestamp . ',"object2":"' . uniqid() . '","array4":["' . uniqid() . '"]}')
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))

                ->and($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->populate())

                    ->mock($client)
                        ->call('request')
                            ->once

                    ->boolean($this->testedInstance->getObject2()->testOnlyGetPopulated())
                        ->isTrue

                    ->array($array4 = $this->testedInstance->getArray4())
                        ->hasSize(1)

                    ->boolean($array4[0]->testOnlyGetPopulated())
                        ->isTrue

            ->assert('Populate working normally')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))
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
                ->and($object2 = uniqid())

                ->and($body = json_encode(compact('id', 'created', 'string1', 'string2', 'string3', 'string4', 'integer1', 'integer2', 'integer3', 'restricted1', 'object2')))

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

                        ->object($this->testedInstance->getObject2())
                            ->isInstanceOfTestedClass
                        ->string($this->testedInstance->getObject2()->getId())
                            ->isIdenticalTo($object2)
                        ->boolean($this->testedInstance->getObject2()->testOnlyGetPopulated())
                            ->isTrue

                        ->boolean($this->testedInstance->testOnlyGetPopulated())
                            ->isTrue

            ->assert('Inner object are not marked as modified')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($id = uniqid())
                ->and($timestamp = time())
                ->and($data = [
                    'id' => $id,
                    'created' => $timestamp,
                    'object2' => [
                        'integer1' => rand(10, 20),
                    ],
                    'array4' => [
                        [
                            'integer1' => rand(10, 20),
                        ],
                    ],
                ])
                ->and($body = json_encode($data))
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))

                ->and($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->populate())

                    ->mock($client)
                        ->call('request')
                            ->once

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

                    ->boolean($this->testedInstance->getObject2()->isModified())
                        ->isFalse

                    ->array($array4 = $this->testedInstance->getArray4())
                        ->hasSize(1)

                    ->boolean($array4[0]->isModified())
                        ->isFalse
        ;
    }

    public function testRetrieve()
    {
        $this
            ->if($id = uniqid())
            ->and($class = (string) $this->testedClass)
            ->then
                ->object($obj = $class::retrieve($id))
                    ->isInstanceOf($this->newTestedInstance)

                ->string($obj->getId())
                    ->isIdenticalTo($id)
        ;
    }

    public function testSend()
    {
        $this
            ->assert('Throw exception if requirement are not provided')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setString2($this->makeStringAtLeast(10)))
                ->then
                    ->exception(function () {
                        $this->testedInstance->send();
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('You need to provide a value for : integer1, string1')

            ->assert('Send data if all requirement are complete')
                ->if($string1 = $this->makeStringBetween(10, 20))
                ->and($integer1 = $this->makeIntegerBetween(10, 20))
                ->and($id = uniqid())
                ->and($created = time())

                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($body = json_encode(compact('id', 'created', 'string1', 'integer1')))
                ->and($client = new mock\GuzzleHttp\Client)
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))
                ->and($config->setDebug(false))

                ->and($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))
                ->and($logMessage = sprintf('StubObject "%s" created', $id))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setString1($string1))
                ->and($this->testedInstance->setInteger1($integer1))

                ->if($options = [])
                ->and($options['headers'] = [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ])
                ->and($options['timeout'] = $config->getTimeout())
                ->and($options['body'] = json_encode($this->testedInstance))
                ->and($location = $this->testedInstance->getUri())
                ->then
                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $location, $options)
                                ->once

                    ->mock($logger)
                        ->call('info')
                            ->withArguments($logMessage)
                                ->once

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

            ->assert('Update data if object has an id')
                ->if($string1 = $this->makeStringBetween(10, 20))
                ->and($id = uniqid())
                ->and($created = time())

                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($body = json_encode(compact('id', 'created', 'string1')))
                ->and($client = new mock\GuzzleHttp\Client)
                ->and($response = new GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))
                ->and($config->setDebug(false))

                ->and($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))
                ->and($logMessage = sprintf('StubObject "%s" updated', $id))

                ->if($this->newTestedInstance($id))
                ->and($this->testedInstance->setString1($string1))

                ->if($options = [])
                ->and($options['headers'] = [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ])
                ->and($options['timeout'] = $config->getTimeout())
                ->and($options['body'] = json_encode(['string1' => $string1]))
                ->and($location = $this->testedInstance->getUri())
                ->then
                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')
                            ->withArguments('PATCH', $location, $options)
                                ->once

                    ->mock($logger)
                        ->call('info')
                            ->withArguments($logMessage)
                                ->once

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

            ->assert('No error if returned body is null (saw with PATCH implementation)')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($client = new mock\ild78\Http\Client)
                ->and($config->setHttpClient($client))
                ->and($config->setDebug(false))

                ->if($response = new mock\ild78\Http\Response(200))
                ->and($this->calling($client)->request = $response)
                ->and($this->calling($response)->getBody = new ild78\Http\Stream(''))

                ->if($string1 = $this->makeStringBetween(10, 20))
                ->and($integer1 = $this->makeIntegerBetween(10, 20))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setString1($string1))
                ->and($this->testedInstance->setInteger1($integer1))

                ->if($options = [])
                ->and($options['headers'] = [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ])
                ->and($options['timeout'] = $config->getTimeout())
                ->and($options['body'] = json_encode($this->testedInstance))
                ->and($location = $this->testedInstance->getUri())
                ->then
                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->variable($this->testedInstance->getId())
                        ->isNull // no body, no id :/

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $location, $options)
                                ->once

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
        ;
    }

    public function testToArray()
    {
        $this
            ->given($object2 = $this->newTestedInstance)
            ->and($object2->setString1($this->makeStringBetween(10, 20)))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setCamelCaseProperty($camelCase = uniqid()))
            ->and($this->testedInstance->forceRestricted1($restricted = uniqid()))
            ->and($this->testedInstance->setObject2($object2))
            ->then
                ->array($this->testedInstance->toArray())
                    ->notHasKey('restricted1')

                    ->notHasKey('camelCaseProperty')
                    ->hasKey('camel_case_property')
                    ->string['camel_case_property']
                        ->isIdenticalTo($camelCase)

                    ->hasKey('object2')
                    ->object['object2']
                        ->isIdenticalTo($object2)
        ;
    }

    public function testToString_toJson_castToString()
    {
        $this
            ->given($object1 = $this->newTestedInstance) // Unmodified / Got id and string1
            ->and($object2 = $this->newTestedInstance)   // Modified   / Got id and string1
            ->and($object3 = $this->newTestedInstance)   // Modified   / Got string1
            ->and($object = $this->newTestedInstance)    // Modified   / Go id and string1  / Not in array

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setObject2($object))
            ->and($this->testedInstance->addArray4($object1))
            ->and($this->testedInstance->addArray4($object2))
            ->and($this->testedInstance->addArray4($object3))

            ->and($this->testedInstance->setCamelCaseProperty($camelCase = uniqid()))
            ->and($this->testedInstance->forceRestricted1($restricted = uniqid()))
            ->and($this->testedInstance->setString1($this->makeStringBetween(10, 20)))

            ->and($object->setString1($this->makeStringBetween(10, 20)))
            ->and($object1->setString1($this->makeStringBetween(10, 20)))
            ->and($object2->setString1($this->makeStringBetween(10, 20)))
            ->and($object3->setString1($this->makeStringBetween(10, 20)))

            ->and($this->testedInstance->testOnlySetId(uniqid()))
            ->and($object->testOnlySetId(uniqid()))
            ->and($object1->testOnlySetId(uniqid()))
            ->and($object2->testOnlySetId(uniqid()))

            ->and($object1->testOnlyResetModified())

            ->then
                ->json($json = $this->testedInstance->toJson())
                    ->isIdenticalTo($this->testedInstance->toString())
                    ->isIdenticalTo(json_encode($this->testedInstance))
                    ->isIdenticalTo((string) $this->testedInstance)

                ->array(json_decode($json, true))
                    ->notHasKeys(['id', 'camelCaseProperty', 'restricted1'])

                    ->hasKeys(['string1', 'camel_case_property', 'object2', 'array4'])

                    ->string['string1']
                        ->isIdenticalTo($this->testedInstance->getString1())

                    ->string['camel_case_property']
                        ->isIdenticalTo($this->testedInstance->getCamelCaseProperty())

                    ->child['object2'](function ($obj2) use ($object) {
                        $obj2
                            ->hasSize(1)

                            ->hasKey('string1')
                            ->string['string1']
                                ->isIdenticalTo($object->getString1())
                        ;
                    })

                    ->child['array4'](function ($array4) use ($object1, $object2, $object3) {
                        $array4
                            ->hasSize(3)

                            // $object1
                            ->string[0]
                                ->isIdenticalTo($object1->getId())

                            // $object2
                            ->child[1](function ($child) use ($object2) {
                                $child
                                    ->notHasKey('id')

                                    ->string['string1']
                                        ->isIdenticalTo($object2->getString1())
                                ;
                            })

                            // $object3
                            ->child[2](function ($child) use ($object3) {
                                $child
                                    ->notHasKey('id')

                                    ->string['string1']
                                        ->isIdenticalTo($object3->getString1())
                                ;
                            })
                        ;
                    })

            ->if($this->testedInstance->testOnlyResetModified())
            ->and($this->testedInstance->testOnlyAddModified('string1'))
            ->and($object->testOnlyResetModified())
            ->and($object1->testOnlyResetModified())
            ->and($object2->testOnlyResetModified())

            ->then
                ->json($json = $this->testedInstance->toJson())
                    ->isIdenticalTo($this->testedInstance->toString())
                    ->isIdenticalTo(json_encode($this->testedInstance))
                    ->isIdenticalTo((string) $this->testedInstance)

                ->array(json_decode($json, true))
                    ->notHasKeys(['id', 'camelCaseProperty', 'restricted1', 'camel_case_property', 'object2'])

                    ->hasKeys(['string1', 'array4'])

                    ->string['string1']
                        ->isIdenticalTo($this->testedInstance->getString1())

                    ->child['array4'](function ($array4) use ($object1, $object2, $object3) {
                        $array4
                            ->hasSize(3)

                            // $object1
                            ->string[0]
                                ->isIdenticalTo($object1->getId())

                            // $object2
                            ->string[1]
                                ->isIdenticalTo($object2->getId())

                            // $object3
                            ->child[2](function ($child) use ($object3) {
                                $child
                                    ->notHasKey('id')

                                    ->string['string1']
                                        ->isIdenticalTo($object3->getString1())
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
