<?php
declare(strict_types=1);

namespace ild78\Sepa;

use ild78;

/**
 * Representation of SEPA check informations.
 *
 * This will use SEPAmail, a french service allowing to verify bank details on SEPA.
 *
 * @method string|null getStatus()
 *
 * @property-read DateTimeImmutable|null $created
 * @property-read DateTimeImmutable|null $creationDate
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
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];
}
