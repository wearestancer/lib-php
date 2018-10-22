<?php

namespace ild78\tests\unit;

use atoum;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
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

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount(rand(100, 999999)))
            ->and($this->testedInstance->setCard($card))
            ->and($this->testedInstance->setCurrency('EUR'))
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
            ])
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull
                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $this->testedInstance->getEndpoint(), $options)
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
                    ->isInstanceOf($card)

                ->string($this->testedInstance->getCurrency())
                    ->isIdenticalTo('eur')

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
            ])
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull
                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $this->testedInstance->getEndpoint(), $options)
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
