<?php

namespace ild78\tests\functional;

use ild78;
use ild78\Payment as testedClass;

/**
 * @namespace \tests\functional
 */
class Payment extends TestCase
{
    protected $order;
    protected $paymentList = [];

    public function beforeTestMethod($testMethod)
    {
        if ($testMethod === 'testList' && !$this->order) {
            $this->order = uniqid();
        }

        return parent::beforeTestMethod($testMethod);
    }

    public function testBadCredential()
    {
        $this
            ->given(ild78\Api\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($this->newTestedInstance(uniqid()))
            ->then
                ->exception(function () {
                    $this->testedInstance->getCard();
                })
                    ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
        ;
    }

    public function testGetData()
    {
        $this
            ->assert('Unknown payment result a 404 exception')
                ->if($this->newTestedInstance($id = md5(uniqid())))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getAmount();
                    })
                        ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('No such payment id ' . $id)

            ->assert('Get test payment')
                ->if($this->newTestedInstance('paym_uyqKGrWvxC7AlsuJq1vlh5FF'))
                ->then
                    ->integer($this->testedInstance->getAmount())
                        ->isIdenticalTo(7810)

                    ->string($this->testedInstance->getCurrency())
                        ->isIdenticalTo('usd')

                    ->string($this->testedInstance->getDescription())
                        ->isIdenticalTo('Automatic test, 78.10 USD')

                    ->string($this->testedInstance->getMethod())
                        ->isIdenticalTo('card')

                    ->object($card = $this->testedInstance->getCard())
                        ->isInstanceOf(ild78\Card::class)

                    ->string($card->getId())
                        ->isIdenticalTo('card_nc1Xikd2ihaz1n5OjRciHoZK')

                    ->object($customer = $this->testedInstance->getCustomer())
                        ->isInstanceOf(ild78\Customer::class)

                    ->string($customer->getId())
                        ->isIdenticalTo('cust_Ptlig1Zc0ln17OHqeANRdHHU')
        ;
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testList($currency)
    {
        $this
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount = rand(50, 10000)))
            ->and($this->testedInstance->setDescription(sprintf('Automatic test for list, %.02f %s', $amount / 100, $currency)))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setCard($card = new ild78\Card))
            ->and($this->testedInstance->setOrderId($this->order))
            ->and($card->setNumber($this->getValidCardNumber()))
            ->and($card->setExpirationMonth(rand(1, 12)))
            ->and($card->setExpirationYear(date('Y') + rand(1, 5)))
            ->and($card->setCvc((string) rand(100, 999)))
            ->and($this->testedInstance->setCustomer($customer = new ild78\Customer))
            ->and($customer->setName('John Doe'))
            ->and($customer->setEMail('john.doe@example.com'))
            ->and($this->testedInstance->save())
            ->and(array_push($this->paymentList, $this->testedInstance))
            ->then
                ->generator($gen = testedClass::list(['order_id' => $this->order]))
        ;

        $methods = [
            'getId',
            'getAmount',
            'getDescription',
            'getCurrency',
            'getOrderId',
        ];
        $cust = [
            'getId',
            'getEmail',
            'getMobile',
            'getName',
        ];

        foreach ($gen as $idx => $object) {
            $this
                ->object($object)
                    ->isInstanceOfTestedClass
                ->string($object->getCard()->getId())
                    ->isEqualTo($this->paymentList[$idx]->getCard()->getId())
            ;

            foreach ($methods as $method) {
                $this
                    ->variable($object->{$method}())
                        ->isIdenticalTo($this->paymentList[$idx]->{$method}())
                ;
            }

            foreach ($cust as $method) {
                $this
                    ->variable($object->getCustomer()->{$method}())
                        ->isIdenticalTo($this->paymentList[$idx]->getCustomer()->{$method}())
                ;
            }
        }
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testPay($currency)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setAmount($amount = rand(50, 10000)))
                    ->isTestedInstance

                ->object($this->testedInstance->setDescription(sprintf('Automatic test, %.02f %s', $amount / 100, $currency)))
                    ->isTestedInstance

                ->object($this->testedInstance->setCurrency($currency))
                    ->isTestedInstance

                ->object($this->testedInstance->setCard($card = new ild78\Card))
                    ->isTestedInstance

                ->object($card->setNumber($this->getValidCardNumber()))
                    ->isInstanceOf(ild78\Card::class)

                ->object($card->setExpirationMonth(rand(1, 12)))
                    ->isInstanceOf(ild78\Card::class)

                ->object($card->setExpirationYear(date('Y') + rand(1, 5)))
                    ->isInstanceOf(ild78\Card::class)

                ->object($card->setCvc((string) rand(100, 999)))
                    ->isInstanceOf(ild78\Card::class)

                ->object($this->testedInstance->setCustomer($customer = new ild78\Customer))
                    ->isTestedInstance

                ->object($customer->setName('John Doe'))
                    ->isInstanceOf(ild78\Customer::class)

                ->object($customer->setEMail('john.doe@example.com'))
                    ->isInstanceOf(ild78\Customer::class)

                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->string($this->testedInstance->getId())
        ;
    }
}
