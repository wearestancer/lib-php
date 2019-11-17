<?php

namespace ild78\tests\unit;

use DateTime;
use DateInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ild78;
use ild78\Payment as testedClass;
use mock;

class Payment extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Currencies;
    use ild78\Tests\Provider\Network;

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
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
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

                    ->string($sepa->getFormattedIban())
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
            ->currentlyTestedClass()
                ->isSubclassOf(ild78\Core\AbstractObject::class)
                ->hasTrait(ild78\Traits\AmountTrait::class)
                ->hasTrait(ild78\Traits\SearchTrait::class)
        ;
    }

    public function testDelete()
    {
        $this
            ->exception(function () {
                $this->newTestedInstance(uniqid())->delete();
            })
                ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                ->message
                    ->isIdenticalTo('You are not allowed to delete a payment, you need to refund it instead.')
        ;
    }

    public function testFilterListParams()
    {
        $gen = function ($length) {
            $text = '';

            for ($i = 0; $i < $length; $i++) {
                $text .= chr(rand(65, 122));
            }

            return $text;
        };

        $this
            ->given($this->newTestedInstance)
            ->and($order = ['order_id' => uniqid()])
            ->and($unique = ['unique_id' => uniqid()])
            ->then
                ->assert('Remove unknown')
                    ->array($this->testedInstance->filterListParams([uniqid() => uniqid()]))
                        ->isEmpty

                ->assert('Allow order_id')
                    ->array($this->testedInstance->filterListParams($order))
                        ->isIdenticalTo($order)

                ->assert('Validate order_id')
                    ->exception(function () {
                        $this->testedInstance->filterListParams(['order_id' => rand(1, PHP_INT_MAX)]);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidSearchOrderIdFilterException::class)
                        ->message
                            ->isIdenticalTo('Order ID must be a string.')

                    ->exception(function () use ($gen) {
                        $this->testedInstance->filterListParams(['order_id' => $gen(37)]);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidSearchOrderIdFilterException::class)
                        ->message
                            ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->assert('Allow unique_id')
                    ->array($this->testedInstance->filterListParams($unique))
                        ->isIdenticalTo($unique)

                ->assert('Validate unique_id')
                    ->exception(function () {
                        $this->testedInstance->filterListParams(['unique_id' => rand(1, PHP_INT_MAX)]);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidSearchUniqueIdFilterException::class)
                        ->message
                            ->isIdenticalTo('Unique ID must be a string.')

                    ->exception(function () use ($gen) {
                        $this->testedInstance->filterListParams(['unique_id' => $gen(37)]);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidSearchUniqueIdFilterException::class)
                        ->message
                            ->isIdenticalTo('A valid unique ID must be between 1 and 36 characters.')
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

    public function testGetPaymentPageUrl()
    {
        $this
            ->given($secret = 'stest_' . bin2hex(random_bytes(12)))
            ->and($public = 'ptest_' . bin2hex(random_bytes(12)))
            ->and($config = ild78\Config::init([$secret]))

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->currencyDataProvider()[0])

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setCurrency($currency))

            ->if($return = 'https://www.example.org?' . uniqid())
            ->and($url = vsprintf('https://%s/%s/', [
                str_replace('api', 'payment', $config->getHost()),
                $public
            ]))

            ->if($lang = uniqid())
            ->and($params = [
                'lang' => $lang,
                uniqid() => uniqid(),
            ])

            ->then
                ->exception(function () {
                    $this->testedInstance->getPaymentPageUrl();
                })
                    ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid public API key for development.')

                ->object($config->setKeys([$public, $secret]))

                ->exception(function () {
                    $this->testedInstance->getPaymentPageUrl();
                })
                    ->isInstanceOf(ild78\Exceptions\MissingReturnUrlException::class)
                    ->message
                        ->isIdenticalTo('You must provide a return URL before asking for the payment page.')

                ->object($this->testedInstance->setReturnUrl($return))
                    ->isTestedInstance

                ->exception(function () {
                    $this->testedInstance->getPaymentPageUrl();
                })
                    ->isInstanceOf(ild78\Exceptions\MissingPaymentIdException::class)
                    ->message
                        ->isIdenticalTo('A payment ID is mandatory to obtain a payment page URL. Maybe you forgot to save the payment.')

                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->string($this->testedInstance->getPaymentPageUrl())
                    ->isIdenticalTo($url . $this->testedInstance->getId())

                ->string($this->testedInstance->getPaymentPageUrl($params))
                    ->isIdenticalTo($url . $this->testedInstance->getId() . '?lang=' . $lang)
        ;
    }

    public function testGetRefundableAmount()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($client = new mock\ild78\Http\Client)
            ->and($config->setHttpClient($client))

            ->if($body = file_get_contents(__DIR__ . '/fixtures/payment/read.json'))
            ->and($responsePayment = new ild78\Http\Response(200, $body))
            ->and($this->calling($client)->request = $responsePayment)

            ->and($paymentData = json_decode($body, true))
            ->and($paid = $paymentData['amount'])
            ->and($id = $paymentData['id'])

            ->if($completeRefund = new ild78\Refund())
            ->and($completeRefund->setAmount($paid))

            ->if($amount = rand(50, $paid))
            ->and($partialRefund = new ild78\Refund())
            ->and($partialRefund->setAmount($amount))

            ->then
                ->assert('All paid amount is refundable if not refund was made')
                    ->integer($this->newTestedInstance($id)->getRefundableAmount())
                        ->isIdenticalTo($paid)

                    ->integer($this->testedInstance->getRefundedAmount())
                        ->isZero

                ->assert('When all was refunded, no more refund is possible')
                    ->integer($this->newTestedInstance($id)->addRefunds($completeRefund)->getRefundableAmount())
                        ->isZero

                    ->integer($this->testedInstance->getRefundedAmount())
                        ->isIdenticalTo($paid)

                ->assert('When one refund was done (' . $amount . ' / ' . $paid . ')')
                    ->integer($this->newTestedInstance($id)->addRefunds($partialRefund)->getRefundableAmount())
                        ->isIdenticalTo($paid - $amount)

                    ->integer($this->testedInstance->getRefundedAmount())
                        ->isIdenticalTo($amount)
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
                ->and($this->testedInstance->hydrate(['response' => $code]))
                ->then
                    ->string($this->testedInstance->getResponseMessage())
                        ->isIdenticalTo($message)
        ;
    }

    public function testGetReturnUrl_SetReturnUrl()
    {
        $this
            ->given($https = 'https://www.example.org/?' . uniqid())
            ->and($http = 'http://www.example.org/?' . uniqid())

            ->if($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getReturnUrl())
                    ->isNull

                ->object($this->testedInstance->setReturnUrl($https))
                    ->isTestedInstance

                ->string($this->testedInstance->getReturnUrl())
                    ->isIdenticalTo($https)

                ->exception(function () use ($http) {
                    $this->testedInstance->setReturnUrl($http);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidUrlException::class)
                    ->message
                        ->isIdenticalTo('You must provide an HTTPS URL.')
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
                ->then
                    ->boolean($this->testedInstance->isSuccess())
                        ->isFalse

                    ->boolean($this->testedInstance->isNotSuccess())
                        ->isFalse

                ->if($this->testedInstance->hydrate(['response' => $code]))
                ->then
                    ->boolean($this->testedInstance->isSuccess())
                        ->isIdenticalTo($code === '00')
                        ->isIdenticalTo(!$this->testedInstance->isNotSuccess())
        ;
    }

    public function testList()
    {
        $this
            ->given($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/list.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))

            ->and($options = [
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ],
                'timeout' => $config->getTimeout(),
            ])

            ->assert('Invalid limit')
                ->exception(function () {
                    testedClass::list(['limit' => 0]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchLimitException::class)
                    ->message
                        ->isIdenticalTo('Limit must be between 1 and 100.')

                ->exception(function () {
                    testedClass::list(['limit' => 101]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchLimitException::class)
                    ->message
                        ->isIdenticalTo('Limit must be between 1 and 100.')

                ->exception(function () {
                    testedClass::list(['limit' => uniqid()]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchLimitException::class)
                    ->message
                        ->isIdenticalTo('Limit must be between 1 and 100.')

            ->assert('Invalid start')
                ->exception(function () {
                    testedClass::list(['start' => -1]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchStartException::class)
                    ->message
                        ->isIdenticalTo('Start must be a positive integer.')

                ->exception(function () {
                    testedClass::list(['start' => uniqid()]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchStartException::class)
                    ->message
                        ->isIdenticalTo('Start must be a positive integer.')

            ->assert('No terms')
                ->exception(function () {
                    testedClass::list([]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchFilterException::class)
                    ->message
                        ->isIdenticalTo('Invalid search filters.')

                ->exception(function () {
                    testedClass::list(['foo' => 'bar']);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchFilterException::class)
                    ->message
                        ->isIdenticalTo('Invalid search filters.')

            ->assert('Invalid created filter')
                ->exception(function () {
                    testedClass::list(['created' => time() + 100]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be in the past.')

                ->exception(function () {
                    $date = new DateTime();
                    $date->add(new DateInterval('P1D'));

                    testedClass::list(['created' => $date]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be in the past.')

                ->exception(function () {
                    testedClass::list(['created' => 0]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be a position integer or a DateTime object.')

                ->exception(function () {
                    testedClass::list(['created' => uniqid()]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be a position integer or a DateTime object.')

            ->assert('Invalid order id filter')
                ->exception(function () {
                    testedClass::list(['order_id' => '']);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchOrderIdFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->exception(function () {
                    testedClass::list(['order_id' => rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchOrderIdFilterException::class)
                    ->message
                        ->isIdenticalTo('Order ID must be a string.')

            ->assert('Invalid unique id filter')
                ->exception(function () {
                    testedClass::list(['unique_id' => '']);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchUniqueIdFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid unique ID must be between 1 and 36 characters.')

                ->exception(function () {
                    testedClass::list(['unique_id' => rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidSearchUniqueIdFilterException::class)
                    ->message
                        ->isIdenticalTo('Unique ID must be a string.')

            ->assert('Make request')
                ->if($limit = rand(1, 100))
                ->and($start = rand(0, PHP_INT_MAX))
                ->and($orderId = uniqid())
                ->and($created = time() - rand(10, 1000000))
                ->and($uniqueId = uniqid())

                ->and($location = $this->newTestedInstance->getUri())
                ->and($terms1 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start,
                    'order_id' => $orderId,
                    'unique_id' => $uniqueId,
                ])
                ->and($location1 = $location . '?' . http_build_query($terms1))

                ->and($terms2 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start + 2, // Forced in json sample
                    'order_id' => $orderId,
                    'unique_id' => $uniqueId,
                ])
                ->and($location2 = $location . '?' . http_build_query($terms2))
                ->then
                    ->generator($gen = testedClass::list($terms1))
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"paym_JnU7xyTGJvxRWZuxvj78qz7e"') // From json sample

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location1, $options)
                                ->once
                            ->withArguments('GET', $location2, $options)
                                ->never

                    ->generator($gen)
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"paym_p5tjCrXHy93xtVtVqvEJoC1c"') // From json sample
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"paym_JnU7xyTGJvxRWZuxvj78qz7e"') // From json sample

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location1, $options)
                                ->once // Called the first time
                            ->withArguments('GET', $location2, $options)
                                ->once

            ->assert('Empty response')
                ->given($body = [
                    'payments' => [],
                    'range' => [
                        'has_more' => false,
                        'limit' => 10,
                    ],
                ])
                ->and($this->calling($response)->getBody = json_encode($body))

                ->if($limit = rand(1, 100))
                ->and($terms = [
                    'limit' => $limit,
                ])
                ->and($query = http_build_query(['limit' => $limit, 'start' => 0]))
                ->and($location = $this->newTestedInstance->getUri() . '?' . $query)
                ->then
                    ->generator($gen = testedClass::list($terms))
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location, $options)
                                ->once

            ->assert('Invalid response')
                ->given($this->calling($response)->getBody = null)

                ->if($limit = rand(1, 100))
                ->and($terms = [
                    'limit' => $limit,
                ])
                ->and($query = http_build_query(['limit' => $limit, 'start' => 0]))
                ->and($location = $this->newTestedInstance->getUri() . '?' . $query)
                ->then
                    ->generator($gen = testedClass::list($terms))
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location, $options)
                                ->once
        ;
    }

    public function testPay()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($this->calling($client)->request = $response)
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))

            ->then
                ->assert('Pay with card')
                    ->if($card = new ild78\Card)
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
                    ->if($sepa = new ild78\Sepa)
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

    public function testRefund()
    {
        $this
            ->given($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($this->calling($client)->request = $response)
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            // Behavior modification are done in assert part to prevent confusion on multiple calls mocking

            ->and($logger = new mock\ild78\Core\Logger)
            ->and($config->setLogger($logger))

            ->if($body = file_get_contents(__DIR__ . '/fixtures/payment/read.json'))
            ->and($paymentData = json_decode($body, true))
            ->and($paid = $paymentData['amount'])

            ->if($amount = rand(50, $paid))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/refund/read.json'))
            ->and($refund1Data = json_decode($body, true))
            ->and($refund1Data['amount'] = $amount)

            ->if($lastPart = $paid - $amount)
            ->and($refund2Data = json_decode($body, true))
            ->and($refund2Data['amount'] = $lastPart)

            ->given($id = 'paym_SKMLflt8NBATuiUzgvTYqsw5') // from fixtures
            ->and($this->newTestedInstance($id))
            ->and($tooMuch = rand($paid + 1, 9999))
            ->and($notEnough = rand(1, 49))
            ->then
                ->assert('Without refunds we get an empty array')
                    ->if($this->calling($response)->getBody = json_encode($paymentData))
                    ->then
                        ->array($this->testedInstance->getRefunds())
                            ->isEmpty

                ->assert('We can not refund more than paid')
                    ->exception(function () use ($tooMuch) {
                        $this->testedInstance->refund($tooMuch);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('You are trying to refund (' . sprintf('%.02f', $tooMuch / 100) . ' EUR) more than paid (34.06 EUR).')

                ->assert('Amount must be greater or equal than 50')
                    ->exception(function () use ($notEnough) {
                        $this->testedInstance->refund($notEnough);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidAmountException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->assert('We can put a refund amount')
                    ->if($this->calling($response)->getBody = json_encode($refund1Data))
                    ->then
                        ->object($this->testedInstance->refund($amount))
                            ->isTestedInstance

                        ->array($refunds = $this->testedInstance->getRefunds())
                            ->object[0]
                                ->isInstanceOf(ild78\Refund::class)
                            ->size
                                ->isEqualTo(1)

                        ->object($refunds[0]->getPayment())
                            ->isTestedInstance

                        ->integer($refunds[0]->getAmount())
                            ->isIdenticalTo($amount)

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse

                        ->boolean($refunds[0]->isModified())
                            ->isFalse

                        ->mock($logger)
                            ->call('info')
                                ->withArguments(sprintf('Refund of %.02f EUR on payment "%s"', $amount / 100, $id))
                                    ->once

                ->assert('We can not refund more than refundable')
                    ->exception(function () use ($paid) {
                        $this->testedInstance->refund($paid);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('You are trying to refund (' . sprintf('%.02f', $paid / 100) . ' EUR) more than paid (34.06 EUR with ' . sprintf('%.02f', $amount / 100) . ' EUR already refunded).')

                ->assert('Without amount we will refund all')
                    ->if($this->calling($response)->getBody = json_encode($refund2Data))
                    ->then
                        ->object($this->testedInstance->refund())
                            ->isTestedInstance

                        ->array($refunds = $this->testedInstance->getRefunds())
                            ->hasSize(2)
                            ->object[0]
                                ->isInstanceOf(ild78\Refund::class)
                            ->object[1]
                                ->isInstanceOf(ild78\Refund::class)

                        ->object($refunds[0]->getPayment())
                            ->isTestedInstance

                        ->integer($refunds[0]->getAmount())
                            ->isIdenticalTo($amount)

                        ->object($refunds[1]->getPayment())
                            ->isTestedInstance

                        ->integer($refunds[1]->getAmount())
                            ->isIdenticalTo($lastPart)

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse

                        ->boolean($refunds[0]->isModified())
                            ->isFalse

                        ->boolean($refunds[1]->isModified())
                            ->isFalse

                        ->mock($logger)
                            ->call('info')
                                ->withArguments(sprintf('Refund of %.02f EUR on payment "%s"', $lastPart / 100, $id))
                                    ->once

                ->assert('We can not refund on unsaved payment')
                    ->exception(function () {
                        $this->newTestedInstance->refund();
                    })
                        ->isInstanceOf(ild78\Exceptions\MissingPaymentIdException::class)
                        ->message
                            ->isIdenticalTo('A payment ID is mandatory. Maybe you forgot to save the payment.')
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
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))

            ->if($card = new ild78\Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(rand(date('Y'), 3000)))
            ->and($card->setName(uniqid()))
            ->and($card->setNumber($number = '4111111111111111'))
            ->and($card->setZipCode(substr(uniqid(), 0, rand(2, 8))))

            ->if($customer = new ild78\Customer)
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

            ->if($logger = new mock\ild78\Core\Logger)
            ->and($config->setLogger($logger))
            ->and($logMessage = 'Payment of 1.00 eur with mastercard "4444"')

            ->and($json = json_encode($this->testedInstance))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
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
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))

            ->if($sepa = new ild78\Sepa)
            ->and($sepa->setBic('DEUTDEFF')) // Thx Wikipedia
            ->and($sepa->setIban('DE91 1000 0000 0123 4567 89')) // Thx Wikipedia
            ->and($sepa->setName(uniqid()))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount(rand(100, 999999)))
            ->and($this->testedInstance->setSepa($sepa))
            ->and($this->testedInstance->setCurrency('EUR'))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))

            ->if($logger = new mock\ild78\Core\Logger)
            ->and($config->setLogger($logger))
            ->and($logMessage = 'Payment of 1.00 eur with IBAN "2606" / BIC "ILADFRPP"')

            ->and($json = json_encode($this->testedInstance))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
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

    public function testSave_authenticatedPayment()
    {
        $_SERVER['SERVER_ADDR'] = $ip = $this->ipDataProvider()[0];
        $_SERVER['SERVER_PORT'] = $port = rand(1, 65535);

        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card-auth.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($amount = rand(10, 99999))
            ->and($currency = $this->currencyDataProvider()[0])
            ->and($description = uniqid())
            ->and($url = 'https://www.example.org?' . uniqid())

            ->if($card = new ild78\Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(rand(date('Y'), 3000)))
            ->and($card->setNumber($number = '5555555555554444'))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setAuth($url))
            ->and($this->testedInstance->setCard($card))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setDescription($description))

            ->and($json = json_encode([
                'amount' => $amount,
                'auth' => [
                    'return_url' => $url,
                    'status' => ild78\Auth\Status::REQUEST,
                ],
                'card' => [
                    'cvc' => $card->getCvc(),
                    'exp_month' => $card->getExpMonth(),
                    'exp_year' => $card->getExpYear(),
                    'number' => $card->getNumber(),
                ],
                'currency' => strtolower($currency),
                'description' => $description,
                'device' => [
                    'ip' => $ip,
                    'port' => $port,
                ],
            ]))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
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

                // Payment object
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo('paym_RMLytyx2xLkdXkATKSxHOlvC')

                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@1567094428'))

                ->integer($this->testedInstance->getAmount())
                    ->isIdenticalTo(1337)

                ->object($this->testedInstance->getCard())
                    ->isIdenticalTo($card)

                ->string($this->testedInstance->getCurrency())
                    ->isIdenticalTo('eur')

                ->string($this->testedInstance->getDescription())
                    ->isIdenticalTo('Auth test')

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

                ->string($card->getNumber())
                    ->isIdenticalTo($number) // Number is unchanged in save process

                // Auth object
                ->object($auth = $this->testedInstance->getAuth())
                    ->isInstanceOf(ild78\Auth::class)

                ->string($auth->getReturnUrl())
                    ->isIdenticalTo('https://www.free.fr')

                ->string($auth->getStatus())
                    ->isIdenticalTo(ild78\Auth\Status::AVAILABLE)

                // Device object
                ->object($device = $this->testedInstance->getDevice())
                    ->isInstanceOf(ild78\Device::class)

                ->string($device->getIp())
                    ->isIdenticalTo('212.27.48.10')

                ->integer($device->getPort())
                    ->isEqualTo(1337)

                ->string($device->getHttpAccept())
                    ->isIdenticalTo('text/html')
        ;
    }

    public function testSave_fullyCustomAuthenticatedPayment()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card-auth.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($amount = rand(10, 99999))
            ->and($currency = $this->currencyDataProvider()[0])
            ->and($description = uniqid())
            ->and($url = 'https://www.example.org?' . uniqid())

            ->if($auth = new ild78\Auth)
            ->and($auth->setReturnUrl($url))

            ->if($ip = $this->ipDataProvider()[0])
            ->and($port = rand(1, 65535))
            ->and($device = new ild78\Device(['ip' => $ip, 'port' => $port]))

            ->if($card = new ild78\Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(rand(date('Y'), 3000)))
            ->and($card->setNumber($number = '5555555555554444'))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setAuth($auth))
            ->and($this->testedInstance->setCard($card))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setDescription($description))
            ->and($this->testedInstance->setDevice($device))

            ->and($json = json_encode([
                'amount' => $amount,
                'auth' => [
                    'return_url' => $url,
                    'status' => ild78\Auth\Status::REQUEST,
                ],
                'card' => [
                    'cvc' => $card->getCvc(),
                    'exp_month' => $card->getExpMonth(),
                    'exp_year' => $card->getExpYear(),
                    'number' => $card->getNumber(),
                ],
                'currency' => strtolower($currency),
                'description' => $description,
                'device' => [
                    'ip' => $ip,
                    'port' => $port,
                ],
            ]))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
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

                // Payment object
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo('paym_RMLytyx2xLkdXkATKSxHOlvC')

                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@1567094428'))

                ->integer($this->testedInstance->getAmount())
                    ->isIdenticalTo(1337)

                ->object($this->testedInstance->getCard())
                    ->isIdenticalTo($card)

                ->string($this->testedInstance->getCurrency())
                    ->isIdenticalTo('eur')

                ->string($this->testedInstance->getDescription())
                    ->isIdenticalTo('Auth test')

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

                ->string($card->getNumber())
                    ->isIdenticalTo($number) // Number is unchanged in save process

                // Auth object
                ->object($this->testedInstance->getAuth())
                    ->isIdenticalTo($auth)

                ->string($auth->getReturnUrl())
                    ->isIdenticalTo('https://www.free.fr')

                ->string($auth->getStatus())
                    ->isIdenticalTo(ild78\Auth\Status::AVAILABLE)

                // Device object
                ->object($this->testedInstance->getDevice())
                    ->isIdenticalTo($device)

                ->string($device->getIp())
                    ->isIdenticalTo('212.27.48.10')

                ->integer($device->getPort())
                    ->isEqualTo(1337)

                ->string($device->getHttpAccept())
                    ->isIdenticalTo('text/html')
        ;
    }

    public function testSave_withoutCardOrSepa()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($customer = new ild78\Customer)
            ->and($customer->setName(uniqid()))
            ->and($customer->setEmail(uniqid() . '@example.org'))
            ->and($customer->setMobile(uniqid()))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->currencyDataProvider()[0])

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setCustomer($customer))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))

            ->if($logger = new mock\ild78\Core\Logger)
            ->and($config->setLogger($logger))
            ->and($logMessage = 'Payment of 100.00 eur without payment method')

            ->and($json = json_encode($this->testedInstance))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
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
                    ->isIdenticalTo('paym_pia9ossoqujuFFbX0HdS3FLi')

                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@1562085759'))

                ->integer($this->testedInstance->getAmount())
                    ->isIdenticalTo(10000)

                ->string($this->testedInstance->getCurrency())
                    ->isIdenticalTo('eur')

                ->object($this->testedInstance->getCustomer())
                    ->isIdenticalTo($customer)

                ->string($this->testedInstance->getDescription())
                    ->isIdenticalTo('Test payment without any card or sepa account')

                ->variable($this->testedInstance->getOrderId())
                    ->isNull

                ->variable($this->testedInstance->getCard())
                    ->isNull

                ->variable($this->testedInstance->getSepa())
                    ->isNull

                ->variable($this->testedInstance->getMethod())
                    ->isNull
        ;
    }

    public function testSave_authenticationAndPaymentPage()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method-auth.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->currencyDataProvider()[0])
            ->and($description = uniqid())

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setAuth(true))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setDescription($description))

            ->and($json = json_encode([
                'amount' => $amount,
                'auth' => [
                    'status' => ild78\Auth\Status::REQUEST,
                ],
                'currency' => strtolower($currency),
                'description' => $description,
            ]))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
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

                // Payment object
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo('paym_RMLytyx2xLkdXkATKSxHOlvC')

                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@1567094428'))

                ->integer($this->testedInstance->getAmount())
                    ->isIdenticalTo(1337)

                ->string($this->testedInstance->getCurrency())
                    ->isIdenticalTo('eur')

                ->string($this->testedInstance->getDescription())
                    ->isIdenticalTo('Auth test')

                ->variable($this->testedInstance->getCard())
                    ->isNull

                ->variable($this->testedInstance->getSepa())
                    ->isNull

                ->variable($this->testedInstance->getMethod())
                    ->isNull

                ->variable($this->testedInstance->getStatus())
                    ->isNull

                ->object($auth = $this->testedInstance->getAuth())
                    ->isInstanceOf(ild78\Auth::class)

                ->variable($auth->getRedirectUrl())
                    ->isNull

                ->variable($auth->getReturnUrl())
                    ->isNull

                ->variable($auth->getStatus())
                    ->isIdenticalTo(ild78\Auth\Status::REQUESTED)
        ;
    }

    public function testSave_status()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($customer = new ild78\Customer)
            ->and($customer->setName(uniqid()))
            ->and($customer->setEmail(uniqid() . '@example.org'))
            ->and($customer->setMobile(uniqid()))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->currencyDataProvider()[0])

            ->if($card = new ild78\Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(rand(date('Y'), 3000)))
            ->and($card->setNumber($number = '4111111111111111'))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setCustomer($customer))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))
            ->and($this->testedInstance->save())

            ->if($status = ild78\Payment\Status::AUTHORIZE)

            ->and($options = [
                'body' => json_encode(['status' => $status]),
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ],
                'timeout' => $config->getTimeout(),
            ])
            ->and($location = $this->testedInstance->getUri())
            ->then
                ->object($this->testedInstance->setStatus($status)->save())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('PATCH', $location, $options)
                            ->once
        ;
    }

    public function testSave_device()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($port = rand(1, 65535))
            ->and($addr = $this->ipDataProvider()[0])
            ->and($url = 'https://www.example.org?' . uniqid())

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($card = new ild78\Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(rand(date('Y'), 3000)))
            ->and($card->setName(uniqid()))
            ->and($card->setNumber($number = '4111111111111111'))
            ->and($card->setZipCode(substr(uniqid(), 0, rand(2, 8))))

            ->if($customer = new ild78\Customer)
            ->and($customer->setName(uniqid()))
            ->and($customer->setEmail(uniqid() . '@example.org'))
            ->and($customer->setMobile(uniqid()))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount(rand(100, 999999)))
            ->and($this->testedInstance->setAuth($url))
            ->and($this->testedInstance->setCard($card))
            ->and($this->testedInstance->setCurrency('EUR'))
            ->and($this->testedInstance->setCustomer($customer))
            ->and($this->testedInstance->setDescription(uniqid()))

            ->and($json = json_encode(array_merge($this->testedInstance->toArray(), [
                'device' => [
                    'ip' => $addr,
                    'port' => $port,
                ],
            ])))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ],
                'timeout' => $config->getTimeout(),
            ])
            ->and($location = $this->testedInstance->getUri())
            ->then
                ->assert('Must have an IP address in env')
                    ->exception(function () {
                        $this->testedInstance->save();
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidIpAddressException::class)

                ->assert('Must have an port in env')
                    ->if($_SERVER['SERVER_ADDR'] = $addr)
                    ->then
                        ->exception(function () {
                            $this->testedInstance->save();
                        })
                            ->isInstanceOf(ild78\Exceptions\InvalidPortException::class)

                ->assert('Should add a device')
                    ->if($_SERVER['SERVER_PORT'] = $port)
                    ->then
                        ->variable($this->testedInstance->getId())
                            ->isNull
                        ->object($this->testedInstance->save())
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->withArguments('POST', $location, $options)
                                    ->once

                        ->string($this->testedInstance->getId())
                            ->isIdenticalTo('paym_KIVaaHi7G8QAYMQpQOYBrUQE')
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
                        ->isInstanceOf(ild78\Exceptions\InvalidAmountException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

                ->assert('49 is not a valid amount')
                    ->exception(function () {
                        $this->testedInstance->setAmount(49);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidAmountException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

                ->assert('50 is valid')
                    ->object($this->newTestedInstance->setAmount(50))
                        ->isTestedInstance
                    ->integer($this->testedInstance->getAmount())
                        ->isEqualTo(50)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('amount')
                        ->integer['amount']
                            ->isEqualTo(50)

                ->assert('random value')
                    ->object($this->newTestedInstance->setAmount($amount = rand(50, 999999)))
                        ->isTestedInstance
                    ->integer($this->testedInstance->getAmount())
                        ->isEqualTo($amount)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('amount')
                        ->integer['amount']
                            ->isEqualTo($amount)
        ;
    }

    public function testSetAuth()
    {
        $this
            ->assert('With an Auth object')
                ->if($auth = new ild78\Auth)
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getAuth())
                        ->isNull

                    ->object($this->testedInstance->setAuth($auth))
                        ->isTestedInstance

                    ->object($this->testedInstance->getAuth())
                        ->isIdenticalTo($auth)

            ->assert('With an URL')
                ->if($https = 'https://www.example.org?' . uniqid())
                ->and($http = 'http://www.example.org?' . uniqid())
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getAuth())
                        ->isNull

                    ->object($this->testedInstance->setAuth($https))
                        ->isTestedInstance

                    ->object($this->testedInstance->getAuth())
                        ->isInstanceOf(ild78\Auth::class)

                    ->string($this->testedInstance->getAuth()->getReturnUrl())
                        ->isIdenticalTo($https)

                    ->string($this->testedInstance->getAuth()->getStatus())
                        ->isIdenticalTo(ild78\Auth\Status::REQUEST)

                    ->exception(function () use ($http) {
                        $this->testedInstance->setAuth($http);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidUrlException::class)
                        ->message
                            ->isIdenticalTo('You must provide an HTTPS URL.')

            ->assert('With a true value')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getAuth())
                        ->isNull

                    ->object($this->testedInstance->setAuth(true))
                        ->isTestedInstance

                    ->object($this->testedInstance->getAuth())
                        ->isInstanceOf(ild78\Auth::class)

                    ->variable($this->testedInstance->getAuth()->getReturnUrl())
                        ->isNull

                    ->string($this->testedInstance->getAuth()->getStatus())
                        ->isIdenticalTo(ild78\Auth\Status::REQUEST)

            ->assert('With false')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getAuth())
                        ->isNull

                    ->object($this->testedInstance->setAuth(false))
                        ->isTestedInstance

                    ->variable($this->testedInstance->getAuth())
                        ->isNull
        ;
    }

    public function testSetCard()
    {
        $this
            ->if($card = new ild78\Card)
            ->and($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getCard())
                    ->isNull

                ->variable($this->testedInstance->getMethod())
                    ->isNull

                ->object($this->testedInstance->setCard($card))
                    ->isTestedInstance

                ->object($this->testedInstance->getCard())
                    ->isIdenticalTo($card)

                ->string($this->testedInstance->getMethod())
                    ->isIdenticalTo('card')
        ;
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testSetCurrency($currency)
    {
        $this
            ->given($fakeCurrency = uniqid())
            ->and($upper = strtoupper($currency))
            ->and($lower = strtolower($currency))
            ->then
                ->assert('Valid currency : ' . $upper)
                    ->variable($this->newTestedInstance->getCurrency())
                        ->isNull

                    ->object($this->testedInstance->setCurrency($upper))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCurrency())
                        ->isIdenticalTo($lower)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('currency')
                        ->string['currency']
                            ->isEqualTo($lower)

                ->assert('Valid currency : ' . $lower)
                    ->object($this->newTestedInstance->setCurrency($lower))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCurrency())
                        ->isIdenticalTo($lower)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('currency')
                        ->string['currency']
                            ->isEqualTo($lower)

                ->assert('Invalid currency')
                    ->exception(function () use ($fakeCurrency) {
                        $this->newTestedInstance->setCurrency($fakeCurrency);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidCurrencyException::class)
                        ->message
                            ->contains('"' . $fakeCurrency . '" is not a valid currency')
                            ->contains('please use one of the following :')
                            ->contains($upper)

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
        ;
    }

    public function testSetDescription()
    {
        $description = '';

        for ($idx = 0; $idx < 70; $idx++) {
            $length = strlen($description);

            if ($length < 3 || $length > 64) {
                $this
                    ->assert($length . ' characters => Not valid')
                        ->exception(function () use ($description) {
                            $this->newTestedInstance->setDescription($description);
                        })
                            ->isInstanceOf(ild78\Exceptions\InvalidDescriptionException::class)
                            ->hasNestedException
                            ->message
                                ->isIdenticalTo('A valid description must be between 3 and 64 characters.')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse
                ;
            } else {
                $this
                    ->assert($length . ' characters => Valid')
                        ->object($this->newTestedInstance->setDescription($description))
                            ->isTestedInstance

                        ->string($this->testedInstance->getDescription())
                            ->isIdenticalTo($description)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('description')
                            ->string['description']
                                ->isEqualTo($description)
                ;
            }

            $description .= chr(rand(65, 90));
        }
    }

    public function testSetSepa()
    {
        $this
            ->if($sepa = new ild78\Sepa)
            ->and($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getSepa())
                    ->isNull

                ->variable($this->testedInstance->getMethod())
                    ->isNull

                ->object($this->testedInstance->setSepa($sepa))
                    ->isTestedInstance

                ->object($this->testedInstance->getSepa())
                    ->isIdenticalTo($sepa)

                ->string($this->testedInstance->getMethod())
                    ->isIdenticalTo('sepa')
        ;
    }

    public function testSetOrderId()
    {
        $orderId = '';

        for ($idx = 0; $idx < 40; $idx++) {
            $length = strlen($orderId);

            if ($length < 1 || $length > 36) {
                $this
                    ->assert($length . ' characters => Not valid')
                        ->exception(function () use ($orderId) {
                            $this->newTestedInstance->setOrderId($orderId);
                        })
                            ->isInstanceOf(ild78\Exceptions\InvalidOrderIdException::class)
                            ->hasNestedException
                            ->message
                                ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse
                ;
            } else {
                $this
                    ->assert($length . ' characters => Valid')
                        ->object($this->newTestedInstance->setOrderId($orderId))
                            ->isTestedInstance

                        ->string($this->testedInstance->getOrderId())
                            ->isIdenticalTo($orderId)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('order_id')
                            ->string['order_id']
                                ->isEqualTo($orderId)
                ;
            }

            $orderId .= chr(rand(65, 90));
        }
    }

    public function testSetUniqueId()
    {
        $uniqueId = '';

        for ($idx = 0; $idx < 40; $idx++) {
            $length = strlen($uniqueId);

            if ($length < 1 || $length > 36) {
                $this
                    ->assert($length . ' characters => Not valid')
                        ->exception(function () use ($uniqueId) {
                            $this->newTestedInstance->setUniqueId($uniqueId);
                        })
                            ->isInstanceOf(ild78\Exceptions\InvalidUniqueIdException::class)
                            ->hasNestedException
                            ->message
                                ->isIdenticalTo('A valid unique ID must be between 1 and 36 characters.')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse
                ;
            } else {
                $this
                    ->assert($length . ' characters => Valid')
                        ->object($this->newTestedInstance->setUniqueId($uniqueId))
                            ->isTestedInstance

                        ->string($this->testedInstance->getUniqueId())
                            ->isIdenticalTo($this->testedInstance->uniqueId)
                            ->isIdenticalTo($this->testedInstance->unique_id)
                            ->isIdenticalTo($uniqueId)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->notHasKey('uniqueId')
                            ->hasKey('unique_id')
                            ->string['unique_id']
                                ->isEqualTo($uniqueId)
                ;
            }

            $uniqueId .= chr(rand(65, 90));
        }
    }
}
