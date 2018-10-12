<?php

namespace ild78\tests\unit;

use atoum;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ild78\Api;
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
                'headers' => ['Authorization' => 'Basic ' . $config->getKey()]
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
        ;
    }
}
