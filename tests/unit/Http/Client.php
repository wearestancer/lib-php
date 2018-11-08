<?php

namespace ild78\Http\tests\unit;

use atoum;
use ild78;

class Client extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->implements(ild78\Interfaces\HttpClientInterface::class)
        ;
    }

    public function test__construct__destruct()
    {
        $this
            ->given($ressource = uniqid())
            ->and($this->function->curl_init = $ressource)
            ->and($this->function->curl_close = true)
            ->then
                ->object($this->newTestedInstance)
                ->function('curl_init')->wasCalled->once
                ->function('curl_close')->wasCalled->never

                ->variable($this->testedInstance->__destruct())
                ->function('curl_close')
                    ->wasCalledWithArguments($ressource)
                        ->once
        ;
    }

    public function testGetCurlResource()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->resource($this->testedInstance->getCurlResource())
                    ->isOfType('curl')
        ;
    }

    public function testRequest()
    {
        $this
            ->assert('Basic request')
                ->given($this->newTestedInstance)
                ->and($curl = $this->testedInstance->getCurlResource())
                ->if($this->function->curl_setopt = true)
                ->and($this->function->curl_exec = $body = uniqid())
                ->and($this->function->curl_errno = 0)
                ->and($this->function->curl_error = '')
                ->and($method = 'GET')
                ->and($host = uniqid())
                ->then
                    ->object($response = $this->testedInstance->request($method, $host))
                        ->isInstanceOf(ild78\Http\Response::class)

                    ->string($response->getBody())
                        ->isIdenticalTo($body)

                    ->function('curl_setopt')
                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_URL, $host)
                            ->once

                        ->wasCalledWithIdenticalArguments($curl, CURLOPT_CUSTOMREQUEST, $method)
                            ->once

                        ->wasCalledWithArguments($curl, CURLOPT_CONNECTTIMEOUT)
                            ->never

                        ->wasCalledWithArguments($curl, CURLOPT_TIMEOUT)
                            ->never

                        ->wasCalledWithArguments($curl, CURLOPT_HTTPHEADER)
                            ->never

                    ->function('curl_exec')
                        ->wasCalled
                            ->once
        ;
    }
}
