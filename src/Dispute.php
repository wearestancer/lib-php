<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Representation of a dispute
 *
 * @method integer getAmount()
 * @method string getCurrency()
 * @method integer getOrderId()
 * @method string getPayment()
 * @method string getResponseCode()
 * @method Generator list(array $terms)
 */
class Dispute extends Api\AbstractObject
{
    use ild78\Traits\AmountTrait;
    use ild78\Traits\SearchTrait;

    /** @var string */
    protected $endpoint = 'disputes';

    /** @var array */
    protected $dataModel = [
        'orderId' => [
            'restricted' => true,
            'size' => [
                'min' => 1,
                'max' => 24,
            ],
            'type' => self::STRING,
        ],
        'payment' => [
            'restricted' => true,
            'type' => ild78\Payment::class,
        ],
        'responseCode' => [
            'restricted' => true,
            'size' => [
                'fixed' => 2,
            ],
            'type' => self::STRING,
        ],
    ];
}
