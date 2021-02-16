<?php
declare(strict_types=1);

namespace ild78\Sepa;

use ild78;

/**
 * Representation of SEPA check informations.
 *
 * This will use SEPAmail, a french service allowing to verify bank details on SEPA.
 *
 * @property-read DateTimeImmutable|null $created
 * @property-read DateTimeImmutable|null $creationDate
 */
class Check extends ild78\Core\AbstractObject
{
    /** @var string */
    protected $endpoint = 'sepa/check';
}
