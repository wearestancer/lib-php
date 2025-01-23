<?php

namespace Stancer\tests\unit\Stub\Traits;

use DateInterval;
use DatePeriod;
use DateTime;
use Stancer;
use Stancer\Stub\Traits\SearchTrait as testedClass;
use mock;

class SearchTrait extends Stancer\Tests\atoum
{
    public function testIssueGitLab2()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client))

            ->and($this->calling($client)->request = $this->mockJsonResponses([
                ['stub', 'list-1'],
                ['stub', 'list-2'],
                ['stub', 'list-3'],
                ['stub', 'list-4'],
            ]))
            ->and($options = $this->mockRequestOptions($config))

            ->if($location = $this->newTestedInstance->getUri())
            ->and($location1 = $location . '?' . http_build_query(['limit' => 2, 'start' => 0]))
            ->and($location2 = $location . '?' . http_build_query(['limit' => 2, 'start' => 2]))
            ->and($location3 = $location . '?' . http_build_query(['limit' => 2, 'start' => 4]))
            ->and($location4 = $location . '?' . http_build_query(['limit' => 2, 'start' => 6]))
            ->and($location5 = $location . '?' . http_build_query(['limit' => 2, 'start' => 8]))

            ->then
                ->generator($gen = testedClass::list(['limit' => 2]))
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_ItejQGbS2nH8mRJwramVgqC5"') // From json sample

                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', $location1, $options)->once
                        ->withArguments('GET', $location2, $options)->never
                        ->withArguments('GET', $location3, $options)->never
                        ->withArguments('GET', $location4, $options)->never
                        ->withArguments('GET', $location5, $options)->never

                ->generator($gen)
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_9PP2Ps1u2nNks5zJmIAHai24"') // From json sample
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_lvR3PkFan8dTWTTzkbbR0H74"') // From json sample

                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', $location1, $options)->once
                        ->withArguments('GET', $location2, $options)->once
                        ->withArguments('GET', $location3, $options)->never
                        ->withArguments('GET', $location4, $options)->never
                        ->withArguments('GET', $location5, $options)->never

                ->generator($gen)
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_N1DDSO8zL71mt6byGIEv4x31"') // From json sample
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_IV92PD8IaiVui4wJORmEiU09"') // From json sample

                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', $location1, $options)->once
                        ->withArguments('GET', $location2, $options)->once
                        ->withArguments('GET', $location3, $options)->once
                        ->withArguments('GET', $location4, $options)->never
                        ->withArguments('GET', $location5, $options)->never

                ->generator($gen)
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_gWaePaC4pOkBbz7DUHm7LsS3"') // From json sample
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_DgTidk8n9UrKdimRfg34qJ87"') // From json sample

                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', $location1, $options)->once
                        ->withArguments('GET', $location2, $options)->once
                        ->withArguments('GET', $location3, $options)->once
                        ->withArguments('GET', $location4, $options)->once
                        ->withArguments('GET', $location5, $options)->never

                ->generator($gen)
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"stub_66t9TXtC3uk03UfKhmalDDO5"') // From json sample
                    ->yields
                        ->variable
                            ->isNull

                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', $location1, $options)->once
                        ->withArguments('GET', $location2, $options)->once
                        ->withArguments('GET', $location3, $options)->once
                        ->withArguments('GET', $location4, $options)->once
                        ->withArguments('GET', $location5, $options)->never
        ;
    }

    public function testList()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($config = $this->mockConfig($client))
            ->and($options = $this->mockRequestOptions($config))

            ->and($response = $this->mockJsonResponse('stub', 'list'))
            ->and($this->calling($client)->request = $response)

            ->if($this->newTestedInstance)
            ->then
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

                ->assert('Invalid created until filter')
                    ->exception(function () {
                        testedClass::list(['created_until' => time() + 100]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationUntilFilterException::class)
                        ->message
                            ->isIdenticalTo('Created until must be in the past.')

                    ->exception(function () {
                        $date = new DateTime();
                        $date->add(new DateInterval('P1D'));

                        testedClass::list(['created_until' => $date]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationUntilFilterException::class)
                        ->message
                            ->isIdenticalTo('Created until must be in the past.')

                    ->exception(function () {
                        $created = new DateTime();
                        $created->sub(new DateInterval('P1D'));

                        $until = new DateTime();
                        $until->sub(new DateInterval('P2D'));

                        testedClass::list(['created' => $created, 'created_until' => $until]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationUntilFilterException::class)
                        ->message
                            ->isIdenticalTo('Created until must be after created date.')

                    ->exception(function () {
                        testedClass::list(['created_until' => 0]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationUntilFilterException::class)
                        ->message
                            ->isIdenticalTo('Created until must be a positive integer or a DateTime object.')

                    ->exception(function () {
                        testedClass::list(['created_until' => uniqid()]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationUntilFilterException::class)
                        ->message
                            ->isIdenticalTo('Created until must be a positive integer or a DateTime object.')

                ->assert('Make request')
                    ->if($limit = rand(1, 100))
                    ->and($start = rand(0, PHP_INT_MAX))
                    ->and($created = time() - rand(10, 1000000))

                    ->and($location = $this->testedInstance->getUri())
                    ->and($terms1 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start,
                    ])
                    ->and($location1 = $location . '?' . http_build_query($terms1))

                    ->and($terms2 = [
                        'created' => $created,
                        'limit' => $limit,
                        'start' => $start + 2, // Based on json sample
                    ])
                    ->and($location2 = $location . '?' . http_build_query($terms2))
                    ->then
                        ->generator($gen = testedClass::list($terms1))
                            ->yields
                                ->object
                                    ->isInstanceOf(testedClass::class)
                                    ->toString
                                        ->isIdenticalTo('"stub_JnU7xyTGJvxRWZuxvj78qz7e"') // From json sample

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
                                        ->isIdenticalTo('"stub_p5tjCrXHy93xtVtVqvEJoC1c"') // From json sample
                            ->yields
                                ->object
                                    ->isInstanceOf(testedClass::class)
                                    ->toString
                                        ->isIdenticalTo('"stub_JnU7xyTGJvxRWZuxvj78qz7e"') // From json sample

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location1, $options)
                                    ->once // Called the first time
                                ->withArguments('GET', $location2, $options)
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

                ->assert('Empty response')
                    ->given($body = [
                        'searchtraits' => [],
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
                    ->and($location = $this->testedInstance->getUri() . '?' . $query)
                    ->then
                        ->generator($gen = testedClass::list($terms))
                            ->yields
                                ->variable
                                    ->isNull

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location, $options)
                                    ->once

                ->assert('Results not present')
                    ->given($body = [
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
                    ->and($location = $this->testedInstance->getUri() . '?' . $query)
                    ->then
                        ->generator($gen = testedClass::list($terms))
                            ->yields
                                ->variable
                                    ->isNull

                        ->mock($client)
                            ->call('request')
                                ->withArguments('GET', $location, $options)
                                    ->once

                ->assert('Empty response (real case)')
                    ->given($this->calling($client)->request->throw = new Stancer\Exceptions\NotFoundException())

                    ->if($limit = rand(1, 100))
                    ->and($terms = [
                        'limit' => $limit,
                    ])
                    ->and($query = http_build_query(['limit' => $limit, 'start' => 0]))
                    ->and($location = $this->testedInstance->getUri() . '?' . $query)
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

    public function testList_with_date_period()
    {
        $created = time() - rand(10, 1000000);
        $until = 0;
        $tmp = $created;
        $items = [];

        for ($index = 0; $index < 5; $index++) {
            $until = $tmp;
            $items[] = [
                'created' => $tmp += rand(100, 1000),
                'id' => 'stub_' . $this->getRandomString(24),
            ];
        }

        $body = [
            'live_mode' => true,
            'searchtraits' => $items,
            'range' => [
                'end' => 5,
                'has_more' => false,
                'limit' => 10,
                'start' => 0,
            ],
        ];

        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($response = new mock\Stancer\Http\Response(200, json_encode($body)))
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

            ->and($this->newTestedInstance)
            ->and($location = $this->testedInstance->getUri())

            ->assert('Period with an end without until')
                ->if($start = new DateTime('@' . $created))
                ->and($interval = new DateInterval('P1D'))
                ->and($end = new DateTime('@' . $until))
                ->and($period = new DatePeriod($start, $interval, $end))

                ->and($location1 = $location . '?' . http_build_query(['created' => $created, 'start' => 0]))
                ->then
                    ->generator(testedClass::list(['created' => $period]))
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[0]['id'] . '"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[1]['id'] . '"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[2]['id'] . '"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[3]['id'] . '"')
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location1, $options)
                                ->once

            ->assert('Period with an end with until')
                ->if($start = new DateTime('@' . $created))
                ->and($interval = new DateInterval('P1D'))
                ->and($end = new DateTime())
                ->and($period = new DatePeriod($start, $interval, $end))

                ->and($location1 = $location . '?' . http_build_query(['created' => $created, 'start' => 0]))
                ->then
                    ->generator(testedClass::list(['created' => $period, 'created_until' => $until]))
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[0]['id'] . '"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[1]['id'] . '"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[2]['id'] . '"')
                        ->yields
                            ->object
                                ->isInstanceOf(testedClass::class)
                                ->toString
                                    ->isIdenticalTo('"' . $items[3]['id'] . '"')
                        ->yields
                            ->variable
                                ->isNull

                    ->mock($client)
                        ->call('request')
                            ->withArguments('GET', $location1, $options)
                                ->once

            ->assert('Period without an end')
                ->if($start = new DateTime('@' . $created))
                ->and($interval = new DateInterval('P1D'))
                ->and($recurrences = rand(1, 100))
                ->and($period = new DatePeriod($start, $interval, $recurrences))

                ->then
                    ->exception(function () use ($period) {
                        testedClass::list(['created' => $period]);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidSearchCreationFilterException::class)
                        ->message
                            ->isIdenticalTo('DatePeriod must have an end to be used.')
        ;
    }

    public function testList_with_until()
    {
        $created = time() - rand(10, 1000000);
        $until = 0;
        $tmp = $created;
        $items = [];

        for ($index = 0; $index < 5; $index++) {
            $until = $tmp;
            $items[] = [
                'created' => $tmp += rand(100, 1000),
                'id' => 'stub_' . $this->getRandomString(24),
            ];
        }

        $body = [
            'live_mode' => true,
            'searchtraits' => $items,
            'range' => [
                'end' => 5,
                'has_more' => false,
                'limit' => 10,
                'start' => 0,
            ],
        ];

        $this
            ->given($client = new mock\Stancer\Http\Client())
            ->and($response = new mock\Stancer\Http\Response(200, json_encode($body)))
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

            ->if($limit = rand(1, 100))
            ->and($start = rand(0, PHP_INT_MAX))

            ->and($location = $this->newTestedInstance->getUri())
            ->and($terms1 = [
                'created' => $created,
                'limit' => $limit,
                'start' => $start,
            ])
            ->and($location1 = $location . '?' . http_build_query($terms1))
            ->and($terms1['created_until'] = $until)

            ->then
                ->generator(testedClass::list($terms1))
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"' . $items[0]['id'] . '"')
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"' . $items[1]['id'] . '"')
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"' . $items[2]['id'] . '"')
                    ->yields
                        ->object
                            ->isInstanceOf(testedClass::class)
                            ->toString
                                ->isIdenticalTo('"' . $items[3]['id'] . '"')
                    ->yields
                        ->variable
                            ->isNull

                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', $location1, $options)
                            ->once
        ;
    }
}
