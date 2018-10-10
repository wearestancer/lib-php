<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Representation of a card
 */
class Card extends Api\Object
{
    /** @var string */
    protected $brand;

    /** @var string */
    protected $country;

    /** @var integer */
    protected $cvc;

    /** @var integer */
    protected $expMonth;

    /** @var integer */
    protected $expYear;

    /** @var string */
    protected $last4;

    /** @var string|null */
    protected $name;

    /** @var integer */
    protected $number;

    /** @var string|null */
    protected $zipCode;

    /**
     * Return an array of properties not allowed to change with a setter
     *
     * @see self::__call()
     * @return array
     */
    public function getForbiddenProperties() : array
    {
        $forbidden = [
            'last4',
        ];

        return array_merge(parent::getForbiddenProperties(), $forbidden);
    }

    /**
     * Add a card number
     *
     * @param integer $number A valid card number.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When the card number is invalid.
     */
    public function setNumber(int $number) : self
    {
        $parts = str_split((string) $number);
        $reversed = array_reverse($parts);
        $sum = 0;

        $manip = function ($n, $index) use (&$sum) {
            if ($index % 2) {
                $n *= 2;

                if ($n > 9) {
                    $n -= 9;
                }
            }

            $sum += $n;
        };

        array_walk($reversed, $manip);

        if ($sum % 10) {
            $message = sprintf('"%s" is not a valid credit card number.', $number);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        $this->last4 = substr((string) $number, -4);
        $this->number = $number;

        return $this;
    }
}
