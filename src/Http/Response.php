<?php
declare(strict_types=1);

// Next line is required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace Stancer\Http;

use Psr;
use Stancer;

/**
 * Basic HTTP response.
 *
 * @method static with_body(Psr\Http\Message\StreamInterface $body) Return an instance with the specified message body.
 * @method static with_status(int $code, string $reason_phrase = null) Return an instance with the specified
 *   status code and, optionally, reason phrase.
 * @method static without_header(string $name) Return an instance without the specified header.
 * @method static with_header(string $name, $value) Return an instance with the provided value replacing the
 *   specified header.
 * @method static with_modified_body($in, $out) Return an instance with obfuscated message body.
 * @method static with_protocol_version(string $version) Return an instance with the specified HTTP protocol version.
 *
 * @method Psr\Http\Message\StreamInterface get_body() Gets the body of the message.
 * @method array<mixed> get_header(string $name) Retrieves a message header value by the given case-insensitive name.
 * @method string get_header_line(string $name) Retrieves a comma-separated string of the values for a single header.
 * @method array<mixed> get_headers() Retrieves all message header values.
 * @method string get_protocol_version() Retrieves the HTTP protocol version as a string.
 * @method string get_reason_phrase() Gets the response reason phrase associated with the status code.
 * @method int get_status_code() Gets the response status code.
 * @method boolean has_header(string $name) Checks if a header exists by the given case-insensitive name.
 */
class Response implements Psr\Http\Message\ResponseInterface
{
    use Stancer\Traits\AliasTrait;
    use MessageTrait;

    /** @var array<int, string> HTTP status list */
    protected array $status = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Create a response instance.
     *
     * @param integer $code Status code.
     * @param Psr\Http\Message\StreamInterface|string|null $body Response body.
     * @param array<string, string|string[]> $headers Response headers.
     * @param string $protocol Protocol version.
     * @param string|null $reason Reason phrase (when empty a default will be used based on the status code).
     */
    public function __construct(
        protected int $code,
        Psr\Http\Message\StreamInterface|string|null $body = null,
        array $headers = [],
        protected string $protocol = '1.1',
        protected ?string $reason = null
    ) {
        if ($body instanceof Psr\Http\Message\StreamInterface) {
            $this->body = $body;
        } else {
            $this->body = new Stream($body ?? '');
        }

        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase(): string
    {
        if ($this->reason) {
            return $this->reason;
        }

        $code = $this->getStatusCode();

        if (array_key_exists($code, $this->status)) {
            return $this->status[$code];
        }

        return '';
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return integer Status code.
     */
    public function getStatusCode(): int
    {
        return $this->code;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param integer $code The 3-digit integer result code to set.
     * @param string|null $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     */
    public function withStatus(int $code, ?string $reasonPhrase = null): static
    {
        $obj = clone $this;
        $obj->code = $code;

        if ($reasonPhrase) {
            $obj->reason = $reasonPhrase;
        }

        return $obj;
    }
}
