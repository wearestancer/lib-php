<?php

namespace ild78\tests\unit;

use DateTime;
use DateTimeZone;
use GuzzleHttp;
use ild78;
use ild78\Config as testedClass;
use mock;
use Psr;

class Config extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Dates;

    public function testClass()
    {
        $this
            ->given($package = json_decode(file_get_contents(__DIR__ . '/../../composer.json'), true))
            ->then
                ->currentlyTestedClass
                    ->hasConstant('LIVE_MODE')
                    ->hasConstant('TEST_MODE')
                    ->hasConstant('VERSION')

                    ->constant('VERSION')
                        ->isEqualTo($package['version'])
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
        ;
    }

    public function testGetCalls()
    {
        $this
            ->given($this->function->setDefaultNamespace('ild78\\Http'))

            ->assert('With debug mode activated / Default client / Without exception')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(true))

                ->if($body = uniqid())
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = 200)
                ->and($this->function->curl_errno = 0)

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->testedInstance->setHttpClient($client))

                ->if($object = new ild78\Stub\Core\StubObject)
                ->and($req = new ild78\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                ->if($req->get($object))
                ->then
                    ->array($calls = $this->testedInstance->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(ild78\Http\Request::class)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(ild78\Http\Response::class)

            ->assert('With debug mode activated / Default client / With exception')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(true))

                ->if($body = uniqid())
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = 401)
                ->and($this->function->curl_errno = rand(100, 200))

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->testedInstance->setHttpClient($client))

                ->if($object = new ild78\Stub\Core\StubObject)
                ->and($req = new ild78\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                    ->exception(function () use ($req, $object) {
                        $req->get($object);
                    })
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)

                    ->array($calls = $this->testedInstance->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->object($calls[0]->getException())
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
                        ->isIdenticalTo($this->exception)

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(ild78\Http\Request::class)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(ild78\Http\Response::class)

                    ->string($calls[0]->getResponse()->getBody())
                        ->isIdenticalTo($body)

            ->assert('With debug mode activated / Guzzle / Without exception')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(true))

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($response = new mock\GuzzleHttp\Psr7\Response)
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($this->testedInstance->setHttpClient($client))

                ->if($object = new ild78\Stub\Core\StubObject)
                ->and($req = new ild78\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                ->if($req->get($object))
                ->then
                    ->array($calls = $this->testedInstance->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)

                    ->string($calls[0]->getResponse()->getBody())
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

                ->if($object = new ild78\Stub\Core\StubObject)
                ->and($req = new ild78\Core\Request)
                ->then
                    ->array($this->testedInstance->getCalls())
                        ->isEmpty

                    ->exception(function () use ($req, $object) {
                        $req->get($object);
                    })
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)

                    ->array($calls = $this->testedInstance->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->object($calls[0]->getException())
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
                        ->isIdenticalTo($this->exception)

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)

                    ->castToString($calls[0]->getResponse()->getBody())
                        ->isIdenticalTo($body)

            ->assert('Without debug mode activated')
                ->given(testedClass::setGlobal($this->newTestedInstance(['stest_' . bin2hex(random_bytes(12))])))
                ->and($this->testedInstance->setDebug(false))

                ->if($body = uniqid())
                ->and($this->function->curl_exec = $body)
                ->and($this->function->curl_getinfo = 200)
                ->and($this->function->curl_errno = 0)

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->testedInstance->setHttpClient($client))

                ->if($object = new ild78\Stub\Core\StubObject)
                ->and($req = new ild78\Core\Request)
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
        ;
    }

    /**
     * @dataProvider timeZoneProvider
     */
    public function testGetDefaultTimeZone_SetDefaultTimeZone($zone)
    {
        $this
            ->assert('Default value')
                ->if($this->newTestedInstance([]))
                ->then
                    ->variable($this->testedInstance->getDefaultTimeZone())
                        ->isNull

            ->assert('Exception if not a string or a DateTimeZone instance')
                ->exception(function () {
                    $this->newTestedInstance([])->setDefaultTimeZone(new DateTime);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Invalid "$tz" argument.')

            ->assert('Update with a string')
                ->if($this->newTestedInstance([]))
                ->then
                    ->object($this->testedInstance->setDefaultTimeZone($zone))
                        ->isTestedInstance

                    ->object($this->testedInstance->getDefaultTimeZone())
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->getDefaultTimeZone()->getName())
                        ->isIdenticalTo($zone)

            ->assert('Update with an instance')
                ->if($this->newTestedInstance([]))
                ->and($tz = new DateTimeZone($zone))
                ->then
                    ->object($this->testedInstance->setDefaultTimeZone($tz))
                        ->isTestedInstance

                    ->object($this->testedInstance->getDefaultTimeZone())
                        ->isInstanceOf(DateTimeZone::class)

                    ->string($this->testedInstance->getDefaultTimeZone()->getName())
                        ->isIdenticalTo($zone)
        ;
    }

    public function testGetDefaultUserAgent()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($guzzle = new mock\GuzzleHttp\ClientInterface)
            ->and($client = new ild78\Http\Client)
            ->and($agent = vsprintf(' libiliad-php/%s (%s %s %s; php %s)', [
                testedClass::VERSION,
                PHP_OS,
                php_uname('m'),
                php_uname('r'),
                PHP_VERSION,
            ]))
            ->and($guzzlePrefix = 'GuzzleHttp/' . GuzzleHttp\Client::VERSION)
            ->and($curlPrefix = 'curl/' . curl_version()['version'])

            ->if($this->testedInstance->setHttpClient($client))
            ->then
                ->string($this->testedInstance->getDefaultUserAgent())
                    ->isIdenticalTo($curlPrefix . $agent)

            ->if($this->testedInstance->setHttpClient($guzzle))
            ->then
                ->string($this->testedInstance->getDefaultUserAgent())
                    ->isIdenticalTo($guzzlePrefix . $agent)
        ;
    }

    public function testGetGlobal_SetGlobal()
    {
        $this
            ->exception(function () {
                testedClass::getGlobal();
            })
                ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                ->message
                    ->contains('You need to provide API credential')

            ->if($this->newTestedInstance([]))
            ->then
                ->object(testedClass::setGlobal($this->testedInstance))
                    ->isTestedInstance

                ->object(testedClass::getGlobal())
                    ->isTestedInstance
        ;
    }

    public function testGetHost_SetHost()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($defaultHost = 'api.iliad78.net')
            ->and($randomHost = uniqid())
            ->then
                ->string($this->testedInstance->getHost())
                    ->isIdenticalTo($defaultHost)
                ->object($this->testedInstance->setHost($randomHost))
                    ->isTestedInstance
                ->string($this->testedInstance->getHost())
                    ->isIdenticalTo($randomHost)
        ;
    }

    public function testGetHttpClient_SetHttpClient()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($guzzle = new mock\GuzzleHttp\ClientInterface)
            ->and($client = new ild78\Http\Client)
            ->then
                ->object($this->testedInstance->getHttpClient())
                    ->isInstanceOf('ild78\Http\Client') // Automagicaly created instance

                ->object($this->testedInstance->setHttpClient($guzzle))
                    ->isTestedInstance

                ->object($this->testedInstance->getHttpClient())
                    ->isIdenticalTo($guzzle)

                ->object($this->testedInstance->setHttpClient($client))
                    ->isTestedInstance

                ->object($this->testedInstance->getHttpClient())
                    ->isIdenticalTo($client)
        ;
    }

    public function testGetLogger_SetLogger()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($mock = new mock\Psr\Log\LoggerInterface)
            ->then
                ->object($this->testedInstance->getLogger())
                    ->isInstanceOf(ild78\Core\Logger::class)

                ->object($this->testedInstance->setLogger($mock))
                    ->isTestedInstance

                ->object($this->testedInstance->getLogger())
                    ->isIdenticalTo($mock)
        ;
    }

    public function testGetMode_SetMode()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($invalidMode = uniqid())
            ->then
                ->string($this->testedInstance->getMode())
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
                    ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->contains($invalidMode)
                        ->contains('LIVE_MODE')
                        ->contains('TEST_MODE')
        ;
    }

    public function testGetPort_SetPort()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($defaultPort = 443)
            ->and($randomPort = rand(0, PHP_INT_MAX))
            ->then
                ->integer($this->testedInstance->getPort())
                    ->isIdenticalTo($defaultPort)
                ->object($this->testedInstance->setPort($randomPort))
                    ->isTestedInstance
                ->integer($this->testedInstance->getPort())
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
                    ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid public API key for production.')

            ->if($this->newTestedInstance([$pprod]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::TEST_MODE)->getPublicKey();
                })
                    ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
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
                    ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid secret API key for production.')

            ->if($this->newTestedInstance([$sprod]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::TEST_MODE)->getSecretKey();
                })
                    ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid secret API key for development.')
        ;
    }

    public function testGetTimeout_SetTimeout()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($defaultTimeout = 5)
            ->and($randomTimeout = rand(1, 60 * 3))
            ->and($tooMuchTimeout = 60 * 3 + rand(1, 9999))
            ->then
                ->integer($this->testedInstance->getTimeout())
                    ->isIdenticalTo($defaultTimeout)

                ->object($this->testedInstance->setTimeout($randomTimeout))
                    ->isTestedInstance

                ->integer($this->testedInstance->getTimeout())
                    ->isIdenticalTo($randomTimeout)

                ->exception(function () use ($tooMuchTimeout) {
                    $this->testedInstance->setTimeout($tooMuchTimeout);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('Timeout (' . $tooMuchTimeout . 's) is too high, the maximum allowed is 180 seconds (3 minutes, and it\'s already too much).')
        ;
    }

    public function testGetUri()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->assert('Default values')
                ->then
                    ->string($this->testedInstance->getUri())
                        ->isIdenticalTo('https://api.iliad78.net/v1/')
            ->assert('Random values')
                ->if($host = uniqid())
                ->and($port = rand(0, PHP_INT_MAX))
                ->and($version = rand(0, PHP_INT_MAX))
                ->and($protocol = 'https')

                ->given($this->testedInstance->setHost($host))
                ->and($this->testedInstance->setPort($port))
                ->and($this->testedInstance->setVersion($version))

                ->then
                    ->string($this->testedInstance->getUri())
                        ->isIdenticalTo(sprintf('%s://%s:%d/v%d/', $protocol, $host, $port, $version))
        ;
    }

    public function testGetVersion_SetVersion()
    {
        $this
            ->given($this->newTestedInstance([]))
            ->and($defaultVersion = 1)
            ->and($randomVersion = rand(0, PHP_INT_MAX))
            ->then
                ->integer($this->testedInstance->getVersion())
                    ->isIdenticalTo($defaultVersion)
                ->object($this->testedInstance->setVersion($randomVersion))
                    ->isTestedInstance
                ->integer($this->testedInstance->getVersion())
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
            ->given($this->newTestedInstance([]))
            ->then
                ->assert('Defaults values')
                    ->boolean($this->testedInstance->isLiveMode())->isFalse
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
                        ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
                        ->message
                            ->isIdenticalTo('You did not provide valid public API key for development.')

                    ->exception(function () {
                        $this->testedInstance->getSecretKey();
                    })
                        ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
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
                        ->isInstanceOf(ild78\Exceptions\MissingApiKeyException::class)
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
