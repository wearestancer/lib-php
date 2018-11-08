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

    public function testWithBody()
    {
        $this
            ->given($code = rand(100, 600))
            ->and($before = uniqid())
            ->and($after = uniqid())
            ->and($this->newTestedInstance($code, $before))
            ->then
                ->object($obj = $this->testedInstance->withBody($after))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($obj->getBody())
                    ->isIdenticalTo($after)

                ->string($this->testedInstance->getBody())
                    ->isIdenticalTo($before)
        ;
    }

    public function testWithStatus()
    {
        $this
            ->assert('Without changing reason')
                ->given($before = rand(100, 600))
                ->and($after = rand(100, 600))
                ->and($reason = uniqid())
                ->and($this->newTestedInstance($before, '', [], '', $reason))
                ->then
                    ->object($obj = $this->testedInstance->withStatus($after))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->integer($obj->getStatusCode())
                        ->isIdenticalTo($after)

                    ->string($obj->getReasonPhrase())
                        ->isIdenticalTo($reason)

                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($before)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($reason)

            ->assert('Without new reason')
                ->given($codeBefore = rand(100, 600))
                ->and($codeAfter = rand(100, 600))
                ->and($reasonBefore = uniqid())
                ->and($reasonAfter = uniqid())
                ->and($this->newTestedInstance($codeBefore, '', [], '', $reasonBefore))
                ->then
                    ->object($obj = $this->testedInstance->withStatus($codeAfter, $reasonAfter))
                        ->isInstanceOfTestedClass
                        ->isNotTestedInstance

                    ->integer($obj->getStatusCode())
                        ->isIdenticalTo($codeAfter)

                    ->string($obj->getReasonPhrase())
                        ->isIdenticalTo($reasonAfter)

                    ->integer($this->testedInstance->getStatusCode())
                        ->isIdenticalTo($codeBefore)

                    ->string($this->testedInstance->getReasonPhrase())
                        ->isIdenticalTo($reasonBefore)
        ;
    }
}
