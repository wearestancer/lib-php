<?php

namespace ild78\tests\unit\Api;

use atoum;
use GuzzleHttp;
use ild78\Api;
use ild78\Api\Object as testedClass;
use ild78\Exceptions;

class Object extends atoum
{
    public function test__construct()
    {
        $this
            ->assert('Without id')
                ->given($this->newTestedInstance())
                    ->variable($this->testedInstance->getId())
                        ->isNull

            ->assert('With valid id')
                ->given($id = uniqid())
                ->and($timestamp = rand())
                ->and($mock = new GuzzleHttp\Handler\MockHandler([
                    new GuzzleHttp\Psr7\Response(200, [], '{"id":"' . $id . '","created":' . $timestamp . '}'),
                ]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($api = Api\Config::init(uniqid()))
                ->and($api->setHttpClient($client))

                ->if($this->newTestedInstance($id))
                ->then
                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo($id)

                    ->object($date = $this->testedInstance->getCreationDate())
                        ->isInstanceOf('DateTime')
                    ->variable($date->format('U'))
                        ->isEqualTo($timestamp)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isEmpty
        ;
    }

    public function testGetId()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getId())
                    ->isNull // No default value
        ;
    }

    public function testHydrate()
    {
        $this
            ->given($data = [
                'id' => uniqid(),
                'created' => rand(0, PHP_INT_MAX),
            ])
            ->and($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->hydrate($data))
                    ->isTestedInstance
                ->string($this->testedInstance->getId())
                    ->isIdenticalTo($data['id'])
                ->dateTime($date = $this->testedInstance->getCreationDate())
                    ->variable($date->format('U'))
                        ->isEqualTo($data['created'])
        ;
    }
}
