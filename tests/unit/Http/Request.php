<?php

namespace ild78\Http\tests\unit;

use ild78;
use mock;
use Psr;

class Request extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->implements(Psr\Http\Message\RequestInterface::class)
        ;
    }

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
            ->and($host = uniqid())
            ->and($path = '/' . uniqid())
            ->and($location = 'http://' . $host . $path)
            ->and($uri = new ild78\Http\Uri($location))
            ->then
                ->assert('With a string')
                    ->if($this->newTestedInstance($method, $location))
                    ->then
                        ->object($this->testedInstance->getUri())
                            ->isInstanceOf(ild78\Http\Uri::class)

                        ->castToString($this->testedInstance->getUri())
                            ->isIdenticalTo($path)

                ->assert('With an object')
                    ->if($this->newTestedInstance($method, $uri))
                    ->then
                        ->object($this->testedInstance->getUri())
                            ->isInstanceOf(ild78\Http\Uri::class)
                            ->isNotIdenticalTo($uri)

                        ->castToString($this->testedInstance->getUri())
                            ->isIdenticalTo($path)
        ;
    }

    public function testGetRequestTarget()
    {
        $this
            ->given($method = uniqid())
            ->and($host = uniqid())
            ->and($query = '/' . uniqid())
            ->and($uri = 'http://' . $host . $query)
            ->if($this->newTestedInstance($method, $uri))
            ->then
                ->string($this->testedInstance->getRequestTarget())
                    ->isIdenticalTo($query)
        ;
    }

    public function testUpdateUri()
    {
        $this
            ->given($this->newTestedInstance(uniqid(), uniqid()))

            ->assert('With HTTP')
                ->if($host = uniqid())
                ->and($query = '/' . uniqid())
                ->and($uri = 'http://' . $host . $query)
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->object($this->testedInstance->getUri())
                        ->isInstanceOf(Ild78\Http\Uri::class)

                    ->castToString($this->testedInstance->getUri())
                        ->isIdenticalTo($query)

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('With HTTPS')
                ->if($host = uniqid())
                ->and($query = '/' . uniqid())
                ->and($uri = 'https://' . $host . $query)
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->object($this->testedInstance->getUri())
                        ->isInstanceOf(Ild78\Http\Uri::class)

                    ->castToString($this->testedInstance->getUri())
                        ->isIdenticalTo($query)

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('With HTTP, host only')
                ->if($host = uniqid())
                ->and($uri = 'http://' . $host)
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->object($this->testedInstance->getUri())
                        ->isInstanceOf(Ild78\Http\Uri::class)

                    ->castToString($this->testedInstance->getUri())
                        ->isIdenticalTo('')

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('With HTTPS, host only')
                ->if($host = uniqid())
                ->and($uri = 'https://' . $host)
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->object($this->testedInstance->getUri())
                        ->isInstanceOf(Ild78\Http\Uri::class)

                    ->castToString($this->testedInstance->getUri())
                        ->isIdenticalTo('')

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('With HTTP, host only and trailing slash')
                ->if($host = uniqid())
                ->and($uri = 'http://' . $host . '/')
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->object($this->testedInstance->getUri())
                        ->isInstanceOf(Ild78\Http\Uri::class)

                    ->castToString($this->testedInstance->getUri())
                        ->isIdenticalTo('/')

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('With HTTPS, host only and trailing slash')
                ->if($host = uniqid())
                ->and($uri = 'https://' . $host . '/')
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->object($this->testedInstance->getUri())
                        ->isInstanceOf(Ild78\Http\Uri::class)

                    ->castToString($this->testedInstance->getUri())
                        ->isIdenticalTo('/')

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('Double use will result only one header')
                ->if($host1 = uniqid())
                ->and($uri1 = 'https://' . $host1)
                ->and($host2 = uniqid())
                ->and($uri2 = 'https://' . $host2)
                ->then
                    ->object($this->testedInstance->updateUri($uri1))
                        ->isTestedInstance

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host1)

                    ->object($this->testedInstance->updateUri($uri2))
                        ->isTestedInstance

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host2)
        ;
    }

    public function testWithMethod()
    {
        $this
            ->given($method = uniqid())
            ->and($host = uniqid())
            ->and($query = '/' . uniqid())
            ->and($uri = 'https://' . $host . $query)
            ->and($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
                'Host' => [$host],
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
                ->object($this->testedInstance->getBody())
                    ->isInstanceOf(ild78\Http\Stream::class)
                    ->isIdenticalTo($obj->getBody())

                ->castToString($this->testedInstance->getBody())
                    ->isIdenticalTo($body)

                ->array($this->testedInstance->getHeaders())
                    ->isIdenticalTo($obj->getHeaders())
                    ->isIdenticalTo($headers)
        ;
    }

    public function testWithRequestTarget()
    {
        $this
            ->given($method = uniqid())
            ->and($host = uniqid())
            ->and($query = '/' . uniqid())
            ->and($uri = 'https://' . $host . $query)
            ->and($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
                'Host' => [$host],
            ])
            ->and($protocol = uniqid())
            ->and($this->newTestedInstance($method, $uri, $headers, $body, $protocol))

            ->if($changes = uniqid())
            ->then
                ->object($obj = $this->testedInstance->withRequestTarget($changes))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($obj->getRequestTarget())
                    ->isIdenticalTo($changes)

                ->string($this->testedInstance->getRequestTarget())
                    ->isIdenticalTo($query)

                // Check no diff on other properties
                ->object($this->testedInstance->getBody())
                    ->isInstanceOf(ild78\Http\Stream::class)
                    ->isIdenticalTo($obj->getBody())

                ->castToString($this->testedInstance->getBody())
                    ->isIdenticalTo($body)

                ->array($this->testedInstance->getHeaders())
                    ->isIdenticalTo($obj->getHeaders())
                    ->isIdenticalTo($headers)
        ;
    }

    public function testWithUri()
    {
        $this
            ->if($this->newTestedInstance(uniqid(), uniqid()))
            ->and($mock = new mock\Psr\Http\Message\UriInterface)
            ->then
                ->exception(function () use ($mock) {
                    $this->testedInstance->withUri($mock);
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('This method is not implemented for now')
        ;
    }
}
