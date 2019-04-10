<?php

namespace ild78\Stub\Api;

use ild78;

class StubObject extends ild78\Api\AbstractObject
{
    protected $endpoint = 'objects'; // invalid but must be not empty

    protected $dataModel = [
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
        'object1' => [
            'type' => ild78\Card::class,
        ],
        'object2' => [
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
    public function forceRestricted1(string $value) : self
    {
        $this->dataModel['restricted1']['value'] = $value;

        return $this;
    }

    public function testOnlySetId(string $id) : self
    {
        $this->id = $id;

        return $this;
    }

    public function testOnlyGetPopulated() : bool
    {
        return $this->populated;
    }

    public function testOnlySetModified(bool $modified) : self
    {
        $this->modified = $modified;

        return $this;
    }

    public function testOnlySetPopulated(bool $populated) : self
    {
        $this->populated = $populated;

        return $this;
    }
}
