<?php
declare(strict_types=1);

// Next lines are required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace Stancer\Http;

use Stancer;
use Psr;

/**
 * Basic HTTP request.
 */
class Request implements Psr\Http\Message\RequestInterface
{
    use MessageTrait;

    /** @var string */
    protected $method;

    /** @var Psr\Http\Message\UriInterface */
    protected $uri;

    /**
     * Create a response instance.
     *
     * @param Stancer\Http\Verb\AbstractVerb|string $method HTTP method.
     * @param Psr\Http\Message\UriInterface|string $uri URI.
     * @param mixed[] $headers Request headers.
     * @param Psr\Http\Message\StreamInterface|string|mixed[]|null $body Request body.
     * @param string $version Protocol version.
     *
     * @phpstan-param array<string, string | string[]> $headers
     */
    public function __construct(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ) {
        $this->method = strtoupper((string) $method);
        $this->protocol = $version;

        if ($body instanceof Psr\Http\Message\StreamInterface) {
            $this->body = $body;
        } elseif (is_array($body)) {
            $this->body = new Stream('Unsupported multipart form data');
        } else {
            $this->body = new Stream($body ?? '');
        }

        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }

        $this->updateUri($uri);
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        $uri = $this->getUri();
        $fragment = $uri->getFragment();
        $query = $uri->getQuery();

        $target = $uri->getPath();

        if ($query) {
            $target .= '?' . $query;
        }

        if ($fragment) {
            $target .= '#' . $fragment;
        }

        return $target;
    }

    /**
     * Retrieves the URI instance.
     *
     * We will not implement the real interface as we will not return an UriInterface.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return Psr\Http\Message\UriInterface Returns the URI of the request.
     */
    public function getUri(): Psr\Http\Message\UriInterface
    {
        return $this->uri;
    }

    /**
     * Update URI and host header.
     *
     * @param Psr\Http\Message\UriInterface|string $uri New URI.
     * @return $this
     */
    public function updateUri($uri): self
    {
        if (is_string($uri)) {
            $this->uri = new Uri($uri);
        } else {
            $this->uri = $uri;
        }

        $host = $this->uri->getHost();

        if ($host) {
            $this->removeHeader('Host')->addHeader('Host', $host);
        }

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
     * @return static
     */
    public function withMethod($method): self
    {
        $obj = clone $this;
        $obj->method = strtoupper($method);

        return $obj;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget New target.
     * @return static
     */
    public function withRequestTarget($requestTarget): self
    {
        // phpcs:disable PEAR.Functions.FunctionCallSignature.SpaceAfterCloseBracket
        // phpcs:disable Squiz.WhiteSpace.ObjectOperatorSpacing.Before
        // phpcs:disable Squiz.WhiteSpace.SemicolonSpacing.Incorrect

        /** @phpstan-var string|UriComponents $requestTarget */
        $parse = new Uri($requestTarget);
        $obj = clone $this;

        $obj->uri = $this->uri
            ->withPath($parse->getPath())
            ->withQuery($parse->getQuery())
            ->withFragment($parse->getFragment())
        ;

        return $obj;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param Psr\Http\Message\UriInterface $uri New request URI to use.
     * @param boolean $preserveHost Preserve the original state of the Host header.
     * @return static
     * @throws Stancer\Exceptions\BadMethodCallException For every call.
     */
    public function withUri(Psr\Http\Message\UriInterface $uri, $preserveHost = false): self
    {
        $message = 'This method is not implemented for now';

        throw new Stancer\Exceptions\BadMethodCallException($message);
    }
}
