<?php
declare(strict_types=1);

namespace ild78;

use ild78;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Representation of a SEPA account
 *
 * @method string getBic()
 * @method string|null getCountry()
 * @method DateTimeInterface|null getDateMandate()
 * @method string|null getIban()
 * @method string getLast4()
 * @method string|null getMandate()
 * @method string getName()
 *
 * @method $this setDateMandate(DateTimeInterface $dateMandate)
 * @method $this setMandate(string $mandate)
 * @method $this setName(string $name)
 *
 * @property string $bic
 * @property string|null $country
 * @property DateTimeInterface|null $dateMandate
 * @property string $last4
 * @property string|null $mandate
 * @property string $name
 *
 * @property-read ild78\Sepa\Check|null $check
 * @property-read DateTimeImmutable|null $created
 * @property-read DateTimeImmutable|null $creationDate
 */
class Sepa extends ild78\Core\AbstractObject implements ild78\Interfaces\PaymentMeansInterface
{
    /** @var string */
    protected $endpoint = 'sepa';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'bic' => [
            'required' => true,
            'type' => self::STRING,
        ],
        'check' => [
            'restricted' => true,
            'type' => ild78\Sepa\Check::class,
        ],
        'country' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'dateBirth' => [
            'format' => ild78\Core\Type\Helper::DATE_ONLY,
            'type' => DateTimeInterface::class,
        ],
        'dateMandate' => [
            'type' => DateTimeInterface::class,
        ],
        'iban' => [
            'required' => true,
            'type' => self::STRING,
        ],
        'last4' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'mandate' => [
            'size' => [
                'min' => 3,
                'max' => 35,
            ],
            'type' => self::STRING,
        ],
        'name' => [
            'required' => true,
            'size' => [
                'min' => 4,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Return verification results.
     *
     * @return ild78\Sepa\Check|null
     */
    public function getCheck(): ?ild78\Sepa\Check
    {
        if ($this->id) {
            $check = $this->dataModelGetter('check', false);

            if (!$check) {
                $check = new ild78\Sepa\Check($this->id);
            }

            try {
                $this->dataModel['check']['value'] = $check->populate();
            } catch (ild78\Exceptions\NotFoundException $exception) {
                return null;
            }
        }

        return parent::getCheck();
    }

    /**
     * Return IBAN with usual readeable format (AAAA BBBB CCCC ...).
     *
     * @return string|null
     */
    public function getFormattedIban(): ?string
    {
        $iban = $this->getIban();

        if (!$iban) {
            return null;
        }

        return trim(chunk_split($iban, 4, ' '));
    }

    /**
     * Add or update a Bank Identifier Code (BIC).
     *
     * @param string $bic A Bank Identifier Code.
     * @return $this
     * @throws ild78\Exceptions\InvalidBicException When BIC seems invalid.
     */
    public function setBic(string $bic): self
    {
        $length = strlen($bic);

        if ($length !== 8 && $length !== 11) {
            throw new ild78\Exceptions\InvalidBicException(sprintf('"%s" is not a valid BIC', $bic));
        }

        $this->dataModel['bic']['value'] = $bic;
        $this->modified[] = 'bic';

        return $this;
    }

    /**
     * Add or update an International Bank Account Number (IBAN).
     *
     * @param string $iban An International Bank Account Number.
     * @return $this
     * @throws ild78\Exceptions\InvalidIbanException When IBAN is invalid.
     */
    public function setIban(string $iban): self
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
        $this->modified[] = 'iban';

        return $this;
    }
}
