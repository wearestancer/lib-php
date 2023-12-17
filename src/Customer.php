<?php
declare(strict_types=1);

namespace Stancer;

use Override;
use Stancer;

/**
 * Representation of a customer.
 *
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?string getEmail() Get customer's email.
 * @method ?string getExternalId() Get external identifier.
 * @method ?string getMobile() Get customer's mobile phone.
 * @method ?string getName() Get customer's name.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?string get_email() Get customer's email.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_external_id() Get external identifier.
 * @method ?string get_id() Get object ID.
 * @method ?string get_mobile() Get customer's mobile phone.
 * @method ?string get_name() Get customer's name.
 * @method string get_uri() Get entity resource location.
 * @method $this setEmail(string $email) Set customer's email.
 * @method $this setExternalId(string $externalId) Set external identifier.
 * @method $this setMobile(string $mobile) Set customer's mobile phone.
 * @method $this setName(string $name) Set customer's name.
 * @method $this set_email(string $email) Set customer's email.
 * @method $this set_external_id(string $external_id) Set external identifier.
 * @method $this set_mobile(string $mobile) Set customer's mobile phone.
 * @method $this set_name(string $name) Set customer's name.
 *
 * @property ?string $email Customer's email.
 * @property ?string $externalId External identifier.
 * @property ?string $external_id External identifier.
 * @property ?string $mobile Customer's mobile phone.
 * @property ?string $name Customer's name.
 *
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read string $uri Entity resource location.
 */
class Customer extends Stancer\Core\AbstractObject
{
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'customers';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'email' => [
            'desc' => 'Customer\'s email',
            'size' => [
                'min' => 5,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'externalId' => [
            'desc' => 'External identifier',
            'size' => [
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
        'mobile' => [
            'desc' => 'Customer\'s mobile phone',
            'size' => [
                'min' => 8,
                'max' => 16,
            ],
            'type' => self::STRING,
        ],
        'name' => [
            'desc' => 'Customer\'s name',
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Send a customer.
     *
     * @uses Request::post()
     * @return $this
     * @throws Stancer\Exceptions\BadMethodCallException When trying to send a customer without an email
     *    or a phone number.
     */
    #[Override]
    public function send(): static
    {
        if (!$this->getId() && !$this->getEmail() && !$this->getMobile()) {
            $message = 'You must provide an email or a phone number to create a customer.';

            throw new Stancer\Exceptions\BadMethodCallException($message);
        }

        return parent::send();
    }
}
