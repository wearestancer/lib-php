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
}
