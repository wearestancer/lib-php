<?php
declare(strict_types=1);

namespace Stancer;

use Override;
use Stancer;

/**
 * Representation of a customer.
 *
 * @method ?\Stancer\Address getBillingAddress() Get customer's billing address.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?string getEmail() Get customer's email.
 * @method ?string getExternalId() Get external identifier.
 * @method ?string getMobile() Get customer's mobile phone.
 * @method ?string getName() Get customer's name.
 * @method ?\Stancer\Address getShippingAddress() Get customer's shipping address.
 * @method ?\Stancer\Address get_billing_address() Get customer's billing address.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?string get_email() Get customer's email.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_external_id() Get external identifier.
 * @method ?string get_id() Get object ID.
 * @method ?string get_mobile() Get customer's mobile phone.
 * @method ?string get_name() Get customer's name.
 * @method ?\Stancer\Address get_shipping_address() Get customer's shipping address.
 * @method string get_uri() Get entity resource location.
 * @method $this setBillingAddress(\Stancer\Address $billingAddress) Set customer's billing address.
 * @method $this setEmail(string $email) Set customer's email.
 * @method $this setExternalId(string $externalId) Set external identifier.
 * @method $this setMobile(string $mobile) Set customer's mobile phone.
 * @method $this setName(string $name) Set customer's name.
 * @method $this setShippingAddress(\Stancer\Address $shippingAddress) Set customer's shipping address.
 * @method $this set_billing_address(\Stancer\Address $billing_address) Set customer's billing address.
 * @method $this set_email(string $email) Set customer's email.
 * @method $this set_external_id(string $external_id) Set external identifier.
 * @method $this set_mobile(string $mobile) Set customer's mobile phone.
 * @method $this set_name(string $name) Set customer's name.
 * @method $this set_shipping_address(\Stancer\Address $shipping_address) Set customer's shipping address.
 *
 * @property ?\Stancer\Address $billingAddress Customer's billing address.
 * @property ?\Stancer\Address $billing_address Customer's billing address.
 * @property ?string $email Customer's email.
 * @property ?string $externalId External identifier.
 * @property ?string $external_id External identifier.
 * @property ?string $mobile Customer's mobile phone.
 * @property ?string $name Customer's name.
 * @property ?\Stancer\Address $shippingAddress Customer's shipping address.
 * @property ?\Stancer\Address $shipping_address Customer's shipping address.
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
        'billingAddress' => [
            'desc' => 'Customer\'s billing address',
            'onlyID' => true,
            'type' => Stancer\Address::class,
        ],
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
        'shippingAddress' => [
            'desc' => 'Customer\'s shipping address',
            'onlyID' => true,
            'type' => Stancer\Address::class,
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
