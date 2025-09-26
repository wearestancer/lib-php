<?php

namespace Stancer\Tests\functional;

use Stancer;

/**
 * @namespace \Tests\functional
 *
 * @internal
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
                        ->isInstanceOf(Stancer\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo($this->getNotFoundExceptionMessage($id, 'SEPA'))

            ->assert('Get test sepa')
                ->if($this->newTestedInstance('sepa_XbEkAt8hAKTbh6Yel7xjEk7O'))
                ->then
                    ->string($this->testedInstance->getBic())
                        ->isIdenticalTo('TESTFRPP')

                    ->dateTime($this->testedInstance->getDateMandate())
                        ->isEqualTo(new \DateTimeImmutable('2024-12-25'))

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo('2606')

                    ->string($this->testedInstance->getMandate())
                        ->isIdenticalTo('libraries_mandate_id')

                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo('Patrice Dinde')

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->isEqualTo(new \DateTime('@1758551637'))

                    /*
                     * TODO THe customer field is not set in our SDK for now
                     * ->string($this->testedInstance->getCustomer()->getId())
                     *      ->isIdenticalTo('cust_kw4kwsJHmcWTPd2w5Y6XaT6Q')
                     */
        ;
    }
}
