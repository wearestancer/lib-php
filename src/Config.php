<?php
declare(strict_types=1);

namespace Stancer;

use DateTimeZone;
use GuzzleHttp;
use Psr;
use SensitiveParameter;
use Stancer;

/**
 * Handle configuration, connection and credential to API.
 *
 * @method $this add_app_data(string $name, string $version = null) Define application data (name and version).
 * @method $this add_call(Stancer\Core\Request\Call $call) Add a call to the list.
 * @method string get_basic_auth_header() Get HTTP "basic" authentication header's value.
 * @method Stancer\Core\Request\Call[] get_calls() Get request list recorded on debug mode.
 * @method boolean get_debug() Get debug mode.
 * @method ?\DateTimeZone get_default_time_zone() Get default time zone.
 * @method string get_default_user_agent() Get default user agent.
 * @method static static get_global() Return current instance.
 * @method string get_host() Get API host.
 * @method \Stancer\Http\Client|\GuzzleHttp\ClientInterface get_http_client() Get HTTP client instance.
 * @method \Psr\Log\LoggerInterface get_logger() Get logger handler.
 * @method string get_mode() Get API mode (test or live).
 * @method ?integer get_port() Get API port.
 * @method string get_public_key() Get public API key.
 * @method string get_secret_key() Get secret API key.
 * @method integer get_timeout() Get API timeout.
 * @method string get_uri() Get API URI.
 * @method integer get_version() Get API version.
 * @method boolean is_live_mode() Indicate if API is in live mode.
 * @method boolean is_not_live_mode() Indicate if API is not in live mode.
 * @method boolean is_not_test_mode() Indicate if API is not in test mode.
 * @method boolean is_test_mode() Indicate if API is in test mode.
 * @method $this reset_app_data() Reset app data.
 * @method $this reset_default_time_zone() Reset default time zone.
 * @method $this set_debug(boolean $debug) Set debug mode.
 * @method $this set_default_time_zone(\DateTimeZone $default_time_zone) Set default time zone.
 * @method static self set_global(self $instance) Register a configuration for deferred API call.
 * @method $this set_host(string $host) Set API host.
 * @method $this set_http_client(\Stancer\Http\Client|\GuzzleHttp\ClientInterface $http_client) Set HTTP client
 *   instance.
 * @method $this set_keys($keys) Update authentication keys.
 * @method $this set_logger(\Psr\Log\LoggerInterface $logger) Set logger handler.
 * @method $this set_mode(string $mode) Set API mode (test or live).
 * @method $this set_port(integer $port) Set API port.
 * @method $this set_timeout(integer $timeout) Set API timeout.
 * @method $this set_version(integer $version) Set API version.
 *
 * @property boolean $debug Debug mode.
 * @property ?\DateTimeZone $defaultTimeZone Default time zone.
 * @property ?\DateTimeZone $default_time_zone Default time zone.
 * @property string $host API host.
 * @property \Stancer\Http\Client|\GuzzleHttp\ClientInterface $httpClient HTTP client instance.
 * @property \Stancer\Http\Client|\GuzzleHttp\ClientInterface $http_client HTTP client instance.
 * @property \Psr\Log\LoggerInterface $logger Logger handler.
 * @property string $mode API mode (test or live).
 * @property ?integer $port API port.
 * @property integer $timeout API timeout.
 * @property integer $version API version.
 *
 * @property-read string $basicAuthHeader HTTP "basic" authentication header's value.
 * @property-read string $basic_auth_header HTTP "basic" authentication header's value.
 * @property-read Stancer\Core\Request\Call[] $calls Request list recorded on debug mode.
 * @property-read string $defaultUserAgent Default user agent.
 * @property-read string $default_user_agent Default user agent.
 * @property-read boolean $isLiveMode Alias for `Stancer\Config::isLiveMode()`.
 * @property-read boolean $isNotLiveMode Alias for `Stancer\Config::isNotLiveMode()`.
 * @property-read boolean $isNotTestMode Alias for `Stancer\Config::isNotTestMode()`.
 * @property-read boolean $isTestMode Alias for `Stancer\Config::isTestMode()`.
 * @property-read boolean $is_live_mode Alias for `Stancer\Config::isLiveMode()`.
 * @property-read boolean $is_not_live_mode Alias for `Stancer\Config::isNotLiveMode()`.
 * @property-read boolean $is_not_test_mode Alias for `Stancer\Config::isNotTestMode()`.
 * @property-read boolean $is_test_mode Alias for `Stancer\Config::isTestMode()`.
 * @property-read string $publicKey Public API key.
 * @property-read string $public_key Public API key.
 * @property-read $this $resetAppData Alias for `Stancer\Config::resetAppData()`.
 * @property-read $this $resetDefaultTimeZone Alias for `Stancer\Config::resetDefaultTimeZone()`.
 * @property-read $this $reset_app_data Alias for `Stancer\Config::resetAppData()`.
 * @property-read $this $reset_default_time_zone Alias for `Stancer\Config::resetDefaultTimeZone()`.
 * @property-read string $secretKey Secret API key.
 * @property-read string $secret_key Secret API key.
 * @property-read string $uri API URI.
 */
class Config
{
    use Stancer\Traits\AliasTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    public const LIVE_MODE = 'live';
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    public const TEST_MODE = 'test';
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    public const VERSION = '2.0.1';

    /** @var non-empty-array<string|null>[] */
    protected array $app = [];

    /** @var Stancer\Core\Request\Call[] */
    protected array $calls = [];

    protected ?bool $debug = null;

    protected string $host = 'api.stancer.com';

    protected Stancer\Http\Client|GuzzleHttp\ClientInterface|null $httpClient = null;

    /** @var self|null */
    protected static ?self $instance = null;

    /** @var array<string, string|null> */
    protected array $keys = [
        'pprod' => null,
        'ptest' => null,
        'sprod' => null,
        'stest' => null,
    ];

    protected ?Psr\Log\LoggerInterface $logger = null;

    protected string $mode = self::TEST_MODE;

    protected ?int $port = null;

    protected int $timeout = 0;

    protected ?DateTimeZone $timezone = null;

    protected int $version = 1;

    /**
     * Create an API configuration.
     *
     * An authentication key is required to make a new instance. It will be used on every API call.
     * You needed to set a configuration as global to be used on every API call.
     *
     * @see self::init() for a quick config setup
     * @param string[] $keys Authentication keys.
     */
    public function __construct(
        #[SensitiveParameter]
        array $keys
    ) {
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
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'HTTP "basic" authentication header\'s value',
        nullable: false,
        restricted: true,
    )]
    public function getBasicAuthHeader(): string
    {
        return 'Basic ' . base64_encode($this->getSecretKey() . ':');
    }

    /**
     * Return call list recorded on debug mode.
     *
     * @return Stancer\Core\Request\Call[]
     */
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'Request list recorded on debug mode',
        list: true,
        restricted: true,
        type: Stancer\Core\Request\Call::class,
    )]
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * Indicate if we are in debug mode.
     *
     * @return boolean
     */
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'Debug mode',
        nullable: false,
        type: Stancer\Core\AbstractObject::BOOLEAN,
    )]
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'Default time zone', type: DateTimeZone::class)]
    public function getDefaultTimeZone(): ?DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * Return default user agent.
     *
     * @return string
     * @phpstan-return non-empty-string
     */
    #[Stancer\Core\Documentation\FormatProperty(description: 'Default user agent', nullable: false, restricted: true)]
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
    public static function getGlobal(): static
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'API host', nullable: false)]
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
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'HTTP client instance',
        nullable: false,
        type: [
            Stancer\Http\Client::class,
            GuzzleHttp\ClientInterface::class,
        ],
    )]
    public function getHttpClient(): Stancer\Http\Client|GuzzleHttp\ClientInterface
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
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'Logger handler',
        nullable: false,
        type: Psr\Log\LoggerInterface::class,
    )]
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'API mode (test or live)', value: 'test')]
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'API port', type: 'integer')]
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'Public API key', nullable: false, restricted: true)]
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'Secret API key', nullable: false, restricted: true)]
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
     * No default.
     *
     * @return integer
     */
    #[Stancer\Core\Documentation\FormatProperty(description: 'API timeout', type: 'integer', value: 0)]
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'API URI', nullable: false, restricted: true)]
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'API version', nullable: false, type: 'integer')]
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
    public static function init(
        #[SensitiveParameter]
        array $keys
    ): self {
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
    public function setKeys(
        #[SensitiveParameter]
        $keys
    ): self {
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
            ];
            $pattern = 'Timeout (%ds) is too high, the maximum allowed is %d seconds.';
            $message = vsprintf($pattern, $params);

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
