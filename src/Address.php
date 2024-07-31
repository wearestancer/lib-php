<?php

declare(strict_types=1);

namespace Stancer;

use Stancer\Core\AbstractObject;
use Stancer;

/**
 * Representation of an address.
 *
 * @method static add_metadata(array $new_metadata) Add metadata to our array of metadata.
 * @method ?string getCity() Get the name of the city.
 * @method ?string getCountry() Get ISO 3166-1 alpha-3 country code.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?boolean getDeleted() Get if the address is deleted or not.
 * @method ?string getLine1() Get the street number and name, line 1.
 * @method ?string getLine2() Get the street number and name, line 2.
 * @method ?string getLine3() Get the street number and name, line 3.
 * @method ?string getState() Get ISO 3166-2 state or province.
 * @method ?string getZipCode() Get the zip code.
 * @method ?string get_city() Get the name of the city.
 * @method ?string get_country() Get ISO 3166-1 alpha-3 country code.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?boolean get_deleted() Get if the address is deleted or not.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method ?string get_line1() Get the street number and name, line 1.
 * @method ?string get_line2() Get the street number and name, line 2.
 * @method ?string get_line3() Get the street number and name, line 3.
 * @method ?string get_metadata() Get A json object, with various usefull data.
 * @method ?string get_state() Get ISO 3166-2 state or province.
 * @method string get_uri() Get entity resource location.
 * @method ?string get_zip_code() Get the zip code.
 * @method $this setCity(string $city) Set the name of the city.
 * @method $this setDeleted(boolean $deleted) Set if the address is deleted or not.
 * @method $this setLine1(string $line1) Set the street number and name, line 1.
 * @method $this setLine2(string $line2) Set the street number and name, line 2.
 * @method $this setLine3(string $line3) Set the street number and name, line 3.
 * @method $this setState(string $state) Set ISO 3166-2 state or province.
 * @method $this setZipCode(string $zipCode) Set the zip code.
 * @method $this set_city(string $city) Set the name of the city.
 * @method $this set_country(string $country) Set ISO 3166-1 alpha-3 country code.
 * @method $this set_deleted(boolean $deleted) Set if the address is deleted or not.
 * @method $this set_line1(string $line1) Set the street number and name, line 1.
 * @method $this set_line2(string $line2) Set the street number and name, line 2.
 * @method $this set_line3(string $line3) Set the street number and name, line 3.
 * @method $this set_metadata(string $metadata) Set A json object, with various usefull data.
 * @method $this set_state(string $state) Set ISO 3166-2 state or province.
 * @method $this set_zip_code(string $zip_code) Set the zip code.
 *
 * @property ?string $city The name of the city.
 * @property ?string $country ISO 3166-1 alpha-3 country code.
 * @property ?boolean $deleted If the address is deleted or not.
 * @property ?string $line1 The street number and name, line 1.
 * @property ?string $line2 The street number and name, line 2.
 * @property ?string $line3 The street number and name, line 3.
 * @property ?string $metadata A json object, with various usefull data.
 * @property ?string $state ISO 3166-2 state or province.
 * @property ?string $zipCode The zip code.
 * @property ?string $zip_code The zip code.
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
class Address extends AbstractObject
{
    final public const ENDPOINT = 'addresses';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'city' => [
            'desc' => 'The name of the city',
            'type' => self::STRING,
            'size' => [
                'min' => 1,
                'max' => 50,
            ],
        ],
        'country' => [
            'desc' => 'ISO 3166-1 alpha-3 country code',
            'type' => self::STRING,
            'size' => [
                'fixed' => 3,
            ],
        ],
        'deleted' => [
            'desc' => 'If the address is deleted or not',
            'type' => self::BOOLEAN,
        ],
        'line1' => [
            'desc' => 'The street number and name, line 1',
            'type' => self::STRING,
            'size' => [
                'min' => 1,
                'max' => 50,
            ],
        ],
        'line2' => [
            'desc' => 'The street number and name, line 2',
            'type' => self::STRING,
            'size' => [
                'min' => 1,
                'max' => 50,
            ],
        ],
        'line3' => [
            'desc' => 'The street number and name, line 3',
            'type' => self::STRING,
            'size' => [
                'min' => 1,
                'max' => 50,
            ],
        ],
        'metadata' => [
            'desc' => 'A json object, with various usefull data',
            'type' => self::STRING,
            'value' => '{"origin":"sdk_PHP"}',
        ],
        'state' => [
            'desc' => 'ISO 3166-2 state or province',
            'type' => self::STRING,
            'size' => [
                'min' => 1,
                'max' => 3,
            ],
        ],
        'zipCode' => [
            'desc' => 'The zip code',
            'type' => self::STRING,
            'size' => [
                'min' => 1,
                'max' => 16,
            ],
        ],
    ];

    /**
     * Add metadata to our array of metadata.
     *
     * @param array<string,mixed> $newMetadata Data to add to our array.
     * @return static
     *
     * @throws Stancer\Exceptions\InvalidJsonException Throw exception if the json we got isn't an array.
     */
    public function addMetadata(array $newMetadata): static
    {
        $previousMetadata = $this->getMetadata();

        $currentMetadata = [
            ...$previousMetadata,
            ...$newMetadata,
        ];
        return $this->setMetadata($currentMetadata);
    }

    /**
     * Get decoded json metadata.
     *
     * @return array<mixed> decoded json metadata.
     *
     * @throws Stancer\Exceptions\InvalidJsonException When the json is invalid.
     */
    public function getMetadata(): array
    {
        $currentMetadata = parent::getMetadata();
        if (is_array($currentMetadata)) {
            return $currentMetadata;
        }

        $serializedMetadadata = json_decode($currentMetadata, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($serializedMetadadata)) {
            throw new Stancer\Exceptions\InvalidJsonException('Invalid Json, couldn\'t be parsed as an array.');
        }

        return $serializedMetadadata;
    }

    /**
     * Override send to block patching Address.
     *
     * @return $this
     *
     * @throws Stancer\Exceptions\BadMethodCallException Trying to patch an address result in error.
     */
    #[\Override]
    public function send(): static
    {
        if ($this->getId()) {
            throw new Stancer\Exceptions\BadMethodCallException('Addresses cannot be patched.');
        }
        return parent::send();
    }

    /**
     * Set metadata.
     *
     * @param array<string,mixed>|string $newMetadata Data to set to our metadata field.
     * @return static
     *
     * @throws Stancer\Exceptions\InvalidJsonException When the json is invalid.
     */
    public function setMetadata(array|string $newMetadata): static
    {
        $currentMetadata = json_encode($newMetadata);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Stancer\Exceptions\InvalidJsonException('Invalid Json, cannot be parsed.');
        }
        return parent::setMetadata($currentMetadata);
    }
}
