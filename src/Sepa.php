<?php
declare(strict_types=1);

namespace Stancer;

use DateTimeImmutable;
use DateTimeInterface;
use SensitiveParameter;
use Stancer;

/**
 * Representation of a SEPA account.
 *
 * @method ?string getBic() Get bank Identifier Code.
 * @method ?string getCountry() Get IBAN country.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\DateTimeInterface getDateBirth() Get account holder birth date.
 * @method ?\DateTimeInterface getDateMandate() Get mandate signature date.
 * @method ?string getIban() Get international Bank Account Number.
 * @method ?string getLast4() Get IBAN last 4 characters.
 * @method ?string getMandate() Get mandate number.
 * @method ?string getName() Get account holder name.
 * @method ?string get_bic() Get bank Identifier Code.
 * @method ?\Stancer\Sepa\Check get_check()
 * @method ?string get_country() Get IBAN country.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?\DateTimeInterface get_date_birth() Get account holder birth date.
 * @method ?\DateTimeInterface get_date_mandate() Get mandate signature date.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_formatted_iban() Get formatted IBAN.
 * @method ?string get_iban() Get international Bank Account Number.
 * @method ?string get_id() Get object ID.
 * @method ?string get_last4() Get IBAN last 4 characters.
 * @method ?string get_mandate() Get mandate number.
 * @method ?string get_name() Get account holder name.
 * @method string get_uri() Get entity resource location.
 * @method $this setDateBirth(\DateTimeInterface $dateBirth) Set account holder birth date.
 * @method $this setDateMandate(\DateTimeInterface $dateMandate) Set mandate signature date.
 * @method $this setMandate(string $mandate) Set mandate number.
 * @method $this setName(string $name) Set account holder name.
 * @method $this set_bic(string $bic) Set bank Identifier Code.
 * @method $this set_date_birth(\DateTimeInterface $date_birth) Set account holder birth date.
 * @method $this set_date_mandate(\DateTimeInterface $date_mandate) Set mandate signature date.
 * @method $this set_iban(string $iban) Set international Bank Account Number.
 * @method $this set_mandate(string $mandate) Set mandate number.
 * @method $this set_name(string $name) Set account holder name.
 *
 * @property ?string $bic Bank Identifier Code.
 * @property ?\DateTimeInterface $dateBirth Account holder birth date.
 * @property ?\DateTimeInterface $dateMandate Mandate signature date.
 * @property ?\DateTimeInterface $date_birth Account holder birth date.
 * @property ?\DateTimeInterface $date_mandate Mandate signature date.
 * @property ?string $iban International Bank Account Number.
 * @property ?string $mandate Mandate number.
 * @property ?string $name Account holder name.
 *
 * @property-read ?\Stancer\Sepa\Check $check
 * @property-read ?string $country IBAN country.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $formattedIban Formatted IBAN.
 * @property-read ?string $formatted_iban Formatted IBAN.
 * @property-read ?string $id Object ID.
 * @property-read ?string $last4 IBAN last 4 characters.
 * @property-read string $uri Entity resource location.
 * @property-read $this $validate Alias for `Stancer\Sepa::validate()`.
 */
class Sepa extends Stancer\Core\AbstractObject implements Stancer\Interfaces\PaymentMeansInterface
{
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'sepa';

    /**
     * @var array<string, DataModel>
     */
    protected array $dataModel = [
        'bic' => [
            'desc' => 'Bank Identifier Code',
            'required' => false,
            'type' => self::STRING,
        ],
        'check' => [
            'restricted' => true,
            'type' => Stancer\Sepa\Check::class,
        ],
        'country' => [
            'desc' => 'IBAN country',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'dateBirth' => [
            'desc' => 'Account holder birth date',
            'format' => Stancer\Core\Type\Helper::DATE_ONLY,
            'type' => DateTimeInterface::class,
        ],
        'dateMandate' => [
            'desc' => 'Mandate signature date',
            'type' => DateTimeInterface::class,
        ],
        'iban' => [
            'desc' => 'International Bank Account Number',
            'required' => true,
            'type' => self::STRING,
        ],
        'last4' => [
            'desc' => 'IBAN last 4 characters',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'mandate' => [
            'desc' => 'Mandate number',
            'size' => [
                'min' => 3,
                'max' => 35,
            ],
            'type' => self::STRING,
        ],
        'name' => [
            'desc' => 'Account holder name',
            'required' => true,
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Return verification results.
     *
     * @return Stancer\Sepa\Check|null
     */
    public function getCheck(): ?Stancer\Sepa\Check
    {
        if ($this->id) {
            $check = $this->dataModelGetter('check', false);

            if (!($check instanceof Stancer\Sepa\Check)) {
                $check = new Stancer\Sepa\Check($this->id);
            }

            try {
                $this->dataModel['check']['value'] = $check->populate();
            } catch (Stancer\Exceptions\NotFoundException) {
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
    #[Stancer\Core\Documentation\FormatProperty(description: 'Formatted IBAN', required: true, restricted: true)]
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
     * @throws Stancer\Exceptions\InvalidBicException When BIC seems invalid.
     */
    public function setBic(string $bic): self
    {
        $length = strlen($bic);

        if ($length !== 8 && $length !== 11) {
            throw new Stancer\Exceptions\InvalidBicException(sprintf('"%s" is not a valid BIC', $bic));
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
     * @throws Stancer\Exceptions\InvalidIbanException When IBAN is invalid.
     */
    public function setIban(
        #[SensitiveParameter]
        string $iban
    ): self {
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
            throw new Stancer\Exceptions\InvalidIbanException(sprintf('"%s" is not a valid IBAN', $iban));
        }

        $this->dataModel['country']['value'] = $country;
        $this->dataModel['iban']['value'] = $iban;
        $this->dataModel['last4']['value'] = substr($iban, -4);
        $this->modified[] = 'iban';

        return $this;
    }

    /**
     * Will ask for SEPA validation.
     *
     * @return $this
     */
    public function validate(): self
    {
        $check = new Stancer\Sepa\Check(['sepa' => $this]);

        $this->dataModel['check']['value'] = $check->send();
        $this->id = $check->id;

        $this->modified = [];

        return $this;
    }
}
