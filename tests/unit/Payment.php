<?php

namespace ild78\tests\unit;

use atoum;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ild78;
use ild78\Api;
use ild78\Card;
use ild78\Customer;
use ild78\Exceptions;
use ild78\Payment as testedClass;
use ild78\Sepa;
use mock;

class Payment extends atoum
{
    public function currencyDataProvider()
    {
        return [
            'EUR',
            'USD',
            'GBP',
        ];
    }

    public function responseMessageDataProvider()
    {
        $datas = [
            '00' => ['00', 'OK'],
            '05' => ['05', 'Do not honor'],
            '41' => ['41', 'Lost card'],
            '42' => ['42', 'Stolen card'],
            '51' => ['51', 'Insufficient funds'],
        ];

        do {
            $key = substr(uniqid(), -2);
        } while (array_key_exists($key, $datas));

        $datas[$key] = [$key, 'Unknown'];

        return $datas;
    }

    public function testCharge()
    {
        $this
            ->if($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($this->calling($client)->request = $response)
            ->and($config = Api\Config::init(uniqid()))
            ->and($config->setHttpClient($client))

            ->assert('Test with a card token')
                ->given($options = [
                    'amount' => rand(50, 99999),
                    'currency' => 'eur',
                    'description' => 'Stripe compatible charge',
                    'source' => 'card_' . uniqid(),
                ])
                ->and($json = [
                    'amount' => $options['amount'],
                    'currency' => $options['currency'],
                    'description' => $options['description'],
                    'card' => $options['source'],
                ])

                ->if($this->calling($response)->getBody = json_encode($json))
                ->then
                    ->object($obj = testedClass::charge($options))
                        ->isInstanceOf(testedClass::class)

                    ->integer($obj->getAmount())
                        ->isIdenticalTo($options['amount'])

                    ->object($card = $obj->getCard())
                        ->isInstanceOf(ild78\Card::class)

                    ->string($card->getId())
                        ->isIdenticalTo($options['source'])

            ->assert('Test with a sepa object')
                ->given($options = [
                    'amount' => rand(50, 99999),
                    'currency' => 'eur',
                    'description' => 'Stripe compatible charge',
                    'source' => [
                        'object' => 'bank_account',
                        'account_number' => 'DE91 1000 0000 0123 4567 89',
                        'account_holder_name' => uniqid(),
                    ],
                ])
                ->and($json = [
                    'amount' => $options['amount'],
                    'currency' => $options['currency'],
                    'description' => $options['description'],
                    'sepa' => [
                        'id' => 'sepa_' . uniqid(),
                        'last4' => '6789',
                        'name' => $options['source']['account_holder_name'],
                    ],
                ])

                ->if($this->calling($response)->getBody = json_encode($json))
                ->then
                    ->object($obj = testedClass::charge($options))
                        ->isInstanceOf(testedClass::class)

                    ->integer($obj->getAmount())
                        ->isIdenticalTo($options['amount'])

                    ->object($sepa = $obj->getSepa())
                        ->isInstanceOf(ild78\Sepa::class)

                    ->string($sepa->getIban())
                        ->isIdenticalTo($options['source']['account_number'])

                    ->string($sepa->getName())
                        ->isIdenticalTo($options['source']['account_holder_name'])

            ->assert('Test with a sepa token (in object)')
                ->given($id = 'sepa_' . uniqid())
                ->and($last = substr(uniqid(), 0, 4))
                ->and($options = [
                    'amount' => rand(50, 99999),
                    'currency' => 'eur',
                    'description' => 'Stripe compatible charge',
                    'source' => [
                        'id' => $id,
                    ],
                ])
                ->and($json = [
                    'amount' => $options['amount'],
                    'currency' => $options['currency'],
                    'description' => $options['description'],
                    'sepa' => [
                        'id' => $id,
                        'last4' => $last,
                    ],
                ])

                ->if($this->calling($response)->getBody = json_encode($json))
                ->then
                    ->object($obj = testedClass::charge($options))
                        ->isInstanceOf(testedClass::class)

                    ->integer($obj->getAmount())
                        ->isIdenticalTo($options['amount'])

                    ->object($sepa = $obj->getSepa())
                        ->isInstanceOf(ild78\Sepa::class)

                    ->string($sepa->getId())
                        ->isIdenticalTo($id)

                    ->string($sepa->getLast4())
                        ->isIdenticalTo($last)
        ;
    }

    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('checkout')
        ;
    }

    /**
     * @dataProvider responseMessageDataProvider
     */
    public function testGetResponseMessage($code, $message)
    {
        $this
            ->assert($code . ' / ' . $message)
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->hydrate(['response_code' => $code]))
                ->then
                    ->string($this->testedInstance->getResponseMessage())
                        ->isIdenticalTo($message)
        ;
    }

    /**
     * @dataProvider responseMessageDataProvider
     */
    public function testIsSuccess_IsNotSuccess($code, $message)
    {
        $this
            ->assert($code . ' / ' . $message)
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->hydrate(['response_code' => $code]))
                ->then
                    ->boolean($this->testedInstance->isSuccess())
                        ->isIdenticalTo($code === '00')
                        ->isIdenticalTo(!$this->testedInstance->isNotSuccess())
        ;
    }

    public function testPay()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($this->calling($client)->request = $response)
            ->and($config = Api\Config::init(uniqid()))
            ->and($config->setHttpClient($client))

            ->then
                ->assert('Pay with card')
                    ->if($card = new Card)
                    ->and($card->setCvc(substr(uniqid(), 0, 3)))
                    ->and($card->setExpMonth(rand(1, 12)))
                    ->and($card->setExpYear(date('Y') + rand(1, 10)))
                    ->and($card->setName(uniqid()))
                    ->and($card->setNumber('4111111111111111'))
                    ->and($card->setZipCode(substr(uniqid(), 0, rand(2, 8))))

                    ->if($file = __DIR__ . '/fixtures/payment/create-card.json')
                    ->and($this->calling($response)->getBody = file_get_contents($file))
                    ->then
                        ->object($this->newTestedInstance->pay(rand(50, 9999), 'EUR', $card))
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->once

                ->assert('Pay with SEPA')
                    ->if($sepa = new Sepa)
                    ->and($sepa->setBic('DEUTDEFF')) // Thx Wikipedia
                    ->and($sepa->setIban('DE91 1000 0000 0123 4567 89')) // Thx Wikipedia
                    ->and($sepa->setName(uniqid()))

                    ->if($file = __DIR__ . '/fixtures/payment/create-sepa.json')
                    ->and($this->calling($response)->getBody = file_get_contents($file))
                    ->then
                        ->object($this->newTestedInstance->pay(rand(50, 9999), 'EUR', $sepa))
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->once
        ;
    }

    public function testSave_withCard()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)
            ->and($config = Api\Config::init(uniqid()))
            ->and($config->setHttpClient($client))

            ->if($card = new Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(rand(date('Y'), 3000)))
            ->and($card->setName(uniqid()))
            ->and($card->setNumber($number = '4111111111111111'))
            ->and($card->setZipCode(substr(uniqid(), 0, rand(2, 8))))

            ->if($customer = new Customer)
            ->and($customer->setName(uniqid()))
            ->and($customer->setEmail(uniqid() . '@example.org'))
            ->and($customer->setMobile(uniqid()))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount(rand(100, 999999)))
            ->and($this->testedInstance->setCard($card))
            ->and($this->testedInstance->setCurrency('EUR'))
            ->and($this->testedInstance->setCustomer($customer))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))

            ->if($logger = new mock\ild78\Api\Logger)
            ->and($config->setLogger($logger))
            ->and($logMessage = 'Payment of 1.00 eur with mastercard "4444"')

            ->and($json = json_encode($this->testedInstance))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                ],
                'timeout' => $config->getTimeout(),
            ])
            ->and($location = $this->testedInstance->getUri())
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull
                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $location, $options)
                            ->once

                ->mock($logger)
                    ->call('info')->withArguments($logMessage)->once

                // Payment object
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo('paym_KIVaaHi7G8QAYMQpQOYBrUQE')

                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@1538564253'))

                ->integer($this->testedInstance->getAmount())
                    ->isIdenticalTo(100)

                ->object($this->testedInstance->getCard())
                    ->isIdenticalTo($card)

                ->string($this->testedInstance->getCurrency())
                    ->isIdenticalTo('eur')

                ->object($this->testedInstance->getCustomer())
                    ->isIdenticalTo($customer)

                ->string($this->testedInstance->getDescription())
                    ->isIdenticalTo('le test restfull v1')

                ->variable($this->testedInstance->getOrderId())
                    ->isNull

                // Card object
                ->string($card->getBrand())
                    ->isIdenticalTo('mastercard')

                ->string($card->getCountry())
                    ->isIdenticalTo('US')

                ->integer($card->getExpMonth())
                    ->isIdenticalTo(2)

                ->integer($card->getExpYear())
                    ->isIdenticalTo(2020)

                ->string($card->getId())
                    ->isIdenticalTo('card_xognFbZs935LMKJYeHyCAYUd')

                ->string($card->getLast4())
                    ->isIdenticalTo('4444')

                ->variable($card->getName())
                    ->isNull

                ->string($card->getNumber())
                    ->isIdenticalTo($number) // Number is unchanged in save process

                ->variable($card->getZipCode())
                    ->isNull
        ;
    }

    public function testSave_withSepa()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-sepa.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)
            ->and($config = Api\Config::init(uniqid()))
            ->and($config->setHttpClient($client))

            ->if($sepa = new Sepa)
            ->and($sepa->setBic('DEUTDEFF')) // Thx Wikipedia
            ->and($sepa->setIban('DE91 1000 0000 0123 4567 89')) // Thx Wikipedia
            ->and($sepa->setName(uniqid()))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount(rand(100, 999999)))
            ->and($this->testedInstance->setSepa($sepa))
            ->and($this->testedInstance->setCurrency('EUR'))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))

            ->if($logger = new mock\ild78\Api\Logger)
            ->and($config->setLogger($logger))
            ->and($logMessage = 'Payment of 1.00 eur with IBAN "2606" / BIC "ILADFRPP"')

            ->and($json = json_encode($this->testedInstance))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                ],
                'timeout' => $config->getTimeout(),
            ])
            ->and($location = $this->testedInstance->getUri())
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull
                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $location, $options)
                            ->once

                ->mock($logger)
                    ->call('info')->withArguments($logMessage)->once

                // Payment object
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo('paym_5IptC9R1Wu2wKBR5cjM2so7k')

                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@1538564504'))

                ->integer($this->testedInstance->getAmount())
                    ->isIdenticalTo(100)

                ->object($this->testedInstance->getSepa())
                    ->isInstanceOf($sepa)

                ->string($this->testedInstance->getCurrency())
                    ->isIdenticalTo('eur')

                ->string($this->testedInstance->getDescription())
                    ->isIdenticalTo('le test restfull v1')

                ->variable($this->testedInstance->getOrderId())
                    ->isNull

                // Sepa object
                ->string($sepa->getId())
                    ->isIdenticalTo('sepa_oazGliEo6BuqUlyCzE42hcNp')

                ->string($sepa->getBic())
                    ->isIdenticalTo('ILADFRPP')

                ->string($sepa->getLast4())
                    ->isIdenticalTo('2606')

                ->string($sepa->getName())
                    ->isIdenticalTo('David Coaster')
        ;
    }

    public function testSave_withoutCardOrSepa()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($config = Api\Config::init(uniqid()))
            ->and($config->setHttpClient($client))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount(rand(100, 999999)))
            ->and($this->testedInstance->setCurrency('EUR'))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))

            ->then
                ->exception(function () {
                    $this->testedInstance->save();
                })
                    ->isInstanceOf(Exceptions\MissingPaymentMethodException::class)
                    ->message
                        ->isIdenticalTo('You must provide a valid credit card or SEPA account to make a payment.')

                ->mock($client)
                    ->call('request')
                        ->never
        ;
    }

    public function testSetAmount()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->assert('0 is not a valid amount')
                    ->exception(function () {
                        $this->testedInstance->setAmount(0);
                    })
                        ->isInstanceOf(Exceptions\InvalidAmountException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->assert('49 is not a valid amount')
                    ->exception(function () {
                        $this->testedInstance->setAmount(49);
                    })
                        ->isInstanceOf(Exceptions\InvalidAmountException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->assert('50 is valid')
                    ->object($this->testedInstance->setAmount(50))
                        ->isTestedInstance
                    ->integer($this->testedInstance->getAmount())
                        ->isEqualTo(50)

                ->assert('random value')
                    ->object($this->testedInstance->setAmount($amount = rand(50, 999999)))
                        ->isTestedInstance
                    ->integer($this->testedInstance->getAmount())
                        ->isEqualTo($amount)
        ;
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testSetCurrency($currency)
    {
        $this
            ->given($this->newTestedInstance)
            ->and($fakeCurrency = uniqid())
            ->and($upper = strtoupper($currency))
            ->and($lower = strtolower($currency))
            ->then
                ->assert('Valid currency : ' . $upper)
                    ->variable($this->testedInstance->getCurrency())
                        ->isNull

                    ->object($this->testedInstance->setCurrency($upper))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCurrency())
                        ->isIdenticalTo($lower)

                ->assert('Valid currency : ' . $lower)
                    ->object($this->testedInstance->setCurrency($lower))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCurrency())
                        ->isIdenticalTo($lower)

                ->assert('Invalid currency')
                    ->exception(function () use ($fakeCurrency) {
                        $this->testedInstance->setCurrency($fakeCurrency);
                    })
                        ->isInstanceOf(Exceptions\InvalidCurrencyException::class)
                        ->message
                            ->contains('"' . $fakeCurrency . '" is not a valid currency')
                            ->contains('please use one of the following :')
                            ->contains($upper)
        ;
    }

    public function testSetDescription()
    {
        $this->newTestedInstance;
        $description = '';

        for ($idx = 0; $idx < 70; $idx++) {
            $length = strlen($description);

            if ($length < 3 || $length > 64) {
                $this
                    ->assert($length . ' characters => Not valid')
                        ->exception(function () use ($description) {
                            $this->testedInstance->setDescription($description);
                        })
                            ->isInstanceOf(Exceptions\InvalidDescriptionException::class)
                            ->hasNestedException
                            ->message
                                ->isIdenticalTo('A valid description must be between 3 and 64 characters.')
                ;
            } else {
                $this
                    ->assert($length . ' characters => Valid')
                        ->object($this->testedInstance->setDescription($description))
                            ->isTestedInstance

                        ->string($this->testedInstance->getDescription())
                            ->isIdenticalTo($description)
                ;
            }

            $description .= chr(rand(65, 90));
        }
    }

    public function testSetOrderId()
    {
        $this->newTestedInstance;
        $orderId = '';

        for ($idx = 0; $idx < 30; $idx++) {
            $length = strlen($orderId);

            if ($length < 1 || $length > 24) {
                $this
                    ->assert($length . ' characters => Not valid')
                        ->exception(function () use ($orderId) {
                            $this->testedInstance->setOrderId($orderId);
                        })
                            ->isInstanceOf(Exceptions\InvalidOrderIdException::class)
                            ->hasNestedException
                            ->message
                                ->isIdenticalTo('A valid order ID must be between 1 and 24 characters.')
                ;
            } else {
                $this
                    ->assert($length . ' characters => Valid')
                        ->object($this->testedInstance->setOrderId($orderId))
                            ->isTestedInstance

                        ->string($this->testedInstance->getOrderId())
                            ->isIdenticalTo($orderId)
                ;
            }

            $orderId .= chr(rand(65, 90));
        }
    }
}
