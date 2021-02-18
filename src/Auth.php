<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Data for authenticated payment
 *
 * @method string|null getRedirectUrl()
 * @method string getReturnUrl()
 * @method string getStatus()
 *
 * @property DateTimeImmutable|null $created
 * @property string|null $redirectUrl
 * @property string $returnUrl
 * @property string $status
 */
class Auth extends ild78\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
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
            'exportable' => true,
            'restricted' => true,
            'type' => self::STRING,
            'value' => ild78\Auth\Status::REQUEST,
        ],
    ];

    /** @var string[] */
    protected $modified = [
        'status',
    ];

    /**
     * Update return URL
     *
     * @param string $url New HTTPS URL.
     * @return $this
     * @throws ild78\Exceptions\InvalidUrlException When URL is not an HTTPS URL.
     */
    public function setReturnUrl(string $url): self
    {
        if (strpos($url, 'https://') !== 0) {
            throw new ild78\Exceptions\InvalidUrlException('You must provide an HTTPS URL.');
        }

        return parent::setReturnUrl($url);
    }
}
