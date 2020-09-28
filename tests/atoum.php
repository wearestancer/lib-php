<?php

namespace ild78\Tests;

use ild78;
use Ramsey\Uuid\Uuid;

class atoum extends \atoum\test
{
    public function __construct(
        adapter $adapter = null,
        annotations\extractor $annotationExtractor = null,
        asserter\generator $asserterGenerator = null,
        test\assertion\manager $assertionManager = null,
        \closure $reflectionClassFactory = null
    ) {
        parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);

        $this->getAsserterGenerator()->addNamespace('ild78\Tests\asserters');
    }

    public function beforeTestMethod($method)
    {
        if ($method !== 'testGetGlobal_SetGlobal') {
            ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]);
        }
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

        return bin2hex(random_bytes($len / 2));
    }

    public function getUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}
