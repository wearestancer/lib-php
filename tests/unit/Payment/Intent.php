<?php

namespace Stancer\tests\unit\Payment;

use Stancer;

class Intent extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Currencies;

    public function testAmount()
    {
        $this
            ->given($amount = rand(50, 10000))

            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->testedInstance->getAmount())
                        ->isNull

                ->assert('Should throw an exception if under 50')
                    ->exception(function () {
                        $this->testedInstance->setAmount(rand(1, 49));
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->assert('Update value')
                    ->object($this->testedInstance->setAmount($amount))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getAmount())
                        ->isIdenticalTo($amount)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_amount())->isNull
                    ->object($this->testedInstance->set_amount($amount))->isTestedInstance
                    ->integer($this->testedInstance->get_amount())->isIdenticalTo($amount)

                    ->variable($this->newTestedInstance->amount)->isNull
                    ->variable($this->testedInstance->amount = $amount)
                    ->integer($this->testedInstance->amount)->isIdenticalTo($amount)
        ;
    }

    public function testCapture()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->testedInstance->getCapture())
                        ->isNull


                ->assert('Update value')
                    ->object($this->testedInstance->setCapture(true))
                        ->isTestedInstance

                    ->boolean($this->testedInstance->getCapture())
                        ->isTrue

                    ->object($this->testedInstance->setCapture(false))
                        ->isTestedInstance

                    ->boolean($this->testedInstance->getCapture())
                        ->isFalse

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_capture())->isNull
                    ->object($this->testedInstance->set_capture(true))->isTestedInstance
                    ->boolean($this->testedInstance->get_capture())->isTrue
                    ->object($this->testedInstance->set_capture(false))->isTestedInstance
                    ->boolean($this->testedInstance->get_capture())->isFalse

                    ->variable($this->newTestedInstance->capture)->isNull
                    ->variable($this->testedInstance->capture = false)
                    ->boolean($this->testedInstance->capture)->isFalse
                    ->variable($this->testedInstance->capture = true)
                    ->boolean($this->testedInstance->capture)->isTrue
        ;
    }

    public function testCard()
    {
        $this
            ->if($card = new Stancer\Card)
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getCard())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setCard($card))
                        ->isTestedInstance

                    ->object($this->testedInstance->getCard())
                        ->isIdenticalTo($card)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_card())->isNull
                    ->object($this->testedInstance->set_card($card))->isTestedInstance
                    ->object($this->testedInstance->get_card())->isIdenticalTo($card)

                    ->variable($this->newTestedInstance->card)->isNull
                    ->variable($this->testedInstance->card = $card)
                    ->object($this->testedInstance->card)->isIdenticalTo($card)
        ;
    }

    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
        ;
    }

    /**
     * @dataProvider cardCurrencyDataProvider
     */
    public function testCurrency_card($currency)
    {
        $this
            ->given($badCurrency = uniqid())

            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getCurrency())
                        ->isNull

                ->assert('Throw an exception if currency is unknown')
                    ->exception(function () use ($badCurrency) {
                        $this->testedInstance->setCurrency($badCurrency);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCurrencyException::class)
                        ->message
                            ->contains($badCurrency)
                            ->contains('is not a valid currency, please use one of the following')

                ->assert('Update value')
                    ->object($this->testedInstance->setCurrency($currency))
                        ->isTestedInstance

                    ->object($this->testedInstance->getCurrency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_currency())->isNull
                    ->object($this->testedInstance->set_currency($currency))->isTestedInstance
                    ->object($this->testedInstance->get_currency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                    ->variable($this->newTestedInstance->currency)->isNull
                    ->variable($this->testedInstance->currency = $currency)
                    ->object($this->testedInstance->currency)
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))
        ;
    }

    /**
     * @dataProvider sepaCurrencyDataProvider
     */
    public function testCurrency_sepa($currency)
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getCurrency())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setCurrency($currency))
                        ->isTestedInstance

                    ->object($this->testedInstance->getCurrency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_currency())->isNull
                    ->object($this->testedInstance->set_currency($currency))->isTestedInstance
                    ->object($this->testedInstance->get_currency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                    ->variable($this->newTestedInstance->currency)->isNull
                    ->variable($this->testedInstance->currency = $currency)
                    ->object($this->testedInstance->currency)
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))
        ;

        foreach ($this->cardCurrencyDataProvider() as $badCurrency) {
            if ($currency === $badCurrency) {
                continue;
            }

            $this
                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setMethodsAllowed(['sepa']))
                ->then
                    ->assert('Throw an exception for invalid currency')
                        ->exception(function () use ($badCurrency) {
                            $this->testedInstance->setCurrency($badCurrency);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidCurrencyException::class)
                            ->message
                                ->isIdenticalTo(sprintf('You can not use "%s" currency with "sepa" method.', strtolower($badCurrency)))
            ;
        }
    }

    public function testCustomer()
    {
        $this
            ->if($customer = new Stancer\Customer)
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getCustomer())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setCustomer($customer))
                        ->isTestedInstance

                    ->object($this->testedInstance->getCustomer())
                        ->isIdenticalTo($customer)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_customer())->isNull
                    ->object($this->testedInstance->set_customer($customer))->isTestedInstance
                    ->object($this->testedInstance->get_customer())->isIdenticalTo($customer)

                    ->variable($this->newTestedInstance->customer)->isNull
                    ->variable($this->testedInstance->customer = $customer)
                    ->object($this->testedInstance->customer)->isIdenticalTo($customer)
        ;
    }

    public function testDescription()
    {
        $this
            ->if($description = $this->getRandomString(3, 64))
            ->and($tooShort = $this->getRandomString(2))
            ->and($tooLong = $this->getRandomString(65, 100))
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getDescription())
                        ->isNull

                ->assert('Should throw an exception if the description is too small')
                    ->exception(function () use ($tooShort) {
                        $this->testedInstance->setDescription($tooShort);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('A valid description must be between 3 and 64 characters.')

                ->assert('Should throw an exception if the description is too long')
                    ->exception(function () use ($tooLong) {
                        $this->testedInstance->setDescription($tooLong);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('A valid description must be between 3 and 64 characters.')

                ->assert('Update value')
                    ->object($this->testedInstance->setDescription($description))
                        ->isTestedInstance

                    ->string($this->testedInstance->getDescription())
                        ->isIdenticalTo($description)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_description())->isNull
                    ->object($this->testedInstance->set_description($description))->isTestedInstance
                    ->string($this->testedInstance->get_description())->isIdenticalTo($description)

                    ->variable($this->newTestedInstance->description)->isNull
                    ->variable($this->testedInstance->description = $description)
                    ->string($this->testedInstance->description)->isIdenticalTo($description)
        ;
    }

    public function testEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('payment_intents')

                ->string($this->testedInstance->endpoint)
                    ->isIdenticalTo('payment_intents')
        ;
    }

    /**
     * @dataProvider cardCurrencyDataProvider
     */
    public function testMethodsAllowed($currency)
    {
        $this
            ->given($methods = ['card', 'sepa'])

            ->assert('Should return an empty array as default')
                ->array($this->newTestedInstance->getMethodsAllowed())
                    ->isEmpty

            ->assert('Should allow array of strings')
                ->object($this->newTestedInstance->setMethodsAllowed($methods))
                    ->isTestedInstance

                ->array($this->testedInstance->getMethodsAllowed())
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                        ->isIdenticalTo(Stancer\Payment\MethodsAllowed::CARD)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                        ->isIdenticalTo(Stancer\Payment\MethodsAllowed::SEPA)

            ->assert('Should only allow known methods')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setMethodsAllowed([$value]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isEqualTo('"' . $value . '" is not a valid method, please use one of the following: card, sepa')

            ->assert('Should allow to add methods')
                ->given($this->newTestedInstance)
                ->then
                    ->object($this->testedInstance->addMethodsAllowed($methods[0]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getMethodsAllowed())
                        ->hasSize(1)
                        ->object[0]
                            ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                            ->isIdenticalTo(Stancer\Payment\MethodsAllowed::tryFrom($methods[0]))

            ->assert('Aliases')
                ->array($this->newTestedInstance->get_methods_allowed())->isEmpty
                ->object($this->testedInstance->add_methods_allowed($methods[0]))->isTestedInstance
                ->array($this->testedInstance->get_methods_allowed())->hasSize(1)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                        ->isIdenticalTo(Stancer\Payment\MethodsAllowed::tryFrom($methods[0]))
                ->object($this->newTestedInstance->set_methods_allowed($methods))->isTestedInstance
                ->array($this->testedInstance->get_methods_allowed())
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)

                ->array($this->newTestedInstance->methodsAllowed)->isEmpty
                ->variable($this->newTestedInstance->methodsAllowed = $methods)
                ->array($this->testedInstance->methodsAllowed)
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)

                ->array($this->newTestedInstance->methods_allowed)->isEmpty
                ->variable($this->newTestedInstance->methods_allowed = $methods)
                ->array($this->testedInstance->methods_allowed)
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
        ;

        $lower = strtolower($currency);

        if ($lower !== 'eur') {
            $this
                ->assert('Currency can be refused when using SEPA')
                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->setCurrency($currency))
                    ->then
                        ->exception(function () {
                            $this->testedinstance->addMethodsAllowed('sepa');
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                            ->message
                                ->isIdenticalTo(sprintf('You can not use "%s" method with "%s" currency.', 'sepa', $lower))

                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->setCurrency($currency))
                    ->then
                        ->exception(function () use ($methods) {
                            $this->testedinstance->setMethodsAllowed($methods);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                            ->message
                                ->isIdenticalTo(sprintf('You can not use "%s" method with "%s" currency.', 'sepa', $lower))
            ;
        }
    }

    public function testOrderId()
    {
        $this
            ->if($tooShort = '')
            ->and($tooLong = $this->getRandomString(37, 40))
            ->and($orderId = $this->getRandomString(1, 36))

            ->then
                ->assert('Must have one character')
                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->setOrderId($tooShort);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                        ->message
                            ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->assert('Must have less than 36 characters')
                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->setOrderId($tooLong);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                        ->message
                            ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->assert('With a valid order ID')
                    ->object($this->newTestedInstance->setOrderId($orderId))
                        ->isTestedInstance

                    ->string($this->testedInstance->getOrderId())
                        ->isIdenticalTo($orderId)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('order_id')
                        ->string['order_id']
                            ->isEqualTo($orderId)

                ->assert('Aliases')
                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->set_order_id($tooShort);
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->set_order_id($tooLong);
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->object($this->newTestedInstance->set_order_id($orderId))->isTestedInstance
                    ->string($this->testedInstance->get_order_id())->isIdenticalTo($orderId)

                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->orderId = $tooShort;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->orderId = $tooLong;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->variable($this->newTestedInstance->orderId = $orderId)
                    ->string($this->testedInstance->orderId)->isIdenticalTo($orderId)

                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->order_id = $tooShort;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->order_id = $tooLong;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->variable($this->newTestedInstance->order_id = $orderId)
                    ->string($this->testedInstance->order_id)->isIdenticalTo($orderId)
            ;
    }

    public function testPayment()
    {
        $this
            ->if($payment = new Stancer\Payment)
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getPayment())
                        ->isNull

                ->assert('Should throw an exception if setted')
                    ->exception(function () use ($payment) {
                        $this->testedInstance->setPayment($payment);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "payment".')

                ->assert('Get value')
                    ->if($this->testedInstance->hydrate(['payment' => $payment]))
                    ->then
                        ->object($this->testedInstance->getPayment())
                            ->isIdenticalTo($payment)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_payment())->isNull
                    ->variable($this->testedInstance->hydrate(['payment' => $payment]))
                    ->object($this->testedInstance->get_payment())->isIdenticalTo($payment)

                    ->variable($this->newTestedInstance->payment)->isNull
                    ->variable($this->testedInstance->hydrate(['payment' => $payment]))
                    ->object($this->testedInstance->payment)->isIdenticalTo($payment)
        ;
    }

    public function testReturnUrl()
    {
        $this
            ->given($https = 'https://www.example.org/?' . uniqid())
            ->and($http = 'http://www.example.org/?' . uniqid())

            ->if($this->newTestedInstance)
            ->then
                ->assert('Default value')
                    ->variable($this->testedInstance->getReturnUrl())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setReturnUrl($https))
                        ->isTestedInstance

                    ->string($this->testedInstance->getReturnUrl())
                        ->isIdenticalTo($https)

                ->assert('Does not allow HTTP URL')
                    ->exception(function () use ($http) {
                        $this->testedInstance->setReturnUrl($http);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
                        ->message
                            ->isIdenticalTo('You must provide an HTTPS URL.')

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_return_url())->isNull
                    ->object($this->testedInstance->set_return_url($https))->isTestedInstance
                    ->string($this->testedInstance->get_return_url())->isIdenticalTo($https)

                    ->variable($this->newTestedInstance->returnUrl)->isNull
                    ->variable($this->testedInstance->returnUrl = $https)
                    ->string($this->testedInstance->returnUrl)->isIdenticalTo($https)

                    ->variable($this->newTestedInstance->return_url)->isNull
                    ->variable($this->testedInstance->return_url = $https)
                    ->string($this->testedInstance->return_url)->isIdenticalTo($https)

        ;
    }

    public function testSepa()
    {
        $this
            ->if($sepa = new Stancer\Sepa)
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getSepa())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setSepa($sepa))
                        ->isTestedInstance

                    ->object($this->testedInstance->getSepa())
                        ->isIdenticalTo($sepa)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_sepa())->isNull
                    ->object($this->testedInstance->set_sepa($sepa))->isTestedInstance
                    ->object($this->testedInstance->get_sepa())->isIdenticalTo($sepa)

                    ->variable($this->newTestedInstance->sepa)->isNull
                    ->variable($this->testedInstance->sepa = $sepa)
                    ->object($this->testedInstance->sepa)->isIdenticalTo($sepa)
        ;
    }

    public function testStatus()
    {
        $this
            ->if($status = $this->choose(Stancer\Payment\Intent\Status::cases()))
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getStatus())
                        ->isNull

                ->assert('Should throw an exception if setted')
                    ->exception(function () use ($status) {
                        $this->testedInstance->setStatus($status);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "status".')

                ->assert('Get value')
                    ->if($this->testedInstance->hydrate(['status' => $status]))
                    ->then
                        ->object($this->testedInstance->getStatus())
                            ->isIdenticalTo($status)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_status())->isNull
                    ->object($this->testedInstance->hydrate(['status' => $status]))->isTestedInstance
                    ->object($this->testedInstance->get_status())->isIdenticalTo($status)

                    ->variable($this->newTestedInstance->status)->isNull
                    ->object($this->testedInstance->hydrate(['status' => $status]))->isTestedInstance
                    ->object($this->testedInstance->status)->isIdenticalTo($status)
        ;
    }

    public function testThreeDS()
    {
        $this
            ->if($threeDS = $this->choose(Stancer\ThreeDomainsSecure\Status::cases()))
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getThreeds())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setThreeds($threeDS))
                        ->isTestedInstance

                    ->object($this->testedInstance->getThreeds())
                        ->isIdenticalTo($threeDS)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->getThreeDS())->isNull
                    ->object($this->testedInstance->setThreeDS($threeDS))->isTestedInstance
                    ->object($this->testedInstance->getThreeDS())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get_three_ds())->isNull
                    ->object($this->testedInstance->set_three_ds($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get_three_ds())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->threeDS)->isNull
                    ->variable($this->testedInstance->threeDS = $threeDS)
                    ->object($this->testedInstance->threeDS)->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get_threeds())->isNull
                    ->object($this->testedInstance->set_threeds($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get_threeds())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->threeds)->isNull
                    ->variable($this->testedInstance->threeds = $threeDS)
                    ->object($this->testedInstance->threeds)->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->three_ds)->isNull
                    ->variable($this->testedInstance->three_ds = $threeDS)
                    ->object($this->testedInstance->three_ds)->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get3DS())->isNull
                    ->object($this->testedInstance->set3DS($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get3DS())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get_3ds())->isNull
                    ->object($this->testedInstance->set_3ds($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get_3ds())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->{'3DS'})->isNull
                    ->variable($this->testedInstance->{'3DS'} = $threeDS)
                    ->object($this->testedInstance->{'3DS'})->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->{'3ds'})->isNull
                    ->variable($this->testedInstance->{'3ds'} = $threeDS)
                    ->object($this->testedInstance->{'3ds'})->isIdenticalTo($threeDS)
        ;
    }

    public function testUrl()
    {
        $this
            ->if($url = uniqid())
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getUrl())
                        ->isNull

                ->assert('Should throw an exception if setted')
                    ->exception(function () use ($url) {
                        $this->testedInstance->setUrl($url);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "url".')

                ->assert('Get value')
                    ->if($this->testedInstance->hydrate(['url' => $url]))
                    ->then
                        ->string($this->testedInstance->getUrl())
                            ->isIdenticalTo($url)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_url())->isNull
                    ->variable($this->testedInstance->hydrate(['url' => $url]))
                    ->string($this->testedInstance->get_url())->isIdenticalTo($url)

                    ->variable($this->newTestedInstance->url)->isNull
                    ->variable($this->testedInstance->hydrate(['url' => $url]))
                    ->string($this->testedInstance->url)->isIdenticalTo($url)
        ;
    }
}
