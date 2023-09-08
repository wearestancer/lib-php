<?php
declare(strict_types=1);

namespace Stancer\Http;

use Stancer;
use Psr;

/**
 * Basic HTTP client.
 */
class Client implements Stancer\Interfaces\HttpClientInterface
{
    /** @var resource */
    protected $curl;

    /**
     * @var mixed[]
     * @phpstan-var array<string, string[]>
     */
    protected $headers = [];

    /** @var Stancer\Http\Request|null */
    protected $lastRequest;

    /** @var Stancer\Http\Response|null */
    protected $lastResponse;

    /**
     * Creation of a new client instance.
     *
     * This only start a new cURL ressource.
     */
    public function __construct()
    {
        $this->curl = curl_init();
    }

    /**
     * Close cURL ressource on destruction.
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Return cURL resource.
     *
     * This is mainly use for testing purpose. Be carefull if you need to use it.
     *
     * @return resource
     */
    public function getCurlResource()
    {
        return $this->curl;
    }

    /**
     * Return the last response.
     *
     * @return Stancer\Http\Response|null
     */
    public function getLastResponse(): ?Stancer\Http\Response
    {
        return $this->lastResponse;
    }

    /**
     * Return the last request.
     *
     * @return Stancer\Http\Request|null
     */
    public function getLastRequest(): ?Stancer\Http\Request
    {
        return $this->lastRequest;
    }

    /**
     * Return parsed response header.
     *
     * @return mixed[]
     *
     * @phpstan-return array<string, string[]>
     */
    public function getResponseHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Parse response header line to pass it to `Response` object.
     *
     * As written in documentation "Return the number of bytes written.".
     *
     * @param resource $curl Actual cURL resource (not used but mandatory).
     * @param string $line One header line.
     * @return integer
     */
    public function parseHeaderLine($curl, string $line): int
    {
        if (!trim($line)) {
            return strlen($line);
        }

        $name = 'Status-Line';
        $value = $line;

        if (strpos($line, ':') !== false) {
            list($name, $value) = explode(':', $line, 2);
        }

        if (!array_key_exists($name, $this->headers)) {
            $this->headers[$name] = [];
        }

        if ($name === 'Date') {
            $values = [$value];
        } else {
            $values = explode(',', $value);
        }

        $this->headers[$name] = array_merge($this->headers[$name], array_map('trim', $values));

        return strlen($line);
    }

    /**
     * Create and send an HTTP request.
     *
     * @param string $method HTTP method.
     * @param string $uri URI string.
     * @param mixed[] $options Request options to apply.
     *
     * @return Psr\Http\Message\ResponseInterface
     *
     * @throws Stancer\Exceptions\HttpException On cURL error.
     * @throws Stancer\Exceptions\TooManyRedirectsException On 310 HTTP error.
     * @throws Stancer\Exceptions\RedirectionException On over 300 level HTTP error.
     * @throws Stancer\Exceptions\BadRequestException On 400 HTTP error.
     * @throws Stancer\Exceptions\NotAuthorizedException On 401 HTTP error.
     * @throws Stancer\Exceptions\NotFoundException On 404 HTTP error.
     * @throws Stancer\Exceptions\ConflictException On 409 HTTP error.
     * @throws Stancer\Exceptions\ClientException On over 400 level HTTP error.
     * @throws Stancer\Exceptions\ServerException On over 500 level HTTP error.
     *
     * @phpstan-param array{body?: string, headers?: array<string, string|string[]>, timeout?: int} $options
     *   Request options to apply.
     */
    public function request(string $method, string $uri, array $options = []): Psr\Http\Message\ResponseInterface
    {
        $config = Stancer\Config::getGlobal();
        $logger = $config->getLogger();

        // Set URL.
        curl_setopt($this->curl, CURLOPT_URL, trim($uri));

        // Set HTTP method.
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, trim($method));

        // Timeout.
        if (array_key_exists('timeout', $options)) {
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $options['timeout']);
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $options['timeout']);
        }

        // Headers.
        if (array_key_exists('headers', $options)) {
            $headers = [];

            foreach ($options['headers'] as $key => $value) {
                if (is_array($value)) {
                    $headers[] = $key . ': ' . implode(', ', $value);
                } else {
                    $headers[] = $key . ': ' . $value;
                }
            }

            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        } else {
            $options['headers'] = [];
        }

        // Data.
        if (array_key_exists('body', $options)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $options['body']);
        } else {
            $options['body'] = null;
        }

        // `curl_exec` will return the body.
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        // Get response headers.
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'parseHeaderLine']);

        // Add user agent.
        curl_setopt($this->curl, CURLOPT_USERAGENT, $config->getDefaultUserAgent());

        $body = curl_exec($this->curl);
        $error = curl_errno($this->curl);
        $code = curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE);

        if (is_bool($body)) {
            $body = null;
        }

        $this->lastRequest = new Request($method, $uri, $options['headers'], $options['body']);
        $this->lastResponse = new Response($code, $body, $this->getResponseHeaders());

        if ($error || $code >= 400) {
            if ($error === CURLE_TOO_MANY_REDIRECTS) {
                $code = 310;
            }

            if ($error === CURLE_OPERATION_TIMEDOUT) {
                $code = 408;
            }

            $class = Stancer\Exceptions\HttpException::getClassFromStatus($code);

            $params = [
                'code' => $code,
                'request' => $this->lastRequest,
                'response' => $this->lastResponse,
            ];

            if (intval($class::getStatus()) !== $code) {
                $params['message'] = curl_error($this->curl);
                $params['status'] = $code;
            }

            $body = $this->lastResponse->getBody();

            if ($body->getSize()) {
                $json = json_decode((string) $body, true);

                if (
                    json_last_error() === JSON_ERROR_NONE
                    && is_array($json)
                    && array_key_exists('error', $json)
                    && array_key_exists('message', $json['error'])
                    && $json['error']['message']
                ) {
                    $params['message'] = $json['error']['message'];

                    if (is_array($json['error']['message'])) {
                        $params['message'] = current($json['error']['message']);
                        $id = '';

                        if (array_key_exists('id', $json['error']['message'])) {
                            $id = $json['error']['message']['id'];
                            $params['message'] = $json['error']['message']['id'];
                        }

                        if (array_key_exists('error', $json['error']['message'])) {
                            $params['message'] = $json['error']['message']['error'];

                            if ($id) {
                                $params['message'] .= ' (' . $id . ')';
                            }
                        }
                    }
                }
            }

            $logMethod = $class::getLogLevel();
            $logMessage = null;

            switch ($code) {
                case 401:
                    $logMessage = 'HTTP 401 - Invalid credential: ' . $config->getSecretKey();
                    break;

                case 404:
                    $logMessage = 'HTTP 404 - Not Found';
                    break;

                case 500:
                    $logMessage = 'HTTP 500 - Internal Server Error';
                    break;

                default:
                    $logMessage = $params['message'] ?? $class::getDefaultMessage();
                    break;
            }

            $logger->$logMethod($logMessage);

            throw $class::create($params);
        }

        return $this->lastResponse;
    }
}
