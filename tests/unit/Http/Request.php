<?php

namespace ild78\Http\tests\unit;

use atoum;
use Psr;

class Request extends atoum
{
    public function testGetMethod()
    {
        $this
            ->given($method = uniqid())
            ->and($uri = uniqid())
            ->if($this->newTestedInstance($method, $uri))
            ->then
                ->string($this->testedInstance->getMethod())
                    ->isIdenticalTo(strtoupper($method))
        ;
    }

    public function testGetUri()
    {
        $this
            ->given($method = uniqid())
            ->and($uri = uniqid())
            ->if($this->newTestedInstance($method, $uri))
            ->then
                ->string($this->testedInstance->getUri())
                    ->isIdenticalTo($uri)
        ;
    }

    public function testWithMethod()
    {
        $this
            ->given($method = uniqid())
            ->and($uri = uniqid())
            ->and($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
            ])
            ->and($protocol = uniqid())
            ->and($this->newTestedInstance($method, $uri, $headers, $body, $protocol))

            ->if($changes = uniqid())
            ->then
                ->object($obj = $this->testedInstance->withMethod($changes))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($obj->getMethod())
                    ->isIdenticalTo(strtoupper($changes))

                ->string($this->testedInstance->getMethod())
                    ->isIdenticalTo(strtoupper($method))

                // Check no diff on other properties
                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($obj->getBody())
                    ->isIdenticalTo($body)

                ->array($this->testedInstance->getHeaders())
                    ->isIdenticalTo($obj->getHeaders())
                    ->isIdenticalTo($headers)
        ;
    }
}
