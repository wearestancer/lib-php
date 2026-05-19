<?php

namespace Stancer\Tests\functional;

use Stancer;

/**
 * @tags Card AbstractObject
 *
 * @namespace \Tests\functional
 *
 * @internal
 */
class Card extends TestCase
{
    public function personalTypo(): string
    {
        if ($this->config->version === Stancer\Enum\ApiVersion::VERSION_1) {
            return 'personnal';
        }

        return 'personal';
    }

    public function testGetData()
    {
        $this
            ->assert('Unknown user result a 404 exception')
                ->if($this->newTestedInstance($id = 'card_' . $this->getRandomString(24)))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getName();
                    })
                        ->isInstanceOf(Stancer\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo($this->getNotFoundExceptionMessage($id, 'Card'))

            ->assert('Get test user')
                ->if($this->newTestedInstance('card_uqY2HrovY2sPm0Ac2xhnBkfU'))
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
                        ->isIdenticalTo($this->personalTypo())

                    ->string($this->testedInstance->getNetwork())
                        ->isIdenticalTo('visa')
                    ->string($this->testedInstance->getZipCode())
                        ->isIdenticalTo('75001')

                    ->dateTime($this->testedInstance->getExpirationDate())
                        ->hasYear(2099)
                        ->hasMonth(12)

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->isEqualTo(new \DateTime('@1758551022'))
        ;
    }

    public function testCrudV1()
    {
        if ($this->config->version !== Stancer\Enum\ApiVersion::VERSION_1) {
            return;
        }

        $this
            ->given($cvc = $this->getRandomCvc())
            ->and(['network' => $network, 'card' => $card] = $this->getValidCardAndNetwork())
            ->and($name = $this->getRandomString(10))
            ->and($number = $card)
            ->and($last4 = substr($number, -4))
            ->and($preferredNetwork = $network)

            ->and($month = $this->getRandomMonth())
            ->and($year = $this->getRandomExpYear())

            ->assert('Create card')
                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setCvc($cvc))
                ->and($this->testedInstance->setExpMonth($month))
                ->and($this->testedInstance->setExpYear($year))
                ->and($this->testedInstance->setNumber($number))
                ->and($this->testedInstance->setPreferredNetwork($preferredNetwork))
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

            ->exception(function () {
                $this->testedInstance->send();
            })
                ->isInstanceOf(Stancer\Exceptions\ConflictException::class)
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

                    ->variable($this->testedInstance->getPreferredNetwork())
                        ->isIdenticalTo($preferredNetwork)

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

            ->assert('Read data / preferred network')
                ->if($this->newTestedInstance($id))
                ->then
                    ->variable($this->testedInstance->getPreferredNetwork())
                        ->isIdenticalTo($preferredNetwork)

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
        ;
    }

    public function testCrudV2()
    {
        if ($this->config->version === Stancer\Enum\ApiVersion::VERSION_1) {
            return;
        }

        $this
            ->given($cvc = $this->getRandomCvc())
            ->and(['network' => $network, 'card' => $card] = $this->getValidCardAndNetwork())
            ->and($name = $this->getRandomString(10))
            ->and($number = $card)
            ->and($last4 = substr($number, -4))
            ->and($preferredNetwork = $network)
            ->and($dateNow = new \DateTimeImmutable())
            ->and($month = $this->getRandomMonth())
            ->and($year = $this->getRandomExpYear())

            ->assert('Create card')
                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setCvc($cvc))
                ->and($this->testedInstance->setExpMonth($month))
                ->and($this->testedInstance->setExpYear($year))
                ->and($this->testedInstance->setNumber($number))
                ->and($this->testedInstance->setName($name))
                ->and($this->testedInstance->setPreferredNetwork($preferredNetwork))
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

                ->object($this->testedInstance->send())
                    ->isInstanceOf(Stancer\Card::class)
                ->string($this->testedInstance->getId())
                    ->isNotEmpty
                ->integer($this->testedInstance->getExpMonth())
                    ->isIdenticalTo($month)

           ->assert('Update')
                ->if($this->newTestedInstance($id))
                ->then
                    ->integer($this->testedInstance->getExpYear())
                        ->isIdenticalto($year)

                    ->integer($this->testedInstance->getExpMonth())
                        ->isIdenticalto($month)

                    ->object($this->testedInstance->setExpYear(++$year)->send())
                        ->isTestedInstance

                    ->integer($this->newTestedInstance($id)->getExpYear())
                        ->isIdenticalTo($year)

                    ->dateTime($dateCreated = $this->testedInstance->getCreated())

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
        ;

        if ($dateNow < $dateCreated) {
            $this
                ->assert('Read data / preferred network')
                ->if($this->newTestedInstance($id))
                    ->then
                        ->variable($this->testedInstance->getPreferredNetwork())
                        ->isIdenticalTo($preferredNetwork)

                ->assert('Read data / Name')
                ->if($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo($name)
                ->assert('created today')
            ;
        }

        $this
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
        ;
    }

    public function testUpdateV1()
    {
        if ($this->config->version !== Stancer\Enum\ApiVersion::VERSION_1) {
            return;
        }
        $this
            ->given($cvc = $this->getRandomCvc())
            ->and($card = 4000000000003055) // Card reserved for update testing (see youtrack API-549).
            ->and($name = $this->getRandomString(10))
            ->and($number = $card)

            ->and($month = $this->getRandomMonth())
            ->and($year = $this->getRandomExpYear())

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

            ->assert('Update')
                ->if($this->newTestedInstance($id))
                ->then
                    ->variable($this->testedInstance->getName())
                        ->isNull

                    ->object($this->testedInstance->setName($name)->send())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getName())
                        ->isIdenticalTo($name)
        ;
    }

    public function testUpdateV2()
    {
        if ($this->config->version === Stancer\Enum\ApiVersion::VERSION_1) {
            return;
        }

        $this
            ->given($cvc = $this->getRandomCvc())
            ->and($number = 4000000000003055) // Card reserved for update testing (see youtrack API-549).
            ->and($month = $this->getRandomMonth())
            ->and($year = $this->getRandomExpYear())

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

                   ->assert('Update')
                ->if($this->newTestedInstance($id))
                ->then
                    ->integer($year = $this->testedInstance->getExpYear())

                    ->object($this->testedInstance->setExpYear($newYear = $year === 2099 ? --$year : ++$year)->send()) //stay in the bondary of our exp year
                        ->isTestedInstance

                    ->integer($this->newTestedInstance($id)->getExpYear())
                        ->isIdenticalTo($newYear)

                    ->dateTime($dateCreated = $this->testedInstance->getCreated())

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
        ;
    }
}
