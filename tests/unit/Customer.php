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

    public function test__construct()
    {
        // Other HTTP errors are tested in Api\Request test case
        // Here we are testing that we will use everything in the response and we have a custom error for 404

        $mock = new MockHandler([
            new Response(200, [], file_get_contents(__DIR__ . '/fixtures/customers/read.json')),
            new Response(404),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = Api\Config::init(uniqid());
        $api->setHttpClient($client);

        $this
            ->assert('Without id')
                ->given($this->newTestedInstance())
                    ->variable($this->testedInstance->getId())
                        ->isNull

            ->assert('With valid id')
                ->given($id = uniqid())
                ->and($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->getId())
                        ->isNotIdenticalTo($id) // Original `$id` is replace by the one in API's data
                        ->isIdenticalTo('cust_9Cle7TXKkjhwqcWx4Kl5cQYk')

                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo('David Coaster')

                    ->string($this->testedInstance->getMobile())
                        ->isIdenticalTo('+33684858687')

                    ->string($this->testedInstance->getEmail())
                        ->isIdenticalTo('david@coaster.com')

                    ->object($date = $this->testedInstance->getCreationDate())
                        ->isInstanceOf('DateTime')
                    ->variable($date->format('U'))
                        ->isEqualTo(1538562174)

            ->assert('With invalid id')
                ->given($id = uniqid())
                ->then
                    ->exception(function () use ($id) {
                        $this->newTestedInstance($id);
                    })
                        ->isInstanceOf(NotFoundException::class)
                        ->hasNestedException
                        ->message
                            ->contains($id)
                            ->contains('Customer')
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
