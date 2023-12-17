<?php
declare(strict_types=1);

namespace Stancer;

use DateInterval;
use DateTimeImmutable;
use SensitiveParameter;
use Stancer;

/**
 * Representation of a card.
 *
 * @method ?string getBrand() Get card brand.
 * @method ?string getCountry() Get card country.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?string getCvc() Get card Validation Code.
 * @method ?integer getExpMonth() Get card expiration month.
 * @method ?integer getExpYear() Get card expiration year.
 * @method ?string getFunding() Get card funding.
 * @method ?string getLast4() Get card last 4 digits.
 * @method ?string getName() Get card holder's name.
 * @method ?string getNature() Get card nature.
 * @method ?string getNetwork() Get card network.
 * @method ?string getNumber() Get card number.
 * @method ?string getZipCode()
 * @method ?string get_brand() Get card brand.
 * @method ?string get_brand_name() Get formatted brand name.
 * @method ?string get_country() Get card country.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?string get_cvc() Get card Validation Code.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method DateTimeImmutable get_exp_date() Return the expiration date.
 * @method ?integer get_exp_month() Get card expiration month.
 * @method ?integer get_exp_year() Get card expiration year.
 * @method DateTimeImmutable get_expiration_date() Alias for `self::getExpDate()`.
 * @method ?int get_expiration_month() Alias for `self::getExpMonth()`.
 * @method ?int get_expiration_year() Alias for `self::getExpYear()`.
 * @method ?string get_funding() Get card funding.
 * @method ?string get_id() Get object ID.
 * @method ?string get_last4() Get card last 4 digits.
 * @method ?string get_name() Get card holder's name.
 * @method ?string get_nature() Get card nature.
 * @method ?string get_network() Get card network.
 * @method ?string get_number() Get card number.
 * @method boolean get_tokenize() Is the card tokenized?
 * @method string get_uri() Get entity resource location.
 * @method ?string get_zip_code()
 * @method boolean is_tokenized() Alias for `Stancer\Card::getTokenize()`.
 * @method $this setCvc(string $cvc) Set card Validation Code.
 * @method $this setExpYear(integer $expYear) Set card expiration year.
 * @method $this setName(string $name) Set card holder's name.
 * @method $this setTokenize(boolean $tokenize) Is the card tokenized?
 * @method $this setZipCode(string $zipCode)
 * @method $this set_cvc(string $cvc) Set card Validation Code.
 * @method $this set_exp_month(integer $exp_month) Set card expiration month.
 * @method $this set_exp_year(integer $exp_year) Set card expiration year.
 * @method $this set_expiration_month(int $month) Alias for `self::setExpMonth()`.
 * @method $this set_expiration_year(int $year) Alias for `self::setExpYear()`.
 * @method $this set_name(string $name) Set card holder's name.
 * @method $this set_number(string $number) Set card number.
 * @method $this set_tokenize(boolean $tokenize) Is the card tokenized?
 * @method $this set_zip_code(string $zip_code)
 *
 * @property ?string $cvc Card Validation Code.
 * @property ?integer $expMonth Card expiration month.
 * @property ?integer $expYear Card expiration year.
 * @property ?integer $exp_month Card expiration month.
 * @property ?integer $exp_year Card expiration year.
 * @property ?string $name Card holder's name.
 * @property ?string $number Card number.
 * @property boolean $tokenize
 * @property ?string $zipCode
 * @property ?string $zip_code
 *
 * @property-read ?string $brand Card brand.
 * @property-read ?string $brandName Formatted brand name.
 * @property-read ?string $brand_name Formatted brand name.
 * @property-read ?string $country Card country.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $funding Card funding.
 * @property-read ?string $id Object ID.
 * @property-read boolean $isTokenized Alias for `Stancer\Card::isTokenized()`.
 * @property-read boolean $is_tokenized Alias for `Stancer\Card::isTokenized()`.
 * @property-read ?string $last4 Card last 4 digits.
 * @property-read ?string $nature Card nature.
 * @property-read ?string $network Card network.
 * @property-read string $uri Entity resource location.
 */
class Card extends Stancer\Core\AbstractObject implements Stancer\Interfaces\PaymentMeansInterface
{
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'cards';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'brand' => [
            'desc' => 'Card brand',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'country' => [
            'desc' => 'Card country',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'cvc' => [
            'desc' => 'Card Validation Code',
            'exception' => Stancer\Exceptions\InvalidCardCvcException::class,
            'required' => true,
            'size' => [
                'fixed' => 3,
            ],
            'type' => self::STRING,
        ],
        'expMonth' => [
            'desc' => 'Card expiration month',
            'required' => true,
            'type' => self::INTEGER,
        ],
        'expYear' => [
            'desc' => 'Card expiration year',
            'required' => true,
            'type' => self::INTEGER,
        ],
        'funding' => [
            'desc' => 'Card funding',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'last4' => [
            'desc' => 'Card last 4 digits',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'name' => [
            'desc' => 'Card holder\'s name',
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'nature' => [
            'desc' => 'Card nature',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'network' => [
            'desc' => 'Card network',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'number' => [
            'desc' => 'Card number',
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

    /**
     * Return real brand name.
     *
     * Whereas `Card::getBrand()` returns brand as a simple normalized string like "amex",
     * `Card::getBrandName()` will return a complete and real brand name, like "American Express".
     *
     * @return string|null
     */
    #[Stancer\Core\Documentation\FormatProperty(description: 'Formatted brand name', restricted: true)]
    public function getBrandName(): ?string
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

        if ($brand && array_key_exists($brand, $names)) {
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
     * Alias for `self::getExpDate()`.
     *
     * @see self::getExpDate() Return the expiration date.
     * @return DateTimeImmutable
     */
    public function getExpirationDate(): DateTimeImmutable
    {
        return $this->getExpDate();
    }

    /**
     * Alias for `self::getExpMonth()`.
     *
     * @see self::getExpMonth() Return the expiration month.
     * @return integer|null
     */
    public function getExpirationMonth(): ?int
    {
        return $this->getExpMonth();
    }

    /**
     * Alias for `self::getExpYear()`.
     *
     * @see self::getExpYear() Return the expiration year.
     * @return integer|null
     */
    public function getExpirationYear(): ?int
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
    #[Stancer\Core\Documentation\FormatProperty(
        fullDescription: 'Is the card tokenized?',
        type: self::BOOLEAN,
        value: false,
    )]
    public function getTokenize(): bool
    {
        $tokenize = parent::getTokenize();

        if (!is_bool($tokenize)) {
            return false;
        }

        return $tokenize;
    }

    /**
     * Alias for `Stancer\Card::getTokenize()`.
     *
     * @see Stancer\Card::getTokenize()
     * @return boolean
     */
    public function isTokenized(): bool
    {
        return $this->getTokenize();
    }

    /**
     * Alias for `self::setExpMonth()`.
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
     * Alias for `self::setExpYear()`.
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
    public function setNumber(
        #[SensitiveParameter]
        string $number
    ): self {
        $spaceless = preg_replace('/\s/', '', $number);
        $numb = preg_replace('/\D/', '', $number);

        if (!$numb) {
            $message = sprintf('"%s" is not a valid credit card number.', $spaceless);

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
            $message = sprintf('"%s" is not a valid credit card number.', $spaceless);

            throw new Stancer\Exceptions\InvalidCardNumberException($message);
        }

        $this->dataModel['last4']['value'] = substr($numb, -4);
        $this->dataModel['number']['value'] = $numb;
        $this->modified[] = 'number';

        return $this;
    }
}
