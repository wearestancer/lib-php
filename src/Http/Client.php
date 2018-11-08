<?php
declare(strict_types=1);

namespace ild78\Http;

use ild78;
use Psr;

/**
 * Basic HTTP client
 */
class Client implements ild78\Interfaces\HttpClientInterface
{
    /** @var ressource */
    protected $curl;

    /**
     * Creation of a new client instance
     *
     * This only start a new cURL ressource
     */
    public function __construct()
    {
        $this->curl = curl_init();
    }

    /**
     * Close cURL ressource on destruction
     */
    public function __destruct()
    {
        if ($this->curl) {
            curl_close($this->curl);
            $this->curl = null;
        }
    }

    /**
     * Return cURL resource
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
     * Create and send an HTTP request.
     *
     * @param string $method HTTP method.
     * @param string $uri URI string.
     * @param array $options Request options to apply.
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $uri, array $options = []) : Psr\Http\Message\ResponseInterface
    {
        // Set URL.
        curl_setopt($this->curl, CURLOPT_URL, $uri);

        // Set HTTP method.
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);

        // Timeout.
        if (array_key_exists('timeout', $options)) {
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $options['timeout']);
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $options['timeout']);
        }

        // Headers.
        if (array_key_exists('headers', $options)) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $options['headers']);
        }

        // Data.
        if (array_key_exists('body', $options)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $options['body']);
        }

        // `curl_exec` will return the body.
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $body = curl_exec($this->curl);

        $code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        $response = new Response($code, $body);

        return $response;
    }
}
