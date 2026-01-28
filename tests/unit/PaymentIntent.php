<?php

namespace Stancer\tests\unit;

use mock;
use Stancer;
use Stancer\PaymentIntent as testedClass;

class PaymentIntent extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Currencies;
    use Stancer\Tests\Provider\Cards;
    use Stancer\Tests\Provider\Banks;

    /**
     * Run before test, set api version as 2 by default for intents.
     *
     * @param string $method
     */
    public function beforeTestMethod($method)
    {
        parent::beforeTestMethod($method);
        Stancer\Config::getGlobal()->setVersion(Stancer\Enum\ApiVersion::VERSION_2);
    }

    public function testAmount()
    {
        $this
            ->given($amount = rand(50, 10000))

            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->testedInstance->getAmount())
                        ->isNull

                ->assert('Should throw an exception if under 50')
                    ->exception(function () {
                        $this->testedInstance->setAmount(rand(1, 49));
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidAmountException::class)
                        ->message
                            ->isIdenticalTo('Amount must be greater than or equal to 50.')

                ->assert('Update value')
                    ->object($this->testedInstance->setAmount($amount))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getAmount())
                        ->isIdenticalTo($amount)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_amount())->isNull
                    ->object($this->testedInstance->set_amount($amount))->isTestedInstance
                    ->integer($this->testedInstance->get_amount())->isIdenticalTo($amount)

                    ->variable($this->newTestedInstance->amount)->isNull
                    ->variable($this->testedInstance->amount = $amount)
                    ->integer($this->testedInstance->amount)->isIdenticalTo($amount)
        ;
    }

    public function testCapture()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->testedInstance->getCapture())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setCapture(true))
                        ->isTestedInstance

                    ->boolean($this->testedInstance->getCapture())
                        ->isTrue

                    ->object($this->testedInstance->setCapture(false))
                        ->isTestedInstance

                    ->boolean($this->testedInstance->getCapture())
                        ->isFalse

                ->assert('Aliases')
                    ->if($this->newTestedInstance)
                    ->and($this->testedInstance->amount = 100)
                    ->and($this->testedInstance->currency = 'eur')

                        ->variable($this->testedInstance->get_capture())->isNull
                        ->object($this->testedInstance->set_capture(true))->isTestedInstance
                        ->boolean($this->testedInstance->get_capture())->isTrue
                        ->object($this->testedInstance->set_capture(false))->isTestedInstance
                        ->boolean($this->testedInstance->get_capture())->isFalse
        ;
    }

    public function testCard()
    {
        $this
            ->if($card = new Stancer\Card('card_' . $this->getRandomString(24)))
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getCard())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setCard($card))
                        ->isTestedInstance

                    ->object($this->testedInstance->getCard())
                        ->isInstanceof(Stancer\Card::class)
                    ->string($this->testedInstance->getCard()->getId())
                        ->isIdenticalTo($card->getId())

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_card())->isNull
                    ->object($this->testedInstance->set_card($card))->isTestedInstance
                    ->string($this->testedInstance->get_card()->getId())->isIdenticalTo($card->getId())

                    ->variable($this->newTestedInstance->card)->isNull
                    ->variable($this->testedInstance->card = $card)
                    ->string($this->testedInstance->card->getId())->isIdenticalTo($card->getId())
        ;
    }

    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
        ;
    }

    /**
     * @dataProvider cardCurrencyDataProvider
     *
     * @param mixed $currency
     */
    public function testCurrency_card($currency)
    {
        $this
            ->given($badCurrency = uniqid())

            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getCurrency())
                        ->isNull

                ->assert('Throw an exception if currency is unknown')
                    ->exception(function () use ($badCurrency) {
                        $this->testedInstance->setCurrency($badCurrency);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidCurrencyException::class)
                        ->message
                            ->contains($badCurrency)
                            ->contains('is not a valid currency, please use one of the following')

                ->assert('Update value')
                    ->object($this->testedInstance->setCurrency($currency))
                        ->isTestedInstance

                    ->object($this->testedInstance->getCurrency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_currency())->isNull
                    ->object($this->testedInstance->set_currency($currency))->isTestedInstance
                    ->object($this->testedInstance->get_currency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                    ->variable($this->newTestedInstance->currency)->isNull
                    ->variable($this->testedInstance->currency = $currency)
                    ->object($this->testedInstance->currency)
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))
        ;
    }

    /**
     * @dataProvider sepaCurrencyDataProvider
     *
     * @param mixed $currency
     */
    public function testCurrency_sepa($currency)
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getCurrency())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setCurrency($currency))
                        ->isTestedInstance

                    ->object($this->testedInstance->getCurrency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_currency())->isNull
                    ->object($this->testedInstance->set_currency($currency))->isTestedInstance
                    ->object($this->testedInstance->get_currency())
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))

                    ->variable($this->newTestedInstance->currency)->isNull
                    ->variable($this->testedInstance->currency = $currency)
                    ->object($this->testedInstance->currency)
                        ->isInstanceOf(Stancer\Currency::class)
                        ->isIdenticalTo(Stancer\Currency::from(strtolower($currency)))
        ;

        foreach ($this->cardCurrencyDataProvider() as $badCurrency) {
            if ($currency === $badCurrency) {
                continue;
            }

            $this
                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setMethodsAllowed(['sepa']))
                ->then
                    ->assert('Throw an exception for invalid currency')
                        ->exception(function () use ($badCurrency) {
                            $this->testedInstance->setCurrency($badCurrency);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidCurrencyException::class)
                            ->message
                                ->isIdenticalTo(sprintf('You can not use "%s" currency with "sepa" method.', strtolower($badCurrency)))
            ;
        }
    }

    public function testCreationDate()
    {
        $this
            ->given($pi = $this->newTestedInstance)
            ->if($created = new \DateTimeImmutable())
            ->then
                ->assert('Default value and Aliases')

                    ->variable($pi->getCreated())->isNull

                    ->variable($pi->created())->isNull

                    ->variable($pi->get_creation_date())->isNull

                    ->variable($pi->getCreationDate())->isNull

                    ->variable($pi->get_creation_date())->isNull

                    ->variable($pi->getCreatedAt())->isNull

                    ->variable($pi->get_created_at())->isNull

                    ->variable($pi->createdAt)->isNull

                    ->variable($pi->created_at)->isNull
                ->assert('Date Creation is not settable')

                    ->exception(function () {
                        $this->newTestedInstance()->created = new \DateTimeImmutable();
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify the creation date.')

            ->given($secret = 'stest_' . $this->getRandomString(24))

            ->and($config = Stancer\Config::init([$secret]))
            ->and($config->setDebug(false))
            ->and($config->setVersion(Stancer\Enum\ApiVersion::VERSION_2))

            ->if($client = new mock\Stancer\Http\Client())
            ->and($response = $this->mockJsonResponse('payment_intents', 'intent'))
            ->and($this->calling($client)->request = $response)
            ->and($config->setHttpClient($client))
            ->and($created = new \DateTimeImmutable('@1714742425'))
            // We set an ID to our instance, to make sure we populate Created_At on call
            ->and($pi = $this->NewTestedInstance('pi_yWYfCSzsUUhr9KwMv7vuLZHX'))

                ->assert('test getting created_at from API with aliases.')
                    ->then
                            ->dateTime($pi->createdAt)->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->created())->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->get_creation_date())->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->getCreationDate())->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->get_creation_date())->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->getCreatedAt())->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->get_created_at())->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->createdAt)->isImmutable
                                ->isEqualTo($created)

                            ->dateTime($pi->created_at)->isImmutable
                                ->isEqualTo($created)
        ;
    }

    public function testCustomer()
    {
        $this
            ->if($customer = new Stancer\Customer('cust_' . $this->getRandomString(24)))
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getCustomer())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setCustomer($customer))
                        ->isTestedInstance
                    ->object($this->testedInstance->getCustomer())
                        ->isInstanceOf(Stancer\Customer::class)
                    ->string($this->testedInstance->getCustomer()->id)
                        ->isEqualTo($customer->id)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_customer())->isNull
                    ->object($this->testedInstance->set_customer($customer))->isTestedInstance
                    ->string($this->testedInstance->get_customer()->getId())->isIdenticalTo($customer->getId())

                    ->variable($this->newTestedInstance->customer)->isNull
                    ->variable($this->testedInstance->customer = $customer)
                    ->string($this->testedInstance->customer->getId())->isIdenticalTo($customer->getId())
        ;
    }

    public function testDescription()
    {
        $this
            ->if($description = $this->getRandomString(3, 64))
            ->and($tooShort = $this->getRandomString(2))
            ->and($tooLong = $this->getRandomString(65, 100))

            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getDescription())
                        ->isNull

                ->assert('Should throw an exception if the description is too small')
                    ->exception(function () use ($tooShort) {
                        $this->testedInstance->setDescription($tooShort);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('A valid description must be between 3 and 64 characters.')

                ->assert('Should throw an exception if the description is too long')
                    ->exception(function () use ($tooLong) {
                        $this->testedInstance->setDescription($tooLong);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('A valid description must be between 3 and 64 characters.')

                ->assert('Update value')
                    ->object($this->testedInstance->setDescription($description))
                        ->isTestedInstance

                    ->string($this->testedInstance->getDescription())
                        ->isIdenticalTo($description)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_description())->isNull
                    ->object($this->testedInstance->set_description($description))->isTestedInstance
                    ->string($this->testedInstance->get_description())->isIdenticalTo($description)

                    ->variable($this->newTestedInstance->description)->isNull
                    ->variable($this->testedInstance->description = $description)
                    ->string($this->testedInstance->description)->isIdenticalTo($description)
        ;
    }

    public function testEndpoint()
    {
        $this
            ->given($this->newTestedInstance)

            ->then
                ->assert('endpoint is payment_intents.')
                    ->string($this->testedInstance->getEndpoint())
                        ->isIdenticalTo('payment_intents')

                    ->string($this->testedInstance->endpoint)
                        ->isIdenticalTo('payment_intents')
        ;
    }

    public function testList()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($response = $this->mockJsonResponse('payment_intents', 'list'))
            ->and($this->calling($client)->request = $response)
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->and($options = $this->mockRequestOptions($config))

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
                    $date = new \DateTime();
                    $date->add(new \DateInterval('P1D'));

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

            ->assert('Invalid card filter')
                ->exception(function () {
                    testedClass::list(['card' => '']);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchCardFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid Card reference must have 29 characters.')
                ->exception(function () {
                    testedClass::list(['card' => new Stancer\Card('')]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchCardFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid Card reference must have 29 characters.')

                ->exception(function () {
                    testedClass::list(['card' => rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchCardFilterException::class)
                    ->message
                        ->isIdenticalTo('Card must be a card object or a string.')

            ->assert('Invalid sepa filter')
                ->exception(function () {
                    testedClass::list(['sepa' => new Stancer\Sepa('')]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchSepaFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid SEPA reference must have 29 characters.')

                ->exception(function () {
                    testedClass::list(['sepa' => '']);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchSepaFilterException::class)
                    ->message
                        ->isIdenticalTo('A valid SEPA reference must have 29 characters.')

                ->exception(function () {
                    testedClass::list(['sepa' => rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidSearchSepaFilterException::class)
                    ->message
                        ->isIdenticalTo('SEPA must be a string.')

            ->assert('Make request')
                ->if($limit = rand(1, 100))
                ->and($start = rand(0, PHP_INT_MAX))
                ->and($orderId = uniqid())
                ->and($created = time() - rand(10, 1000000))
                ->and($card = 'card_' . $this->getRandomString(24))
                ->and($sepa = 'sepa_' . $this->getRandomString(24))

                ->and($location = $this->newTestedInstance->getUri())
                ->and($terms1 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start,
                    'card' => $card,
                    'order_id' => $orderId,
                    'sepa' => $sepa,
                ])
                ->and($location1 = $location . '?' . http_build_query($terms1))

                ->and($terms2 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start + 2, // Based on json sample
                    'card' => $card,
                    'order_id' => $orderId,
                    'sepa' => $sepa,
                ])
                ->and($location2 = $location . '?' . http_build_query($terms2))
                ->then
                    ->generator(testedClass::list($terms1))
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"pi_3G2eWFsPOQSdWVXb7HnDP7Zt"')
                        /*
                         * This test is build a bit weirdly:
                         * the yields method of atoum call next(), this result in weirdly called requests.
                         * But we still test properly that we call the first page then the second page of PI.
                         */
                        ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location1, $options)
                                ->once
                            ->withArguments('GET', $location2, $options)
                                ->never
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"pi_hX59mjTCvC0TuGbkTEAFOF7l"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"pi_3G2eWFsPOQSdWVXb7HnDP7Zt"')

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location1, $options)
                                ->once // Called the first time
                            ->withArguments('GET', $location2, $options)
                                ->once

            ->assert('Empty response')
                ->given($body = [
                    'payment_intents' => [],
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

    public function testListNoMore()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($response = $this->mockJsonResponse('payment_intents', 'list-two-pi'))
            ->and($this->calling($client)->request = $response)
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->and($options = $this->mockRequestOptions($config))

            ->assert('make request with only two pi')
                ->if($limit = 2)
                ->and($start = 0)
                ->and($orderId = uniqid())
                ->and($created = time() - rand(10, 1000000))
                ->and($card = 'card_' . $this->getRandomString(24))
                ->and($sepa = 'sepa_' . $this->getRandomString(24))

                ->and($location = $this->newTestedInstance->getUri())
                ->and($terms1 = [
                    'created' => $created,
                    'start' => $start,
                    'limit' => $limit,
                    'card' => $card,
                    'order_id' => $orderId,
                    'sepa' => $sepa,
                ])
                ->and($location1 = $location . '?' . http_build_query($terms1))
                ->then
                    ->generator($gen = testedClass::list($terms1))
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"pi_3G2eWFsPOQSdWVXb7HnDP7Zt"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"pi_hX59mjTCvC0TuGbkTEAFOF7l"')
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                    ->call('request')
                        ->withArguments('GET', $location1, $options)
                            ->once // Called the first time
        ;
    }

    public function testListPayments()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($response = $this->mockJsonResponse('payment', 'list'))
            ->and($this->calling($client)->request = $response)
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->and($options = $this->mockRequestOptions($config))

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
                    $date = new \DateTime();
                    $date->add(new \DateInterval('P1D'));

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

            ->assert('Make request')
                ->if($limit = rand(1, 100))
                ->and($start = rand(0, PHP_INT_MAX))
                ->and($created = time() - rand(10, 1000000))

                ->and($location = $this->newTestedInstance('pi_' . $this->getRandomString(24))->getUri())
                ->and($terms1 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start,
                ])
                ->and($location1 = $location . '/payments?' . http_build_query($terms1))

                ->and($terms2 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start + 2, // Based on json sample
                ])
                ->and($location2 = $location . '/payments?' . http_build_query($terms2))
                ->then
                    ->generator($gen = $this->testedInstance->listPayments($terms1))
                        ->yields
                            ->object
                                ->isInstanceOf(Stancer\Payment::class)
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
                                ->isInstanceOf(Stancer\Payment::class)
                                ->toString
                                    ->isIdenticalTo('"paym_p5tjCrXHy93xtVtVqvEJoC1c"') // From json sample
                        ->yields
                            ->object
                                ->isInstanceOf(Stancer\Payment::class)
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
                ->and($location = $this->newTestedInstance('pi_' . $this->getRandomString(24))->getUri() . '/payments?' . $query)
                ->then
                    ->generator($gen = $this->testedInstance->listPayments($terms))
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
                ->and($location = $this->newTestedInstance('pi_' . $this->getRandomString(24))->getUri() . '/payments?' . $query)
                ->then
                    ->generator($gen = $this->testedInstance->listPayments($terms))
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location, $options)
                                ->once

            ->assert('Make request', 'alias')
                ->given($client = new mock\Stancer\Http\Client())
                ->and($response = $this->mockJsonResponse('payment', 'list'))
                ->and($this->calling($client)->request = $response)
                ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
                ->and($options = $this->mockRequestOptions($config))

                ->if($limit = rand(1, 100))
                ->and($start = rand(0, PHP_INT_MAX))
                ->and($created = time() - rand(10, 1000000))

                ->and($location = $this->newTestedInstance('pi_' . $this->getRandomString(24))->getUri())
                ->and($terms1 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start,
                ])
                ->and($location1 = $location . '/payments?' . http_build_query($terms1))

                ->and($terms2 = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start + 2, // Based on json sample
                ])
                ->and($location2 = $location . '/payments?' . http_build_query($terms2))
                ->then
                    ->generator($gen = $this->testedInstance->listPayments($terms1))
                        ->yields
                            ->object
                                ->isInstanceOf(Stancer\Payment::class)
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
                                ->isInstanceOf(Stancer\Payment::class)
                                ->toString
                                    ->isIdenticalTo('"paym_p5tjCrXHy93xtVtVqvEJoC1c"') // From json sample
                        ->yields
                            ->object
                                ->isInstanceOf(Stancer\Payment::class)
                                ->toString
                                    ->isIdenticalTo('"paym_JnU7xyTGJvxRWZuxvj78qz7e"') // From json sample

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location1, $options)
                                ->once // Called the first time
                            ->withArguments('GET', $location2, $options)
                                ->once

            ->assert('Empty response, alias')
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
                ->and($location = $this->newTestedInstance('pi_' . $this->getRandomString(24))->getUri() . '/payments?' . $query)
                ->then
                    ->generator($gen = $this->testedInstance->payments($terms))
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location, $options)
                                ->once

            ->assert('Invalid response, alias')
                ->given($this->calling($response)->getBody = new Stancer\Http\Stream(''))

                ->if($limit = rand(1, 100))
                ->and($terms = [
                    'limit' => $limit,
                ])
                ->and($query = http_build_query(['limit' => $limit, 'start' => 0]))
                ->and($location = $this->newTestedInstance('pi_' . $this->getRandomString(24))->getUri() . '/payments?' . $query)
                ->then
                    ->generator($gen = $this->testedInstance->payments($terms))
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location, $options)
                                ->once
        ;
    }

    public function testMetadata()
    {
        $this
            ->given($asString = $this->getRandomString(10))
            ->and($asInteger = $this->getRandomInteger(0, 100))
            ->and($asArray = [$asString, $asInteger])
            ->and($asDict = ['string' => $asString, 'integer' => $asInteger])
            ->and($asJsonObject = new Stancer\Stub\Core\StubObject(['string1' => $asString]))
            ->and($asStringable = new Stancer\Stub\Stringable($asString))

            ->assert('Allow strings')
                ->object($this->newTestedInstance->setMetadata($asString))
                    ->isTestedInstance

                ->string($this->testedInstance->getMetadata())
                    ->isIdenticalTo($asString)

            ->assert('Allow integers')
                ->object($this->newTestedInstance->setMetadata($asInteger))
                    ->isTestedInstance

                ->integer($this->testedInstance->getMetadata())
                    ->isIdenticalTo($asInteger)

            ->assert('Allow arrays')
                ->object($this->newTestedInstance->setMetadata($asArray))
                    ->isTestedInstance

                ->array($this->testedInstance->getMetadata())
                    ->hasSize(2)
                    ->string[0]
                        ->isIdenticalTo($asString)
                    ->integer[1]
                        ->isIdenticalTo($asInteger)

            ->assert('Allow associative arrays')
                ->object($this->newTestedInstance->setMetadata($asDict))
                    ->isTestedInstance

                ->array($this->testedInstance->getMetadata())
                    ->hasSize(2)
                    ->string['string']
                        ->isIdenticalTo($asString)
                    ->integer['integer']
                        ->isIdenticalTo($asInteger)

            ->assert('Allow JSON compatible objects')
                ->object($this->newTestedInstance->setMetadata($asJsonObject))
                    ->isTestedInstance

                ->object($this->testedInstance->getMetadata())
                    ->isIdenticalTo($asJsonObject)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('metadata')
                    ->child['metadata'](function ($metadata) use ($asString) {
                        $metadata
                            ->hasSize(1)
                            ->hasKey('string1')
                            ->string['string1']
                                ->isEqualTo($asString)
                        ;
                    })

            ->assert('Allow stringable objects')
                ->object($this->newTestedInstance->setMetadata($asStringable))
                    ->isTestedInstance

                ->object($this->testedInstance->getMetadata())
                    ->isIdenticalTo($asStringable)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('metadata')
                    ->string['metadata']
                        ->isEqualTo($asString)

            ->assert('Does not allow other objects')
                ->exception(function () {
                    $this->newTestedInstance->setMetadata(new \stdClass());
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidMetadataException::class)
                    ->message
                        ->isIdenticalTo('Objects are not allowed if not JSON serializable or stringable.')

            ->assert('Aliases')
                ->variable($this->newTestedInstance->metadata = $asString)
                ->string($this->testedInstance->metadata)->isIdenticalTo($asString)

                ->variable($this->newTestedInstance->metadata = $asInteger)
                ->integer($this->testedInstance->Metadata)->isIdenticalTo($asInteger)

                ->variable($this->newTestedInstance->metadata = $asArray)
                ->array($this->testedInstance->metadata)->hasSize(2)

                ->variable($this->newTestedInstance->metadata = $asDict)
                ->array($this->testedInstance->metadata)->hasSize(2)

                ->variable($this->newTestedInstance->metadata = $asJsonObject)
                ->object($this->testedInstance->metadata)->isIdenticalTo($asJsonObject)

                ->variable($this->newTestedInstance->metadata = $asStringable)
                ->object($this->testedInstance->metadata)->isIdenticalTo($asStringable)

                ->exception(function () {
                    $this->newTestedInstance->metadata = new \stdClass();
                })->isInstanceOf(Stancer\Exceptions\InvalidMetadataException::class)
        ;
    }

    /**
     * @dataProvider cardCurrencyDataProvider
     *
     * @param mixed $currency
     */
    public function testMethodsAllowed($currency)
    {
        $this
            ->given($methods = ['card', 'sepa'])

            ->assert('Should return an empty array as default')
                ->array($this->newTestedInstance->getMethodsAllowed())
                    ->isEmpty

            ->assert('Should allow array of strings')
                ->object($this->newTestedInstance->setMethodsAllowed($methods))
                    ->isTestedInstance

                ->array($this->testedInstance->getMethodsAllowed())
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                        ->isIdenticalTo(Stancer\Payment\MethodsAllowed::CARD)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                        ->isIdenticalTo(Stancer\Payment\MethodsAllowed::SEPA)

            ->assert('Should only allow known methods')
                ->if($this->newTestedInstance)
                ->and($value = uniqid())
                ->then
                    ->exception(function () use ($value) {
                        $this->testedInstance->setMethodsAllowed([$value]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isEqualTo('"' . $value . '" is not a valid method, please use one of the following: card, sepa')

            ->assert('Should allow to add methods')
                ->given($this->newTestedInstance)
                ->then
                    ->object($this->testedInstance->addMethodsAllowed($methods[0]))
                        ->isTestedInstance

                    ->array($this->testedInstance->getMethodsAllowed())
                        ->hasSize(1)
                        ->object[0]
                            ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                            ->isIdenticalTo(Stancer\Payment\MethodsAllowed::tryFrom($methods[0]))

            ->assert('Aliases')
                ->array($this->newTestedInstance->get_methods_allowed())->isEmpty
                ->object($this->testedInstance->add_methods_allowed($methods[0]))->isTestedInstance
                ->array($this->testedInstance->get_methods_allowed())->hasSize(1)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                        ->isIdenticalTo(Stancer\Payment\MethodsAllowed::tryFrom($methods[0]))
                ->object($this->newTestedInstance->set_methods_allowed($methods))->isTestedInstance
                ->array($this->testedInstance->get_methods_allowed())
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)

                ->array($this->newTestedInstance->methodsAllowed)->isEmpty
                ->variable($this->newTestedInstance->methodsAllowed = $methods)
                ->array($this->testedInstance->methodsAllowed)
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)

                ->array($this->newTestedInstance->methods_allowed)->isEmpty
                ->variable($this->newTestedInstance->methods_allowed = $methods)
                ->array($this->testedInstance->methods_allowed)
                    ->hasSize(2)
                    ->object[0]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
                    ->object[1]
                        ->isInstanceOf(Stancer\Payment\MethodsAllowed::class)
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

    public function testOrderId()
    {
        $this
            ->if($tooShort = '')
            ->and($tooLong = $this->getRandomString(37, 40))
            ->and($orderId = $this->getRandomString(1, 36))

            ->then
                ->assert('Must have one character')
                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->setOrderId($tooShort);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                        ->message
                            ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->assert('Must have less than 36 characters')
                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->setOrderId($tooLong);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                        ->message
                            ->isIdenticalTo('A valid order ID must be between 1 and 36 characters.')

                ->assert('With a valid order ID')
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

                ->assert('Aliases')
                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->set_order_id($tooShort);
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)

                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->set_order_id($tooLong);
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->object($this->newTestedInstance->set_order_id($orderId))->isTestedInstance
                    ->string($this->testedInstance->get_order_id())->isIdenticalTo($orderId)

                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->orderId = $tooShort;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)

                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->orderId = $tooLong;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->variable($this->newTestedInstance->orderId = $orderId)
                    ->string($this->testedInstance->orderId)->isIdenticalTo($orderId)

                    ->exception(function () use ($tooShort) {
                        $this->newTestedInstance->order_id = $tooShort;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)

                    ->exception(function () use ($tooLong) {
                        $this->newTestedInstance->order_id = $tooLong;
                    })->isInstanceOf(Stancer\Exceptions\InvalidOrderIdException::class)
                    ->variable($this->newTestedInstance->order_id = $orderId)
                    ->string($this->testedInstance->order_id)->isIdenticalTo($orderId)
        ;
    }

    public function testPayment()
    {
        $this
            ->if($payment = new Stancer\Payment())
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getPayment())
                        ->isNull

                ->assert('Should throw an exception if setted')
                    ->exception(function () use ($payment) {
                        $this->testedInstance->setPayment($payment);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "payment".')

                ->assert('Get value')
                    ->if($this->testedInstance->hydrate(['payment' => $payment]))
                    ->then
                        ->object($this->testedInstance->getPayment())
                            ->isIdenticalTo($payment)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_payment())->isNull
                    ->variable($this->testedInstance->hydrate(['payment' => $payment]))
                    ->object($this->testedInstance->get_payment())->isIdenticalTo($payment)

                    ->variable($this->newTestedInstance->payment)->isNull
                    ->variable($this->testedInstance->hydrate(['payment' => $payment]))
                    ->object($this->testedInstance->payment)->isIdenticalTo($payment)
        ;
    }

    public function testPost_capture()
    {
        $this
        ->given($this->newTestedInstance->setAmount('300'))
        ->and($this->testedInstance->setCurrency('eur'))

        ->assert('Does not allow Capture new payment')
            ->exception(function () {
                $this->testedInstance->capture();
            })
                ->isInstanceOf(Stancer\Exceptions\BadRequestException::class)
                ->message
                    ->isIdenticalTo('The payment_intent must be authorized to be captured.')

        ->assert('Does not allow Capture non authorized payment')
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->if($response = $this->mockJsonResponse('payment_intents', 'intent'))
            ->and($this->calling($client)->request = $response)

            ->object($this->testedInstance->send())
                ->isTestedInstance

            ->object($this->testedInstance->getStatus())
                ->isEqualTo(Stancer\PaymentIntent\Status::REQUIRE_AUTHENTICATION)

            ->exception(function () {
                $this->testedInstance->capture();
            })
            ->isInstanceOf(Stancer\Exceptions\BadRequestException::class)
            ->message
                ->isIdenticalTo('The payment_intent must be authorized to be captured.')

        ->assert('Capture an authorize payment')
            ->given($client = new mock\Stancer\Http\Client())
            ->and($this->testedInstance->setDescription('adding a field to send non-empty object'))
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->if($response = $this->mockJsonResponse('payment_intents', 'authorized-pi'))
            ->and($this->calling($client)->request = $response)

                ->object($this->testedInstance->send())
                    ->isTestedInstance

                ->object($this->testedInstance->getStatus())
                    ->isEqualTo(Stancer\PaymentIntent\Status::AUTHORIZED)

            ->then
                ->if($newresponse = $this->mockJsonResponse('payment_intents', 'capture'))
                ->and($this->calling($client)->request = $newresponse)
                ->and($options = $this->mockRequestOptions($config, ['body' => json_encode(['id' => 'pi_UBW7wgcQiRv0YsfnaKjc4XvM'])]))
                ->and($location = $this->testedInstance->uri . '/capture')

                    ->object($this->testedInstance->capture())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $location, $options)
                                ->once

                    ->object($this->testedInstance->getStatus())
                        ->isInstanceOf(Stancer\PaymentIntent\Status::class)
                        ->isEqualTo(Stancer\PaymentIntent\Status::CAPTURED)
        ;
    }

    public function testReturnUrl()
    {
        $this
            ->given($https = 'https://www.example.org/?' . uniqid())
            ->and($http = 'http://www.example.org/?' . uniqid())

            ->if($this->newTestedInstance)
            ->then
                ->assert('Default value')
                    ->variable($this->testedInstance->getReturnUrl())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setReturnUrl($https))
                        ->isTestedInstance

                    ->string($this->testedInstance->getReturnUrl())
                        ->isIdenticalTo($https)

                ->assert('Does not allow HTTP URL')
                    ->exception(function () use ($http) {
                        $this->testedInstance->setReturnUrl($http);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
                        ->message
                            ->isIdenticalTo('You must provide an HTTPS URL.')

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_return_url())->isNull
                    ->object($this->testedInstance->set_return_url($https))->isTestedInstance
                    ->string($this->testedInstance->get_return_url())->isIdenticalTo($https)

                    ->variable($this->newTestedInstance->returnUrl)->isNull
                    ->variable($this->testedInstance->returnUrl = $https)
                    ->string($this->testedInstance->returnUrl)->isIdenticalTo($https)

                    ->variable($this->newTestedInstance->return_url)->isNull
                    ->variable($this->testedInstance->return_url = $https)
                    ->string($this->testedInstance->return_url)->isIdenticalTo($https)
        ;
    }

    public function testSend_flat()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->assert('Flat send')
                ->given($card = new Stancer\Card())
                ->and($cardNumber = $this->cardNumberDataProvider(true))
                ->and($card->setNumber($cardNumber))
                ->and($card->setCvc((string) $this->getRandomInteger(100, 999)))
                ->and($card->setName($this->getRandomString(5, 25)))
                ->and($card->setExpMonth($this->getRandomInteger(1, 12)))
                ->and($card->setExpYear(date('Y') + $this->getRandomInteger(5, 10)))
                ->and($cardId = 'card_' . $this->getRandomString(24))
                ->then
                    ->if($cardLocation = $card->getUri())
                    ->if($cardOptions = $this->mockRequestOptions($config, ['body' => json_encode($card)]))

                    ->if($this->calling($client)->request[1] = $this->mockResponse(json_encode(['id' => $cardId])))
                    ->object($card->send())
                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $cardLocation, $cardOptions)
                                ->once
                ->given($customer = new Stancer\Customer())
                ->and($customer->setEmail(uniqid()))
                ->and($customer->setMobile(uniqid()))
                ->and($customer->setName(uniqid()))
                ->and($customerId = 'cust_' . $this->getRandomString(24))
                ->then
                    ->if($customerLocation = $customer->getUri())
                    ->if($customerOptions = $this->mockRequestOptions($config, ['body' => json_encode($customer)]))
                    ->if($this->calling($client)->request[2] = $this->mockResponse(json_encode(['id' => $customerId])))
                    ->object($customer->send())
                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $customerLocation, $customerOptions)
                                ->once
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($this->getRandomInteger(50, 1000)))
                ->and($this->testedInstance->setCurrency('eur'))
                ->and($this->testedInstance->setCard($card))
                ->and($this->testedInstance->setCustomer($customer))
                ->then
                    ->if($location = $this->testedInstance->getUri())
                    ->if($options = $this->mockRequestOptions($config, ['body' => json_encode($this->testedInstance)]))
                    ->if($this->calling($client)->request = $this->mockJsonResponse('payment_intents', 'intent'))
                    ->object($this->testedInstance->send())
                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $location, $options)
                                ->once
        ;
    }

    public function testSend_flatenedCard()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->assert('with card object')
                ->given($this->newTestedInstance)
                ->given($card = new Stancer\Card())
                ->and($this->testedInstance->setAmount($this->getRandomInteger(50, 1000)))
                ->and($this->testedInstance->setCurrency('eur'))
                ->and($this->testedInstance->setCard($card))
                ->and($cardNumber = $this->cardNumberDataProvider(true))
                ->and($card->setNumber($cardNumber))
                ->and($card->setCvc((string) $this->getRandomInteger(100, 999)))
                ->and($card->setName($this->getRandomString(5, 25)))
                ->and($card->setExpMonth($this->getRandomInteger(1, 12)))
                ->and($card->setExpYear(date('Y') + $this->getRandomInteger(5, 10)))
                ->and($cardId = 'card_' . $this->getRandomString(24))

                ->then
                ->if($location = $this->testedInstance->getUri())
                ->if($cardLocation = $card->getUri())
                ->if($cardOptions = $this->mockRequestOptions($config, ['body' => json_encode($card)]))
                ->if($body = $this->testedInstance->jsonSerialize())
                ->if($this->calling($client)->request[0] = $this->mockResponse(json_encode(['id' => $cardId])))

                ->object($this->testedInstance->send())
                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $cardLocation, $cardOptions)
                            ->once

                ->if($this->calling($client)->request[1] = $this->mockJsonResponse('payment_intents', 'intent'))
                ->if($options = $this->mockRequestOptions(
                    $config,
                    [
                        'body' => json_encode(
                            array_merge(
                                $body,
                                ['card' => $cardId]
                            )
                        )]
                ))
                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $location, $options)
                                ->once
                ->string($this->testedInstance->getUri())
                    ->isEqualTo($location . $this->testedInstance->getId())
        ;
    }

    public function testSend_flatenedCustomer()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->assert('send with customer object')
                ->given($this->newTestedInstance)
                ->given($customer = new Stancer\Customer())
                ->and($this->testedInstance->setAmount($this->getRandomInteger(50, 1000)))
                ->and($this->testedInstance->setCurrency('eur'))
                ->and($this->testedInstance->setCustomer($customer))
                ->and($customer->setName($this->getRandomString(5, 25)))
                ->and($customer->setMobile((string) $this->getRandomNumber()))
                ->and($customer->setEmail($customer->getName() . '@example.com'))
                ->and($customerId = 'cust_' . $this->getRandomString(24))
                ->then
                    ->if($location = $this->testedInstance->getUri())
                    ->if($customerLocation = $customer->getUri())
                    ->if($customerOptions = $this->mockRequestOptions($config, ['body' => json_encode($customer)]))
                    ->if($body = $this->testedInstance->jsonSerialize())
                    ->if($this->calling($client)->request[0] = $this->mockResponse(json_encode(['id' => $customerId])))

                    ->object($this->testedInstance->send())
                        ->isTestedInstance()
                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $customerLocation, $customerOptions)
                                ->once
                    ->string($customer->getId())
                        ->isIdenticalTo($customerId)

                    ->if($this->calling($client)->request[1] = $this->mockJsonResponse('payment_intents', 'intent'))
                    ->if($options = $this->mockRequestOptions(
                        $config,
                        [
                            'body' => json_encode(
                                array_merge(
                                    $body,
                                    ['customer' => $customerId]
                                )
                            )]
                    ))
                        ->mock($client)
                            ->call('request')
                                ->withArguments('POST', $location, $options)
                                    ->once
                    ->string($this->testedInstance->getUri())
                        ->isEqualTo($location . $this->testedInstance->getId())
        ;
    }

    public function testSend_flatenedSepa()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->assert('flatening Sepa')
                ->given($this->newTestedInstance)
                ->given($sepa = new Stancer\Sepa())
                ->and($this->testedInstance->setAmount($this->getRandomInteger(50, 1000)))
                ->and($this->testedInstance->setCurrency('eur'))
                ->and($sepa->setIban($this->ibanDataProvider(true)))
                ->and($sepa->setName($this->getRandomString(3, 64)))
                ->and($this->testedInstance->setSepa($sepa))
                ->and($sepaId = 'sepa_' . $this->getRandomString(24))

                ->then
                    ->if($location = $this->testedInstance->getUri())
                    ->if($sepaLocation = $sepa->getUri())
                    ->if($sepaOptions = $this->mockRequestOptions($config, ['body' => json_encode($sepa)]))
                    ->if($body = $this->testedInstance->jsonSerialize())
                    ->if($this->calling($client)->request = $this->mockResponse(json_encode(['id' => $sepaId])))

                    ->object($this->testedInstance->send())
                        ->isTestedInstance()
                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $sepaLocation, $sepaOptions)
                                ->once

                    ->if($this->calling($client)->request = $this->mockJsonResponse('payment_intents', 'intent'))
                    ->if($options = $this->mockRequestOptions(
                        $config,
                        [
                            'body' => json_encode(
                                array_merge(
                                    $body,
                                    ['sepa' => $sepaId]
                                )
                            )]
                    ))
                        ->mock($client)
                            ->call('request')
                                ->withArguments('POST', $location, $options)
                                    ->once
                    ->string($this->testedInstance->getUri())
                        ->isEqualTo($location . $this->testedInstance->getId())
        ;
    }

    public function testSend_patchCard()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->and($response = $this->mockJsonResponse('payment_intents', 'create-no-method'))

            ->given($this->newTestedInstance->setCustomer(new Stancer\Customer('cust_dFM2igoh05T3CYpJ2TUJtZGh')))
            ->and($this->testedInstance->setAmount('300'))
            ->and($this->testedInstance->setCurrency('eur'))
            ->and($this->testedInstance->setThreeds(Stancer\ThreeDomainsSecure\Status::REQUIRED))

            ->then
                ->assert('send Payment Intent without payment method.')
                    ->and($json = [
                        'amount' => 300,
                        'currency' => 'eur',
                        'customer' => 'cust_dFM2igoh05T3CYpJ2TUJtZGh',
                        'threeds' => 'required',
                    ])

                    ->and($options = $this->mockRequestOptions(
                        $config,
                        ['body' => json_encode($json)]
                    ))
                    ->and($location = $this->testedInstance->getUri())

                        ->then
                        ->if($this->calling($client)->request = $response)

                            ->object($this->testedInstance->send())
                                ->isTestedInstance
                            ->mock($client)
                                ->call('request')
                                        ->withArguments('POST', $location, $options)
                                        ->once
            ->then
                ->assert('adding payment method')
                    ->if($response = $this->mockJsonResponse('payment_intents', 'intent'))

                    ->given($json = ['card' => 'card_XZ8h7vcnEIecJkDdlnI5gyD5'])
                    ->and($this->testedInstance->setCard(new Stancer\Card('card_XZ8h7vcnEIecJkDdlnI5gyD5')))
                    ->and($newOption = $this->mockRequestOptions($config, ['body' => json_encode($json)]))
                    ->and($newlocation = $this->testedInstance->uri)
                    ->then
                        ->object($this->testedInstance->send())
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->withArguments('PATCH', $newlocation, $newOption)
                                    ->once
        ;
    }

    public function testSend_withCard()
    {
        $this
        ->given($client = new mock\Stancer\Http\Client())
        ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))

        ->given($this->newTestedInstance->setCustomer(new Stancer\Customer('cust_dFM2igoh05T3CYpJ2TUJtZGh')))
        ->and($this->testedInstance->setAmount('300'))
        ->and($this->testedInstance->setCurrency('eur'))
        ->and($this->testedInstance->setThreeds(Stancer\ThreeDomainsSecure\Status::REQUIRED))

        ->then
            ->given($card = new Stancer\card())
            ->assert('create card')
                ->given($response = $this->mockJsonResponse('card', 'read'))
                ->if($card = new Stancer\card())
                ->and($card->setNumber('4242424242424242'))
                ->and($card->setExpMonth(12))
                ->and($card->setExpYear(2029))
                ->and($body = [
                    'exp_month' => 12,
                    'exp_year' => 2029,
                    'number' => '4242424242424242',
                ])

                ->then
                    ->if($this->calling($client)->request = $response)
                    ->and($location = $card->uri)
                    ->and($options = $this->mockRequestOptions($config, ['body' => json_encode($body)]))
                        ->object($this->testedInstance->setcard($card))
                            ->isTestedInstance
                        ->object($card->send)
                        ->mock($client)
                            ->call('request')
                                ->withArguments('POST', $location, $options)
                                    ->once

        ->then
            ->assert('send Payment Intent with cardObject.')
                ->given($response = $this->mockJsonResponse('payment_intents', 'intent'))
                ->and($json = [
                    'amount' => 300,
                    'card' => 'card_XZ8h7vcnEIecJkDdlnI5gyD5',
                    'currency' => 'eur',
                    'customer' => 'cust_dFM2igoh05T3CYpJ2TUJtZGh',
                    'threeds' => 'required',
                ])

                ->and($options = $this->mockRequestOptions(
                    $config,
                    ['body' => json_encode($json)]
                ))
                ->and($location = $this->testedInstance->getUri())
                ->then
                    ->if($this->calling($client)->request = $response)
                        ->object($this->testedInstance->send())
                            ->isTestedInstance
                        ->mock($client)
                            ->call('request')
                                ->withArguments('POST', $location, $options)
                                    ->once
                    ->assert('hydratation is correct based on fixtures')
                        ->object($this->testedInstance)
                            ->isInstanceOf(Stancer\PaymentIntent::class)
                        ->object($responseCard = $this->testedInstance->getCard())
                            ->isInstanceOf(Stancer\Card::class)
                        ->string($responseCard->getId())
                            ->isIdenticalTo('card_XZ8h7vcnEIecJkDdlnI5gyD5')
                        ->object($responseCustomer = $this->testedInstance->getCustomer())
                            ->isInstanceOf(Stancer\Customer::class)
                        ->string($responseCustomer->getId())
                            ->isIdenticalTo('cust_dFM2igoh05T3CYpJ2TUJtZGh')
                        ->integer($this->testedInstance->getAmount())
                            ->isIdenticalTo(300)
                        ->boolean($this->testedInstance->getCapture())
                            ->isTrue()
                        ->object($responseCurrency = $this->testedInstance->getCurrency())
                            ->isInstanceOf(Stancer\Currency::class)
                            ->isEqualTo(Stancer\Currency::EUR)
                        ->string($responseCurrency->value)
                            ->isIdenticalTo(Stancer\Currency::EUR->value)
                        ->variable($this->testedInstance->getDescription())
                            ->isNull()
                        ->string($this->testedInstance->getId())
                            ->isIdenticalTo('pi_zoiuKRVPoeSUKufHxghJn99T')
                        ->variable($this->testedInstance->getMetadata())
                            ->isNull()
                        ->array($this->testedInstance->getMethodsAllowed())
                            ->isEqualTo([Stancer\Payment\MethodsAllowed::CARD, Stancer\Payment\MethodsAllowed::SEPA])
                        ->variable($this->testedInstance->getOrderId())
                            ->isNull()
                        ->variable($this->testedInstance->getPayment())
                            ->isNull()
                        ->variable($this->testedInstance->getReturnUrl())
                            ->isNull()
                        ->variable($this->testedInstance->getSepa())
                            ->isNull()
                        ->object($this->testedInstance->getStatus())
                            ->isInstanceOf(Stancer\PaymentIntent\Status::class)
                            ->isIdenticalTo(Stancer\PaymentIntent\Status::REQUIRE_AUTHENTICATION)
                        ->object($this->testedInstance->getThreeds())
                            ->isInstanceOf(Stancer\ThreeDomainsSecure\Status::class)
                            ->isIdenticalTo(Stancer\ThreeDomainsSecure\Status::REQUIRED)
        ;
    }

    public function testSend_withSepa()
    {
        $this
        ->given($client = new mock\Stancer\Http\Client())
        ->and($config = $this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))

        ->given($this->newTestedInstance->setCustomer(new Stancer\Customer('cust_dFM2igoh05T3CYpJ2TUJtZGh')))
        ->and($this->testedInstance->setAmount('300'))
        ->and($this->testedInstance->setCurrency('eur'))
        ->and($this->testedInstance->setThreeds(Stancer\ThreeDomainsSecure\Status::REQUIRED))

        ->then
            ->given($sepa = new Stancer\Sepa())
            ->assert('create Sepa')
                ->given($response = $this->mockJsonResponse('sepa', 'read'))
                ->if($sepa = new Stancer\Sepa())
                ->and($sepa->setName('John Doe'))
                ->and($sepa->setIban('SE3550000000054910000003'))

                ->then
                    ->if($this->calling($client)->request = $this->mockResponse(json_encode(['id' => 'sepa_bIvCZePYqfMlU11TANT8IqL1'])))
                    ->and($location = $sepa->uri)
                    ->and($options = $this->mockRequestOptions($config, ['body' => json_encode($sepa)]))
                        ->object($sepa->send())
                            ->isInstanceOf('Stancer\\Sepa')
                        ->mock($client)
                            ->call('request')
                                ->withArguments('POST', $location, $options)
                                    ->once

        ->then
            ->assert('send Payment Intent with SepaObject.')
                ->given($json = [
                    'amount' => 300,
                    'currency' => 'eur',
                    'customer' => 'cust_dFM2igoh05T3CYpJ2TUJtZGh',
                    'sepa' => 'sepa_bIvCZePYqfMlU11TANT8IqL1',
                    'threeds' => 'required',
                ])
                ->and($this->testedInstance->setSepa($sepa))
                ->and($options = $this->mockRequestOptions(
                    $config,
                    ['body' => json_encode($json)]
                ))
                ->and($location = $this->testedInstance->getUri())

                ->then
                    ->if($this->calling($client)->request = $response)
                        ->object($this->testedInstance->send())
                            ->isTestedInstance
                        ->mock($client)
                            ->call('request')
                                ->withArguments('POST', $location, $options)
                                    ->once

        ->then
            ->assert('send Payment Intent with SEPA ID')
                ->given($this->newTestedInstance->setSepa(new Stancer\Sepa('sepa_bIvCZePYqfMlU11TANT8IqL1')))
                ->and($this->testedInstance->setAmount('300'))
                ->and($this->testedInstance->setCurrency('eur'))
                ->and($json = [
                    'amount' => 300,
                    'currency' => 'eur',
                    'sepa' => 'sepa_bIvCZePYqfMlU11TANT8IqL1',
                ])

                ->and($newOptions = $this->mockRequestOptions(
                    $config,
                    ['body' => json_encode($json)]
                ))
                ->and($newLocation = $this->testedInstance->getUri())

            ->then
            ->given($response = $this->mockJsonResponse('payment_intents', 'intent-sepa'))
                ->if($this->calling($client)->request = $response)
                    ->object($this->testedInstance->send())
                        ->isTestedInstance
                        ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $newLocation, $newOptions)
                                ->once
                ->assert('hydratation is correct based on fixtures')

                        ->object($this->testedInstance)
                            ->isInstanceOf(Stancer\PaymentIntent::class)

                        ->variable($this->testedInstance->getCard())
                            ->isNull()

                        ->object($responseCustomer = $this->testedInstance->getCustomer())
                            ->isInstanceOf(Stancer\Customer::class)

                        ->string($responseCustomer->getId())
                            ->isIdenticalTo('cust_dFM2igoh05T3CYpJ2TUJtZGh')

                        ->integer($this->testedInstance->getAmount())
                            ->isIdenticalTo(300)

                        ->boolean($this->testedInstance->getCapture())
                            ->isTrue()

                        ->object($responseCurrency = $this->testedInstance->getCurrency())
                            ->isInstanceOf(Stancer\Currency::class)
                            ->isEqualTo(Stancer\Currency::EUR)

                        ->string($responseCurrency->value)
                            ->isIdenticalTo(Stancer\Currency::EUR->value)

                        ->variable($this->testedInstance->getDescription())
                            ->isNull()

                        ->string($this->testedInstance->getId())
                            ->isIdenticalTo('pi_zoiuKRVPoeSUKufHxghJn99T')

                        ->variable($this->testedInstance->getMetadata())
                            ->isNull()

                        ->array($this->testedInstance->getMethodsAllowed())
                            ->isEqualTo([Stancer\Payment\MethodsAllowed::CARD, Stancer\Payment\MethodsAllowed::SEPA])

                        ->variable($this->testedInstance->getOrderId())
                            ->isNull()

                        ->variable($this->testedInstance->getPayment())
                            ->isNull()

                        ->variable($this->testedInstance->getReturnUrl())
                            ->isNull()

                        ->object($responseSepa = $this->testedInstance->getSepa())
                            ->isInstanceOf(Stancer\Sepa::class)

                        ->string($responseSepa->getId())
                            ->isIdenticalTo('sepa_bIvCZePYqfMlU11TANT8IqL1')

                        ->object($this->testedInstance->getStatus())
                            ->isInstanceOf(Stancer\PaymentIntent\Status::class)
                            ->isIdenticalTo(Stancer\PaymentIntent\Status::REQUIRE_AUTHENTICATION)

                        ->object($this->testedInstance->getThreeds())
                            ->isInstanceOf(Stancer\ThreeDomainsSecure\Status::class)
                            ->isIdenticalTo(Stancer\ThreeDomainsSecure\Status::REQUIRED)
        ;
    }

    public function testSepa()
    {
        $this
            ->if($sepa = new Stancer\Sepa('sepa_' . $this->getRandomString(24)))
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getSepa())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setSepa($sepa))
                        ->isTestedInstance

                    ->object($this->testedInstance->getSepa())
                        ->isInstanceof(Stancer\Sepa::class)
                    ->string($this->testedInstance->getSepa()->getId())
                        ->isIdenticalTo($sepa->getId())

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_sepa())->isNull
                    ->object($this->testedInstance->set_sepa($sepa))->isTestedInstance
                    ->object($this->testedInstance->get_sepa())->isIdenticalTo($sepa)

                    ->variable($this->newTestedInstance->sepa)->isNull
                    ->variable($this->testedInstance->sepa = $sepa)
                    ->object($this->testedInstance->sepa)->isIdenticalTo($sepa)
        ;
    }

    public function testStatus()
    {
        $this
            ->if($status = $this->choose(Stancer\PaymentIntent\Status::cases()))
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getStatus())
                        ->isNull

                ->assert('Should throw an exception if setted')
                    ->exception(function () use ($status) {
                        $this->testedInstance->setStatus($status);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "status".')

                ->assert('Get value')
                    ->if($this->testedInstance->hydrate(['status' => $status]))
                    ->then
                        ->object($this->testedInstance->getStatus())
                            ->isIdenticalTo($status)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_status())->isNull
                    ->object($this->testedInstance->hydrate(['status' => $status]))->isTestedInstance
                    ->object($this->testedInstance->get_status())->isIdenticalTo($status)

                    ->variable($this->newTestedInstance->status)->isNull
                    ->object($this->testedInstance->hydrate(['status' => $status]))->isTestedInstance
                    ->object($this->testedInstance->status)->isIdenticalTo($status)
        ;
    }

    public function testThreeDS()
    {
        $this
            ->if($threeDS = $this->choose(Stancer\ThreeDomainsSecure\Status::cases()))
            ->then
                ->assert('Default value')
                    ->variable($this->newTestedInstance->getThreeds())
                        ->isNull

                ->assert('Update value')
                    ->object($this->testedInstance->setThreeds($threeDS))
                        ->isTestedInstance

                    ->object($this->testedInstance->getThreeds())
                        ->isIdenticalTo($threeDS)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->getThreeDS())->isNull
                    ->object($this->testedInstance->setThreeDS($threeDS))->isTestedInstance
                    ->object($this->testedInstance->getThreeDS())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get_three_ds())->isNull
                    ->object($this->testedInstance->set_three_ds($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get_three_ds())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->threeDS)->isNull
                    ->variable($this->testedInstance->threeDS = $threeDS)
                    ->object($this->testedInstance->threeDS)->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get_threeds())->isNull
                    ->object($this->testedInstance->set_threeds($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get_threeds())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->threeds)->isNull
                    ->variable($this->testedInstance->threeds = $threeDS)
                    ->object($this->testedInstance->threeds)->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->three_ds)->isNull
                    ->variable($this->testedInstance->three_ds = $threeDS)
                    ->object($this->testedInstance->three_ds)->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get3DS())->isNull
                    ->object($this->testedInstance->set3DS($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get3DS())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->get_3ds())->isNull
                    ->object($this->testedInstance->set_3ds($threeDS))->isTestedInstance
                    ->object($this->testedInstance->get_3ds())->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->{'3DS'})->isNull
                    ->variable($this->testedInstance->{'3DS'} = $threeDS)
                    ->object($this->testedInstance->{'3DS'})->isIdenticalTo($threeDS)

                    ->variable($this->newTestedInstance->{'3ds'})->isNull
                    ->variable($this->testedInstance->{'3ds'} = $threeDS)
                    ->object($this->testedInstance->{'3ds'})->isIdenticalTo($threeDS)
        ;
    }

    public function testUrl()
    {
        $this
            ->if($url = uniqid())
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getUrl())
                        ->isNull

                ->assert('Should throw an exception if setted')
                    ->exception(function () use ($url) {
                        $this->testedInstance->setUrl($url);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "url".')

                ->assert('Get value')
                    ->if($this->testedInstance->hydrate(['url' => $url]))
                    ->then
                        ->string($this->testedInstance->getUrl())
                            ->isIdenticalTo($url)
                        ->string($this->testedInstance->getPaymentPageUrl())
                            ->isIdenticalTo($url)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_url())->isNull
                    ->variable($this->testedInstance->hydrate(['url' => $url]))
                    ->string($this->testedInstance->get_url())->isIdenticalTo($url)

                    ->variable($this->newTestedInstance->url)->isNull
                    ->variable($this->testedInstance->hydrate(['url' => $url]))
                    ->string($this->testedInstance->url)->isIdenticalTo($url)

                    ->variable($this->newTestedInstance->get_payment_page_url())->isNull
                    ->variable($this->testedInstance->hydrate(['url' => $url]))
                    ->string($this->testedInstance->get_payment_page_url())->isIdenticalTo($url)

                    ->variable($this->newTestedInstance->getPaymentPageUrl())->isNull
                    ->variable($this->testedInstance->hydrate(['url' => $url]))
                    ->string($this->testedInstance->getPaymentPageUrl())->isIdenticalTo($url)
        ;
    }
}
