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
 *
 * @method static with_body(Psr\Http\Message\StreamInterface $body) Return an instance with the specified message body.
 * @method static with_method(string $method) Return an instance with the provided HTTP method.
 * @method static without_header(string $name) Return an instance without the specified header.
 * @method static with_header(string $name, $value) Return an instance with the provided value replacing the
 *   specified header.
 * @method static with_modified_body($in, $out) Return an instance with obfuscated message body.
 * @method static with_request_target(mixed $request_target) Return an instance with the specific request-target.
 * @method static with_uri(Psr\Http\Message\UriInterface $uri, boolean $preserve_host = false) Returns an
 *   instance with the provided URI.
 * @method static with_protocol_version(string $version) Return an instance with the specified HTTP protocol version.
 *
 * @method static update_uri($uri) Update URI and host header.
 * @method Psr\Http\Message\StreamInterface get_body() Gets the body of the message.
 * @method array<mixed> get_header(string $name) Retrieves a message header value by the given case-insensitive name.
 * @method string get_header_line(string $name) Retrieves a comma-separated string of the values for a single header.
 * @method array<mixed> get_headers() Retrieves all message header values.
 * @method string get_method() Retrieves the HTTP method of the request.
 * @method string get_protocol_version() Retrieves the HTTP protocol version as a string.
 * @method string get_request_target() Retrieves the message's request target.
 * @method Psr\Http\Message\UriInterface get_uri() Retrieves the URI instance.
 * @method boolean has_header(string $name) Checks if a header exists by the given case-insensitive name.
 */
class Request implements Psr\Http\Message\RequestInterface
{
    use Stancer\Traits\AliasTrait;
    use MessageTrait;

    protected string $method;

    protected Psr\Http\Message\UriInterface $uri;

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
        Stancer\Http\Verb\AbstractVerb|string $method,
        Psr\Http\Message\UriInterface|string $uri,
        array $headers = [],
        Psr\Http\Message\StreamInterface|string|array|null $body = null,
        string $version = '1.1'
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
    public function updateUri(Psr\Http\Message\UriInterface|string $uri): static
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
    public function withMethod(string $method): static
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
    public function withRequestTarget(mixed $requestTarget): static
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
            ->withFragment($parse->getFragment());

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
    public function withUri(Psr\Http\Message\UriInterface $uri, bool $preserveHost = false): static
    {
        $message = 'This method is not implemented for now';

        throw new Stancer\Exceptions\BadMethodCallException($message);
    }
}
