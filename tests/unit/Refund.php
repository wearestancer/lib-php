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
                ->isSubclassOf(ild78\Api\AbstractObject::class)
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

            ->assert('random value')
                ->object($this->newTestedInstance->setAmount($amount = rand(50, 999999)))
                    ->isTestedInstance
                ->integer($this->testedInstance->getAmount())
                    ->isEqualTo($amount)

                ->boolean($this->testedInstance->isModified())
                    ->isTrue
        ;
    }
}
