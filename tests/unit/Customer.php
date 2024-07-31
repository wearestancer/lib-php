<?php

namespace Stancer\tests\unit;

use DateTime;
use Stancer;
use Stancer\Customer as testedClass;
use mock;

class Customer extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
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

    public function testBillingAddress()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
            ->assert('Full new address')
                ->if($address = new Stancer\Address())
                ->and($address->setCity($this->getRandomString(1, 50)))
                ->and($address->setCountry($this->getRandomString(3, 3)))
                ->and($address->setLine1($this->getRandomString(1, 50)))
                ->and($address->setLine2($this->getRandomString(1, 50)))
                ->and($address->setLine3($this->getRandomString(1, 50)))
                ->and($address->setMetadata(['origin' => $this->getRandomString(1, 50)]))
                ->and($address->setState($this->getRandomString(1, 3)))
                ->and($address->setzipCode($this->getRandomString(1, 16)))

                ->variable($this->newTestedInstance->getBillingAddress())
                    ->isNull

                ->object($this->testedInstance->setBillingAddress($address))
                    ->isTestedInstance

                ->object($this->testedInstance->getBillingAddress())
                    ->isIdenticalTo($address)

            ->given($this->newTestedInstance)
            ->assert('ID address')
                ->if($address = new Stancer\Address('addr_'.$this->getRandomString(24)))

                ->variable($this->testedInstance->getBillingAddress())
                    ->isNull

                ->object($this->testedInstance->setBillingAddress($address))
                    ->isTestedInstance

                ->object($this->testedInstance->getBillingAddress())
                    ->isIdenticalTo($address)
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
            ->and($response = $this->mockJsonResponse('customers', 'create', new mock\GuzzleHttp\Psr7\Response))
            ->and($this->calling($client)->request = $response)

            ->and($config = $this->mockConfig($client))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setEmail(uniqid()))
            ->and($this->testedInstance->setMobile(uniqid()))
            ->and($this->testedInstance->setName(uniqid()))

            ->and($options = $this->mockRequestOptions($config, [
                'body' => json_encode($this->testedInstance),
            ]))

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
                ->exception(function () {
                    $this->testedInstance->send();
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('The object you tried to send is empty.')

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
                    ->then
                        ->exception(function () {
                            $this->testedInstance->send();
                        })
                            ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                            ->message
                                ->isIdenticalTo('The object you tried to send is empty.')

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
                            ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
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
            ->given($client = new mock\Stancer\Http\Client)
            ->and($config = $this->mockConfig($client))

            ->then
                ->assert('Modify a fresh and not populated instance, will send only known data')
                    ->if($this->calling($client)->request = $this->mockEmptyJsonResponse())

                    ->if($this->newTestedInstance(uniqid()))
                    ->and($name = uniqid())
                    ->and($this->testedInstance->setName($name))

                    ->and($options = $this->mockRequestOptions($config, [
                        'body' => json_encode(['name' => $name]),
                    ]))

                    ->then
                        ->object($this->testedInstance->send())
                            ->isTestedInstance

                        ->mock($client)
                            ->call('request')
                                ->withArguments('PATCH', $this->testedInstance->getUri(), $options)
                                    ->once

                ->assert('Modify a populated instance will send everything known')
                    ->if($response = new mock\Stancer\Http\Response(200))
                    ->and($body = $this->getFixture('customers', 'read'))
                    ->and($this->calling($response)->getBody[] = new Stancer\Http\Stream($body)) // default response
                    ->and($this->calling($response)->getBody[2] = new Stancer\Http\Stream('{}'))
                    ->and($this->calling($client)->request = $response)

                    ->if($this->newTestedInstance(uniqid()))
                    ->and($name = str_rot13($this->testedInstance->getName()))
                    ->and($this->testedInstance->setName($name))

                    ->and($options = $this->mockRequestOptions($config, [
                        'body' => json_encode(['name' => $name]),
                    ]))

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
                        ->exception(function () {
                            $this->testedInstance->send();
                        })
                            ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                            ->message
                                ->isIdenticalTo('The object you tried to send is empty.')

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
                    ->isInstanceOf(Stancer\Exceptions\InvalidEmailException::class)
                    ->message
                        ->isIdenticalTo('A valid email must be between 5 and 64 characters.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse
        ;
    }

    public function testSetExternalId()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($externalId = $this->getUuid())
            ->and($tooLong = $this->getUuid() . substr(uniqid(), 0, 1))
            ->then
                ->variable($this->testedInstance->getExternalId())
                    ->isNull

                ->object($this->testedInstance->setExternalId($externalId))
                    ->isTestedInstance

                ->string($this->testedInstance->getExternalId())
                    ->isIdenticalTo($externalId)

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('external_id')
                    ->string['external_id']
                        ->isEqualTo($externalId)

                ->exception(function () use ($tooLong) {
                    $this->testedInstance->setExternalId($tooLong);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidExternalIdException::class)
                    ->message
                        ->isIdenticalTo('A valid external ID must have less than 36 characters.')
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
                    ->isInstanceOf(Stancer\Exceptions\InvalidMobileException::class)
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
                    ->isInstanceOf(Stancer\Exceptions\InvalidNameException::class)
                    ->message
                        ->isIdenticalTo('A valid name must be between 3 and 64 characters.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse
        ;
    }
    public function testShippingAddress()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
            ->assert('Full new address')
                ->if($address = new Stancer\Address())
                ->and($address->setCity($this->getRandomString(1, 50)))
                ->and($address->setCountry($this->getRandomString(3, 3)))
                ->and($address->setLine1($this->getRandomString(1, 50)))
                ->and($address->setLine2($this->getRandomString(1, 50)))
                ->and($address->setLine3($this->getRandomString(1, 50)))
                ->and($address->setMetadata(['origin' => $this->getRandomString(1, 50)]))
                ->and($address->setState($this->getRandomString(1, 3)))
                ->and($address->setzipCode($this->getRandomString(1, 16)))

                ->variable($this->newTestedInstance->getShippingAddress())
                    ->isNull

                ->object($this->testedInstance->setShippingAddress($address))
                    ->isTestedInstance

                ->object($this->testedInstance->getShippingAddress())
                    ->isIdenticalTo($address)

            ->given($this->newTestedInstance)
            ->assert('ID address')
                ->if($address = new Stancer\Address('addr_'.$this->getRandomString(24)))

                ->variable($this->testedInstance->getShippingAddress())
                    ->isNull

                ->object($this->testedInstance->setShippingAddress($address))
                    ->isTestedInstance

                ->object($this->testedInstance->getShippingAddress())
                    ->isIdenticalTo($address)
        ;
    }
}
