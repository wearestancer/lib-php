<?php

namespace ild78\tests\unit\Stub\Api;

require_once __DIR__ . '/../../../Stub/Api/Object.php';

use atoum;
use GuzzleHttp;
use ild78;
use mock;

class Object extends atoum
{
    public function testPopulate()
    {
        $this
            ->given($config = ild78\Api\Config::init(uniqid()))
            ->and($id = uniqid())
            ->and($created = time())

            ->and($string1 = $this->stringBetween(10, 20))
            ->and($string2 = $this->stringAtLeast(10))
            ->and($string3 = $this->stringLessThan(20))
            ->and($string4 = $this->stringWithFixedSize(5))

            ->and($integer1 = $this->integerBetween(10, 20))
            ->and($integer2 = $this->integerAtLeast(10))
            ->and($integer3 = $this->integerLessThan(10))

            ->and($restricted1 = $this->stringAtLeast(10))

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
                ->assert('Populate working normally')
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

                ->assert('Update a property allow new request')
                    ->if($config->setHttpClient($mock))
                    ->and($this->newTestedInstance($id))
                    ->and($this->testedInstance->populate())
                    ->and($this->testedInstance->setString1(uniqid()))
                    ->and($this->testedInstance->populate())
                    ->then
                        ->mock($mock)
                            ->call('request')
                                ->twice

                ->assert('Save block populate')
                    ->if($config->setHttpClient($mock))
                    ->and($this->newTestedInstance($id))
                    ->and($this->testedInstance->setString1(uniqid()))
                    ->and($this->testedInstance->save())
                    ->and($this->testedInstance->populate())
                    ->then
                        ->mock($mock)
                            ->call('request')
                                ->withAtLeastArguments(['POST']) // Save action
                                    ->once

                            ->call('request')
                                ->withAtLeastArguments(['GET'])
                                    ->never
        ;
    }

    public function testUnknownProperty()
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
     * @dataProvider validDataProvider
     */
    public function testGetterAndSetter($property, $value)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert(sprintf('Test with %s with value "%s" (%d chars)', $property, $value, strlen((string) $value)))
                    ->variable($this->testedInstance->dataModelGetter($property))
                        ->isNull

                    ->object($this->testedInstance->dataModelSetter($property, $value))
                        ->isTestedInstance

                    ->variable($this->testedInstance->dataModelGetter($property))
                        ->isIdenticalTo($value)
        ;
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalidData($property, $value, $class, $message)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert(sprintf('$%s = "%s" (%d chars) => %s', $property, $value, strlen((string) $value), $message))
                    ->exception(function () use ($property, $value) {
                        $this->testedInstance->dataModelSetter($property, $value);
                    })
                        ->isInstanceOf($class)
                        ->message
                            ->isIdenticalTo($message)
        ;
    }

    public function integerBetween($min, $max)
    {
        return rand($min, $max);
    }

    public function integerAtLeast($min)
    {
        return $this->integerBetween($min, 1000);
    }

    public function integerLessThan($max)
    {
        return $this->integerBetween(0, $max);
    }

    public function stringWithFixedSize($length)
    {
        return substr(md5(uniqid()), 0, $length);
    }

    public function stringBetween($min, $max)
    {
        return $this->stringWithFixedSIze($this->integerBetween($min, $max));
    }

    public function stringAtLeast($min)
    {
        return $this->stringWithFixedSIze($this->integerAtLeast($min));
    }

    public function stringLessThan($max)
    {
        return $this->stringWithFixedSIze($this->integerLessThan($max));
    }

    public function validDataProvider()
    {
        $datas = [];

        // We will make 3 data of each
        for ($idx = 0; $idx < 3; $idx++) {
            // string 1, between 10 and 20
            $datas[] = [
                'string1',
                $this->stringBetween(10, 20),
            ];

            // string 2, at least 10
            $datas[] = [
                'string2',
                $this->stringAtLeast(10),
            ];

            // string 3, max 20
            $datas[] = [
                'string3',
                $this->stringLessThan(20),
            ];

            // integer 1, between 10 and 20
            $datas[] = [
                'integer1',
                $this->integerBetween(10, 20),
            ];

            // integer 2, at least 10
            $datas[] = [
                'integer2',
                $this->integerAtLeast(10),
            ];

            // integer 3, max 20
            $datas[] = [
                'integer3',
                $this->integerLessThan(10),
            ];
        }

        // Fixed sizes

        // string 4, exactly 5
        $datas[] = [
            'string4',
            $this->stringWithFixedSize(5),
        ];

        // string 1, between 10 and 20
        $datas[] = [
            'string1',
            $this->stringWithFixedSize(10),
        ];

        // string 1, between 10 and 20
        $datas[] = [
            'string1',
            $this->stringWithFixedSize(20),
        ];

        // integer 1, between 10 and 20
        $datas[] = [
            'integer1',
            10,
        ];

        // integer 1, between 10 and 20
        $datas[] = [
            'integer1',
            20,
        ];

        // integer 1, between 10 and 20
        $datas[] = [
            'object1',
            new ild78\Card,
        ];

        return $datas;
    }

    public function invalidDataProvider()
    {
        $datas = [];

        // We will make 3 data of each
        for ($idx = 0; $idx < 3; $idx++) {
            // string 1, between 10 and 20
            $datas[] = [
                'string1',
                $this->stringAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string1 must be between 10 and 20 characters.',
            ];

            // string 1, between 10 and 20
            $datas[] = [
                'string1',
                $this->stringLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string1 must be between 10 and 20 characters.',
            ];

            // string 2, at least 10
            $datas[] = [
                'string2',
                $this->stringLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string2 must be at least 10 characters.',
            ];

            // string 3, max 20
            $datas[] = [
                'string3',
                $this->stringAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string3 must have less than 20 characters.',
            ];

            // string 4, exactly 5
            $datas[] = [
                'string4',
                $this->stringAtLeast(6),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string4 must have 5 characters.',
            ];

            // string 4, exactly 5
            $datas[] = [
                'string4',
                $this->stringLessThan(4),
                ild78\Exceptions\InvalidArgumentException::class,
                'A valid string4 must have 5 characters.',
            ];

            // integer 1, between 10 and 20
            $datas[] = [
                'integer1',
                $this->integerLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer1 must be greater than or equal to 10 and be less than or equal to 20.',
            ];

            // integer 1, between 10 and 20
            $datas[] = [
                'integer1',
                $this->integerAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer1 must be greater than or equal to 10 and be less than or equal to 20.',
            ];

            // integer 2, at least 10
            $datas[] = [
                'integer2',
                $this->integerLessThan(9),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer2 must be greater than or equal to 10.',
            ];

            // integer 3, max 20
            $datas[] = [
                'integer3',
                $this->integerAtLeast(21),
                ild78\Exceptions\InvalidArgumentException::class,
                'Integer3 must be less than or equal to 20.',
            ];
        }

        // string 1 with integer
        $datas[] = [
            'string1',
            $this->integerBetween(1, 10),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "integer" expected "string".',
        ];

        // integer 1 with string
        $datas[] = [
            'integer1',
            $this->stringBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "string" expected "integer".',
        ];

        // object 1 with string
        $datas[] = [
            'object1',
            $this->stringBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'Type mismatch, given "string" expected "ild78\Card".',
        ];

        // restricted
        $datas[] = [
            'restricted1',
            $this->stringBetween(10, 20),
            ild78\Exceptions\InvalidArgumentException::class,
            'You are not allowed to modify "restricted1".',
        ];

        return $datas;
    }
}
