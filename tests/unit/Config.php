<?php

namespace Stancer\tests\unit;

use DateTime;
use DateTimeZone;
use GuzzleHttp;
use Psr;
use Stancer;
use Stancer\Config as testedClass;
use mock;

class Config extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Banks;
    use Stancer\Tests\Provider\Cards;
    use Stancer\Tests\Provider\Dates;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->hasConstant('LIVE_MODE')
                ->hasConstant('TEST_MODE')
                ->hasConstant('VERSION')
        ;
    }

    public function testAddAppData()
    {
        $this
            ->given($name1 = uniqid())
            ->and($version = uniqid())
            ->and($name2 = uniqid())

            ->and($agent0 = vsprintf('libstancer-php/%s (%s %s %s; php %s)', [
                testedClass::VERSION,
                PHP_OS,
                php_uname('m'),
                php_uname('r'),
                PHP_VERSION,
            ]))
            ->and($agent1 = vsprintf('libstancer-php/%s %s/%s (%s %s %s; php %s)', [
                testedClass::VERSION,
                $name1,
                $version,
                PHP_OS,
                php_uname('m'),
                php_uname('r'),
                PHP_VERSION,
            ]))
            ->and($agent2 = vsprintf('libstancer-php/%s %s/%s %s (%s %s %s; php %s)', [
                testedClass::VERSION,
                $name1,
                $version,
                $name2,
                PHP_OS,
                php_uname('m'),
                php_uname('r'),
                PHP_VERSION,
            ]))

            ->then
                ->assert('camelCase method')
                    ->if($this->newTestedInstance([]))
                        ->object($this->testedInstance->addAppData($name1, $version))
                            ->isTestedInstance

                        ->string($this->testedInstance->getDefaultUserAgent())
                            ->contains($agent1)

                        ->object($this->testedInstance->addAppData($name2))
                            ->isTestedInstance

                        ->string($this->testedInstance->getDefaultUserAgent())
                            ->contains($agent2)

                        ->object($this->testedInstance->resetAppData())
                            ->isTestedInstance

                        ->string($this->testedInstance->getDefaultUserAgent())
                            ->contains($agent0)

                ->assert('snake_case method')
                    ->if($this->newTestedInstance([]))
                        ->object($this->testedInstance->add_app_data($name1, $version))
                            ->isTestedInstance

                        ->string($this->testedInstance->get_default_user_agent())
                            ->contains($agent1)

                        ->object($this->testedInstance->add_app_data($name2))
                            ->isTestedInstance

                        ->string($this->testedInstance->get_default_user_agent())
                            ->contains($agent2)

                        ->object($this->testedInstance->reset_app_data())
                            ->isTestedInstance

                        ->string($this->testedInstance->get_default_user_agent())
                            ->contains($agent0)
        ;
    }

    public function testGetBasicAuthHeader()
    {
        $this
            ->given($pprod = 'pprod_' . bin2hex(random_bytes(12)))
            ->and($ptest = 'ptest_' . bin2hex(random_bytes(12)))
            ->and($sprod = 'sprod_' . bin2hex(random_bytes(12)))
            ->and($stest = 'stest_' . bin2hex(random_bytes(12)))

            ->and($this->newTestedInstance([$pprod, $ptest, $sprod, $stest]))
            ->then
                ->string($this->testedInstance->getBasicAuthHeader())
                    ->isIdenticalTo('Basic ' . base64_encode($stest . ':'))

                ->string($this->testedInstance->get_basic_auth_header())
                    ->isIdenticalTo('Basic ' . base64_encode($stest . ':'))

                ->string($this->testedInstance->basicAuthHeader)
                    ->isIdenticalTo('Basic ' . base64_encode($stest . ':'))

                ->string($this->testedInstance->basic_auth_header)
                    ->isIdenticalTo('Basic ' . base64_encode($stest . ':'))
        ;
    }

    public function testGetCalls()
    {
        $this
            ->given($this->function->setDefaultNamespace('Stancer\\Http'))

            ->assert('With debug mode activated / Default client / Without exception')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(true))

                ->if($body = uniqid())
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = 200)
                ->and($this->function->curl_errno = 0)

                ->if($client = new mock\Stancer\Http\Client)
                ->and($this->testedInstance->setHttpClient($client))

                ->if($number = $this->cardNumberDataProvider(true))
                ->and($obfuscated = str_pad('', strlen($number) - 4, 'x') . substr($number, -4))
                ->and($card = new Stancer\Card(['number' => $number]))
                ->and($payment = new Stancer\Payment(['card' => $card]))
                ->and($req = new Stancer\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                    ->array($this->testedInstance->get_calls())
                        ->isEmpty

                    ->array($this->testedInstance->calls)
                        ->isEmpty

                ->if($req->post($payment))
                ->then
                    ->assert('camelCase method')
                        ->array($calls = $this->testedInstance->getCalls())
                            ->hasSize(1)

                        ->object($calls[0])
                            ->isInstanceOf(Stancer\Core\Request\Call::class)

                        ->variable($calls[0]->getException())
                            ->isNull

                        ->object($calls[0]->getRequest())
                            ->isInstanceOf(Stancer\Http\Request::class)

                        ->object($calls[0]->getRequest()->getBody())
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->getRequest()->getBody())
                            ->notContains($number)
                            ->contains($obfuscated)

                        ->object($calls[0]->getResponse())
                            ->isInstanceOf(Stancer\Http\Response::class)

                    ->assert('snake_case method')
                        ->array($calls = $this->testedInstance->get_calls())
                            ->hasSize(1)

                        ->object($calls[0])
                            ->isInstanceOf(Stancer\Core\Request\Call::class)

                        ->variable($calls[0]->get_exception())
                            ->isNull

                        ->object($calls[0]->get_request())
                            ->isInstanceOf(Stancer\Http\Request::class)

                        ->object($calls[0]->get_request()->get_body())
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->get_request()->get_body())
                            ->notContains($number)
                            ->contains($obfuscated)

                        ->object($calls[0]->get_response())
                            ->isInstanceOf(Stancer\Http\Response::class)

                    ->assert('property')
                        ->array($calls = $this->testedInstance->calls)
                            ->hasSize(1)

                        ->object($calls[0])
                            ->isInstanceOf(Stancer\Core\Request\Call::class)

                        ->variable($calls[0]->exception)
                            ->isNull

                        ->object($calls[0]->request)
                            ->isInstanceOf(Stancer\Http\Request::class)

                        ->object($calls[0]->request->body)
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->request->body)
                            ->notContains($number)
                            ->contains($obfuscated)

                        ->object($calls[0]->response)
                            ->isInstanceOf(Stancer\Http\Response::class)

            ->assert('With debug mode activated / Default client / With exception')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(true))

                ->if($body = uniqid())
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = 401)
                ->and($this->function->curl_errno = rand(100, 200))

                ->if($client = new mock\Stancer\Http\Client)
                ->and($this->testedInstance->setHttpClient($client))

                ->if($iban = $this->ibanDataProvider(true))
                ->and($sepa = new Stancer\Sepa(['iban' => $iban]))
                ->and($obfuscated = str_pad($sepa->getLast4(), strlen($sepa->getIban()), 'x', STR_PAD_LEFT))
                ->and($payment = new Stancer\Payment(['sepa' => $sepa]))
                ->and($req = new Stancer\Core\Request)
                ->then
                    ->assert('default empty')
                        ->array($this->testedInstance->getCalls())
                            ->isEmpty

                        ->array($this->testedInstance->get_calls())
                            ->isEmpty

                        ->array($this->testedInstance->calls)
                            ->isEmpty

                    ->exception(function () use ($req, $payment) {
                        $req->post($payment);
                    })
                        ->isInstanceOf(Stancer\Exceptions\NotAuthorizedException::class)

                    ->assert('camelCase method')
                        ->array($calls = $this->testedInstance->getCalls())
                            ->hasSize(1)

                        ->object($calls[0])
                            ->isInstanceOf(Stancer\Core\Request\Call::class)

                        ->object($calls[0]->getException())
                            ->isInstanceOf(Stancer\Exceptions\NotAuthorizedException::class)
                            ->isIdenticalTo($this->exception)

                        ->object($calls[0]->getRequest())
                            ->isInstanceOf(Stancer\Http\Request::class)

                        ->object($calls[0]->getRequest()->getBody())
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->getRequest()->getBody())
                            ->notContains($iban)
                            ->contains($obfuscated)

                        ->object($calls[0]->getResponse())
                            ->isInstanceOf(Stancer\Http\Response::class)

                        ->object($calls[0]->getResponse()->getBody())
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->getResponse()->getBody())
                            ->isIdenticalTo($body)

                    ->assert('snake_case method')
                        ->array($calls = $this->testedInstance->get_calls())
                            ->hasSize(1)

                        ->object($calls[0])
                            ->isInstanceOf(Stancer\Core\Request\Call::class)

                        ->object($calls[0]->get_exception())
                            ->isInstanceOf(Stancer\Exceptions\NotAuthorizedException::class)
                            ->isIdenticalTo($this->exception)

                        ->object($calls[0]->get_request())
                            ->isInstanceOf(Stancer\Http\Request::class)

                        ->object($calls[0]->get_request()->get_body())
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->get_request()->get_body())
                            ->notContains($iban)
                            ->contains($obfuscated)

                        ->object($calls[0]->get_response())
                            ->isInstanceOf(Stancer\Http\Response::class)

                        ->object($calls[0]->get_response()->get_body())
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->get_response()->get_body())
                            ->isIdenticalTo($body)

                    ->assert('property')
                        ->array($calls = $this->testedInstance->calls)
                            ->hasSize(1)

                        ->object($calls[0])
                            ->isInstanceOf(Stancer\Core\Request\Call::class)

                        ->object($calls[0]->exception)
                            ->isInstanceOf(Stancer\Exceptions\NotAuthorizedException::class)
                            ->isIdenticalTo($this->exception)

                        ->object($calls[0]->request)
                            ->isInstanceOf(Stancer\Http\Request::class)

                        ->object($calls[0]->request->body)
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->request->body)
                            ->notContains($iban)
                            ->contains($obfuscated)

                        ->object($calls[0]->response)
                            ->isInstanceOf(Stancer\Http\Response::class)

                        ->object($calls[0]->response->body)
                            ->isInstanceOf(Stancer\Http\Stream::class)

                        ->castToString($calls[0]->response->body)
                            ->isIdenticalTo($body)

            ->assert('With debug mode activated / Guzzle / Without exception')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(true))

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($body = uniqid())
                ->and($response = new mock\GuzzleHttp\Psr7\Response(200, [], $body))
                ->and($this->calling($client)->request = $response)

                ->and($this->testedInstance->setHttpClient($client))

                ->if($iban = $this->ibanDataProvider(true))
                ->and($sepa = new Stancer\Sepa(['iban' => $iban]))
                ->and($obfuscated = str_pad($sepa->getLast4(), strlen($sepa->getIban()), 'x', STR_PAD_LEFT))
                ->and($payment = new Stancer\Payment(['sepa' => $sepa]))
                ->and($req = new Stancer\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                ->if($req->patch($payment))
                ->then
                    ->array($calls = $this->testedInstance->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(Stancer\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)

                    ->object($calls[0]->getRequest()->getBody())
                        ->isInstanceOf(GuzzleHttp\Psr7\Stream::class)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->notContains($iban)
                        ->contains($obfuscated)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)

                    ->object($calls[0]->getResponse()->getBody())
                        ->isInstanceOf(GuzzleHttp\Psr7\Stream::class)

                    ->castToString($calls[0]->getResponse()->getBody())
                        ->isIdenticalTo($body)

            ->assert('With debug mode activated / Guzzle / With exception')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(true))

                ->if($body = uniqid())
                ->and($response = new GuzzleHttp\Psr7\Response(401, [], $body))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))

                ->and($this->testedInstance->setHttpClient($client))

                ->if($number = $this->cardNumberDataProvider(true))
                ->and($obfuscated = str_pad(substr($number, -4), strlen($number), 'x', STR_PAD_LEFT))
                ->and($card = new Stancer\Card(['number' => $number]))
                ->and($payment = new Stancer\Payment(['card' => $card]))
                ->and($req = new Stancer\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                    ->exception(function () use ($req, $payment) {
                        $req->patch($payment);
                    })
                        ->isInstanceOf(Stancer\Exceptions\NotAuthorizedException::class)

                    ->array($calls = $this->testedInstance->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(Stancer\Core\Request\Call::class)

                    ->object($calls[0]->getException())
                        ->isInstanceOf(Stancer\Exceptions\NotAuthorizedException::class)
                        ->isIdenticalTo($this->exception)

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)

                    ->object($calls[0]->getRequest()->getBody())
                        ->isInstanceOf(GuzzleHttp\Psr7\Stream::class)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->notContains($iban)
                        ->contains($obfuscated)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)

                    ->object($calls[0]->getResponse()->getBody())
                        ->isInstanceOf(GuzzleHttp\Psr7\Stream::class)

                    ->castToString($calls[0]->getResponse()->getBody())
                        ->isIdenticalTo($body)

            ->assert('Without debug mode activated')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(false))

                ->if($body = uniqid())
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = 200)
                ->and($this->function->curl_errno = 0)

                ->if($client = new mock\Stancer\Http\Client)
                ->and($this->testedInstance->setHttpClient($client))

                ->if($object = new Stancer\Stub\Core\StubObject)
                ->and($req = new Stancer\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                ->if($req->get($object))
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty
        ;
    }

    public function testGetDebug_SetDebug()
    {
        $this
            ->assert('camelCase method')
                ->assert('TRUE in test mode')
                    ->if($this->newTestedInstance([]))
                    ->then
                        ->boolean($this->testedInstance->getDebug())
                            ->isTrue

                        ->object($this->testedInstance->setDebug(false))
                            ->isTestedInstance

                        ->boolean($this->testedInstance->getDebug())
                            ->isFalse

                ->assert('FALSE in live mode')
                    ->if($this->newTestedInstance([]))
                    ->and($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->getDebug())
                            ->isFalse

                        ->object($this->testedInstance->setDebug(true))
                            ->isTestedInstance

                        ->boolean($this->testedInstance->getDebug())
                            ->isTrue

                ->assert('Can be forced')
                    ->if($this->newTestedInstance([]))
                    ->then
                        ->object($this->testedInstance->setDebug(true))
                            ->isTestedInstance

                        ->boolean($this->testedInstance->getDebug())
                            ->isTrue

                    ->if($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->getDebug())
                            ->isTrue

            ->assert('snake_case method')
                ->assert('TRUE in test mode')
                    ->if($this->newTestedInstance([]))
                    ->then
                        ->boolean($this->testedInstance->get_debug())
                            ->isTrue

                        ->object($this->testedInstance->set_debug(false))
                            ->isTestedInstance

                        ->boolean($this->testedInstance->get_debug())
                            ->isFalse

                ->assert('FALSE in live mode')
                    ->if($this->newTestedInstance([]))
                    ->and($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->get_debug())
                            ->isFalse

                        ->object($this->testedInstance->set_debug(true))
                            ->isTestedInstance

                        ->boolean($this->testedInstance->get_debug())
                            ->isTrue

                ->assert('Can be forced')
                    ->if($this->newTestedInstance([]))
                    ->then
                        ->object($this->testedInstance->set_debug(true))
                            ->isTestedInstance

                        ->boolean($this->testedInstance->get_debug())
                            ->isTrue

                    ->if($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->get_debug())
                            ->isTrue

            ->assert('property')
                ->assert('TRUE in test mode')
                    ->if($this->newTestedInstance([]))
                    ->then
                        ->boolean($this->testedInstance->debug)
                            ->isTrue

                        ->boolean($this->testedInstance->debug = false)
                            ->isFalse

                        ->boolean($this->testedInstance->debug)
                            ->isFalse

                ->assert('FALSE in live mode')
                    ->if($this->newTestedInstance([]))
                    ->and($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->debug)
                            ->isFalse

                        ->boolean($this->testedInstance->debug = true)
                            ->isTrue

                        ->boolean($this->testedInstance->debug)
                            ->isTrue

                ->assert('Can be forced')
                    ->if($this->newTestedInstance([]))
                    ->then
                        ->boolean($this->testedInstance->debug = true)
                            ->isTrue

                        ->boolean($this->testedInstance->debug)
                            ->isTrue

                    ->if($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->debug)
                            ->isTrue
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testGetDefaultTimeZone_SetDefaultTimeZone($zone)
    {
        $this
            ->given($badName = uniqid())
            ->assert('Default value')
                ->if($this->newTestedInstance([]))
                ->then
                    ->variable($this->testedInstance->getDefaultTimeZone())
                        ->isNull

                    ->variable($this->testedInstance->get_default_timezone())
                        ->isNull

                    ->variable($this->testedInstance->get_default_time_zone())
                        ->isNull

                    ->variable($this->testedInstance->defaultTimeZone)
                        ->isNull

                    ->variable($this->testedInstance->default_timezone)
                        ->isNull

                    ->variable($this->testedInstance->default_time_zone)
                        ->isNull

            ->assert('Exception if not a DateTimeZone instance')
                ->exception(function () {
                    $this->newTestedInstance([])->setDefaultTimeZone(new DateTime);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone.')

                ->exception(function () {
                    $this->newTestedInstance([])->set_default_timezone(new DateTime);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone.')

                ->exception(function () {
                    $this->newTestedInstance([])->set_default_time_zone(new DateTime);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone.')

                ->exception(function () {
                    $this->newTestedInstance([])->defaultTimeZone = new DateTime;
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone.')

                ->exception(function () {
                    $this->newTestedInstance([])->default_timezone = new DateTime;
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone.')

                ->exception(function () {
                    $this->newTestedInstance([])->default_time_zone = new DateTime;
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone.')

            ->assert('Exception if not a valid time zone name')
                ->exception(function () use ($badName) {
                    $this->newTestedInstance([])->setDefaultTimeZone($badName);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone "' . $badName . '".')

                ->exception(function () use ($badName) {
                    $this->newTestedInstance([])->set_default_timezone($badName);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone "' . $badName . '".')

                ->exception(function () use ($badName) {
                    $this->newTestedInstance([])->set_default_time_zone($badName);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone "' . $badName . '".')

                ->exception(function () use ($badName) {
                    $this->newTestedInstance([])->defaultTimeZone = $badName;
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone "' . $badName . '".')

                ->exception(function () use ($badName) {
                    $this->newTestedInstance([])->default_timezone = $badName;
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone "' . $badName . '".')

                ->exception(function () use ($badName) {
                    $this->newTestedInstance([])->default_time_zone = $badName;
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid time zone "' . $badName . '".')

            ->assert('Update with a string / camelCase method')
                ->if($this->newTestedInstance([]))
                ->then
                    ->object($this->testedInstance->setDefaultTimeZone($zone))
                        ->isTestedInstance

                    ->object($this->testedInstance->getDefaultTimeZone())
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->getDefaultTimeZone()->getName())
                        ->isIdenticalTo($zone)

                    ->object($this->testedInstance->resetDefaultTimeZone())
                        ->isTestedInstance

                    ->variable($this->testedInstance->getDefaultTimeZone())
                        ->isNull

            ->assert('Update with a string / snake_case method')
                ->if($this->newTestedInstance([]))
                ->then
                    ->object($this->testedInstance->set_default_timezone($zone))
                        ->isTestedInstance

                    ->object($this->testedInstance->get_default_timezone())
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->get_default_timezone()->getName())
                        ->isIdenticalTo($zone)

                    ->object($this->testedInstance->reset_default_timezone())
                        ->isTestedInstance

                    ->variable($this->testedInstance->get_default_timezone())
                        ->isNull

            ->assert('Update with a string / snake_case method alternative')
                ->if($this->newTestedInstance([]))
                ->then
                    ->object($this->testedInstance->set_default_time_zone($zone))
                        ->isTestedInstance

                    ->object($this->testedInstance->get_default_time_zone())
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->get_default_time_zone()->getName())
                        ->isIdenticalTo($zone)

                    ->object($this->testedInstance->reset_default_time_zone())
                        ->isTestedInstance

                    ->variable($this->testedInstance->get_default_time_zone())
                        ->isNull

            ->assert('Update with a string / camelCase property')
                ->if($this->newTestedInstance([])->defaultTimeZone = $zone)
                ->then
                    ->object($this->testedInstance->defaultTimeZone)
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->defaultTimeZone->getName())
                        ->isIdenticalTo($zone)

                    ->object($this->testedInstance->resetDefaultTimeZone())
                        ->isTestedInstance

                    ->variable($this->testedInstance->defaultTimeZone)
                        ->isNull

            ->assert('Update with an instance / snake_case property')
                ->given($tz = new DateTimeZone($zone))

                ->if($this->newTestedInstance([])->default_timezone = $tz)
                ->then
                    ->object($this->testedInstance->default_timezone)
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->default_timezone->getName())
                        ->isIdenticalTo($zone)

                    ->object($this->testedInstance->reset_default_timezone())
                        ->isTestedInstance

                    ->variable($this->testedInstance->default_timezone)
                        ->isNull

            ->assert('Update with an instance / snake_case property alternative')
                ->given($tz = new DateTimeZone($zone))

                ->if($this->newTestedInstance([])->default_time_zone = $tz)
                ->then
                    ->object($this->testedInstance->default_time_zone)
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->default_time_zone->getName())
                        ->isIdenticalTo($zone)

                    ->object($this->testedInstance->reset_default_time_zone())
                        ->isTestedInstance

                    ->variable($this->testedInstance->default_time_zone)
                        ->isNull
        ;
    }

    public function testGetDefaultUserAgent()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($guzzle = new mock\GuzzleHttp\ClientInterface)
            ->and($client = new Stancer\Http\Client)
            ->and($agent = vsprintf(' libstancer-php/%s (%s %s %s; php %s)', [
                testedClass::VERSION,
                PHP_OS,
                php_uname('m'),
                php_uname('r'),
                PHP_VERSION,
            ]))
            ->and($guzzlePrefix = 'GuzzleHttp')
            ->and($curlPrefix = 'curl/' . curl_version()['version'])

            ->if($this->testedInstance->setHttpClient($client))
            ->then
                ->string($this->testedInstance->getDefaultUserAgent())
                    ->isIdenticalTo($curlPrefix . $agent)

                ->string($this->testedInstance->get_default_user_agent())
                    ->isIdenticalTo($curlPrefix . $agent)

                ->string($this->testedInstance->defaultUserAgent)
                    ->isIdenticalTo($curlPrefix . $agent)

                ->string($this->testedInstance->default_user_agent)
                    ->isIdenticalTo($curlPrefix . $agent)

            ->if($this->testedInstance->setHttpClient($guzzle))
            ->then
                ->string($this->testedInstance->getDefaultUserAgent())
                    ->isIdenticalTo($guzzlePrefix . $agent)

                ->string($this->testedInstance->get_default_user_agent())
                    ->isIdenticalTo($guzzlePrefix . $agent)

                ->string($this->testedInstance->defaultUserAgent)
                    ->isIdenticalTo($guzzlePrefix . $agent)

                ->string($this->testedInstance->default_user_agent)
                    ->isIdenticalTo($guzzlePrefix . $agent)
        ;
    }

    public function testGetGlobal_SetGlobal()
    {
        $this
            ->exception(function () {
                testedClass::getGlobal();
            })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->contains('You need to provide API credential')

            ->exception(function () {
                testedClass::get_global();
            })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->contains('You need to provide API credential')

            ->object(testedClass::setGlobal($this->newTestedInstance([])))
                ->isTestedInstance

            ->object(testedClass::getGlobal())
                ->isTestedInstance

            ->object(testedClass::set_global($this->newTestedInstance([])))
                ->isTestedInstance

            ->object(testedClass::get_global())
                ->isTestedInstance
        ;
    }

    public function testGetHost_SetHost()
    {
        $this
            ->given($defaultHost = 'api.stancer.com')
            ->and($randomHost = uniqid())
            ->then
                ->assert('camelCase method')
                    ->string($this->newTestedInstance([])->getHost())
                        ->isIdenticalTo($defaultHost)

                    ->object($this->testedInstance->setHost($randomHost))
                        ->isTestedInstance

                    ->string($this->testedInstance->getHost())
                        ->isIdenticalTo($randomHost)

                ->assert('snake_case method')
                    ->string($this->newTestedInstance([])->get_host())
                        ->isIdenticalTo($defaultHost)

                    ->object($this->testedInstance->set_host($randomHost))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_host())
                        ->isIdenticalTo($randomHost)

                ->assert('property')
                    ->string($this->newTestedInstance([])->host)
                        ->isIdenticalTo($defaultHost)

                    ->string($this->testedInstance->host = $randomHost)

                    ->string($this->testedInstance->host)
                        ->isIdenticalTo($randomHost)
        ;
    }

    public function testGetHttpClient_SetHttpClient()
    {
        $this
            ->given($guzzle = new mock\GuzzleHttp\ClientInterface)
            ->and($client = new Stancer\Http\Client)
            ->then
                ->assert('camelCase method')
                    ->object($this->newTestedInstance([])->getHttpClient())
                        ->isInstanceOf(Stancer\Http\Client::class) // Automagicaly created instance

                    ->object($this->testedInstance->setHttpClient($guzzle))
                        ->isTestedInstance

                    ->object($this->testedInstance->getHttpClient())
                        ->isIdenticalTo($guzzle)

                    ->object($this->testedInstance->setHttpClient($client))
                        ->isTestedInstance

                    ->object($this->testedInstance->getHttpClient())
                        ->isIdenticalTo($client)

                ->assert('snake_case method')
                    ->object($this->newTestedInstance([])->get_http_client())
                        ->isInstanceOf(Stancer\Http\Client::class) // Automagicaly created instance

                    ->object($this->testedInstance->set_http_client($guzzle))
                        ->isTestedInstance

                    ->object($this->testedInstance->get_http_client())
                        ->isIdenticalTo($guzzle)

                    ->object($this->testedInstance->set_http_client($client))
                        ->isTestedInstance

                    ->object($this->testedInstance->get_http_client())
                        ->isIdenticalTo($client)

                ->assert('camelCase property')
                    ->object($this->newTestedInstance([])->httpClient)
                        ->isInstanceOf(Stancer\Http\Client::class) // Automagicaly created instance

                    ->object($this->testedInstance->httpClient = $guzzle)
                        ->isInstanceOf(GuzzleHttp\ClientInterface::class)

                    ->object($this->testedInstance->httpClient)
                        ->isIdenticalTo($guzzle)

                    ->object($this->testedInstance->httpClient = $client)
                        ->isInstanceOf(Stancer\Http\Client::class)

                    ->object($this->testedInstance->httpClient)
                        ->isIdenticalTo($client)

                ->assert('snake_case property')
                    ->object($this->newTestedInstance([])->http_client)
                        ->isInstanceOf(Stancer\Http\Client::class) // Automagicaly created instance

                    ->object($this->testedInstance->http_client = $guzzle)
                        ->isInstanceOf(GuzzleHttp\ClientInterface::class)

                    ->object($this->testedInstance->http_client)
                        ->isIdenticalTo($guzzle)

                    ->object($this->testedInstance->http_client = $client)
                        ->isInstanceOf(Stancer\Http\Client::class)

                    ->object($this->testedInstance->http_client)
                        ->isIdenticalTo($client)
        ;
    }

    public function testGetLogger_SetLogger()
    {
        $this
            ->if($mock = new mock\Psr\Log\LoggerInterface)
            ->then
                ->assert('camelCase method')
                    ->object($this->newTestedInstance([])->getLogger())
                        ->isInstanceOf(Stancer\Core\Logger::class)

                    ->object($this->testedInstance->setLogger($mock))
                        ->isTestedInstance

                    ->object($this->testedInstance->getLogger())
                        ->isIdenticalTo($mock)

                ->assert('snake_case method')
                    ->object($this->newTestedInstance([])->get_logger())
                        ->isInstanceOf(Stancer\Core\Logger::class)

                    ->object($this->testedInstance->set_logger($mock))
                        ->isTestedInstance

                    ->object($this->testedInstance->get_logger())
                        ->isIdenticalTo($mock)

                ->assert('property')
                    ->object($this->newTestedInstance([])->logger)
                        ->isInstanceOf(Stancer\Core\Logger::class)

                    ->object($this->testedInstance->logger = $mock)
                        ->isInstanceOf(Psr\Log\LoggerInterface::class)

                    ->object($this->testedInstance->logger)
                        ->isIdenticalTo($mock)
        ;
    }

    public function testGetMode_SetMode()
    {
        $this
            ->if($invalidMode = uniqid())
            ->then
                ->assert('camelCase method')
                    ->string($this->newTestedInstance([])->getMode())
                        ->isIdenticalTo(testedClass::TEST_MODE)

                    ->object($this->testedInstance->setMode(testedClass::LIVE_MODE))
                        ->isTestedInstance

                    ->string($this->testedInstance->getMode())
                        ->isIdenticalTo(testedClass::LIVE_MODE)

                    ->object($this->testedInstance->setMode(testedClass::TEST_MODE))
                        ->isTestedInstance

                    ->string($this->testedInstance->getMode())
                        ->isIdenticalTo(testedClass::TEST_MODE)

                    ->exception(function () use ($invalidMode) {
                        $this->testedInstance->setMode($invalidMode);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($invalidMode)
                            ->contains('LIVE_MODE')
                            ->contains('TEST_MODE')

                ->assert('snake_case method')
                    ->string($this->newTestedInstance([])->get_mode())
                        ->isIdenticalTo(testedClass::TEST_MODE)

                    ->object($this->testedInstance->set_mode(testedClass::LIVE_MODE))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_mode())
                        ->isIdenticalTo(testedClass::LIVE_MODE)

                    ->object($this->testedInstance->set_mode(testedClass::TEST_MODE))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_mode())
                        ->isIdenticalTo(testedClass::TEST_MODE)

                    ->exception(function () use ($invalidMode) {
                        $this->testedInstance->set_mode($invalidMode);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($invalidMode)
                            ->contains('LIVE_MODE')
                            ->contains('TEST_MODE')

                ->assert('property')
                    ->string($this->newTestedInstance([])->mode)
                        ->isIdenticalTo(testedClass::TEST_MODE)

                    ->variable($this->testedInstance->mode = testedClass::LIVE_MODE)

                    ->string($this->testedInstance->mode)
                        ->isIdenticalTo(testedClass::LIVE_MODE)

                    ->variable($this->testedInstance->mode = testedClass::TEST_MODE)

                    ->string($this->testedInstance->mode)
                        ->isIdenticalTo(testedClass::TEST_MODE)

                    ->exception(function () use ($invalidMode) {
                        $this->testedInstance->mode = $invalidMode;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($invalidMode)
                            ->contains('LIVE_MODE')
                            ->contains('TEST_MODE')
        ;
    }

    public function testGetPort_SetPort()
    {
        $this
            ->if($defaultPort = 443)
            ->and($randomPort = rand(0, PHP_INT_MAX))
            ->then
                ->assert('camelCase method')
                    ->integer($this->newTestedInstance([])->getPort())
                        ->isIdenticalTo($defaultPort)

                    ->object($this->testedInstance->setPort($randomPort))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getPort())
                        ->isIdenticalTo($randomPort)

                ->assert('snake_case method')
                    ->integer($this->newTestedInstance([])->get_port())
                        ->isIdenticalTo($defaultPort)

                    ->object($this->testedInstance->set_port($randomPort))
                        ->isTestedInstance

                    ->integer($this->testedInstance->get_port())
                        ->isIdenticalTo($randomPort)

                ->assert('property')
                    ->integer($this->newTestedInstance([])->port)
                        ->isIdenticalTo($defaultPort)

                    ->integer($this->testedInstance->port = $randomPort)

                    ->integer($this->testedInstance->port)
                        ->isIdenticalTo($randomPort)
        ;
    }

    public function testGetPublicKey()
    {
        $this
            ->given($pprod = 'pprod_' . bin2hex(random_bytes(12)))
            ->and($ptest = 'ptest_' . bin2hex(random_bytes(12)))
            ->and($sprod = 'sprod_' . bin2hex(random_bytes(12)))
            ->and($stest = 'stest_' . bin2hex(random_bytes(12)))

            ->if($this->newTestedInstance([$pprod, $ptest, $sprod, $stest]))
            ->then
                ->string($this->testedInstance->getPublicKey())
                    ->isIdenticalTo($ptest)

                ->string($this->testedInstance->setMode(testedClass::LIVE_MODE)->getPublicKey())
                    ->isIdenticalTo($pprod)

                ->string($this->testedInstance->setMode(testedClass::TEST_MODE)->getPublicKey())
                    ->isIdenticalTo($ptest)

            ->if($this->newTestedInstance([$ptest]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::LIVE_MODE)->getPublicKey();
                })
                    ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid public API key for production.')

            ->if($this->newTestedInstance([$pprod]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::TEST_MODE)->getPublicKey();
                })
                    ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid public API key for development.')
        ;
    }

    public function testGetSecretKey()
    {
        $this
            ->given($pprod = 'pprod_' . bin2hex(random_bytes(12)))
            ->and($ptest = 'ptest_' . bin2hex(random_bytes(12)))
            ->and($sprod = 'sprod_' . bin2hex(random_bytes(12)))
            ->and($stest = 'stest_' . bin2hex(random_bytes(12)))

            ->if($this->newTestedInstance([$pprod, $ptest, $sprod, $stest]))
            ->then
                ->string($this->testedInstance->getSecretKey())
                    ->isIdenticalTo($stest)

                ->string($this->testedInstance->setMode(testedClass::LIVE_MODE)->getSecretKey())
                    ->isIdenticalTo($sprod)

                ->string($this->testedInstance->setMode(testedClass::TEST_MODE)->getSecretKey())
                    ->isIdenticalTo($stest)

            ->if($this->newTestedInstance([$stest]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::LIVE_MODE)->getSecretKey();
                })
                    ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid secret API key for production.')

            ->if($this->newTestedInstance([$sprod]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::TEST_MODE)->getSecretKey();
                })
                    ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid secret API key for development.')
        ;
    }

    public function testGetTimeout_SetTimeout()
    {
        $this
            ->if($defaultTimeout = 0)
            ->and($randomTimeout = rand(1, 60 * 3))
            ->and($tooMuchTimeout = 60 * 3 + rand(1, 9999))
            ->then
                ->assert('camelCase method')
                    ->integer($this->newTestedInstance([])->getTimeout())
                        ->isIdenticalTo($defaultTimeout)

                    ->object($this->testedInstance->setTimeout($randomTimeout))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getTimeout())
                        ->isIdenticalTo($randomTimeout)

                    ->exception(function () use ($tooMuchTimeout) {
                        $this->testedInstance->setTimeout($tooMuchTimeout);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('Timeout (' . $tooMuchTimeout . 's) is too high, the maximum allowed is 180 seconds.')

                ->assert('snake_case method')
                    ->integer($this->newTestedInstance([])->get_timeout())
                        ->isIdenticalTo($defaultTimeout)

                    ->object($this->testedInstance->set_timeout($randomTimeout))
                        ->isTestedInstance

                    ->integer($this->testedInstance->get_timeout())
                        ->isIdenticalTo($randomTimeout)

                    ->exception(function () use ($tooMuchTimeout) {
                        $this->testedInstance->set_timeout($tooMuchTimeout);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('Timeout (' . $tooMuchTimeout . 's) is too high, the maximum allowed is 180 seconds.')

                ->assert('property')
                    ->integer($this->newTestedInstance([])->timeout)
                        ->isIdenticalTo($defaultTimeout)

                    ->integer($this->testedInstance->timeout = $randomTimeout)

                    ->integer($this->testedInstance->timeout)
                        ->isIdenticalTo($randomTimeout)

                    ->exception(function () use ($tooMuchTimeout) {
                        $this->testedInstance->timeout = $tooMuchTimeout;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->isIdenticalTo('Timeout (' . $tooMuchTimeout . 's) is too high, the maximum allowed is 180 seconds.')
        ;
    }

    public function testGetUri()
    {
        $this
            ->assert('Default values')
                ->string($this->newTestedInstance([])->getUri())
                    ->isIdenticalTo('https://api.stancer.com/v1/')

                ->string($this->newTestedInstance([])->get_uri())
                    ->isIdenticalTo('https://api.stancer.com/v1/')

                ->string($this->newTestedInstance([])->uri)
                    ->isIdenticalTo('https://api.stancer.com/v1/')

            ->assert('Random values / camelCase method')
                ->if($host = uniqid())
                ->and($port = rand(0, PHP_INT_MAX))
                ->and($version = rand(0, PHP_INT_MAX))
                ->and($protocol = 'https')

                ->given($this->newTestedInstance([])->setHost($host))
                ->and($this->testedInstance->setPort($port))
                ->and($this->testedInstance->setVersion($version))

                ->then
                    ->string($this->testedInstance->getUri())
                        ->isIdenticalTo(sprintf('%s://%s:%d/v%d/', $protocol, $host, $port, $version))

            ->assert('Random values / snake_case method')
                ->if($host = uniqid())
                ->and($port = rand(0, PHP_INT_MAX))
                ->and($version = rand(0, PHP_INT_MAX))
                ->and($protocol = 'https')

                ->given($this->newTestedInstance([])->set_host($host))
                ->and($this->testedInstance->set_port($port))
                ->and($this->testedInstance->set_version($version))

                ->then
                    ->string($this->testedInstance->get_uri())
                        ->isIdenticalTo(sprintf('%s://%s:%d/v%d/', $protocol, $host, $port, $version))

            ->assert('Random values / property')
                ->if($host = uniqid())
                ->and($port = rand(0, PHP_INT_MAX))
                ->and($version = rand(0, PHP_INT_MAX))
                ->and($protocol = 'https')

                ->given($this->newTestedInstance([])->host = $host)
                ->and($this->testedInstance->port = $port)
                ->and($this->testedInstance->version = $version)

                ->then
                    ->string($this->testedInstance->uri)
                        ->isIdenticalTo(sprintf('%s://%s:%d/v%d/', $protocol, $host, $port, $version))
        ;
    }

    public function testGetVersion_SetVersion()
    {
        $this
            ->if($defaultVersion = 1)
            ->and($randomVersion = rand(0, PHP_INT_MAX))
            ->then
                ->assert('camelCase method')
                    ->integer($this->newTestedInstance([])->getVersion())
                        ->isIdenticalTo($defaultVersion)

                    ->object($this->testedInstance->setVersion($randomVersion))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getVersion())
                        ->isIdenticalTo($randomVersion)

                ->assert('snake_case method')
                    ->integer($this->newTestedInstance([])->get_version())
                        ->isIdenticalTo($defaultVersion)

                    ->object($this->testedInstance->set_version($randomVersion))
                        ->isTestedInstance

                    ->integer($this->testedInstance->get_version())
                        ->isIdenticalTo($randomVersion)

                ->assert('property')
                    ->integer($this->newTestedInstance([])->version)
                        ->isIdenticalTo($defaultVersion)

                    ->integer($this->testedInstance->version = $randomVersion)

                    ->integer($this->testedInstance->version)
                        ->isIdenticalTo($randomVersion)
        ;
    }

    public function testInit()
    {
        $this
            ->given($pprod = 'pprod_' . bin2hex(random_bytes(12)))
            ->and($ptest = 'ptest_' . bin2hex(random_bytes(12)))
            ->and($sprod = 'sprod_' . bin2hex(random_bytes(12)))
            ->and($stest = 'stest_' . bin2hex(random_bytes(12)))

            ->then
                ->object($obj = testedClass::init([$pprod, $ptest, $sprod, $stest]))
                    ->isInstanceOf(testedClass::class)
                ->string($obj->getPublicKey())
                    ->isIdenticalTo($ptest)
                ->string($obj->getSecretKey())
                    ->isIdenticalTo($stest)
                ->object(testedClass::getGlobal())
                    ->isIdenticalTo($obj)
        ;
    }

    public function testModes()
    {
        $this
            ->assert('camelCase method')
                ->assert('Defaults values')
                    ->boolean($this->newTestedInstance([])->isLiveMode())->isFalse
                    ->boolean($this->testedInstance->isNotLiveMode())->isTrue
                    ->boolean($this->testedInstance->isTestMode())->isTrue
                    ->boolean($this->testedInstance->isNotTestMode())->isFalse

                ->assert('Force test mode')
                    ->if($this->testedInstance->setMode(testedClass::TEST_MODE))
                    ->then
                        ->boolean($this->testedInstance->isLiveMode())->isFalse
                        ->boolean($this->testedInstance->isNotLiveMode())->isTrue
                        ->boolean($this->testedInstance->isTestMode())->isTrue
                        ->boolean($this->testedInstance->isNotTestMode())->isFalse

                ->assert('Force live mode')
                    ->if($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->isLiveMode())->isTrue
                        ->boolean($this->testedInstance->isNotLiveMode())->isFalse
                        ->boolean($this->testedInstance->isTestMode())->isFalse
                        ->boolean($this->testedInstance->isNotTestMode())->isTrue

            ->assert('snake_case method')
                ->assert('Defaults values')
                    ->boolean($this->newTestedInstance([])->is_live_mode())->isFalse
                    ->boolean($this->testedInstance->is_not_live_mode())->isTrue
                    ->boolean($this->testedInstance->is_test_mode())->isTrue
                    ->boolean($this->testedInstance->is_not_test_mode())->isFalse

                ->assert('Force test mode')
                    ->if($this->testedInstance->setMode(testedClass::TEST_MODE))
                    ->then
                        ->boolean($this->testedInstance->is_live_mode())->isFalse
                        ->boolean($this->testedInstance->is_not_live_mode())->isTrue
                        ->boolean($this->testedInstance->is_test_mode())->isTrue
                        ->boolean($this->testedInstance->is_not_test_mode())->isFalse

                ->assert('Force live mode')
                    ->if($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->is_live_mode())->isTrue
                        ->boolean($this->testedInstance->is_not_live_mode())->isFalse
                        ->boolean($this->testedInstance->is_test_mode())->isFalse
                        ->boolean($this->testedInstance->is_not_test_mode())->isTrue

            ->assert('camelCase property')
                ->assert('Defaults values')
                    ->boolean($this->newTestedInstance([])->isLiveMode)->isFalse
                    ->boolean($this->testedInstance->isNotLiveMode)->isTrue
                    ->boolean($this->testedInstance->isTestMode)->isTrue
                    ->boolean($this->testedInstance->isNotTestMode)->isFalse

                ->assert('Force test mode')
                    ->if($this->testedInstance->setMode(testedClass::TEST_MODE))
                    ->then
                        ->boolean($this->testedInstance->isLiveMode)->isFalse
                        ->boolean($this->testedInstance->isNotLiveMode)->isTrue
                        ->boolean($this->testedInstance->isTestMode)->isTrue
                        ->boolean($this->testedInstance->isNotTestMode)->isFalse

                ->assert('Force live mode')
                    ->if($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->isLiveMode)->isTrue
                        ->boolean($this->testedInstance->isNotLiveMode)->isFalse
                        ->boolean($this->testedInstance->isTestMode)->isFalse
                        ->boolean($this->testedInstance->isNotTestMode)->isTrue

            ->assert('snake_case property')
                ->assert('Defaults values')
                    ->boolean($this->newTestedInstance([])->is_live_mode)->isFalse
                    ->boolean($this->testedInstance->is_not_live_mode)->isTrue
                    ->boolean($this->testedInstance->is_test_mode)->isTrue
                    ->boolean($this->testedInstance->is_not_test_mode)->isFalse

                ->assert('Force test mode')
                    ->if($this->testedInstance->setMode(testedClass::TEST_MODE))
                    ->then
                        ->boolean($this->testedInstance->is_live_mode)->isFalse
                        ->boolean($this->testedInstance->is_not_live_mode)->isTrue
                        ->boolean($this->testedInstance->is_test_mode)->isTrue
                        ->boolean($this->testedInstance->is_not_test_mode)->isFalse

                ->assert('Force live mode')
                    ->if($this->testedInstance->setMode(testedClass::LIVE_MODE))
                    ->then
                        ->boolean($this->testedInstance->is_live_mode)->isTrue
                        ->boolean($this->testedInstance->is_not_live_mode)->isFalse
                        ->boolean($this->testedInstance->is_test_mode)->isFalse
                        ->boolean($this->testedInstance->is_not_test_mode)->isTrue
        ;
    }

    public function testSetKeys()
    {
        $this
            ->given($ptest1 = 'ptest_' . bin2hex(random_bytes(12)))
            ->and($ptest2 = 'ptest_' . bin2hex(random_bytes(12)))
            ->and($stest = 'stest_' . bin2hex(random_bytes(12)))

            ->if($this->newTestedInstance([]))
            ->then
                ->assert('No keys on default')
                    ->exception(function () {
                        $this->testedInstance->getPublicKey();
                    })
                        ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                        ->message
                            ->isIdenticalTo('You did not provide valid public API key for development.')

                    ->exception(function () {
                        $this->testedInstance->getSecretKey();
                    })
                        ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                        ->message
                            ->isIdenticalTo('You did not provide valid secret API key for development.')

                ->assert('Allow string value')
                    ->object($this->testedInstance->setKeys($ptest1))
                        ->isTestedInstance

                    ->string($this->testedInstance->getPublicKey())
                        ->isIdenticalTo($ptest1)

                    ->exception(function () {
                        $this->testedInstance->getSecretKey();
                    })
                        ->isInstanceOf(Stancer\Exceptions\MissingApiKeyException::class)
                        ->message
                            ->isIdenticalTo('You did not provide valid secret API key for development.')

                ->assert('Allow an array of keys')
                    ->object($this->testedInstance->setKeys([$ptest2, $stest]))
                        ->isTestedInstance

                    ->string($this->testedInstance->getPublicKey())
                        ->isIdenticalTo($ptest2)

                    ->string($this->testedInstance->getSecretKey())
                        ->isIdenticalTo($stest)

                ->assert('Ignore unknowned keys')
                    ->object($this->testedInstance->setKeys(uniqid()))
                        ->isTestedInstance

                    ->string($this->testedInstance->getPublicKey())
                        ->isIdenticalTo($ptest2)

                    ->string($this->testedInstance->getSecretKey())
                        ->isIdenticalTo($stest)
        ;
    }
}
