<?php

namespace Stancer\Tests\functional;

use Stancer;

/**
 * @tags AbstractObject Address
 *
 * @namespace \Tests\functional
 *
 * @internal
 */
class Address extends TestCase
{
    public function testDelete()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($key = $this->getRandomString(10))
            ->and($this->testedInstance->setCountry($this->getRandomString(2)))
            ->and($this->testedInstance->setCity($this->getRandomString(1, 50)))
            ->and($this->testedInstance->setZipCode($this->getRandomString(2, 16)))

            ->and($id = $this->testedInstance->send()->getId())
            ->then
                ->object($this->testedInstance->delete())
                    ->isTestedInstance

                ->variable($this->testedInstance->getId())
                    ->isNull
        ;
        if ($this->config->version === Stancer\Enum\ApiVersion::VERSION_1) {
            $this->exception(function () use ($id) {
                $this->newTestedInstance($id)->getCountry();
            })
                    ->isInstanceOf(Stancer\Exceptions\NotFoundException::class)
                    ->message
                        ->isIdenticalTo($this->getNotFoundExceptionMessage($id, 'address'))
            ;
        } else {
            $this
                ->given($this->newTestedInstance($id))
                    ->then
                        ->boolean($this->testedInstance->getDeleted())
                            ->isTrue
            ;
        }
    }

    public function testGetData()
    {
        $this
            ->assert('Unknown address result a 404 exception')
                ->if($this->newTestedInstance($id = 'addr_' . $this->getRandomString(24)))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getCountry();
                    })
                        ->isInstanceOf(Stancer\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo($this->getNotFoundExceptionMessage($id, 'Address'))

            ->assert('Get test address')
                ->if($this->newTestedInstance('addr_0CMU6czWYmRtqqLEi6emsJ1B'))
                ->then
                    ->string($this->testedInstance->getCity())
                        ->isIdenticalTo('Paris')

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo('FR')

                    ->datetime($this->testedInstance->getCreatedAt())
                        ->isEqualTo(new \DateTime('@1760622735'))

                    ->string($this->testedInstance->getLine1())
                        ->isIdenticalTo('10 rue du Poulet')

                    ->string($this->testedInstance->getLine2())
                        ->isIdenticalTo('derrière le poulet jaune')

                    ->string($this->testedInstance->getLine3())
                        ->isIdenticalTo('à coté du poussin bleu')

                    ->string($this->testedInstance->getState())
                        ->isIdenticalTo('IDF')

                    ->string($this->testedInstance->getZipCode())
                        ->isIdenticalTo('75001')
        ;
    }

    public function testSend()
    {
        $this
            ->and($line1 = $this->getRandomstring(3, 50))
            ->and($line2 = $this->getRandomstring(3, 50))
            ->and($line3 = $this->getRandomstring(3, 50))
            ->and($state = strtoupper($this->getRandomString(1, 3)))
            ->and($zipCode = strtoupper($this->getRandomString(2, 16)))
            ->and($city = $this->getRandomString(1, 50))
            ->and($country = $this->getRandomString(2))

            ->assert('Complete address')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setLine1($line1))
                ->and($this->testedInstance->setLine2($line2))
                ->and($this->testedInstance->setLine3($line3))
                ->and($this->testedInstance->setZipCode($zipCode))
                ->and($this->testedInstance->setCity($city))
                ->and($this->testedInstance->setState($state))
                ->and($this->testedInstance->setCountry($country))

                ->then
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($id = $this->testedInstance->getId())
                        ->isNotEmpty

            ->assert('Same data create a new address')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setLine1($line1))
                ->and($this->testedInstance->setLine2($line2))
                ->and($this->testedInstance->setLine3($line3))
                ->and($this->testedInstance->setZipCode($zipCode))
                ->and($this->testedInstance->setCity($city))
                ->and($this->testedInstance->setState($state))
                ->and($this->testedInstance->setCountry($country))
                ->then
                    ->object($this->testedInstance->send())
                        ->isInstanceOf(Stancer\Address::class)
                    ->string($this->testedInstance->id)
                        ->isNotEmpty
                        ->isNotEqualTo($id)
                    ->string($this->testedInstance->city)
                        ->isEqualTo($city)
        ;
    }

    /**
     * @DataProvider versionDataProvider
     */
    public function testUpdate()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($zipCode = strtoupper($this->getRandomString(2, 16)))
            ->and($city = $this->getRandomString(1, 40))
            ->and($country = $this->getRandomString(2))
            ->and($this->testedInstance->setZipCode($zipCode))
            ->and($this->testedInstance->setCity($city))
            ->and($this->testedInstance->setCountry($country))
            ->and($this->testedInstance->send())
            ->and($newCity = 'New-' . $city)
            ->and($this->testedInstance->setCity($newCity))
            ->then
                ->exception(function () {
                    $this->testedInstance->send();
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('Addresses cannot be patched.')
        ;
    }
}
