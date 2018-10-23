<?php

namespace ild78\tests\functional;

use atoum;
use ild78;

/**
 * @namespace \tests\functional
 */
class Payment extends atoum
{
    protected $config;

    public function beforeTestMethod($testMethod)
    {
        $env = [
            'ILD_API_HOST' => '',
            'ILD_API_KEY' => '',
        ];

        foreach ($env as $key => &$value) {
            $value = getenv($key);

            if (!$value) {
                $this->skip('Missing env ' . $key);
            }
        }

        if (!$this->config) {
            $this->config = ild78\Api\Config::init($env['ILD_API_KEY']);
        }

        $this->config->setHost($env['ILD_API_HOST']);
    }

    public function currencyDataProvider()
    {
        return [
            'EUR',
            'USD',
            'GBP',
        ];
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

    public function testBadCredential()
    {
        $this
            ->given($this->config->setKey(uniqid()))
            ->and($this->newTestedInstance(uniqid()))
            ->then
                ->exception(function () {
                    $this->testedInstance->getCard();
                })
                    ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
        ;
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testPay($currency)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setAmount($amount = rand(50, 10000)))
                    ->isTestedInstance

                ->object($this->testedInstance->setDescription(sprintf('Automatic test, %.02f %s', $amount / 100, $currency)))
                    ->isTestedInstance

                ->object($this->testedInstance->setCurrency($currency))
                    ->isTestedInstance

                ->object($this->testedInstance->setCard($card = new ild78\Card))
                    ->isTestedInstance

                ->object($card->setNumber($this->getValidCardNumber()))
                    ->isInstanceOf(ild78\Card::class)

                ->object($card->setExpirationMonth(rand(1, 12)))
                    ->isInstanceOf(ild78\Card::class)

                ->object($card->setExpirationYear(date('Y') + rand(1, 5)))
                    ->isInstanceOf(ild78\Card::class)

                ->object($card->setCvc((string) rand(100, 999)))
                    ->isInstanceOf(ild78\Card::class)

                ->object($this->testedInstance->setCustomer($customer = new ild78\Customer))
                    ->isTestedInstance

                ->object($customer->setName('John Doe'))
                    ->isInstanceOf(ild78\Customer::class)

                ->object($customer->setEMail('john.doe@example.com'))
                    ->isInstanceOf(ild78\Customer::class)

                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->string($this->testedInstance->getId())
        ;
    }
}
