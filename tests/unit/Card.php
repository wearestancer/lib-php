<?php

namespace ild78\tests\unit;

use DateTime;
use ild78;
use ild78\Card as testedClass;

class Card extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Cards;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(ild78\Core\AbstractObject::class)
                ->implements(ild78\Interfaces\PaymentMeansInterface::class)
        ;
    }

    /**
     * @dataProvider brandDataProvider
     */
    public function testGetBrand($tag, $name)
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getBrand())
                    ->isNull

                ->exception(function () use ($tag) {
                    $this->testedInstance->setBrand($tag);
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)

            ->if($this->testedInstance->hydrate(['brand' => $tag]))
            ->then
                ->string($this->testedInstance->getBrand())
                    ->isIdenticalTo($tag)
        ;
    }

    /**
     * @dataProvider brandDataProvider
     */
    public function testGetBrandName($tag, $name)
    {
        $this
            ->given($this->newTestedInstance)
            ->and($data = [
                'brand' => $tag,
            ])
            ->and($this->testedInstance->hydrate($data))
            ->then
                ->string($this->testedInstance->getBrand())
                    ->isIdenticalTo($tag)

                ->string($this->testedInstance->getBrandName())
                    ->isIdenticalTo($name)
        ;
    }

    public function testGetExpDate()
    {
        $this
            ->assert('Month and year already set')
                ->given($this->newTestedInstance)
                ->and($month = rand(1, 12))
                ->and($year = date('Y') + rand(0, 10))
                ->and($this->testedInstance->setExpirationMonth($month))
                ->and($this->testedInstance->setExpirationYear($year))

                ->if($date = new DateTime($year . '-' . $month . '-01T23:59:59'))
                ->and($date->modify('last day of'))
                ->then
                    ->dateTime($this->testedInstance->getExpDate())
                        ->hasYear($year)
                        ->hasMonth($month)
                        ->isEqualTo($date)

                    ->dateTime($this->testedInstance->getExpirationDate()) // alias
                        ->isEqualTo($date)

            ->assert('Month not set')
                ->given($this->newTestedInstance)
                ->and($year = date('Y') + rand(0, 10))
                ->and($this->testedInstance->setExpirationYear($year))

                ->then
                    ->exception(function () {
                        $this->testedInstance->getExpDate();
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidExpirationMonthException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration month before asking for a date.')

            ->assert('Year not set')
                ->given($this->newTestedInstance)
                ->and($month = rand(1, 12))
                ->and($this->testedInstance->setExpirationMonth($month))

                ->then
                    ->exception(function () {
                        $this->testedInstance->getExpDate();
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidExpirationYearException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration year before asking for a date.')
        ;
    }

    public function testGetExpMonth_SetExpMonth()
    {
        foreach (range(1, 12) as $month) {
            $this
                ->assert('Test month ' . $month)
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->getExpMonth())
                            ->isNull

                        ->object($this->testedInstance->setExpMonth($month))
                            ->isTestedInstance

                        ->integer($this->testedInstance->getExpMonth())
                            ->isIdenticalTo($month)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_month')
                            ->integer['exp_month']
                                ->isEqualTo($month)

                ->assert('Test month ' . $month . ' : alias')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->getExpirationMonth())
                            ->isNull

                        ->object($this->testedInstance->setExpirationMonth($month))
                            ->isTestedInstance

                        ->integer($this->testedInstance->getExpirationMonth())
                            ->isIdenticalTo($month)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_month')
                            ->integer['exp_month']
                                ->isEqualTo($month)
            ;
        }

        $months = [0, 13];

        for ($index = 0; $index < rand(1, 10) ; $index ++) {
            $months[] = rand(14, 100);
        }

        foreach ($months as $month) {
            $this
                ->assert($month . ' is not a valid month')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->setExpMonth($month);
                        })
                            ->isInstanceOf(ild78\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse
            ;
        }
    }

    public function testGetExpYear_SetExpYear()
    {
        $currentYear = (int) date('Y');

        for ($year = $currentYear - 50; $year < $currentYear + 25; $year++) {
            $this
                ->assert('Test year ' . $year)
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->getExpYear())
                            ->isNull

                        ->object($this->testedInstance->setExpYear($year))
                            ->isTestedInstance

                        ->integer($this->testedInstance->getExpYear())
                            ->isIdenticalTo($year)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_year')
                            ->integer['exp_year']
                                ->isEqualTo($year)

                ->assert('Test year ' . $year . ' : alias')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->getExpirationYear())
                            ->isNull

                        ->object($this->testedInstance->setExpirationYear($year))
                            ->isTestedInstance

                        ->integer($this->testedInstance->getExpirationYear())
                            ->isIdenticalTo($year)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_year')
                            ->integer['exp_year']
                                ->isEqualTo($year)
            ;
        }
    }

    public function testGetFunding()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($value = uniqid())
            ->then
                ->variable($this->testedInstance->getFunding())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->testedInstance->setFunding($value);
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)

            ->if($this->testedInstance->hydrate(['funding' => $value]))
            ->then
                ->string($this->testedInstance->getFunding())
                    ->isIdenticalTo($value)
        ;
    }

    public function testGetName_SetName()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($name = $this->getRandomString(4, 64))
            ->then
                ->variable($this->testedInstance->getName())
                    ->isNull

                ->object($this->testedInstance->setName($name))
                    ->isTestedInstance

                ->string($this->testedInstance->getName())
                    ->isIdenticalTo($name)

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('name')
                    ->string['name']
                        ->isEqualTo($name)
        ;
    }

    public function testGetTokenize_SetTokenize()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert('Return false by default')
                    ->boolean($this->testedInstance->getTokenize())
                        ->isFalse

                ->assert('Tokenize is not sended by default')
                    ->array($this->testedInstance->toArray())
                        ->notHasKey('tokenize')

                ->assert('setTokenize return itself')
                    ->object($this->testedInstance->setTokenize(true))
                        ->isTestedInstance

                ->assert('Now it\'s visible')
                    ->boolean($this->testedInstance->getTokenize())
                        ->isTrue

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($data = $this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('tokenize')

                    ->boolean($data['tokenize'])
                        ->isTrue

                ->assert('Visible when forced to false')
                    ->object($this->testedInstance->setTokenize(false))
                        ->isTestedInstance

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->boolean($this->testedInstance->getTokenize())
                        ->isFalse

                    ->array($this->testedInstance->toArray())
                        ->hasKey('tokenize')

                    ->array($data = $this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('tokenize')

                    ->boolean($data['tokenize'])
                        ->isFalse

                ->assert('Aliases')
                    ->boolean($this->testedInstance->getTokenize())
                        ->isIdenticalTo($this->testedInstance->getTokenize)
                        ->isIdenticalTo($this->testedInstance->tokenize)
                        ->isIdenticalTo($this->testedInstance->isTokenized())
                        ->isIdenticalTo($this->testedInstance->isTokenized)
        ;
    }

    public function testGetZipCode_SetZipCode()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($zip = $this->getRandomString(2, 8))
            ->then
                ->variable($this->testedInstance->getZipCode())
                    ->isNull

                ->object($this->testedInstance->setZipCode($zip))
                    ->isTestedInstance

                ->string($this->testedInstance->getZipCode())
                    ->isIdenticalTo($zip)

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('zip_code')
                    ->string['zip_code']
                        ->isEqualTo($zip)
        ;
    }

    public function testSetCvc()
    {
        for ($index = 0; $index < 5; $index++) {
            if ($index === 3) {
                continue;
            }

            $this
                ->given($this->newTestedInstance)
                ->and($cvc = substr(uniqid(), 0, $index))
                ->then
                    ->exception(function () use ($cvc) {
                        $this->testedInstance->setCvc($cvc);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidCardCvcException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('A valid cvc must have 3 characters.')

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
            ;
        }
    }

    public function testSetName()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function () {
                    $this->testedInstance->setName('');
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidNameException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid name must be between 4 and 64 characters.')

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
        ;
    }

    /**
     * @dataProvider cardNumberDataProvider
     */
    public function testSetNumber($number)
    {
        $this
            ->assert('Accept valid card')
                ->given($this->newTestedInstance)
                ->and($last = substr((string) $number, -4))
                ->then
                    ->variable($this->testedInstance->getNumber())
                        ->isNull

                    ->variable($this->testedInstance->getLast4())
                        ->isNull

                    ->object($this->testedInstance->setNumber((string) $number))
                        ->isTestedInstance

                    ->string($this->testedInstance->getNumber())
                        ->isIdenticalTo((string) $number)

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo($last)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

            ->assert('Throw exception if invalid')
                ->given($this->newTestedInstance)
                ->and($badNumber = $number + 1)
                ->then
                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->setNumber((string) $badNumber);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidCardNumberException::class)
                        ->message
                            ->isIdenticalTo('"' . $badNumber . '" is not a valid credit card number.')

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
        ;
    }

    /**
     * @dataProvider cardNumberDataProvider
     */
    public function testToArray($number)
    {
        $this
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->setNumber((string) $number))
            ->then
                ->array($this->testedInstance->toArray())
                    ->notHasKey('last4')

                    ->hasKey('number')
                    ->string['number']
                        ->isEqualTo($number)
        ;
    }
}
