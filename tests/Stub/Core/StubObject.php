<?php

namespace Stancer\Stub\Core;

use DateTimeInterface;
use Stancer;

class StubObject extends Stancer\Core\AbstractObject
{
    use Stancer\Stub\TestMethodTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final const ENDPOINT = 'objects'; // invalid but must be not empty

    protected array $dataModel = [
        'date1' => [
            'type' => DateTimeInterface::class,
        ],
        'date2' => [
            'list' => true,
            'type' => DateTimeInterface::class,
        ],
        'date3' => [
            'format' => Stancer\Core\Type\Helper::DATE_ONLY,
            'type' => DateTimeInterface::class,
        ],
        'date4' => [
            'format' => Stancer\Core\Type\Helper::DATE_ONLY,
            'list' => true,
            'type' => DateTimeInterface::class,
        ],
        'enum' => [
            'type' => Stancer\Stub\FakeStatus::class,
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
        'object1' => [
            'type' => Stancer\Card::class,
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
            'type' => Stancer\Card::class,
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

    public function testOnlyDataModelAdder(string $property, $value): self
    {
        return $this->dataModelAdder($property, $value);
    }

    public function testOnlyDataModelGetter(string $property, bool $autoPopulate = true)
    {
        return $this->dataModelGetter($property, $autoPopulate);
    }

    public function testOnlyDataModelSetter(string $property, $value): self
    {
        return $this->dataModelSetter($property, $value);
    }
}
