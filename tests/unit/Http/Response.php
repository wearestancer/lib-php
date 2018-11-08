<?php

namespace ild78\Http\tests\unit;

use atoum;

class Response extends atoum
{
    public function httpStatusDataProvider()
    {
        return [
            [100, 'Continue'],
            [101, 'Switching Protocols'],
            [102, 'Processing'],
            [103, 'Early Hints'],
            [200, 'OK'],
            [201, 'Created'],
            [202, 'Accepted'],
            [203, 'Non-Authoritative Information'],
            [204, 'No Content'],
            [205, 'Reset Content'],
            [206, 'Partial Content'],
            [207, 'Multi-Status'],
            [208, 'Already Reported'],
            [226, 'IM Used'],
            [300, 'Multiple Choices'],
            [301, 'Moved Permanently'],
            [302, 'Found'],
            [303, 'See Other'],
            [304, 'Not Modified'],
            [305, 'Use Proxy'],
            [306, 'Switch Proxy'],
            [307, 'Temporary Redirect'],
            [308, 'Permanent Redirect'],
            [400, 'Bad Request'],
            [401, 'Unauthorized'],
            [402, 'Payment Required'],
            [403, 'Forbidden'],
            [404, 'Not Found'],
            [405, 'Method Not Allowed'],
            [406, 'Not Acceptable'],
            [407, 'Proxy Authentication Required'],
            [408, 'Request Timeout'],
            [409, 'Conflict'],
            [410, 'Gone'],
            [411, 'Length Required'],
            [412, 'Precondition Failed'],
            [413, 'Payload Too Large'],
            [414, 'URI Too Long'],
            [415, 'Unsupported Media Type'],
            [416, 'Range Not Satisfiable'],
            [417, 'Expectation Failed'],
            [418, 'I\'m a teapot'],
            [421, 'Misdirected Request'],
            [422, 'Unprocessable Entity'],
            [423, 'Locked'],
            [424, 'Failed Dependency'],
            [426, 'Upgrade Required'],
            [428, 'Precondition Required'],
            [429, 'Too Many Requests'],
            [431, 'Request Header Fields Too Large'],
            [451, 'Unavailable For Legal Reasons'],
            [500, 'Internal Server Error'],
            [501, 'Not Implemented'],
            [502, 'Bad Gateway'],
            [503, 'Service Unavailable'],
            [504, 'Gateway Timeout'],
            [505, 'HTTP Version Not Supported'],
            [506, 'Variant Also Negotiates'],
            [507, 'Insufficient Storage'],
            [508, 'Loop Detected'],
            [510, 'Not Extended'],
            [511, 'Network Authentication Required'],
        ];
    }

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

    public function testWithAddedHeader()
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
                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($obj->getStatusCode())
                        ->isIdenticalTo($code)

                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($obj->getReasonPhrase())
                        ->isIdenticalTo($reason)

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
                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($obj->getStatusCode())
                        ->isIdenticalTo($code)

                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($obj->getReasonPhrase())
                        ->isIdenticalTo($reason)
        ;
    }

    public function testWithBody()
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

            ->if($changes = uniqid())
            ->then
                ->object($obj = $this->testedInstance->withBody($changes))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($obj->getBody())
                    ->isIdenticalTo($changes)

                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($body)

                // Check no diff on other properties
                ->integer($this->testedInstance->getStatusCode())
                    ->isIdenticalTo($obj->getStatusCode())
                    ->isIdenticalTo($code)

                ->array($this->testedInstance->getHeaders())
                    ->isIdenticalTo($obj->getHeaders())
                    ->isIdenticalTo($headers)

                ->string($this->testedInstance->getProtocolVersion())
                    ->isIdenticalTo($obj->getProtocolVersion())
                    ->isIdenticalTo($protocol)

                ->string($this->testedInstance->getReasonPhrase())
                    ->isIdenticalTo($obj->getReasonPhrase())
                    ->isIdenticalTo($reason)
        ;
    }

    public function testWithHeader()
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
                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($obj->getStatusCode())
                        ->isIdenticalTo($code)

                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($obj->getReasonPhrase())
                        ->isIdenticalTo($reason)

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
                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($obj->getStatusCode())
                        ->isIdenticalTo($code)

                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($obj->getReasonPhrase())
                        ->isIdenticalTo($reason)
        ;
    }

    public function testWithoutHeader()
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
                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($obj->getStatusCode())
                        ->isIdenticalTo($code)

                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
                        ->isIdenticalTo($body)

                    ->string($this->testedInstance->getProtocolVersion())
                        ->isIdenticalTo($obj->getProtocolVersion())
                        ->isIdenticalTo($protocol)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($obj->getReasonPhrase())
                        ->isIdenticalTo($reason)

                ->assert('With unknown header')
                    ->object($obj = $this->testedInstance->withoutHeader(uniqid()))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    // Check no diff on other properties
                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($obj->getStatusCode())
                        ->isIdenticalTo($code)

                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
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
        ;
    }

    public function testWithProtocolVersion()
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
                ->integer($this->testedInstance->getStatusCode())
                    ->isIdenticalTo($obj->getStatusCode())
                    ->isIdenticalTo($code)

                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($obj->getBody())
                    ->isIdenticalTo($body)

                ->array($this->testedInstance->getHeaders())
                    ->isIdenticalTo($obj->getHeaders())
                    ->isIdenticalTo($headers)

                ->string($this->testedInstance->getReasonPhrase())
                    ->isIdenticalTo($obj->getReasonPhrase())
                    ->isIdenticalTo($reason)
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
                    ->string($this->testedInstance->getBody())
                        ->isIdenticalTo($obj->getBody())
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
}
