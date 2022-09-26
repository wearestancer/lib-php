<?php

namespace Stancer\tests\unit;

use Stancer;
use Stancer\Dispute as testedClass;

class Dispute extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\AmountTrait::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
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
            ->given($this->newTestedInstance)
            ->and($orderId = uniqid())
            ->then
                ->variable($this->testedInstance->getOrderId())
                    ->isNull

            ->if($this->testedInstance->hydrate(['order_id' => $orderId]))
            ->then
                ->string($this->testedInstance->getOrderId())
                    ->isIdenticalTo($orderId)
        ;
    }

    public function testGetPayment()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($payment = new Stancer\Payment)
            ->then
                ->variable($this->testedInstance->getPayment())
                    ->isNull

            ->if($this->testedInstance->hydrate(['payment' => $payment]))
            ->then
                ->object($this->testedInstance->getPayment())
                    ->isIdenticalTo($payment)
        ;
    }

    public function testGetResponse()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($response = uniqid())
            ->then
                ->variable($this->testedInstance->getResponse())
                    ->isNull

            ->if($this->testedInstance->hydrate(['response' => $response]))
            ->then
                ->string($this->testedInstance->getResponse())
                    ->isIdenticalTo($response)
        ;
    }
}
