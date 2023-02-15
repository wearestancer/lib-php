<?php
declare(strict_types=1);

namespace Stancer;

use DateTimeImmutable;
use Stancer;

/**
 * Representation of a refund.
 *
 * @method integer getAmount()
 * @method string getCurrency()
 * @method string getDateBank()
 * @method string getDateRefund()
 * @method Stancer\Payment getPayment()
 * @method string getStatus()
 *
 * @method $this setAmount(integer $amount)
 * @method $this setPayment(Stancer\Payment $payment)
 *
 * @property integer $amount
 * @property Stancer\Payment $payment
 *
 * @property-read DateTimeImmutable|null $created
 * @property-read string $currency
 * @property-read string $dateBank
 * @property-read string $dateRefund
 * @property-read string $status
 */
class Refund extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\AmountTrait;
    use Stancer\Traits\SearchTrait;

    /** @var string */
    protected $endpoint = 'refunds';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'amount' => [
            'size' => [
                'min' => 50,
            ],
            'type' => self::INTEGER,
        ],
        'currency' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'dateBank' => [
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'dateRefund' => [
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'payment' => [
            'required' => true,
            'type' => Stancer\Payment::class,
        ],
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];

    /**
     * Indicate if the current object is modified.
     *
     * Overridden to prevent to return payment state.
     *
     * @return boolean
     */
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
    public function send(): Stancer\Core\AbstractObject
    {
        $payment = $this->getPayment();
        $modified = $payment->modified;
        $payment->modified = [];

        $this->modified[] = 'amount'; // Mandatory, force `parent::send()` to work even if no amount is set.

        parent::send();

        // Force same payment instance.
        $this->setPayment($payment);
        $this->modified = [];
        $payment->modified = $modified;

        return $this;
    }
}
