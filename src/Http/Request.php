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
            $this->addHeader($name, $value);
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

    /**
     * Retrieves the URI instance.
     *
     * We will not implement the real interface as we will not return an UriInterface.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return string Returns the URI of the request.
     */
    public function getUri() : string
    {
        return $this->uri;
    }

    /**
     * Update URI and host header
     *
     * @param string $uri New URI.
     * @return self
     */
    public function updateUri(string $uri) : self
    {
        $matches = [];
        $name = 'Host';

        preg_match('!https?://([^/]+)(.*)!', $uri, $matches);

        $this->removeHeader($name)->addHeader($name, $matches[1]);
        $this->uri = $matches[2] ?: '/';

        return $this;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return self
     */
    public function withMethod($method) : self
    {
        $obj = clone $this;
        $obj->method = strtoupper($method);

        return $obj;
    }
}
