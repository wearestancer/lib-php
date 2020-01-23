<?php

namespace ild78\tests\unit;

use ild78;

class Device extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Network;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(ild78\Core\AbstractObject::class)
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
                ->object($this->newTestedInstance())
                    ->isInstanceOfTestedClass

                ->object($this->newTestedInstance($data))
                    ->isInstanceOfTestedClass
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
            ->given($accept = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->getHttpAccept())
                    ->isNull

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
            ->given($bad = rand(250, 300) . '.' . rand(250, 300) . '.' . rand(250, 300) . '.' . rand(250, 300))

            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->getIp())
                    ->isNull

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
            ->given($languages = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->getLanguages())
                    ->isNull

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
            ->given($port = rand(1, 65535))

            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->getPort())
                    ->isNull

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
            ->given($agent = uniqid())

            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->getUserAgent())
                    ->isNull

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
