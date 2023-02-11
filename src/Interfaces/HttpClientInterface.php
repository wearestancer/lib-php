<?php

namespace Stancer\Interfaces;

use Psr;

/**
 * HTTP client requirement.
 */
interface HttpClientInterface
{
    /**
     * Create and send an HTTP request.
     *
     * @param string $method HTTP method.
     * @param string $uri URI string.
     * @param mixed[] $options Request options to apply.
     *
     * @return Psr\Http\Message\ResponseInterface
     *
     * @phpstan-param array{body?: string, headers?: array<string, string|string[]>, timeout?: int} $options
     *   Request options to apply.
     */
    public function request(string $method, string $uri, array $options = []): Psr\Http\Message\ResponseInterface;
}
