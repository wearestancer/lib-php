<?php

namespace Stancer\Tests\functional;

use Stancer;
use Stancer\Dispute as testedClass;

/**
 * @tags AbstractObject Card Customer Dispute
 *
 * @namespace \Tests\functional
 *
 * @internal
 */
class Dispute extends TestCase
{
    use Stancer\Tests\Provider\Currencies;

    public function testList()
    {
        $currency = $this->cardCurrencyDataProvider(true);
        $payment = new Stancer\Payment();
        $payment->setAmount($amount = $this->getRandomAmount());
        $payment->setDescription(sprintf('Automatic test for disputes list, %.02f %s', $amount / 100, $currency));
        $payment->setCurrency($currency);
        $payment->setCard($card = new Stancer\Card());
        $card->setNumber($this->getDisputedCardNumber());
        $card->setExpirationMonth($this->getRandomMonth());
        $card->setExpirationYear($this->getRandomExpYear());
        $card->setCvc($this->getRandomCvc());
        $payment->setCustomer($customer = new Stancer\Customer());
        $customer->setName('John Doe');
        $customer->setEmail('john.doe' . $this->getRandomString(10) . '@example.com');
        $payment->send();

        $this
            ->if($this->newTestedInstance) // Needed to use "isInstanceOfTestedClass" asserter
            ->then
                ->generator(testedClass::list(['created' => (time() - 2600000)])) //aproximately one month of results
                    ->yields
                        ->object
                            ->isInstanceOfTestedClass

                    ->yields
                        ->variable
                            ->isNotNull
        ;
    }

    /**
     * @dataProvider versionDataProvider
     */
    public function testGetDispute(Stancer\Enum\ApiVersion $version)
    {
        $this
            ->given(Stancer\Config::getGlobal()->setVersion($version))
            ->given($this->newTestedInstance('dspt_a4dIMSi7PBBoGiu2BocagB2f'))
            ->then

            ->string($this->testedInstance->getPayment()->getId())
                ->isIdenticalTo('paym_0yG3rJ6rBT6u9Kc5HUyRAzsP')

            ->string($this->testedInstance->getResponse())
                ->isIdenticalTo('AC04')

            ->integer($this->testedInstance->getAmount())
                ->isIdenticalTo(300)
        ;
    }
}
