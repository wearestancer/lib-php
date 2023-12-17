<?php
declare(strict_types=1);

namespace Stancer;

use Stancer;

/**
 * Data for authenticated payment.
 *
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?string getRedirectUrl() Get the redirection URL to start an authentification session.
 * @method ?string getReturnUrl() Get the return URL at end of the authentification session.
 * @method \Stancer\Auth\Status getStatus() Get the authentification status.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method ?string get_redirect_url() Get the redirection URL to start an authentification session.
 * @method ?string get_return_url() Get the return URL at end of the authentification session.
 * @method \Stancer\Auth\Status get_status() Get the authentification status.
 * @method string get_uri() Get entity resource location.
 * @method $this set_return_url(string $return_url) Set the return URL at end of the authentification session.
 *
 * @property ?string $returnUrl The return URL at end of the authentification session.
 * @property ?string $return_url The return URL at end of the authentification session.
 *
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read ?string $redirectUrl The redirection URL to start an authentification session.
 * @property-read ?string $redirect_url The redirection URL to start an authentification session.
 * @property-read \Stancer\Auth\Status $status The authentification status.
 * @property-read string $uri Entity resource location.
 */
class Auth extends Stancer\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'redirectUrl' => [
            'desc' => 'The redirection URL to start an authentification session',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'returnUrl' => [
            'desc' => 'The return URL at end of the authentification session',
            'size' => [
                'min' => 1,
                'max' => 2048,
            ],
            'type' => self::STRING,
        ],
        'status' => [
            'desc' => 'The authentification status',
            'exportable' => true,
            'restricted' => true,
            'type' => Stancer\Auth\Status::class,
            'value' => Stancer\Auth\Status::REQUEST,
        ],
    ];

    /** @var string[] */
    protected array $modified = [
        'status',
    ];

    /**
     * Update return URL.
     *
     * @param string $url New HTTPS URL.
     * @return $this
     * @throws Stancer\Exceptions\InvalidUrlException When URL is not an HTTPS URL.
     */
    public function setReturnUrl(string $url): self
    {
        if (strpos($url, 'https://') !== 0) {
            throw new Stancer\Exceptions\InvalidUrlException('You must provide an HTTPS URL.');
        }

        return parent::setReturnUrl($url);
    }
}
