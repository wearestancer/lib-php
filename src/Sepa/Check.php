<?php
declare(strict_types=1);

namespace Stancer\Sepa;

use Override;
use ReturnTypeWillChange;
use Stancer;

/**
 * Representation of SEPA check informations.
 *
 * This will use SEPAmail, a french service allowing to verify bank details on SEPA.
 *
 * @method ?DateTimeImmutable getCreated() Get creation date.
 * @method ?boolean getDateBirth()
 * @method ?string getResponse()
 * @method ?float getScoreName()
 * @method ?string getStatus()
 * @method ?DateTimeImmutable get_created() Get creation date.
 * @method ?DateTimeImmutable get_creation_date() Get creation date.
 * @method ?boolean get_date_birth()
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method ?string get_response()
 * @method ?float get_score_name()
 * @method ?Stancer\Sepa get_sepa()
 * @method ?string get_status()
 * @method string get_uri() Get entity resource location.
 *
 * @property-read ?DateTimeImmutable $created Creation date.
 * @property-read ?DateTimeImmutable $creationDate Creation date.
 * @property-read ?DateTimeImmutable $creation_date Creation date.
 * @property-read ?boolean $dateBirth
 * @property-read ?boolean $date_birth
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read mixed $jsonSerialize Alias for `Stancer\Sepa\Check::jsonSerialize()`.
 * @property-read mixed $json_serialize Alias for `Stancer\Sepa\Check::jsonSerialize()`.
 * @property-read ?string $response
 * @property-read ?float $scoreName
 * @property-read ?float $score_name
 * @property-read ?Stancer\Sepa $sepa
 * @property-read ?string $status
 * @property-read string $uri Entity resource location.
 */
class Check extends Stancer\Core\AbstractObject
{
    /** @var string */
    protected $endpoint = 'sepa/check';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'dateBirth' => [
            'restricted' => true,
            'type' => self::BOOLEAN,
        ],
        'response' => [
            'restricted' => true,
            'size' => [
                'min' => 2,
                'max' => 4,
            ],
            'type' => self::STRING,
        ],
        'scoreName' => [
            'coerce' => Stancer\Core\Type\Helper::INTEGER_TO_PERCENTAGE,
            'restricted' => true,
            'type' => self::FLOAT,
        ],
        'sepa' => [
            'restricted' => true,
            'type' => Stancer\Sepa::class,
        ],
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];

    /**
     * Return Sepa object attached to this check.
     *
     * @return Stancer\Sepa|null
     */
    public function getSepa(): ?Stancer\Sepa
    {
        if ($this->id) {
            $sepa = $this->dataModelGetter('sepa', false);

            if (is_null($sepa)) {
                $this->dataModel['sepa']['value'] = new Stancer\Sepa($this->id);
            }
        }

        return parent::getSepa();
    }

    /**
     * Return a array representation of the current object for a conversion as JSON.
     *
     * @uses self::toArray()
     * @return string|integer|boolean|null|array<string, mixed>
     */
    #[Override]
    #[ReturnTypeWillChange, Stancer\WillChange\PHP8_0\MixedType]
    public function jsonSerialize()
    {
        $sepa = $this->getSepa();

        if (!$sepa) {
            return [];
        }

        if ($sepa->id) {
            return [
                'id' => $sepa->id,
            ];
        }

        return $sepa->jsonSerialize();
    }

    /**
     * Send the current object.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When all requirement are not provided.
     */
    #[Override]
    public function send(): Stancer\Core\AbstractObject
    {
        $this->modified[] = 'sepa'; // Mandatory, force `parent::send()` to work.

        return parent::send();
    }
}
