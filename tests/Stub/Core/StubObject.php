<?php

namespace ild78\Stub\Core;

use DateTimeInterface;
use ild78;

class StubObject extends ild78\Core\AbstractObject
{
    use ild78\Stub\TestMethodTrait;

    protected $endpoint = 'objects'; // invalid but must be not empty

    protected $dataModel = [
        'date1' => [
            'type' => DateTimeInterface::class,
        ],
        'date2' => [
            'list' => true,
            'type' => DateTimeInterface::class,
        ],
        'string1' => [
            'required' => true,
            'type' => self::STRING,
            'size' => [
                'min' => 10,
                'max' => 20,
            ],
        ],
        'string2' => [
            'type' => self::STRING,
            'size' => [
                'min' => 10,
            ],
        ],
        'string3' => [
            'type' => self::STRING,
            'size' => [
                'max' => 20,
            ],
        ],
        'string4' => [
            'type' => self::STRING,
            'size' => [
                'fixed' => 5,
            ],
        ],
        'string5' => [
            'allowedValues' => ['foo', 'bar'],
            'type' => self::STRING,
        ],
        'string6' => [
            'allowedValues' => ['foo', 'bar'],
            'list' => true,
            'type' => self::STRING,
        ],
        'string7' => [
            'allowedValues' => ild78\Stub\FakeStatus::class,
            'type' => self::STRING,
        ],
        'integer1' => [
            'required' => true,
            'type' => self::INTEGER,
            'size' => [
                'min' => 10,
                'max' => 20,
            ],
        ],
        'integer2' => [
            'type' => self::INTEGER,
            'size' => [
                'min' => 10,
            ],
        ],
        'integer3' => [
            'type' => self::INTEGER,
            'size' => [
                'max' => 20,
            ],
        ],
        'integer4' => [
            'allowedValues' => [1, 2, 3],
            'type' => self::INTEGER,
        ],
        'integer5' => [
            'allowedValues' => [1, 2, 3],
            'list' => true,
            'type' => self::INTEGER,
        ],
        'integer6' => [
            'allowedValues' => ild78\Stub\FakeOptions::class,
            'type' => self::INTEGER,
        ],
        'object1' => [
            'type' => ild78\Card::class,
        ],
        'object2' => [
            'type' => self::class,
        ],
        'object3' => [
            'exportable' => false,
            'type' => self::class,
        ],
        'restricted1' => [
            'restricted' => true,
            'type' => 'string',
        ],
        'camelCaseProperty' => [
            'type' => self::STRING,
        ],
        'array1' => [
            'list' => true,
            'type' => self::STRING,
        ],
        'array2' => [
            'list' => true,
            'type' => self::INTEGER,
        ],
        'array3' => [
            'list' => true,
            'type' => ild78\Card::class,
        ],
        'array4' => [
            'list' => true,
            'type' => self::class,
        ],
    ];

    // Test only methods
    public function forceRestricted1(string $value): self
    {
        $this->dataModel['restricted1']['value'] = $value;

        return $this;
    }
}
