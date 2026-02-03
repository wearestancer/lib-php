<?php

namespace Stancer\tests\unit\Payout;

use Stancer;

class Details extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
        ;
    }

    public function testDisputes()
    {
        $this
            ->if($amount = rand(50, 999999))
            ->then
                ->variable($this->newTestedInstance->getDisputes())
                    ->isNull

                ->exception(function () {
                    $this->newTestedInstance->setDisputes(uniqid());
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "disputes".')

                ->object($this->newTestedInstance->hydrate(['disputes' => ['amount' => $amount]])->getDisputes())
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->object($this->testedInstance->disputes)
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->integer($this->testedInstance->disputes->amount)
                    ->isIdenticalTo($amount)
        ;
    }

    public function testPayments()
    {
        $this
            ->if($amount = rand(50, 999999))
            ->then
                ->variable($this->newTestedInstance->getPayments())
                    ->isNull

                ->exception(function () {
                    $this->newTestedInstance->setPayments(uniqid());
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "payments".')

                ->object($this->newTestedInstance->hydrate(['payments' => ['amount' => $amount]])->getPayments())
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->object($this->testedInstance->payments)
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->integer($this->testedInstance->payments->amount)
                    ->isIdenticalTo($amount)
        ;
    }

    public function testRefunds()
    {
        $this
            ->if($amount = rand(50, 999999))
            ->then
                ->variable($this->newTestedInstance->getRefunds())
                    ->isNull

                ->exception(function () {
                    $this->newTestedInstance->setRefunds(uniqid());
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "refunds".')

                ->object($this->newTestedInstance->hydrate(['refunds' => ['amount' => $amount]])->getRefunds())
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->object($this->testedInstance->refunds)
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->integer($this->testedInstance->refunds->amount)
                    ->isIdenticalTo($amount)
        ;
    }
}
