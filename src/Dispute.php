<?php
declare(strict_types=1);

namespace ild78;

use ild78;

/**
 * Representation of a dispute
 *
 * @method integer getAmount()
 * @method string getCurrency()
 * @method string getOrderId()
 * @method ild78\Payment getPayment()
 * @method string getResponse()
 *
 * @method Generator list(array $terms)
 *
 * @property integer $amount
 * @property DateTime|null $created
 * @property string $currency
 * @property string $orderId
 * @property ild78\Payment $payment
 * @property string $response
 */
class Dispute extends ild78\Core\AbstractObject
{
    use ild78\Traits\AmountTrait;
    use ild78\Traits\SearchTrait;

    /** @var string */
    protected $endpoint = 'disputes';

    /** @var array<string, DataModel> */
    protected $dataModel = [
        'orderId' => [
            'restricted' => true,
            'size' => [
                'min' => 1,
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
        'payment' => [
            'restricted' => true,
            'type' => ild78\Payment::class,
        ],
        'response' => [
            'restricted' => true,
            'size' => [
                'fixed' => 2,
            ],
            'type' => self::STRING,
        ],
    ];
}
