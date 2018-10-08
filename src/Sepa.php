<?php
declare(strict_types=1);

namespace ild78;

/**
 * Representation of a SEPA account
 */
class Sepa extends Api\Object
{
    /** @var string */
    protected $bic;

    /** @var string */
    protected $country;

    /** @var string */
    protected $iban;

    /** @var string|null */
    protected $name;
}
