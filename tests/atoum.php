<?php

namespace Stancer\Tests;

use atoum\atoum as base;
use Stancer;
use Faker;

class atoum extends base\test
{
    public function __construct(
        base\adapter $adapter = null,
        base\annotations\extractor $annotationExtractor = null,
        base\asserter\generator $asserterGenerator = null,
        base\test\assertion\manager $assertionManager = null,
        \closure $reflectionClassFactory = null
    ) {
        parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);

        $this->getAsserterGenerator()->addNamespace('Stancer\Tests\asserters');
    }

    public function beforeTestMethod($method)
    {
        if ($method !== 'testGetGlobal_SetGlobal') {
            Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]);
        }
    }

    public function fake(): Faker\Generator
    {
        return Faker\Factory::create();
    }

    public function getRandomDate(int $min, int $max = null): string
    {
        if (!$max) {
            $max = date('Y');
        }

        $year = random_int($min, $max);
        $month = random_int(1, 12);

        $dMax = 31;

        if ($month == 2) {
            $dMax = 27;
        } else if (in_array($month, [4, 6, 9, 11])) {
            $dMax = 30;
        }

        $day = random_int(1, $dMax);

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    public function getRandomNumber(): string
    {
        // Simulate a french mobile phone number
        $first = rand(0, 1) + 6;
        $loop = 4;

        $number = '+33' . $first;

        if ($first === 7) {
            $number .= str_pad(rand(30, 99), 2, '0');
            $loop--;
        }

        for ($idx = 0; $idx < $loop; $idx++) {
            $number .= str_pad(rand(0, 99), 2, '0');
        }

        return $number;
    }

    public function getRandomString(int $min, int $max = null): string
    {
        if (!$max) {
            $max = $min;
        }

        $len = random_int($min, $max);

        return bin2hex(random_bytes(floor($len / 2)));
    }

    public function getUuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
