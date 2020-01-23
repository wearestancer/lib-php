<?php

namespace ild78\Tests\functional;

use DateTime;
use ild78;

/**
 * @namespace \Tests\functional
 */
class Card extends TestCase
{
    public function testGetData()
    {
        $this
            ->assert('Unknown user result a 404 exception')
                ->if($this->newTestedInstance($id = 'card_' . $this->getRandomString(24)))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getName();
                    })
                        ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('No such card ' . $id)

            ->assert('Get test user')
                ->if($this->newTestedInstance('card_9bKZ9cr0Ji0qSPs5c1uMQG5z'))
                ->then
                    ->string($this->testedInstance->getBrand())
                        ->isIdenticalTo('visa')

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo('US')

                    ->string($this->testedInstance->getFunding())
                        ->isIdenticalTo('credit')

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo('3055')

                    ->string($this->testedInstance->getNature())
                        ->isIdenticalTo('personnal')

                    ->string($this->testedInstance->getNetwork())
                        ->isIdenticalTo('visa')

                    ->dateTime($this->testedInstance->getExpirationDate())
                        ->hasYear(2030)
                        ->hasMonth(2)

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->isEqualTo(new DateTime('@1579024205'))
        ;
    }

    public function testCrud()
    {
        $this
            ->given($cvc = random_int(100, 999))
            ->and($name = $this->getRandomString(10))
            ->and($number = $this->getValidCardNumber())
            ->and($last4 = substr($number, -4))

            ->and($month = random_int(1, 12))
            ->and($year = date('Y') + random_int(20, 30))

            ->assert('Create card')
                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setCvc($cvc))
                ->and($this->testedInstance->setExpMonth($month))
                ->and($this->testedInstance->setExpYear($year))
                ->and($this->testedInstance->setNumber($number))
                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($id = $this->testedInstance->getId())
                        ->startWith('card_')

            ->assert('No duplication allowed')
                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setCvc($cvc))
                ->and($this->testedInstance->setExpMonth($month))
                ->and($this->testedInstance->setExpYear($year))
                ->and($this->testedInstance->setNumber($number))
                ->then
                    ->exception(function () {
                        $this->testedInstance->send();
                    })
                        ->isInstanceOf(ild78\Exceptions\ConflictException::class)
                        ->message
                            ->isIdenticalTo('Card already exists, you may want to update it instead creating a new one (' . $id . ')')

            ->assert('Update')
                ->if($this->newTestedInstance($id))
                ->then
                    ->variable($this->testedInstance->getName())
                        ->isNull

                    ->object($this->testedInstance->setName($name)->send())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getName())
                        ->isIdenticalTo($name)

            ->assert('Read data / Name')
                ->if($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo($name)

            ->assert('Read data / Expiration month')
                ->if($this->newTestedInstance($id))
                ->then
                    ->integer($this->testedInstance->getExpMonth())
                        ->isIdenticalTo($month)

            ->assert('Read data / Expiration year')
                ->if($this->newTestedInstance($id))
                ->then
                    ->integer($this->testedInstance->getExpYear())
                        ->isIdenticalTo($year)

            ->assert('Read data / Other field')
                ->if($this->newTestedInstance($id))
                ->then
                    // Could not be return by the API
                    ->variable($this->testedInstance->getCvc())
                        ->isNull

                    // Could not be return by the API
                    ->variable($this->testedInstance->getNumber())
                        ->isNull

                    // We could not validate the value
                    ->string($this->testedInstance->getFunding())
                    ->string($this->testedInstance->getNature())
                    ->string($this->testedInstance->getNetwork())

            ->assert('Delete card')
                ->if($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->delete())
                        ->isTestedInstance

                    ->variable($this->testedInstance->getId())
                        ->isNull

            ->assert('No more data')
                ->if($this->newTestedInstance($id))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getName();
                    })
                        ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('No such card ' . $id)
        ;
    }
}
