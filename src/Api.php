<?php
declare(strict_types=1);

namespace ild78;

/**
 * Handle configuration, connection and credential to API
 */
class Api
{
    /** @var string */
    protected $host = 'api.iliad78.net';

    /** @var integer */
    protected $port;

    /** @var integer */
    protected $version = 1;

    /**
     * Return API host
     *
     * Default : api.iliad78.net
     *
     * @return string
     */
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * Return API port
     *
     * Default : 443 (HTTPS)
     *
     * If defaut port is used, it will not be shown in API URI
     *
     * @return string
     */
    public function getPort() : int
    {
        return $this->port ?: 443;
    }

    /**
     * Return API version
     *
     * Default : 1
     *
     * @return string
     */
    public function getVersion() : int
    {
        return $this->version;
    }

    /**
     * Update API host
     *
     * @param string $host New host
     * @return self
     */
    public function setHost(string $host) : self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Update API port
     *
     * @param integer $port New port
     * @return self
     */
    public function setPort(int $port) : self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Update API version
     *
     * @param integer $version New version
     * @return self
     */
    public function setVersion(int $version) : self
    {
        $this->version = $version;

        return $this;
    }
}
