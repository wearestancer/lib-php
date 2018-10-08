<?php
declare(strict_types=1);

namespace ild78;

use ild78\Exceptions;

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

    /**
     * Add or update a Bank Identifier Code (BIC)
     *
     * @param string $bic A Bank Identifier Code
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException when BIC seems invalid
     */
    public function setBic($bic) : self
    {
        $length = strlen($bic);

        if ($length !== 8 && $length !== 11) {
            throw new Exceptions\InvalidArgumentException(sprintf('"%s" is not a valid BIC', $bic));
        }

        $this->bic = $bic;

        return $this;
    }
}
