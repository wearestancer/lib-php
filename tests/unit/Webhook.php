<?php

namespace Stancer\Tests\unit;

use mock;
use Stancer;

class Webhook extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Events;

    /**
     * @tags Webhooks
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(Stancer\Core\AbstractObject::class)
                ->hasTrait(Stancer\Traits\SearchTrait::class)
        ;
    }

    /**
     * @tags Webhooks
     */
    public function test_getUrl()
    {
        $this
            ->if($url = $this->getRandomString(1, 2048))
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getUrl())
                        ->isNull

                ->assert('Update value')
                    ->object($this->newTestedInstance->setUrl($url))
                        ->isTestedInstance

                    ->string($this->testedInstance->getUrl())
                        ->isIdenticalTo($url)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_url())
                        ->isNull
                    ->object($this->testedInstance->set_url($url))
                        ->isTestedInstance
                    ->string($this->testedInstance->get_url())
                        ->isIdenticalTo($url)

                    ->variable($this->newTestedInstance->url)
                        ->isNull
                    ->given($this->testedInstance->url = $url)
                    ->string($this->testedInstance->url)
                        ->isIdenticalTo($url)
        ;
    }

    /**
     * @tags Webhooks
     */
    public function test_getEnabled()
    {
        $this
            ->if($enabled = (bool) random_int(0, 1))
            ->then
                ->assert('Null as default')
                    ->variable($this->newTestedInstance->getEnabled())
                        ->isNull

                ->assert('Update value')
                    ->object($this->newTestedInstance->setEnabled($enabled))
                        ->isTestedInstance

                    ->variable($this->testedInstance->getEnabled())
                        ->isIdenticalTo($enabled)

                ->assert('Aliases')
                    ->variable($this->newTestedInstance->get_enabled())
                        ->isNull
                    ->object($this->testedInstance->set_enabled($enabled))
                        ->isTestedInstance
                    ->variable($this->testedInstance->get_enabled())
                        ->isIdenticalTo($enabled)

                    ->variable($this->newTestedInstance->enabled)
                        ->isNull
                    ->given($this->testedInstance->enabled = $enabled)
                    ->variable($this->testedInstance->enabled)
                        ->isIdenticalTo($enabled)
        ;
    }

    /**
     * @tags Webhooks
     */
    public function test_getSecret()
    {
        $this
            ->if($value = $this->getRandomString(1, 70))
            ->then
                ->variable($this->newTestedInstance->getSecret())
                    ->isNull

                ->exception(function () use ($value) {
                    $this->newTestedInstance->setSecret($value);
                })
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "secret".')

            ->if($client = new mock\Stancer\Http\Client())
            ->and($this->mockConfig($client, Stancer\Enum\ApiVersion::VERSION_2))
            ->and($this->calling($client)->request = $this->mockJsonResponse('webhook', 'read'))

            ->if($this->newTestedInstance(uniqid()))

            ->if($location = $this->testedInstance->getUri())

            ->mock($client)
                ->call('request')
                    ->withArguments('GET', $location)
                        ->never
            ->variable($this->testedInstance->getEnabled())
            ->mock($client)
                ->call('request')
                    ->withArguments('GET', $location)
                        ->once

                ->string($this->testedInstance->getSecret())
                    ->isIdenticalTo('15dc23af918e5dcf6b909166e9519d1bdfb9ea36792e9209')

                ->string($this->testedInstance->secret)
                    ->isIdenticalTo('15dc23af918e5dcf6b909166e9519d1bdfb9ea36792e9209')
        ;
    }

    /**
     * @tags Webhooks
     *
     * @dataProvider signatureCheckProvider
     */
    public function test_simpleHmac(array $eventData)
    {
        $this
            ->if($hmac = $eventData['hmac'])
            ->and($timestamp = $eventData['timestamp'])
            ->and($payload = $eventData['payload'])
            ->and($this->newTestedInstance(['secret' => $eventData['secret']]))
            ->then
                ->variable($this->testedInstance->checkHmac($hmac, $timestamp, $payload))
                    ->isNull()
        ;
    }

    /**
     * @tags Webhooks
     *
     * @dataProvider signatureCheckProvider
     */
    public function test_verifyBody(array $eventData)
    {
        $this
            ->if($stancer_secret = $eventData['signature'])
            ->and($body = $eventData['payload'])
            ->and($this->newTestedInstance(['secret' => $eventData['secret']]))
            ->then
                ->variable($this->testedInstance->verifyBody($body, $stancer_secret, new \DateTimeImmutable('@946684800'), new \DateInterval('PT2M')))
                    ->isNull()
        ;
    }

    /**
     * @tags Webhooks
     *
     * @dataProvider eventProvider
     */
    public function test_realHmac(string $hmac, string $payload, string $secret, string $timestamp, string $signature)
    {
        $this
        ->if($time = new \DateTimeImmutable('@' . $timestamp))
        ->and($two_minutes = new \DateInterval('PT2M'))
            ->then
                ->variable($this->newTestedInstance(['secret' => $secret])->verifyBody($payload, $signature, $time, $two_minutes))

        ->assert('wrong datetime does not validate')
            ->exception(fn () => $this->newTestedInstance(['secret' => $secret])->verifyBody($payload, $signature, new \DateTimeImmutable('@1801085781'), $two_minutes))
                ->isInstanceOf(\Stancer\Exceptions\InvalidSignatureException::class)
                    ->message
                        ->isIdenticalTo('Expired signature')
        ->assert('wrong timestamp does not validate')
            ->if($bad_signature = 't=1792485771,v1=' . $hmac)
            ->exception(fn () => $this->newTestedInstance(['secret' => $secret])->verifyBody($payload, $bad_signature, $time, $two_minutes))
                ->isInstanceOf(\Stancer\Exceptions\InvalidSignatureException::class)
                    ->message
                        ->isIdenticalTo('Invalid signature')
        ->assert('wrong signaturedoes not validate')
            ->if($bad_signature = 't=' . $timestamp . ',v1=02f0efd40bcfdbf23fab999953cbdc0323b8817657947bd56966f1b0fe7dabaaa')

            ->exception(fn () => $this->newTestedInstance(['secret' => $secret])->verifyBody($payload, $bad_signature, $time, $two_minutes))
                ->isInstanceOf(\Stancer\Exceptions\InvalidSignatureException::class)
                    ->message
                        ->isIdenticalTo('Invalid signature')
        ->assert('wrong secret does not validate')
            ->if($wrong_secret = 'f61e1955a003c1a9973ccf0cb849bd861a4452d0ead317ba')

            ->exception(fn () => $this->newTestedInstance(['secret' => $wrong_secret])->verifyBody($payload, $signature, $time, $two_minutes))
                ->isInstanceOf(\Stancer\Exceptions\InvalidSignatureException::class)
                    ->message
                        ->isIdenticalTo('Invalid signature')
        ;
    }

    /**
     * @tags Webhooks
     *
     * @dataProvider eventProvider
     */
    public function test_getEvent(string $hmac, string $payload, string $secret, string $timestamp, string $signature)
    {
        $this
            ->if($this->newTestedInstance(['secret' => $secret]))
            ->assert('goodJson return an event')
                ->object($event = $this->testedInstance->getEvent($payload))
                    ->isInstanceOf(Stancer\Event::class)
                ->string($event->type)
                    ->isIdenticalTo('customer.created')
                ->string($event->id)
                    ->isIdenticalTo('evt_9Om8qngWyslwRJizGObuXH5y')
            ->assert('badjson throw')
                ->exception(fn () => $this->newTestedInstance->getEvent('{ bar: "baz", }'))
                    ->isInstanceOf(\Stancer\Exceptions\InvalidEventException::class)
                    ->message
                        ->isIdenticalTo('Data supplied are not valid event data,make sure that the data supplied come from an event request.')
            ->assert('string as json throw')
                ->exception(fn () => $this->newTestedInstance->getEvent('hello world!'))
                    ->isInstanceOf(\Stancer\Exceptions\InvalidEventException::class)
                    ->message
                        ->isIdenticalTo('Data supplied are not valid event data,make sure that the data supplied come from an event request.')
        ;
    }
}
