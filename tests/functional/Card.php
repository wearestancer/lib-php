<?php

namespace Stancer\Tests\functional;

use Stancer;
use Stancer\Config;

/**
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

    public function testCrud()
    {
        $this
            ->given($cvc = random_int(100, 999))
            ->and($name = $this->getRandomString(10))
            ->and($number = $this->getValidCardNumber())
            ->and($last4 = substr($number, -4))

            ->and($month = random_int(1, 12))
            ->and($year = (int) date('Y') + random_int(20, 30))

            ->assert('Create card')
                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setCvc((string) $cvc))
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
                ->and($this->testedInstance->setCvc((string) $cvc))
                ->and($this->testedInstance->setExpMonth($month))
                ->and($this->testedInstance->setExpYear($year))
                ->and($this->testedInstance->setNumber($number))
        ;

        if ($this->config->version === Stancer\Enum\ApiVersion::VERSION_1) {
            $this->exception(function () {
                $this->testedInstance->send();
            })
                ->isInstanceOf(Stancer\Exceptions\ConflictException::class)
                ->message
                    ->isIdenticalTo('Card already exists, you may want to update it instead creating a new one (' . $id . ')')
            ;
        } else {
            $this->object($this->testedInstance->send())
                ->isInstanceOf(Stancer\Card::class)
                ->string($this->testedInstance->getId())
                    ->isNotEmpty
                ->integer($this->testedInstance->getExpMonth())
                    ->isIdenticalTo($month)
            ;
        }

        $this
            ->assert('Updatev1')
                ->given(Config::getGlobal()->setVersion(Stancer\Enum\ApiVersion::VERSION_1))
                ->if($this->newTestedInstance($id))
                ->then
                    ->variable($this->testedInstance->getName())
                        ->isNull

                    ->object($this->testedInstance->setName($name)->send())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getName())
                        ->isIdenticalTo($name)

            ->assert('Updatev2')
                ->given(Config::getGlobal()->setVersion(Stancer\Enum\ApiVersion::VERSION_2))
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
        ;
    }
}
