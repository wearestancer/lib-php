<?php
declare(strict_types=1);

namespace ild78\Sepa;

use ild78;

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
 * @property-read string|null $status
 */
class Check extends ild78\Core\AbstractObject
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
            'coerce' => ild78\Core\Type\Helper::INTEGER_TO_PERCENTAGE,
            'restricted' => true,
            'type' => self::FLOAT,
        ],
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];
}
