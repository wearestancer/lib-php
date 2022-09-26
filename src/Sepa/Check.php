<?php
declare(strict_types=1);

namespace Stancer\Sepa;

use Stancer;

/**
 * Representation of SEPA check informations.
 *
 * This will use SEPAmail, a french service allowing to verify bank details on SEPA.
 *
 * @method bool|null getDateBirth()
 * @method string|null getResponse()
 * @method float|null getScoreName()
 * @method string|null getStatus()
 *
 * @property-read bool|null $dateBirth
 * @property-read DateTimeImmutable|null $created
 * @property-read DateTimeImmutable|null $creationDate
 * @property-read string|null $response
 * @property-read float|null $scoreName
 * @property-read Stancer\Sepa|null $sepa
 * @property-read string|null $status
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
    public function send(): Stancer\Core\AbstractObject
    {
        $this->modified[] = 'sepa'; // Mandatory, force `parent::send()` to work.

        return parent::send();
    }
}
