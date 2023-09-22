<?php
declare(strict_types=1);

namespace Stancer;

use DateTimeZone;
use GuzzleHttp;
use Stancer;
use Psr;

/**
 * Handle configuration, connection and credential to API.
 */
class Config
{
    public const LIVE_MODE = 'live';
    public const TEST_MODE = 'test';
    public const VERSION = '1.1.1';

    /** @var non-empty-array<string|null>[] */
    protected $app = [];

    /** @var Stancer\Core\Request\Call[] */
    protected $calls = [];

    /** @var boolean|null */
    protected $debug;

    /** @var string */
    protected $host = 'api.stancer.com';

    /** @var Stancer\Http\Client|GuzzleHttp\ClientInterface|null */
    protected $httpClient;

    /** @var static|null */
    protected static $instance;

    /** @var array<string, string|null> */
    protected $keys = [
        'pprod' => null,
        'ptest' => null,
        'sprod' => null,
        'stest' => null,
    ];

    /** @var Psr\Log\LoggerInterface|null */
    protected $logger;

    /** @var string */
    protected $mode;

    /** @var integer */
    protected $port;

    /** @var integer */
    protected $timeout = 0;

    /** @var DateTimeZone|null */
    protected $timezone;

    /** @var integer */
    protected $version = 1;

    /**
     * Create an API configuration.
     *
     * An authentication key is required to make a new instance. It will be used on every API call.
     * You needed to set a configuration as global to be used on every API call.
     *
     * @see self::init() for a quick config setup
     * @param string[] $keys Authentication keys.
     */
    public function __construct(array $keys)
    {
        $this->setKeys($keys);
    }

    /**
     * Define application data (name and version).
     *
     * @param string $name Application name.
     * @param string $version Application version.
     * @return self
     */
    public function addAppData(string $name, string $version = null): self
    {
        $this->app[] = [
            $name,
            $version,
        ];

        return $this;
    }

    /**
     * Add a call to the list.
     *
     * @param Stancer\Core\Request\Call $call New call to add.
     * @return $this
     */
    public function addCall(Stancer\Core\Request\Call $call): self
    {
        $this->calls[] = $call;

        return $this;
    }

    /**
     * Return HTTP "basic" authentication header's value.
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
     * @return Stancer\Core\Request\Call[]
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
     * Return default time zone.
     *
     * @return DateTimeZone|null
     */
    public function getDefaultTimeZone(): ?DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * Return default user agent.
     *
     * @return string
     */
    public function getDefaultUserAgent(): string
    {
        $params = [];
        $client = $this->getHttpClient();

        if ($client instanceof Stancer\Http\Client) {
            $curl = curl_version();
            $version = 'unknown-version';

            if ($curl) {
                $version = $curl['version'];
            }

            $params[] = 'curl/' . $version;
        }

        if ($client instanceof GuzzleHttp\ClientInterface) {
            $params[] = 'GuzzleHttp';
        }

        $params[] = 'libstancer-php/' . static::VERSION;

        foreach ($this->app as $app) {
            if (!$app[1]) {
                $params[] = $app[0];
            } else {
                $params[] = join('/', $app);
            }
        }

        $params[] = sprintf('(%s %s %s; php %s)', PHP_OS, php_uname('m'), php_uname('r'), PHP_VERSION);

        return join(' ', $params);
    }

    /**
     * Return current instance.
     *
     * This is used to prevent passing an API instance on every call.
     * `Api::setGlobal()` is called on every new instance to simplify your workflow.
     *
     * @return static
     * @throws Stancer\Exceptions\InvalidArgumentException When no previous instance was stored (use `Config::init()`).
     */
    public static function getGlobal(): self
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        }

        throw new Stancer\Exceptions\InvalidArgumentException('You need to provide API credential.');
    }

    /**
     * Return API host.
     *
     * Default : api.stancer.com
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Return an instance of HTTP client.
     *
     * You can give your instance of `Stancer\Http\Client` or `GuzzleHttp\ClientInterface`
     * with `Api::setHttpClient()` method.
     * If none provided, we will spawn a default instance for you.
     *
     * @return Stancer\Http\Client|GuzzleHttp\ClientInterface
     */
    public function getHttpClient()
    {
        if ($this->httpClient) {
            return $this->httpClient;
        }

        $client = new Stancer\Http\Client();

        $this->setHttpClient($client);

        return $client;
    }

    /**
     * Return a valid and PSR3 compatible logger instance.
     *
     * @return Psr\Log\LoggerInterface
     */
    public function getLogger(): Psr\Log\LoggerInterface
    {
        if ($this->logger) {
            return $this->logger;
        }

        $logger = new Stancer\Core\Logger();

        $this->setLogger($logger);

        return $logger;
    }

    /**
     * Return API mode (test or live).
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
     * Return API port.
     *
     * Default : 443 (HTTPS)
     *
     * If default port is used, it will not be shown in API URI.
     *
     * @return integer
     */
    public function getPort(): int
    {
        return $this->port ?: 443;
    }

    /**
     * Return public API key.
     *
     * @return string
     * @throws Stancer\Exceptions\MissingApiKeyException When no key is found.
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

            throw new Stancer\Exceptions\MissingApiKeyException($message);
        }

        return $key;
    }

    /**
     * Return secret API key.
     *
     * @return string
     * @throws Stancer\Exceptions\MissingApiKeyException When no key is found.
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

            throw new Stancer\Exceptions\MissingApiKeyException($message);
        }

        return $key;
    }

    /**
     * Return API timeout.
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
     * Return API URI.
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
     * Return API version.
     *
     * Default : 1
     *
     * @return integer
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
     * Indicate if API is in live mode.
     *
     * @return boolean
     */
    public function isLiveMode(): bool
    {
        return $this->getMode() === static::LIVE_MODE;
    }

    /**
     * Indicate if API is not in live mode.
     *
     * @return boolean
     */
    public function isNotLiveMode(): bool
    {
        return !$this->isLiveMode();
    }

    /**
     * Indicate if API is not in test mode.
     *
     * @return boolean
     */
    public function isNotTestMode(): bool
    {
        return !$this->isTestMode();
    }

    /**
     * Indicate if API is in test mode.
     *
     * @return boolean
     */
    public function isTestMode(): bool
    {
        return $this->getMode() === static::TEST_MODE;
    }

    /**
     * Reset app data.
     *
     * @return $this
     */
    public function resetAppData(): self
    {
        $this->app = [];

        return $this;
    }

    /**
     * Reset default time zone.
     *
     * @return $this
     */
    public function resetDefaultTimeZone(): self
    {
        $this->timezone = null;

        return $this;
    }

    /**
     * Change debug mode.
     *
     * @param boolean $value New value for the mode.
     * @return $this
     */
    public function setDebug(bool $value): self
    {
        $this->debug = $value;

        return $this;
    }

    /**
     * Update default time zone.
     *
     * @param string|DateTimeZone $tz New time zone.
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When `$tz` is not a string or a DateTimeZone instance.
     */
    public function setDefaultTimeZone($tz): self
    {
        $message = 'Invalid time zone.';
        $zone = $tz;

        if (is_string($tz)) {
            $message = sprintf('Invalid time zone "%s".', $tz);

            try {
                $zone = new DateTimeZone($tz);
            } catch (\Exception $exception) {
                $code = (int) $exception->getCode();

                throw new Stancer\Exceptions\InvalidArgumentException($message, $code, $exception);
            }
        }

        if (!($zone instanceof DateTimeZone)) {
            throw new Stancer\Exceptions\InvalidArgumentException($message);
        }

        $this->timezone = $zone;

        return $this;
    }

    /**
     * Register a configuration for deferred API call.
     *
     * @param self $instance Current API instance.
     * @return self
     *
     * @phpstan-param static $instance Current API instance.
     */
    public static function setGlobal(self $instance): self
    {
        static::$instance = $instance;

        return $instance;
    }

    /**
     * Update API host.
     *
     * @param string $host New host.
     * @return $this
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Update HTTP client instance.
     *
     * Be careful, no limitation is done on this method to allow you to use your own
     * implementation of an HTTP client.
     *
     * @param Stancer\Http\Client|GuzzleHttp\ClientInterface $client New instance.
     * @return $this
     */
    public function setHttpClient($client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Update authentication keys.
     *
     * @param string|string[] $keys One or more keys to update.
     *
     * @return $this
     */
    public function setKeys($keys): self
    {
        if (!is_array($keys)) {
            return $this->setKeys([$keys]);
        }

        $prefixes = [
            'pprod',
            'ptest',
            'sprod',
            'stest',
        ];

        foreach ($keys as $key) {
            foreach ($prefixes as $prefix) {
                if (preg_match('`^' . $prefix . '_\w{24}$`', $key)) {
                    $this->keys[$prefix] = $key;
                }
            }
        }

        return $this;
    }

    /**
     * Update logger handler.
     *
     * @param Psr\Log\LoggerInterface $logger A PSR3 compatible logger.
     * @return $this
     */
    public function setLogger(Psr\Log\LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Update API mode.
     *
     * You should use class constant `LIVE_MODE` and `TEST_MODE` to be sure.
     *
     * @param string $mode New mode. Should be class constant `LIVE_MODE` or `TEST_MODE`.
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException If new mode is not valid.
     */
    public function setMode(string $mode): self
    {
        $validMode = [
            static::LIVE_MODE,
            static::TEST_MODE,
        ];

        if (!in_array($mode, $validMode, true)) {
            $message = 'Unknown mode "%s". Please use class constant "LIVE_MODE" or "TEST_MODE".';

            throw new Stancer\Exceptions\InvalidArgumentException(sprintf($message, $mode));
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * Update API port.
     *
     * @param integer $port New port.
     * @return $this
     */
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Update API timeout.
     *
     * @param integer $timeout New timeout.
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When setting a too high timeout.
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

            throw new Stancer\Exceptions\InvalidArgumentException($message);
        }

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Update API version.
     *
     * @param integer $version New version.
     * @return $this
     */
    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }
}
