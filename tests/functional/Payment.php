<?php

namespace ild78\Tests\functional;

use ild78;
use ild78\Payment as testedClass;

/**
 * @namespace \Tests\functional
 */
class Payment extends TestCase
{
    use ild78\Tests\Provider\Currencies;
    use ild78\Tests\Provider\Network;

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
            ->given(ild78\Api\Config::init(['stest_' . bin2hex(random_bytes(12))])->setHost(getenv('API_HOST')))
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
                ->if($this->newTestedInstance($id = 'paym_' . bin2hex(random_bytes(12))))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getAmount();
                    })
                        ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('No such payment ' . $id)

            ->assert('Get test payment')
                ->if($this->newTestedInstance('paym_FQgpGVJpyGPVJVIuQtO3zy6i'))
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
                        ->isIdenticalTo('card_nsA0eap90E6HRod6j54pnVWg')

                    ->object($customer = $this->testedInstance->getCustomer())
                        ->isInstanceOf(ild78\Customer::class)

                    ->string($customer->getId())
                        ->isIdenticalTo('cust_6FbQaYtxjADzerqdO5gs79as')
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

    /**
     * @dataProvider currencyDataProvider
     */
    public function testSave($currency)
    {
        $this
            ->assert('With a card')
                ->given($amount = rand(50, 99999))
                ->and($description = vsprintf('Automatic test, %.02f %s', [
                    $amount / 100,
                    $currency,
                ]))

                ->if($card = new ild78\Card)
                ->and($card->setNumber($this->getValidCardNumber()))
                ->and($card->setExpirationMonth(rand(1, 12)))
                ->and($card->setExpirationYear(date('Y') + rand(1, 5)))
                ->and($card->setCvc((string) rand(100, 999)))

                ->if($customer = new ild78\Customer)
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe@example.com'))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setCard($card))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setDescription($description))

                ->then
                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('paym_')
                        ->hasLength(29)

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->hasDay(date('d'))

                    ->string($this->testedInstance->getMethod())
                        ->isIdenticalTo('card')

                    ->string($card->getId())
                        ->startWith('card_')
                        ->hasLength(29)

                    ->dateTime($card->getCreationDate())
                        ->hasDay(date('d'))

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

                    ->dateTime($customer->getCreationDate())
                        ->hasDay(date('d'))

            ->assert('With authentication')
                ->given($amount = rand(50, 99999))
                ->and($description = vsprintf('Automatic auth test, %.02f %s', [
                    $amount / 100,
                    $currency,
                ]))

                ->if($card = new ild78\Card)
                ->and($card->setNumber($this->getValidCardNumber()))
                ->and($card->setExpirationMonth(rand(1, 12)))
                ->and($card->setExpirationYear(date('Y') + rand(1, 5)))
                ->and($card->setCvc((string) rand(100, 999)))

                ->if($customer = new ild78\Customer)
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe@example.com'))

                ->if($url = 'https://www.example.org?' . uniqid())

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setAuth($url))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setCard($card))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setDescription($description))

                // You may not need to do that, we will use SERVER_ADDR and SERVER_PORT environment variable
                //  as IP and port (they are populated by Apache or nginx)
                ->if($ip = $this->ipDataProvider()[0])
                ->and($port = rand(1, 65535))
                ->and($this->testedInstance->setDevice(new ild78\Device(['ip' => $ip, 'port' => $port])))

                ->then
                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('paym_')
                        ->hasLength(29)

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->hasDay(date('d'))

                    ->string($this->testedInstance->getMethod())
                        ->isIdenticalTo('card')

                    ->variable($this->testedInstance->getStatus())
                        ->isNull

                    ->string($card->getId())
                        ->startWith('card_')
                        ->hasLength(29)

                    ->dateTime($card->getCreationDate())
                        ->hasDay(date('d'))

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

                    ->dateTime($customer->getCreationDate())
                        ->hasDay(date('d'))

                    ->object($auth = $this->testedInstance->getAuth())
                        ->isInstanceOf(ild78\Auth::class)

                    ->string($auth->getReturnUrl())
                        ->isIdenticalTo($url)

                    ->string($auth->getRedirectUrl())
                        ->startWith('https://3ds.')

                    ->string($auth->getStatus())
                        ->isIdenticalTo(ild78\Auth\Status::AVAILABLE)

            ->assert('For payment page')
                ->given($amount = rand(50, 99999))
                ->and($description = vsprintf('Non authenticated payment page test, %.02f %s', [
                    $amount / 100,
                    $currency,
                ]))

                ->if($customer = new ild78\Customer)
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe@example.com'))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setDescription($description))

                ->then
                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('paym_')
                        ->hasLength(29)

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->hasDay(date('d'))

                    ->variable($this->testedInstance->getMethod())
                        ->isNull

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

                    ->dateTime($customer->getCreationDate())
                        ->hasDay(date('d'))

            ->assert('For payment page with authentication')
                ->given($amount = rand(50, 99999))
                ->and($description = vsprintf('Authenticated payment page test, %.02f %s', [
                    $amount / 100,
                    $currency,
                ]))

                ->if($customer = new ild78\Customer)
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe@example.com'))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setAuth(true))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setDescription($description))

                ->then
                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('paym_')
                        ->hasLength(29)

                    ->dateTime($this->testedInstance->getCreationDate())
                        ->hasDay(date('d'))

                    ->variable($this->testedInstance->getMethod())
                        ->isNull

                    ->variable($this->testedInstance->getStatus())
                        ->isNull

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

                    ->dateTime($customer->getCreationDate())
                        ->hasDay(date('d'))

                    ->object($auth = $this->testedInstance->getAuth())
                        ->isInstanceOf(ild78\Auth::class)

                    ->variable($auth->getReturnUrl())
                        ->isNull

                    ->variable($auth->getRedirectUrl())
                        ->isNull

                    ->string($auth->getStatus())
                        ->isIdenticalTo(ild78\Auth\Status::REQUESTED)
        ;
    }
}
