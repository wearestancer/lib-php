<?php

namespace Stancer\tests\unit;

use Stancer;

class Device extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Network;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Core\AbstractObject::class)
        ;
    }

    public function test__construct()
    {
        $this
            ->if($data = [
                'ip' => $this->ipDataProvider(true),
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
            ->if($city = uniqid())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getCity())
                        ->isNull

                    ->exception(function () use ($city) {
                        $this->testedInstance->setCity($city);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "city".')

                    ->if($this->testedInstance->hydrate(['city' => $city]))
                    ->then
                        ->string($this->testedInstance->getCity())
                            ->isIdenticalTo($city)

                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('city')

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_city())
                        ->isNull

                    ->exception(function () use ($city) {
                        $this->testedInstance->set_city($city);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "city".')

                    ->if($this->testedInstance->hydrate(['city' => $city]))
                    ->then
                        ->string($this->testedInstance->get_city())
                            ->isIdenticalTo($city)

                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('city')

                ->assert('property')
                    ->variable($this->newTestedInstance->city)
                        ->isNull

                    ->exception(function () use ($city) {
                        $this->testedInstance->city = $city;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "city".')

                    ->if($this->testedInstance->hydrate(['city' => $city]))
                    ->then
                        ->string($this->testedInstance->city)
                            ->isIdenticalTo($city)

                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('city')
        ;
    }

    public function testGetCountry_SetCountry()
    {
        $this
            ->if($country = uniqid())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getCountry())
                        ->isNull

                    ->exception(function () use ($country) {
                        $this->testedInstance->setCountry($country);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "country".')

                    ->if($this->testedInstance->hydrate(['country' => $country]))
                    ->then
                        ->string($this->testedInstance->getCountry())
                            ->isIdenticalTo($country)

                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('country')

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_country())
                        ->isNull

                    ->exception(function () use ($country) {
                        $this->testedInstance->set_country($country);
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "country".')

                    ->if($this->testedInstance->hydrate(['country' => $country]))
                    ->then
                        ->string($this->testedInstance->get_country())
                            ->isIdenticalTo($country)

                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('country')

                ->assert('property')
                    ->variable($this->newTestedInstance->country)
                        ->isNull

                    ->exception(function () use ($country) {
                        $this->testedInstance->country = $country;
                    })
                        ->isInstanceOf(Stancer\Exceptions\BadPropertyAccessException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "country".')

                    ->if($this->testedInstance->hydrate(['country' => $country]))
                    ->then
                        ->string($this->testedInstance->country)
                            ->isIdenticalTo($country)

                        ->array($this->testedInstance->jsonSerialize())
                            ->notHasKey('country')
        ;
    }

    public function testGetHttpAccept_SetHttpAccept()
    {
        $this
            ->if($accept = uniqid())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getHttpAccept())
                        ->isNull

                    ->object($this->testedInstance->setHttpAccept($accept))
                        ->isTestedInstance

                    ->string($this->testedInstance->getHttpAccept())
                        ->isIdenticalTo($accept)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('http_accept')
                        ->string['http_accept']
                            ->isIdenticalTo($accept)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_http_accept())
                        ->isNull

                    ->object($this->testedInstance->set_http_accept($accept))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_http_accept())
                        ->isIdenticalTo($accept)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('http_accept')
                        ->string['http_accept']
                            ->isIdenticalTo($accept)

                ->assert('camelCase property')
                    ->variable($this->newTestedInstance->httpAccept)
                        ->isNull

                    ->variable($this->testedInstance->httpAccept = $accept)

                    ->string($this->testedInstance->httpAccept)
                        ->isIdenticalTo($accept)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('http_accept')
                        ->string['http_accept']
                            ->isIdenticalTo($accept)

                ->assert('snake_case property')
                    ->variable($this->newTestedInstance->http_accept)
                        ->isNull

                    ->variable($this->testedInstance->http_accept = $accept)

                    ->string($this->testedInstance->http_accept)
                        ->isIdenticalTo($accept)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('http_accept')
                        ->string['http_accept']
                            ->isIdenticalTo($accept)
        ;
    }

    /**
     * @dataProvider ipDataProvider
     *
     * @param mixed $ip
     */
    public function testGetIp_SetIp($ip)
    {
        $this
            ->if($bad = rand(250, 300) . '.' . rand(250, 300) . '.' . rand(250, 300) . '.' . rand(250, 300))
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getIp())
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
                        ->isInstanceOf(Stancer\Exceptions\InvalidIpAddressException::class)
                        ->message
                            ->isIdenticalTo('"' . $bad . '" is not a valid IP address.')

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_ip())
                        ->isNull

                    ->object($this->testedInstance->set_ip($ip))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_ip())
                        ->isIdenticalTo($ip)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('ip')
                        ->string['ip']
                            ->isIdenticalTo($ip)

                    ->exception(function () use ($bad) {
                        $this->testedInstance->set_ip($bad);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidIpAddressException::class)
                        ->message
                            ->isIdenticalTo('"' . $bad . '" is not a valid IP address.')

                ->assert('property')
                    ->variable($this->newTestedInstance->ip)
                        ->isNull

                    ->variable($this->testedInstance->ip = $ip)

                    ->string($this->testedInstance->ip)
                        ->isIdenticalTo($ip)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('ip')
                        ->string['ip']
                            ->isIdenticalTo($ip)

                    ->exception(function () use ($bad) {
                        $this->testedInstance->ip = $bad;
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidIpAddressException::class)
                        ->message
                            ->isIdenticalTo('"' . $bad . '" is not a valid IP address.')
        ;
    }

    public function testGetLanguages_SetLanguages()
    {
        $this
            ->if($languages = uniqid())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getLanguages())
                        ->isNull

                    ->object($this->testedInstance->setLanguages($languages))
                        ->isTestedInstance

                    ->string($this->testedInstance->getLanguages())
                        ->isIdenticalTo($languages)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('languages')
                        ->string['languages']
                            ->isIdenticalTo($languages)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_languages())
                        ->isNull

                    ->object($this->testedInstance->set_languages($languages))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_languages())
                        ->isIdenticalTo($languages)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('languages')
                        ->string['languages']
                            ->isIdenticalTo($languages)

                ->assert('property')
                    ->variable($this->newTestedInstance->languages)
                        ->isNull

                    ->variable($this->testedInstance->languages = $languages)

                    ->string($this->testedInstance->languages)
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
            ->if($port = rand(1, 65535))
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getPort())
                        ->isNull

                    ->object($this->testedInstance->setPort($port))
                        ->isTestedInstance

                    ->integer($this->testedInstance->getPort())
                        ->isIdenticalTo($port)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('port')
                        ->integer['port']
                            ->isIdenticalTo($port)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_port())
                        ->isNull

                    ->object($this->testedInstance->set_port($port))
                        ->isTestedInstance

                    ->integer($this->testedInstance->get_port())
                        ->isIdenticalTo($port)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('port')
                        ->integer['port']
                            ->isIdenticalTo($port)

                ->assert('property')
                    ->variable($this->newTestedInstance->port)
                        ->isNull

                    ->variable($this->testedInstance->port = $port)

                    ->integer($this->testedInstance->port)
                        ->isIdenticalTo($port)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('port')
                        ->integer['port']
                            ->isIdenticalTo($port)
        ;
    }

    public function testGetUserAgent_SetUserAgent()
    {
        $this
            ->if($agent = uniqid())
            ->then
                ->assert('camelCase method')
                    ->variable($this->newTestedInstance->getUserAgent())
                        ->isNull

                    ->object($this->testedInstance->setUserAgent($agent))
                        ->isTestedInstance

                    ->string($this->testedInstance->getUserAgent())
                        ->isIdenticalTo($agent)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('user_agent')
                        ->string['user_agent']
                            ->isIdenticalTo($agent)

                ->assert('snake_case method')
                    ->variable($this->newTestedInstance->get_user_agent())
                        ->isNull

                    ->object($this->testedInstance->set_user_agent($agent))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_user_agent())
                        ->isIdenticalTo($agent)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('user_agent')
                        ->string['user_agent']
                            ->isIdenticalTo($agent)

                ->assert('camelCase property')
                    ->variable($this->newTestedInstance->userAgent)
                        ->isNull

                    ->variable($this->testedInstance->userAgent = $agent)

                    ->string($this->testedInstance->userAgent)
                        ->isIdenticalTo($agent)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('user_agent')
                        ->string['user_agent']
                            ->isIdenticalTo($agent)

                ->assert('snake_case property')
                    ->variable($this->newTestedInstance->user_agent)
                        ->isNull

                    ->variable($this->testedInstance->user_agent = $agent)

                    ->string($this->testedInstance->user_agent)
                        ->isIdenticalTo($agent)

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasKey('user_agent')
                        ->string['user_agent']
                            ->isIdenticalTo($agent)
        ;
    }

    public function testHydrateFromEnvironment()
    {
        $this
            ->given($accept = uniqid())
            ->and($agent = uniqid())
            ->and($ip = rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254))
            ->and($languages = uniqid())
            ->and($port = rand(1, 65535))

            ->assert('No IP')
                ->if($this->newTestedInstance)
                ->and($this->function->getenv = null)
                ->then
                    ->exception(function () {
                        $this->testedInstance->hydrateFromEnvironment();
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidIpAddressException::class)
                        ->message
                            ->isIdenticalTo('You must provide an IP address.')

            ->assert('No port')
                ->if($this->newTestedInstance)
                ->and($this->function->getenv = function ($varname) use ($ip) {
                    $name = strtolower($varname);

                    if ($name === 'remote_addr') {
                        return $ip;
                    }

                    return null;
                })

                ->then
                    ->exception(function () {
                        $this->testedInstance->hydrateFromEnvironment();
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidPortException::class)
                        ->message
                            ->isIdenticalTo('You must provide a port.')

            ->assert('Got IP and port')
                ->if($this->newTestedInstance)
                ->and($this->function->getenv = function ($varname) use ($accept, $agent, $ip, $languages, $port) {
                    $name = strtolower($varname);

                    if ($name === 'http_accept') {
                        return $accept;
                    }

                    if ($name === 'http_accept_language') {
                        return $languages;
                    }

                    if ($name === 'http_user_agent') {
                        return $agent;
                    }

                    if ($name === 'remote_addr') {
                        return $ip;
                    }

                    if ($name === 'remote_port') {
                        return $port;
                    }

                    return null;
                })
                ->then
                    ->variable($this->testedInstance->getCity())
                        ->isNull

                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getHttpAccept())
                        ->isNull

                    ->variable($this->testedInstance->getIp())
                        ->isNull

                    ->variable($this->testedInstance->getLanguages())
                        ->isNull

                    ->variable($this->testedInstance->getPort())
                        ->isNull

                    ->variable($this->testedInstance->getUserAgent())
                        ->isNull

                    ->object($this->testedInstance->hydrateFromEnvironment())
                        ->isTestedInstance

                    ->variable($this->testedInstance->getCity())
                        ->isNull

                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->string($this->testedInstance->getHttpAccept())
                        ->isIdenticalTo($accept)

                    ->string($this->testedInstance->getIp())
                        ->isIdenticalTo($ip)

                    ->string($this->testedInstance->getLanguages())
                        ->isIdenticalTo($languages)

                    ->integer($this->testedInstance->getPort())
                        ->isIdenticalTo($port)

                    ->string($this->testedInstance->getUserAgent())
                        ->isIdenticalTo($agent)
        ;
    }
}
