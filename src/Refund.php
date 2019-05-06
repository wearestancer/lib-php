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
 */
class Refund extends Api\AbstractObject
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
    ];

    /**
     * Indicate if the current object is modified.
     *
     * Overrided to prevent to return payment state.
     *
     * @return boolean
     */
    public function isModified() : bool
    {
        return $this->modified;
    }

    /**
     * Save the current object.
     *
     * Overrided to make sure that the payment instance and the modified flag will not change.
     *
     * @return Api\AbstractObject
     * @throws ild78\Exceptions\InvalidArgumentException When all requirement are not provided.
     */
    public function save() : Api\AbstractObject
    {
        $payment = $this->getPayment();

        $this->modified = true; // Mandatory to force `parent::save()` to work when no amount setted.

        parent::save();

        // Force same payment instance.
        $this->setPayment($payment);

        return $this;
    }
}
