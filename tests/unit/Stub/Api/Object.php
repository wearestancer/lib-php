<?php

namespace ild78\tests\unit\Stub\Api;

require_once __DIR__ . '/../../../Stub/Api/Object.php';

use atoum;
use ild78;

class Object extends atoum
{
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
