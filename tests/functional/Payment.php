<?php

namespace ild78\tests\functional;

use ild78;

/**
 * @namespace \tests\functional
 */
class Payment extends TestCase
{
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
