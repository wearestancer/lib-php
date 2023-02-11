<?php

namespace Stancer\tests\unit;

use Stancer;
use mock;

class Payout extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
        ;
    }

    public function testGetAmount()
    {
        $this
            ->if($value = rand(50, 99999))
            ->then
                ->variable($this->newTestedInstance->getAmount())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setAmount($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "amount".')

            ->if($client = new mock\Stancer\Http\Client)
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->integer($this->newTestedInstance(uniqid())->getAmount())
                    ->isIdenticalTo(9400)

                ->integer($this->testedInstance->amount)
                    ->isIdenticalTo(9400)
        ;
    }

    public function testGetCurrency()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getCurrency())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setCurrency($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "currency".')

            ->if($client = new mock\Stancer\Http\Client)
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->string($this->newTestedInstance(uniqid())->getCurrency())
                    ->isIdenticalTo('eur')

                ->string($this->testedInstance->currency)
                    ->isIdenticalTo('eur')
        ;
    }

    public function testGetDateBank()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getDateBank())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setDateBank($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "dateBank".')

            ->if($client = new mock\Stancer\Http\Client)
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->dateTime($this->newTestedInstance(uniqid())->getDateBank())
                    ->hasDate(2022, 1, 27)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->dateBank)
                    ->hasDate(2022, 1, 27)
                    ->hasTime(0, 0, 0)
        ;
    }

    public function testGetDatePaym()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getDatePaym())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setDatePaym($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "datePaym".')

            ->if($client = new mock\Stancer\Http\Client)
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->dateTime($this->newTestedInstance(uniqid())->getDatePaym())
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->datePaym)
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->getDatePayment())
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->datePayment)
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->string($this->newTestedInstance->getEndpoint())
                ->isIdenticalTo('payouts')
        ;
    }

    public function testGetFees()
    {
        $this
            ->if($value = rand(0, 100))
            ->then
                ->variable($this->newTestedInstance->getFees())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setFees($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "fees".')

            ->if($client = new mock\Stancer\Http\Client)
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->integer($this->newTestedInstance(uniqid())->getFees())
                    ->isIdenticalTo(100)

                ->integer($this->testedInstance->fees)
                    ->isIdenticalTo(100)
        ;
    }

    public function testGetStatementDescription()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getStatementDescription())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setStatementDescription($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "statementDescription".')

            ->if($client = new mock\Stancer\Http\Client)
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->string($this->newTestedInstance(uniqid())->getStatementDescription())
                    ->isIdenticalTo('Stancer Payout Statement')

                ->string($this->testedInstance->statementDescription)
                    ->isIdenticalTo('Stancer Payout Statement')
        ;
    }

    public function testGetStatus()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getStatus())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setStatus($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "status".')

            ->if($client = new mock\Stancer\Http\Client)
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->string($this->newTestedInstance(uniqid())->getStatus())
                    ->isIdenticalTo(Stancer\Payout\Status::PAID)

                ->string($this->testedInstance->status)
                    ->isIdenticalTo(Stancer\Payout\Status::PAID)
        ;
    }
}
