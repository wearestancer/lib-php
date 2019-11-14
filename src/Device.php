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
 * @method self setCity(string $city)
 * @method self setCountry(string $country)
 * @method self setHttpAccept(string $httpAccept)
 * @method self setLanguages(string $languages)
 * @method self setUserAgent(string $userAgent)
 */
class Device extends Api\AbstractObject
{
    /** @var array */
    protected $dataModel = [
        'city' => [
            'type' => self::STRING,
        ],
        'country' => [
            'type' => self::STRING,
        ],
        'httpAccept' => [
            'size' => [
                'max' => 2048,
            ],
            'type' => self::STRING,
        ],
        'ip' => [
            'type' => self::STRING,
        ],
        'languages' => [
            'size' => [
                'max' => 32,
            ],
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
            'size' => [
                'max' => 256,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Create a new device
     *
     * @param array $data Data for instant hydratation.
     * @return self
     * @throws ild78\Exceptions\InvalidIpAddressException When no IP address was given.
     * @throws ild78\Exceptions\InvalidPortException When no port was given.
     */
    public function __construct(array $data = [])
    {
        if (empty($data['http_accept'])) {
            $data['http_accept'] = $_SERVER['HTTP_ACCEPT'] ?? null;
        }

        if (empty($data['languages'])) {
            $data['languages'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
        }

        if (empty($data['ip'])) {
            $data['ip'] = $_SERVER['SERVER_ADDR'] ?? null;
        }

        if (empty($data['port'])) {
            $data['port'] = $_SERVER['SERVER_PORT'] ?? 0;
        }

        if (empty($data['user_agent'])) {
            $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? null;
        }

        if (!$data['ip']) {
            throw new ild78\Exceptions\InvalidIpAddressException('You must provide an IP address.');
        }

        if (!$data['port']) {
            throw new ild78\Exceptions\InvalidPortException('You must provide a port.');
        }

        parent::__construct($data);
    }

    /**
     * Update customer's IP address
     *
     * We allow IPv4 and IPv6 addresses.
     *
     * @param string $ip New IP address.
     * @return self
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

    /**
     * Update customer's port
     *
     * @param integer $port New port.
     * @return self
     * @throws ild78\Exceptions\InvalidPortException When $port is not a correct port.
     */
    public function setPort(int $port): self
    {
        try {
            return parent::setPort($port);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidPortException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }
}
