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
}
