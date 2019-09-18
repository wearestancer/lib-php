<?php

namespace ild78\Http\tests\unit;

use ild78;
use Psr;

class Response extends ild78\Tests\atoum
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

            // Unknown status will result empty message.
            [999, ''],
        ];
    }

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
