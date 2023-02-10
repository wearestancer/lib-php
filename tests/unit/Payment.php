<?php

namespace Stancer\tests\unit;

use DateTime;
use DateInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Stancer;
use Stancer\Payment as testedClass;
use mock;

class Payment extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Currencies;
    use Stancer\Tests\Provider\Network;

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
            ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

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

                ->if($this->calling($response)->getBody = new Stancer\Http\Stream(json_encode($json)))
                ->then
                    ->object($obj = testedClass::charge($options))
                        ->isInstanceOf(testedClass::class)

                    ->integer($obj->getAmount())
                        ->isIdenticalTo($options['amount'])

                    ->object($card = $obj->getCard())
                        ->isInstanceOf(Stancer\Card::class)

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

                ->if($this->calling($response)->getBody = new Stancer\Http\Stream(json_encode($json)))
                ->then
                    ->object($obj = testedClass::charge($options))
                        ->isInstanceOf(testedClass::class)

                    ->integer($obj->getAmount())
                        ->isIdenticalTo($options['amount'])

                    ->object($sepa = $obj->getSepa())
                        ->isInstanceOf(Stancer\Sepa::class)

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

                ->if($this->calling($response)->getBody = new Stancer\Http\Stream(json_encode($json)))
                ->then
                    ->object($obj = testedClass::charge($options))
                        ->isInstanceOf(testedClass::class)

                    ->integer($obj->getAmount())
                        ->isIdenticalTo($options['amount'])

                    ->object($sepa = $obj->getSepa())
                        ->isInstanceOf(Stancer\Sepa::class)

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
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\AmountTrait::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
        ;
    }

    public function testDelete()
    {
        $this
            ->exception(function () {
                $this->newTestedInstance(uniqid())->delete();
            })
                ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
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
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchOrderIdFilterException::class)
                        ->message
                            ->isIdenticalTo('Order ID must be a string.')

                    ->exception(function () use ($gen) {
                        $this->testedInstance->filterListParams(['order_id' => $gen(37)]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchOrderIdFilterException::class)
                        ->message
                            ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->assert('Allow unique_id')
                    ->array($this->testedInstance->filterListParams($unique))
                        ->isIdenticalTo($unique)

                ->assert('Validate unique_id')
                    ->exception(function () {
                        $this->testedInstance->filterListParams(['unique_id' => rand(1, PHP_INT_MAX)]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchUniqueIdFilterException::class)
                        ->message
                            ->isIdenticalTo('Unique ID must be a string.')

                    ->exception(function () use ($gen) {
                        $this->testedInstance->filterListParams(['unique_id' => $gen(37)]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchUniqueIdFilterException::class)
                        ->message
                            ->isIdenticalTo('A valid unique ID must be between 1 and 36 characters.')
        ;
    }

    public function testGetDateBank()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($date = new DateTime)
            ->then
                ->variable($this->testedInstance->getDateBank())
                    ->isNull

                ->exception(function () use ($date) {
                    $this->testedInstance->setDateBank($date);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "dateBank".')

            ->if($this->testedInstance->hydrate(['dateBank' => $date]))
            ->then
                ->dateTime($this->testedInstance->getDateBank())
                    ->isEqualTo($date)
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
            ->and($config = Stancer\Config::init([$secret]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->cardCurrencyDataProvider(true))

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
                    ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid public API key for development.')

                ->object($config->setKeys([$public, $secret]))

                ->exception(function () {
                    $this->testedInstance->getPaymentPageUrl();
                })
                    ->isInstanceOf(Stancer\Exceptions\MissingReturnUrlException::class)
                    ->message
                        ->isIdenticalTo('You must provide a return URL before asking for the payment page.')

                ->object($this->testedInstance->setReturnUrl($return))
                    ->isTestedInstance

                ->exception(function () {
                    $this->testedInstance->getPaymentPageUrl();
                })
                    ->isInstanceOf(Stancer\Exceptions\MissingPaymentIdException::class)
                    ->message
                        ->isIdenticalTo('A payment ID is mandatory to obtain a payment page URL. Maybe you forgot to send the payment.')

                ->object($this->testedInstance->send())
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
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($client = new mock\Stancer\Http\Client)
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

            ->if($body = file_get_contents(__DIR__ . '/fixtures/payment/read.json'))
            ->and($responsePayment = new Stancer\Http\Response(200, $body))
            ->and($this->calling($client)->request = $responsePayment)

            ->and($paymentData = json_decode($body, true))
            ->and($paid = $paymentData['amount'])
            ->and($id = $paymentData['id'])

            ->if($completeRefund = new Stancer\Refund())
            ->and($completeRefund->setAmount($paid))

            ->if($amount = rand(50, $paid))
            ->and($partialRefund = new Stancer\Refund())
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

    public function testGetResponseAuthor()
    {
        $this
            ->given($author = $this->getRandomString(6))
            ->if($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getResponseAuthor())
                    ->isNull

                ->exception(function () use ($author) {
                    $this->testedInstance->setResponseAuthor($author);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "responseAuthor".')


            ->if($this->testedInstance->hydrate(['response_author' => $author]))
            ->then
                ->string($this->testedInstance->getResponseAuthor())
                    ->isIdenticalTo($author)
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
                    ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
                    ->message
                        ->isIdenticalTo('You must provide an HTTPS URL.')
        ;
    }

    public function testIsSuccess_IsNotSuccess()
    {
        $this
            ->assert('Default values')
                ->given($this->newTestedInstance)
                ->then
                    ->boolean($this->testedInstance->isSuccess())
                        ->isFalse('isSuccess')

                    ->boolean($this->testedInstance->isNotSuccess())
                        ->isTrue('isNotSuccess')

                    ->boolean($this->testedInstance->isError())
                        ->isFalse('isError')

                    ->boolean($this->testedInstance->isNotError())
                        ->isTrue('isNotError')
        ;

        $oks = [
            Stancer\Payment\Status::CAPTURE_SENT,
            Stancer\Payment\Status::CAPTURED,
            Stancer\Payment\Status::TO_CAPTURE,
        ];
        $noks = [
            Stancer\Payment\Status::CANCELED,
            Stancer\Payment\Status::DISPUTED,
            Stancer\Payment\Status::EXPIRED,
            Stancer\Payment\Status::FAILED,
        ];

        foreach ($oks as $status) {
            $this
                ->assert('Captured payment, "' . $status . '" is success')
                    ->given($this->newTestedInstance)
                    ->and($this->testedInstance->hydrate(['capture' => true, 'status' => $status]))
                    ->then
                        ->boolean($this->testedInstance->isSuccess())
                            ->isTrue('isSuccess')

                        ->boolean($this->testedInstance->isNotSuccess())
                            ->isFalse('isNotSuccess')

                        ->boolean($this->testedInstance->isError())
                            ->isFalse('isError')

                        ->boolean($this->testedInstance->isNotError())
                            ->isTrue('isNotError')
            ;
        }

        $this
            ->assert('Captured payment, "' . Stancer\Payment\Status::AUTHORIZED . '" is an error')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->hydrate(['capture' => true, 'status' => Stancer\Payment\Status::AUTHORIZED]))
                ->then
                    ->boolean($this->testedInstance->isSuccess())
                        ->isFalse('isSuccess')

                    ->boolean($this->testedInstance->isNotSuccess())
                        ->isTrue('isNotSuccess')

                    ->boolean($this->testedInstance->isError())
                        ->isTrue('isError')

                    ->boolean($this->testedInstance->isNotError())
                        ->isFalse('isNotError')
        ;

        foreach ($noks as $status) {
            $this
                ->assert('Captured payment, "' . $status . '" is an error')
                    ->given($this->newTestedInstance)
                    ->and($this->testedInstance->hydrate(['capture' => true, 'status' => $status]))
                    ->then
                        ->boolean($this->testedInstance->isSuccess())
                            ->isFalse('isSuccess')

                        ->boolean($this->testedInstance->isNotSuccess())
                            ->isTrue('isNotSuccess')

                        ->boolean($this->testedInstance->isError())
                            ->isTrue('isError')

                        ->boolean($this->testedInstance->isNotError())
                            ->isFalse('isNotError')
            ;
        }

        foreach ($oks as $status) {
            $this
                ->assert('Authorization only, "' . $status . '" is success')
                    ->given($this->newTestedInstance)
                    ->and($this->testedInstance->hydrate(['capture' => false, 'status' => $status]))
                    ->then
                        ->boolean($this->testedInstance->isSuccess())
                            ->isTrue('isSuccess')

                        ->boolean($this->testedInstance->isNotSuccess())
                            ->isFalse('isNotSuccess')

                        ->boolean($this->testedInstance->isError())
                            ->isFalse('isError')

                        ->boolean($this->testedInstance->isNotError())
                            ->isTrue('isNotError')
            ;
        }

        $this
            ->assert('Authorization only, "' . Stancer\Payment\Status::AUTHORIZED . '" is success')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->hydrate(['capture' => false, 'status' => Stancer\Payment\Status::AUTHORIZED]))
                ->then
                    ->boolean($this->testedInstance->isSuccess())
                        ->isTrue('isSuccess')

                    ->boolean($this->testedInstance->isNotSuccess())
                        ->isFalse('isNotSuccess')

                    ->boolean($this->testedInstance->isError())
                        ->isFalse('isError')

                    ->boolean($this->testedInstance->isNotError())
                        ->isTrue('isNotError')
        ;

        foreach ($noks as $status) {
            $this
                ->assert('Authorization only, "' . $status . '" is an error')
                    ->given($this->newTestedInstance)
                    ->and($this->testedInstance->hydrate(['capture' => false, 'status' => $status]))
                    ->then
                        ->boolean($this->testedInstance->isSuccess())
                            ->isFalse('isSuccess')

                        ->boolean($this->testedInstance->isNotSuccess())
                            ->isTrue('isNotSuccess')

                        ->boolean($this->testedInstance->isError())
                            ->isTrue('isError')

                        ->boolean($this->testedInstance->isNotError())
                            ->isFalse('isNotError')
            ;
        }
    }

    public function testIssue7_old()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/issue/7.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($this->newTestedInstance('paym_T3xKVkOq17DCjBEHsAefovuJ'))
            ->then
                ->object($this->testedInstance->setStatus(Stancer\Payment\Status::CAPTURE))
                    ->isTestedInstance

                ->object($this->testedInstance->send())
                    ->isTestedInstance
        ;
    }

    public function testList()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/list.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)
            ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

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
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                    ->message
                        ->isIdenticalTo('Limit must be between 1 and 100.')

                ->exception(function () {
                    testedClass::list(['limit' => 101]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                    ->message
                        ->isIdenticalTo('Limit must be between 1 and 100.')

                ->exception(function () {
                    testedClass::list(['limit' => uniqid()]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                    ->message
                        ->isIdenticalTo('Limit must be between 1 and 100.')

            ->assert('Invalid start')
                ->exception(function () {
                    testedClass::list(['start' => -1]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                    ->message
                        ->isIdenticalTo('Start must be a positive integer.')

                ->exception(function () {
                    testedClass::list(['start' => uniqid()]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                    ->message
                        ->isIdenticalTo('Start must be a positive integer.')

            ->assert('No terms')
                ->exception(function () {
                    testedClass::list([]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchFilterException::class)
                    ->message
                        ->isIdenticalTo('Invalid search filters.')

                ->exception(function () {
                    testedClass::list(['foo' => 'bar']);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchFilterException::class)
                    ->message
                        ->isIdenticalTo('Invalid search filters.')

            ->assert('Invalid created filter')
                ->exception(function () {
                    testedClass::list(['created' => time() + 100]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be in the past.')

                ->exception(function () {
                    $date = new DateTime();
                    $date->add(new DateInterval('P1D'));

                    testedClass::list(['created' => $date]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be in the past.')

                ->exception(function () {
                    testedClass::list(['created' => 0]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

                ->exception(function () {
                    testedClass::list(['created' => uniqid()]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                    ->message
                        ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

            ->assert('Invalid order id filter')
                ->exception(function () {
                    testedClass::list(['order_id' => '']);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchOrderIdFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->exception(function () {
                    testedClass::list(['order_id' => rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchOrderIdFilterException::class)
                    ->message
                        ->isIdenticalTo('Order ID must be a string.')

            ->assert('Invalid unique id filter')
                ->exception(function () {
                    testedClass::list(['unique_id' => '']);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchUniqueIdFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid unique ID must be between 1 and 36 characters.')

                ->exception(function () {
                    testedClass::list(['unique_id' => rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchUniqueIdFilterException::class)
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
                    'start' => $start + 2, // Based on json sample
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
                ->and($this->calling($response)->getBody = new Stancer\Http\Stream(json_encode($body)))

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
                ->given($this->calling($response)->getBody = new Stancer\Http\Stream(''))

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

    /**
     * @dataProvider cardCurrencyDataProvider
     */
    public function testMethodsAllowed($currency)
    {
        $this
            ->given($methods = ['card', 'sepa'])

            ->assert('Should return an empty array as default')
                ->given($this->newTestedInstance)
                ->then
                    ->array($this->testedInstance->getMethodsAllowed())
                        ->isEmpty

            ->assert('Should allow array of strings')
                ->given($this->newTestedInstance)
                ->then
                    ->object($this->testedInstance->setMethodsAllowed($methods))
                        ->isTestedInstance

                    ->array($this->testedInstance->getMethodsAllowed())
                        ->hasSize(2)
                        ->string[0]
                            ->isIdenticalTo('card')
                        ->string[1]
                            ->isIdenticalTo('sepa')

            ->assert('Should only allow known methods')
                ->given($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setMethodsAllowed([$value]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($value)

            ->assert('Should allow to add methods')
                ->given($this->newTestedInstance)
                ->then
                    ->object($this->testedInstance->addMethodsAllowed($methods[0]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getMethodsAllowed())
                        ->hasSize(1)
                        ->string[0]
                            ->isIdenticalTo($methods[0])
        ;

        $lower = strtolower($currency);

        if ($lower !== 'eur') {
            $this
                ->assert('Currency can be refused when using SEPA')
                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->setCurrency($currency))
                    ->then
                        ->exception(function () {
                            $this->testedinstance->addMethodsAllowed('sepa');
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                            ->message
                                ->isIdenticalTo(sprintf('You can not use "%s" method with "%s" currency.', 'sepa', $lower))

                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->setCurrency($currency))
                    ->then
                        ->exception(function () use ($methods) {
                            $this->testedinstance->setMethodsAllowed($methods);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                            ->message
                                ->isIdenticalTo(sprintf('You can not use "%s" method with "%s" currency.', 'sepa', $lower))
            ;
        }
    }

    public function testPay()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($this->calling($client)->request = $response)
            ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

            ->then
                ->assert('Pay with card')
                    ->if($card = new Stancer\Card)
                    ->and($card->setCvc(substr(uniqid(), 0, 3)))
                    ->and($card->setExpMonth(rand(1, 12)))
                    ->and($card->setExpYear(date('Y') + rand(1, 10)))
                    ->and($card->setName(uniqid()))
                    ->and($card->setNumber('4111111111111111'))
                    ->and($card->setZipCode(substr(uniqid(), 0, rand(2, 8))))

                    ->if($file = __DIR__ . '/fixtures/payment/create-card.json')
                    ->and($this->calling($response)->getBody = new Stancer\Http\Stream(file_get_contents($file)))
                    ->then
                        ->object($this->newTestedInstance->pay(rand(50, 9999), 'EUR', $card))
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->once

                ->assert('Pay with SEPA')
                    ->if($sepa = new Stancer\Sepa)
                    ->and($sepa->setBic('DEUTDEFF')) // Thx Wikipedia
                    ->and($sepa->setIban('DE91 1000 0000 0123 4567 89')) // Thx Wikipedia
                    ->and($sepa->setName(uniqid()))

                    ->if($file = __DIR__ . '/fixtures/payment/create-sepa.json')
                    ->and($this->calling($response)->getBody = new Stancer\Http\Stream(file_get_contents($file)))
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
            ->given($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($this->calling($client)->request = $response)
            ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))
            // Behavior modification are done in assert part to prevent confusion on multiple calls mocking

            ->and($logger = new mock\Stancer\Core\Logger)
            ->and($config->setLogger($logger))

            ->if($body = file_get_contents(__DIR__ . '/fixtures/payment/read.json'))
            ->and($paymentData = json_decode($body, true))
            ->and($paid = $paymentData['amount'])

            ->if($amount = rand(50, $paid - 50))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/refund/read.json'))
            ->and($refund1Data = json_decode($body, true))
            ->and($refund1Data['amount'] = $amount)

            ->if($lastPart = $paid - $amount)
            ->and($refund2Data = json_decode($body, true))
            ->and($refund2Data['amount'] = $lastPart)
            ->and($refund2Data['status'] = 'refunded')

            ->given($id = 'paym_SKMLflt8NBATuiUzgvTYqsw5') // from fixtures
            ->and($this->newTestedInstance($id))
            ->and($tooMuch = rand($paid + 1, 9999))
            ->and($notEnough = rand(1, 49))
            ->then
                ->assert('Without refunds we get an empty array')
                    ->if($this->calling($response)->getBody = new Stancer\Http\Stream(json_encode($paymentData)))
                    ->then
                        ->array($this->testedInstance->getRefunds())
                            ->isEmpty

                ->assert('We can not refund more than paid')
                    ->exception(function () use ($tooMuch) {
                        $this->testedInstance->refund($tooMuch);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('You are trying to refund (' . sprintf('%.02f', $tooMuch / 100) . ' EUR) more than paid (34.06 EUR).')

                ->assert('Amount must be greater or equal than 50')
                    ->exception(function () use ($notEnough) {
                        $this->testedInstance->refund($notEnough);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->assert('We can put a refund amount')
                    ->if($this->calling($response)->getBody = new Stancer\Http\Stream(json_encode($refund1Data)))
                    ->then
                        ->object($this->testedInstance->refund($amount))
                            ->isTestedInstance

                        ->array($refunds = $this->testedInstance->getRefunds())
                            ->object[0]
                                ->isInstanceOf(Stancer\Refund::class)
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

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $this->testedInstance->getUri())
                                    ->never

                ->assert('We can not refund more than refundable')
                    ->exception(function () use ($paid) {
                        $this->testedInstance->refund($paid);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('You are trying to refund (' . sprintf('%.02f', $paid / 100) . ' EUR) more than paid (34.06 EUR with ' . sprintf('%.02f', $amount / 100) . ' EUR already refunded).')

                ->assert('Without amount we will refund all')
                    ->if($this->calling($response)->getBody = new Stancer\Http\Stream(json_encode($refund2Data)))
                    ->and($location = $this->testedInstance->getUri())
                    ->then
                        ->object($this->testedInstance->refund())
                            ->isTestedInstance

                        ->array($refunds = $this->testedInstance->getRefunds())
                            ->hasSize(2)
                            ->object[0]
                                ->isInstanceOf(Stancer\Refund::class)
                            ->object[1]
                                ->isInstanceOf(Stancer\Refund::class)

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

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location)
                                    ->once

                ->assert('We can not refund on unsent payment')
                    ->exception(function () {
                        $this->newTestedInstance->refund();
                    })
                        ->isInstanceOf(Stancer\Exceptions\MissingPaymentIdException::class)
                        ->message
                            ->isIdenticalTo('A payment ID is mandatory. Maybe you forgot to send the payment.')

                ->assert('Should work with methods allowed (internal bug)')
                    ->if($this->calling($response)->getBody = new Stancer\Http\Stream(file_get_contents(__DIR__ . '/fixtures/payment/read-methods-allowed.json')))

                    ->if($this->newTestedInstance('paym_QAM6fOpJnH5DvkYr3ezAVPpa'))
                    ->then
                        ->array($this->testedInstance->getMethodsAllowed())
                            ->hasSize(2)
                            ->containsValues(['card', 'sepa'])

                        ->object($this->testedInstance->refund())
                            ->isTestedInstance
        ;
    }

    public function testSend_exceptions()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($this->calling($client)->request = $response)
            ->and($config->setHttpClient($client))

            ->if($this->newTestedInstance)
            ->then
                ->exception(function () {
                    $this->testedInstance->send();
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)

            ->if($this->testedInstance->setAmount(rand(100, 999999)))
            ->then
                ->exception(function () {
                    $this->testedInstance->send();
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidCurrencyException::class)

            ->if($this->testedInstance->setCurrency($this->cardCurrencyDataProvider(true)))
            ->then
                ->object($this->testedInstance->send())
                    ->isTestedInstance
        ;
    }

    public function testSend_withCard()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)
            ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

            ->if($card = new Stancer\Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(date('Y') - rand(1, 10)))
            ->and($card->setName(uniqid()))
            ->and($card->setNumber($number = '4111111111111111'))
            ->and($card->setZipCode(substr(uniqid(), 0, rand(2, 8))))

            ->if($customer = new Stancer\Customer)
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

            ->if($logger = new mock\Stancer\Core\Logger)
            ->and($config->setLogger($logger))
            ->and($logMessage = 'Payment of 1.00 eur with mastercard "4444"')

            ->and($location = $this->testedInstance->getUri())
            ->then
                ->exception(function () {
                    $this->testedInstance->send();
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidExpirationException::class)
                    ->message
                        ->isIdenticalTo('Card expiration is invalid.')

            ->if($card->setExpYear(date('Y') + rand(1, 10)))
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
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull
                ->object($this->testedInstance->send())
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
                    ->isIdenticalTo($number) // Number is unchanged in send process

                ->variable($card->getZipCode())
                    ->isNull
        ;
    }

    public function testSend_withSepa()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-sepa.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)
            ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

            ->if($sepa = new Stancer\Sepa)
            ->and($sepa->setBic('DEUTDEFF')) // Thx Wikipedia
            ->and($sepa->setIban('DE91 1000 0000 0123 4567 89')) // Thx Wikipedia
            ->and($sepa->setName(uniqid()))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount(rand(100, 999999)))
            ->and($this->testedInstance->setSepa($sepa))
            ->and($this->testedInstance->setCurrency('EUR'))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))

            ->if($logger = new mock\Stancer\Core\Logger)
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
                ->object($this->testedInstance->send())
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

    public function testSend_authenticatedPayment()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card-auth.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($ip = $this->ipDataProvider(true))
            ->and($port = rand(1, 65535))
            ->and($this->function->getenv = function ($varname) use ($ip, $port) {
                $name = strtolower($varname);

                if ($name === 'remote_addr') {
                    return $ip;
                }

                if ($name === 'remote_port') {
                    return $port;
                }

                return null;
            })

            ->if($amount = rand(10, 99999))
            ->and($currency = $this->cardCurrencyDataProvider(true))
            ->and($description = uniqid())
            ->and($url = 'https://www.example.org?' . uniqid())

            ->if($card = new Stancer\Card)
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
                    'status' => Stancer\Auth\Status::REQUEST,
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

                ->object($this->testedInstance->send())
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
                    ->isIdenticalTo($number) // Number is unchanged in send process

                // Auth object
                ->object($auth = $this->testedInstance->getAuth())
                    ->isInstanceOf(Stancer\Auth::class)

                ->string($auth->getReturnUrl())
                    ->isIdenticalTo('https://www.free.fr')

                ->string($auth->getStatus())
                    ->isIdenticalTo(Stancer\Auth\Status::AVAILABLE)

                // Device object
                ->object($device = $this->testedInstance->getDevice())
                    ->isInstanceOf(Stancer\Device::class)

                ->string($device->getIp())
                    ->isIdenticalTo('212.27.48.10')

                ->integer($device->getPort())
                    ->isEqualTo(1337)

                ->string($device->getHttpAccept())
                    ->isIdenticalTo('text/html')
        ;
    }

    public function testSend_fullyCustomAuthenticatedPayment()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card-auth.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($amount = rand(10, 99999))
            ->and($currency = $this->cardCurrencyDataProvider(true))
            ->and($description = uniqid())
            ->and($url = 'https://www.example.org?' . uniqid())

            ->if($auth = new Stancer\Auth)
            ->and($auth->setReturnUrl($url))

            ->if($ip = $this->ipDataProvider(true))
            ->and($port = rand(1, 65535))
            ->and($device = new Stancer\Device(['ip' => $ip, 'port' => $port]))

            ->if($card = new Stancer\Card)
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
                    'status' => Stancer\Auth\Status::REQUEST,
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

                ->object($this->testedInstance->send())
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
                    ->isIdenticalTo($number) // Number is unchanged in send process

                // Auth object
                ->object($this->testedInstance->getAuth())
                    ->isIdenticalTo($auth)

                ->string($auth->getReturnUrl())
                    ->isIdenticalTo('https://www.free.fr')

                ->string($auth->getStatus())
                    ->isIdenticalTo(Stancer\Auth\Status::AVAILABLE)

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

    public function testSend_withoutCardOrSepa()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($customer = new Stancer\Customer)
            ->and($customer->setName(uniqid()))
            ->and($customer->setEmail(uniqid() . '@example.org'))
            ->and($customer->setMobile(uniqid()))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->cardCurrencyDataProvider(true))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setCustomer($customer))
            ->and($this->testedInstance->setDescription(uniqid()))
            ->and($this->testedInstance->setOrderId(uniqid()))

            ->if($logger = new mock\Stancer\Core\Logger)
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
                ->object($this->testedInstance->send())
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

    public function testSend_authenticationAndPaymentPage()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method-auth.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->cardCurrencyDataProvider(true))
            ->and($description = uniqid())

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setAmount($amount))
            ->and($this->testedInstance->setAuth(true))
            ->and($this->testedInstance->setCurrency($currency))
            ->and($this->testedInstance->setDescription($description))

            ->and($json = json_encode([
                'amount' => $amount,
                'auth' => [
                    'status' => Stancer\Auth\Status::REQUEST,
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
                ->object($this->testedInstance->send())
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
                    ->isInstanceOf(Stancer\Auth::class)

                ->variable($auth->getRedirectUrl())
                    ->isNull

                ->variable($auth->getReturnUrl())
                    ->isNull

                ->variable($auth->getStatus())
                    ->isIdenticalTo(Stancer\Auth\Status::REQUESTED)
        ;
    }

    public function testSend_status()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-no-method.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($customer = new Stancer\Customer)
            ->and($customer->setName(uniqid()))
            ->and($customer->setEmail(uniqid() . '@example.org'))
            ->and($customer->setMobile(uniqid()))

            ->if($amount = rand(100, 999999))
            ->and($currency = $this->cardCurrencyDataProvider(true))

            ->if($card = new Stancer\Card)
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
            ->and($this->testedInstance->send())

            ->if($status = Stancer\Payment\Status::AUTHORIZE)

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
                ->object($this->testedInstance->setStatus($status)->send())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('PATCH', $location, $options)
                            ->once
        ;
    }

    public function testSend_device()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))
            ->and($port = rand(1, 65535))
            ->and($addr = $this->ipDataProvider(true))
            ->and($url = 'https://www.example.org?' . uniqid())

            ->if($client = new mock\Stancer\Http\Client)
            ->and($response = new mock\Stancer\Http\Response(200))
            ->and($body = file_get_contents(__DIR__ . '/fixtures/payment/create-card.json'))
            ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($this->function->getenv = false)

            ->if($card = new Stancer\Card)
            ->and($card->setCvc(substr(uniqid(), 0, 3)))
            ->and($card->setExpMonth(rand(1, 12)))
            ->and($card->setExpYear(rand(date('Y'), 3000)))
            ->and($card->setName(uniqid()))
            ->and($card->setNumber($number = '4111111111111111'))
            ->and($card->setZipCode(substr(uniqid(), 0, rand(2, 8))))

            ->if($customer = new Stancer\Customer)
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
                        $this->testedInstance->send();
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidIpAddressException::class)

                ->assert('Must have an port in env')
                    ->if($this->function->getenv = function ($varname) use ($addr) {
                        $name = strtolower($varname);

                        if ($name === 'remote_addr') {
                            return $addr;
                        }

                        return null;
                    })
                    ->then
                        ->exception(function () {
                            $this->testedInstance->send();
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidPortException::class)

                ->assert('Should add a device')
                    ->if($this->function->getenv = function ($varname) use ($addr, $port) {
                        $name = strtolower($varname);

                        if ($name === 'remote_addr') {
                            return $addr;
                        }

                        if ($name === 'remote_port') {
                            return $port;
                        }

                        return null;
                    })
                    ->then
                        ->variable($this->testedInstance->getId())
                            ->isNull
                        ->object($this->testedInstance->send())
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
                        ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

                ->assert('49 is not a valid amount')
                    ->exception(function () {
                        $this->testedInstance->setAmount(49);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)
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

                ->assert('Beware of the unexpected floating number')
                    ->object($this->newTestedInstance->setAmount(34.8 * 100))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getAmount())
                        ->isEqualTo(3480)
        ;
    }

    public function testSetAuth()
    {
        $this
            ->assert('With an Auth object')
                ->if($auth = new Stancer\Auth)
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
                        ->isInstanceOf(Stancer\Auth::class)

                    ->string($this->testedInstance->getAuth()->getReturnUrl())
                        ->isIdenticalTo($https)

                    ->string($this->testedInstance->getAuth()->getStatus())
                        ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

                    ->exception(function () use ($http) {
                        $this->testedInstance->setAuth($http);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
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
                        ->isInstanceOf(Stancer\Auth::class)

                    ->variable($this->testedInstance->getAuth()->getReturnUrl())
                        ->isNull

                    ->string($this->testedInstance->getAuth()->getStatus())
                        ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

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
            ->if($card = new Stancer\Card)
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
     * @dataProvider cardCurrencyDataProvider
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
                        ->isInstanceOf(Stancer\Exceptions\InvalidCurrencyException::class)
                        ->message
                            ->contains('"' . $fakeCurrency . '" is not a valid currency')
                            ->contains('please use one of the following :')
                            ->contains($lower)

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
        ;

        if ($lower !== 'eur') {
            $this
                ->assert('Currency can be refused when using SEPA')
                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->addMethodsAllowed('sepa'))
                    ->then
                        ->exception(function () use ($currency) {
                            $this->testedInstance->setCurrency($currency);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidCurrencyException::class)
                            ->message
                                ->isIdenticalTo(sprintf('You can not use "%s" currency with "%s" method.', $lower, 'sepa'))
            ;
        }
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
                            ->isInstanceOf(Stancer\Exceptions\InvalidDescriptionException::class)
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
            ->if($sepa = new Stancer\Sepa)
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
                            ->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
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
                            ->isInstanceOf(Stancer\Exceptions\InvalidUniqueIdException::class)
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
