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
    }
}
