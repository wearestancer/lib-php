<?php
declare(strict_types=1);

// Next line is required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace ild78\Http;

use ild78;

/**
 * Basic HTTP request
 */
class Request
{
    use MessageTrait;

    /** @var string */
    protected $method;

    /** @var string */
    protected $uri;

    /**
     * Create a response instance
     *
     * @param string $method HTTP method.
     * @param string $uri URI.
     * @param array $headers Request headers.
     * @param string|null $body Request body.
     * @param string $version Protocol version.
     */
    public function __construct(
        string $method,
        string $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->body = $body;
        $this->protocol = $version;

        foreach ($headers as $name => $value) {
            $this->headers[$name] = (array) $value;
        }
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod() : string
    {
        return $this->method;
    }
}
