<?php

namespace Stancer\tests\unit;

use Stancer;

class Refund extends Stancer\Tests\atoum
{
    /**
     * @tag Refund
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\AmountTrait::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
        ;
    }

    /**
     * @tag AbstractObject Refund
     */
    public function testGetDateBank()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($date = new \DateTime())
            ->then
                ->variable($this->testedInstance->getDateBank())
                    ->isNull

                ->exception(function () use ($date) {
                    $this->testedInstance->setDateBank($date);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "dateBank".')

            ->if($this->testedInstance->hydrate(['dateBank' => $date]))
            ->then
                ->dateTime($this->testedInstance->getDateBank())
                    ->isEqualTo($date)
        ;
    }

    /**
     * @tag AbstractObject Refund
     */
    public function testGetDateRefund()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($date = new \DateTime())
            ->then
                ->variable($this->testedInstance->getDateRefund())
                    ->isNull

                ->exception(function () use ($date) {
                    $this->testedInstance->setDateRefund($date);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "dateRefund".')

            ->if($this->testedInstance->hydrate(['dateRefund' => $date]))
            ->then
                ->dateTime($this->testedInstance->getDateRefund())
                    ->isEqualTo($date)
        ;
    }

    /**
     * @tag Refund
     */
    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('refunds')
        ;
    }

    /**
     * @tag AbstractObject AliasTrait Refund
     */
    public function testGetStatus()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getStatus())
                    ->isIdenticalTo($this->testedInstance->status)
                    ->isNull

                ->exception(function () {
                    $this->testedInstance->setStatus(uniqid());
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "status".')
        ;

        $list = [
            Stancer\Refund\Status::NOT_HONORED,
            Stancer\Refund\Status::REFUND_SENT,
            Stancer\Refund\Status::REFUNDED,
            Stancer\Refund\Status::TO_REFUND,
        ];

        foreach ($list as $status) {
            $this
                ->if($this->testedInstance->hydrate(['status' => $status]))
                ->then
                    ->enum($this->testedInstance->getStatus())
                        ->isIdenticalTo($this->testedInstance->status)
                        ->isIdenticalTo($status)
            ;
        }
    }

    /**
     * @tag AbstractObject AliasTrait AmountTrait Refund
     */
    public function testSetAmount()
    {
        $this
            ->assert('camelCase method')
                ->exception(function () {
                    $this->newTestedInstance->setAmount($this->getRandomAmount());
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "amount".')

            ->assert('camelCase method')
                ->exception(function () {
                    $this->newTestedInstance->set_amount($this->getRandomAmount());
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "amount".')

            ->assert('property')
                ->exception(function () {
                    $this->newTestedInstance->amount = $this->getRandomAmount();
                })
                    ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "amount".')
        ;
    }
}
