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

    public function testGetHeader()
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
                ->array($this->testedInstance->getHeader($key))
                    ->strictlyContains($value)

                ->array($this->testedInstance->getHeader(uniqid()))
                    ->isEmpty
        ;
    }

    public function testGetHeaderLine()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($body = uniqid())
            ->and($key = uniqid())
            ->and($value1 = uniqid())
            ->and($value2 = uniqid())
            ->and($headers = [
                $key => [$value1, $value2],
            ])
            ->and($this->newTestedInstance($code, $body, $headers))
            ->then
                ->string($this->testedInstance->getHeaderLine($key))
                    ->contains($value1)
                    ->contains($value2)
                    ->isIdenticalTo($value1 . ', ' . $value2)

                ->string($this->testedInstance->getHeaderLine(uniqid()))
                    ->isEmpty
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

    public function testGetProtocolVersion()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($body = uniqid())
            ->and($headers = [])
            ->and($protocol = uniqid())
            ->then
                ->assert('Defaults')
                    ->if($this->newTestedInstance($code, $body, $headers))
                    ->then
                        ->string($this->testedInstance->getProtocolVersion())
                            ->isIdenticalTo('1.1')

                ->assert('Passed')
                    ->if($this->newTestedInstance($code, $body, $headers, $protocol))
                    ->then
                        ->string($this->testedInstance->getProtocolVersion())
                            ->isIdenticalTo($protocol)
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
