<?php
declare(strict_types=1);

namespace Stancer;

use DateTimeImmutable;
use Override;
use Stancer;

/**
 * Representation of a refund.
 *
 * @method integer getAmount() Get amount to refund.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method string getCurrency() Get processed currency.
 * @method ?\DateTimeImmutable getDateBank() Get delivery date of the funds by the bank.
 * @method ?\DateTimeImmutable getDateRefund() Get date when the API sent the refund request to the bank.
 * @method \Stancer\Payment getPayment() Get refunded payment identifier.
 * @method ?\Stancer\Refund\Status getStatus() Get refund status.
 * @method integer get_amount() Get amount to refund.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_currency() Get processed currency.
 * @method ?\DateTimeImmutable get_date_bank() Get delivery date of the funds by the bank.
 * @method ?\DateTimeImmutable get_date_refund() Get date when the API sent the refund request to the bank.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method \Stancer\Payment get_payment() Get refunded payment identifier.
 * @method ?\Stancer\Refund\Status get_status() Get refund status.
 * @method string get_uri() Get entity resource location.
 * @method boolean is_modified() Indicate if the current object is modified.
 * @method $this set_amount(float $amount) Update amount.
 *
 * @phpstan-method $this setPayment(Stancer\Payment $payment)
 *
 * @property-read integer $amount Amount to refund.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $currency Processed currency.
 * @property-read ?\DateTimeImmutable $dateBank Delivery date of the funds by the bank.
 * @property-read ?\DateTimeImmutable $dateRefund Date when the API sent the refund request to the bank.
 * @property-read ?\DateTimeImmutable $date_bank Delivery date of the funds by the bank.
 * @property-read ?\DateTimeImmutable $date_refund Date when the API sent the refund request to the bank.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read boolean $isModified Alias for `Stancer\Refund::isModified()`.
 * @property-read boolean $is_modified Alias for `Stancer\Refund::isModified()`.
 * @property-read \Stancer\Payment $payment Refunded payment identifier.
 * @property-read ?\Stancer\Refund\Status $status Refund status.
 * @property-read string $uri Entity resource location.
 */
#[Stancer\Core\Documentation\AddMethod('setPayment', ['Stancer\Payment $payment'], '$this', stan: true)]
class Refund extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\AmountTrait;
    use Stancer\Traits\SearchTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'refunds';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'amount' => [
            'desc' => 'Amount to refund',
            'exportable' => true,
            'nullable' => false,
            'restricted' => true,
            'size' => [
                'min' => 50,
            ],
            'type' => self::INTEGER,
        ],
        'currency' => [
            'desc' => 'Processed currency',
            'exportable' => true,
            'nullable' => false,
            'restricted' => true,
            'type' => self::STRING,
        ],
        'dateBank' => [
            'desc' => 'Delivery date of the funds by the bank',
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'dateRefund' => [
            'desc' => 'Date when the API sent the refund request to the bank',
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'payment' => [
            'desc' => 'Refunded payment identifier',
            'exportable' => true,
            'nullable' => false,
            'restricted' => true,
            'type' => Stancer\Payment::class,
        ],
        'status' => [
            'desc' => 'Refund status',
            'restricted' => true,
            'type' => Stancer\Refund\Status::class,
        ],
    ];

    /**
     * Indicate if the current object is modified.
     *
     * Overridden to prevent to return payment state.
     *
     * @return boolean
     */
    #[Override]
    public function isModified(): bool
    {
        return !!count($this->modified);
    }

    /**
     * Send the current object.
     *
     * Overridden to make sure that the payment instance and the modified flag will not change.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When all requirement are not provided.
     */
    #[Override]
    public function send(): static
    {
        $payment = $this->getPayment();
        $modified = $payment->modified;
        $payment->modified = [];

        $this->modified[] = 'amount'; // Mandatory, force `parent::send()` to work even if no amount is set.
        $this->modified[] = 'payment'; // Mandatory, force `parent::send()` to work even if no amount is set.

        parent::send();

        // Force same payment instance.
        $this->hydrate(['payment' => $payment]);
        $this->modified = [];
        $payment->modified = $modified;

        return $this;
    }
}
