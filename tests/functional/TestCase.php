<?php

namespace ild78\Tests\functional;

use ild78;

class TestCase extends ild78\Tests\atoum
{
    protected $config;

    public function beforeTestMethod($testMethod)
    {
        $env = [
            'API_HOST' => '',
            'API_KEY' => '',
        ];

        foreach ($env as $key => &$value) {
            $value = getenv($key);

            if (!$value) {
                $this->skip('Missing env ' . $key);
            }
        }

        if (!$this->config) {
            $this->config = ild78\Config::setGlobal(new ild78\Config([$env['API_KEY']]));
        }

        $this->config->setHost($env['API_HOST']);
    }

    public function getRandomNumber()
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

    public function getRandomString($min, $max = null)
    {
        if (!$max) {
            $max = $min;
        }

        $len = random_int($min, $max);

        return bin2hex(random_bytes($len / 2));
    }

    public function getValidCardNumber()
    {
        $cards = [
            '4242424242424242',
            '5555555555554444',
            '4000000760000002',
            '4000001240000000',
            '4000004840000008',
            '4000000400000008',
            '4000000560000004',
            '4000002080000001',
            '4000002460000001',
            '4000002500000003',
            '4000002760000016',
            '4000003720000005',
            '4000003800000008',
            '4000004420000006',
            '4000005280000002',
            '4000005780000007',
            '4000006200000007',
            '4000006430000009',
            '4000007240000007',
            '4000007520000008',
            '4000007560000009',
            '4000008260000000',
            '4000000360000006',
            '4000001560000002',
            '4000003440000004',
            '4000003920000003',
            '3530111333300000',
            '4000005540000008',
            '4000007020000003',
        ];

        shuffle($cards);

        return array_shift($cards);
    }
}
