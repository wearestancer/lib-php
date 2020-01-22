<?php

namespace ild78\tests\unit;

use ild78;
use ild78\Refund as testedClass;

class Refund extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(ild78\Core\AbstractObject::class)
                ->hasTrait(ild78\Traits\AmountTrait::class)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('refunds')
        ;
    }

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
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "status".')
        ;

        $list = [
            ild78\Refund\Status::NOT_HONORED,
            ild78\Refund\Status::REFUND_SENT,
            ild78\Refund\Status::REFUNDED,
            ild78\Refund\Status::TO_REFUND,
        ];

        foreach ($list as $status) {
            $this
                ->if($this->testedInstance->hydrate(['status' => $status]))
                ->then
                    ->string($this->testedInstance->getStatus())
                        ->isIdenticalTo($this->testedInstance->status)
                        ->isIdenticalTo($status)
            ;
        }
    }

    public function testSetAmount()
    {
        $this
            ->assert('0 is not a valid amount')
                ->exception(function () {
                    $this->newTestedInstance->setAmount(0);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidAmountException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse

            ->assert('49 is not a valid amount')
                ->exception(function () {
                    $this->newTestedInstance->setAmount(49);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidAmountException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse

            ->assert('50 is valid')
                ->object($this->newTestedInstance->setAmount(50))
                    ->isTestedInstance
                ->integer($this->testedInstance->getAmount())
                    ->isEqualTo(50)

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('amount')
                    ->integer['amount']
                        ->isEqualTo(50)

            ->assert('random value')
                ->object($this->newTestedInstance->setAmount($amount = rand(50, 999999)))
                    ->isTestedInstance
                ->integer($this->testedInstance->getAmount())
                    ->isEqualTo($amount)

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('amount')
                    ->integer['amount']
                        ->isEqualTo($amount)
        ;
    }
}
