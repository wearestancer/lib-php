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
    ];
}
