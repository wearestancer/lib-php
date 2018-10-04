<?php

namespace ild78\tests\unit;

use atoum;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ild78\Api;
use ild78\Core;
use ild78\Customer as testedClass;
use ild78\Exceptions\NotFoundException;

class Customer extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Core::class)
        ;
    }

    public function test__construct()
    {
        // Other HTTP errors are tested in Core test case
        // Here we are testing that we will use everything in the response and we have a custom error for 404

        $mock = new MockHandler([
            new Response(200, [], file_get_contents(__DIR__ . '/fixtures/customers/read.json')),
            new Response(404),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Api(uniqid());
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

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('customers')
        ;
    }
}
