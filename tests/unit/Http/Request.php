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

                    ->string($this->testedInstance->getUri())
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

                    ->string($this->testedInstance->getUri())
                        ->isIdenticalTo($query)

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('With HTTP, host only')
                ->if($host = uniqid())
                ->and($uri = 'http://' . $host)
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->string($this->testedInstance->getUri())
                        ->isIdenticalTo('/')

                    ->array($this->testedInstance->getHeader('host'))
                        ->contains($host)

            ->assert('With HTTPS, host only')
                ->if($host = uniqid())
                ->and($uri = 'https://' . $host)
                ->then
                    ->object($this->testedInstance->updateUri($uri))
                        ->isTestedInstance

                    ->string($this->testedInstance->getUri())
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
