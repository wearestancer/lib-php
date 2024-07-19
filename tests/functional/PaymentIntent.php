<?php

namespace Stancer\tests\functional\PaymentIntent;

use Stancer;
use Stancer\Tests\atoum;
use Stancer\Tests\functional\TestCase;

/**
 * @namespace tests\functional
 *
 * @internal
 */
class PaymentIntent extends TestCase
{
    use Stancer\Tests\Provider\Currencies;
    use Stancer\Tests\Provider\Network;

    public function testGetData()
    {
        $this
            ->assert('We test Payments Intents')
            ->if($this->newTestedInstance())
            ->then
                ->object($this->testedInstance)
                ->isInstanceOf(\Stancer\PaymentIntent::class)
        ;
        $this
            ->assert('Unkwown payment intent result a 404 exception')
                ->if($this->newTestedInstance($id = 'pi_' . $this->getRandomString(24)))
                ->then
                    ->string($this->testedInstance->getId())
                    ->isIdenticalTo($id)
                    ->exception(function () {
                        $this->testedInstance->getAmount();
                    })
                        ->isInstanceOf(Stancer\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo($this->getNotFoundExceptionMessage($id, 'Payment intent'))
            ->assert('Get A Payment Intent')
                ->if($this->newTestedInstance($id = 'pi_cFGdU9TtJfU9NbhMLGjyMXCq'))
                ->then

                    ->string($this->testedInstance->getCustomer()->getId())
                        ->isEqualTo('cust_yKdNid3wEcUkHt1oNboYGg3D')

                    ->string($this->testedInstance->getCard()->getId())
                        ->isIdenticalTo('card_XZ8h7vcnEIecJkDdlnI5gyD5')

                    // test Created At when implemented.

                      ->variable($this->testedInstance->getCreatedAt())
                      ->isEqualTo(\DateTimeImmutable::createFromFormat('U', 1718635874))

                     ->integer($this->testedInstance->getAmount())
                        ->isIdenticalTo(3000)

                    ->Boolean($this->testedInstance->getCapture())
                        ->isTrue

                    ->object($this->testedInstance->getCurrency())
                        ->isEqualTo(Stancer\Currency::EUR)

                    ->string($this->testedInstance->getDescription())
                        ->isIdenticalTo('test get pi')

                    // Because we have an array of Enum, the Array should be EqualTo [enum::card,enum::sepa]
                    // But Atoum doesn't check Enums correctly
                    ->array($this->testedInstance->getMethodsAllowed())
                        ->contains(Stancer\Payment\MethodsAllowed::CARD)
                        ->contains(Stancer\Payment\MethodsAllowed::SEPA)

                    ->object($this->testedInstance->getStatus())
                        ->isEqualTo(Stancer\PaymentIntent\Status::REQUIRE_AUTHENTICATION)

                    ->object($this->testedInstance->getThreeds())
                        ->isEqualTo(Stancer\ThreeDomainsSecure\Status::REQUIRED)

                    ->string($this->testedInstance->getUrl())
                        ->isIdenticalTo('https://payment.stancer.com/test_pi_cFGdU9TtJfU9NbhMLGjyMXCq')
        ;
    }

    public function testSend()
    {
        $amount = $this->getRandomInteger(50, 100);
        $desc = $this->getRandomString(3, 64);
        $methods_allowed = [Stancer\Payment\MethodsAllowed::CARD];
        $capture = false;
        $threeds = Stancer\ThreeDomainsSecure\Status::NONE;
        $name = 'test' . $this->getRandomString(4);
        $email = $name . '@test.com';
        $customer = new Stancer\Customer(['email' => $email, 'name' => $name]);
        $this
            ->given($this->newTestedInstance())
            ->assert('create a payment intent')
                ->object($this->testedInstance->setAmount($amount))
                    ->isTestedInstance

                ->object($this->testedInstance->setCurrency('EUR'))
                    ->isTestedInstance

                ->object($this->testedInstance->setDescription($desc))
                    ->isTestedInstance

                ->object($this->testedInstance->setMethodsAllowed($methods_allowed))
                    ->isTestedInstance

                ->object($this->testedInstance->setCapture($capture))
                    ->isTestedInstance

                ->object($this->testedInstance->setThreeds($threeds))
                    ->isTestedInstance

                ->object($this->testedInstance->setCustomer($customer))
                    ->isTestedInstance

                ->object($this->testedInstance->send())
                    ->isTestedInstance

                ->string($this->testedInstance->getId())
        ;
    }

    public function testPaymentIntent()
    {
        $currency = 'eur';
        $this
            ->assert('With authentication')
                ->given($amount = rand(50, 99999))
                ->and($description = vsprintf('Automatic auth test, %.02f %s', [
                    $amount / 100,
                    $currency,
                ]))

                ->if($card = new Stancer\Card())
                ->and($card->setNumber($this->getValidCardNumber()))
                ->and($card->setExpirationMonth(rand(1, 12)))
                ->and($card->setExpirationYear(date('Y') + rand(1, 5)))
                ->and($card->setCvc((string) rand(100, 999)))

                ->if($customer = new Stancer\Customer())
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe' . $this->getRandomString(10) . '@example.com'))

                ->if($threeds = Stancer\ThreeDomainsSecure\Status::REQUIRED)

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->set3ds($threeds))
                ->and($this->testedInstance->setCard($card))
                ->and($this->testedInstance->setCapture(false))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setDescription($description))
                ->and($this->testedInstance->SetReturnUrl($url = 'https://perdu.com'))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('pi_')
                        ->hasLength(27)

                     ->dateTime($this->testedInstance->getCreationDate())
                         ->hasDay(date('d'))

                    ->object($this->testedInstance->getStatus())
                        ->isEqualTo(Stancer\PaymentIntent\Status::REQUIRE_AUTHENTICATION)

                    ->string($card->getId())
                        ->startWith('card_')
                        ->hasLength(29)

                    ->dateTime($card->getCreationDate())
                        ->hasYear(date('Y'))

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

                    ->dateTime($customer->getCreationDate())
                        ->hasDay(date('d'))

                    ->string($this->testedInstance->getReturnUrl())
                        ->isIdenticalTo($url)

                    ->string($this->testedInstance->getUrl())
                        ->startWith('https://payment.stancer.com')

                    ->object($this->testedInstance->getThreeds())
                        ->isIdenticalTo(Stancer\ThreeDomainsSecure\Status::REQUIRED)

            ->assert('For payment page')
                ->given($amount = rand(50, 100))
                ->and($description = vsprintf('Non authenticated payment page test, %.02f %s', [
                    $amount / 100,
                    $currency,
                ]))

                ->if($customer = new Stancer\Customer())
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe' . $this->getRandomString(10) . '@example.com'))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setDescription($description))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('pi_')
                        ->hasLength(27)

                    ->dateTime($this->testedInstance->getCreationDate())
                         ->hasDay(date('d'))

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

                    ->dateTime($customer->getCreationDate())
                        ->hasDay(date('d'))

            ->assert('For payment page with authentication')
                ->given($amount = rand(50, 100))
                ->and($description = vsprintf('Authenticated payment page test, %.02f %s', [
                    $amount / 100,
                    $currency,
                ]))

                ->if($customer = new Stancer\Customer())
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe' . $this->getRandomString(10) . '@example.com'))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setDescription($description))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('pi_')
                        ->hasLength(27)

                    ->dateTime($this->testedInstance->getCreationDate())
                         ->hasDay(date('d'))

                    ->object($this->testedInstance->getStatus())
                        ->isEqualTo(Stancer\PaymentIntent\Status::REQUIRE_PAYMENT_METHOD)

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

                    ->dateTime($customer->getCreationDate())
                        ->hasDay(date('d'))

            ->assert('Patch card and status')
                ->given($this->newTestedInstance)
                ->and($amount = rand(50, 100))
                ->and($description = sprintf('Automatic test, PATCH card, %.02f %s', $amount / 100, $currency))

                ->if($customer = new Stancer\Customer())
                ->and($customer->setName('John Doe'))
                ->and($customer->setEmail('john.doe' . $this->getRandomString(10) . '@example.com'))

                ->if($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setDescription($description))
                ->and($this->testedInstance->setCustomer($customer))

                ->if($card = new Stancer\Card())
                ->and($card->setNumber($this->getValidCardNumber()))
                ->and($card->setExpirationMonth(rand(1, 12)))
                ->and($card->setExpirationYear(rand(1, 15) + date('Y')))
                ->and($card->setCvc((string) rand(100, 999)))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->startWith('pi_')
                        ->hasLength(27)

                    ->variable($this->testedInstance->getCard())
                        ->isNull

                    ->variable($this->testedInstance->getSepa())
                        ->isNull

                    ->object($this->testedInstance->getStatus())
                        ->isEqualTo(Stancer\PaymentIntent\Status::REQUIRE_PAYMENT_METHOD)

                    ->object($this->testedInstance->setCard($card))
                        ->isTestedInstance

                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($this->testedInstance->getCard()->getId())
                        ->isIdenticalTo($card->getId())

                    ->variable($this->testedInstance->getSepa())
                        ->isNull

                    ->string($card->getId())
                        ->startWith('card_')
                        ->hasLength(29)

                    ->object($this->testedInstance->getStatus())
                        ->isEqualTo(Stancer\PaymentIntent\Status::REQUIRE_AUTHENTICATION)

            ->assert('With order ID')
                ->given($this->newTestedInstance)
                ->and($amount = rand(50, 100))
                ->and($description = sprintf('Automatic test, with unique ID, %.02f %s', $amount / 100, $currency))
                ->and($uniqueID = $this->getRandomString(10, 20))

                ->if($name = 'Pickle Rick')
                ->and($email = 'pickle.rick' . $this->getRandomString(10) . '@example.com')
                ->and($mobile = $this->getRandomNumber())

                ->if($card = new Stancer\Card())
                ->and($card->setNumber($this->getValidCardNumber()))
                ->and($card->setExpirationMonth(rand(1, 12)))
                ->and($card->setExpirationYear(rand(1, 15) + date('Y')))
                ->and($card->setCvc((string) rand(100, 999)))

                ->if($customer = new Stancer\Customer())
                ->and($customer->setName($name))
                ->and($customer->setEmail($email))
                ->and($customer->setMobile($mobile))

                ->if($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setCard($card))
                ->and($this->testedInstance->setCapture(false))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setDescription($description))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setOrderId($uniqueID))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($id = $this->testedInstance->getId())
                        ->startWith('pi_')
                        ->hasLength(27)

                    ->string($this->testedInstance->getOrderId())
                        ->isIdenticalTo($uniqueID)

                    ->object($this->testedInstance->getCard())
                        ->isInstanceOf(Stancer\Card::class)
                    ->string($this->testedInstance->getCard()->getId())
                        ->isIdenticalTo($card->getId())

                    ->string($card->getId())
                        ->startWith('card_')
                        ->hasLength(29)

                    ->object($this->testedInstance->getCustomer())
                        ->isInstanceOf(Stancer\Customer::class)

                    ->string($this->testedInstance->getCustomer()->getId())
                        ->isIdenticalTo($customer->getId())

                    ->string($customerID = $customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)

            ->assert('Allow duplicate customer')
                ->given($this->newTestedInstance)
                ->and($amount = rand(50, 100))
                ->and($description = sprintf('Automatic test, duplicate customer, %.02f %s', $amount / 100, $currency))

                ->if($card = new Stancer\Card())
                ->and($card->setNumber($this->getValidCardNumber()))
                ->and($card->setExpirationMonth(rand(1, 12)))
                ->and($card->setExpirationYear(rand(1, 15) + date('Y')))
                ->and($card->setCvc((string) rand(100, 999)))

                ->if($customer = new Stancer\Customer())
                ->and($customer->setName($name)) // From previous test
                ->and($customer->setEmail($email)) // From previous test
                ->and($customer->setMobile($mobile)) // From previous test

                ->if($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->setCard($card))
                ->and($this->testedInstance->setCurrency($currency))
                ->and($this->testedInstance->setDescription($description))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($this->testedInstance->setCapture(false))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($id = $this->testedInstance->getId())
                        ->startWith('pi_')
                        ->hasLength(27)

                    ->string($this->testedInstance->getCard()->getId())
                        ->isIdenticalTo($card->getId())

                    ->string($card->getId())
                        ->startWith('card_')
                        ->hasLength(29)

                    ->string($this->testedInstance->getCustomer()->getId())
                        ->isIdenticalTo($customer->getId())

                    ->string($customer->getId())
                        ->startWith('cust_')
                        ->hasLength(29)
                        // ->isIdenticalTo($customerID) // From previous test
        ;
    }
}
