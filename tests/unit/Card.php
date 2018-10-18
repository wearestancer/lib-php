<?php

namespace ild78\tests\unit;

use atoum;
use DateTime;
use ild78\Api;
use ild78\Card as testedClass;
use ild78\Exceptions;

class Card extends atoum
{
    public function cardNumberDataProvider()
    {
        // Card number found on https://www.freeformatter.com/credit-card-number-generator-validator.html
        return [
            // VISA
            4532160583905253,
            4103344114503410,
            4716929813250776300,

            // MasterCard
            5312580044202748,
            2720995588028031,
            5217849688268117,

            // American Express (AMEX)
            370301138747716,
            340563568138644,
            371461161518951,

            // Discover
            6011651456571367,
            6011170656779399,
            6011693048292929421,

            // JCB
            3532433013111566,
            3544337258139297,
            3535502591342895821,

            // Diners Club - North America
            5480649643931654,
            5519243149714783,
            5509141180527803,

            // Diners Club - Carte Blanche
            30267133988393,
            30089013015810,
            30109478108973,

            // Diners Club - International
            36052879958170,
            36049904526204,
            36768208048819,

            // Maestro
            5893433915020244,
            6759761854174320,
            6759998953884124,

            // Visa Electron
            4026291468019846,
            4844059039871494,
            4913054050962393,

            // InstaPayment
            6385037148943057,
            6380659492219803,
            6381454097795863,

            // Classic one
            4111111111111111,
            4242424242424242,
            4444333322221111,
        ];
    }

    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
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
                        ->isInstanceOf(Exceptions\InvalidExpirationMonthException::class)
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
                        ->isInstanceOf(Exceptions\InvalidExpirationYearException::class)
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

                ->assert('Test month ' . $month . ' : alias')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->getExpirationMonth())
                            ->isNull

                        ->object($this->testedInstance->setExpirationMonth($month))
                            ->isTestedInstance

                        ->integer($this->testedInstance->getExpirationMonth())
                            ->isIdenticalTo($month)
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
                            ->isInstanceOf(Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')
            ;
        }
    }

    public function testGetExpYear_SetExpYear()
    {
        $currentYear = (int) date('Y');

        for ($year = $currentYear; $year < $currentYear + 20; $year++) {
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

                ->assert('Test year ' . $year . ' : alias')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->getExpirationYear())
                            ->isNull

                        ->object($this->testedInstance->setExpirationYear($year))
                            ->isTestedInstance

                        ->integer($this->testedInstance->getExpirationYear())
                            ->isIdenticalTo($year)
            ;
        }

        for ($year = $currentYear - 10; $year < $currentYear; $year++) {
            $this
                ->assert('Test year ' . $year)
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($year) {
                            $this->testedInstance->setExpYear($year);
                        })
                            ->isInstanceOf(Exceptions\InvalidExpirationYearException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration year "' . $year . '"')
            ;
        }
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
                        ->isInstanceOf(Exceptions\InvalidCardCvcException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('A valid cvc must have 3 characters.')
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
                    ->isInstanceOf(Exceptions\InvalidNameException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid name must be between 4 and 64 characters.')
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

                    ->object($this->testedInstance->setNumber($number))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getNumber())
                        ->isIdenticalTo($number)

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo($last)

            ->assert('Throw exception if invalid')
                ->given($this->newTestedInstance)
                ->and($badNumber = $number + 1)
                ->then
                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->setNumber($badNumber);
                    })
                        ->isInstanceOf(Exceptions\InvalidCardNumberException::class)
                        ->message
                            ->isIdenticalTo('"' . $badNumber . '" is not a valid credit card number.')
        ;
    }
}
