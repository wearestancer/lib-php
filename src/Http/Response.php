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
}
