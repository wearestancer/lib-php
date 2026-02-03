<?php

namespace Stancer\tests\unit;

use Stancer;

class Dispute extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Currencies;

    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
        ;
    }

    public function testGetAmount()
    {
        $this
            ->if($amount = rand(50, 10000))
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getAmount())
                        ->isNull

                    ->exception(function () use ($amount) {
                        $this->testedInstance->setAmount($amount);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "amount".')

                    ->if($this->testedInstance->hydrate(['amount' => $amount]))
                    ->then
                        ->integer($this->testedInstance->getAmount())
                            ->isIdenticalTo($amount)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_amount())
                        ->isNull

                    ->exception(function () use ($amount) {
                        $this->testedInstance->set_amount($amount);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "amount".')

                    ->if($this->testedInstance->hydrate(['amount' => $amount]))
                    ->then
                        ->integer($this->testedInstance->get_amount())
                            ->isIdenticalTo($amount)

                ->assert('property')
                    ->variable($this->newTestedInstance->amount)
                        ->isNull

                    ->exception(function () use ($amount) {
                        $this->testedInstance->amount = $amount;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "amount".')

                    ->if($this->testedInstance->hydrate(['amount' => $amount]))
                    ->then
                        ->integer($this->testedInstance->amount)
                            ->isIdenticalTo($amount)
        ;
    }

    /**
     * @dataProvider cardCurrencyDataProvider
     *
     * @param mixed $currency
     */
    public function testGetCurrency($currency)
    {
        $this
            ->assert('camelCase method')
                ->variable($this->newTestedInstance->getCurrency())
                    ->isNull

                ->exception(function () use ($currency) {
                    $this->testedInstance->setCurrency($currency);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "currency".')

                ->if($this->testedInstance->hydrate(['currency' => $currency]))
                ->then
                    ->string($this->testedInstance->getCurrency())
                        ->isIdenticalTo(strtolower($currency))

            ->assert('snake_case method')
                ->variable($this->newTestedInstance->get_currency())
                    ->isNull

                ->exception(function () use ($currency) {
                    $this->testedInstance->set_currency($currency);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "currency".')

                ->if($this->testedInstance->hydrate(['currency' => $currency]))
                ->then
                    ->string($this->testedInstance->get_currency())
                        ->isIdenticalTo(strtolower($currency))

            ->assert('property')
                ->variable($this->newTestedInstance->currency)
                    ->isNull

                ->exception(function () use ($currency) {
                    $this->testedInstance->currency = $currency;
                })
                    ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "currency".')

                ->if($this->testedInstance->hydrate(['currency' => $currency]))
                ->then
                    ->string($this->testedInstance->currency)
                        ->isIdenticalTo(strtolower($currency))
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('disputes')
        ;
    }

    public function testGetOrderId()
    {
        $this
            ->if($orderId = uniqid())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getOrderId())
                        ->isNull

                    ->exception(function () use ($orderId) {
                        $this->testedInstance->setOrderId($orderId);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "orderId".')

                    ->if($this->testedInstance->hydrate(['orderId' => $orderId]))
                    ->then
                        ->string($this->testedInstance->getOrderId())
                            ->isIdenticalTo($orderId)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_order_id())
                        ->isNull

                    ->exception(function () use ($orderId) {
                        $this->testedInstance->set_order_id($orderId);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "orderId".')

                    ->if($this->testedInstance->hydrate(['orderId' => $orderId]))
                    ->then
                        ->string($this->testedInstance->get_order_id())
                            ->isIdenticalTo($orderId)

                ->assert('camelCase property')
                    ->variable($this->newTestedInstance->orderId)
                        ->isNull

                    ->exception(function () use ($orderId) {
                        $this->testedInstance->orderId = $orderId;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "orderId".')

                    ->if($this->testedInstance->hydrate(['orderId' => $orderId]))
                    ->then
                        ->string($this->testedInstance->orderId)
                            ->isIdenticalTo($orderId)

                ->assert('snake_case property')
                    ->variable($this->newTestedInstance->order_id)
                        ->isNull

                    ->exception(function () use ($orderId) {
                        $this->testedInstance->order_id = $orderId;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "orderId".')

                    ->if($this->testedInstance->hydrate(['orderId' => $orderId]))
                    ->then
                        ->string($this->testedInstance->order_id)
                            ->isIdenticalTo($orderId)
        ;
    }

    public function testGetPayment()
    {
        $this
            ->if($payment = new Stancer\Payment())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getPayment())
                        ->isNull

                    ->exception(function () use ($payment) {
                        $this->testedInstance->setPayment($payment);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "payment".')

                    ->if($this->testedInstance->hydrate(['payment' => $payment]))
                    ->then
                        ->object($this->testedInstance->getPayment())
                            ->isIdenticalTo($payment)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_payment())
                        ->isNull

                    ->exception(function () use ($payment) {
                        $this->testedInstance->set_payment($payment);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "payment".')

                    ->if($this->testedInstance->hydrate(['payment' => $payment]))
                    ->then
                        ->object($this->testedInstance->get_payment())
                            ->isIdenticalTo($payment)

                ->assert('property')
                    ->variable($this->newTestedInstance->payment)
                        ->isNull

                    ->exception(function () use ($payment) {
                        $this->testedInstance->payment = $payment;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "payment".')

                    ->if($this->testedInstance->hydrate(['payment' => $payment]))
                    ->then
                        ->object($this->testedInstance->payment)
                            ->isIdenticalTo($payment)
        ;
    }

    public function testGetResponse()
    {
        $this
            ->if($response = uniqid())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getResponse())
                        ->isNull

                    ->exception(function () use ($response) {
                        $this->testedInstance->setResponse($response);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "response".')

                    ->if($this->testedInstance->hydrate(['response' => $response]))
                    ->then
                        ->string($this->testedInstance->getResponse())
                            ->isIdenticalTo($response)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_response())
                        ->isNull

                    ->exception(function () use ($response) {
                        $this->testedInstance->set_response($response);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "response".')

                    ->if($this->testedInstance->hydrate(['response' => $response]))
                    ->then
                        ->string($this->testedInstance->get_response())
                            ->isIdenticalTo($response)

                ->assert('property')
                    ->variable($this->newTestedInstance->response)
                        ->isNull

                    ->exception(function () use ($response) {
                        $this->testedInstance->response = $response;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "response".')

                    ->if($this->testedInstance->hydrate(['response' => $response]))
                    ->then
                        ->string($this->testedInstance->response)
                            ->isIdenticalTo($response)
        ;
    }
}
