<?php

namespace Stancer\Tests\Provider;

trait Strings
{
    public function caseStringDataProvider()
    {
        // $camel, $snake

        $data = [];

        $data[] = ['camelCase', 'camel_case'];
        $data[] = ['snakeCase', 'snake_case'];

        $gen = function () {
            $str = '';

            while (strlen($str) < 10) {
                $str .= chr(rand(97, 122));
            }

            return $str;
        };

        $tmp1 = $gen();
        $tmp2 = $gen();

        $data[] = [
            $tmp1 . ucfirst($tmp2),
            $tmp1 . '_' . $tmp2,
        ];

        shuffle($data);

        return $data;
    }
}
