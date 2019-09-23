<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Data for authenticated payment
 *
 * @method string getRedirectUrl()
 * @method string getReturnUrl()
 * @method string getStatus()
 */
class Auth extends Api\AbstractObject
{
    /** @var array */
    protected $dataModel = [
        'redirectUrl' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'returnUrl' => [
            'size' => [
                'min' => 1,
                'max' => 2048,
            ],
            'type' => self::STRING,
        ],
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];

    /**
     * Update return URL
     *
     * @param string $url New HTTPS URL.
     * @return self
     * @throws ild78\Exceptions\InvalidUrlException When URL is not an HTTPS URL.
     */
    public function setReturnUrl(string $url) : self
    {
        if (strpos($url, 'https://') !== 0) {
            throw new ild78\Exceptions\InvalidUrlException('You must provide an HTTPS URL.');
        }

        return parent::setReturnUrl($url);
    }
}
