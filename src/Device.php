<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Device data for authenticated payment
 *
 * @method string getCity()
 * @method string getCountry()
 * @method string getHttpAccept()
 * @method string getIp()
 * @method string getLanguages()
 * @method integer getPort()
 * @method string getUserAgent()
 *
 * @method $this setCity(string $city)
 * @method $this setCountry(string $country)
 * @method $this setHttpAccept(string $httpAccept)
 * @method $this setLanguages(string $languages)
 * @method $this setPort(integer $port)
 * @method $this setUserAgent(string $userAgent)
 *
 * @property string $city
 * @property string $country
 * @property DateTimeImmutable|null $created
 * @property string $httpAccept
 * @property string $ip
 * @property string $languages
 * @property integer $port
 * @property string $userAgent
 */
class Device extends ild78\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'city' => [
            'type' => self::STRING,
        ],
        'country' => [
            'type' => self::STRING,
        ],
        'httpAccept' => [
            'type' => self::STRING,
        ],
        'ip' => [
            'type' => self::STRING,
        ],
        'languages' => [
            'type' => self::STRING,
        ],
        'port' => [
            'size' => [
                'min' => 1,
                'max' => 65535,
            ],
            'type' => self::INTEGER,
        ],
        'userAgent' => [
            'type' => self::STRING,
        ],
    ];

    /**
     * Hydrate from environment
     *
     * @return $this
     * @throws ild78\Exceptions\InvalidIpAddressException When no IP address was given.
     * @throws ild78\Exceptions\InvalidPortException When no port was given.
     */
    public function hydrateFromEnvironment()
    {
        if (!$this->getHttpAccept() && getenv('HTTP_ACCEPT')) {
            $this->setHttpAccept(getenv('HTTP_ACCEPT'));
        }

        if (!$this->getLanguages() && getenv('HTTP_ACCEPT_LANGUAGE')) {
            $this->setLanguages(getenv('HTTP_ACCEPT_LANGUAGE'));
        }

        if (!$this->getIp() && getenv('SERVER_ADDR')) {
            $this->setIp(getenv('SERVER_ADDR'));
        }

        if (!$this->getPort() && getenv('SERVER_PORT')) {
            $this->setPort((int) getenv('SERVER_PORT'));
        }

        if (!$this->getUserAgent() && getenv('HTTP_USER_AGENT')) {
            $this->setUserAgent(getenv('HTTP_USER_AGENT'));
        }

        if (!$this->getIp()) {
            throw new ild78\Exceptions\InvalidIpAddressException('You must provide an IP address.');
        }

        if (!$this->getPort()) {
            throw new ild78\Exceptions\InvalidPortException('You must provide a port.');
        }

        return $this;
    }

    /**
     * Update customer's IP address
     *
     * We allow IPv4 and IPv6 addresses.
     *
     * @param string $ip New IP address.
     * @return $this
     * @throws ild78\Exceptions\InvalidIpAddressException When $ip is not a correct IP address.
     */
    public function setIp(string $ip): self
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $message = sprintf('"%s" is not a valid IP address.', $ip);

            throw new ild78\Exceptions\InvalidIpAddressException($message);
        }

        parent::setIp($ip);

        return $this;
    }
}
