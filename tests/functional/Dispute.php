<?php

namespace ild78\Tests\functional;

use ild78;
use ild78\Dispute as testedClass;

/**
 * @namespace \Tests\functional
 */
class Dispute extends TestCase
{
    use ild78\Tests\Provider\Currencies;

    public function testList()
    {
        $this
            ->given($currency = $this->currencyDataProvider(true))

            ->if($payment = new ild78\Payment)
            ->and($payment->setAmount($amount = rand(50, 10000)))
            ->and($payment->setDescription(sprintf('Automatic test for disputes list, %.02f %s', $amount / 100, $currency)))
            ->and($payment->setCurrency($currency))
            ->and($payment->setCard($card = new ild78\Card))
            ->and($card->setNumber($this->getDisputedCardNumber()))
            ->and($card->setExpirationMonth(rand(1, 12)))
            ->and($card->setExpirationYear(date('Y') + rand(1, 5)))
            ->and($card->setCvc((string) rand(100, 999)))
            ->and($payment->setCustomer($customer = new ild78\Customer))
            ->and($customer->setName('John Doe'))
            ->and($customer->setEMail('john.doe@example.com'))
            ->and($payment->send())

            ->if($this->newTestedInstance) // Needed to use "isInstanceOfTestedClass" asserter
            ->then
                ->generator(testedClass::list(['created' => time() - 10]))
                    ->yields
                        ->object
                            ->isInstanceOfTestedClass

                    ->yields
                        ->variable
                            ->isNull
        ;
    }
}
