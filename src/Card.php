<?php
declare(strict_types=1);

namespace ild78;

use DateInterval;
use DateTime;
use ild78;

/**
 * Representation of a card
 */
class Card extends Api\Object
{
    /** @var array */
    protected $dataModel = [
        'brand' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'capture' => [
            'type' => self::BOOLEAN,
        ],
        'country' => [
            'type' => self::STRING,
        ],
        'cvc' => [
            'required' => true,
            'size' => [
                'fixed' => 3,
            ],
            'type' => self::STRING,
        ],
        'expMonth' => [
            'required' => true,
            'type' => self::INTEGER,
        ],
        'expYear' => [
            'required' => true,
            'type' => self::INTEGER,
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
        'number' => [
            'required' => true,
            'size' => [
                'min' => 16,
                'max' => 19,
            ],
            'type' => self::INTEGER,
        ],
        'tokenize' => [
            'type' => self::BOOLEAN,
        ],
        'zipCode' => [
            'size' => [
                'min' => 2,
                'max' => 8,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Return the expiration date.
     *
     * The DateTime object is at last second of the last day in the expiration month.
     *
     * @return DateTime
     * @throws ild78\Exceptions\RangeException When month or year is not set.
     */
    public function getExpDate() : DateTime
    {
        $month = $this->getExpMonth();
        $year = $this->getExpYear();

        if (!$month) {
            throw new ild78\Exceptions\RangeException('You must set an expiration month before asking for a date.');
        }

        if (!$year) {
            throw new ild78\Exceptions\RangeException('You must set an expiration year before asking for a date.');
        }

        $date = new DateTime(sprintf('%d-%d-01', $year, $month));

        $oneMonth = new DateInterval('P1M');
        $date->add($oneMonth);

        $oneSecond = new DateInterval('PT1S');
        $date->sub($oneSecond);

        return $date;
    }

    /**
     * Alias for `self::getExpDate()`
     *
     * @see self::getExpDate() Return the expiration date.
     * @return DateTime
     */
    public function getExpirationDate() : DateTime
    {
        return $this->getExpDate();
    }

    /**
     * Alias for `self::getExpMonth()`
     *
     * @see self::getExpMonth() Return the expiration month.
     * @return integer|null
     */
    public function getExpirationMonth()
    {
        return $this->getExpMonth();
    }

    /**
     * Alias for `self::getExpYear()`
     *
     * @see self::getExpYear() Return the expiration year.
     * @return integer|null
     */
    public function getExpirationYear()
    {
        return $this->getExpYear();
    }

    /**
     * Alias for `self::setExpMonth()`
     *
     * @see self::setExpMonth() Return the expiration month.
     * @param integer $month The expiration month.
     * @return self
     */
    public function setExpirationMonth(int $month) : self
    {
        return $this->setExpMonth($month);
    }

    /**
     * Alias for `self::setExpYear()`
     *
     * @see self::setExpYear() Return the expiration year.
     * @param integer $year The expiration year.
     * @return integer|null
     */
    public function setExpirationYear(int $year) : self
    {
        return $this->setExpYear($year);
    }

    /**
     * Update the expiration month.
     *
     * @param integer $month The expiration month.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When expiration is invalid (not between 1 and 12).
     */
    public function setExpMonth(int $month) : self
    {
        if ($month < 1 || $month > 12) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Invalid expiration month "%d"', $month));
        }

        $this->dataModel['expMonth']['value'] = $month;

        return $this;
    }

    /**
     * Update the expiration year.
     *
     * @param integer $year The expiration year.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When expiration is invalid (in past).
     */
    public function setExpYear(int $year) : self
    {
        if ($year < date('Y')) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Invalid expiration year "%d"', $year));
        }

        $this->dataModel['expYear']['value'] = $year;

        return $this;
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

        $this->dataModel['last4']['value'] = substr((string) $number, -4);
        $this->dataModel['number']['value'] = $number;

        return $this;
    }
}
