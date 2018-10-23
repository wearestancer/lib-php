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
}
