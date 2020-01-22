<?php

namespace ild78\tests\unit;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ild78;
use ild78\Customer as testedClass;
use mock;

class Customer extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(ild78\Core\AbstractObject::class)
        ;
    }

    public function test__call()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($email = uniqid())
            ->and($mobile = uniqid())
            ->and($name = uniqid())
            ->then
                ->object($this->testedInstance->setEmail($email))
                    ->isTestedInstance
                ->string($this->testedInstance->getEmail())
                    ->isIdenticalTo($email)

                ->object($this->testedInstance->setMobile($mobile))
                    ->isTestedInstance
                ->string($this->testedInstance->getMobile())
                    ->isIdenticalTo($mobile)

                ->object($this->testedInstance->setName($name))
                    ->isTestedInstance
                ->string($this->testedInstance->getName())
                    ->isIdenticalTo($name)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('customers')
        ;
    }

    public function testSend()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($body = file_get_contents(__DIR__ . '/fixtures/customers/create.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)
            ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setEmail(uniqid()))
            ->and($this->testedInstance->setMobile(uniqid()))
            ->and($this->testedInstance->setName(uniqid()))

            ->and($json = json_encode($this->testedInstance))
            ->and($options = [
                'body' => $json,
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ],
                'timeout' => $config->getTimeout(),
            ])
            ->and($location = $this->testedInstance->getUri())
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull

                ->object($this->testedInstance->send())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $location, $options)
                            ->once

                ->string($this->testedInstance->getId())
                    ->isIdenticalTo('cust_nwSpP6LKE828Inhiu1CXyp7l')

                ->dateTime($this->testedInstance->getCreationDate())
                    ->isEqualTo(new DateTime('@1538565198'))

                ->string($this->testedInstance->getEmail())
                    ->isIdenticalTo('david@coaster.net')

                ->string($this->testedInstance->getMobile())
                    ->isIdenticalTo('+33684858687')

                ->string($this->testedInstance->getName())
                    ->isIdenticalTo('David Coaster')

                // Check it was not called twice
                ->object($this->testedInstance->send())
                    ->isTestedInstance
                    ->isInstanceOf($this->testedInstance->send())

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $location, $options)
                            ->once

                ->assert('Update a property allow new request')
                    ->if($this->testedInstance->setName(uniqid()))
                    ->and($this->testedInstance->send())
                    ->then
                        ->mock($client)
                            ->call('request')
                                ->once

                ->assert('Populate block send')
                    ->if($this->newTestedInstance(uniqid()))
                    ->and($this->testedInstance->setName(uniqid()))
                    ->and($this->testedInstance->populate())
                    ->and($this->testedInstance->send())
                    ->then
                        ->mock($client)
                            ->call('request')
                                ->withAtLeastArguments(['POST'])
                                    ->never

                            ->call('request')
                                ->withAtLeastArguments(['PATCH'])
                                    ->never

                ->assert('An email or a phone number is required')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () {
                            $this->testedInstance->send();
                        })
                            ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                            ->message
                                ->isIdenticalTo('You must provide an email or a phone number to create a customer.')

                        ->mock($client)
                            ->call('request')
                                ->never
        ;
    }

    public function testSend_forUpdate()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($client = new mock\ild78\Http\Client)
            ->and($config->setHttpClient($client))
            ->and($config->setDebug(false))

            ->then
                ->assert('Modify a fresh and not populated instance, will send only known data')
                    ->if($response = new mock\ild78\Http\Response(200))
                    ->and($this->calling($response)->getBody = '{}')
                    ->and($this->calling($client)->request = $response)

                    ->if($this->newTestedInstance(uniqid()))
                    ->and($name = uniqid())
                    ->and($this->testedInstance->setName($name))

                    ->and($options = [
                        'body' => json_encode(['name' => $name]),
                        'headers' => [
                            'Authorization' => $config->getBasicAuthHeader(),
                            'Content-Type' => 'application/json',
                            'User-Agent' => $config->getDefaultUserAgent(),
                        ],
                        'timeout' => $config->getTimeout(),
                    ])

                    ->then
                        ->object($this->testedInstance->send())
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->withArguments('PATCH', $this->testedInstance->getUri(), $options)
                                    ->once

                ->assert('Modify a populated instance will send everything known')
                    ->if($response = new mock\ild78\Http\Response(200))
                    ->and($body = file_get_contents(__DIR__ . '/fixtures/customers/read.json'))
                    ->and($this->calling($response)->getBody[] = $body) // default response
                    ->and($this->calling($response)->getBody[2] = '{}')
                    ->and($this->calling($client)->request = $response)

                    ->if($this->newTestedInstance(uniqid()))
                    ->and($name = str_rot13($this->testedInstance->getName()))
                    ->and($this->testedInstance->setName($name))

                    ->and($body = json_decode($body, true))

                    ->and($options = [
                        'body' => json_encode(['name' => $name]),
                        'headers' => [
                            'Authorization' => $config->getBasicAuthHeader(),
                            'Content-Type' => 'application/json',
                            'User-Agent' => $config->getDefaultUserAgent(),
                        ],
                        'timeout' => $config->getTimeout(),
                    ])

                    ->then
                        ->object($this->testedInstance->send())
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->withArguments('PATCH', $this->testedInstance->getUri(), $options)
                                    ->once

                ->assert('Unmodified instance will not trigger an update')
                    ->if($this->newTestedInstance(uniqid()))
                    ->then
                        ->object($this->testedInstance->send())
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->never
        ;
    }

    public function testSetEmail()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function () {
                    $this->testedInstance->setEmail('');
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidEmailException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid email must be between 5 and 64 characters.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse
        ;
    }

    public function testSetMobile()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function () {
                    $this->testedInstance->setMobile('');
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidMobileException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid mobile must be between 8 and 16 characters.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse
        ;
    }

    public function testSetName()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function () {
                    $this->testedInstance->setName('');
                })
                    ->isInstanceOf(ild78\Exceptions\InvalidNameException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid name must be between 4 and 64 characters.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse
        ;
    }
}
