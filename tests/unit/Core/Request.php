<?php

namespace ild78\tests\unit\Core;

use Exception;
use GuzzleHttp;
use ild78;
use ild78\Core\Request as testedClass;
use mock;

class Request extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Banks;
    use ild78\Tests\Provider\Cards;
    use ild78\Tests\Provider\Http;

    public function testAddCallWithDefaultClient()
    {
        $this
            ->assert('With card')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($number = $this->cardNumberDataProvider(true))
                ->and($card = new ild78\Card(['number' => $number]))
                ->and($payment = new ild78\Payment(['card' => $card]))
                ->and($obfuscated = str_pad($card->getLast4(), strlen($number), 'x', STR_PAD_LEFT))

                ->if($request = new ild78\Http\Request($this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\ild78\Http\Response(200))

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->calling($client)->getLastRequest = $request)
                ->and($this->calling($client)->getLastResponse = $response)
                ->and($config->setHttpClient($client))
                ->then
                    ->object($this->invoke($this->newTestedInstance)->addCallWithDefaultClient($payment))
                        ->isTestedInstance

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(ild78\Http\Request::class)
                        ->isNotIdenticalTo($request) // Returns clone

                    ->object($calls[0]->getRequest()->getBody())
                        ->isInstanceOf(ild78\Http\Stream::class)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->notContains($number)
                        ->contains($obfuscated)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(ild78\Http\Response::class)
                        ->isNotIdenticalTo($response) // Returns clone

            ->assert('With SEPA')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($iban = $this->ibanDataProvider(true))
                ->and($sepa = new ild78\Sepa(['iban' => $iban]))
                ->and($payment = new ild78\Payment(['sepa' => $sepa]))
                ->and($obfuscated = str_pad($sepa->getLast4(), strlen($sepa->getIban()), 'x', STR_PAD_LEFT))

                ->if($request = new ild78\Http\Request($this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\ild78\Http\Response(200))

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->calling($client)->getLastRequest = $request)
                ->and($this->calling($client)->getLastResponse = $response)
                ->and($config->setHttpClient($client))
                ->then
                    ->object($this->invoke($this->newTestedInstance)->addCallWithDefaultClient($payment))
                        ->isTestedInstance

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(ild78\Http\Request::class)
                        ->isNotIdenticalTo($request) // Returns clone

                    ->object($calls[0]->getRequest()->getBody())
                        ->isInstanceOf(ild78\Http\Stream::class)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->notContains($iban)
                        ->contains($obfuscated)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(ild78\Http\Response::class)
                        ->isNotIdenticalTo($response) // Returns clone

            ->assert('With card but without number')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($id = 'card_' . bin2hex(random_bytes(12)))
                ->and($card = new ild78\Card($id))
                ->and($payment = new ild78\Payment(['card' => $card]))

                ->if($request = new ild78\Http\Request($this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\ild78\Http\Response(200))

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->calling($client)->getLastRequest = $request)
                ->and($this->calling($client)->getLastResponse = $response)
                ->and($config->setHttpClient($client))
                ->then
                    ->when(function () use ($payment) {
                        $this
                            ->object($this->invoke($this->newTestedInstance)->addCallWithDefaultClient($payment))
                                ->isTestedInstance
                        ;
                    })
                        ->error
                            ->notExists

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(ild78\Http\Request::class)
                        ->isNotIdenticalTo($request) // Returns clone

                    ->object($calls[0]->getRequest()->getBody())
                        ->isInstanceOf(ild78\Http\Stream::class)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->isIdenticalTo(sprintf('{"card":"%s"}', $id))

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(ild78\Http\Response::class)
                        ->isNotIdenticalTo($response) // Returns clone

            ->assert('With SEPA but without IBAN')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($id = 'sepa_' . bin2hex(random_bytes(12)))
                ->and($sepa = new ild78\Sepa($id))
                ->and($payment = new ild78\Payment(['sepa' => $sepa]))

                ->if($request = new ild78\Http\Request($this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\ild78\Http\Response(200))

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->calling($client)->getLastRequest = $request)
                ->and($this->calling($client)->getLastResponse = $response)
                ->and($config->setHttpClient($client))
                ->then
                    ->when(function () use ($payment) {
                        $this
                            ->object($this->invoke($this->newTestedInstance)->addCallWithDefaultClient($payment))
                                ->isTestedInstance
                        ;
                    })
                        ->error
                            ->notExists

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(ild78\Http\Request::class)
                        ->isNotIdenticalTo($request) // Returns clone

                    ->object($calls[0]->getRequest()->getBody())
                        ->isInstanceOf(ild78\Http\Stream::class)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->isIdenticalTo(sprintf('{"sepa":"%s"}', $id))

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(ild78\Http\Response::class)
                        ->isNotIdenticalTo($response) // Returns clone

            ->assert('With card')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))

                ->if($number = $this->cardNumberDataProvider(true))
                ->and($card = new ild78\Card(['number' => $number]))
                ->and($payment = new ild78\Payment(['card' => $card]))

                ->if($request = new ild78\Http\Request($this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\ild78\Http\Response(200))

                ->if($client = new mock\ild78\Http\Client)
                ->and($this->calling($client)->getLastRequest = $request)
                ->and($this->calling($client)->getLastResponse = $response)
                ->and($config->setHttpClient($client))
                ->then
                    ->object($this->invoke($this->newTestedInstance)->addCallWithDefaultClient($payment))
                        ->isTestedInstance

                    ->array($calls = $config->getCalls())
                        ->isEmpty
        ;
    }

    public function testAddCallWithOtherClient()
    {
        $this
            ->assert('With card')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($number = $this->cardNumberDataProvider(true))
                ->and($card = new ild78\Card(['number' => $number]))
                ->and($payment = new ild78\Payment(['card' => $card]))
                ->and($obfuscated = str_pad($card->getLast4(), strlen($number), 'x', STR_PAD_LEFT))

                ->if($request = new GuzzleHttp\Psr7\Request((string) $this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\GuzzleHttp\Psr7\Response)

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($config->setHttpClient($client))
                ->then
                    ->object($this->invoke($this->newTestedInstance)->addCallWithOtherClient($request, $response, $payment))
                        ->isTestedInstance

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)
                        ->isNotIdenticalTo($request) // Returns clone

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->notContains($number)
                        ->contains($obfuscated)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)
                        ->isIdenticalTo($response)

            ->assert('With SEPA')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($iban = $this->ibanDataProvider(true))
                ->and($sepa = new ild78\Sepa(['iban' => $iban]))
                ->and($payment = new ild78\Payment(['sepa' => $sepa]))
                ->and($obfuscated = str_pad($sepa->getLast4(), strlen($sepa->getIban()), 'x', STR_PAD_LEFT))

                ->if($request = new GuzzleHttp\Psr7\Request((string) $this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\GuzzleHttp\Psr7\Response)

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($config->setHttpClient($client))
                ->then
                    ->object($this->invoke($this->newTestedInstance)->addCallWithOtherClient($request, $response, $payment))
                        ->isTestedInstance

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)
                        ->isNotIdenticalTo($request) // Returns clone

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->notContains($number)
                        ->contains($obfuscated)

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)
                        ->isIdenticalTo($response)

            ->assert('With card but without number')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($id = 'card_' . bin2hex(random_bytes(12)))
                ->and($card = new ild78\Card($id))
                ->and($payment = new ild78\Payment(['card' => $card]))

                ->if($request = new GuzzleHttp\Psr7\Request((string) $this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\GuzzleHttp\Psr7\Response)

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($config->setHttpClient($client))
                ->then
                    ->when(function () use ($request, $response, $payment) {
                        $this
                            ->object($this->invoke($this->newTestedInstance)->addCallWithOtherClient($request, $response, $payment))
                                ->isTestedInstance
                        ;
                    })
                        ->error
                            ->notExists

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)
                        ->isIdenticalTo($request)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->isIdenticalTo(sprintf('{"card":"%s"}', $id))

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)
                        ->isIdenticalTo($response)

            ->assert('With SEPA but without IBAN')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(true))

                ->if($id = 'sepa_' . bin2hex(random_bytes(12)))
                ->and($sepa = new ild78\Sepa($id))
                ->and($payment = new ild78\Payment(['sepa' => $sepa]))

                ->if($request = new GuzzleHttp\Psr7\Request((string) $this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\GuzzleHttp\Psr7\Response)

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($config->setHttpClient($client))
                ->then
                    ->when(function () use ($request, $response, $payment) {
                        $this
                            ->object($this->invoke($this->newTestedInstance)->addCallWithOtherClient($request, $response, $payment))
                                ->isTestedInstance
                        ;
                    })
                        ->error
                            ->notExists

                    ->array($calls = $config->getCalls())
                        ->hasSize(1)

                    ->object($calls[0])
                        ->isInstanceOf(ild78\Core\Request\Call::class)

                    ->variable($calls[0]->getException())
                        ->isNull

                    ->object($calls[0]->getRequest())
                        ->isInstanceOf(GuzzleHttp\Psr7\Request::class)
                        ->isIdenticalTo($request)

                    ->castToString($calls[0]->getRequest()->getBody())
                        ->isIdenticalTo(sprintf('{"sepa":"%s"}', $id))

                    ->object($calls[0]->getResponse())
                        ->isInstanceOf(GuzzleHttp\Psr7\Response::class)
                        ->isIdenticalTo($response)

            ->assert('Without debug mode')
                ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setDebug(false))

                ->if($number = $this->cardNumberDataProvider(true))
                ->and($card = new ild78\Card(['number' => $number]))
                ->and($payment = new ild78\Payment(['card' => $card]))

                ->if($request = new GuzzleHttp\Psr7\Request((string) $this->httpVerbProvider(true), uniqid(), [], $payment->toJson()))
                ->and($response = new mock\GuzzleHttp\Psr7\Response)

                ->if($client = new mock\GuzzleHttp\Client)
                ->and($config->setHttpClient($client))
                ->then
                    ->object($this->invoke($this->newTestedInstance)->addCallWithOtherClient($request, $response, $payment))
                        ->isTestedInstance

                    ->array($calls = $config->getCalls())
                        ->isEmpty
        ;
    }

    public function testRequest_workingWithDefaultClient()
    {
        $this
            ->given($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->if($client = new mock\ild78\Http\Client)
            ->and($response = new mock\ild78\Http\Response(200))
            ->and($body = uniqid())
            ->and($this->calling($response)->getBody = new ild78\Http\Stream($body))
            ->and($this->calling($client)->request = $response)

            ->and($config->setHttpClient($client))

            ->if($this->newTestedInstance)
            ->and($method = new ild78\Http\Verb\Get)
            ->and($object = new mock\ild78\Core\AbstractObject)

            ->if($logger = new mock\ild78\Core\Logger)
            ->and($config->setLogger($logger))

            ->then
                ->assert('No query params')
                    ->if($debugMessage = 'API call : ' . $method . ' ' . $object->getUri())
                    ->then
                        ->string($this->testedInstance->request($method, $object))
                            ->isIdenticalTo($body)
                        ->mock($client)
                            ->call('request')
                                ->withIdenticalArguments((string) $method, $object->getUri())
                                    ->once
                        ->mock($logger)
                            ->call('debug')->withArguments($debugMessage, [])->once
                            ->call('error')->never
                            ->call('notice')->never

                ->assert('With query params')
                    ->if($key1 = uniqid())
                    ->and($value1 = uniqid())
                    ->and($key2 = uniqid())
                    ->and($value2 = uniqid())
                    ->and($query = [$key1 => $value1, $key2 => $value2])
                    ->and($location = $object->getUri() . '?' . $key1 . '=' . $value1 . '&'. $key2 . '=' . $value2)
                    ->and($debugMessage = 'API call : ' . $method . ' ' . $location)
                    ->then
                        ->string($this->testedInstance->request($method, $object, ['query' => $query]))
                            ->isIdenticalTo($body)
                        ->mock($client)
                            ->call('request')
                                ->withIdenticalArguments((string) $method, $location)
                                    ->once
                        ->mock($logger)
                            ->call('debug')->withArguments($debugMessage, [])->once
                            ->call('error')->never
                            ->call('notice')->never
        ;
    }

    public function testRequest_errorsWithDefaultClient()
    {
        $this
            ->given($this->function->setDefaultNamespace('ild78\\Http'))
            ->if($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->assert('With bad credential')
                ->given($content = file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json'))
                ->and($this->function->curl_exec = $content)
                ->and($this->function->curl_getinfo = 401)
                ->and($this->function->curl_errno = rand(100, 200))

                ->if($client = new ild78\Http\Client)
                ->and($config->setHttpClient($client))

                ->if($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new ild78\Http\Verb\Post)

                ->if($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = vsprintf('API call : %s %s', [
                    (string) $method,
                    $object->getUri(),
                ]))
                ->and($criticalMessage = vsprintf('HTTP 401 - Invalid credential : %s', [
                    $config->getSecretKey(),
                ]))

                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () use ($object, $method) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
                        ->message
                            ->isIdenticalTo('You are not authorized to access that resource')

                        ->variable($this->exception->getPrevious())
                            ->isNull

                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('critical')->withArguments($criticalMessage, [])->once

            ->assert('Unsupported method')
                ->if($this->newTestedInstance)
                ->and($this->function->curl_exec = uniqid())
                ->and($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new mock\ild78\Http\Verb\AbstractVerb)

                ->if($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))
                ->and($errorMessage = sprintf('HTTP verb "%s" unsupported', (string) $method))
                ->then
                    ->exception(function () use ($method, $object) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($errorMessage)

                    ->mock($logger)
                        ->call('debug')->never
                        ->call('error')->withArguments($errorMessage)->once
                        ->call('critical')->never

                    ->function('curl_exec')
                        ->wasCalled->never
        ;
    }

    public function testRequest_withGuzzle()
    {
        $this
            ->if($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->assert('Use test of client')
                ->given($client = new mock\GuzzleHttp\Client)
                ->and($response = new mock\GuzzleHttp\Psr7\Response)
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($method = new ild78\Http\Verb\Get)
                ->and($object = new mock\ild78\Core\AbstractObject)

                ->if($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = 'API call : ' . $method . ' ' . $object->getUri())
                ->then
                    ->string($this->testedInstance->request($method, $object))
                        ->isIdenticalTo($body)

                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments((string) $method, $object->getUri())
                                ->once

                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->never

            ->assert('With query parameters')
                ->given($client = new mock\GuzzleHttp\Client)
                ->and($response = new mock\GuzzleHttp\Psr7\Response)
                ->and($body = uniqid())
                ->and($this->calling($response)->getBody = $body)
                ->and($this->calling($client)->request = $response)

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)
                ->and($method = new ild78\Http\Verb\Get)
                ->and($object = new mock\ild78\Core\AbstractObject)

                ->if($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))

                ->if($key1 = uniqid())
                ->and($value1 = uniqid())
                ->and($key2 = uniqid())
                ->and($value2 = uniqid())
                ->and($query = [$key1 => $value1, $key2 => $value2])
                ->and($location = $object->getUri() . '?' . $key1 . '=' . $value1 . '&'. $key2 . '=' . $value2)
                ->and($debugMessage = 'API call : ' . $method . ' ' . $location)
                ->then
                    ->string($this->testedInstance->request($method, $object, ['query' => $query]))
                        ->isIdenticalTo($body)

                    ->mock($client)
                        ->call('request')
                            ->withIdenticalArguments((string) $method, $location)
                                ->once

                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->never

            ->assert('With bad credential')
                ->given($content = file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json'))
                ->and($response = new GuzzleHttp\Psr7\Response(401, [], $content))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($config->setHttpClient($client))

                ->if($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new ild78\Http\Verb\Post)

                ->if($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = vsprintf('API call : %s %s', [
                    (string) $method,
                    $object->getUri(),
                ]))
                ->and($noticeMessage = vsprintf('HTTP 401 - Invalid credential : %s', [
                    $config->getSecretKey(),
                ]))
                ->then
                    ->exception(function () use ($object, $method) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\NotAuthorizedException::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('You are not authorized to access that resource')

                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->never
                        ->call('notice')->withArguments($noticeMessage, [])->once

            ->assert('Use error.message.error as message')
                ->given($body = [
                    'error' => [
                        'message' => [
                            'error' => uniqid(),
                        ],
                    ],
                ])

                ->if($response = new GuzzleHttp\Psr7\Response(400, [], json_encode($body)))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)

                ->if($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new ild78\Http\Verb\Post)
                ->then
                    ->exception(function () use ($method, $object) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\BadRequestException::class)
                        ->message
                            ->isIdenticalTo($body['error']['message']['error'])

            ->assert('Same with error.message.id')
                ->given($body = [
                    'error' => [
                        'message' => [
                            'id' => uniqid(),
                        ],
                    ],
                ])

                ->if($response = new GuzzleHttp\Psr7\Response(400, [], json_encode($body)))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)

                ->if($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new ild78\Http\Verb\Post)
                ->then
                    ->exception(function () use ($method, $object) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\BadRequestException::class)
                        ->message
                            ->isIdenticalTo($body['error']['message']['id'])

            ->assert('Same with error.message.error and error.message.id')
                ->given($body = [
                    'error' => [
                        'message' => [
                            'error' => uniqid(),
                            'id' => uniqid(),
                        ],
                    ],
                ])

                ->if($response = new GuzzleHttp\Psr7\Response(400, [], json_encode($body)))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)

                ->if($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new ild78\Http\Verb\Post)
                ->then
                    ->exception(function () use ($method, $object) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\BadRequestException::class)
                        ->message
                            ->isIdenticalTo($body['error']['message']['error'] . ' (' . $body['error']['message']['id'] . ')')

            ->assert('Same with error.message')
                ->given($body = [
                    'error' => [
                        'message' => uniqid(),
                    ],
                ])

                ->if($response = new GuzzleHttp\Psr7\Response(400, [], json_encode($body)))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))

                ->and($config->setHttpClient($client))

                ->if($this->newTestedInstance)

                ->if($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new ild78\Http\Verb\Post)
                ->then
                    ->exception(function () use ($method, $object) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\BadRequestException::class)
                        ->message
                            ->isIdenticalTo($body['error']['message'])

            ->assert('Every Guzzle exceptions (except the ones below)')
                ->given($content = file_get_contents(__DIR__ . '/../fixtures/auth/not-authorized.json'))
                ->and($exceptionMessage = uniqid())
                ->and($response = new Exception($exceptionMessage))
                ->and($mock = new GuzzleHttp\Handler\MockHandler([$response]))
                ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                ->and($config->setHttpClient($client))

                ->if($object = new mock\ild78\Core\AbstractObject)
                ->and($method = new ild78\Http\Verb\Get)

                ->if($logger = new mock\ild78\Core\Logger)
                ->and($config->setLogger($logger))
                ->and($debugMessage = vsprintf('API call : %s %s', [
                    (string) $method,
                    $config->getUri() . $object->getEndpoint(),
                ]))
                ->and($errorMessage = sprintf('Unknown error : %s', $exceptionMessage))
                ->then
                    ->exception(function () use ($object, $method) {
                        $this->testedInstance->request($method, $object);
                    })
                        ->isInstanceOf(ild78\Exceptions\Exception::class)
                        ->hasNestedException
                        ->message
                            ->isIdenticalTo('Unknown error, may be a network error')

                    ->mock($logger)
                        ->call('debug')->withArguments($debugMessage, [])->once
                        ->call('error')->withArguments($errorMessage)->once
                        ->call('notice')->never
        ;

        $errors = [
            310 => [
                'expected' => ild78\Exceptions\TooManyRedirectsException::class,
                'thrown' => GuzzleHttp\Exception\TooManyRedirectsException::class,
                'message' => 'Too Many Redirection',
                'logLevel' => 'critical',
            ],
            400 => [
                'expected' => ild78\Exceptions\BadRequestException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Bad Request',
                'logLevel' => 'critical',
            ],
            402 => [
                'expected' => ild78\Exceptions\PaymentRequiredException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Payment Required',
                'logLevel' => 'error',
            ],
            403 => [
                'expected' => ild78\Exceptions\ForbiddenException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Forbidden',
                'logLevel' => 'error',
            ],
            404 => [
                'expected' => ild78\Exceptions\NotFoundException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not found',
                'logLevel' => 'error',
            ],
            405 => [
                'expected' => ild78\Exceptions\MethodNotAllowedException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Method Not Allowed',
                'logLevel' => 'critical',
            ],
            406 => [
                'expected' => ild78\Exceptions\NotAcceptableException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Not Acceptable',
                'logLevel' => 'error',
            ],
            407 => [
                'expected' => ild78\Exceptions\ProxyAuthenticationRequiredException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Proxy Authentication Required',
                'logLevel' => 'error',
            ],
            408 => [
                'expected' => ild78\Exceptions\RequestTimeoutException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Request Time-out',
                'logLevel' => 'error',
            ],
            409 => [
                'expected' => ild78\Exceptions\ConflictException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Conflict',
                'logLevel' => 'error',
            ],
            410 => [
                'expected' => ild78\Exceptions\GoneException::class,
                'thrown' => GuzzleHttp\Exception\ClientException::class,
                'message' => 'Gone',
                'logLevel' => 'error',
            ],
            500 => [
                'expected' => ild78\Exceptions\InternalServerErrorException::class,
                'thrown' => GuzzleHttp\Exception\ServerException::class,
                'message' => 'Internal Server Error',
                'logLevel' => 'critical',
            ],
        ];

        foreach ($errors as $code => $infos) {
            $this
                ->assert(sprintf('%d - %s', $code, $infos['message']))
                    ->given($request = new GuzzleHttp\Psr7\Request('GET', $config->getUri()))
                    ->and($response = new GuzzleHttp\Psr7\Response($code))
                    ->and($exception = $infos['thrown'])
                    ->and($mock = new GuzzleHttp\Handler\MockHandler([
                        new $exception($infos['message'], $request, $response),
                    ]))
                    ->and($handler = GuzzleHttp\HandlerStack::create($mock))
                    ->and($client = new GuzzleHttp\Client(['handler' => $handler]))
                    ->and($config->setHttpClient($client))

                    ->if($object = new mock\ild78\Core\AbstractObject)
                    ->and($method = new ild78\Http\Verb\Get)

                    ->if($logger = new mock\ild78\Core\Logger)
                    ->and($config->setLogger($logger))
                    ->and($debugMessage = vsprintf('API call : %s %s', [
                        (string) $method,
                        $object->getUri(),
                    ]))
                    ->and($logMessage = sprintf('HTTP %d - %s', $code, $infos['message']))
                    ->when(function () use ($object, $code, &$logMessage, &$infos) {
                        if ($code === 404) {
                            $tmp = get_class($object);
                            $parts = explode('\\', $tmp);
                            $class = end($parts);

                            $infos['message'] = vsprintf('Ressource "%s" unknown for %s', [
                                $object->getId(),
                                $class,
                            ]);

                            $logMessage .= ' : ' . $infos['message'];
                        }

                        if ($code === 500) {
                            $infos['message'] = 'Servor error, please leave a minute to repair it and try again';
                        }
                    })
                    ->then
                        ->exception(function () use ($object, $method) {
                            $this->testedInstance->request($method, $object);
                        })
                            ->isInstanceOf($infos['expected'])
                            ->hasNestedException
                            ->message
                                ->contains($infos['message'])

                        ->object($this->exception->getRequest())
                            ->isInstanceOf($request)

                        ->object($this->exception->getResponse())
                            ->isInstanceOf($response)

                        ->mock($logger)
                            ->call('debug')->withArguments($debugMessage, [])->once
                            ->call($infos['logLevel'])->withArguments($logMessage)->once
            ;
        }
    }

    public function testVerbProxy()
    {
        // testing a mock is not a good test but here we only want to test we call an other method

        $this
            ->given($request = new mock\ild78\Core\Request)
            ->and($this->calling($request)->request = true)

            ->if($object = new mock\ild78\Core\AbstractObject(uniqid()))

            ->and($delete = new ild78\Http\Verb\Delete)
            ->and($get = new ild78\Http\Verb\Get)
            ->and($post = new ild78\Http\Verb\Post)
            ->and($put = new ild78\Http\Verb\Put)
            ->and($patch = new ild78\Http\Verb\Patch)

            ->then
                ->assert('DELETE')
                    ->if($request->delete($object))
                    ->then
                        ->mock($request)
                            ->call('request')
                                ->withArguments($delete, $object)
                                    ->once

                ->assert('GET')
                    ->if($request->get($object))
                    ->then
                        ->mock($request)
                            ->call('request')
                                ->withArguments($get, $object)
                                    ->once

                ->assert('GET with query parameters')
                    ->if($query = [uniqid() => uniqid()])
                    ->and($request->get($object, $query))
                    ->then
                        ->mock($request)
                            ->call('request')
                                ->withArguments($get, $object, ['query' => $query])
                                    ->once

                ->assert('POST')
                    ->if($request->post($object))
                    ->and($options = [
                        'body' => json_encode($object),
                    ])
                    ->then
                        ->mock($request)
                            ->call('request')
                                ->withArguments($post, $object, $options)
                                    ->once

                ->assert('PUT')
                    ->if($request->put($object))
                    ->and($options = [
                        'body' => json_encode($object),
                    ])
                    ->then
                        ->mock($request)
                            ->call('request')
                                ->withArguments($put, $object, $options)
                                    ->once

                ->assert('PATCH')
                    ->if($request->patch($object))
                    ->and($options = [
                        'body' => json_encode($object),
                    ])
                    ->then
                        ->mock($request)
                            ->call('request')
                                ->withArguments($patch, $object, $options)
                                    ->once

                ->assert('update proxy for PATCH')
                    ->if($request->update($object))
                    ->and($options = [
                        'body' => json_encode($object),
                    ])
                    ->then
                        ->mock($request)
                            ->call('request')
                                ->withArguments($patch, $object, $options)
                                    ->once
        ;
    }
}
