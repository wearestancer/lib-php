<?php

namespace ild78\tests\unit;

use atoum;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ild78\Api;
use ild78\Exceptions;
use ild78\Customer as testedClass;
use ild78\Exceptions\NotFoundException;
use mock;

class Customer extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
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

    public function testSave()
    {
        $this
            ->given($client = new mock\GuzzleHttp\Client)
            ->and($response = new mock\GuzzleHttp\Psr7\Response)
            ->and($body = file_get_contents(__DIR__ . '/fixtures/customers/create.json'))
            ->and($this->calling($response)->getBody = $body)
            ->and($this->calling($client)->request = $response)
            ->and($config = Api\Config::init(uniqid()))
            ->and($config->setHttpClient($client))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setEmail(uniqid()))
            ->and($this->testedInstance->setMobile(uniqid()))
            ->and($this->testedInstance->setName(uniqid()))

            ->and($json = json_encode($this->testedInstance))
            ->and($options = [
                'body' => $json,
                'headers' => ['Authorization' => $config->getBasicAuthHeader()]
            ])
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull
                ->object($this->testedInstance->save())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $this->testedInstance->getEndpoint(), $options)
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
                ->object($this->testedInstance->save())
                    ->isTestedInstance
                    ->isInstanceOf($this->testedInstance->save())

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $this->testedInstance->getEndpoint(), $options)
                            ->once

                ->assert('Update a property allow new request')
                    ->if($this->testedInstance->setName(uniqid()))
                    ->and($this->testedInstance->save())
                    ->then
                        ->mock($client)
                            ->call('request')
                                ->withAtLeastArguments(['POST'])
                                    ->once

                ->assert('Populate block save')
                    ->if($this->testedInstance->setName(uniqid()))
                    ->and($this->testedInstance->populate())
                    ->and($this->testedInstance->save())
                    ->then
                        ->mock($client)
                            ->call('request')
                                ->withAtLeastArguments(['POST'])
                                    ->never

                ->assert('An email or a phone number is required')
                    ->given($this->newTestedInstance)
                    ->then
                        ->exception(function () {
                            $this->testedInstance->save();
                        })
                            ->isInstanceOf(Exceptions\BadMethodCallException::class)
                            ->message
                                ->isIdenticalTo('You must provide an email or a phone number to create a customer.')

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
                    ->isInstanceOf(Exceptions\InvalidEmailException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid email must be between 5 and 64 characters.')
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
                    ->isInstanceOf(Exceptions\InvalidMobileException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid mobile must be between 8 and 16 characters.')
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
                    ->isInstanceOf(Exceptions\InvalidNameException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid name must be between 4 and 64 characters.')
        ;
    }
}
