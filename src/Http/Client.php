<?php
declare(strict_types=1);

namespace ild78\Http;

/**
 * Basic HTTP client
 */
class Client
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
}
