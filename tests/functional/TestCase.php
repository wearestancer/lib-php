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

    public function getDisputedCardNumber()
    {
        $cards = [
            '4000000000000259',
            '4000000000001976',
            '4000000000005423',
        ];

        shuffle($cards);

        return array_shift($cards);
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

    public function getValidIban()
    {
        $cards = [
            'AT611904300234573201',
            'BE62510007547061',
            'CH2089144321842946678',
            'DE89370400440532013000',
            'EE382200221020145685',
            'ES0700120345030000067890',
            'FI2112345600000785',
            'FR1420041010050500013M02606',
            'GB33BUKB20201555555555',
            'IE29AIBK93115212345678',
            'LT121000011101001000',
            'LU280019400644750000',
            'IT02A0301926102000000490887',
            'NL39RABO0300065264',
            'NO9386011117947',
            'PT50000201231234567890154',
            'SE3550000000054910000003',
        ];

        shuffle($cards);

        return array_shift($cards);
    }
}
