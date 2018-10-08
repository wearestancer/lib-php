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
     * Return an array of properties not allowed to change with a setter
     *
     * @see self::__call()
     * @return array
     */
    public function getForbiddenProperties() : array
    {
        return array_merge(parent::getForbiddenProperties(), [
            'country',
        ]);
    }

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

    /**
     * Add or update an International Bank Account Number (IBAN)
     *
     * @param string $iban An International Bank Account Number
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException when IBAN is invalid
     */
    public function setIban(string $iban) : self
    {
        $iban = str_replace(' ', '', $iban);
        $country = substr($iban, 0, 2);
        $start = substr($iban, 0, 4);
        $bban = substr($iban, 4);
        $code = strtoupper($bban . $start);

        $letters = range('A', 'Z');
        $replace = [];
        $index = 10;

        foreach ($letters as $letter) {
            $replace[$letter] = $index++;
        }

        $code = str_replace(array_keys($replace), $replace, $code);

        $check = substr($code, 0, 2);
        $parts = explode(' ', chunk_split(substr($code, 2), 7, ' '));

        foreach ($parts as $part) {
            $check = ($check . $part) % 97;
        }

        if ($check !== 1) {
            throw new Exceptions\InvalidArgumentException(sprintf('"%s" is not a valid IBAN', $iban));
        }

        $this->country = $country;
        $this->iban = $iban;

        return $this;
    }
}
