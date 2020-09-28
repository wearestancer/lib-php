<?php

namespace ild78\Tests\functional;

use DateTime;
use ild78;

/**
 * @namespace \Tests\functional
 */
class Sepa extends TestCase
{
    public function testGetData()
    {
        $this
            ->assert('Unknown user result a 404 exception')
                ->if($this->newTestedInstance($id = 'sepa_' . $this->getRandomString(24)))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getName();
                    })
                        ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('No such sepa ' . $id)

            ->assert('Get test sepa')
                ->if($this->newTestedInstance('sepa_bIvCZePYqfMlU11TANT8IqL1'))
                ->then
                    ->string($this->testedInstance->getBic())
                        ->isIdenticalTo('TESTSEPP')

                    ->dateTime($this->testedInstance->getDateMandate())
                        ->isEqualTo(new DateTime('@1601045728'))

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo('0003')

                    ->string($this->testedInstance->getMandate())
                        ->isIdenticalTo('mandate-identifier')

                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo('John Doe')

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->isEqualTo(new DateTime('@1601045777'))
        ;
    }
}
