<?php

namespace ild78\tests\unit;

use atoum;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ild78\Api;
use ild78\Card;
use ild78\Customer;
use ild78\Exceptions\NotFoundException;
use ild78\Payment as testedClass;

class Payment extends atoum
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
            new Response(200, [], file_get_contents(__DIR__ . '/fixtures/payment/read.json')),
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
                        ->isIdenticalTo('paym_SKMLflt8NBATuiUzgvTYqsw5')

                    ->integer($this->testedInstance->getAmount())
                        ->isIdenticalTo(3406)

                    ->object($card = $this->testedInstance->getCard())
                        ->isInstanceOf(Card::class)
                    ->string($card->getBrand())
                        ->isIdenticalTo('visa')
                    ->string($card->getCountry())
                        ->isIdenticalTo('US')
                    ->integer($card->getExpMonth())
                        ->isIdenticalTo(3)
                    ->integer($card->getExpYear())
                        ->isIdenticalTo(2021)
                    ->string($card->getId())
                        ->isIdenticalTo('card_jSmaDq5t5lMnz6H8tCZ0AbRG')
                    ->string($card->getLast4())
                        ->isIdenticalTo('4242')
                    ->variable($card->getName())
                        ->isIdenticalTo(null)
                    ->variable($card->getZipCode())
                        ->isIdenticalTo(null)

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo('FR')

                    ->string($this->testedInstance->getCurrency())
                        ->isIdenticalTo('eur')

                    ->variable($this->testedInstance->getDescription())
                        ->isNull

                    ->variable($this->testedInstance->getIdCustomer())
                        ->isNull

                    ->string($this->testedInstance->getMethod())
                        ->isIdenticalTo('card')

                    ->string($this->testedInstance->getOrderId())
                        ->isIdenticalTo('815730837')

                    ->string($this->testedInstance->getResponse())
                        ->isIdenticalTo('00')

                    ->string($this->testedInstance->getStatus())
                        ->isIdenticalTo('to_capture')

                    ->object($date = $this->testedInstance->getCreationDate())
                        ->isInstanceOf('DateTime')
                    ->variable($date->format('U'))
                        ->isEqualTo(1538492150)

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
                            ->contains('Payment')
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('checkout')
        ;
    }

    public function testHydrateCard()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($data = [
                'id' => uniqid(),
                'brand' => uniqid(),
                'country' => uniqid(),
                'cvc' => rand(0, PHP_INT_MAX),
                'exp_month' => rand(0, PHP_INT_MAX),
                'exp_year' => rand(0, PHP_INT_MAX),
                'last4' => uniqid(),
                'name' => uniqid(),
                'number' => rand(0, PHP_INT_MAX),
                'zip_code' => uniqid(),
            ])
            ->then
                ->object($this->testedInstance->hydrateCard($data))
                    ->isTestedInstance
                ->object($card = $this->testedInstance->getCard())
                    ->isInstanceOf(Card::class)

                ->string($card->getId())
                    ->isIdenticalTo($data['id'])
                ->string($card->getBrand())
                    ->isIdenticalTo($data['brand'])
                ->string($card->getCountry())
                    ->isIdenticalTo($data['country'])
                ->integer($card->getCvc())
                    ->isIdenticalTo($data['cvc'])
                ->integer($card->getExpMonth())
                    ->isIdenticalTo($data['exp_month'])
                ->integer($card->getExpYear())
                    ->isIdenticalTo($data['exp_year'])
                ->string($card->getLast4())
                    ->isIdenticalTo($data['last4'])
                ->string($card->getName())
                    ->isIdenticalTo($data['name'])
                ->integer($card->getNumber())
                    ->isIdenticalTo($data['number'])
                ->string($card->getZipCode())
                    ->isIdenticalTo($data['zip_code'])
        ;
    }
}
