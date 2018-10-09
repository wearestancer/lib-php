<?php
declare(strict_types=1);

namespace ild78\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use ild78;

/**
 * Handle configuration, connection and credential to API
 */
class Config
{
    const LIVE_MODE = 'live';
    const TEST_MODE = 'test';

    /** @var string */
    protected $host = 'api.iliad78.net';

    /** @var GuzzleHttp\ClientInterface */
    protected $httpClient;

    /** @var self */
    protected static $instance;

    /** @var string */
    protected $key;

    /** @var string */
    protected $mode;

    /** @var integer */
    protected $port;

    /** @var integer */
    protected $version = 1;

    /**
     * Create an API configuration
     *
     * An authentication key is required to make a new instance. It will be used on every API call.
     * You needed to set a configuration as global to be used on every API call.
     *
     * @see self::init() for a quick config setup
     * @param string $key Authentication key.
     * @return self
     */
    public function __construct(string $key)
    {
        $this->setKey($key);
    }

    /**
     * Return current instance
     *
     * This is used to prevent passing an API instance on everycall.
     * `Api::setGlobal()` is called on every new instance to simplify your workflow.
     *
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When no previous instance was stored (use `Config::init()`).
     */
    public static function getGlobal() : self
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        }

        throw new ild78\Exceptions\InvalidArgumentException('You need to provide API credential.');
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
     * Return an instance of GuzzleHttp\Client
     *
     * We call give your instance of `GuzzleHttp\Client` with `Api::setHttpClient()` method.
     * If none provided, we will spawn an instance for you.
     *
     * @return GuzzleHttp\ClientInterface
     */
    public function getHttpClient() : ClientInterface
    {
        if ($this->httpClient) {
            return $this->httpClient;
        }

        $params = [
            'base_uri' => $this->getUri(),
        ];
        $client = new Client($params);

        $this->setHttpClient($client);

        return $client;
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
     * If defaut port is used, it will not be shown in API URI.
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

        $params = [
            'https',
            $this->getHost(),
            $this->getPort(),
            $this->getVersion(),
        ];

        return vsprintf($pattern, $params);
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
     * Proxy that create a new instance of configuration and register it as global.
     *
     * @see self::setGlobal()
     * @param string $key Authentication key.
     * @return self
     */
    public static function init(string $key) : self
    {
        $obj = new static($key);

        return static::setGlobal($obj);
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
     * Register a configuration for deferred API call
     *
     * @param self $instance Current API instance.
     * @return self
     */
    public static function setGlobal(self $instance) : self
    {
        static::$instance = $instance;

        return $instance;
    }

    /**
     * Update API host
     *
     * @param string $host New host.
     * @return self
     */
    public function setHost(string $host) : self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Update GuzzleHttp\Client instance
     *
     * @param GuzzleHttp\ClientInterface $client New instance.
     * @return self
     */
    public function setHttpClient(ClientInterface $client) : self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Update API key
     *
     * @param string $key New key.
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
     * @param string $mode New mode. Should be class constant `LIVE_MODE` or `TEST_MODE`.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException If new mode is not valid.
     */
    public function setMode(string $mode) : self
    {
        $validMode = [
            static::LIVE_MODE,
            static::TEST_MODE,
        ];

        if (!in_array($mode, $validMode, true)) {
            $message = 'Unknonw mode "%s". Please use class constant "LIVE_MODE" or "TEST_MODE".';

            throw new ild78\Exceptions\InvalidArgumentException(sprintf($message, $mode));
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * Update API port
     *
     * @param integer $port New port.
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
     * @param integer $version New version.
     * @return self
     */
    public function setVersion(int $version) : self
    {
        $this->version = $version;

        return $this;
    }
}
