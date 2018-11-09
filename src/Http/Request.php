<?php
declare(strict_types=1);

// Next line is required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace ild78\Http;

use ild78;
use Psr;

/**
 * Basic HTTP request
 */
class Request implements Psr\Http\Message\RequestInterface
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
        $this->body = $body;
        $this->protocol = $version;

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
    public function getMethod() : string
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
    public function getRequestTarget() : string
    {
        return $this->getUri();
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

    /**
     * Return an instance with the specific request-target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget New target.
     * @return self
     */
    public function withRequestTarget($requestTarget) : self
    {
        $obj = clone $this;

        $obj->uri = $requestTarget;

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
     * @return void
     * @throws ild78\Exceptions\BadMethodCallException For everycall.
     */
    public function withUri(Psr\Http\Message\UriInterface $uri, $preserveHost = false) : self
    {
        $message = 'This method is not implemented for now';

        throw new ild78\Exceptions\BadMethodCallException($message);
    }
}
