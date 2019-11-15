<?php

namespace ild78\tests\unit;

use GuzzleHttp;
use ild78;
use ild78\Config as testedClass;
use ild78\Exceptions;
use ild78\Exceptions\InvalidArgumentException;
use ild78\Exceptions\NotAuthorizedException;
use mock;
use Psr;

class Config extends ild78\Tests\atoum
{
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
                ->isInstanceOf(InvalidArgumentException::class)
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
                    ->isInstanceOf(ild78\Api\Logger::class)

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
                    ->isInstanceOf(InvalidArgumentException::class)
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
                    ->isInstanceOf(Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid public API key for production.')

            ->if($this->newTestedInstance([$pprod]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::TEST_MODE)->getPublicKey();
                })
                    ->isInstanceOf(Exceptions\MissingApiKeyException::class)
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
                    ->isInstanceOf(Exceptions\MissingApiKeyException::class)
                    ->message
                        ->isIdenticalTo('You did not provide valid secret API key for production.')

            ->if($this->newTestedInstance([$sprod]))
            ->then
                ->exception(function () {
                    $this->testedInstance->setMode(testedClass::TEST_MODE)->getSecretKey();
                })
                    ->isInstanceOf(Exceptions\MissingApiKeyException::class)
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
                        ->isInstanceOf(Exceptions\MissingApiKeyException::class)
                        ->message
                            ->isIdenticalTo('You did not provide valid public API key for development.')

                    ->exception(function () {
                        $this->testedInstance->getSecretKey();
                    })
                        ->isInstanceOf(Exceptions\MissingApiKeyException::class)
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
                        ->isInstanceOf(Exceptions\MissingApiKeyException::class)
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
