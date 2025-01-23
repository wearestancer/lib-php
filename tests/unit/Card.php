<?php

namespace Stancer\tests\unit;

use DateTime;
use Stancer;

class Card extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Cards;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Core\AbstractObject::class)
                ->implements(Stancer\Interfaces\PaymentMeansInterface::class)
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
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->variable($this->testedInstance->get_brand())
                    ->isNull

                ->exception(function () use ($tag) {
                    $this->testedInstance->set_brand($tag);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->variable($this->testedInstance->brand)
                    ->isNull

                ->exception(function () use ($tag) {
                    $this->testedInstance->brand = $tag;
                })
                    ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)

            ->if($this->testedInstance->hydrate(['brand' => $tag]))
            ->then
                ->string($this->testedInstance->getBrand())
                    ->isIdenticalTo($tag)

                ->string($this->testedInstance->get_brand())
                    ->isIdenticalTo($tag)

                ->string($this->testedInstance->brand)
                    ->isIdenticalTo($tag)
        ;
    }

    /**
     * @dataProvider brandDataProvider
     */
    public function testGetBrandName($tag, $name)
    {
        $this
            ->assert('camelCase method')
                ->variable($this->newTestedInstance->getBrandName())
                    ->isNull

                ->exception(function () use ($tag) {
                    $this->testedInstance->setBrandName($tag);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['brand' => $tag]))
                ->then
                    ->string($this->testedInstance->getBrandName())
                        ->isIdenticalTo($name)

            ->assert('snake_case method')
                ->variable($this->newTestedInstance->get_brand_name())
                    ->isNull

                ->exception(function () use ($tag) {
                    $this->testedInstance->set_brand_name($tag);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['brand' => $tag]))
                ->then
                    ->string($this->testedInstance->get_brand_name())
                        ->isIdenticalTo($name)

            ->assert('camelCase property')
                ->variable($this->newTestedInstance->brandName)
                    ->isNull

                ->exception(function () use ($tag) {
                    $this->testedInstance->brandName = $tag;
                })
                    ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)

                ->if($this->testedInstance->hydrate(['brand' => $tag]))
                ->then
                    ->string($this->testedInstance->brandName)
                        ->isIdenticalTo($name)

            ->assert('snake_case property')
                ->variable($this->newTestedInstance->brand_name)
                    ->isNull

                ->exception(function () use ($tag) {
                    $this->testedInstance->brand_name = $tag;
                })
                    ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)

                ->if($this->testedInstance->hydrate(['brand' => $tag]))
                ->then
                    ->string($this->testedInstance->brand_name)
                        ->isIdenticalTo($name)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('cards')
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

                    ->dateTime($this->testedInstance->expDate) // alias
                        ->isEqualTo($date)

                    ->dateTime($this->testedInstance->exp_date) // alias
                        ->isEqualTo($date)

                    ->dateTime($this->testedInstance->getExpirationDate()) // alias
                        ->isEqualTo($date)

                    ->dateTime($this->testedInstance->expirationDate) // alias
                        ->isEqualTo($date)

                    ->dateTime($this->testedInstance->expiration_date) // alias
                        ->isEqualTo($date)

            ->assert('Month not set')
                ->given($this->newTestedInstance)
                ->and($year = date('Y') + rand(0, 10))
                ->and($this->testedInstance->setExpirationYear($year))

                ->then
                    ->exception(function () {
                        $this->testedInstance->getExpDate();
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration month before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->expDate;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration month before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->exp_date;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration month before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->getExpirationDate();
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration month before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->expirationDate;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration month before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->expiration_date;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
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
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationYearException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration year before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->expDate;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationYearException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration year before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->exp_date;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationYearException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration year before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->getExpirationDate();
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationYearException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration year before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->expirationDate;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationYearException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration year before asking for a date.')

                    ->exception(function () {
                        $this->testedInstance->expiration_date;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidExpirationYearException::class)
                        ->message
                            ->isIdenticalTo('You must set an expiration year before asking for a date.')
        ;
    }

    public function testGetExpMonth_SetExpMonth()
    {
        foreach (range(1, 12) as $month) {
            $this
                ->assert('Test month ' . $month . ', camelCase method')
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

                ->assert('Test month ' . $month . ', snake_case method')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->get_exp_month())
                            ->isNull

                        ->object($this->testedInstance->set_exp_month($month))
                            ->isTestedInstance

                        ->integer($this->testedInstance->get_exp_month())
                            ->isIdenticalTo($month)

                        ->boolean($this->testedInstance->is_modified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_month')
                            ->integer['exp_month']
                                ->isEqualTo($month)

                ->assert('Test month ' . $month . ', camelCase method long form')
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

                ->assert('Test month ' . $month . ', snake_case method long form')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->get_expiration_month())
                            ->isNull

                        ->object($this->testedInstance->set_expiration_month($month))
                            ->isTestedInstance

                        ->integer($this->testedInstance->get_expiration_month())
                            ->isIdenticalTo($month)

                        ->boolean($this->testedInstance->is_modified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_month')
                            ->integer['exp_month']
                                ->isEqualTo($month)

                ->assert('Test month ' . $month . ', camelCase property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->expMonth)
                            ->isNull

                        ->if($this->testedInstance->expMonth = $month)
                        ->then
                            ->integer($this->testedInstance->expMonth)
                                ->isIdenticalTo($month)

                            ->boolean($this->testedInstance->isModified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('exp_month')
                                ->integer['exp_month']
                                    ->isEqualTo($month)

                ->assert('Test month ' . $month . ', snake_case property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->exp_month)
                            ->isNull

                        ->if($this->testedInstance->exp_month = $month)
                        ->then
                            ->integer($this->testedInstance->exp_month)
                                ->isIdenticalTo($month)

                            ->boolean($this->testedInstance->is_modified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('exp_month')
                                ->integer['exp_month']
                                    ->isEqualTo($month)

                ->assert('Test month ' . $month . ', camelCase property long form')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->expirationMonth)
                            ->isNull

                        ->if($this->testedInstance->expirationMonth = $month)
                        ->then
                            ->integer($this->testedInstance->expirationMonth)
                                ->isIdenticalTo($month)

                            ->boolean($this->testedInstance->isModified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('exp_month')
                                ->integer['exp_month']
                                    ->isEqualTo($month)

                ->assert('Test month ' . $month . ', snake_case property long form')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->expiration_month)
                            ->isNull

                        ->if($this->testedInstance->expiration_month = $month)
                        ->then
                            ->integer($this->testedInstance->expiration_month)
                                ->isIdenticalTo($month)

                            ->boolean($this->testedInstance->is_modified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('exp_month')
                                ->integer['exp_month']
                                    ->isEqualTo($month)
            ;
        }

        $months = [0, 13];

        for ($index = 0; $index < rand(1, 10) ; $index++) {
            $months[] = rand(14, 100);
        }

        foreach ($months as $month) {
            $this
                ->assert($month . ' is not a valid month, camelCase method')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->setExpMonth($month);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse

                ->assert($month . ' is not a valid month, snake_case method')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->set_exp_month($month);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->is_modified())
                            ->isFalse

                ->assert($month . ' is not a valid month, camelCase long form method')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->setExpirationMonth($month);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse

                ->assert($month . ' is not a valid month, snake_case long form method')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->set_expiration_month($month);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->is_modified())
                            ->isFalse

                ->assert($month . ' is not a valid month, camelCase property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->expMonth = $month;
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->isModified)
                            ->isFalse

                ->assert($month . ' is not a valid month, snake_case property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->exp_month = $month;
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->is_modified)
                            ->isFalse

                ->assert($month . ' is not a valid month, camelCase long form property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->expirationMonth = $month;
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->isModified)
                            ->isFalse

                ->assert($month . ' is not a valid month, snake_case long form property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($month) {
                            $this->testedInstance->expiration_month = $month;
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidExpirationMonthException::class)
                            ->message
                                ->isIdenticalTo('Invalid expiration month "' . $month . '"')

                        ->boolean($this->testedInstance->is_modified)
                            ->isFalse
            ;
        }
    }

    public function testGetExpYear_SetExpYear()
    {
        $currentYear = (int) date('Y');

        for ($year = $currentYear - 50; $year < $currentYear + 25; $year++) {
            $this
                ->assert('Test year ' . $year . ', camelCase method')
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

                ->assert('Test year ' . $year . ', snake method')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->get_exp_year())
                            ->isNull

                        ->object($this->testedInstance->set_exp_year($year))
                            ->isTestedInstance

                        ->integer($this->testedInstance->get_exp_year())
                            ->isIdenticalTo($year)

                        ->boolean($this->testedInstance->is_modified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_year')
                            ->integer['exp_year']
                                ->isEqualTo($year)

                ->assert('Test year ' . $year . ', camelCase property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->expYear)
                            ->isNull

                        ->if($this->testedInstance->expYear = $year)
                        ->then
                            ->integer($this->testedInstance->expYear)
                                ->isIdenticalTo($year)

                            ->boolean($this->testedInstance->isModified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('exp_year')
                                ->integer['exp_year']
                                    ->isEqualTo($year)

                ->assert('Test year ' . $year . ', snake property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->get_exp_year())
                            ->isNull

                        ->if($this->testedInstance->exp_year = $year)
                        ->then
                            ->integer($this->testedInstance->get_exp_year())
                                ->isIdenticalTo($year)

                            ->boolean($this->testedInstance->is_modified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('exp_year')
                                ->integer['exp_year']
                                    ->isEqualTo($year)

                ->assert('Test year ' . $year . ', camelCase long form method')
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

                ->assert('Test year ' . $year . ', snake_case long form method')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->get_expiration_year())
                            ->isNull

                        ->object($this->testedInstance->set_expiration_year($year))
                            ->isTestedInstance

                        ->integer($this->testedInstance->get_expiration_year())
                            ->isIdenticalTo($year)

                        ->boolean($this->testedInstance->is_modified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('exp_year')
                            ->integer['exp_year']
                                ->isEqualTo($year)

                ->assert('Test year ' . $year . ', camelCase long form property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->expirationYear)
                            ->isNull

                        ->if($this->testedInstance->expirationYear = $year)
                        ->then
                            ->integer($this->testedInstance->expirationYear)
                                ->isIdenticalTo($year)

                            ->boolean($this->testedInstance->isModified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('exp_year')
                                ->integer['exp_year']
                                    ->isEqualTo($year)

                ->assert('Test year ' . $year . ', snake_case long form property')
                    ->given($this->newTestedInstance)
                    ->then
                        ->variable($this->testedInstance->expiration_year)
                            ->isNull

                        ->if($this->testedInstance->expiration_year = $year)
                        ->then
                            ->integer($this->testedInstance->expiration_year)
                                ->isIdenticalTo($year)

                            ->boolean($this->testedInstance->is_modified)
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
            ->assert('camelCase method')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->getFunding())
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->setFunding($value);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['funding' => $value]))
                ->then
                    ->string($this->testedInstance->getFunding())
                        ->isIdenticalTo($value)

            ->assert('snake_case method')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->get_funding())
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->set_funding($value);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['funding' => $value]))
                ->then
                    ->string($this->testedInstance->get_funding())
                        ->isIdenticalTo($value)

            ->assert('as property')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->funding)
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->funding = $value;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)

                ->if($this->testedInstance->hydrate(['funding' => $value]))
                ->then
                    ->string($this->testedInstance->funding)
                        ->isIdenticalTo($value)
        ;
    }

    public function testGetName_SetName()
    {
        $this
            ->if($name = $this->getRandomString(4, 64))
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getName())
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

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_name())
                        ->isNull

                    ->object($this->testedInstance->set_name($name))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_name())
                        ->isIdenticalTo($name)

                    ->boolean($this->testedInstance->is_modified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('name')
                        ->string['name']
                            ->isEqualTo($name)

                ->assert('as proprty')
                    ->variable($this->newTestedInstance->name)
                        ->isNull

                    ->if($this->testedInstance->name = $name)
                    ->then
                        ->string($this->testedInstance->name)
                            ->isIdenticalTo($name)

                        ->boolean($this->testedInstance->isModified)
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('name')
                            ->string['name']
                                ->isEqualTo($name)
        ;
    }

    public function testGetNature()
    {
        $this
            ->assert('camelCase method')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->getNature())
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->setNature($value);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['nature' => $value]))
                ->then
                    ->string($this->testedInstance->getNature())
                        ->isIdenticalTo($value)

            ->assert('snake_case method')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->get_nature())
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->set_nature($value);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['nature' => $value]))
                ->then
                    ->string($this->testedInstance->get_nature())
                        ->isIdenticalTo($value)

            ->assert('as property')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->nature)
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->nature = $value;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)

                ->if($this->testedInstance->hydrate(['nature' => $value]))
                ->then
                    ->string($this->testedInstance->nature)
                        ->isIdenticalTo($value)
        ;
    }

    public function testGetNetwork()
    {
        $this
            ->assert('camelCase method')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->getNetwork())
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->setNetwork($value);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['network' => $value]))
                ->then
                    ->string($this->testedInstance->getNetwork())
                        ->isIdenticalTo($value)

            ->assert('snake_case method')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->get_network())
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->set_network($value);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)

                ->if($this->testedInstance->hydrate(['network' => $value]))
                ->then
                    ->string($this->testedInstance->get_network())
                        ->isIdenticalTo($value)

            ->assert('as property')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->variable($this->testedInstance->network)
                        ->isNull

                    ->exception(function () use ($value) {
                        $this->testedInstance->network = $value;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)

                ->if($this->testedInstance->hydrate(['network' => $value]))
                ->then
                    ->string($this->testedInstance->network)
                        ->isIdenticalTo($value)
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
                        ->isIdenticalTo($this->testedInstance->get_tokenize())
                        ->isIdenticalTo($this->testedInstance->getTokenize)
                        ->isIdenticalTo($this->testedInstance->tokenize)
                        ->isIdenticalTo($this->testedInstance->is_tokenized())
                        ->isIdenticalTo($this->testedInstance->isTokenized())
                        ->isIdenticalTo($this->testedInstance->isTokenized)
        ;
    }

    public function testGetZipCode_SetZipCode()
    {
        $this
            ->if($zip = $this->getRandomString(2, 8))
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getZipCode())
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

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_zip_code())
                        ->isNull

                    ->object($this->testedInstance->set_zip_code($zip))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_zip_code())
                        ->isIdenticalTo($zip)

                    ->boolean($this->testedInstance->is_modified())
                        ->isTrue

                    ->array($this->testedInstance->json_serialize())
                        ->hasSize(1)
                        ->hasKey('zip_code')
                        ->string['zip_code']
                            ->isEqualTo($zip)

                ->assert('camelCase propperty')
                    ->variable($this->newTestedInstance->zipCode)
                        ->isNull

                    ->if($this->testedInstance->zipCode = $zip)
                    ->then
                        ->string($this->testedInstance->zipCode)
                            ->isIdenticalTo($zip)

                        ->boolean($this->testedInstance->isModified)
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize)
                            ->hasSize(1)
                            ->hasKey('zip_code')
                            ->string['zip_code']
                                ->isEqualTo($zip)

                ->assert('snake_case propperty')
                    ->variable($this->newTestedInstance->zip_code)
                        ->isNull

                    ->if($this->testedInstance->zip_code = $zip)
                    ->then
                        ->string($this->testedInstance->zip_code)
                            ->isIdenticalTo($zip)

                        ->boolean($this->testedInstance->is_modified)
                            ->isTrue

                        ->array($this->testedInstance->json_serialize)
                            ->hasSize(1)
                            ->hasKey('zip_code')
                            ->string['zip_code']
                                ->isEqualTo($zip)
        ;
    }

    public function testSetCvc()
    {
        for ($index = 0; $index < 5; $index++) {
            $cvc = substr(uniqid(), 0, $index);

            if ($index === 3) {
                $this
                    ->assert('camelCase method')
                        ->variable($this->newTestedInstance->getCvc())
                            ->isNull

                        ->object($this->testedInstance->setCvc($cvc))
                            ->isTestedInstance

                        ->string($this->testedInstance->getCvc())
                            ->isIdenticalTo($cvc)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('cvc')
                            ->string['cvc']
                                ->isEqualTo($cvc)

                    ->assert('snake_case method')
                        ->variable($this->newTestedInstance->get_cvc())
                            ->isNull

                        ->object($this->testedInstance->set_cvc($cvc))
                            ->isTestedInstance

                        ->string($this->testedInstance->get_cvc())
                            ->isIdenticalTo($cvc)

                        ->boolean($this->testedInstance->is_modified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('cvc')
                            ->string['cvc']
                                ->isEqualTo($cvc)

                    ->assert('as property')
                        ->variable($this->newTestedInstance->cvc)
                            ->isNull

                        ->if($this->testedInstance->cvc = $cvc)
                        ->then
                            ->string($this->testedInstance->cvc)
                                ->isIdenticalTo($cvc)

                            ->boolean($this->testedInstance->isModified)
                                ->isTrue

                            ->array($this->testedInstance->jsonSerialize())
                                ->hasSize(1)
                                ->hasKey('cvc')
                                ->string['cvc']
                                    ->isEqualTo($cvc)
                ;
            } else {
                $this
                    ->if($this->newTestedInstance)
                    ->then
                        ->exception(function () use ($cvc) {
                            $this->testedInstance->setCvc($cvc);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidCardCvcException::class)
                            ->message
                                ->isIdenticalTo('A valid cvc must have 3 characters.')

                        ->exception(function () use ($cvc) {
                            $this->testedInstance->set_cvc($cvc);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidCardCvcException::class)
                            ->message
                                ->isIdenticalTo('A valid cvc must have 3 characters.')

                        ->exception(function () use ($cvc) {
                            $this->testedInstance->cvc = $cvc;
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidCardCvcException::class)
                            ->message
                                ->isIdenticalTo('A valid cvc must have 3 characters.')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse
                ;
            }
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
                    ->isInstanceOf(Stancer\Exceptions\InvalidNameException::class)
                    ->message
                        ->isIdenticalTo('A valid name must be between 3 and 64 characters.')

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
            ->assert('Accept valid card, camelCase method')
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

            ->assert('Accept valid card, snake_case method')
                ->given($this->newTestedInstance)
                ->and($last = substr((string) $number, -4))
                ->then
                    ->variable($this->testedInstance->get_number())
                        ->isNull

                    ->variable($this->testedInstance->get_last4())
                        ->isNull

                    ->object($this->testedInstance->set_number((string) $number))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_number())
                        ->isIdenticalTo((string) $number)

                    ->string($this->testedInstance->get_last4())
                        ->isIdenticalTo($last)

                    ->boolean($this->testedInstance->is_modified())
                        ->isTrue

            ->assert('Accept valid card, using properties')
                ->given($this->newTestedInstance)
                ->and($last = substr((string) $number, -4))
                ->then
                    ->variable($this->testedInstance->number)
                        ->isNull

                    ->variable($this->testedInstance->last4)
                        ->isNull

                    ->if($this->testedInstance->number = (string) $number)
                    ->then
                        ->string($this->testedInstance->number)
                            ->isIdenticalTo((string) $number)

                        ->string($this->testedInstance->last4)
                            ->isIdenticalTo($last)

                        ->boolean($this->testedInstance->isModified)
                            ->isTrue

            ->assert('Throw exception if invalid / Does not contains numbers')
                ->given($this->newTestedInstance)
                ->and($badNumber = preg_replace('/\d/', '', uniqid() . 'x' . uniqid()))
                ->then
                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->setNumber($badNumber);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCardNumberException::class)
                        ->message
                            ->isIdenticalTo('"' . $badNumber . '" is not a valid credit card number.')

                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->set_number($badNumber);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCardNumberException::class)
                        ->message
                            ->isIdenticalTo('"' . $badNumber . '" is not a valid credit card number.')

                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->number = $badNumber;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCardNumberException::class)
                        ->message
                            ->isIdenticalTo('"' . $badNumber . '" is not a valid credit card number.')

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

            ->assert('Throw exception if invalid / Invalid')
                ->given($this->newTestedInstance)
                ->and($badNumber = $number + 1)
                ->then
                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->setNumber((string) $badNumber);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCardNumberException::class)
                        ->message
                            ->isIdenticalTo('"' . $badNumber . '" is not a valid credit card number.')

                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->set_number((string) $badNumber);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCardNumberException::class)
                        ->message
                            ->isIdenticalTo('"' . $badNumber . '" is not a valid credit card number.')

                    ->exception(function () use ($badNumber) {
                        $this->testedInstance->number = (string) $badNumber;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCardNumberException::class)
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
