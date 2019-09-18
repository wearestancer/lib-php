<?php

namespace ild78\tests\unit\Stub\Http;

use ild78;
use mock;

class Message extends ild78\Tests\atoum
{
    public function testAddHeader()
    {
        $this
            ->given($body = uniqid())
            ->and($this->newTestedInstance($body))
            ->and($name = uniqid())
            ->and($value = uniqid())
            ->then
                ->object($this->testedInstance->addHeader($name, $value))
                    ->isTestedInstance

                ->array($this->testedInstance->getHeader($name))
                    ->contains($value)
        ;
    }

    public function testGetBody()
    {
        $this
            ->given($body = uniqid())
            ->and($this->newTestedInstance($body))
            ->then
                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($body)
        ;
    }

    public function testGetHeader()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => $value,
            ])
            ->and($this->newTestedInstance($body, $headers))
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
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value1 = uniqid())
            ->and($value2 = uniqid())
            ->and($headers = [
                $key => [$value1, $value2],
            ])
            ->and($this->newTestedInstance($body, $headers))
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
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => $value,
            ])
            ->and($this->newTestedInstance($body, $headers))
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
            ->given($body = uniqid())
            ->and($headers = [])
            ->and($protocol = uniqid())
            ->then
                ->assert('Defaults')
                    ->if($this->newTestedInstance($body, $headers))
                    ->then
                        ->string($this->testedInstance->getProtocolVersion())
                            ->isIdenticalTo('1.1')

                ->assert('Passed')
                    ->if($this->newTestedInstance($body, $headers, $protocol))
                    ->then
                        ->string($this->testedInstance->getProtocolVersion())
                            ->isIdenticalTo($protocol)
        ;
    }

    public function testHasHeader()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => $value,
            ])
            ->and($this->newTestedInstance($body, $headers))
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

    public function testRemoveHeader()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => $value,
            ])
            ->and($this->newTestedInstance($body, $headers))
            ->then
                ->array($this->testedInstance->getHeaders())
                    ->isNotEmpty

                ->object($this->testedInstance->removeHeader($key))
                    ->isTestedInstance

                ->array($this->testedInstance->getHeaders())
                    ->isEmpty
        ;
    }

    public function testWithAddedHeader()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
            ])
            ->and($protocol = uniqid())
            ->and($this->newTestedInstance($body, $headers, $protocol))

            ->if($changes = [uniqid()])
            ->and($newKey = uniqid())
            ->then
                ->assert('With known header')
                    ->object($obj = $this->testedInstance->withAddedHeader($key, $changes))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->array($obj->getHeader($key))
                        ->isIdenticalTo(array_merge((array) $value, $changes))

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($headers)

                    // Check no diff on other properties
                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                ->assert('With unknown header')
                    ->object($obj = $this->testedInstance->withAddedHeader($newKey, $changes))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->array($obj->getHeaders())
                        ->hasKey($key)
                        ->hasKey($newKey)

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($headers)

                    // Check no diff on other properties
                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)
        ;
    }

    public function testWithBody()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
            ])
            ->and($protocol = uniqid())
            ->and($this->newTestedInstance($body, $headers, $protocol))

            ->if($changes = new mock\Psr\Http\Message\StreamInterface)
            ->and($this->calling($changes)->__toString = $newBody = uniqid())
            ->then
                ->object($obj = $this->testedInstance->withBody($changes))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($obj->getBody())
                    ->isIdenticalTo($newBody)

                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($body)

                // Check no diff on other properties
                ->array($this->testedInstance->getHeaders())
                    ->isIdenticalTo($obj->getHeaders())
                    ->isIdenticalTo($headers)

                ->string($this->testedInstance->getProtocolVersion())
                    ->isIdenticalTo($obj->getProtocolVersion())
                    ->isIdenticalTo($protocol)
        ;
    }

    public function testWithHeader()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
            ])
            ->and($protocol = uniqid())
            ->and($this->newTestedInstance($body, $headers, $protocol))

            ->if($changes = [uniqid()])
            ->and($newKey = uniqid())
            ->then
                ->assert('With known header')
                    ->object($obj = $this->testedInstance->withHeader($key, $changes))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->array($obj->getHeader($key))
                        ->isIdenticalTo($changes)

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($headers)

                    // Check no diff on other properties
                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                ->assert('With unknown header')
                    ->object($obj = $this->testedInstance->withHeader($newKey, $changes))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->array($obj->getHeaders())
                        ->hasKey($key)
                        ->hasKey($newKey)

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($headers)

                    // Check no diff on other properties
                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)
        ;
    }

    public function testWithoutHeader()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
            ])
            ->and($protocol = uniqid())
            ->and($this->newTestedInstance($body, $headers, $protocol))

            ->then
                ->assert('With known header')
                    ->object($obj = $this->testedInstance->withoutHeader($key))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->array($obj->getHeaders())
                        ->isEmpty

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($headers)

                    // Check no diff on other properties
                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                ->assert('With unknown header')
                    ->object($obj = $this->testedInstance->withoutHeader(uniqid()))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    // Check no diff on other properties
                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($obj->getHeaders())
                        ->isIdenticalTo($headers)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)
        ;
    }

    public function testWithProtocolVersion()
    {
        $this
            ->given($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
            ])
            ->and($protocol = uniqid())
            ->and($this->newTestedInstance($body, $headers, $protocol))

            ->if($changes = uniqid())
            ->then
                ->object($obj = $this->testedInstance->withProtocolVersion($changes))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($obj->getProtocolVersion())
                    ->isIdenticalTo($changes)

                ->string($this->testedInstance->getProtocolVersion())
                    ->isIdenticalTo($protocol)

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
