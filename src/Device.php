<?php
declare(strict_types=1);

namespace Stancer;

use Stancer;

/**
 * Device data for authenticated payment.
 *
 * @method ?string getCity() Get city of the payer.
 * @method ?string getCountry() Get country of the payer.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?string getHttpAccept() Get HTTP Accept header.
 * @method ?string getIp() Get IP address of the payer.
 * @method ?string getLanguages() Get HTTP Accept-Language header.
 * @method ?integer getPort() Get TCP port number of the payer.
 * @method ?string getUserAgent() Get HTTP User Agent header.
 * @method ?string get_city() Get city of the payer.
 * @method ?string get_country() Get country of the payer.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_http_accept() Get HTTP Accept header.
 * @method ?string get_id() Get object ID.
 * @method ?string get_ip() Get IP address of the payer.
 * @method ?string get_languages() Get HTTP Accept-Language header.
 * @method ?integer get_port() Get TCP port number of the payer.
 * @method string get_uri() Get entity resource location.
 * @method ?string get_user_agent() Get HTTP User Agent header.
 * @method $this hydrate_from_environment() Hydrate from environment.
 * @method $this setHttpAccept(string $httpAccept) Set HTTP Accept header.
 * @method $this setLanguages(string $languages) Set HTTP Accept-Language header.
 * @method $this setPort(integer $port) Set TCP port number of the payer.
 * @method $this setUserAgent(string $userAgent) Set HTTP User Agent header.
 * @method $this set_http_accept(string $http_accept) Set HTTP Accept header.
 * @method $this set_ip(string $ip) Set IP address of the payer.
 * @method $this set_languages(string $languages) Set HTTP Accept-Language header.
 * @method $this set_port(integer $port) Set TCP port number of the payer.
 * @method $this set_user_agent(string $user_agent) Set HTTP User Agent header.
 *
 * @property ?string $httpAccept HTTP Accept header.
 * @property ?string $http_accept HTTP Accept header.
 * @property ?string $ip IP address of the payer.
 * @property ?string $languages HTTP Accept-Language header.
 * @property ?integer $port TCP port number of the payer.
 * @property ?string $userAgent HTTP User Agent header.
 * @property ?string $user_agent HTTP User Agent header.
 *
 * @property-read ?string $city City of the payer.
 * @property-read ?string $country Country of the payer.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read $this $hydrateFromEnvironment Alias for `Stancer\Device::hydrateFromEnvironment()`.
 * @property-read $this $hydrate_from_environment Alias for `Stancer\Device::hydrateFromEnvironment()`.
 * @property-read ?string $id Object ID.
 * @property-read string $uri Entity resource location.
 */
class Device extends Stancer\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'city' => [
            'desc' => 'City of the payer',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'country' => [
            'desc' => 'Country of the payer',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'httpAccept' => [
            'desc' => 'HTTP Accept header',
            'type' => self::STRING,
        ],
        'ip' => [
            'desc' => 'IP address of the payer',
            'type' => self::STRING,
        ],
        'languages' => [
            'desc' => 'HTTP Accept-Language header',
            'type' => self::STRING,
        ],
        'port' => [
            'desc' => 'TCP port number of the payer',
            'size' => [
                'min' => 1,
                'max' => 65535,
            ],
            'type' => self::INTEGER,
        ],
        'userAgent' => [
            'desc' => 'HTTP User Agent header',
            'type' => self::STRING,
        ],
    ];

    /**
     * Hydrate from environment.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidIpAddressException When no IP address was given.
     * @throws Stancer\Exceptions\InvalidPortException When no port was given.
     */
    public function hydrateFromEnvironment(): self
    {
        if (!$this->getHttpAccept() && getenv('HTTP_ACCEPT')) {
            $this->setHttpAccept(getenv('HTTP_ACCEPT'));
        }

        if (!$this->getLanguages() && getenv('HTTP_ACCEPT_LANGUAGE')) {
            $this->setLanguages(getenv('HTTP_ACCEPT_LANGUAGE'));
        }

        if (!$this->getIp() && getenv('REMOTE_ADDR')) {
            $this->setIp(getenv('REMOTE_ADDR'));
        }

        if (!$this->getPort() && getenv('REMOTE_PORT')) {
            $this->setPort((int) getenv('REMOTE_PORT'));
        }

        if (!$this->getUserAgent() && getenv('HTTP_USER_AGENT')) {
            $this->setUserAgent(getenv('HTTP_USER_AGENT'));
        }

        if (!$this->getIp()) {
            throw new Stancer\Exceptions\InvalidIpAddressException('You must provide an IP address.');
        }

        if (!$this->getPort()) {
            throw new Stancer\Exceptions\InvalidPortException('You must provide a port.');
        }

        return $this;
    }

    /**
     * Update customer's IP address.
     *
     * We allow IPv4 and IPv6 addresses.
     *
     * @param string $ip New IP address.
     * @return $this
     * @throws Stancer\Exceptions\InvalidIpAddressException When $ip is not a correct IP address.
     */
    public function setIp(string $ip): self
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $message = sprintf('"%s" is not a valid IP address.', $ip);

            throw new Stancer\Exceptions\InvalidIpAddressException($message);
        }

        parent::setIp($ip);

        return $this;
    }
}
