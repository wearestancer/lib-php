<?php
declare(strict_types=1);

namespace ild78\Http;

use ild78;

/**
 * Basic HTTP response
 */
class Response
{
    /** @var string */
    protected $body;

    /** @var integer */
    protected $code;

    /** @var array */
    protected $headers;

    /**
     * Create a response instance
     *
     * @param integer $code Status code.
     * @param string $body Response body.
     * @param array $headers Response headers.
     * @param string $version Protocol version.
     * @param string|null $reason  Reason phrase (when empty a default will be used based on the status code).
     */
    public function __construct(
        int $code,
        string $body = null,
        array $headers = [],
        string $version = '1.1',
        $reason = null
    ) {
        $this->code = $code;
        $this->body = $body;

        foreach ($headers as $name => $value) {
            $this->headers[$name] = (array) $value;
        }
    }

    /**
     * Gets the body of the message.
     *
     * @return string
     */
    public function getBody() : string
    {
        return $this->body;
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
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return integer Status code.
     */
    public function getStatusCode() : int
    {
        return $this->code;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return boolean Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader(string $name) : bool
    {
        $keys = array_keys($this->headers);
        $keys = array_map('strtolower', $keys);

        return in_array(strtolower($name), $keys, true);
    }
}
