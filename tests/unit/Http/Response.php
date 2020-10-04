<?php

namespace ild78\Http\tests\unit;

use ild78;
use Psr;

class Response extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Http;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->implements(Psr\Http\Message\ResponseInterface::class)
        ;
    }

    /**
     * @dataProvider httpStatusDataProvider
     */
    public function testGetReasonPhrase($code, $message)
    {
        $this
            ->assert('Default reason')
                ->given($this->newTestedInstance($code))
                ->then
                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($message)

            ->assert('Custom reason')
                ->given($reason = uniqid())
                ->and($this->newTestedInstance($code, uniqid(), [], uniqid(), $reason))
                ->then
                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($reason)
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

    public function testWithStatus()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($body = uniqid())
            ->and($key = uniqid())
            ->and($value = uniqid())
            ->and($headers = [
                $key => [$value],
            ])
            ->and($protocol = uniqid())
            ->and($reason = uniqid())
            ->and($this->newTestedInstance($code, $body, $headers, $protocol, $reason))

            ->assert('Without changing reason')
                ->if($changes = rand(100, 600))
                ->then
                    ->object($obj = $this->testedInstance->withStatus($changes))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->integer($obj->getStatusCode())
                        ->isIdenticalTo($changes)

                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($code)

                    // Check no diff on other properties
                    ->object($this->testedInstance->getBody())
                        ->isInstanceOf(ild78\Http\Stream::class)
                        ->isIdenticalTo($obj->getBody())

                    ->castToString($this->testedInstance->getBody())
                        ->isIdenticalTo($body)

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($obj->getHeaders())
                        ->isIdenticalTo($headers)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($obj->getReasonPhrase())
                        ->isIdenticalTo($reason)

            ->assert('Without new reason')
                ->if($newCode = rand(100, 600))
                ->and($newReason = uniqid())
                ->then
                    ->object($obj = $this->testedInstance->withStatus($newCode, $newReason))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->integer($obj->getStatusCode())
                        ->isIdenticalTo($newCode)

                    ->string($obj->getReasonPhrase())
                        ->isIdenticalTo($newReason)

                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($code)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($reason)

                    // Check no diff on other properties
                    ->object($this->testedInstance->getBody())
                        ->isInstanceOf(ild78\Http\Stream::class)
                        ->isIdenticalTo($obj->getBody())

                    ->castToString($this->testedInstance->getBody())
                        ->isIdenticalTo($body)

                    ->array($this->testedInstance->getHeaders())
                        ->isIdenticalTo($obj->getHeaders())
                        ->isIdenticalTo($headers)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)
        ;
    }
}
