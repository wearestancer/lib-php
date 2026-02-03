<?php

namespace Stancer\Tests\Provider;

use Stancer;

trait Http
{
    public function httpStatusDataProvider()
    {
        $data = [];

        $data[] = [100, 'Continue'];
        $data[] = [101, 'Switching Protocols'];
        $data[] = [102, 'Processing'];
        $data[] = [103, 'Early Hints'];

        $data[] = [200, 'OK'];
        $data[] = [201, 'Created'];
        $data[] = [202, 'Accepted'];
        $data[] = [203, 'Non-Authoritative Information'];
        $data[] = [204, 'No Content'];
        $data[] = [205, 'Reset Content'];
        $data[] = [206, 'Partial Content'];
        $data[] = [207, 'Multi-Status'];
        $data[] = [208, 'Already Reported'];
        $data[] = [226, 'IM Used'];

        $data[] = [300, 'Multiple Choices'];
        $data[] = [301, 'Moved Permanently'];
        $data[] = [302, 'Found'];
        $data[] = [303, 'See Other'];
        $data[] = [304, 'Not Modified'];
        $data[] = [305, 'Use Proxy'];
        $data[] = [306, 'Switch Proxy'];
        $data[] = [307, 'Temporary Redirect'];
        $data[] = [308, 'Permanent Redirect'];

        $data[] = [400, 'Bad Request', Stancer\Exceptions\BadRequestException::class];
        $data[] = [401, 'Unauthorized', Stancer\Exceptions\NotAuthorizedException::class];
        $data[] = [402, 'Payment Required', Stancer\Exceptions\PaymentRequiredException::class];
        $data[] = [403, 'Forbidden', Stancer\Exceptions\ForbiddenException::class];
        $data[] = [404, 'Not Found', Stancer\Exceptions\NotFoundException::class];
        $data[] = [405, 'Method Not Allowed', Stancer\Exceptions\MethodNotAllowedException::class];
        $data[] = [406, 'Not Acceptable', Stancer\Exceptions\NotAcceptableException::class];
        $data[] = [407, 'Proxy Authentication Required', Stancer\Exceptions\ProxyAuthenticationRequiredException::class];
        $data[] = [408, 'Request Timeout', Stancer\Exceptions\RequestTimeoutException::class];
        $data[] = [409, 'Conflict', Stancer\Exceptions\ConflictException::class];
        $data[] = [410, 'Gone', Stancer\Exceptions\GoneException::class];
        $data[] = [411, 'Length Required'];
        $data[] = [412, 'Precondition Failed'];
        $data[] = [413, 'Payload Too Large'];
        $data[] = [414, 'URI Too Long'];
        $data[] = [415, 'Unsupported Media Type'];
        $data[] = [416, 'Range Not Satisfiable'];
        $data[] = [417, 'Expectation Failed'];
        $data[] = [418, 'I\'m a teapot'];
        $data[] = [421, 'Misdirected Request'];
        $data[] = [422, 'Unprocessable Entity'];
        $data[] = [423, 'Locked'];
        $data[] = [424, 'Failed Dependency'];
        $data[] = [426, 'Upgrade Required'];
        $data[] = [428, 'Precondition Required'];
        $data[] = [429, 'Too Many Requests'];
        $data[] = [431, 'Request Header Fields Too Large'];
        $data[] = [451, 'Unavailable For Legal Reasons'];

        $data[] = [500, 'Internal Server Error', Stancer\Exceptions\InternalServerErrorException::class];
        $data[] = [501, 'Not Implemented'];
        $data[] = [502, 'Bad Gateway'];
        $data[] = [503, 'Service Unavailable'];
        $data[] = [504, 'Gateway Timeout'];
        $data[] = [505, 'HTTP Version Not Supported'];
        $data[] = [506, 'Variant Also Negotiates'];
        $data[] = [507, 'Insufficient Storage'];
        $data[] = [508, 'Loop Detected'];
        $data[] = [510, 'Not Extended'];
        $data[] = [511, 'Network Authentication Required'];

        // Unknown status will result empty message.
        $data[] = [999, '', Stancer\Exceptions\HttpException::class];

        shuffle($data);

        return $data;
    }

    public function httpVerbProvider($one = false)
    {
        $data = [];
        $data[] = new Stancer\Http\Verb\Delete();
        $data[] = new Stancer\Http\Verb\Get();
        $data[] = new Stancer\Http\Verb\Head();
        $data[] = new Stancer\Http\Verb\Options();
        $data[] = new Stancer\Http\Verb\Patch();
        $data[] = new Stancer\Http\Verb\Post();
        $data[] = new Stancer\Http\Verb\Put();

        shuffle($data);

        if ($one) {
            return $data[0];
        }

        return $data;
    }

    public function verbAndUrlProvider()
    {
        $verbs = $this->httpVerbProvider();
        $urls = array_map(function ($url) {
            return new Stancer\Http\Uri($url[0]);
        }, array_slice($this->urlProvider(), 0, 3));

        foreach ($verbs as $verb) {
            foreach ($urls as $url) {
                yield [$verb, $url];

                yield [(string) $verb, $url];

                yield [$verb, (string) $url];

                yield [(string) $verb, (string) $url];
            }
        }
    }

    public function statusDataProvider()
    {
        $data = [];

        foreach ($this->httpStatusDataProvider() as $value) {
            if (!empty($value[2])) {
                $data[] = [
                    $value[0],
                    $value[2],
                ];
            }
        }

        $data[] = [310, Stancer\Exceptions\TooManyRedirectsException::class];

        // Levels
        $data[] = [399, Stancer\Exceptions\RedirectionException::class];
        $data[] = [499, Stancer\Exceptions\ClientException::class];
        $data[] = [599, Stancer\Exceptions\ServerException::class];

        shuffle($data);

        return $data;
    }

    public function urlProvider($one = false): array
    {
        $data = [];

        // Full

        $data[] = [
            'https://user:pass@example.com:8080/path/123?q=abc#test',
            'https',
            'example.com',
            8080,
            'user',
            'pass',
            '/path/123',
            'q=abc',
            'test',
            'https://user:pass@example.com:8080/path/123?q=abc#test',
        ];

        $data[] = [
            'http://user:pass@example.com:8080/path/123?q=abc#test',
            'http',
            'example.com',
            8080,
            'user',
            'pass',
            '/path/123',
            'q=abc',
            'test',
            'http://user:pass@example.com:8080/path/123?q=abc#test',
        ];

        $data[] = [
            '//user:pass@example.com:8080/path/123?q=abc#test',
            '',
            'example.com',
            8080,
            'user',
            'pass',
            '/path/123',
            'q=abc',
            'test',
            '//user:pass@example.com:8080/path/123?q=abc#test',
        ];

        // Default port / multiple queries

        $data[] = [
            'https://user:pass@example.com:443/path/123?foo=123&bar=456',
            'https',
            'example.com',
            null,
            'user',
            'pass',
            '/path/123',
            'foo=123&bar=456',
            '',
            'https://user:pass@example.com/path/123?foo=123&bar=456',
        ];

        $data[] = [
            'http://user:pass@example.com:80/path/123?foo=123&bar=456',
            'http',
            'example.com',
            null,
            'user',
            'pass',
            '/path/123',
            'foo=123&bar=456',
            '',
            'http://user:pass@example.com/path/123?foo=123&bar=456',
        ];

        // No port / no passwd

        $data[] = [
            'https://user@example.com/path/123?foo=bar#test',
            'https',
            'example.com',
            null,
            'user',
            '',
            '/path/123',
            'foo=bar',
            'test',
            'https://user@example.com/path/123?foo=bar#test',
        ];

        $data[] = [
            'http://user@example.com/path/123?foo=bar#test',
            'http',
            'example.com',
            null,
            'user',
            '',
            '/path/123',
            'foo=bar',
            'test',
            'http://user@example.com/path/123?foo=bar#test',
        ];

        $data[] = [
            '//user@example.com/path/123?foo=bar#test',
            '',
            'example.com',
            null,
            'user',
            '',
            '/path/123',
            'foo=bar',
            'test',
            '//user@example.com/path/123?foo=bar#test',
        ];

        // No hash / no identity / multiple queries

        $data[] = [
            'https://example.com/path/123?foo=bar',
            'https',
            'example.com',
            null,
            '',
            '',
            '/path/123',
            'foo=bar',
            '',
            'https://example.com/path/123?foo=bar',
        ];

        $data[] = [
            'http://example.com/path/123?foo=bar',
            'http',
            'example.com',
            null,
            '',
            '',
            '/path/123',
            'foo=bar',
            '',
            'http://example.com/path/123?foo=bar',
        ];

        $data[] = [
            '//example.com/path/123?foo=bar',
            '',
            'example.com',
            null,
            '',
            '',
            '/path/123',
            'foo=bar',
            '',
            '//example.com/path/123?foo=bar',
        ];

        // No hash / no identity

        $data[] = [
            'https://example.com/path/123?foo=bar',
            'https',
            'example.com',
            null,
            '',
            '',
            '/path/123',
            'foo=bar',
            '',
            'https://example.com/path/123?foo=bar',
        ];

        $data[] = [
            'http://example.com/path/123?foo=bar',
            'http',
            'example.com',
            null,
            '',
            '',
            '/path/123',
            'foo=bar',
            '',
            'http://example.com/path/123?foo=bar',
        ];

        $data[] = [
            '//example.com/path/123?foo=bar',
            '',
            'example.com',
            null,
            '',
            '',
            '/path/123',
            'foo=bar',
            '',
            '//example.com/path/123?foo=bar',
        ];

        // No path / no query

        $data[] = [
            'https://example.com/',
            'https',
            'example.com',
            null,
            '',
            '',
            '/',
            '',
            '',
            'https://example.com/',
        ];

        $data[] = [
            'http://example.com/',
            'http',
            'example.com',
            null,
            '',
            '',
            '/',
            '',
            '',
            'http://example.com/',
        ];

        $data[] = [
            '//example.com/',
            '',
            'example.com',
            null,
            '',
            '',
            '/',
            '',
            '',
            '//example.com/',
        ];

        $data[] = [
            'https://example.com',
            'https',
            'example.com',
            null,
            '',
            '',
            '',
            '',
            '',
            'https://example.com',
        ];

        $data[] = [
            'http://example.com',
            'http',
            'example.com',
            null,
            '',
            '',
            '',
            '',
            '',
            'http://example.com',
        ];

        $data[] = [
            '//example.com',
            '',
            'example.com',
            null,
            '',
            '',
            '',
            '',
            '',
            '//example.com',
        ];

        // No host

        $data[] = [
            '/foo/bar',
            '',
            '',
            null,
            '',
            '',
            '/foo/bar',
            '',
            '',
            '/foo/bar',
        ];

        $data[] = [
            'foo/bar/',
            '',
            '',
            null,
            '',
            '',
            'foo/bar/',
            '',
            '',
            'foo/bar/',
        ];

        $data[] = [
            '?foo=bar',
            '',
            '',
            null,
            '',
            '',
            '',
            'foo=bar',
            '',
            '?foo=bar',
        ];

        $data[] = [
            '#123',
            '',
            '',
            null,
            '',
            '',
            '',
            '',
            '123',
            '#123',
        ];

        // Case

        $data[] = [
            'HTTPS://eXAMple.com',
            'https',
            'example.com',
            null,
            '',
            '',
            '',
            '',
            '',
            'https://example.com',
        ];

        $data[] = [
            'hTTp://examPLE.com',
            'http',
            'example.com',
            null,
            '',
            '',
            '',
            '',
            '',
            'http://example.com',
        ];

        $data[] = [
            '//ExaMplE.COM',
            '',
            'example.com',
            null,
            '',
            '',
            '',
            '',
            '',
            '//example.com',
        ];

        // Others

        $data[] = [
            'file:///home/user/file.txt',
            'file',
            '',
            null,
            '',
            '',
            '/home/user/file.txt',
            '',
            '',
            'file:///home/user/file.txt',
        ];

        $data[] = [
            '',
            '',
            '',
            null,
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        shuffle($data);

        if ($one) {
            return $data[0];
        }

        return $data;
    }
}
