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
            'type' => self::STRING,
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

    /** @var array */
    protected $aliases = [
        'istokenized' => 'gettokenize',
    ];

    /**
     * Return the expiration date.
     *
     * The DateTime object is at last second of the last day in the expiration month.
     *
     * @return DateTime
     * @throws ild78\Exceptions\InvalidExpirationMonthException When month is not set.
     * @throws ild78\Exceptions\InvalidExpirationYearException When year is not set.
     */
    public function getExpDate() : DateTime
    {
        $month = $this->getExpMonth();
        $year = $this->getExpYear();

        if (!$month) {
            $message = 'You must set an expiration month before asking for a date.';

            throw new ild78\Exceptions\InvalidExpirationMonthException($message);
        }

        if (!$year) {
            $message = 'You must set an expiration year before asking for a date.';

            throw new ild78\Exceptions\InvalidExpirationYearException($message);
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
     * Return tokenize status
     *
     * For every card sended to the API, you will get an ID representing it.
     * This ID is not reusable, you can not use it for an other payment.
     *
     * If you needed to make later payment, you can set tokenize to true,
     * in that case, the card ID may be reuse for other payment.
     *
     * This can be usefull for payments in multiple times.
     *
     * @return boolean
     */
    public function getTokenize() : bool
    {
        $tokenize = parent::getTokenize();

        if (!is_bool($tokenize)) {
            return false;
        }

        return $tokenize;
    }

    /**
     * Update CVC.
     *
     * We use string for CVC to prevent errors with leading zeros.
     *
     * @param string $cvc New CVC.
     * @return self
     * @throws ild78\Exceptions\InvalidCardCvcException When CVC is not valid.
     */
    public function setCvc(string $cvc) : self
    {
        try {
            return parent::setCvc($cvc);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidCardCvcException($excep->getMessage(), $excep->getCode(), $excep);
        }
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
     * @throws ild78\Exceptions\InvalidExpirationMonthException When expiration is invalid (not between 1 and 12).
     */
    public function setExpMonth(int $month) : self
    {
        if ($month < 1 || $month > 12) {
            $message = sprintf('Invalid expiration month "%d"', $month);

            throw new ild78\Exceptions\InvalidExpirationMonthException($message);
        }

        $this->dataModel['expMonth']['value'] = $month;

        return $this;
    }

    /**
     * Update the expiration year.
     *
     * @param integer $year The expiration year.
     * @return self
     * @throws ild78\Exceptions\InvalidExpirationYearException When expiration is invalid (in past).
     */
    public function setExpYear(int $year) : self
    {
        if ($year < date('Y')) {
            $message = sprintf('Invalid expiration year "%d"', $year);

            throw new ild78\Exceptions\InvalidExpirationYearException($message);
        }

        $this->dataModel['expYear']['value'] = $year;

        return $this;
    }

    /**
     * Add a card holder name
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

    /**
     * Add a card number
     *
     * @param string $number A valid card number.
     * @return self
     * @throws ild78\Exceptions\InvalidCardNumberException When the card number is invalid.
     */
    public function setNumber(string $number) : self
    {
        $number = preg_replace('`\s*`', '', $number);
        $parts = str_split($number);
        $reversed = array_reverse($parts);
        $sum = 0;
        $calc = [
            0,
            2,
            4,
            6,
            8,
            1,
            3,
            5,
            7,
            9,
        ];

        $manip = function ($n, $index) use (&$sum, $calc) {
            $sum += ($index % 2) ? $calc[$n] : $n;
        };

        array_walk($reversed, $manip);

        if ($sum % 10) {
            $message = sprintf('"%s" is not a valid credit card number.', $number);

            throw new ild78\Exceptions\InvalidCardNumberException($message);
        }

        $this->dataModel['last4']['value'] = substr((string) $number, -4);
        $this->dataModel['number']['value'] = $number;

        return $this;
    }
}
