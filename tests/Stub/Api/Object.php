<?php

namespace ild78\Stub\Api;

use ild78;

class Object extends ild78\Api\Object
{
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
    ];

    // Test only method
    public function forceRestricted1(string $value) : self
    {
        $this->dataModel['restricted1']['value'] = $value;

        return $this;
    }
}
