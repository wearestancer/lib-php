<?php

namespace ild78\Interfaces;

use Psr;

/**
 * HTTP client requirement
 */
interface HttpClientInterface
{
    /**
     * Create and send an HTTP request.
     *
     * @param string $method HTTP method.
     * @param string $uri URI string.
     * @param array $options Request options to apply.
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $uri, array $options = []): Psr\Http\Message\ResponseInterface;
}
