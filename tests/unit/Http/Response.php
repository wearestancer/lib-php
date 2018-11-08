<?php

namespace ild78\Http\tests\unit;

use atoum;

class Response extends atoum
{
    public function testGetBody()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($body = uniqid())
            ->and($this->newTestedInstance($code, $body))
            ->then
                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($body)
        ;
    }

    public function testGetHeaders()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => $value,
            ])
            ->and($this->newTestedInstance($code, $body, $headers))
            ->then
                ->array($this->testedInstance->getHeaders())
                    ->hasKey($key)
                    ->child[$key](function ($child) use ($value) {
                        $child
                            ->strictlyContains($value)
                        ;
                    })
        ;
    }

    public function testGetStatusCode()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($this->newTestedInstance($code))
            ->then
                ->integer($this->testedInstance->getStatusCode())
                    ->isIdenticalTo($code)
        ;
    }

    public function testHasHeader()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => $value,
            ])
            ->and($this->newTestedInstance($code, $body, $headers))
            ->then
                ->assert('Present')
                    ->boolean($this->testedInstance->hasHeader($key))
                        ->isTrue

                ->assert('Not present')
                    ->boolean($this->testedInstance->hasHeader(uniqid()))
                        ->isFalse

                ->assert('Case insensitive')
                    ->boolean($this->testedInstance->hasHeader(strtoupper($key)))
                        ->isTrue
        ;
    }
}
