<?php
declare(strict_types=1);

namespace ild78;

use DateTimeZone;
use GuzzleHttp;
use ild78;
use Psr\Log\LoggerInterface;

/**
 * Handle configuration, connection and credential to API
 */
class Config
{
    public const LIVE_MODE = 'live';
    public const TEST_MODE = 'test';
    public const VERSION = '0.0.3';

    /** @var ild78\Core\Request\Call[] */
    protected $calls = [];

    /** @var boolean */
    protected $debug;

    /** @var string */
    protected $host = 'api.iliad78.net';

    /** @var ild78\Http\Client|GuzzleHttp\ClientInterface */
    protected $httpClient;

    /** @var self */
    protected static $instance;

    /** @var array[string]string */
    protected $keys = [
        'pprod' => null,
        'ptest' => null,
        'sprod' => null,
        'stest' => null,
    ];

    /** @var Psr\\Log\\LoggerInterface */
    protected $logger;

    /** @var string */
    protected $mode;

    /** @var integer */
    protected $port;

    /** @var integer */
    protected $timeout = 5;

    /** @var DateTimeZone */
    protected $timezone;

    /** @var integer */
    protected $version = 1;

    /**
     * Create an API configuration
     *
     * An authentication key is required to make a new instance. It will be used on every API call.
     * You needed to set a configuration as global to be used on every API call.
     *
     * @see self::init() for a quick config setup
     * @param string[] $keys Authentication keys.
     * @return self
     */
    public function __construct(array $keys)
    {
        $this->setKeys($keys);
    }

    /**
     * Add a call to the list.
     *
     * @param ild78\Core\Request\Call $call New call to add.
     * @return self
     */
    public function addCall(ild78\Core\Request\Call $call): self
    {
        $this->calls[] = $call;

        return $this;
    }

    /**
     * Return HTTP "basic" authentication header's value
     *
     * @return string
     */
    public function getBasicAuthHeader(): string
    {
        return 'Basic ' . base64_encode($this->getSecretKey() . ':');
    }

    /**
     * Return call list recorded on debug mode.
     *
     * @return ild78\Core\Request\Call[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * Indicate if we are in debug mode.
     *
     * @return boolean
     */
    public function getDebug(): bool
    {
        if (!is_null($this->debug)) {
            return $this->debug;
        }

        return $this->isTestMode();
    }

    /**
     * Return default time zone
     *
     * @return DateTimeZone|null
     */
    public function getDefaultTimeZone(): ?DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * Return default user agent
     *
     * @return string
     */
    public function getDefaultUserAgent(): string
    {
        $params = [
            static::VERSION,
            PHP_OS,
            php_uname('m'),
            php_uname('r'),
            PHP_VERSION,
        ];
        $client = $this->getHttpClient();

        if ($client instanceof ild78\Http\Client) {
            $curl = curl_version();

            array_unshift($params, 'curl/' . $curl['version']);
        }

        if ($client instanceof GuzzleHttp\ClientInterface) {
            array_unshift($params, 'GuzzleHttp/' . GuzzleHttp\Client::VERSION);
        }

        return vsprintf('%s libiliad-php/%s (%s %s %s; php %s)', $params);
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
    public static function getGlobal(): self
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
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Return an instance of HTTP client
     *
     * You can give your instance of `ild78\Http\Client` or `GuzzleHttp\Client`
     * with `Api::setHttpClient()` method.
     * If none provided, we will spawn a default instance for you.
     *
     * @return object
     */
    public function getHttpClient()
    {
        if ($this->httpClient) {
            return $this->httpClient;
        }

        $client = new ild78\Http\Client();

        $this->setHttpClient($client);

        return $client;
    }

    /**
     * Return a valid and PSR3 compatible logger instance
     *
     * @return Psr\\Log\\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger ?: new ild78\Core\Logger();
    }

    /**
     * Return API mode (test or live)
     *
     * Default : test
     *
     * You should use class constant `LIVE_MODE` and `TEST_MODE`.
     *
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode ?: static::TEST_MODE;
    }

    /**
     * Return API port
     *
     * Default : 443 (HTTPS)
     *
     * If defaut port is used, it will not be shown in API URI.
     *
     * @return integer
     */
    public function getPort(): int
    {
        return $this->port ?: 443;
    }

    /**
     * Return public API key
     *
     * @return string
     * @throws ild78\Exceptions\MissingApiKeyException When no key is found.
     */
    public function getPublicKey(): string
    {
        $key = $this->keys['ptest'];
        $type = 'development';

        if ($this->isLiveMode()) {
            $key = $this->keys['pprod'];
            $type = 'production';
        }

        if (!$key) {
            $message = sprintf('You did not provide valid public API key for %s.', $type);

            throw new ild78\Exceptions\MissingApiKeyException($message);
        }

        return $key;
    }

    /**
     * Return secret API key
     *
     * @return string
     * @throws ild78\Exceptions\MissingApiKeyException When no key is found.
     */
    public function getSecretKey(): string
    {
        $key = $this->keys['stest'];
        $type = 'development';

        if ($this->isLiveMode()) {
            $key = $this->keys['sprod'];
            $type = 'production';
        }

        if (!$key) {
            $message = sprintf('You did not provide valid secret API key for %s.', $type);

            throw new ild78\Exceptions\MissingApiKeyException($message);
        }

        return $key;
    }

    /**
     * Return API timeout
     *
     * Default : 5 (seconds)
     *
     * @return integer
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Return API URI
     *
     * Default : 1
     *
     * @return string
     */
    public function getUri(): string
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
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Proxy that create a new instance of configuration and register it as global.
     *
     * @see self::setGlobal()
     * @param string[] $keys Authentication keys.
     * @return self
     */
    public static function init(array $keys): self
    {
        $obj = new static($keys);

        return static::setGlobal($obj);
    }

    /**
     * Indicate if API is in live mode
     *
     * @return boolean
     */
    public function isLiveMode(): bool
    {
        return $this->getMode() === static::LIVE_MODE;
    }

    /**
     * Indicate if API is not in live mode
     *
     * @return boolean
     */
    public function isNotLiveMode(): bool
    {
        return !$this->isLiveMode();
    }

    /**
     * Indicate if API is not in test mode
     *
     * @return boolean
     */
    public function isNotTestMode(): bool
    {
        return !$this->isTestMode();
    }

    /**
     * Indicate if API is in test mode
     *
     * @return boolean
     */
    public function isTestMode(): bool
    {
        return $this->getMode() === static::TEST_MODE;
    }

    /**
     * Change debug mode.
     *
     * @param boolean $value New value for the mode.
     * @return self
     */
    public function setDebug(bool $value): self
    {
        $this->debug = $value;

        return $this;
    }

    /**
     * Register a configuration for deferred API call
     *
     * @param self $instance Current API instance.
     * @return self
     */
    public static function setGlobal(self $instance): self
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
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Update HTTP client instance
     *
     * Be carefull, no limitation is done on this method to allow you to use your own
     * implementation of an HTTP client.
     *
     * @param ild78\Http\Client|GuzzleHttp\ClientInterface $client New instance.
     * @return self
     */
    public function setHttpClient($client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Update authentication keys
     *
     * @param string|string[] $keys One or more keys to update.
     *
     * @return self
     */
    public function setKeys($keys): self
    {
        $k = $keys;

        if (!is_array($keys)) {
            $k = [$keys];
        }

        $prefixes = [
            'pprod',
            'ptest',
            'sprod',
            'stest',
        ];

        foreach ($k as $key) {
            foreach ($prefixes as $prefix) {
                if (preg_match('`^' . $prefix . '_\w{24}$`', $key)) {
                    $this->keys[$prefix] = $key;
                }
            }
        }

        return $this;
    }

    /**
     * Update logger handler
     *
     * @param Psr\Log\LoggerInterface $logger A PSR3 compatible logger.
     * @return self
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

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
    public function setMode(string $mode): self
    {
        $validMode = [
            static::LIVE_MODE,
            static::TEST_MODE,
        ];

        if (!in_array($mode, $validMode, true)) {
            $message = 'Unknown mode "%s". Please use class constant "LIVE_MODE" or "TEST_MODE".';

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
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Update API timeout
     *
     * @param integer $timeout New timeout.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When setting a too high timeout.
     */
    public function setTimeout(int $timeout): self
    {
        $max = 180;

        if ($timeout > $max) {
            $params = [
                $timeout,
                $max,
                $max / 60,
            ];
            $pattern = [
                'Timeout (%ds) is too high,',
                'the maximum allowed is %d seconds (%d minutes, and it\'s already too much).',
            ];

            $message = vsprintf(implode(' ', $pattern), $params);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Update default time zone
     *
     * @param string|DateTimeZone $tz New time zone.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When `$tz` is not a string or a DateTimeZone instance.
     */
    public function setDefaultTimeZone($tz): self
    {
        if (is_string($tz)) {
            $tz = new DateTimeZone($tz);
        }

        if (!($tz instanceof DateTimeZone)) {
            throw new ild78\Exceptions\InvalidArgumentException('Invalid "$tz" argument.');
        }

        $this->timezone = $tz;

        return $this;
    }

    /**
     * Update API version
     *
     * @param integer $version New version.
     * @return self
     */
    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }
}
