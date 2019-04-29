<?php

namespace ild78\tests\functional;

use ild78;
use ild78\Refund as testedClass;

/**
 * @namespace \tests\functional
 */
class Refund extends TestCase
{
    /**
     * @dataProvider currencyDataProvider
     */
    public function testRefund($currency)
    {
        $this
            ->given($payment = new ild78\Payment)
            ->and($payment->setAmount($amount = rand(50, 10000)))
            ->and($payment->setDescription(sprintf('Refund test, %.02f %s', $amount / 100, $currency)))
            ->and($payment->setCurrency($currency))
            ->and($payment->setCard($card = new ild78\Card))
            ->and($card->setNumber($this->getValidCardNumber()))
            ->and($card->setExpirationMonth(rand(1, 12)))
            ->and($card->setExpirationYear(date('Y') + rand(1, 5)))
            ->and($card->setCvc((string) rand(100, 999)))
            ->and($payment->setCustomer($customer = new ild78\Customer))
            ->and($customer->setName('John Doe'))
            ->and($customer->setEMail('john.doe@example.com'))
            ->and($payment->save())
            ->if($this->newTestedInstance) // Needed to use "isInstanceOfTestedClass" asserter
            ->then
                ->object($payment->refund())
                    ->isIdenticalTo($payment)

                ->array($refunds = $payment->getRefunds())
                    ->hasSize(1)

                ->object($refund = $refunds[0])
                    ->isInstanceOfTestedClass

                ->integer($refund->getAmount())
                    ->isEqualTo($amount)

                ->string($refund->getCurrency())
                    ->isEqualTo(strtolower($currency))

                ->object($refund->getPayment())
                    ->isIdenticalTo($payment)
        ;
    }
}
