<?php

namespace Stancer\tests\unit;

use DateInterval;
use DateTime;
use Stancer;
use Stancer\Payout as testedClass;
use mock;

class Payout extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
        ;
    }

    public function testGetAmount()
    {
        $this
            ->if($value = rand(50, 99999))
            ->then
                ->variable($this->newTestedInstance->getAmount())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setAmount($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "amount".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->integer($this->newTestedInstance(uniqid())->getAmount())
                    ->isIdenticalTo(9400)

                ->integer($this->testedInstance->amount)
                    ->isIdenticalTo(9400)
        ;
    }

    public function testGetCurrency()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getCurrency())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setCurrency($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "currency".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->string($this->newTestedInstance(uniqid())->getCurrency())
                    ->isIdenticalTo('eur')

                ->string($this->testedInstance->currency)
                    ->isIdenticalTo('eur')
        ;
    }

    public function testGetDateBank()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getDateBank())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setDateBank($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "dateBank".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->dateTime($this->newTestedInstance(uniqid())->getDateBank())
                    ->hasDate(2022, 1, 27)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->dateBank)
                    ->hasDate(2022, 1, 27)
                    ->hasTime(0, 0, 0)
        ;
    }

    public function testGetDatePaym()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getDatePaym())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setDatePaym($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "datePaym".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->dateTime($this->newTestedInstance(uniqid())->getDatePaym())
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->datePaym)
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->getDatePayment())
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)

                ->dateTime($this->testedInstance->datePayment)
                    ->hasDate(2022, 1, 20)
                    ->hasTime(0, 0, 0)
        ;
    }

    public function testDisputesDetails()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))

            ->and($config = $this->mockConfig($client))
            ->and($options = $this->mockRequestOptions($config))

            ->if($this->newTestedInstance('pout_GexV3lpllrBkyRny15qfsMC0')) // from fixture
            ->and($baseLocation = $this->testedInstance->getUri() . '/disputes')
            ->then
                ->assert('Invalid limit')
                    ->exception(function () {
                        $this->testedInstance->details->disputes(['limit' => 0]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                    ->exception(function () {
                        $this->testedInstance->details->disputes(['limit' => 101]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                    ->exception(function () {
                        $this->testedInstance->details->disputes(['limit' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                ->assert('Invalid start')
                    ->exception(function () {
                        $this->testedInstance->details->disputes(['start' => -1]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                        ->message
                            ->isIdenticalTo('Start must be a positive integer.')

                    ->exception(function () {
                        $this->testedInstance->details->disputes(['start' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                        ->message
                            ->isIdenticalTo('Start must be a positive integer.')

                ->assert('Invalid created filter')
                    ->exception(function () {
                        $this->testedInstance->details->disputes(['created' => time() + 100]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be in the past.')

                    ->exception(function () {
                        $date = new DateTime();
                        $date->add(new DateInterval('P1D'));

                        $this->testedInstance->details->disputes(['created' => $date]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be in the past.')

                    ->exception(function () {
                        $this->testedInstance->details->disputes(['created' => 0]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

                    ->exception(function () {
                        $this->testedInstance->details->disputes(['created' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

                ->assert('Make request')
                    ->if($limit = rand(1, 100))
                    ->and($start = rand(0, PHP_INT_MAX))
                    ->and($created = time() - rand(10, 1000000))

                    ->and($terms1 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($terms2 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start + 2, // Based on json sample
                    ])
                    ->and($location2 = $baseLocation . '?' . http_build_query($terms2))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('dispute', 'list'))
                    ->then
                        ->generator($gen = $this->testedInstance->details->disputes($terms1))
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Dispute::class)
                                    ->toString
                                        ->isIdenticalTo('"dspt_kkyLpFvqM8JYQrBJlhN9bxSY"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->never

                        ->generator($gen)
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Dispute::class)
                                    ->toString
                                        ->isIdenticalTo('"dspt_VIk2SufjagxqT6ZtoRbqUkUm"') // From json sample
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Dispute::class)
                                    ->toString
                                        ->isIdenticalTo('"dspt_kkyLpFvqM8JYQrBJlhN9bxSY"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->once

                ->assert('Empty response')
                    ->given($body = [
                        'disputes' => [],
                        'range' => [
                            'has_more' => false,
                            'limit' => 10,
                        ],
                    ])
                    ->and($this->calling($client)->request = $this->mockResponse(json_encode($body)))

                    ->if($limit = rand(1, 100))
                    ->and($terms = [
                        'limit' => $limit,
                    ])
                    ->and($query = http_build_query(['limit' => $limit, 'start' => 0]))
                    ->and($location = $baseLocation . '?' . $query)
                    ->then
                        ->generator($this->testedInstance->details->disputes($terms))
                            ->yields
                                ->variable
                                    ->isNull

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location, $options)
                                    ->once

                ->assert('Without params')
                    ->if($terms1 = [
                        'limit' => 100,
                        'start' => 0,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('dispute', 'list'))
                    ->then
                        ->generator($this->testedInstance->details->disputes())
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Dispute::class)
                                    ->toString
                                        ->isIdenticalTo('"dspt_kkyLpFvqM8JYQrBJlhN9bxSY"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once

                ->assert('With long name')
                    ->if($limit = rand(1, 100))
                    ->and($start = rand(0, PHP_INT_MAX))
                    ->and($created = time() - rand(10, 1000000))

                    ->and($terms1 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($terms2 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start + 2, // Based on json sample
                    ])
                    ->and($location2 = $baseLocation . '?' . http_build_query($terms2))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('dispute', 'list'))
                    ->then
                        ->generator($gen = $this->testedInstance->details->listDisputes($terms1))
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Dispute::class)
                                    ->toString
                                        ->isIdenticalTo('"dspt_kkyLpFvqM8JYQrBJlhN9bxSY"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->never

                        ->generator($gen)
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Dispute::class)
                                    ->toString
                                        ->isIdenticalTo('"dspt_VIk2SufjagxqT6ZtoRbqUkUm"') // From json sample
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Dispute::class)
                                    ->toString
                                        ->isIdenticalTo('"dspt_kkyLpFvqM8JYQrBJlhN9bxSY"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->once
        ;
    }

    public function testGetDetails()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getDetails())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setDetails($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "details".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->object($this->newTestedInstance(uniqid())->getDetails())
                    ->isInstanceOf(Stancer\Payout\Details::class)

                ->object($this->testedInstance->details)
                    ->isInstanceOf(Stancer\Payout\Details::class)

                // disputes
                ->object($this->testedInstance->details->disputes)
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->integer($this->testedInstance->details->disputes->amount)
                    ->isIdenticalTo(500)

                ->string($this->testedInstance->details->disputes->currency)
                    ->isIdenticalTo('eur')

                // payments
                ->object($this->testedInstance->details->payments)
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->integer($this->testedInstance->details->payments->amount)
                    ->isIdenticalTo(15000)

                ->string($this->testedInstance->details->payments->currency)
                    ->isIdenticalTo('eur')

                // refunds
                ->object($this->testedInstance->details->refunds)
                    ->isInstanceOf(Stancer\Payout\Details\Inner::class)

                ->integer($this->testedInstance->details->refunds->amount)
                    ->isIdenticalTo(5000)

                ->string($this->testedInstance->details->refunds->currency)
                    ->isIdenticalTo('eur')
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->string($this->newTestedInstance->getEndpoint())
                ->isIdenticalTo('payouts')
        ;
    }

    public function testGetFees()
    {
        $this
            ->if($value = rand(0, 100))
            ->then
                ->variable($this->newTestedInstance->getFees())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setFees($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "fees".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->integer($this->newTestedInstance(uniqid())->getFees())
                    ->isIdenticalTo(100)

                ->integer($this->testedInstance->fees)
                    ->isIdenticalTo(100)
        ;
    }

    public function testGetStatementDescription()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getStatementDescription())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setStatementDescription($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "statementDescription".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->string($this->newTestedInstance(uniqid())->getStatementDescription())
                    ->isIdenticalTo('Stancer Payout Statement')

                ->string($this->testedInstance->statementDescription)
                    ->isIdenticalTo('Stancer Payout Statement')
        ;
    }

    public function testGetStatus()
    {
        $this
            ->if($value = uniqid())
            ->then
                ->variable($this->newTestedInstance->getStatus())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setStatus($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "status".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))
            ->then
                ->enum($this->newTestedInstance(uniqid())->getStatus())
                    ->isIdenticalTo(Stancer\Payout\Status::PAID)

                ->enum($this->testedInstance->status)
                    ->isIdenticalTo(Stancer\Payout\Status::PAID)
        ;
    }

    public function testList()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client))
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'list'))
            ->and($options = $this->mockRequestOptions($config))

            ->assert('Make request')
                ->if($limit = rand(1, 100))
                ->and($start = rand(0, PHP_INT_MAX))
                ->and($created = time() - rand(10, 1000000))

                ->and($terms = [
                    'created' => $created,
                    'limit' => $limit,
                    'start' => $start,
                ])
                ->and($location = $this->newTestedInstance->getUri() . '?' . http_build_query($terms))

                ->then
                    ->generator(testedClass::list($terms))
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"pout_GexV3lpllrBkyRny15qfsMC0"') // From json sample

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location, $options)->once
        ;
    }

    public function testPaymentsDetails()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))

            ->and($config = $this->mockConfig($client))
            ->and($options = $this->mockRequestOptions($config))

            ->if($this->newTestedInstance('pout_GexV3lpllrBkyRny15qfsMC0')) // from fixture
            ->and($baseLocation = $this->testedInstance->getUri() . '/payments')
            ->then
                ->assert('Invalid limit')
                    ->exception(function () {
                        $this->testedInstance->details->payments(['limit' => 0]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                    ->exception(function () {
                        $this->testedInstance->details->payments(['limit' => 101]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                    ->exception(function () {
                        $this->testedInstance->details->payments(['limit' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                ->assert('Invalid start')
                    ->exception(function () {
                        $this->testedInstance->details->payments(['start' => -1]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                        ->message
                            ->isIdenticalTo('Start must be a positive integer.')

                    ->exception(function () {
                        $this->testedInstance->details->payments(['start' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                        ->message
                            ->isIdenticalTo('Start must be a positive integer.')

                ->assert('Invalid created filter')
                    ->exception(function () {
                        $this->testedInstance->details->payments(['created' => time() + 100]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be in the past.')

                    ->exception(function () {
                        $date = new DateTime();
                        $date->add(new DateInterval('P1D'));

                        $this->testedInstance->details->payments(['created' => $date]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be in the past.')

                    ->exception(function () {
                        $this->testedInstance->details->payments(['created' => 0]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

                    ->exception(function () {
                        $this->testedInstance->details->payments(['created' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

                ->assert('Make request')
                    ->if($limit = rand(1, 100))
                    ->and($start = rand(0, PHP_INT_MAX))
                    ->and($created = time() - rand(10, 1000000))

                    ->and($terms1 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($terms2 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start + 2, // Based on json sample
                    ])
                    ->and($location2 = $baseLocation . '?' . http_build_query($terms2))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('payment', 'list'))
                    ->then
                        ->generator($gen = $this->testedInstance->details->payments($terms1))
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
                    ->and($this->calling($client)->request = $this->mockResponse(json_encode($body)))

                    ->if($limit = rand(1, 100))
                    ->and($terms = [
                        'limit' => $limit,
                    ])
                    ->and($query = http_build_query(['limit' => $limit, 'start' => 0]))
                    ->and($location = $baseLocation . '?' . $query)
                    ->then
                        ->generator($this->testedInstance->details->payments($terms))
                            ->yields
                                ->variable
                                    ->isNull

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location, $options)
                                    ->once

                ->assert('Without params')
                    ->if($terms1 = [
                        'limit' => 100,
                        'start' => 0,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('payment', 'list'))
                    ->then
                        ->generator($this->testedInstance->details->payments())
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Payment::class)
                                    ->toString
                                        ->isIdenticalTo('"paym_JnU7xyTGJvxRWZuxvj78qz7e"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once

                ->assert('With long name')
                    ->if($limit = rand(1, 100))
                    ->and($start = rand(0, PHP_INT_MAX))
                    ->and($created = time() - rand(10, 1000000))

                    ->and($terms1 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($terms2 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start + 2, // Based on json sample
                    ])
                    ->and($location2 = $baseLocation . '?' . http_build_query($terms2))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('payment', 'list'))
                    ->then
                        ->generator($gen = $this->testedInstance->details->listPayments($terms1))
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Payment::class)
                                    ->toString
                                        ->isIdenticalTo('"paym_JnU7xyTGJvxRWZuxvj78qz7e"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->never

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
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->once
        ;
    }

    public function testRefundsDetails()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($this->calling($client)->request = $this->mockJsonResponse('payout', 'read'))

            ->and($config = $this->mockConfig($client))
            ->and($options = $this->mockRequestOptions($config))

            ->if($this->newTestedInstance('pout_GexV3lpllrBkyRny15qfsMC0')) // from fixture
            ->and($baseLocation = $this->testedInstance->getUri() . '/refunds')
            ->then
                ->assert('Invalid limit')
                    ->exception(function () {
                        $this->testedInstance->details->refunds(['limit' => 0]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                    ->exception(function () {
                        $this->testedInstance->details->refunds(['limit' => 101]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                    ->exception(function () {
                        $this->testedInstance->details->refunds(['limit' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchLimitException::class)
                        ->message
                            ->isIdenticalTo('Limit must be between 1 and 100.')

                ->assert('Invalid start')
                    ->exception(function () {
                        $this->testedInstance->details->refunds(['start' => -1]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                        ->message
                            ->isIdenticalTo('Start must be a positive integer.')

                    ->exception(function () {
                        $this->testedInstance->details->refunds(['start' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchStartException::class)
                        ->message
                            ->isIdenticalTo('Start must be a positive integer.')

                ->assert('Invalid created filter')
                    ->exception(function () {
                        $this->testedInstance->details->refunds(['created' => time() + 100]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be in the past.')

                    ->exception(function () {
                        $date = new DateTime();
                        $date->add(new DateInterval('P1D'));

                        $this->testedInstance->details->refunds(['created' => $date]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be in the past.')

                    ->exception(function () {
                        $this->testedInstance->details->refunds(['created' => 0]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

                    ->exception(function () {
                        $this->testedInstance->details->refunds(['created' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('Created must be a positive integer, a DateTime object or a DatePeriod object.')

                ->assert('Make request')
                    ->if($limit = rand(1, 100))
                    ->and($start = rand(0, PHP_INT_MAX))
                    ->and($created = time() - rand(10, 1000000))

                    ->and($terms1 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($terms2 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start + 2, // Based on json sample
                    ])
                    ->and($location2 = $baseLocation . '?' . http_build_query($terms2))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('refund', 'list'))
                    ->then
                        ->generator($gen = $this->testedInstance->details->refunds($terms1))
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Refund::class)
                                    ->toString
                                        ->isIdenticalTo('"refd_J0r3rHzPaaXU2lBLkDFxpqpw"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->never

                        ->generator($gen)
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Refund::class)
                                    ->toString
                                        ->isIdenticalTo('"refd_ae1pJ2wdty6mGiRTTQ1FWw0V"') // From json sample

                        ->generator($gen)
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Refund::class)
                                    ->toString
                                        ->isIdenticalTo('"refd_J0r3rHzPaaXU2lBLkDFxpqpw"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->once

                ->assert('Empty response')
                    ->given($body = [
                        'refunds' => [],
                        'range' => [
                            'has_more' => false,
                            'limit' => 10,
                        ],
                    ])
                    ->and($this->calling($client)->request = $this->mockResponse(json_encode($body)))

                    ->if($limit = rand(1, 100))
                    ->and($terms = [
                        'limit' => $limit,
                    ])
                    ->and($query = http_build_query(['limit' => $limit, 'start' => 0]))
                    ->and($location = $baseLocation . '?' . $query)
                    ->then
                        ->generator($this->testedInstance->details->refunds($terms))
                            ->yields
                                ->variable
                                    ->isNull

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location, $options)
                                    ->once

                ->assert('Without params')
                    ->if($terms1 = [
                        'limit' => 100,
                        'start' => 0,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('refund', 'list'))
                    ->then
                        ->generator($this->testedInstance->details->refunds())
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Refund::class)
                                    ->toString
                                        ->isIdenticalTo('"refd_J0r3rHzPaaXU2lBLkDFxpqpw"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once

                ->assert('With long name')
                    ->if($limit = rand(1, 100))
                    ->and($start = rand(0, PHP_INT_MAX))
                    ->and($created = time() - rand(10, 1000000))

                    ->and($terms1 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start,
                    ])
                    ->and($location1 = $baseLocation . '?' . http_build_query($terms1))

                    ->and($terms2 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start + 2, // Based on json sample
                    ])
                    ->and($location2 = $baseLocation . '?' . http_build_query($terms2))

                    ->and($this->calling($client)->request = $this->mockJsonResponse('refund', 'list'))
                    ->then
                        ->generator($gen = $this->testedInstance->details->listRefunds($terms1))
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Refund::class)
                                    ->toString
                                        ->isIdenticalTo('"refd_J0r3rHzPaaXU2lBLkDFxpqpw"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->never

                        ->generator($gen)
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Refund::class)
                                    ->toString
                                        ->isIdenticalTo('"refd_ae1pJ2wdty6mGiRTTQ1FWw0V"') // From json sample

                        ->generator($gen)
                            ->yields
                                ->object
                                    ->isInstanceOf(Stancer\Refund::class)
                                    ->toString
                                        ->isIdenticalTo('"refd_J0r3rHzPaaXU2lBLkDFxpqpw"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)->once
                                ->withArguments('GET', $location2, $options)->once
        ;
    }
}
