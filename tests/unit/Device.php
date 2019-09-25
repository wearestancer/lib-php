<?php

namespace ild78\tests\unit;

use ild78;

class Device extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Network;

    public function beforeTestMethod($method)
    {
        if ($method !== 'test__construct') {
            $_SERVER['SERVER_ADDR'] = $this->ipDataProvider()[0];
            $_SERVER['SERVER_PORT'] = rand(1, 65535);
        }
    }

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(ild78\Api\AbstractObject::class)
        ;
    }

    public function test__construct()
    {
        $this
            ->if($data = [
                'ip' => $this->ipDataProvider()[0],
                'port' => rand(1, 65535),
            ])
            ->then
                ->object($this->newTestedInstance($data))
                    ->isInstanceOfTestedClass

                ->exception(function () {
                    $data = [
                        'port' => rand(1, 65535),
                    ];

                    $this->newTestedInstance($data);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidIpAddressException::class)
                    ->message
                        ->isIdenticalTo('You must provide an IP address.')

                ->exception(function () {
                    $data = [
                        'ip' => $this->ipDataProvider()[0],
                    ];

                    $this->newTestedInstance($data);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidPortException::class)
                    ->message
                        ->isIdenticalTo('You must provide a port.')
        ;
    }

    public function testGetCity_SetCity()
    {
        $this
            ->given($city = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->getCity())
                    ->isNull

                ->object($this->testedInstance->setCity($city))
                    ->isTestedInstance

                ->string($this->testedInstance->getCity())
                    ->isIdenticalTo($city)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasKey('city')
                    ->string['city']
                        ->isIdenticalTo($city)
        ;
    }

    public function testGetCountry_SetCountry()
    {
        $this
            ->given($country = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->getCountry())
                    ->isNull

                ->object($this->testedInstance->setCountry($country))
                    ->isTestedInstance

                ->string($this->testedInstance->getCountry())
                    ->isIdenticalTo($country)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasKey('country')
                    ->string['country']
                        ->isIdenticalTo($country)
        ;
    }

    public function testGetHttpAccept_SetHttpAccept()
    {
        $this
            ->given($default = uniqid())
            ->and($_SERVER['HTTP_ACCEPT'] = $default)

            ->and($accept = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->string($this->testedInstance->getHttpAccept())
                    ->isIdenticalTo($default)

                ->object($this->testedInstance->setHttpAccept($accept))
                    ->isTestedInstance

                ->string($this->testedInstance->getHttpAccept())
                    ->isIdenticalTo($accept)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasKey('http_accept')
                    ->string['http_accept']
                        ->isIdenticalTo($accept)
        ;
    }

    /**
     * @dataProvider ipDataProvider
     */
    public function testGetIp_SetIp($ip)
    {
        $this
            ->given($default = rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254))
            ->and($_SERVER['SERVER_ADDR'] = $default)

            ->and($bad = rand(250, 300) . '.' . rand(250, 300) . '.' . rand(250, 300) . '.' . rand(250, 300))

            ->if($this->newTestedInstance)

            ->then
                ->string($this->testedInstance->getIp())
                    ->isIdenticalTo($default)

                ->object($this->testedInstance->setIp($ip))
                    ->isTestedInstance

                ->string($this->testedInstance->getIp())
                    ->isIdenticalTo($ip)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasKey('ip')
                    ->string['ip']
                        ->isIdenticalTo($ip)

                ->exception(function () use ($bad) {
                    $this->testedInstance->setIp($bad);
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidIpAddressException::class)
                    ->message
                        ->isIdenticalTo('"' . $bad . '" is not a valid IP address.')
        ;
    }

    public function testGetLanguages_SetLanguages()
    {
        $this
            ->given($default = uniqid())
            ->and($_SERVER['HTTP_ACCEPT_LANGUAGE'] = $default)

            ->and($languages = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->string($this->testedInstance->getLanguages())
                    ->isIdenticalTo($default)

                ->object($this->testedInstance->setLanguages($languages))
                    ->isTestedInstance

                ->string($this->testedInstance->getLanguages())
                    ->isIdenticalTo($languages)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasKey('languages')
                    ->string['languages']
                        ->isIdenticalTo($languages)
        ;
    }

    public function testGetPort_SetPort()
    {
        $this
            ->given($default = rand(1, 65535))
            ->and($_SERVER['SERVER_PORT'] = $default)

            ->and($port = rand(1, 65535))

            ->if($this->newTestedInstance)

            ->then
                ->integer($this->testedInstance->getPort())
                    ->isIdenticalTo($default)

                ->object($this->testedInstance->setPort($port))
                    ->isTestedInstance

                ->integer($this->testedInstance->getPort())
                    ->isIdenticalTo($port)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasKey('port')
                    ->integer['port']
                        ->isIdenticalTo($port)

                ->exception(function () {
                    $this->testedInstance->setPort(rand(65535, 70000));
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidPortException::class)
                    ->message
                        ->isIdenticalTo('Port must be greater than or equal to 1 and be less than or equal to 65535.')
        ;
    }

    public function testGetUserAgent_SetUserAgent()
    {
        $this
            ->given($default = uniqid())
            ->and($_SERVER['HTTP_USER_AGENT'] = $default)

            ->and($agent = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->string($this->testedInstance->getUserAgent())
                    ->isIdenticalTo($default)

                ->object($this->testedInstance->setUserAgent($agent))
                    ->isTestedInstance

                ->string($this->testedInstance->getUserAgent())
                    ->isIdenticalTo($agent)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasKey('user_agent')
                    ->string['user_agent']
                        ->isIdenticalTo($agent)
        ;
    }
}
