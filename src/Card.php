<?php
declare(strict_types=1);

namespace Stancer;

use DateInterval;
use DateTimeImmutable;
use Stancer;

/**
 * Representation of a card.
 *
 * @method string getBrand()
 * @method string getCountry()
 * @method string getCvc()
 * @method integer getExpMonth()
 * @method integer getExpYear()
 * @method string|null getFunding()
 * @method string getLast4()
 * @method string|null getName()
 * @method string|null getNature()
 * @method string|null getNetwork()
 * @method string|null getZipCode()
 *
 * @method boolean isTokenized()
 *
 * @method $this setBrand(string $brand)
 * @method $this setCountry(string $country)
 * @method $this setCvc(string $cvc)
 * @method $this setExpYear(integer $expYear)
 * @method $this setFunding(string $funding)
 * @method $this setLast4(string $last4)
 * @method $this setName(string $name)
 * @method $this setNature(string $nature)
 * @method $this setNetwork(string $network)
 * @method $this setTokenize(boolean $tokenize)
 * @method $this setZipCode(string $zipCode)
 *
 * @property string $brand
 * @property string $country
 * @property DateTimeImmutable|null $created
 * @property string $cvc
 * @property integer $expMonth
 * @property integer $expYear
 * @property string|null $funding
 * @property string $last4
 * @property string|null $name
 * @property string|null $nature
 * @property string|null $network
 * @property string|null $zipCode
 */
class Card extends Stancer\Core\AbstractObject implements Stancer\Interfaces\PaymentMeansInterface
{
    /** @var string */
    protected $endpoint = 'cards';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'brand' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'country' => [
            'type' => self::STRING,
        ],
        'cvc' => [
            'exception' => Stancer\Exceptions\InvalidCardCvcException::class,
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
        'funding' => [
            'restricted' => true,
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
        'nature' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'network' => [
            'restricted' => true,
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

    /** @var array<string, string> */
    protected $aliases = [
        'isTokenized' => 'getTokenize',
    ];

    /**
     * Return real brand name.
     *
     * Whereas `Card::getBrand()` returns brand as a simple normalized string like "amex",
     * `Card::getBrandName()` will return a complete and real brand name, like "American Express".
     *
     * @return string
     */
    public function getBrandName(): string
    {
        $names = [
            'amex' => 'American Express',
            'dankort' => 'Dankort',
            'discover' => 'Discover',
            'jcb' => 'JCB',
            'maestro' => 'Maestro',
            'mastercard' => 'MasterCard',
            'visa' => 'VISA',
        ];

        $brand = $this->getBrand();

        if (array_key_exists($brand, $names)) {
            return $names[$brand];
        }

        return $brand;
    }

    /**
     * Return the expiration date.
     *
     * The DateTime object is at last second of the last day in the expiration month.
     *
     * @return DateTimeImmutable
     * @throws Stancer\Exceptions\InvalidExpirationMonthException When month is not set.
     * @throws Stancer\Exceptions\InvalidExpirationYearException When year is not set.
     */
    public function getExpDate(): DateTimeImmutable
    {
        $month = $this->getExpMonth();
        $year = $this->getExpYear();

        if (!$month) {
            $message = 'You must set an expiration month before asking for a date.';

            throw new Stancer\Exceptions\InvalidExpirationMonthException($message);
        }

        if (!$year) {
            $message = 'You must set an expiration year before asking for a date.';

            throw new Stancer\Exceptions\InvalidExpirationYearException($message);
        }

        $date = new DateTimeImmutable(sprintf('%d-%d-01', $year, $month));

        $oneMonth = new DateInterval('P1M');
        $oneSecond = new DateInterval('PT1S');

        return $date->add($oneMonth)->sub($oneSecond);
    }

    /**
     * Alias for `self::getExpDate()`
     *
     * @see self::getExpDate() Return the expiration date.
     * @return DateTimeImmutable
     */
    public function getExpirationDate(): DateTimeImmutable
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
     * Return tokenize status.
     *
     * For every card sent to the API, you will get an ID representing it.
     * This ID is not reusable, you can not use it for an other payment.
     *
     * If you needed to make later payment, you can set tokenize to true,
     * in that case, the card ID may be reuse for other payment.
     *
     * This can be useful for payments in multiple times.
     *
     * @return boolean
     */
    public function getTokenize(): bool
    {
        $tokenize = parent::getTokenize();

        if (!is_bool($tokenize)) {
            return false;
        }

        return $tokenize;
    }

    /**
     * Alias for `self::setExpMonth()`
     *
     * @see self::setExpMonth() Return the expiration month.
     * @param integer $month The expiration month.
     * @return $this
     */
    public function setExpirationMonth(int $month): self
    {
        return $this->setExpMonth($month);
    }

    /**
     * Alias for `self::setExpYear()`
     *
     * @see self::setExpYear() Return the expiration year.
     * @param integer $year The expiration year.
     * @return $this
     */
    public function setExpirationYear(int $year): self
    {
        return $this->setExpYear($year);
    }

    /**
     * Update the expiration month.
     *
     * @param integer $month The expiration month.
     * @return $this
     * @throws Stancer\Exceptions\InvalidExpirationMonthException When expiration is invalid (not between 1 and 12).
     */
    public function setExpMonth(int $month): self
    {
        if ($month < 1 || $month > 12) {
            $message = sprintf('Invalid expiration month "%d"', $month);

            throw new Stancer\Exceptions\InvalidExpirationMonthException($message);
        }

        $this->dataModel['expMonth']['value'] = $month;
        $this->modified[] = 'exp_month';

        return $this;
    }

    /**
     * Add a card number.
     *
     * @param string $number A valid card number.
     * @return $this
     * @throws Stancer\Exceptions\InvalidCardNumberException When the card number is invalid.
     */
    public function setNumber(string $number): self
    {
        $numb = preg_replace('`\s*`', '', $number);

        if (is_null($numb)) {
            $message = sprintf('"%s" is not a valid credit card number.', $number);

            throw new Stancer\Exceptions\InvalidCardNumberException($message);
        }

        $parts = str_split($numb);
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

        $manip = function ($n, $index) use (&$sum, $calc): void {
            $sum += ($index % 2) ? $calc[$n] : (int) $n;
        };

        array_walk($reversed, $manip);

        if ($sum % 10) {
            $message = sprintf('"%s" is not a valid credit card number.', $numb);

            throw new Stancer\Exceptions\InvalidCardNumberException($message);
        }

        $this->dataModel['last4']['value'] = substr($numb, -4);
        $this->dataModel['number']['value'] = $numb;
        $this->modified[] = 'number';

        return $this;
    }
}
