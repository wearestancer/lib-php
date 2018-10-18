<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Representation of a SEPA account
 */
class Sepa extends Api\Object
{
    /** @var array */
    protected $dataModel = [
        'bic' => [
            'required' => true,
            'type' => self::STRING,
        ],
        'country' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'iban' => [
            'required' => true,
            'type' => self::STRING,
        ],
        'last4' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'name' => [
            'size' => [
                'min' => 4,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Return IBAN with usual readeable format (AAAA BBBB CCCC ...)
     *
     * @return string
     */
    public function getFormattedIban() : string
    {
        return chunk_split($this->getIban(), 4, ' ');
    }

    /**
     * Add or update a Bank Identifier Code (BIC)
     *
     * @param string $bic A Bank Identifier Code.
     * @return self
     * @throws ild78\Exceptions\InvalidBicException When BIC seems invalid.
     */
    public function setBic(string $bic) : self
    {
        $length = strlen($bic);

        if ($length !== 8 && $length !== 11) {
            throw new ild78\Exceptions\InvalidBicException(sprintf('"%s" is not a valid BIC', $bic));
        }

        $this->dataModel['bic']['value'] = $bic;

        return $this;
    }

    /**
     * Add or update an International Bank Account Number (IBAN)
     *
     * @param string $iban An International Bank Account Number.
     * @return self
     * @throws ild78\Exceptions\InvalidIbanException When IBAN is invalid.
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
            throw new ild78\Exceptions\InvalidIbanException(sprintf('"%s" is not a valid IBAN', $iban));
        }

        $this->dataModel['country']['value'] = $country;
        $this->dataModel['iban']['value'] = $iban;
        $this->dataModel['last4']['value'] = substr($iban, -4);

        return $this;
    }

    /**
     * Add an account holder name
     *
     * @param string $name New holder name.
     * @return self
     * @throws ild78\Exceptions\InvalidNameException When the name is invalid.
     */
    public function setName(string $name) : self
    {
        try {
            return parent::setName($name);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidNameException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }
}
