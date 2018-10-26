<?php

namespace ild78\tests\unit\Api;

use atoum;
use ild78;
use ild78\Api\Config as testedClass;
use ild78\Exceptions\InvalidArgumentException;
use ild78\Exceptions\NotAuthorizedException;
use mock;
use Psr;

class Config extends atoum
{
    public function test__construct()
    {
        $this
            ->given($key = uniqid())
            ->and($this->newTestedInstance($key))
            ->then
                ->string($this->testedInstance->getKey())
                    ->isIdenticalTo($key)
        ;
    }

    public function testGetBasicAuthHeader()
    {
        $this
            ->given($key1 = uniqid())
            ->and($this->newTestedInstance($key1))
            ->then
                ->string($this->testedInstance->getBasicAuthHeader())
                    ->isIdenticalTo('Basic ' . base64_encode($key1 . ':'))
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

            ->if($this->newTestedInstance(uniqid()))
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
            ->given($this->newTestedInstance(uniqid()))
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
        $mock = new mock\GuzzleHttp\ClientInterface;

        $this
            ->given($this->newTestedInstance(uniqid()))
            ->then
                ->object($this->testedInstance->getHttpClient())
                    ->isInstanceOf('GuzzleHttp\Client') // Automagicaly created instance

                ->object($this->testedInstance->setHttpClient($mock))
                    ->isTestedInstance

                ->object($this->testedInstance->getHttpClient())
                    ->isIdenticalTo($mock)
        ;
    }

    public function testGetKey_SetKey()
    {
        $this
            ->given($key1 = uniqid())
            ->and($key2 = uniqid())
            ->and($this->newTestedInstance($key1))
            ->then
                ->string($this->testedInstance->getKey())
                    ->isIdenticalTo($key1)

                ->object($this->testedInstance->setKey($key2))
                    ->isTestedInstance

                ->string($this->testedInstance->getKey())
                    ->isIdenticalTo($key2)
        ;
    }

    public function testGetLogger_SetLogger()
    {
        $this
            ->given($this->newTestedInstance(uniqid()))
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
            ->given($this->newTestedInstance(uniqid()))
            ->and($invalidMode = uniqid())
            ->then
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
            ->given($this->newTestedInstance(uniqid()))
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

    public function testGetTimeout_SetTimeout()
    {
        $this
            ->given($this->newTestedInstance(uniqid()))
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
            ->given($this->newTestedInstance(uniqid()))
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
            ->given($this->newTestedInstance(uniqid()))
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
            ->given($key = uniqid())
            ->then
                ->object($obj = testedClass::init($key))
                    ->isInstanceOf(testedClass::class)
                ->string($obj->getKey())
                    ->isIdenticalTo($key)
                ->object(testedClass::getGlobal())
                    ->isIdenticalTo($obj)
        ;
    }

    public function testModes()
    {
        $this
            ->given($this->newTestedInstance(uniqid()))
            ->then
                ->assert('Defaults values')
                    ->boolean($this->testedInstance->isLiveMode())->isTrue
                    ->boolean($this->testedInstance->isNotLiveMode())->isFalse
                    ->boolean($this->testedInstance->isTestMode())->isFalse
                    ->boolean($this->testedInstance->isNotTestMode())->isTrue

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
}
