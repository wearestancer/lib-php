<?php

namespace ild78\tests\unit\Stub;

use ild78;
use mock;

class Refund extends ild78\Tests\atoum
{
    public function testIsModified_isNotModified()
    {
        $this
            ->given($payment = new ild78\Stub\Payment)
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
                ->given($client = new mock\ild78\Http\Client)
                ->and($response = new mock\ild78\Http\Response(200))
                ->and($this->calling($client)->request = $response)
                ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setHttpClient($client))

                ->and($body = file_get_contents(__DIR__ . '/../fixtures/refund/read.json'))
                ->and($this->calling($response)->getBody = $body)

                ->if($paym = 'paym_' . bin2hex(random_bytes(12)))
                ->and($payment = new ild78\Payment($paym))
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
                ->given($client = new mock\ild78\Http\Client)
                ->and($response = new mock\ild78\Http\Response(200))
                ->and($this->calling($client)->request = $response)
                ->and($config = ild78\Config::init(['stest_' . bin2hex(random_bytes(12))]))
                ->and($config->setHttpClient($client))

                ->and($body = file_get_contents(__DIR__ . '/../fixtures/refund/read.json'))
                ->and($this->calling($response)->getBody = $body)

                ->if($paym = 'paym_' . bin2hex(random_bytes(12)))
                ->and($payment = new ild78\Payment($paym))
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
