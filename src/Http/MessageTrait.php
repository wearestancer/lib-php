<?php
declare(strict_types=1);

// Next line is required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace ild78\Http;

use ild78;
use Psr;

/**
 * Common HTTP message method
 */
trait MessageTrait
{
    /** @var string */
    protected $body;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $protocol;

    /**
     * Add a value to an header
     *
     * @param string $name Header name.
     * @param string|string[] $value Header value.
     * @return self
     */
    public function addHeader(string $name, $value): self
    {
        $key = strtolower($name);

        if (!array_key_exists($key, $this->headers)) {
            $this->headers[$key] = [
                'name' => $name,
                'values' => [],
            ];
        }

        $this->headers[$key]['values'] = array_merge($this->headers[$key]['values'], (array) $value);

        return $this;
    }

    /**
     * Gets the body of the message.
     *
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name): array
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        $key = strtolower($name);

        return $this->headers[$key]['values'];
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * Retrieves all message header values.
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders(): array
    {
        $headers = [];
        $func = function ($data, $key) use (&$headers) {
            $headers[$data['name']] = $data['values'];
        };

        array_walk($this->headers, $func);

        return $headers;
    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return boolean Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name): bool
    {
        $key = strtolower($name);

        return array_key_exists($key, $this->headers);
    }

    /**
     * Remove header by name
     *
     * @param string $name Header name to remove.
     * @return self
     */
    public function removeHeader($name): self
    {
        $key = strtolower($name);
        unset($this->headers[$key]);

        return $this;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     */
    public function withAddedHeader($name, $value): self
    {
        $obj = clone $this;

        return $obj->addHeader($name, $value);
    }

    /**
     * Return an instance with the specified message body.
     *
     * @param Psr\Http\Message\StreamInterface $body Body.
     * @return self
     */
    public function withBody(Psr\Http\Message\StreamInterface $body): self
    {
        $obj = clone $this;
        $obj->body = (string) $body;

        return $obj;
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     */
    public function withHeader($name, $value): self
    {
        return $this->withoutHeader($name)->addHeader($name, $value);
    }

    /**
     * Return an instance without the specified header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name): self
    {
        $obj = clone $this;

        return $obj->removeHeader($name);
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * @param string $version HTTP protocol version.
     * @return self
     */
    public function withProtocolVersion($version): self
    {
        $obj = clone $this;
        $obj->protocol = $version;

        return $obj;
    }
}
