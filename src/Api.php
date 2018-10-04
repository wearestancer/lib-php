<?php
declare(strict_types=1);

namespace ild78;

use ild78\Exceptions\InvalidArgumentException;

/**
 * Handle configuration, connection and credential to API
 */
class Api
{
    const LIVE_MODE = 'live';
    const TEST_MODE = 'test';

    /** @var self */
    protected static $instance;

    /** @var string */
    protected $host = 'api.iliad78.net';

    /** @var string */
    protected $key;

    /** @var string */
    protected $mode;

    /** @var integer */
    protected $port;

    /** @var integer */
    protected $version = 1;

    /**
     * Create an API connection
     *
     * An authentication key is required to make a new instance. It will be used on every API call.
     *
     * @param string $key Authentication key
     * @return self
     */
    public function __construct(string $key)
    {
        $this->setKey($key);
        static::setInstance($this);
    }

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
     * Return current instance
     *
     * This is used to prevent passing an API instance on everycall.
     * `Api::setInstance()` is called on every new instance to simplify your workflow.
     *
     * @return self
     * @throws InvalidArgumentException When no previous instance was stored (just do a `new Api($key)`)
     */
    public static function getInstance() : self
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        }

        throw new InvalidArgumentException('You need to provide API credential.');
    }

    /**
     * Return API key
     *
     * Default : ''
     *
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * Return API mode (test or live)
     *
     * Default : live
     *
     * You should use class constant `LIVE_MODE` and `TEST_MODE`.
     *
     * @return string
     */
    public function getMode() : string
    {
        return $this->mode ?: static::LIVE_MODE;
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
     * Return API URI
     *
     * Default : 1
     *
     * @return string
     */
    public function getUri() : string
    {
        $pattern = '%1$s://%2$s/v%4$s/';

        if ($this->port) {
            $pattern = '%1$s://%2$s:%3$d/v%4$s/';
        }

        return vsprintf($pattern, [
            'https',
            $this->getHost(),
            $this->getPort(),
            $this->getVersion(),
        ]);
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
     * Indicate if API is in live mode
     *
     * @return boolean
     */
    public function isLiveMode() : bool
    {
        return $this->getMode() === static::LIVE_MODE;
    }

    /**
     * Indicate if API is not in live mode
     *
     * @return boolean
     */
    public function isNotLiveMode() : bool
    {
        return !$this->isLiveMode();
    }

    /**
     * Indicate if API is not in test mode
     *
     * @return boolean
     */
    public function isNotTestMode() : bool
    {
        return !$this->isTestMode();
    }

    /**
     * Indicate if API is in test mode
     *
     * @return boolean
     */
    public function isTestMode() : bool
    {
        return $this->getMode() === static::TEST_MODE;
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
     * Register the current API instance for deferred API call
     *
     * @param self $instance Current API instance
     * @return self
     */
    public static function setInstance(self $instance) : self
    {
        static::$instance = $instance;

        return $instance;
    }

    /**
     * Update API key
     *
     * @param string $key New key
     * @return self
     */
    public function setKey(string $key) : self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Update API mode
     *
     * You should use class constant `LIVE_MODE` and `TEST_MODE` to be sure
     *
     * @param string $mode New mode. Should be class constant `LIVE_MODE` or `TEST_MODE`
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException If new mode is not valid
     */
    public function setMode(string $mode) : self
    {
        $validMode = [
            static::LIVE_MODE,
            static::TEST_MODE,
        ];

        if (!in_array($mode, $validMode, true)) {
            $message = 'Unknonw mode "%s". Please use class constant "LIVE_MODE" or "TEST_MODE".';

            throw new Exceptions\InvalidArgumentException(sprintf($message, $mode));
        }

        $this->mode = $mode;

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
