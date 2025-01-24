<?php

namespace Stancer\tests\unit\Payout\Details;

use Stancer;

class Inner extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Currencies;

    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
        ;
    }

    public function testGetAmount()
    {
        $this
            ->if($value = rand(50, 9999999))
            ->then
                ->variable($this->newTestedInstance->getAmount())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setAmount($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "amount".')

                ->integer($this->newTestedInstance->hydrate(['amount' => $value])->getAmount())
                    ->isIdenticalTo($value)

                ->integer($this->testedInstance->amount)
                    ->isIdenticalTo($value)
        ;
    }

    /**
     * @dataProvider cardCurrencyDataProvider
     *
     * @param mixed $currency
     */
    public function testGetCurrency($currency)
    {
        $this
            ->variable($this->newTestedInstance->getCurrency())
                ->isNull

            ->exception(function () use ($currency) {
                $this->newTestedInstance->setCurrency($currency);
            })
                ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                ->message
                    ->isIdenticalTo('You are not allowed to modify "currency".')

            ->string($this->newTestedInstance->hydrate(['currency' => $currency])->getCurrency())
                ->isIdenticalTo($currency)

            ->string($this->testedInstance->currency)
                ->isIdenticalTo($currency)
        ;
    }
}
