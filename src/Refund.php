<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Representation of a refund
 *
 * @method integer getAmount()
 * @method string getCurrency()
 * @method ild78\\Payment getPayment()
 * @method string getStatus()
 */
class Refund extends ild78\Core\AbstractObject
{
    use ild78\Traits\AmountTrait;

    /** @var string */
    protected $endpoint = 'refunds';

    /** @var array */
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
        'payment' => [
            'required' => true,
            'type' => ild78\Payment::class,
        ],
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];

    /**
     * Indicate if the current object is modified.
     *
     * Overrided to prevent to return payment state.
     *
     * @return boolean
     */
    public function isModified(): bool
    {
        return !empty($this->modified);
    }

    /**
     * Save the current object.
     *
     * Overrided to make sure that the payment instance and the modified flag will not change.
     *
     * @return ild78\Core\AbstractObject
     * @throws ild78\Exceptions\InvalidArgumentException When all requirement are not provided.
     */
    public function save(): ild78\Core\AbstractObject
    {
        $payment = $this->getPayment();
        $modified = $payment->modified;
        $payment->modified = [];

        $this->modified[] = 'amount'; // Mandatory, force `parent::save()` to work even if no amount is setted.

        parent::save();

        // Force same payment instance.
        $this->setPayment($payment);
        $this->modified = [];
        $payment->modified = $modified;

        return $this;
    }
}
