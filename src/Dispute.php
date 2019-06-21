<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Representation of a dispute
 */
class Dispute extends Api\AbstractObject
{
    use ild78\Traits\AmountTrait;

    /** @var string */
    protected $endpoint = 'disputes';
}
