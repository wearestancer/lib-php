<?php

namespace Stancer\tests\unit\Stub;

use Stancer;
use mock;

class Refund extends Stancer\Tests\atoum
{
    public function testIsModified_isNotModified()
    {
        $this
            ->given($payment = new Stancer\Stub\Payment)
            ->and($payment->testOnlyAddModified('amount'))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setPayment($payment))
            ->and($this->testedInstance->testOnlyResetModified())
            ->then
                ->boolean($this->testedInstance->isModified())
                    ->isFalse

                ->boolean($this->testedInstance->isNotModified())
                    ->isTrue
        ;
    }

    public function testSend()
    {
        $this
            ->assert('With amount')
                ->given($client = new mock\Stancer\Http\Client)
                ->and($response = new mock\Stancer\Http\Response(200))
                ->and($this->calling($client)->request = $response)
                ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setHttpClient($client))
                ->and($config->setDebug(false))

                ->and($this->calling($response)->getBody[] = new Stancer\Http\Stream('{}'))
                ->and($body = file_get_contents(__DIR__ . '/../fixtures/refund/read.json'))
                ->and($this->calling($response)->getBody[] = new Stancer\Http\Stream($body))

                ->if($paym = 'paym_' . bin2hex(random_bytes(12)))
                ->and($payment = new Stancer\Payment($paym))
                ->and($payment->setCurrency('eur'))

                ->if($amount = rand(50, 99999))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setAmount($amount))
                ->and($this->testedInstance->testOnlySetPayment($payment))

                ->and($location = $this->testedInstance->getUri())

                ->if($options = [])
                ->and($options['headers'] = [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ])
                ->and($options['timeout'] = $config->getTimeout())
                ->and($options['body'] = json_encode([
                    'amount' => $amount,
                    'payment' => $paym,
                ]))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $location, $options)
                                ->once

                    ->object($this->testedInstance->getPayment())
                        ->isIdenticalTo($payment)

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

                    // $payment is not modified, not more than before
                    ->array($payment->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('currency')
                        ->string['currency']
                            ->isIdenticalTo('eur')

            ->assert('Without amount')
                ->given($client = new mock\Stancer\Http\Client)
                ->and($response = new mock\Stancer\Http\Response(200))
                ->and($this->calling($client)->request = $response)
                ->and($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setHttpClient($client))
                ->and($config->setDebug(false))

                ->and($this->calling($response)->getBody[] = new Stancer\Http\Stream('{}'))
                ->and($body = file_get_contents(__DIR__ . '/../fixtures/refund/read.json'))
                ->and($this->calling($response)->getBody[] = new Stancer\Http\Stream($body))

                ->if($paym = 'paym_' . bin2hex(random_bytes(12)))
                ->and($payment = new Stancer\Payment($paym))
                ->and($payment->setCurrency('eur'))

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->testOnlySetPayment($payment))

                ->and($location = $this->testedInstance->getUri())

                ->if($options = [])
                ->and($options['headers'] = [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ])
                ->and($options['timeout'] = $config->getTimeout())
                ->and($options['body'] = json_encode([
                    'payment' => $paym,
                ]))

                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $location, $options)
                                ->once

                    ->object($this->testedInstance->getPayment())
                        ->isIdenticalTo($payment)

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse

                    // $payment is not modified, not more than before
                    ->array($payment->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('currency')
                        ->string['currency']
                            ->isIdenticalTo('eur')
        ;
    }
}
